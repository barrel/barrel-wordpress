<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_Admin_Ajax is responsible for implementing admin-only AJAX callbacks
 *
 * @since 2.8
 */
class SearchWP_Admin_Ajax {

	/**
	 * SearchWP_Admin_Ajax constructor.
	 */
	public function __construct() {}

	/**
	 * Initializer
	 */
	public function init() {
		add_action( 'wp_ajax_searchwp_get_tax_terms',         array( $this, 'get_tax_terms' ) );
		add_action( 'wp_ajax_searchwp_get_meta_keys',         array( $this, 'get_meta_keys' ) );
		add_action( 'wp_ajax_searchwp_get_setting',           array( $this, 'get_setting' ) );
		add_action( 'wp_ajax_searchwp_set_setting',           array( $this, 'set_setting' ) );
		add_action( 'wp_ajax_searchwp_get_index_stats',       array( $this, 'get_index_stats' ) );
		add_action( 'wp_ajax_searchwp_save_engines',          array( $this, 'save_engines' ) );
		add_action( 'wp_ajax_searchwp_reset_index',           array( $this, 'reset_index' ) );
		add_action( 'wp_ajax_searchwp_basic_auth',            array( $this, 'is_basic_auth_blocking' ) );

		add_action( 'wp_ajax_searchwp_get_statistics',        array( $this, 'get_statistics' ) );
		add_action( 'wp_ajax_searchwp_reset_stats',           array( $this, 'reset_stats' ) );
		add_action( 'wp_ajax_searchwp_ignore_search',         array( $this, 'ignore_search' ) );
		add_action( 'wp_ajax_searchwp_unignore_search',       array( $this, 'unignore_search' ) );
		add_action( 'wp_ajax_searchwp_recreate_tables',       array( $this, 'recreate_tables' ) );
		add_action( 'wp_ajax_searchwp_update_stopwords',      array( $this, 'update_stopwords' ) );
		add_action( 'wp_ajax_searchwp_update_synonyms',       array( $this, 'update_synonyms' ) );
		add_action( 'wp_ajax_searchwp_wake_indexer',          array( $this, 'wake_indexer' ) );
		add_action( 'wp_ajax_searchwp_reset_notices',         array( $this, 'reset_notices' ) );
		add_action( 'wp_ajax_searchwp_update_setting',        array( $this, 'update_setting' ) );
		add_action( 'wp_ajax_searchwp_config_import',         array( $this, 'config_import' ) );
		add_action( 'wp_ajax_searchwp_stopwords_suggestions', array( $this, 'get_stopwords_suggestions' ) );
	}

	/**
	 * Callback to retrieve stopwords suggestions
	 *
	 * @since 3.0
	 */
	public function get_stopwords_suggestions() {
		check_ajax_referer( 'searchwp_ajax_stopwords_suggestions' );

		do_action( 'searchwp_log', 'Getting stopwords suggestions (AJAX)' );

		$limit = apply_filters( 'searchwp_stopwords_suggestions_limit', 20 );

		$suggested_stopwords = SWP()->stopwords->get_suggested_stopwords( array(
			'threshold' => SWP()->stopwords->get_threshold(),
			'limit'     => absint( $limit ),
		) );

		do_action( 'searchwp_log', 'Getting stopwords suggestions (complete)' );

		wp_send_json_success( $suggested_stopwords );
	}

	/**
	 * Callback to import engine config
	 *
	 * @since 3.0
	 */
	public function config_import() {
		check_ajax_referer( 'searchwp_ajax_config_import' );

		do_action( 'searchwp_log', 'Resetting notices (AJAX)' );

		$settings_to_import = isset( $_REQUEST['import'] ) ? stripslashes( $_REQUEST['import'] ) : '';
		SWP()->import_settings( $settings_to_import ); // Expects JSON.

		wp_send_json_success();
	}

