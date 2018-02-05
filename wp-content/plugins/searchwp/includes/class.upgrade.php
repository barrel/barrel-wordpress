<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWPUpgrade handles any installation or upgrade procedures that need to take place
 *
 * @since 1.0
 */
class SearchWPUpgrade {
	/**
	 * @var string Active plugin version
	 * @since 1.0
	 */
	public $version;

	/**
	 * @var mixed|void The last version that was active
	 * @since 1.0
	 */
	public $last_version;

	/**
	 * @var string Charset for the database
	 * @since 2.5.7
	 */
	private $charset = 'utf8';

	/**
	 * @var string COLLATE SQL (when utf8mb4)
	 * @since 2.5.7
	 */
	private $collate_sql = '';


	/**
	 * Constructor
	 *
	 * @param bool|string $version string Plugin version being activated
	 *
	 * @since 1.0
	 */
	public function __construct( $version = false ) {

		global $wpdb;

		// WordPress 4.2 added support for utf8mb4
		if ( $wpdb->has_cap( 'utf8mb4' ) ) {
			$this->charset = 'utf8mb4';
			$this->collate_sql = ' COLLATE utf8mb4_unicode_ci ';
		}

		if ( ! empty( $version ) ) {
			$this->version      = $version;
			$this->last_version = get_option( SEARCHWP_PREFIX . 'version' );

			if ( false === $this->last_version ) {
				$this->last_version = 0;
			}

			if ( version_compare( $this->last_version, $this->version, '<' ) ) {
				if ( version_compare( $this->last_version, '0.1.0', '<' ) ) {
					$this->install();

					// if this is a fresh install it means that the indexer can support utf8mb4
					if ( 'utf8mb4' === $this->charset ) {
						add_option( SEARCHWP_PREFIX . 'utf8mb4', true, '', 'no' );
					}
				} else {
					$this->upgrade();
				}

				update_option( SEARCHWP_PREFIX . 'version', $this->version, 'no' );
			}
		}

	}


	/**
	 * Installation procedure. Save default settings and insert database tables.
	 *
	 * @since 1.0
	 */
	private function install() {

		/**
		 * Save our default settings so we have a working search engine on activation
		 * that matches what WordPress does out of the box; include post types that are
		 * not specifically set to exclude_from_search
		 */
		$settings = array(
			'engines' => array(
				'default' => array(),
			),
		);

		$post_types = array_merge(
			array(
				'post' => 'post',
				'page' => 'page',
			),
			get_post_types(
				array(
					'exclude_from_search' => false,
					'_builtin' => false,
				)
			)
		);

		// @since 2.9.0 we have a new engine model generator
		foreach ( $post_types as $post_type ) {

			$settings['engines']['default'][ $post_type ] = SWP()->get_default_config_for_post_type( $post_type );

			// Default post type config is disabled, but in this case we want to enable these post types
			// because these post types were what was considered for search before installing SearchWP
			$settings['engines']['default'][ $post_type ]['enabled'] = true;

			// We're also going to do some additional formatting as introduced by 2.9.0
			// Because of the way the data model is set up in Vue the default is an empty
			// object but for back compat we're going to move it back to an array here
			$settings['engines']['default'][ $post_type ]['weights']['cf'] = array();
		}

		// allow developers to filter the default engine settings
		$default_engine = $settings['engines']['default'];
		$settings['engines'] = apply_filters( 'searchwp_initial_engine_settings', $settings['engines'] );

		// @since 2.9.0 we need to ensure that there is a default engine
		if ( ! isset( $settings['engines']['default'] ) ) {
			$filtered_engines = $settings['engines'];
			$settings['engines']['default'] = $default_engine;
			$settings['engines'] = array_merge( $settings['engines'], $filtered_engines );
		}

		// Always run through the validator
		$valid_settings = SWP()->validate_settings(
			array(
				'engines' => $settings['engines'],
			)
		);
		$settings['engines'] = $valid_settings['engines'];

		searchwp_generate_settings( $settings['engines'] );

		$this->create_tables();

		searchwp_add_option( 'progress', 0 );

		// Set a flag to prevent the indexer from automatically starting (and also tell SearchWP to short circuit until further notice)
		searchwp_set_setting( 'initial_settings', false );
	}

