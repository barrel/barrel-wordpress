<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Which option names should be autoloaded
 *
 * @return array
 *
 * @since 2.8.3
 */
function searchwp_get_autoload_options() {
	return array(
		'settings',
		'advanced'
	);
}

/**
 * Remove an option from the database
 *
 * @param $option string The option name
 *
 * @return bool
 * @since 1.9.1
 */
function searchwp_delete_option( $option ) {
	$option = trim( $option );
	if ( empty( $option ) ) {
		return false;
	}

	$result = delete_option( SEARCHWP_PREFIX . $option );

	$force_cache_clear = array( 'indexnonce' );

	if ( in_array( $option, $force_cache_clear ) ) {
		wp_cache_delete( SEARCHWP_PREFIX . $option, 'options' );
	}

	return $result;
}


/**
 * Add an option to the database
 *
 * @param $option string The option name
 * @param $value  mixed The option value
 *
 * @return bool
 * @since 1.9.1
 */
function searchwp_add_option( $option, $value = false ) {
	$option = trim( $option );
	if ( empty( $option ) ) {
		return false;
	}

	$autoload = in_array( $option, searchwp_get_autoload_options(), true ) ? 'yes' : 'no';

	return add_option( SEARCHWP_PREFIX . $option, $value, '', $autoload );
}


/**
 * Update an option in the database
 *
 * @param $option string The option name
 * @param $value  mixed The option value
 *
 * @return bool
 * @since 1.9.1
 */
function searchwp_update_option( $option, $value = false ) {
	$option = trim( $option );
	if ( empty( $option ) ) {
		return false;
	}

	$autoload = in_array( $option, searchwp_get_autoload_options(), true ) ? 'yes' : 'no';

	update_option( SEARCHWP_PREFIX . $option, $value, $autoload );

	return true;
}


/**
 * Retrieve an option from the database
 *
 * @param $option string The option name
 *
 * @return mixed|void
 * @since 1.9.1
 */
function searchwp_get_option( $option ) {

	searchwp_maybe_clear_cache( $option );

	return get_option( SEARCHWP_PREFIX . $option );
}


/**
 * Forcefully purge the object cache for certain options
 *
 * @param $option
 * @since 2.8.3
 */
function searchwp_maybe_clear_cache( $option ) {
	$keys = array( 'progress', 'transient', 'indexer', 'indexnonce' );

	if ( in_array( $option, $keys, true ) ) {
		// manually clear the cache
		wp_cache_delete( SEARCHWP_PREFIX . $option, 'options' );
	}
}


/**
 * Retrieve a setting
 *
 * @param             $setting
 * @param bool|string $group
 *
 * @return bool|mixed
 */
function searchwp_get_setting( $setting, $group = false ) {

	$searchwp = SWP();

	// validate the request
	$setting = trim( $setting );
	if ( empty( $setting ) ) {
		return false;
	}
	if ( false !== $group ) {
		$group = trim( $group );
		if ( empty( $group ) ) {
			return false;
		}
	}

	// get the setting
	if ( false !== $group ) {
		if ( ! isset( $searchwp->settings[ $group ][ $setting ] ) ) {
			searchwp_set_setting( $setting, false, $group );
			return false;
		} else {
			return $searchwp->settings[ $group ][ $setting ];
		}
	} else {
		if ( ! isset( $searchwp->settings[ $setting ] ) ) {
			searchwp_set_setting( $setting, false );
			return false;
		} else {
			return $searchwp->settings[ $setting ];
		}
	}
}


/**
 * Set a setting in the SearchWP singleton. To reduce database calls this update will take place only in the singleton
 * and made persistent by saving to the database when WordPress shuts down.
 *
 * @param      $setting
 * @param      $value
 * @param bool $group
 *
 * @return bool
 */