	/**
	 * Callback to update a setting
	 *
	 * @since 3.0
	 */
	public function update_setting() {
		check_ajax_referer( 'searchwp_ajax_update_setting' );

		do_action( 'searchwp_log', 'Updating setting (AJAX)' );

		$setting = isset( $_REQUEST['setting'] ) ? $_REQUEST['setting'] : '';
		$value   = isset( $_REQUEST['value'] ) ? $_REQUEST['value'] : false;

		// @since 3.0.6 Admin search is a compound value so we need to extract
		if ( 'admin_search' === $setting ) {
			$compound_value = json_decode( stripslashes( $value ) );
			$value          = empty( $compound_value->enabled ) ? 'false' : 'true';
			$engine         = isset( $compound_value->engine ) ? $compound_value->engine : 'default';
			$admin_engine   = SWP()->is_valid_engine( $engine ) ? $engine : 'default';
		}

		if ( 'false' == $value || empty( $value ) ) {
			$value = false;
		} else {
			$value = true;
		}

		$available_toggles = searchwp_get_settings_names();

		if ( ! in_array( $setting, $available_toggles, true ) ) {
			wp_send_json_error();
		}

		// get the existing value
		$existing_settings = searchwp_get_option( 'advanced' );

		if ( ! is_array( $existing_settings ) ) {
			$existing_settings = array();
		}

		$existing_settings[ $setting ] = $value;

		if ( ! empty( $admin_engine ) ) {
			$existing_settings['admin_engine'] = $admin_engine;
		}

		searchwp_update_option( 'advanced', $existing_settings );

		wp_send_json_success();
	}

	/**
	 * Callback to reset statistics
	 *
	 * @since 3.0
	 */
	public function reset_stats() {
		check_ajax_referer( 'searchwp_ajax_reset_stats' );

		do_action( 'searchwp_log', 'Resetting stats (AJAX)' );

		$stats = new SearchWP_Stats();
		$stats->reset();

		wp_send_json_success();
	}

	/**
	 * Callback to reset notices
	 *
	 * @since 3.0
	 */
	public function reset_notices() {
		check_ajax_referer( 'searchwp_ajax_reset_notices' );

		do_action( 'searchwp_log', 'Resetting notices (AJAX)' );

		$existing_dismissals = searchwp_get_setting( 'dismissed' );
		$existing_dismissals['filter_conflicts'] = array();

		searchwp_set_setting( 'dismissed', $existing_dismissals );

		wp_send_json_success();
	}

	/**
	 * Callback to wake up the indexer
	 *
	 * @since 3.0
	 */
	public function wake_indexer() {
		check_ajax_referer( 'searchwp_ajax_wake_indexer' );

		do_action( 'searchwp_log', 'Waking up the indexer (AJAX)' );

		searchwp_wake_up_indexer();

		SWP()->trigger_index();

		wp_send_json_success();
	}

	/**
	 * Callback to update Synonyms
	 *
	 * @since 3.0
	 */
	public function update_synonyms() {
		check_ajax_referer( 'searchwp_ajax_update_synonyms' );

		do_action( 'searchwp_log', 'Updating Synonyms (AJAX)' );

		$synonyms = isset( $_REQUEST['synonyms'] ) ? stripslashes( $_REQUEST['synonyms'] ) : array();

		// Update method expects an array.
		$synonyms = json_decode( $synonyms, true );

		SWP()->synonyms->update( $synonyms );

		wp_send_json_success();
	}

	/**
	 * Callback to update Stopwords
	 *
	 * @since 3.0
	 */
	public function update_stopwords() {
		check_ajax_referer( 'searchwp_ajax_update_stopwords' );

		do_action( 'searchwp_log', 'Updating Stopwords (AJAX)' );

		$stopwords = isset( $_REQUEST['stopwords'] ) ? json_decode( stripslashes( $_REQUEST['stopwords'] ) ) : array();

		SWP()->stopwords->update( $stopwords );

		wp_send_json_success();
	}

	/**
	 * Callback to recreate database tables
	 *
	 * @since 3.0
	 */
	public function recreate_tables() {
		check_ajax_referer( 'searchwp_ajax_recreate_tables' );

		do_action( 'searchwp_log', 'Recreating database tables (AJAX)' );

		$upgrader = new SearchWPUpgrade();
		$upgrader->create_tables();

		SWP()->purge_index();

		$database_tables_recreated = SWP()->custom_db_tables_exist();

		if ( $database_tables_recreated ) {
			wp_send_json_success();
		} else {
			wp_send_json_error( __( 'There was an error recreating the database tables', 'searchwp' ) );
		}
	}