	/**
	 * Create necessary custom database tables
	 */
	function create_tables() {

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// main index table
		$sql = "
			CREATE TABLE {$wpdb->prefix}swp_index (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				term bigint(20) unsigned NOT NULL,
				content bigint(20) unsigned NOT NULL DEFAULT '0',
				title bigint(20) unsigned NOT NULL DEFAULT '0',
				comment bigint(20) unsigned NOT NULL DEFAULT '0',
				excerpt bigint(20) unsigned NOT NULL DEFAULT '0',
				slug bigint(20) unsigned NOT NULL DEFAULT '0',
				post_id bigint(20) unsigned NOT NULL,
				PRIMARY KEY (id),
				KEY termindex (term),
  				KEY postidindex (post_id)
			) DEFAULT CHARSET=" . $this->charset . $this->collate_sql;
		/** @noinspection PhpInternalEntityUsedInspection */
		dbDelta( $sql );

		// terms table

		// if utf8mb4 collation is supported, add it
		$varchar_collate = '';
		if ( 'utf8mb4' === $this->charset ) {
			// normally it's utfmb4_unicode_ci but that is not strict enough for UNIQUE keys
			$varchar_collate = ' COLLATE utf8mb4_bin ';
		}
		$sql = "
			CREATE TABLE {$wpdb->prefix}swp_terms (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				term varchar(80) {$varchar_collate} NOT NULL DEFAULT '',
				reverse varchar(80) {$varchar_collate} NOT NULL DEFAULT '',
				stem varchar(80) {$varchar_collate} NOT NULL DEFAULT '',
				PRIMARY KEY (id),
				UNIQUE KEY termunique (term),
				KEY termindex (term(2)),
  				KEY stemindex (stem(2))
			) DEFAULT CHARSET=" . $this->charset . $varchar_collate;
		/** @noinspection PhpInternalEntityUsedInspection */
		dbDelta( $sql );

		// custom field table
		$sql = "
			CREATE TABLE {$wpdb->prefix}swp_cf (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				metakey varchar(190) {$this->collate_sql} NOT NULL DEFAULT '',
				term int(20) unsigned NOT NULL,
				count bigint(20) unsigned NOT NULL,
				post_id bigint(20) unsigned NOT NULL,
				PRIMARY KEY (id),
				KEY metakey (metakey),
				KEY term (term),
				KEY postidindex (post_id)
			) DEFAULT CHARSET=" . $this->charset . $this->collate_sql;
		/** @noinspection PhpInternalEntityUsedInspection */
		dbDelta( $sql );

		// taxonomy table
		$sql = "
			CREATE TABLE {$wpdb->prefix}swp_tax (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				taxonomy varchar(32) {$this->collate_sql} NOT NULL,
				term int(20) unsigned NOT NULL,
				count bigint(20) unsigned NOT NULL,
				post_id bigint(20) unsigned NOT NULL,
				PRIMARY KEY (id),
				KEY taxonomy (taxonomy),
				KEY term (term),
				KEY postidindex (post_id)
			) DEFAULT CHARSET=" . $this->charset . $this->collate_sql;
		/** @noinspection PhpInternalEntityUsedInspection */
		dbDelta( $sql );

		// log table
		$sql = "
			CREATE TABLE {$wpdb->prefix}swp_log (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	            event enum('search','action') {$this->collate_sql} NOT NULL DEFAULT 'search',
	            query varchar(191) {$this->collate_sql} NOT NULL DEFAULT '',
	            tstamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	            hits mediumint(9) unsigned NOT NULL,
	            engine varchar(191) {$this->collate_sql} NOT NULL DEFAULT 'default',
	            wpsearch tinyint(1) NOT NULL,
	            PRIMARY KEY (id),
	            KEY eventindex (event),
	            KEY queryindex (query(191)),
	            KEY engineindex (engine(191))
			) DEFAULT CHARSET=" . $this->charset . $this->collate_sql;
		/** @noinspection PhpInternalEntityUsedInspection */
		dbDelta( $sql );
	}


