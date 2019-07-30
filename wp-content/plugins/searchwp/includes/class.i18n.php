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
			'actions_settings' => __( 'Actions & Settings', 'searchwp' ),
			'add_attribute' => __( 'Add Attribute', 'searchwp' ),
			'add_content_type' => __( 'Add Content Type', 'searchwp' ),
			'add_engine' => __( 'Add Engine', 'searchwp' ),
			'add_exclusion' => __( 'Add Exclusion', 'searchwp' ),
			'add_limit_exclude_rule' => __( 'Add Limit or Exclude Rule', 'searchwp' ),
			'add_limiter' => __( 'Add Limiter', 'searchwp' ),
			'add_new' => __( 'Add New', 'searchwp' ),
			'add_post_type' => __( 'Add Post Type', 'searchwp' ),
			'add_stopword' => __( 'Add Stopword', 'searchwp' ),
			'add_to_stopwords' => __( 'Add to Stopwords', 'searchwp' ),
			'admin_engine' => __( 'Admin engine', 'searchwp' ),
			'admin_engine_note' => __( 'This engine is used for Admin/Dashboard searches. It cannot be renamed or removed.', 'searchwp' ),
			'admin_search_enabled' => __( 'All post types will be indexed to facilitate searching in Admin/Dashboard', 'searchwp' ),
			'any_custom_field' => __( 'Any Custom Field', 'searchwp' ),
			'are_you_sure' => __( 'Are you sure?', 'searchwp' ),
			'assign_weight_to' => __( 'Assign weight to:', 'searchwp' ),
			'attribute' => __( 'Attribute', 'searchwp' ),
			'attribution' => __( 'Attribution', 'searchwp' ),
			'attribute_results_to' => __( 'Attribute search results to:', 'searchwp' ),
			'auto_scale' => __( 'Currently scaled back to reduce server load. This is monitored automatically.', 'searchwp' ),
			'average' => __( 'Average', 'searchwp' ),
			'basic_auth_heading' => __( 'HTTP Basic Authentication', 'searchwp' ),
			'basic_auth_note' => __( 'SearchWP has detected HTTP Basic Authentication, in order for the indexer to operate as expected you must provide working credentials or disable HTTP Basic Authentication.', 'searchwp' ),
			'can_not_be_undone' => __( 'This can not be undone!', 'searchwp' ),
			'choose' => __( 'Choose', 'searchwp' ),
			'choose_custom_field' => __( 'Choose custom field', 'searchwp' ),
			'choose_native_attribute' => __( 'Choose native attribute', 'searchwp' ),
			'choose_taxonomy' => __( 'Choose taxonomy', 'searchwp' ),
			'choose_terms' => __( 'Choose terms', 'searchwp' ),
			'comma_separated_ids' => __( 'Comma separated IDs', 'searchwp' ),
			'content_type' => __( 'Content Type', 'searchwp' ),
			'count' => __( 'Count', 'searchwp' ),
			'custom_field' => __( 'Custom Field', 'searchwp' ),
			'custom_fields' => __( 'Custom Fields', 'searchwp' ),
			'database_tables_missing' => __( "SearchWP's database tables are missing!", 'searchwp' ),
			'debugging_enabled' => __( 'Debugging enabled', 'searchwp' ),
			'default' => __( 'Default', 'searchwp' ),
			'default_engine_note' => __( 'Native WordPress searches will return these post types', 'searchwp' ),
			'delete_engine' => __( 'Delete Engine', 'searchwp' ),
			'dismiss' => __( 'Dismiss', 'searchwp' ),
			'document_content' => __( 'Document Content', 'searchwp' ),
			'document_properties' => __( 'Document Properties', 'searchwp' ),
			'done' => __( 'Done', 'searchwp' ),
			'engine_config_imported' => __( 'Engine configuration imported', 'searchwp' ),
			'engine_configuration_transfer' => __( 'Engine Configuration Transfer', 'searchwp' ),
			'engine_note' => __( 'These post types will be included in your search results, all other post types will be excluded', 'searchwp' ),
			'engine_note_none' => __( 'You must add at least one post type in order for this engine to return results', 'searchwp' ),
			'engines' => __( 'Engines', 'searchwp' ),
			'entries' => __( 'entries', 'searchwp' ),
			'exclude' => __( 'Exclude', 'searchwp' ),
			'excluded' => __( 'Excluded', 'searchwp' ),
			'exclude_by_taxonomy' => __( 'Exclude by taxonomy', 'searchwp' ),
			'excluded_from_search' => __( 'Excluded from search', 'searchwp' ),
			'excluded_ids' => __( 'Excluded IDs', 'searchwp' ),
			'exclusive_regex_matches' => __( 'Exclusive regex matches', 'searchwp' ),
			'export' => __( 'Export', 'searchwp' ),
			'export_data' => __( 'Export Data', 'searchwp' ),
			'find_terms' => __( 'Find terms', 'searchwp' ),
			'highlight_results' => __( 'Highlight terms in results', 'searchwp' ),
			'ignored' => __( 'Ignored', 'searchwp' ),
			'ignored_message' => __( 'These are your ignored queries', 'searchwp' ),
			'import' => __( 'Import', 'searchwp' ),
			'import_config' => __( 'Import Config', 'searchwp' ),
			'import_data' => __( 'Import Data', 'searchwp' ),
			'import_how_to' => __( 'To import: paste engine config export and click Import', 'searchwp' ),
			'importing_engine_config' => __( 'Importing engine configuration...', 'searchwp' ),
			'index_being_reset' => __( 'The index is being reset...', 'searchwp' ),
			'index_prevalence' => __( 'Index Prevalence', 'searchwp' ),
			'index_progress' => __( 'Index Progress', 'searchwp' ),
			'index_note' => __( 'Note: the index is automatically maintained to be kept as small as possible', 'searchwp' ),
			'index_dirty' => __( 'Index out of date', 'searchwp' ),
			'index_dirty_from_engines_save' => __( 'Engines saved but the index is now out of date, it should be rebuilt', 'searchwp' ),
			'index_needs_reset' => __( 'The index is out of date and needs to be Reset', 'searchwp' ),
			'index_reset_alternate' => __( 'The index has been reset, please rebuild the index on the main settings screen', 'searchwp' ),
			'index_reset_rebuilding' => __( 'The index has been reset and is now rebuilding', 'searchwp' ),
			'indexed' => __( 'Indexed', 'searchwp' ),
			'indexer_waking' => __( 'Waking up the indexer...', 'searchwp' ),
			'indexer_woken' => __( 'The indexer has been woken up', 'searchwp' ),
			'indexer_woken_alternate' => __( 'The indexer has been woken up, please proceed on the main settings screen', 'searchwp' ),
			'initial_settings_notice' => __( 'To enable SearchWP, please review and save your initial settings', 'searchwp' ),
			'intercept_admin_searches' => __( 'Intercept Admin/Dashboard searches', 'searchwp' ),
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
			'no_ignored' => __( 'You have not ignored any searches', 'searchwp' ),
			'no_results_searches' => __( 'No Results Searches', 'searchwp' ),
			'no_searches' => __( 'No searches to display', 'searchwp' ),
			'no_terms_found' => __( 'No terms found', 'searchwp' ),
			'note' => __( 'Note:', 'searchwp' ),
			'notices_reset' => __( 'Notices have been reset', 'searchwp' ),
			'no_engines_in_export' => __( 'No engines have been included in the export', 'searchwp' ),
			'not_available_no_index' => __( 'is not available to PHP. As a result, Office document content will not be indexed.', 'searchwp' ),
			'min_word_length_note' => wp_kses(
				sprintf(
					// Translators: palceholder is the number of characters.
					__( 'In order to utilize Search Terms that are fewer than %s characters, you must remove the minimum character length.', 'searchwp' ),
					'{{ minWordLength }}'
				),
				array(
					'a'  => array(
						'href'   => array(),
						'target' => array(),
					),
				)
			),
			'month' => __( 'Month', 'searchwp' ),
			'more_info' => __( 'More Info', 'searchwp' ),
			'options' => __( 'Options', 'searchwp' ),
			'orignal_search_term' => __( 'Original search term', 'searchwp' ),
			'parse_shortcodes' => __( 'Parse Shortcodes when indexing', 'searchwp' ),
			'partial_matches' => __( 'Partial matches (fuzzy when necessary)', 'searchwp' ),
			'partial_matches_note' => wp_kses(
				sprintf(
					// Translators: first placeholder opens the link to the partial matches docs, the second placeholder closes it
					__( 'Enabling partial matches can adversely affect performance and relevancy %1$sRead more &raquo;%2$s', 'searchwp' ),
					'<a href="https://searchwp.com/?p=163990" target="_blank">',
					'</a>'
				),
				array(
					'a'  => array(
						'href'   => array(),
						'target' => array(),
					),
				)
			),
			'pdf_metadata' => __( 'PDF Metadata', 'searchwp' ),
			'problem_saving_engine_settings' => __( 'There was a problem saving the engine settings', 'searchwp' ),
			'rebuild_index' => __( 'Rebuild Index', 'searchwp' ),
			'recreate_tables' => __( 'Recreate Tables', 'searchwp' ),
			'reduced_indexer_aggressiveness' => __( 'Reduced indexer aggressiveness', 'searchwp' ),
			'remove' => __( 'Remove', 'searchwp' ),
			'remove_all' => __( 'Remove All', 'searchwp' ),
			'remove_all_data' => __( 'Remove all data on uninstall', 'searchwp' ),
			'remove_min_word_length' => __( 'Remove minimum word length', 'searchwp' ),
			'replace' => __( 'Replace', 'searchwp' ),
			'replace_note' => __( 'When enabled, original Search Term will be removed', 'searchwp' ),
			'reset_index' => __( 'Reset Index', 'searchwp' ),
			'reset_stats' => __( 'Reset Stats', 'searchwp' ),
			'restore_defaults' => __( 'Restore Defaults', 'searchwp' ),
			'restore_notices' => __( 'Restore Notices', 'searchwp' ),
			'right_now' => __( 'Right now', 'searchwp' ),
			'rows' => __( 'rows', 'searchwp' ),
			'rules' => __( 'Rules', 'searchwp' ),
			'save' => __( 'Save', 'searchwp' ),
			'save_engine' => __( 'Save Engine', 'searchwp' ),
			'save_engines' => __( 'Save Engines', 'searchwp' ),
			'save_stopwords' => __( 'Save Stopwords', 'searchwp' ),
			'save_synonyms' => __( 'Save Synonyms', 'searchwp' ),
			'saved' => __( 'Saved!', 'searchwp' ),
			'saving' => __( 'Saving...', 'searchwp' ),
			'single_post_id' => __( 'Single post ID', 'searchwp' ),
			'search' => __( 'Search', 'searchwp' ),
			'search_term' => __( 'Search Term', 'searchwp' ),
			'search_term_handling' => __( 'Search Term Handling', 'searchwp' ),
			'search_term_note' => __( 'The original search term', 'searchwp' ),
			'searches' => __( 'Searches', 'searchwp' ),
			'sort_alphabetically' => __( 'Sort Alphabetically', 'searchwp' ),
			'statistics' => __( 'Search Statistics', 'searchwp' ),
			'stopwords' => __( 'Stopwords', 'searchwp' ),
			'stopwords_note' => wp_kses(
				sprintf(
					// Translators: first placeholder opens the link to the stopwords docs, the second placeholder closes it
					__( 'Stopwords are <em>ignored</em> by SearchWP to improve performance. %1$sMore info &raquo;%2$s', 'searchwp' ),
					'<a href="https://searchwp.com/?p=163991" target="_blank">',
					'</a>'
				),
				array(
					'em' => array(),
					'a'  => array(
						'href'   => array(),
						'target' => array(),
					),
				)
			),
			'stopwords_saved' => __( 'Stopwords saved! You should reset the index to apply the changes.', 'searchwp' ),
			'suggested_stopwords' => __( 'Suggested Stopwords', 'searchwp' ),
			'suggested_stopwords_note' => __( 'The following terms are quite common to your site and may be cluttering your index and reducing relevency of results:', 'searchwp' ),
			'suggestions' => __( 'Suggestions', 'searchwp' ),
			'synonyms' => __( 'Synonyms', 'searchwp' ),
			'synonyms_maybe_plural' => __( 'Synonym(s)', 'searchwp' ),
			'synonyms_none' => __( 'There are currently no synonyms', 'searchwp' ),
			'synonyms_note_tooltip' => __( 'Term(s) that are synonymous with the Search Term', 'searchwp' ),
			'synonyms_note' => wp_kses(
				sprintf(
					// Translators: first placeholder opens the link to the synonyms docs, the second placeholder closes it
					__( 'Synonyms allow <em>replacement</em> of search terms. %1$sMore info &raquo;%2$s', 'searchwp' ),
					'<a href="https://searchwp.com/?p=163992" target="_blank">',
					'</a>'
				),
				array(
					'em' => array(),
					'a'  => array(
						'href'   => array(),
						'target' => array(),
					),
				)
			),
			'synonyms_saved' => __( 'Synonyms Saved!', 'searchwp' ),
			'taxonomy' => __( 'Taxonomy', 'searchwp' ),
			'taxonomies' => __( 'Taxonomies', 'searchwp' ),
			'term' => __( 'Term', 'searchwp' ),
			'today' => __( 'Today', 'searchwp' ),
			'transfer_weight_to' => __( 'Transfer weight to:', 'searchwp' ),
			'transfer_weight_to_parent' => __( 'Transfer weight to parent', 'searchwp' ),
			'unindexed' => __( 'Unindexed', 'searchwp' ),
			'use_alternate_indexer' => __( 'Use alternate indexer', 'searchwp' ),
			'use_defaults' => __( 'Use Defaults', 'searchwp' ),
			'use_keyword_stem' => __( 'Use Keyword Stem', 'searchwp' ),
			'wake_up_indexer' => __( 'Wake Up Indexer', 'searchwp' ),
			'week' => __( 'Week', 'searchwp' ),
			'weight' => __( 'Weight', 'searchwp' ),
			'weight_multiplier' => __( 'Weight Multiplier', 'searchwp' ),
			'weight_assignment' => __( 'Weight Assignment', 'searchwp' ),
			'weight_transfer' => __( 'Weight Transfer', 'searchwp' ),
			'without_attributes_no_results' => __( 'Without attributes, this post type will not show up in results', 'searchwp' ),
			'year' => __( 'Year', 'searchwp' ),
			'yes_reset_index' => __( 'Yes, reset index', 'searchwp' ),
			'yes_reset_stats' => __( 'Yes, reset stats', 'searchwp' ),
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
		$mofile = WP_LANG_DIR . '/plugins/searchwp-' . $locale . '.mo';

		if ( file_exists( $mofile ) ) {
			load_textdomain( 'searchwp', $mofile );
		} else {
			load_plugin_textdomain( 'searchwp', false, basename( SWP()->dir ) . '/languages/' );
		}
	}
}
