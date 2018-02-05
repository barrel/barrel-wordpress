<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_i18n is responsible for all things internationalization
 */
class SearchWP_i18n {

	public $strings;

	function __construct() {
		$this->strings = array(
			'a_little' => __( 'A little', 'searchwp' ),
			'a_lot' => __( 'A lot', 'searchwp' ),
			'add_attribute' => __( 'Add Attribute', 'searchwp' ),
			'add_content_type' => __( 'Add Content Type', 'searchwp' ),
			'add_engine' => __( 'Add Engine', 'searchwp' ),
			'add_exclusion' => __( 'Add Exclusion', 'searchwp' ),
			'add_limit_exclude_rule' => __( 'Add Limit or Exclude Rule', 'searchwp' ),
			'add_limiter' => __( 'Add Limiter', 'searchwp' ),
			'add_post_type' => __( 'Add Post Type', 'searchwp' ),
			'admin_search_enabled' => __( 'All post types will be indexed to facilitate searching in Admin/Dashboard', 'searchwp' ),
			'any_custom_field' => __( 'Any Custom Field', 'searchwp' ),
			'assign_weight_to' => __( 'Assign weight to:', 'searchwp' ),
			'attribute' => __( 'Attribute', 'searchwp' ),
			'attribution' => __( 'Attribution', 'searchwp' ),
			'attribute_results_to' => __( 'Attribute search results to:', 'searchwp' ),
			'auto_scale' => __( 'Currently scaled back to reduce server load. This is monitored automatically.', 'searchwp' ),
			'average' => __( 'Average', 'searchwp' ),
			'basic_auth_heading' => __( 'HTTP Basic Authentication', 'searchwp' ),
			'basic_auth_note' => __( 'SearchWP has detected HTTP Basic Authentication, in order for the indexer to operate as expected you must provide working credentials or disable HTTP Basic Authentication.', 'searchwp' ),
			'choose' => __( 'Choose', 'searchwp' ),
			'choose_custom_field' => __( 'Choose custom field', 'searchwp' ),
			'choose_native_attribute' => __( 'Choose native attribute', 'searchwp' ),
			'choose_taxonomy' => __( 'Choose taxonomy', 'searchwp' ),
			'choose_terms' => __( 'Choose terms', 'searchwp' ),
			'comma_separated_ids' => __( 'Comma separated IDs', 'searchwp' ),
			'content_type' => __( 'Content Type', 'searchwp' ),
			'custom_field' => __( 'Custom Field', 'searchwp' ),
			'custom_fields' => __( 'Custom Fields', 'searchwp' ),
			'default' => __( 'Default', 'searchwp' ),
			'default_engine_note' => __( 'Native WordPress searches will return these post types', 'searchwp' ),
			'delete_engine' => __( 'Delete Engine', 'searchwp' ),
			'dismiss' => __( 'Dismiss', 'searchwp' ),
			'document_content' => __( 'Document Content', 'searchwp' ),
			'document_properties' => __( 'Document Properties', 'searchwp' ),
			'done' => __( 'Done', 'searchwp' ),
			'engine_note' => __( 'These post types will be included in your search results, all other post types will be excluded', 'searchwp' ),
			'engine_note_none' => __( 'You must add at least one post type in order for this engine to return results', 'searchwp' ),
			'entries' => __( 'entries', 'searchwp' ),
			'exclude' => __( 'Exclude', 'searchwp' ),
			'excluded' => __( 'Excluded', 'searchwp' ),
			'exclude_by_taxonomy' => __( 'Exclude by taxonomy', 'searchwp' ),
			'excluded_ids' => __( 'Excluded IDs', 'searchwp' ),
			'find_terms' => __( 'Find terms', 'searchwp' ),
			'index_progress' => __( 'Index Progress', 'searchwp' ),
			'index_note' => __( 'Note: the index is automatically maintained to be kept as small as possible', 'searchwp' ),
			'index_dirty' => __( 'Index out of date', 'searchwp' ),
			'index_dirty_from_engines_save' => __( 'Engines saved but the index is now out of date, it should be rebuilt', 'searchwp' ),
			'indexed' => __( 'Indexed', 'searchwp' ),
			'initial_settings_notice' => __( 'To enable SearchWP, please review and save your initial settings', 'searchwp' ),
			'last_activity' => __( 'Last Activity', 'searchwp' ),
			'legacy_settings_notice' => __( 'To take advantage of recent updates please review and save your settings', 'searchwp' ),
			'limit_by_taxonomy' => __( 'Limit by taxonomy', 'searchwp' ),
			'limit_to' => __( 'Limit to', 'searchwp' ),
			'limited_to' => __( 'Limited to', 'searchwp' ),
			'main_row_count' => __( 'Main row count', 'searchwp' ),
			'maximum' => __( 'Maximum', 'searchwp' ),
			'more_info' => __( 'More info', 'searchwp' ),
			'minimum' => __( 'Minimum', 'searchwp' ),
			'native_attribute' => __( 'Native attribute', 'searchwp' ),
			'no_terms_found' => __( 'No terms found', 'searchwp' ),
			'note' => __( 'Note:', 'searchwp' ),
			'not_available_no_index' => __( 'is not available to PHP. As a result, Office document content will not be indexed.', 'searchwp' ),
			'more_info' => __( 'More Info', 'searchwp' ),
			'options' => __( 'Options', 'searchwp' ),
			'pdf_metadata' => __( 'PDF Metadata', 'searchwp' ),
			'problem_saving_engine_settings' => __( 'There was a problem saving the engine settings', 'searchwp' ),
			'remove' => __( 'Remove', 'searchwp' ),
			'rebuild_index' => __( 'Rebuild Index', 'searchwp' ),
			'right_now' => __( 'Right now', 'searchwp' ),
			'rows' => __( 'rows', 'searchwp' ),
			'rules' => __( 'Rules', 'searchwp' ),
			'save_engine' => __( 'Save Engine', 'searchwp' ),
			'save_engines' => __( 'Save Engines', 'searchwp' ),
			'saved' => __( 'Saved!', 'searchwp' ),
			'single_post_id' => __( 'Single post ID', 'searchwp' ),
			'search' => __( 'Search', 'searchwp' ),
			'statistics' => __( 'Search Statistics', 'searchwp' ),
			'taxonomy' => __( 'Taxonomy', 'searchwp' ),
			'taxonomies' => __( 'Taxonomies', 'searchwp' ),
			'transfer_weight_to' => __( 'Transfer weight to:', 'searchwp' ),
			'transfer_weight_to_parent' => __( 'Transfer weight to parent', 'searchwp' ),
			'unindexed' => __( 'Unindexed', 'searchwp' ),
			'use_defaults' => __( 'Use Defaults', 'searchwp' ),
			'use_keyword_stem' => __( 'Use Keyword Stem', 'searchwp' ),
			'weight' => __( 'Weight', 'searchwp' ),
			'weight_multiplier' => __( 'Weight Multiplier', 'searchwp' ),
			'weight_assignment' => __( 'Weight Assignment', 'searchwp' ),
			'weight_transfer' => __( 'Weight Transfer', 'searchwp' ),
			'without_attributes_no_results' => __( 'Without attributes, this post type will not show up in results', 'searchwp' ),
		);

		$this->strings['errors'] = array(
			'generic' => __( 'There was an error, please open a support ticket.', 'searchwp' ),
			'missing_nonce' => __( 'Missing nonce.', 'searchwp' ),
		);
	}

	function init() {
		add_action( 'init', array( $this, 'textdomain' ) );
	}

	/**
	 * Enable SearchWP textdomain
	 *
	 * @since 1.0
	 */
	function textdomain() {
		$locale = apply_filters( 'searchwp', get_locale(), 'searchwp' );
		$mofile = WP_LANG_DIR . '/searchwp/searchwp-' . $locale . '.mo';

		if ( file_exists( $mofile ) ) {
			load_textdomain( 'searchwp', $mofile );
		} else {
			load_plugin_textdomain( 'searchwp', false, SWP()->dir . '/languages/' );
		}
	}
}