	/**
	 * Upgrade routines
	 *
	 * @since 1.0
	 */
	private function upgrade() {
		global $wpdb, $searchwp;

		if ( version_compare( $this->last_version, '1.3.1', '<' ) ) {
			// clean up misuse of cron schedule
			wp_clear_scheduled_hook( 'swp_cron_indexer' );
		}

		if ( version_compare( $this->last_version, '1.6.7', '<' ) ) {
			// truncate logs table
			$prefix = $wpdb->prefix . SEARCHWP_DBPREFIX;
			$tableName = $prefix . 'log';
			$wpdb->query( "TRUNCATE TABLE {$tableName}" );
		}

		if ( version_compare( $this->last_version, '1.8', '<' ) ) {
			// fix a possible issue with settings storage resulting in MySQL errors after update
			$settings = get_option( SEARCHWP_PREFIX . 'settings' );
			if ( is_array( $settings ) ) {
				// make sure additional array keys are present and defined
				foreach ( $settings['engines'] as $engine_key => $engine_setting ) {
					foreach ( $settings['engines'][ $engine_key ] as $post_type => $post_type_settings ) {
						if ( is_array( $settings['engines'][ $engine_key ][ $post_type ] ) && isset( $settings['engines'][ $engine_key ][ $post_type ]['options'] ) && ! is_array( $settings['engines'][ $engine_key ][ $post_type ]['options'] ) ) {
							$settings['engines'][ $engine_key ][ $post_type ]['options'] = array(
								'exclude' 		=> false,
								'attribute_to' 	=> false,
								'stem' 			=> false,
							);
						}
					}
				}
			}
			searchwp_update_option( 'settings', $settings );
		}

		// index cleanup and optimization
		if ( version_compare( $this->last_version, '1.9', '<' ) ) {

			$index_exists = $wpdb->get_results( "SHOW INDEX FROM `{$wpdb->prefix}swp_terms` WHERE Key_name = 'termindex'" , ARRAY_N );
			if ( ! empty( $index_exists ) ) {
				$wpdb->query( "ALTER TABLE {$wpdb->prefix}swp_terms DROP INDEX termindex;" );
			}

			$index_exists = $wpdb->get_results( "SHOW INDEX FROM `{$wpdb->prefix}swp_terms` WHERE Key_name = 'stemindex'" , ARRAY_N );
			if ( ! empty( $index_exists ) ) {
				$wpdb->query( "ALTER TABLE {$wpdb->prefix}swp_terms DROP INDEX stemindex;" );
			}

			$index_exists = $wpdb->get_results( "SHOW INDEX FROM `{$wpdb->prefix}swp_terms` WHERE Key_name = 'id'" , ARRAY_N );
			if ( ! empty( $index_exists ) ) {
				$wpdb->query( "ALTER TABLE {$wpdb->prefix}swp_terms DROP INDEX id;" );
			}

			$index_exists = $wpdb->get_results( "SHOW INDEX FROM `{$wpdb->prefix}swp_index` WHERE Key_name = 'id'" , ARRAY_N );
			if ( ! empty( $index_exists ) ) {
				$wpdb->query( "ALTER TABLE {$wpdb->prefix}swp_index DROP INDEX id;" );
			}

			$wpdb->query( "CREATE INDEX termindex ON {$wpdb->prefix}swp_terms(term(2));" );
			$wpdb->query( "CREATE INDEX stemindex ON {$wpdb->prefix}swp_terms(stem(2));" );
		}

		// consolidate settings into one database record
		if ( version_compare( $this->last_version, '1.9.1', '<' ) ) {

			$index_exists = $wpdb->get_results( "SHOW INDEX FROM `{$wpdb->prefix}swp_terms` WHERE Key_name = 'termindex'" , ARRAY_N );
			if ( empty( $index_exists ) ) {
				$wpdb->query( "CREATE INDEX termindex ON {$wpdb->prefix}swp_terms(term(2));" );
			}

			$index_exists = $wpdb->get_results( "SHOW INDEX FROM `{$wpdb->prefix}swp_terms` WHERE Key_name = 'stemindex'" , ARRAY_N );
			if ( empty( $index_exists ) ) {
				$wpdb->query( "CREATE INDEX stemindex ON {$wpdb->prefix}swp_terms(term(2));" );
			}

			$old_settings = searchwp_get_option( 'settings' );
			$engines = isset( $old_settings['engines'] ) ? $old_settings['engines'] : array();

			// clear out the old settings because we're using the same key
			searchwp_delete_option( 'settings' );

			// in with the new
			searchwp_generate_settings( $engines );

			// delete the old options
			searchwp_delete_option( 'activated' );
			searchwp_delete_option( 'license_nag' );
			searchwp_delete_option( 'dismissed' );
			searchwp_delete_option( 'ignored_queries' );
			searchwp_delete_option( 'indexer_nag' );
			searchwp_delete_option( 'valid_db_environment' );
			searchwp_delete_option( 'running' );
			searchwp_delete_option( 'total' );
			searchwp_delete_option( 'remaining' );
			searchwp_delete_option( 'done' );
			searchwp_delete_option( 'in_process' );
			searchwp_delete_option( 'initial' );
			searchwp_delete_option( 'initial_notified' );
			searchwp_delete_option( 'purgeQueue' );
			searchwp_delete_option( 'processingPurgeQueue' );
			searchwp_delete_option( 'mysql_version_nag' );
			searchwp_delete_option( 'remote' );
			searchwp_delete_option( 'remote_meta' );
			searchwp_delete_option( 'paused' );
			searchwp_delete_option( 'nuke_on_delete' );
			searchwp_delete_option( 'indexnonce' );
		}

		if ( version_compare( $this->last_version, '1.9.2.2', '<' ) ) {
			searchwp_add_option( 'progress', 0 );
		}

		if ( version_compare( $this->last_version, '1.9.4', '<' ) ) {
			// clean up a potential useless settings save
			$live_settings = searchwp_get_option( 'settings' );
			$update_settings_record = false;
			if ( is_array( $live_settings ) ) {
				foreach ( $live_settings as $live_setting_key => $live_setting_value ) {
					// none of our keys should be numeric (specifically going after a rogue 'running' setting that
					// may have been inadvertently set in 1.9.2, we just don't want it in there at all
					if ( is_numeric( $live_setting_key ) ) {
						unset( $live_settings[ $live_setting_key ] );
						$update_settings_record = true;
					}
					// also update 'nuke_on_delete' to be a boolean if necessary
					if ( 'nuke_on_delete' === $live_setting_key ) {
						$live_settings['nuke_on_delete'] = empty( $live_setting_value ) ? false : true;
						$update_settings_record = true;
					}
				}
			}
			if ( $update_settings_record ) {
				// save the cleaned up settings array
				searchwp_update_option( 'settings', $live_settings );
				$searchwp->settings = $live_settings;
			}
		}

		if ( version_compare( $this->last_version, '1.9.5', '<' ) ) {
			// move indexer-specific settings to their own record as they're being constantly updated
			$live_settings = searchwp_get_option( 'settings' );
			$indexer_settings = array();

			// whether the initial index has been built
			if ( isset( $live_settings['initial_index_built'] ) ) {
				$indexer_settings['initial_index_built'] = (bool) $live_settings['initial_index_built'];
				unset( $live_settings['initial_index_built'] );
			} else {
				$indexer_settings['initial_index_built'] = false;
			}

			// all of the stats
			if ( isset( $live_settings['stats'] ) ) {
				$indexer_settings['stats'] = $live_settings['stats'];
				unset( $live_settings['stats'] );
			} else {
				$indexer_settings['stats'] = array();
			}

			// whether the indexer is running
			if ( isset( $live_settings['running'] ) ) {
				$indexer_settings['running'] = (bool) $live_settings['running'];
				unset( $live_settings['running'] );
			} else {
				$indexer_settings['running'] = false;
			}

			// whether the indexer is paused (disabled)
			if ( isset( $live_settings['paused'] ) ) {
				$indexer_settings['paused'] = (bool) $live_settings['paused'];
				unset( $live_settings['paused'] );
			} else {
				$indexer_settings['paused'] = false;
			}

			// whether the indexer is processing the purge queue
			if ( isset( $live_settings['processing_purge_queue'] ) ) {
				$indexer_settings['processing_purge_queue'] = (bool) $live_settings['processing_purge_queue'];
				unset( $live_settings['processing_purge_queue'] );
			} else {
				$indexer_settings['processing_purge_queue'] = false;
			}

			// the purge queue will be moved to it's own option to avoid conflict
			if ( isset( $live_settings['purge_queue'] ) ) {
				searchwp_add_option( 'purge_queue', $live_settings['purge_queue'] );
				unset( $live_settings['purge_queue'] );
			}

			searchwp_update_option( 'settings', $live_settings );
			searchwp_add_option( 'indexer', $indexer_settings );

		}

		if ( version_compare( $this->last_version, '1.9.6', '<' ) ) {
			// wake up the indexer if necessary
			$running = searchwp_get_setting( 'running' );
			if ( empty( $running ) ) {
				searchwp_set_setting( 'running', false );
			}
		}

		// make ignored queries for search stats per-user
		if ( version_compare( $this->last_version, '2.0.2', '<' ) ) {
			$user_id = get_current_user_id();
			if ( $user_id ) {
				$ignored_queries = searchwp_get_setting( 'ignored_queries' );
				update_user_meta( $user_id, SEARCHWP_PREFIX . 'ignored_queries', $ignored_queries );
			}
		}

		// add 'busy' option
		if ( version_compare( $this->last_version, '2.1.5', '<' ) ) {
			searchwp_add_option( 'busy', false );
			searchwp_add_option( 'doing_delta', false );
		}

		// force a wakeup
		if ( version_compare( $this->last_version, '2.2.1', '<' ) ) {
			if ( function_exists( 'searchwp_wake_up_indexer' ) ) {
				searchwp_wake_up_indexer();
			}
		}

		// add new 'waiting' flag, prep for possible new custom endpoint, clear out redundant post meta
		if ( version_compare( $this->last_version, '2.3', '<' ) ) {
			searchwp_add_option( 'waiting', false );
			searchwp_set_setting( 'endpoint', '' );

			// now using last_index instead of indexed, we don't need separate records
			$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => '_' . SEARCHWP_PREFIX . 'indexed' ) );
		}

		if ( version_compare( $this->last_version, '2.4.5', '<' ) ) {

			// implement our settings backup
			$live_settings = searchwp_get_option( 'settings' );
			$settings_backups = array();
			$settings_backups[ current_time( 'timestamp' ) ] = $live_settings;
			searchwp_add_option( 'settings_backup', $settings_backups );

			// there was a bug triggered by a custom post type name of 'label' that caused issues
			// so we need to update all of the supplemental engine label keys to searchwp_engine_label
			// which will not trigger the issue because it is 21 characters in length and WordPress
			// requires post type names to be 20 characters or less
			if ( isset( $live_settings['engines'] ) ) {
				foreach ( $live_settings['engines'] as $live_settings_engine_key => $live_settings_engine_values ) {
					if ( isset( $live_settings_engine_values['label'] ) ) {
						$engine_label = $live_settings_engine_values['label'];
						unset( $live_settings['engines'][ $live_settings_engine_key ]['label'] );
						$live_settings['engines'][ $live_settings_engine_key ]['searchwp_engine_label'] = $engine_label;
					}
				}
			}
			searchwp_update_option( 'settings', $live_settings );
		}

		/**
		 * The upgrade routine for 2.5.7 was designed to implement support for utf8mb4 as per WordPress 4.2, it even
		 * used the same code to do so. Unfortunately the index key changes and charset changes can take a very (very)
		 * long time depending on the power of the server and the size of the database tables. Unfortunately SearchWP's
		 * tables are quite large, and the update routine took *way* too long on some test machines. While the update
		 * was running, performance on the front end was erratic at best, primarily because the table updates caused
		 * MySQL to utilize ~100% CPU, thus preventing other traffic from reaching the server. As a result, existing
		 * installations of SearchWP will not be converted to utf8mb4, only fresh installations. The indexer and search
		 * algorithm will actively strip out problematic characters (e.g. emoji) if the tables are not prepared for them.
		 *

		if ( version_compare( $this->last_version, '2.5.7', '<' ) ) {

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			SWP()->indexer_pause();

			// utf8mb4 index length limit is 191 @link https://make.wordpress.org/core/2015/04/02/the-utf8mb4-upgrade/
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}swp_cf DROP INDEX metakey, ADD INDEX metakey(metakey(191));" );
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}swp_log DROP INDEX queryindex, ADD INDEX queryindex(query(191));" );
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}swp_log DROP INDEX engineindex, ADD INDEX engineindex(engine(191));" );

			// loop through tables and upgrade them to utf8mb4
			$tables = array(
				$wpdb->prefix . 'swp_cf',
				$wpdb->prefix . 'swp_index',
				$wpdb->prefix . 'swp_log',
				$wpdb->prefix . 'swp_tax',
				$wpdb->prefix . 'swp_terms',
			);

			$successful = true;

			foreach ( $tables as $table ) {

				// WordPress 4.2 added maybe_convert_table_to_utf8mb4() but
				// we don't necessarily have access to it (e.g. user is running <4.2)
				// but we also don't want to have to keep track of what WP version
				// is in play and have to continually compare that to whether this
				// upgrade routine has run so the function has been copied verbatim
				// for use here because utf8mb4 is fully backwards compatible so we're
				// going for the full upgrade by using a copy of that function

				$result = searchwp_maybe_convert_table_to_utf8mb4( $table );
				if ( ( is_wp_error( $result ) || false === $result ) ) {
					// there was a problem
					$successful = false;
				}
			}

			if ( ! $successful ) {
				// there was a problem with the utf8mb4 upgrade but that doesn't necessarily
				// mean there is a show-stopping issue, just that the table is still utf8
				// so log that the upgrade failed and indicate it in System Info
				searchwp_add_option( 'utf8mb4_upgrade_failed', true );
			}

			SWP()->indexer_unpause();
		}
		*/

		if ( version_compare( $this->last_version, '2.8', '<' ) ) {
			$swp_live_settings = get_option( SEARCHWP_PREFIX . 'settings' );
			$swp_nuke_on_delete = isset( $swp_live_settings['nuke_on_delete'] ) ? $swp_live_settings['nuke_on_delete'] : false;

			if ( ! empty( $swp_nuke_on_delete ) ) {
				// migrate enabled Nuke on Delete setting to new toggle abstraction
				$existing_settings = searchwp_get_option( 'advanced' );

				if ( ! is_array( $existing_settings ) ) {
					$existing_settings = array();
				}

				// swap it
				$existing_settings['toggle_nuke_on_delete'] = 1;

				// save the updated value
				searchwp_update_option( 'advanced', $existing_settings );
			}
		}

		// Admin Bar option to disable indexer needs to use Advanced tab toggle, not it's own setting
		if ( version_compare( $this->last_version, '2.8.2', '<' ) ) {
			$swp_282_maybe_paused = searchwp_get_option( 'paused' );

			if ( ! empty( $swp_282_maybe_paused ) ) {
				$saved_settings = searchwp_get_option( 'advanced' );

				if ( ! is_array( $saved_settings ) ) {
					$saved_settings = array();
				}

				$saved_settings['disable_indexer'] = true;

				searchwp_update_option( 'advanced', $saved_settings );
			}
		}

		// Update proper autoload flag for settings
		if ( version_compare( $this->last_version, '2.8.3', '<' ) ) {

			// these names should be autoloaded, nothing else should be
			$all_options = array(
				'settings',
				'settings_backup',
				'indexer',
				'purge_queue',
				'version',
				'progress',
				'doing_delta',
				'paused',
				'delta_attempts',
				'waiting',
				'busy',
				'license_key',
				'advanced',
				'transient',
				'utf8mb4',
			);

			// back up the settings
			$live_settings = searchwp_get_option( 'settings' );
			$settings_backups = array();
			$settings_backups[ current_time( 'timestamp' ) ] = $live_settings;
			update_option( SEARCHWP_PREFIX . 'settings_backup', $settings_backups, false );

			$autoload_options = searchwp_get_autoload_options();

			// update autoload flag for settings
			foreach ( $all_options as $option ) {

				// if it's supposed to autoload, forget about it
				if ( in_array( $option, $autoload_options, true ) ) {
					continue;
				}

				$existing = get_option( SEARCHWP_PREFIX . $option );

				// If the option was simply updated, nothing would change because the value is the same
				// so we need to delete it and then re-add it using the proper autoload flag
				delete_option( SEARCHWP_PREFIX . $option );

				add_option( SEARCHWP_PREFIX . $option, $existing, '', 'no' );
				unset( $existing );
			}
		}

		if ( version_compare( $this->last_version, '2.9', '<' ) ) {
			// back up the settings
			searchwp_add_settings_backup();

			// Set the flag to indicate that there are already existing settings that have been set
			searchwp_set_setting( 'initial_settings', true );

			// Set the flag to indicate that the existing engine configuration is considered legacy
			searchwp_set_setting( 'legacy_engines', true );
		}
	}

}