	/**
	 * Callback to retrieve all statistics
	 *
	 * @since 3.0
	 */
	public function get_statistics() {
		global $wpdb;

		check_ajax_referer( 'searchwp_ajax_get_statistics' );

		do_action( 'searchwp_log', 'Retrieving stats (AJAX)' );

		$engine = isset( $_REQUEST['engine'] ) ? $_REQUEST['engine'] : 'default';

		if ( ! SWP()->is_valid_engine( $engine ) ) {
			wp_send_json_error( __( 'Invalid engine', 'searchwp' ) );
		}

		$stats = new SearchWP_Stats();

		$ignored_queries = $stats->get_ignored_queries();

		$searches_over_time_args = array( 'exclude' => $ignored_queries );

		$statistics = array(
			'searches_over_time' => $stats->searches_over_time( 30, $engine, $searches_over_time_args ),
			'popular_today' => $stats->get_popular_searches(
				array(
					'days'      => 1,
					'engine'    => $engine,
					'exclude'   => $ignored_queries,
				)
			),
			'popular_week' => $stats->get_popular_searches(
				array(
					'days'      => 7,
					'engine'    => $engine,
					'exclude'   => $ignored_queries,
				)
			),
			'popular_month' => $stats->get_popular_searches(
				array(
					'days'      => 30,
					'engine'    => $engine,
					'exclude'   => $ignored_queries,
				)
			),
			'popular_year' => $stats->get_popular_searches(
				array(
					'days'      => 365,
					'engine'    => $engine,
					'exclude'   => $ignored_queries,
				)
			),
			'failed' => $stats->get_popular_searches(
				array(
					'days'      => 30,
					'engine'    => $engine,
					'exclude'   => $ignored_queries,
					'min_hits'  => false,
					'max_hits'  => 0,
				)
			),
			'ignored' => array()
		);

		$ignored_hashes = $stats->get_ignored_queries();
		if ( ! empty( $ignored_hashes ) ) {
			$ignored_hashes = array_values( $ignored_hashes );

			foreach ( $ignored_hashes as $ignored_hash ) {
				$actual_query = $stats->decode_hash( $ignored_hash );

				if ( ! empty( $actual_query ) ) {
					$statistics['ignored'][] = array(
						'hash'  => $ignored_hash,
						'query' => $actual_query,
					);
				}
			}
		}

		do_action( 'searchwp_log', 'Retrieving stats (end)' );

		wp_send_json_success( $statistics );
	}

	/**
	 * Callback to ignore a search
	 *
	 * @since 3.0
	 */
	public function ignore_search() {
		check_ajax_referer( 'searchwp_ajax_ignore_search' );

		$query_hash = isset( $_REQUEST['hash'] ) ? $_REQUEST['hash'] : '';

		if ( ! preg_match('/^[a-f0-9]{32}$/', $query_hash ) ) {
			wp_send_json_error( __( 'Invalid format', 'searchwp' ) );
		}

		$stats = new SearchWP_Stats();
		$stats->ignore_query( $query_hash );
		$stats->clear_dashboard_stats_transients();

		wp_send_json_success();
	}

	/**
	 * Callback to unignore a search
	 *
	 * @since 3.0
	 */
	public function unignore_search() {
		check_ajax_referer( 'searchwp_ajax_unignore_search' );

		$query_hash = isset( $_REQUEST['hash'] ) ? $_REQUEST['hash'] : '';

		if ( ! preg_match('/^[a-f0-9]{32}$/', $query_hash ) ) {
			wp_send_json_error( __( 'Invalid format', 'searchwp' ) );
		}

		$stats = new SearchWP_Stats();
		$stats->unignore_query( $query_hash );
		$stats->clear_dashboard_stats_transients();

		wp_send_json_success();
	}

	/**
	 * Utility function to enqueue and localize our Vue-powered scripts
	 *
	 * @param $script string The script to enqueue and localize
	 *
	 * @since 2.9
	 */
	public function enqueue_script( $script, $options = array() ) {
		$base_url = trailingslashit( SWP()->url );
		$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ) || ( isset( $_GET['script_debug'] ) ) ? '' : '.min';

		$handle = "searchwp_${script}";

		wp_register_script( $handle,
			$base_url . "assets/js/dist/${script}${debug}.js",
			null,
			SEARCHWP_VERSION,
			true
		);

		wp_enqueue_script( $handle );

		// We have a baseline of variables for all localized scripts including the indexer endpoint and all i18n
		$script_vars = $this->get_vars();

		// We also accept nonce actions here
		$nonces = array();