function searchwp_set_setting( $setting, $value, $group = false ) {

	$searchwp = SWP();

	// validate the request
	$setting = trim( $setting );
	if ( empty( $setting ) ) {
		return false;
	}
	if ( false !== $group ) {
		$group = trim( $group );
		if ( empty( $group ) ) {
			return false;
		}
	}

	// Settings in SearchWP are a bit unique. There are 'configuration' settings and 'indexer' settings. Configuration
	// settings are those that configure the plugin, the search engine config, keyword weights, etc. The indexer settings
	// store various details for the indexer to utilize. Since the indexer runs independently and is constantly updating
	// it's internal settings, we don't want updates to these settings records to ever collide, so we're going to "route"
	// them here based on their name and/or group.

	$indexer_names = array(
		'initial_index_built',      // whether the initial index has been built
		'stats',                    // group to hold all stats
		'remaining',                // remaining posts to index
		'last_activity',            // last activity timestamp (to check for stalls)
		'total',                    // total number of posts to index
		'done',                     // number of posts already indexed
		'in_progress',              // the posts currently being indexed
		'running',                  // whether the indexer is running
		'paused',                   // whether the indexer is paused (disabled)
		'processing_purge_queue',   // whether the indexer is processing the purge queue
		'endpoint',                 // the indexer endpoint
	);

	// check the setting name to see whether we need to retrieve a searchwp setting or an indexer setting
	if ( in_array( $setting, $indexer_names, true ) || in_array( $group, $indexer_names, true ) ) {

		// it's an indexer setting
		$indexer_settings = get_option( SEARCHWP_PREFIX . 'indexer' );

		if ( ! is_array( $indexer_settings ) ) {
			$indexer_settings = array();
		}

		// set the setting locally and in the singleton
		if ( false !== $group ) {
			// make sure the group exists
			if ( ! isset( $indexer_settings[ $group ] ) ) {
				$indexer_settings[ $group ] = array();
			}
			if ( ! isset( $searchwp->settings[ $group ] ) ) {
				$searchwp->settings[ $group ] = array();
			}
			$indexer_settings[ $group ][ $setting ] = $value;   // database record
			$searchwp->settings[ $group ][ $setting ] = $value; // singleton
		} else {
			$indexer_settings[ $setting ] = $value;   // database record
			$searchwp->settings[ $setting ] = $value; // singleton
		}

		// update the database record
		searchwp_update_option( 'indexer', $indexer_settings );

	} else {

		// it's a SearchWP configuration
		$searchwp_settings = get_option( SEARCHWP_PREFIX . 'settings' );

		// set the setting locally and in the singleton
		if ( false !== $group ) {
			// make sure the group exists
			if ( ! isset( $searchwp_settings[ $group ] ) ) {
				$searchwp_settings[ $group ] = array();
			}
			if ( ! isset( $searchwp->settings[ $group ] ) ) {
				$searchwp->settings[ $group ] = array();
			}
			$searchwp_settings[ $group ][ $setting ] = $value;  // database record
			$searchwp->settings[ $group ][ $setting ] = $value; // singleton
		} else {
			$searchwp_settings[ $setting ] = $value;   // database record
			$searchwp->settings[ $setting ] = $value; // singleton
		}

		// update the database record
		searchwp_update_option( 'settings', $searchwp_settings );

	}

	return true;
}


/**
 * Callback for filter conflict notice dismissals
 *
 * @since 1.8
 */
if ( ! function_exists( 'swp_dismiss_filter_conflict' ) ) {
	function swp_dismiss_filter_conflict() {
		// verify the request
		if ( isset( $_REQUEST['swphash'] ) && isset( $_REQUEST['swpnonce'] ) && isset( $_REQUEST['swpfilter'] ) ) {
			if ( wp_verify_nonce( $_REQUEST['swpnonce'], 'swpconflict_' . $_REQUEST['swpfilter'] ) ) {

				// grab our existing dismissals and make sure our array key is set up
				$existing_dismissals = searchwp_get_setting( 'dismissed' );
				if ( ! is_array( $existing_dismissals ) ) {
					$existing_dismissals = array();
				}
				if ( ! isset( $existing_dismissals['filter_conflicts'] ) ) {
					$existing_dismissals['filter_conflicts'] = array();
				}

				// add this dismissal to the list and save it
				$existing_dismissals['filter_conflicts'][] = sanitize_text_field( $_REQUEST['swphash'] );
				$existing_dismissals['filter_conflicts'] = array_unique( $existing_dismissals['filter_conflicts'] );

				searchwp_set_setting( 'dismissed', $existing_dismissals );
			}
		}
		die();
	}
}


/**
 * Reset all the flags related to an active indexer
 */
if ( ! function_exists( 'searchwp_wake_up_indexer' ) ) {
	function searchwp_wake_up_indexer() {
		// reset all the flags used when indexing
		searchwp_set_setting( 'stats', array() );
		searchwp_set_setting( 'running', false );
		searchwp_update_option( 'busy', false );
		searchwp_update_option( 'doing_delta', false );
		searchwp_update_option( 'waiting', false );
		searchwp_update_option( 'delta_attempts', 0 );
	}
}