/**
 * Create a backup of the current SearchWP settings
 *
 * @since 2.9.0
 */
function searchwp_add_settings_backup() {
	$live_settings = searchwp_get_option( 'settings' );
	$settings_backups = searchwp_get_option( 'settings_backup' );
	if ( empty( $settings_backups ) ) {
		$settings_backups = array();
	}
	$settings_backups[ current_time( 'timestamp' ) ] = $live_settings;
	update_option( SEARCHWP_PREFIX . 'settings_backup', $settings_backups, 'no' );
}

/**
 * TAKEN FROM wp-admin/includes/upgrade.php::maybe_convert_table_to_utf8mb4()
 *
 * If a table only contains utf8 or utf8mb4 columns, convert it to utf8mb4.
 *
 * @since 4.2.0
 *
 * @param string $table The table to convert.
 * @return bool true if the table was converted, false if it wasn't.
 */
function searchwp_maybe_convert_table_to_utf8mb4( $table ) {
	global $wpdb;

	$results = $wpdb->get_results( "SHOW FULL COLUMNS FROM `$table`" );
	if ( ! $results ) {
		return false;
	}

	foreach ( $results as $column ) {
		if ( $column->Collation ) {
			list( $charset ) = explode( '_', $column->Collation );
			$charset = strtolower( $charset );
			if ( 'utf8' !== $charset && 'utf8mb4' !== $charset ) {
				// Don't upgrade tables that have non-utf8 columns.
				return false;
			}
		}
	}

	// WordPress core uses utf8mb4_unicode_ci as the default so we will too...
	$collate = 'utf8mb4_unicode_ci';
	if ( 'swp_terms' === substr( $table, strlen( $wpdb->prefix ), strlen( $table ) - 1 ) ) {
		// ... but we need a more strict collation on the term column in the term table
		// because it has a UNIQUE key and utf8mb4_unicode_ci isn't strict enough
		$collate = 'utf8mb4_bin';
	}

	return $wpdb->query( "ALTER TABLE $table CONVERT TO CHARACTER SET utf8mb4 COLLATE " . $collate );
}