		if ( ! empty( $options ) && is_array( $options['nonces'] ) && ! empty( $options['nonces'] ) ) {
			foreach ( $options['nonces'] as $action ) {
				$nonces[ $action ] = wp_create_nonce( 'searchwp_ajax_' . $action );
			}
		}

		$script_vars['nonces'] = $nonces;

		// Allow for data store
		if ( ! empty( $options ) && ! empty( $options['data'] ) ) {
			$script_vars['data'] = $options['data'];
		}

		wp_localize_script(
			$handle,
			'_SEARCHWP_VARS',
			$script_vars
		);
	}

	/**
	 * All wp_localize_script calls use this method to ensure standard variables are set
	 *
	 * @since 2.9
	 */
	public function get_vars() {
		return array(
			'endpoint' => SWP()->endpoint,
			'i18n'     => SWP()->i18n->strings,
		);
	}

	/**
	 * Callback to initiate an index purge
	 *
	 * @since 2.9
	 */
	public function reset_index() {
		check_ajax_referer( 'searchwp_ajax_reset_index' );

		do_action( 'searchwp_log', 'AJAX: reset_index()' );

		searchwp_set_setting( 'index_dirty', false );

		SWP()->purge_index();

		// Manually force these values to prevent wildly inaccurate updates
		searchwp_set_setting( 'total', 0, 'stats' );
		searchwp_set_setting( 'remaining', 0, 'stats' );
		searchwp_set_setting( 'done', 0, 'stats' );
		searchwp_set_setting( 'last_activity', current_time( 'timestamp' ), 'stats' );

		sleep( 1 );

		$alt_indexer = SWP()->is_using_alternate_indexer();
		if ( empty( $alt_indexer ) ) {
			SWP()->trigger_index();
		}

		do_action( 'searchwp_log', 'AJAX: reset_index() (end)' );

		wp_send_json_success();
	}

	/**
	 * Genereate response object for index stats
	 *
	 * @since 2.9
	 */
	public function get_index_stats() {
		$ajax = is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( $ajax ) {
			check_ajax_referer( 'searchwp_ajax_get_index_stats' );
		}

		$index_stats = SWP()->settings['stats'];

		// If the stats don't exist, the index was likely just purged
		if ( empty( $index_stats ) || empty( $index_stats['last_activity'] ) ) {
			$index_stats = array(
				'last_activity' => __( 'None', 'searchwp' ),
				'done' => 0,
				'remaining' => '-',
			);
		} else {

			// If activity was happening within the past 15 seconds call it "now" else show time diff
			if ( current_time( 'timestamp' ) - absint( $index_stats['last_activity'] ) < 15 ) {
				$index_stats['last_activity'] = __( 'Right now', 'searchwp' );
			} else {
				$index_stats['last_activity'] = sprintf(
					// translators: %s = human-readable time difference
					_x( '%s ago', '%s = human-readable time difference', 'searchwp' ),
					human_time_diff(
						$index_stats['last_activity'],
						current_time( 'timestamp' )
					)
				);
			}
		}

		$index_stats['progress'] = floatval( searchwp_get_option( 'progress' ) );
		$waiting = searchwp_get_option( 'waiting' );
		$index_stats['waiting'] = ! empty( $waiting );

		$indexer = new SearchWPIndexer();
		$index_stats['main_row_count'] = $indexer->get_main_table_row_count();

		if ( $ajax ) {
			wp_send_json_success( $index_stats );
		} else {
			return $index_stats;
		}
	}

	/**
	 * Getter for SearchWP setting
	 *
	 * @since 2.9
	 */
	public function get_setting() {
		if ( empty( $_REQUEST['setting'] ) ) {
			wp_send_json_error();
		}

		$setting = sanitize_text_field( $_REQUEST['setting'] );
		$group = ! empty( $_REQUEST['group'] ) ? $_REQUEST['group'] : false;

		check_ajax_referer( 'searchwp_ajax_' . $setting );

		$value = searchwp_get_setting( $setting, $group );

		wp_send_json_success( $value );
	}

	/**
	 * Setter for SearchWP setting
	 *
	 * @since 2.9
	 */
	public function set_setting() {
		if ( empty( $_REQUEST['setting'] ) || ! isset( $_REQUEST['value'] ) ) {
			wp_send_json_error();
		}

		$setting = sanitize_text_field( $_REQUEST['setting'] );
		$value = stripslashes( $_REQUEST['value'] );
		$group = ! empty( $_REQUEST['group'] ) ? $_REQUEST['group'] : false;

		check_ajax_referer( 'searchwp_ajax_' . $setting );

		// We need to validate the engine settings, they're both strict and complex
		if ( 'engines' === $setting ) {
			// We can remove our initial settings flag
			searchwp_set_setting( 'initial_settings', true );

			if ( version_compare( PHP_VERSION, '5.3', '>=' ) && $this->is_json( $value ) ) {
				$value = json_decode( $value, true ); // Convert to arrays at the same time
			} else {
				// This is PHP 5.2 — hope for the best
				$value = json_decode( $value, true );
			}

			$value = $this->normalize_submitted_settings( $value );
			$value = SWP()->validate_settings(
				array(
					'engines' => $value,
				)
			);

			// Settings validation returns an entire settings array, but we only
			// want the engines because that is the setting we're updating
			$value = $value['engines'];
		}

		if ( 'false' === $value ) {
			$value = false;
		}

		if ( 'true' === $value ) {
			$value = true;
		}

		searchwp_set_setting( $setting, $value, $group );

		// After saving engines we need to trigger the index
		$alternate_indexer = SWP()->is_using_alternate_indexer();
		if ( 'engines' === $setting && empty( $alternate_indexer ) ) {
			SWP()->trigger_index();
		}

		wp_send_json_success();
	}

	/**
	 * Checks to see if the submitted string is JSON
	 *
	 * @since 2.9.0
	 */
	public function is_json( $string ) {
		if ( ! function_exists( 'json_last_error' ) ) {
			return null;
		}

		json_decode( $string );

		return ( json_last_error() == JSON_ERROR_NONE );
	}

	/**
	 * Upon arrival, some engine settings need to be revised
	 * // TODO: Vue should handle this...
	 *
	 * @since 2.9.0
	 */
	public function normalize_submitted_settings( $data ) {
		foreach ( $data as $engine => $engine_settings ) {
			foreach ( $engine_settings as $post_type => $post_type_settings ) {
				// The model uses 'comments' but the validation callback expects 'comment'
				if ( isset( $post_type_settings['weights'] ) ) {
					if ( isset( $post_type_settings['weights']['comments'] ) ) {
						$data[ $engine ][ $post_type ]['weights']['comment'] = $post_type_settings['weights']['comments'];
						unset( $data[ $engine ][ $post_type ]['weights']['comments'] );
					}
				}

				if ( isset( $post_type_settings['options'] ) ) {
					foreach ( $post_type_settings['options'] as $option => $value ) {
						// If any of these values are arrays, they're converted objects from Vue
						// that need to be converted to arrays of just the values from those objects
						if ( is_array( $value ) ) {
							$actual_values = array();

							foreach ( $value as $option_object ) {
								if ( ! isset( $option_object['value'] ) ) {
									continue;
								}
								$actual_values[] = $option_object['value'];
							}

							// Overwrite the array of objects with the array we want
							$data[ $engine ][ $post_type ]['options'][ $option ] = $actual_values;
						}

						// If a taxonomy rule was added with no terms, we can drop it
						if (
							empty( $value )
							&&
							(
								'exclude_' == substr( $option, 0, 8 )
								|| 'limit_to_' == substr( $option, 0, 9 )
							)
						) {
							unset( $data[ $engine ][ $post_type ]['options'][ $option ] );
						}
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Retrieve and return taxonomy terms encoded as JSON, formatted for select2
	 *
	 * @since 2.8
	 */
	public function get_tax_terms() {
		if ( empty( $_REQUEST['tax'] ) || ! taxonomy_exists( $_REQUEST['tax'] ) ) {
			wp_send_json_error();
		}

		$tax = sanitize_text_field( $_REQUEST['tax'] );

		// @since 2.9.0 This is a bit different
		if ( isset( $_REQUEST['_swpvtax_nonce'] ) && isset( $_REQUEST['post_type'] ) ) {
			$nonce_action = 'searchwp_ajax_tax_' . $tax . '_' . $_REQUEST['post_type'];
			check_ajax_referer( $nonce_action, '_swpvtax_nonce' );
		} else {
			check_ajax_referer( 'swp_tax_terms_' . $tax );
		}

		if ( empty( $_REQUEST['q'] ) ) {
			echo wp_json_encode( array() );
		}

		// search for terms
		$taxonomy_args = array(
			'hide_empty' => false,
			'name__like' => sanitize_text_field( $_REQUEST['q'] ),
			'fields'     => 'id=>name',
		);

		$terms = get_terms( $tax, $taxonomy_args );

		$response = array(
			'total_count'           => count( $terms ),
			'incomplete_results'    => false,
			'items'                 => array(),
		);

		foreach ( $terms as $term_id => $term ) {
			$response['items'][] = array(
				'id'    => $term_id,
				'text'  => $term,
			);
		}

		if ( isset( $_REQUEST['_swpvtax_nonce'] ) && isset( $_REQUEST['post_type'] ) ) {
			$return = array();
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term_id => $term ) {
					// This structure must match what Vue is using
					$return[] = array(
						'name' => absint( $term_id ),
						'value' => absint( $term_id ),
						'label' => $term,
					);
				}
			}
			wp_send_json_success( $return );
		} else {
			echo wp_json_encode( $response );

			die();
		}
	}

	/**
	 * Ensure that taxonomy options are normalized
	 *
	 * @since 2.9.0
	 */
	public function normalize_taxonomy_options( $data, $option_prefix = '_exclude' ) {
		foreach ( $data['engines'] as $engine_name => $engine_settings ) {
			foreach ( $engine_settings as $engine_post_type => $engine_post_type_settings ) {
				if ( empty( $data['objects'][ $engine_post_type ]['taxonomies'] ) ) {
					continue;
				}

				$taxonomies = $data['objects'][ $engine_post_type ]['taxonomies'];
				foreach ( $taxonomies as $taxonomy ) {
					if ( empty( $engine_post_type_settings['options'][ $option_prefix . $taxonomy['name'] ] ) ) {
						// We don't want any placeholders because the object property controls whether the exclusion is displayed in the UI
						continue;
					}

					$excluded = $engine_post_type_settings['options'][ $option_prefix . $taxonomy['name'] ];
					$taxonomy_args = array(
						'hide_empty' => false,
						'include'    => $excluded,
						'fields'     => 'id=>name',
					);

					$excluded_terms = get_terms( $taxonomy['name'], $taxonomy_args );

					if ( empty( $excluded_terms ) ) {
						// These terms no longer exist, so we don't want this placeholder
						unset( $data['engines'][ $engine_name ][ $engine_post_type ]['options'][ $option_prefix . $taxonomy['name'] ] );
						continue;
					}

					$normalized_excluded_terms = array();
					foreach ( $excluded_terms as $excluded_term_id => $excluded_term ) {
						// This structure must match what Vue is using
						$normalized_excluded_terms[] = array(
							'name' => $excluded_term_id,
							'value' => $excluded_term_id,
							'label' => $excluded_term,
						);
					}

					$data['engines'][ $engine_name ][ $engine_post_type ]['options'][ $option_prefix . $taxonomy['name'] ] = $normalized_excluded_terms;
				}
			}
		}

		return $data;
	}

	/**
	 * Retrieve and return unique meta_key values encoded as JSON, formatted for select2 autocomplete
	 *
	 * @since 2.8
	 */
	public function get_meta_keys() {

		global $wpdb;

		check_ajax_referer( 'swp_search_meta_keys' );

		if ( empty( $_REQUEST['q'] ) ) {
			echo wp_json_encode( array() );
		}

		// search for keys
		/** @noinspection SqlDialectInspection */
		$meta_keys = $wpdb->get_col( $wpdb->prepare( "
			SELECT meta_key
			FROM $wpdb->postmeta
			WHERE meta_key != %s
			AND meta_key != %s
			AND meta_key != %s
			AND meta_key != %s
			AND meta_key NOT LIKE %s
			AND meta_key LIKE %s
			GROUP BY meta_key
		",
			'_' . SEARCHWP_PREFIX . 'indexed',
			'_' . SEARCHWP_PREFIX . 'content',
			'_' . SEARCHWP_PREFIX . 'needs_remote',
			'_' . SEARCHWP_PREFIX . 'skip',
			'_oembed_%',
			'%' . $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) . '%'
		) );

		// allow devs to filter this list
		$meta_keys = array_unique( apply_filters( 'searchwp_custom_field_keys', $meta_keys ) );

		// sort the keys alphabetically
		if ( $meta_keys ) {
			natcasesort( $meta_keys );
		} else {
			$meta_keys = array();
		}

		$response = array(
			'total_count'        => count( $meta_keys ),
			'incomplete_results' => false,
			'items'              => array(),
		);

		foreach ( $meta_keys as $meta_key ) {
			$response['items'][] = array(
				'id'   => $meta_key,
				'text' => $meta_key,
			);
		}

		echo wp_json_encode( $response );

		die();
	}

	/**
	 * Generate an engine model for Vue to create supplemental engines
	 */
	public function generate_engine_model( $data ) {
		$model = array(
			'searchwp_engine_label' => __( 'Supplemental Engine', 'searchwp' ),
		);

		foreach ( $data['objects'] as $post_type => $post_type_attributes ) {
			$model[ $post_type ] = SWP()->get_default_config_for_post_type( $post_type );
		}

		return $model;
	}

	/**
	 * We need to ensure that all post types are accounted for
	 */
	public function normalize_post_types_to_objects( $data ) {
		if ( empty( $data['engines'] ) ) {
			return $data;
		}

		$normalized_mimes = array();
		if ( isset( $data['misc'] ) && isset( $data['misc']['mimes'] ) ) {
			// Vue expects objects for multiselect, so we're going to convert this array
			foreach ( $data['misc']['mimes'] as $mime_key => $mime_label ) {
				// This mimics the term objects used in Vue multiselects
				$normalized_mime = new stdClass();
				$normalized_mime->name = $mime_key;
				$normalized_mime->value = $mime_key;
				$normalized_mime->label = $mime_label;

				$normalized_mimes[] = $normalized_mime;
			}

			$data['misc']['mimes'] = $normalized_mimes;
		}

		foreach ( $data['engines'] as $engine_name => $engine_settings ) {
			foreach ( $engine_settings as $engine_post_type => $engine_post_type_settings ) {

				// If the post type no longer exists, remove it
				// Keep in mind that engine labels are stored here, which is not ideal...
				if ( 'searchwp_engine_label' !== $engine_post_type && ! post_type_exists( $engine_post_type ) ) {
					unset( $data['engines'][ $engine_name ][ $engine_post_type ] );
					continue;
				}
			}
		}

		if ( empty( $data['objects'] ) ) {
			return $data;
		}

		// Check to see if any post types were added since the last time this engine was saved
		foreach ( $data['objects'] as $active_post_type => $post_type_details ) {
			foreach ( $data['engines'] as $engine_name => $engine_settings ) {
				if ( ! array_key_exists( $active_post_type, $engine_settings ) ) {
					$data['engines'][ $engine_name ][ $active_post_type ] = SWP()->get_default_config_for_post_type( $active_post_type );
				}
			}
		}

		// Parent attribution may be returned as a string, we need a boolean
		foreach ( $data['engines'] as $engine_name => $engine_settings ) {
			foreach ( $engine_settings as $engine_post_type => $engine_post_type_settings ) {
				if ( isset( $engine_post_type_settings['options']['parent'] ) ) {
					$data['engines'][ $engine_name ][ $engine_post_type ]['options']['parent'] = ! empty( $engine_post_type_settings['options']['parent'] );
				}
			}
		}

		// Format various options where necessary
		foreach ( $data['engines'] as $engine_name => $engine_settings ) {
			foreach ( $engine_settings as $engine_post_type => $engine_post_type_settings ) {

				// The legacy UI used 'comment' for Comments instead of 'comments'
				// but now we're bound to the post_type_supports() flags so it needs to match
				if ( ! isset( $engine_post_type_settings['weights']['comments'] ) ) {
					if ( isset( $engine_post_type_settings['weights']['comment'] ) ) {
						$data['engines'][ $engine_name ][ $engine_post_type ]['weights']['comments'] = $engine_post_type_settings['weights']['comment'];
						unset( $data['engines'][ $engine_name ][ $engine_post_type ]['weights']['comment'] );
					} elseif ( post_type_supports( $engine_post_type, 'comments' ) ) {
						$data['engines'][ $engine_name ][ $engine_post_type ]['weights']['comments'] = 0;
					}
				}

				// Clean up empty arrays
				if ( isset( $engine_post_type_settings['weights']['cf'] ) && is_array( $engine_post_type_settings['weights']['cf'] ) && empty( $engine_post_type_settings['weights']['cf'] ) ) {
					unset( $data['engines'][ $engine_name ][ $engine_post_type ]['weights']['cf'] );
				}

				// Mimes are saved as a csv string
				if ( isset( $engine_post_type_settings['options']['mimes'] ) ) {
					$stored_mimes = (string) $engine_post_type_settings['options']['mimes'];

					// The engine validator saves empty mimes as an empty string, but we need an array
					$data['engines'][ $engine_name ][ $engine_post_type ]['options']['mimes'] = array();

					if ( ! empty( $stored_mimes ) || '0' === trim( $stored_mimes ) ) {
						$mimes = explode( ',', $stored_mimes );
						$mimes = array_map( 'absint', $mimes );

						// We need to populate the data with the objects as expected by Vue multiselect

						foreach ( $mimes as $mime_key ) {
							$data['engines'][ $engine_name ][ $engine_post_type ]['options']['mimes'][] = $normalized_mimes[ $mime_key ];
						}
					}
				}

				// Check for newly added taxonomies since last save
				$current_object_taxonomies = array();
				if ( isset( $data['objects'][ $engine_post_type ]['taxonomies'] ) ) {
					$current_object_taxonomies = wp_list_pluck( $data['objects'][ $engine_post_type ]['taxonomies'], 'name' );
				}

				if ( ! empty( $current_object_taxonomies ) ) {

					// Is this the first taxonomy ever?
					if ( ! isset( $data['engines'][ $engine_name ][ $engine_post_type ]['weights']['tax'] ) ) {
						$data['engines'][ $engine_name ][ $engine_post_type ]['weights']['tax'] = array();

						foreach ( $current_object_taxonomies as $current_object_taxonomy ) {
							$data['engines'][ $engine_name ][ $engine_post_type ]['weights']['tax'][ $current_object_taxonomy ] = 0;
						}
					}

					foreach ( $current_object_taxonomies as $current_object_tax ) {
						if (
							isset( $data['engines'][ $engine_name ][ $engine_post_type ]['weights']['tax'] )
							&& is_array( $data['engines'][ $engine_name ][ $engine_post_type ]['weights']['tax'] )
							&& ! array_key_exists( $current_object_tax,  $data['engines'][ $engine_name ][ $engine_post_type ]['weights']['tax'] )
						) {
							$data['engines'][ $engine_name ][ $engine_post_type ]['weights']['tax'][ $current_object_tax ] = 0;
						}
					}
				}

				if ( isset( $engine_post_type_settings['weights']['tax'] ) && is_array( $engine_post_type_settings['weights']['tax'] ) ) {
					// Also check for removed taxonomies
					$saved_taxonomies_from_engine_config = array_keys( $engine_post_type_settings['weights']['tax'] );

					if ( ! empty( $saved_taxonomies_from_engine_config ) ) {
						foreach ( $saved_taxonomies_from_engine_config as $saved_tax ) {
							if ( ! taxonomy_exists( $saved_tax ) ) {
								unset( $data['engines'][ $engine_name ][ $engine_post_type ]['weights']['tax'][ $saved_tax ] );
							}
						}
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Determine whether Basic Authentication will interfere with indexing
	 *
	 * @since 2.9.0
	 */
	public function is_basic_auth_blocking() {
		check_ajax_referer( 'searchwp_ajax_basic_auth' );

		$result = false;

		$basic_auth = searchwp_get_setting( 'basic_auth' );

		// determine if the environment has already been verified; don't want redundant HTTP requests on every page load
		if ( 'no' === $basic_auth ) {
			wp_send_json_success( $result );
		}

		// check to see if the credentials are already provided
		$http_basic_auth_creds = apply_filters( 'searchwp_basic_auth_creds', false );
		if (
			true === $basic_auth
			&& is_array( $http_basic_auth_creds )
			&& isset( $http_basic_auth_creds['username'] )
			&& isset( $http_basic_auth_creds['password'] )
		) {
			wp_send_json_success();
		}

		$searchwp = SWP();
		$response = $searchwp->get_indexer_communication_result();

		if (
			! is_wp_error( $response )
			&& isset( $response['response']['code'] )
			&& 401 === (int) $response['response']['code']
		) {
			searchwp_set_setting( 'basic_auth', true );
			$result = true;
		} else {
			// flag the environment as 'good'
			if ( ! is_wp_error( $response ) ) {
				searchwp_set_setting( 'basic_auth', 'no' );
			}
		}

		wp_send_json_success( $result );
	}
}