/**
 * Determines what percentage of indexing is complete. Polled via AJAX when viewing SearchWP settings page
 *
 * @since 1.0
 */
if ( ! function_exists( 'searchwp_get_indexer_progress' ) ) {
	function searchwp_get_indexer_progress() {
		$progress   = searchwp_get_option( 'progress' );
		$waiting    = searchwp_get_option( 'waiting' );
		echo wp_json_encode( array(
				'progress'  => ( ! empty( $progress ) ) ? floatval( $progress ) : '100',
				'waiting'   => $waiting,
			) );
		die();
	}
}

if ( ! function_exists( 'searchwp_check_for_stalled_indexer' ) ) {
	/**
	 * Determines whether the indexer has stalled based on the time of last activity
	 *
	 * @param int $threshold
	 * @since 1.0
	 */
	function searchwp_check_for_stalled_indexer( $threshold = 180 ) {
		$last_activity  = searchwp_get_setting( 'last_activity', 'stats' );
		$running        = searchwp_get_setting( 'running' );
		$doing_delta    = searchwp_get_option( 'doing_delta' );
		$busy           = searchwp_get_option( 'busy' );
		if ( ! is_null( $last_activity ) && false !== $last_activity ) {
			if (
				( current_time( 'timestamp' ) > $last_activity + absint( $threshold ) )
				&& ( $running || $doing_delta || $busy )
				) {
				// stalled
				do_action( 'searchwp_log', '---------- Indexer has stalled, jumpstarting' );
				searchwp_wake_up_indexer();
			}
		} else {
			// prior to version 2.2.2 the last activity was set to false once indexing was done
			// so if that timestamp is false but there is still a purge queue, we're going to
			// wake up the indexer by force
			$purge_queue = searchwp_get_option( 'purge_queue' );
			if ( ! empty( $purge_queue ) ) {
				searchwp_wake_up_indexer();
			} else {
				if (
					( current_time( 'timestamp' ) > $last_activity + absint( $threshold ) )
					&& ( $running || $doing_delta || $busy )
				) {
					// stalled
					do_action( 'searchwp_log', '---------- Indexer has stalled [alt], jumpstarting' );
					searchwp_wake_up_indexer();
				}
			}
		}
	}
}

if ( ! function_exists( 'searchwp_extract_pdf_text' ) && class_exists( 'SearchWPIndexer' ) ) {
	/**
	 * Extracts PDF content from a PDF within the Media library
	 *
	 * @since 2.5
	 *
	 * @param $post_id
	 *
	 * @return string
	 */
	function searchwp_extract_pdf_text( $post_id ) {
		$indexer = new SearchWPIndexer();
		return $indexer->extract_pdf_text( absint( $post_id ) );
	}
}

if ( ! function_exists( 'searchwp_extract_pdf_metadata' ) && class_exists( 'SearchWPIndexer' ) ) {
	/**
	 * Extracts PDF metadata from a PDF within the Media library
	 *
	 * @since 2.5
	 *
	 * @param $post_id
	 *
	 * @return array
	 */
	function searchwp_extract_pdf_metadata( $post_id ) {
		$indexer = new SearchWPIndexer();
		return $indexer->extract_pdf_metadata( absint( $post_id ) );
	}
}

if ( ! function_exists( 'searchwp_get_license_key' ) ) {
	/**
	 * Retrieve SearchWP's license key
	 *
	 * @since 2.6.2
	 */
	function searchwp_get_license_key() {
		$license_key = defined( 'SEARCHWP_LICENSE_KEY' ) ? SEARCHWP_LICENSE_KEY : get_option( SEARCHWP_PREFIX . 'license_key' );
		$license_key = apply_filters( 'searchwp_license_key', $license_key );
		$license_key = sanitize_text_field( $license_key );
		$license_key = trim( $license_key );

		return $license_key;
	}
}

/**
 * Check whether an engine is valid
 * 
 * @since 2.8
 * 
 * @param $engine
 *
 * @return bool
 */
function searchwp_is_valid_engine( $engine ) {

	if ( ! isset( SWP()->settings['engines'] ) ) {
		return false;
	}

	$engines = SWP()->settings['engines'];

	if ( ! is_array( $engines ) ) {
		return false;
	}

	if ( ! array_key_exists( $engine, $engines ) ) {
		return false;
	}

	return true;
}