function searchwp_generate_settings( $engines ) {

	$searchwp = SWP();

	// grab this early because they're going to be nested
	$dismissed_filter_nags = searchwp_get_option( 'dismissed' );
	$dismissed_filter_nags = isset( $dismissed_filter_nags['filter_conflicts'] ) ? $dismissed_filter_nags['filter_conflicts'] : array();

	$in_process = searchwp_get_option( 'in_process' );
	$in_process = is_array( $in_process ) ? $in_process : null;

	// reformat all of the saved settings
	$new_settings = array(
		'engines' => $engines,
		'activated' => (bool) searchwp_get_option( 'activated' ),
		'dismissed' => array(
			'filter_conflicts' => $dismissed_filter_nags,
			'nags' => array(),
		),
		'notices' => array(),
		'valid_db_environment' => (bool) searchwp_get_option( 'valid_db_environment' ),
		'ignored_queries' => searchwp_get_option( 'ignored_queries' ),
		'remote' => searchwp_get_option( 'remote' ),
		'remote_meta' => searchwp_get_option( 'remote_meta' ),
		'nuke_on_delete' => searchwp_get_option( 'nuke_on_delete' ),
	);

	// break out settings specific to the indexer since that runs independently
	$indexer_settings = array(
		'initial_index_built' => (bool) searchwp_get_option( 'initial' ),
		'stats'     => array(
			'done' => (int) searchwp_get_option( 'done' ),
			'in_process' => $in_process,
			'remaining' => (int) searchwp_get_option( 'remaining' ),
			'total' => (int) searchwp_get_option( 'total' ),
			'last_activity' => (int) searchwp_get_option( 'last_activity' ),
		),
		'running' => (bool) searchwp_get_option( 'running' ),
		'paused' => searchwp_get_option( 'paused' ),
		'processing_purge_queue' => searchwp_get_option( 'processingPurgeQueue' ),
	);

	// set the nags
	if ( searchwp_get_option( 'indexer_nag' ) ) {
		$new_settings['dismissed']['nags'][] = 'indexer';
	}
	if ( searchwp_get_option( 'license_nag' ) ) {
		$new_settings['dismissed']['nags'][] = 'license';
	}
	if ( searchwp_get_option( 'mysql_version_nag' ) ) {
		$new_settings['dismissed']['nags'][] = 'mysql_version';
	}

	// set the notices
	if ( searchwp_get_option( 'initial_notified' ) ) {
		$new_settings['notices'][] = 'initial';
	}

	// save the new options
	searchwp_add_option( 'settings', $new_settings );
	searchwp_add_option( 'settings_backup', array() );
	searchwp_add_option( 'indexer', $indexer_settings );
	searchwp_add_option( 'purge_queue', searchwp_get_option( 'purgeQueue' ) );

	// force our new settings in place
	$searchwp->settings = $new_settings;
	$searchwp->settings_updated = true;
}
