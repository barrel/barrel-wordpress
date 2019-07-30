<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SearchWP_Settings_Implementation_Advanced
 */
class SearchWP_Settings_Implementation_Advanced {

	/**
	 * SearchWP_Settings_Implementation_Advanced constructor.
	 */
	public function __construct() {
		$existing_settings = searchwp_get_option( 'advanced' );

		if ( ! empty( $existing_settings ) ) {
			$this->impose_settings( $existing_settings );
		}
	}

	/**
	 * Determines which Settings to automatically impose.
	 *
	 * @param array $existing_settings The settings to impose.
	 *
	 * @since 3.0
	 */
	public function impose_settings( $existing_settings ) {
		$available_toggles = searchwp_get_settings_names();

		foreach ( $existing_settings as $existing_setting => $value ) {
			if ( ! in_array( $existing_setting, $available_toggles, true ) ) {
				continue;
			}

			if ( empty( $value ) ) {
				continue;
			}

			$this->impose_setting( $existing_setting );
		}
	}

	/**
	 * Impose a specific setting when enabled.
	 *
	 * @param string $setting The setting to impose.
	 *
	 * @since 3.0
	 */
	public function impose_setting( $setting ) {
		switch ( $setting ) {
			case 'debugging':
				add_filter( 'searchwp_debug', '__return_true', 30 );
				break;

			case 'indexer_alternate':
				add_filter( 'searchwp_alternate_indexer', '__return_true', 30 );
				break;

			case 'parse_shortcodes':
				add_filter( 'searchwp_do_shortcode', '__return_true', 30 );
				break;

			case 'partial_matches':
				include_once( SWP()->dir . '/includes/class.partial-matches.php' );
				$partial_matches = new SearchWPPartialMatches();
				$partial_matches->init();
				break;

			case 'indexer_aggressiveness':
				add_filter( 'searchwp_index_chunk_size', array( $this, 'modify_searchwp_index_chunk_size' ), 30 );
				add_filter( 'searchwp_process_term_limit', array( $this, 'modify_searchwp_process_term_limit' ), 30 );
				break;

			case 'min_word_length':
				add_filter( 'searchwp_minimum_word_length', array( $this, 'modify_searchwp_minimum_word_length' ), 30 );
				break;

			case 'admin_search':
				add_filter( 'searchwp_in_admin', '__return_true', 30 );
				break;

			case 'highlight_terms':
				$highlighter = new SearchWPHighlighter();
				$highlighter->setup_auto_highlight();
				break;

			case 'exclusive_regex_matches':
				add_filter( 'searchwp_exclusive_regex_matches', '__return_true', 30 );
				break;

			case 'nuke_on_delete':
				add_filter( 'searchwp_nuke_on_delete', '__return_true', 30 );
				break;
		}
	}

	/**
	 * Callback to modify index chunk size.
	 *
	 * @since 3.0
	 *
	 * @return int The number of posts to index in each chunk (3).
	 */
	public function modify_searchwp_index_chunk_size() {
		return 3;
	}

	/**
	 * Callback to modify term limit.
	 *
	 * @since 3.0
	 *
	 * @return int The number of terms to process per chunk (250).
	 */
	public function modify_searchwp_process_term_limit() {
		return 250;
	}

	/**
	 * Callback to modify minimum word length.
	 *
	 * @since 3.0
	 *
	 * @return int The number of characters (1).
	 */
	public function modify_searchwp_minimum_word_length() {
		return 1;
	}

	/**
	 * Initializer; hook navigation tab (and corresponding view) and any custom functionality
	 *
	 * @since 3.0
	 */
	public function init() {
		// render the 'Advanced' tab on the settings screen
		add_action( 'searchwp_settings_nav_tab', array( $this, 'render_tab_advanced' ), 200 );

		// render the 'Advanced' view when the 'Advanced' tab is viewed
		add_action( 'searchwp_settings_view\advanced', array( $this, 'render_view_advanced' ) );
	}

	/**
	 * Render the tab if current user has appropriate capability
	 *
	 * @since 3.0
	 */
	public function render_tab_advanced() {
		if ( current_user_can( apply_filters( 'searchwp_settings_cap', 'manage_options' ) ) ) {
			searchwp_get_nav_tab( array(
				'tab'   => 'advanced',
				'label' => __( 'Advanced', 'searchwp' ),
			) );
		}
	}

	/**
	 * Render view callback
	 *
	 * @since 3.0
	 */
	public function render_view_advanced() { ?>
		<div class="searchwp-advanced-settings-wrapper swp-group">
			<div id="searchwp-settings-advanced"></div>
		</div>
		<?php
		$this->assets();
	}

	/**
	 * Retrieve and format the stored Advanced settings.
	 *
	 * @since 3.0
	 */
	public function get_settings() {
		$available_toggles = searchwp_get_settings_names();

		$existing_settings = searchwp_get_option( 'advanced' );

		if ( ! is_array( $existing_settings ) ) {
			$existing_settings = array();
		}

		foreach ( $available_toggles as $available_toggle ) {
			if ( ! array_key_exists( $available_toggle, $existing_settings ) ) {
				$existing_settings[ $available_toggle ] = false;
			}

			// Intentionally not using strict comparisons here.
			if ( 1 == $existing_settings[ $available_toggle ] || '1' == $existing_settings[ $available_toggle ] ) {
				$existing_settings[ $available_toggle ] = true;
			} else {
				$existing_settings[ $available_toggle ] = false;
			}
		}

		return $existing_settings;
	}

	/**
	 * Prepare the Vue application.
	 *
	 * @since 3.0
	 */
	public function assets() {
		$export_sources_json = SWP()->export_settings( null, false );

		$do_stopwords_suggestions = apply_filters( 'searchwp_suggested_stopwords', true );

		SWP()->ajax->enqueue_script(
			'settings-advanced',
			array(
				'nonces' => array(
					'reset_index',
					'update_stopwords',
					'update_synonyms',
					'wake_indexer',
					'reset_notices',
					'update_setting',
					'config_import',
					'index_dirty',
					'stopwords_suggestions',
				),
				'data'   => array(
					'engines_config'           => wp_json_encode( $export_sources_json ),
					'stopwords'                => SWP()->common,
					'stopwords_default'        => SWP()->stopwords->default,
					'stopwords_suggestions'    => array(), // This is lazy loaded.
					'do_stopwords_suggestions' => $do_stopwords_suggestions,
					'synonyms'                 => SWP()->synonyms->get(),
					'settings'                 => $this->get_settings(),
					'min_word_length'          => apply_filters( 'searchwp_minimum_word_length', 3 ),
				),
			)
		);
	}
}

$searchwp_advanced_settings = new SearchWP_Settings_Implementation_Advanced();
$searchwp_advanced_settings->init();
