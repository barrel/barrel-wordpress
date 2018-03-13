### 2.9.12
- **[Improvement]** Index better optimized when limiting to Media mime type
- **[Improvement]** AND logic is more restrictive when applicable
- **[Improvement]** Better handling of license key when provided via constant or filter
- **[Update]** Updates translation source
- **[Fix]** Fixes inaccurate indexer progress in some cases
- **[Fix]** Fixes handling of All Documents mime type Media limiter
- **[Fix]** Fixes PHP Warning

### 2.9.11
- **[Improvement]** Additional index optimization when delta updates are applied via new filter `searchwp_aggressive_delta_update`
- **[Improvement]** Debug output cleanup
- **[Fix]** Implements omitted filter argument

### 2.9.10
- **[Fix]** Resolves an issue where AND logic wasn't strict enough in some cases
- **[Fix]** Relocated `searchwp_indexer_pre` action trigger to restore expected behavior
- **[Improvement]** Additional refinements to delta update queue processing to prevent excessive server resource usage in some cases
- **[Improvement]** Adds edit icon to supplemental engine name to communicate it is editable
- **[Change]** License key is no longer displayed in license key field if populated via constant or hook
- **[New]** New filter `searchwp_engine_use_taxonomy_name` that controls displaying name or label of Taxonomies in enging settings
- **[New]** New filter `searchwp_and_fields_{$post_type}` allowing for AND field customization per post type

### 2.9.8
- **[Fix]** Fixes an issue where post type Limit rules were too aggressive in some cases
- **[Improvement]** Refined index delta update routine to reduce potentially unnecessary triggers

### 2.9.7
- **[Fix]** Resolves issue of inaccurate results count when parent attribution is in effect
- **[Fix]** Fixed PHP Warning introduced in 2.9.5
- **[Improvement]** Better processing of engine Rules

### 2.9.6.1
- **[Fix]** Fixed PHP Warning introduced in 2.9.5
- **[Fix]** Fixed link in admin notice

### 2.9.6
- **[Fix]** Fixed an issue causing newly regiestered taxonomies to be unavailable in settings UI
- **[Fix]** Messaging for index being out of date is now more accurate
- **[Fix]** Paged searches are no longer redundantly logged
- **[Improvement]** Improved default regex patterns by making them more strict
- **[Update]** Updated PDF parsing libraries

### 2.9.5
- **[Fix]** Fixed an issue where 'Any Custom Field' did not work as expected in some cases
- **[Fix]** Fixed an issue where taxonomies added since the last engine save may not be available
- **[Improvement]** Actual weight multiplier is displayed in tooltip

### 2.9.4
- **[Fix]** Fixed a CSS bug causing multiselect overlapping when configuring multiple post types
- **[Fix]** Fixed an issue preventing searches of hierarchical post types in the admin

### 2.9.3
- **[Fix]** Fixed a `searchwp_and_logic_only` regression introduced in 2.9
- **[Improvement]** Better handling of initial default engine model

### 2.9.2
- **[Fix]** Fixed an issue with some custom `ORDER BY` statements

### 2.9.1
- **[Fix]** Fixed a potential issue with `sql_mode=only_full_group_by` support (added in 2.9)
- **[Fix]** Avoid error when parsing PDFs without `mbstring`

### 2.9
- **[New]** Redesigned engine configuration interface!
- **[New]** Index is now further optimized as per engine settings
- **[New]** New filter `searchwp_weight_max` to customize a maximum weight
- **[New]** New filter `searchwp_legacy_settings_ui` to use legacy settings UI
- **[New]** New filter `searchwp_indexer_apply_engines_rules` to control whether engine rules are considered during indexing
- **[New]** New filter `searchwp_indexer_additional_meta_exclusions` to control whether default additional Custom Fields are excluded from indexing
- **[New]** New filter `searchwp_supports_label_{post_type}_{support}` to customize the label used for a post type native attribute
- **[Improvement]** Additional debug statements output when enabled
- **[Improvement]** Better formatting of HTML comment debug output
- **[Fix]** Less aggressive handling of `pre_get_posts` during searching
- **[Fix]** Fix an issue with `sql_mode=only_full_group_by` (default in MySQL 5.7)

### 2.8.17
- **[Update]** Updated updater
- **[Fix]** Fixed an issue with database table creation

### 2.8.16
- **[Fix]** Fixed an issue where exclusionary weight was not properly applied to all Custom Fields
- **[Fix]** Resolved premature AND logic aggressiveness in some cases
- **[Fix]** Revised database creation schema to better cooperate with more database environments
- **[Improvement]** Better assumed sorting of results
- **[New]** New filter `searchwp_stats_table_class` to control CSS class applied to stats output

### 2.8.15
- **[Improvement]** Better handling of regex pattern matches
- **[Fix]** Fixes an issue where Media-only default engines did not fully build index
- **[Update]** Updated updater

### 2.8.14
- **[Improvement]** Additional checks to prevent overrun with other plugins

### 2.8.13
- **[Fix]** Fix a regression introduced to `SWP_Query` in 2.8.11 that may have prevented pagination from working as expected

### 2.8.12
- **[Fix]** Additional main query checks to improve plugin compatibility

### 2.8.11
- **[Fix]** Fixed an issue with main query check that prevented search results from appearing in some cases
- **[Fix]** Fixed an issue where default includes/exclusions would be applied outside the main query
- **[Change]** Updated common words (stopwords)
- **[Change]** Switch from `page` parameter to `paged` so as to better match WP_Query

### 2.8.10
- **[Improvement]** Improved main query checks
- **[Improvement]** Improved invisible character tokenizing
- **[Fix]** Fixed an issue with AND logic limits given certain engine configurations
- **[Fix]** Fixed PHP Warnings
- **[Fix]** Fixed lack of output for two tooltips
- **[Fix]** Fixed an issue where usage of -1 weights to actively exclude matches overran enabled engine post type(s)

### 2.8.9
- **[Fix]** Fix PHP Fatal Error with `$wp_query`
- **[Fix]** Fix regression introduced in 2.8.8 that prevented admin searches from working properly in some cases

### 2.8.8
- **[New]** New filter `searchwp_pre_set_post` allowing for filtration of each post object prior to indexing
- **[Fix]** Better interoperation with Widgets
- **[Fix]** Prevent double search logs in certain cases
- **[Fix]** Properly cancel native search SQL when performing admin search (props Jim)
- **[Fix]** Repaired application of 'Remove all traces' feature on Advanced settings page
- **[Fix]** Fixed an issue where incorrect total results counts were logged
- **[Improvement]** PHP Warning cleanup
- **[Improvement]** Better accommodation for customization during import routines
- **[Improvement]** Better accommodation of engine configuration and exclusions when performing AND logic pass
- **[Change]** Update to `SWP_Query`: `post__in` and `post__not_in` parameters are now explicit (previously behaved like hooks)
- **[Update]** Updated updater

### 2.8.7
- **[Fix]** Fixed missing tooltip content
- **[Improvement]** Using `searchwp_admin_bar` now applies to search modification notices
- **[Improvement]** License key now included in System Information
- **[New]** Taxonomy term slugs are now indexed (use `searchwp_indexer_taxonomy_term_index_slug` to disable)
- **[New]** New filter `searchwp_indexer_taxonomy_term` allowing for filtration on taxonomy terms prior to indexing
- **[Update]** Updated updater

### 2.8.6
- **[Fix]** Fixed an issue with imposed engine config implementation for empty searches

### 2.8.5
- **[New]** Engine settings (e.g. exclusions/inclusions) are now imposed for empty searches
- **[New]** New filter `searchwp_disable_impose_engine_config` to disable imposed engine settings for empty searches
- **[Fix]** Fixed an issue that may have triggered unnecessary index update requests
- **[Improvement]** Style updates to better match WordPress' implementation of system font
- **[Improvement]** Better handling of indexer requests
- **[Improvement]** Better support when Admin/Dashboard searches are enabled
- **[Improvement]** Better utilization of existing extracted document content when triggering an index rebuild
- **[Improvement]** Better feedback when document parsing dependencies are not available
- **[Update]** Added more file type limiters to engine settings
- **[Update]** Updated translation sources
- **[Update]** Updated updater

### 2.8.4
- **[New]** New filter `searchwp_indexer_comment` to filter comment arguments during indexing
- **[New]** New filter `searchwp_indexer_pre_get_comments` to filter comment arguments during indexing
- **[New]** New filter `searchwp_indexer_comments_args` to filter comment arguments during indexing
- **[Fix]** Fixed an issue that prevented searching in the WordPress admin (when enabled)

### 2.8.3
- **[New]** New filter `searchwp_search_args` to filter search arguments at runtime
- **[Improvement]** Better handling of object caching
- **[Improvement]** Better messaging when rebuilding index
- **[Improvement]** Dequeue/deregister legacy versions of select2 that are imposed upon SearchWP's settings screen
 - **[Improvement]** Better handling of regex matches
- **[Fix]** Fixed an issue that sometimes prevented the indexer progress bar from displaying after rebuilding index
- **[Fix]** Fixed an issue that may have prevented manually edited document content from being fully re-indexed
- **[Fix]** Fixed PHP Warning during short circuit check
- **[Fix]** Disabling the minimum character count reduces length to 1 instead of 2

### 2.8.2
- **[Fix]** Fixed a mime type mismatch that prevented accurate Media limiting using All Documents file type
- **[Fix]** Admin Bar and Advanced tab indexer pausing now use the same setting
- **[Fix]** Fixed an issue when checking for `utf8mb4` support
- **[Improvement]** Improved regex pattern for hyphen-separated matches
- **[Improvement]** Improved aggressiveness of search algorithm where necessary to prevent unexpected filtration during searches
- **[Update]** Updated updater

### 2.8.1
- **[Fix]** Fixed an error with PHP's return context

### 2.8
- **[New]** Document parsing support added for Office documents (.docx, .xlsx, .pptx)
- **[New]** Document parsing support added for OpenOffice/LibreOffice documents (.odt, .ods, .odp)
- **[New]** Document parsing support added for Rich Text documents
- **[New]** Settings screen update to better accommodate common actions
- **[New]** Improve settings screen performance by requesting taxonomy terms via AJAX
- **[Fix]** When `searchwp_in_admin` is enabled searching in Grid view for Media now works as expected
- **[Improvement]** Better handling of large content (including parsed documents)
- **[Update]** Updated translation source
- **[Update]** Updated select2

### 2.7.2
- **[Improvement]** Better handling of native search query
- **[Fix]** Fixed a case where `searchwp_show_conflict_notices` was not respected
- **[Fix]** Fix PHP Warning (`Invalid argument supplied for foreach() in /wp-includes/query.php on line 4890`)

### 2.7.1
- **[Fix]** Cleaned up PHP Warning in `SWP_Query`
- **[Fix]** Fixed positioning of Extensions dropdown, other minor style updates
- **[Fix]** Fixed an issue with `SWP_Query` not resetting set hooks causing inaccurate results
- **[Fix]** Fixed an issue where Alternate Indexer may report posts left to index when no post types are enabled
- **[Fix]** Fixed an issue where `AND` logic pass was too restrictive in some circumstances
- **[Fix]** Fixed an issue when limiting Media results to Images only throwing a PHP Warning
- **[Fix]** Fixed an issue that caused some WP Admin notices to not display
- **[Improvement]** Refactored some stats logic into `SearchWP_Stats`
- **[Change]** Changed PHPCS ruleset which resulted in some additional hardening/formatting
- **[Update]** Updated translation source

### 2.7
- **[New]** New filter `searchwp_weight_mods` allowing for direct manipulation of computed weights within the search algorithm
- **[New]** New filter `searchwp_license_key` to programmatically define SearchWP license key
- **[New]** SearchWP license key can now be defined with `SEARCHWP_LICENSE_KEY` constant
- **[New]** New filter `searchwp_initial_engine_settings` to programmatically define default engine configurations on activation
- **[New]** New filter `searchwp_keyword_stem_locale` to enable keyword stemming in the current locale
- **[New]** Support for `'fields' => 'ids'` argument in `SWP_Query`
- **[New]** Support for `post_type` argument in `SWP_Query`
- **[New]** New filter `searchwp_purge_pdf_content` to remove parsed PDF content during an index purge
- **[New]** New filter `searchwp_skip_vendor_libs` to prevent loading of any vendor libraries
- **[New]** New filter `searchwp_indexer_taxonomy_terms` allowing for filtration of taxonomy terms pre-index
- **[Fix]** Fixed an issue where `searchwp_exclusive_regex_matches` could have been too greedy
- **[Fix]** Fixed an issue where a filtered post type could not be enabled in engine settings
- **[Fix]** Use multibyte string manipulation when possible
- **[Fix]** PHP Warning cleanup
- **[Fix]** Fixed an issue where the indexer progress bar may not display in WordPress 4.4+
- **[Improvement]** Better handling of matches when taking advantage of `searchwp_exclusive_regex_matches`
- **[Improvement]** Improved handling of deeply serialized meta data
- **[Improvement]** Reduced indexer query overhead
- **[Improvement]** Keyword stemming is only available if the current locale supports it (if you are using a custom stemmer you will need to update)
- **[Improvement]** Less aggressive checks against failed PDF parsing that generated false positive results
- **[Improvement]** Better handling of unicode whitespace during PDF parsing
- **[Change]** Debug log is now written to `searchwp-debug.txt` in the base uploads directory
- **[Change]** Parsed PDF content is not removed when the index is purged
- **[Update]** Updated PDF parsing library
- **[Update]** Updated updater

### 2.6.1
- **[Fix]** Fixed an issue where AND logic may not be enforced in some circumstances
- **[Fix]** PDF metadata is now properly delted when Nuke on Delete is enabled
- **[Fix]** Fixed an issue in the tokenizer that may have prevented tokens from being broken apart when HTML had no whitespace
- **[Fix]** Fixed a potential issue with PDF content indexing
- **[Fix]** Fixed an issue with the indexer not fully completing when only Media was enabled
- **[Fix]** PHP Warning cleanup when no results were found after tokenizing
- **[Improvement]** Improved performance when Media is given parent attribution
- **[Improvement]** SearchWP will now internally short circut when an empty search is performed for the main query
- **[Improvement]** Filtered post types are now given priority on the settings screen
- **[New]** Added Dutch translation

### 2.6
- **[New]** New class: `SWP_Query` which aims to mirror `WP_Query` in many ways, but with a SearchWP twist
- **[New]** Settings UI has been revamped
- **[New]** New filter: `searchwp_swp_query_args` to filter SWP_Query args at runtime
- **[New]** New action: `searchwp_settings_init` fires when the settings utility has been initialized
- **[New]** New action: `searchwp_load` fires when SearchWP has loaded
- **[New]** New action: `searchwp_settings_before_header` fires before the settings header is output
- **[New]** New action: `searchwp_settings_nav_tab` to implement settings tabs
- **[New]** New action: `searchwp_settings_after_header` fires after the settings header is output
- **[New]** New action: `searchwp_settings_before\my_view` where `my_view` is the name of the settings view for that tab
- **[New]** New action: `searchwp_settings_view\my_view` where `my_view` is the name of the settings view for that tab
- **[New]** New action: `searchwp_settings_after\my_view` where `my_view` is the name of the settings view for that tab
- **[New]** New action: `searchwp_settings_footer` fires after each settings view has been displayed
- **[New]** Results weights are included in HTML comment block when debugging is enabled
- **[New]** New filter: `searchwp_debug_append_weights_to_titles` whether weights should be included in HTML comment block debug information
- **[New]** New filter: `searchwp_show_filter_conflict_notices` whether filter conflicts should be shown when debugging is enabled (defaults to `false`)
- **[Improvement]** Better license activation UX
- **[Improvement]** Reduction of index overhead by way of pairing to engine settings
- **[Improvement]** Refined list of default common (stop) words
- **[Improvement]** Better handling of Greek keywords when using `searchwp_lenient_accents`
- **[New]** New filter: `searchwp_lenient_accents_conversions` to manipulate which accents are handled leniently when enabled
- **[New]** New filter: `searchwp_lenient_accent_result` allowing fine-grained control over lenient accents per term
- **[Fix]** Clear out delta ceiling update check when waking up the indexer
- **[Fix]** Fixed an issue that may have caused the indexer to loop when a post has no content to index after tokenizing/processing
- **[Fix]** Fixed an issue where the settings screen spinner would not display
- **[Fix]** Fixed an issue that prevented loading of some assets if the plugin directory was renamed
- **[Update]** Updated translation files

### 2.5.7
- **[Fix]** Fixed an issue with `utf8mb4` (e.g. emoji) support (NOTE: only *new* installations will support `utf8mb4`) — more information at [https://searchwp.com/releases/](https://searchwp.com/releases/)
- **[Change]** All camelCase function and method names have been deprecated (not removed (yet)) in favor of underscores

### 2.5.6
- **[Improved]** Better settings standardization

### 2.5.5
- **[Security Fix]** XSS prevention for authenticated users in the admin with add_query_arg
- **[Improved]** Security improvements: additional/redundant escaping/preparing/casting so as to harden the codebase and improve readability
- **[Improved]** More accurate Today stats
- **[Improved]** Better translation support
- **[Improved]** Better exception handling with PDF parsing
- **[Fix]** Better handling of misconfiguration when attributing an excluded post
- **[New]** Added French translation

### 2.5.4
- **[Fix]** Fixed an issue with regex whitelist term duplication in some cases
- **[Fix]** Fixed an issue when limiting AND fields to one field
- **[Fix]** Fixed inconsistent default parameter for `searchwp_indexed_post_types` filter
- **[Fix]** PHP Warning cleanup with filter conflict detection when debugging is enabled

### 2.5.3
- **[Fix]** Fixed an issue that may have prevented taxonomy searches from returning results
- **[Fix]** Fixed a regression introduced in 2.5 that aimed to optimize the main search query by omitting taxonomies with a weight of zero
- **[Fix]** Fixed an issue that may have prevented supplemental search results from appearing

### 2.5
- **[New]** Alternate, browser based indexer for cases where background indexing doesn't work
- **[New]** Front end Admin Bar entry that calls out any search modifications put in place (e.g. minimum word length, common words, maximum search length)
- **[New]** New filter: `searchwp_alternate_indexer` to trigger the new browser-based indexer
- **[New]** New filter: `searchwp_log_search` to allow prevention of search logging on a per-case basis
- **[New]** New filter: `searchwp_init` to allow for dynamic initialization of SearchWP
- **[New]** New filter: `searchwp_max_delta_attempts` to catch edge cases causing repeated delta updates
- **[New]** New filter: `searchwp_indexer_taxonomies` allows developers to customize which taxonomies are indexed
- **[New]** New filter: `searchwp_indexer_unindexed_args` to customize `WP_Query` arguments used to locate unindexed posts
- **[New]** New filter: `searchwp_indexer_unindexed_media_args` to customize `WP_Query` arguments used to locate unindexed Media
- **[New]** New filter: `searchwp_failed_index_notice` to customize who can see the notice about unindexed posts
- **[New]** New filter: `searchwp_remove_pre_get_posts` to define whether all hooks to `pre_get_posts` are removed during indexing (a very common conflict)
- **[New]** New filter: `searchwp_indexer_pre_process_content` allows developers to customize the content being indexed before it is processed and indexed
- **[New]** Set default array of excluded IDs to the main search query's `post__not_in`
- **[New]** Set default array of included IDs to the main search query's `post__in`
- **[New]** PDF metadata is now indexed
- **[Change]** Use `get_query_var()` instead of directly grabbing `$wp_query` attributes
- **[Change]** Add `index.php` to default loopback endpoint to avoid false positives with WAFs
- **[Change]** Use `debug.txt` instead of `debug.log`
- **[Change]** PDF text extraction abstracted to it's own method `SWP()->extract_pdf_text( $post_id )`
- **[Fix]** Fixed an issue that prevented limiting Media results for an engine to more than one MIME type group
- **[Fix]** Fixed an off-by-one issue that prevented single terms from being indexed when using the `searchwp_process_term_limit` filter
- **[Fix]** Fixed an issue that interfered with pagination in the WP admin when `searchwp_in_admin` was set to true
- **[Fix]** PHP Warning cleanup in Dashboard Widget
- **[Fix]** Only output Dashboard Widget assets on the Dashboard
- **[Fix]** Fixed an overflow issue in Dashboard Widget when collapsed on load
- **[Fix]** Fixed an issue where Dashboard Widget stats transients were not cleared when stats were reset
- **[Improvement]** Better minimum height for statistics graph
- **[Improvement]** Better width restriction on settings screen for post type tabs
- **[Improvement]** Better detection of database environment change that may interfere with indexing
- **[Improvement]** Weights of less than 0 are now called out to ensure intention of excluding results
- **[Improvement]** Better checks against `searchwp_indexed_post_types` when displaying settings screen
- **[Improvement]** Main search algorithm optimizations
- **[Improvement]** Indexer now checks for excluded posts before reindexing them after a purge
- **[Improvement]** Optimizations to search algorithm when weights of zero are used
- **[Improvement]** UI improvements on the settings screen
- **[Update]** Updated translation files
- **[Update]** Updated updater

### 2.4.11
- **[Fix]** Fixed an issue that prevented post attribution when using explicit IDs
- **[Fix]** Set proper flags when all search terms have been invalidated
- **[Fix]** PHP Warning cleanup with Dashboard Widget
- **[Fix]** Better support for plugin-enabled Media categories when setting excluded categories
- **[Improvement]** Further optimization of results pool reduction when exclusions are in place
- **[Update]** Updated translation source files
- **[Change]** `searchwp_term_in` filter now has a 3rd parameter with the original (e.g. unstemmed when stemming is enabled) term

### 2.4.10
- **[Fix]** PHP 5.2 compatibility with PDF parser fallback
- **[Fix]** Fixed an issue where some search strings were not properly ignored
- **[Fix]** Proper clearing of meta boxes on stats page at certain resolutions

### 2.4.9
- **[Fix]** Fixed an issue where limiting Media results to All Documents did not apply the expected limit
- **[Fix]** Fixed an issue where AND logic refinement may cause zero results to display depending on Title weight
- **[Update]** Updated PHP PDF parser (requires PHP 5.3+, will still fall back to less reliable legacy parser if PHP 5.2)
- **[Change]** Return WP_Error when an invalid search engine name is used


### 2.4.8
- **[Fix]** Fixed an issue where proper weights may not have been properly retained throughout the entire search algorithm, resulting in zero results
- **[Fix]** Default search results now take into account an offset (if one was set)
- **[New]** New filter `searchwp_query_offset` allowing for customization of the offset of the default search engine


### 2.4.7
- **[Fix]** PHP Warning cleanup: removed deprecated usage of `mysql_get_server_info()`


### 2.4.6
- **[Fix]** Fixed an issue that prevented parsed PDF content from being indexed on the first pass
- **[New]** Version numbers (SearchWP, WordPress, PHP, MySQL) are now passed along with support requests


### 2.4.5.1
- **[Fix]** PHP Warning cleanup for `in_array()` in `searchwp.php` on line 1588


### 2.4.5
- **[New]** Direct integration of support ticket creation within the WordPress admin
- **[New]** New filter `searchwp_lightweight_settings` allowing for a speedier but degraded settings screen loading (e.g. not loading taxonomy terms)
- **[New]** New filter `searchwp_dashboard_widget_cap` for more control over Dashboard Widget visibility (defaults to settings cap (which defaults to `manage_options`))
- **[New]** Settings export/import facilitated by copying and pasting JSON of engine configuration(s)
- **[Improvement]** Due to numerous issues with object caching, index validation hashes are stored as options instead of transients
- **[Improvement]** Updated licensing system framework
- **[Improvement]** Better handling of initial UI load on settings screen by obstructing UI until all bindings are properly in place
- **[Change]** Switched to [Chartist](http://gionkunz.github.io/chartist-js/) for statistics graphs
- **[Change]** Reinstated automatic theme conflict notices (filter conflicts continue to appear only when debugging is enabled)
- **[Fix]** Search statistics for Today are more accurate
- **[Fix]** Fixed an issue that prevented Shortcodes from being processed when using `searchwp_do_shortcode`
- **[Fix]** Fixed an issue triggered by a Custom Post Type named `label` preventing proper SearchWP settings storage
- **[Fix]** Fixed an issue that prevented Dashboard Widget transient storage if supplemental search names exceeded 17 characters


### 2.4.4
- **[Fix]** Fixed an issue where certain search terms were being double-stemmed


### 2.4.3
- **[Fix]** Fixed an issue where PDFs attributed to their post parent weren't showing up in search results


### 2.4.2
- **[Improvement]** Resolved query latency introduced in 2.4.1 concerning attribution


### 2.4.1
- **[Fix]** Fixed an issue that prevented parent attribution from working properly in certain cases


### 2.4
- **[New]** SearchWP will now detect whether you're using HTTP Basic Authentication
- **[New]** New Filter: `searchwp_basic_auth_creds` allowing developers to define HTTP Basic Authentication credentials
- **[New]** New Filter: `searchwp_query_select_inject` allowing developers the ability to inject their own statements into the main search query, allowing for extensive customization of results display beyond keyword weights
- **[New]** Search Statistics Dashboard Widget
- **[New]** New Filter: `searchwp_dashboard_widget` allowing developers to disable the Search Statistics Dashboard Widget
- **[New]** System Information panel (on the Advanced settings screen) to ease support
- **[Improvement]** Better handling of `searchwp_indexer_enabled` when used at the same time as `searchwp_indexer_paused`
- **[Improvement]** Better handling of accented characters
- **[Fix]** Force transient deletion
- **[Fix]** Fixed an issue where keyword stemming may have not been appropriately applied
- **[Change]** Conflict notices are now only displayed when debugging is enabled


### 2.3.3
- **[Improvement]** Admin Bar entry now elaborates on why a post is not indexed (e.g. draft status if not filtered)
- **[Improvement]** Admin Bar entry now more accurately calls out when a post is in the process of being indexed
- **[Fix]** Fixed an issue that may have prevented strings of only digits from being properly indexed


### 2.3.2
- **[Improvement]** Better capture of `SQL_BIG_SELECTS` errors by including a link to the fix in the Admin Bar
- **[Improvement]** Better handling of character encoding when parsing PDFs
- **[Fix]** Fixed an issue where posts excluded from the index were not properly listed on the exclusions page


### 2.3.1
- **[Fix]** Fixed a check for DOMDocument and cleaned up PHP warning about empty DOMDocument content
- **[Fix]** Fixed an issue where saved Custom Field keys were not retrieved properly on the settings screen
- **[Fix]** Fixed an issue where exclusion fields weren't treated with select2 when creating supplemental search engines
- **[Change]** Loading the settings screen UI partial via AJAX is now opt-in
 - **[New]** New Filter: `searchwp_lazy_settings` allowing developers to trigger a cachebusting version of the settings screen


### 2.3
- **[New]** Added UI feedback when the indexer has been automatically throttled due to excessive server load
- **[New]** Deprecated `$searchwp` global in favor of new function `SWP()`
- **[New]** You can now retrieve the specific weights that set the order for search results per-post and per-post-type-per-post (`SWP()->results_weights`)
- **[New]** New Filter: `searchwp_endpoint` allowing developers to customize the endpoint used by the indexer for each pass (uses only the slug, the `site_url()` is automatically prepended)
- **[New]** SearchWP will now index what it considers valuable HTML element attribute content (e.g. alt text)
- **[New]** New Filter: `searchwp_indexer_tag_attributes` allowing developers to customize which elements and attributes are considered valuable
- **[Improvement]** Significant performance increase of main search algorithm
- **[Improvement]** Better UTF-8 support when tokenizing
- **[Improvement]** Indexer now ensures it's not being throttled before jumpstarting
- **[Improvement]** Reset `AUTO_INCREMENT` when the index is purged
- **[Improvement]** Deprecated and removed explicit `indexed` meta flag in favor of utilizing the already present `last_index` meta flag
- **[Fix]** Removed redundant `error_log` usage with debugging enabled
- **[Fix]** Properly log searches called directly from search class
- **[Fix]** Empty searches are no longer logged when performed via the API
- **[Change]** Removed Remote Debugging as it no longer applies to support
- **[Change]** Settings screen is now loaded via AJAX so as to get around excessive page caching in the WP admin by certain hosts
- **[Change]** MyISAM is no longer explicitly used when creating custom database tables (uses your MySQL default instead)
- **[Change]** Refined default common words


### 2.2.3
- **[Improvement]** Another revision to the indexer stall check
- **[Improvement]** Updated translation source
- **[New]** Added an admin notice if your log file exceeds 2MB


### 2.2.2
- **[Improvement]** Better indexer stall check
- **[Improvement]** Better handling of search logs (moved from main class method to search class) to better accommodate 3rd party integrations


### 2.2.1
- **[Improvement]** Better handling of indexer stall check
- **[Improvement]** Switched Admin Bar entry from 'Currently Being Indexed' to 'In index queue' for accuracy's sake
- **[Improvement]** Better handling of delta updates prior to the initial index being built
- **[Improvement]** Better implementation of `searchwp_exclusive_regex_matches` usage, matches are now extracted earlier resulting in more concise results
- **[Fix]** Fixed an issue that prevented search queries from being logged when instantiating the search class directly
- **[Fix]** Fixed an issue where manually edited PDF content would be overwritten by a subsequent delta index update after saving
- **[Fix]** Fixed an issue that may have prevented the indexer from fully waking up when waking up the indexer
- **[Fix]** Fixed a false positive when checking for WPML Integration
- **[Fix]** Fixed an issue with Xpdf Integration not saving the extracted text


### 2.2
- **[New]** New class: `SearchWP_Stats` which will eventually house a number of utility methods for better statistics as development continues
- **[New]** SearchWP will now detect if you're running a plugin that has an integration Extension available and tell you about it
- **[New]** New Filter: `searchwp_omit_meta_key` allows developers to omit specific meta keys from being indexed during indexing
- **[Improvement]** Hardened the indexer communication process, reducing server resource consumption during indexing
- **[Improvement]** Better handling of regex whitelist matches that result with multi-word tokens (e.g. spaces within) NOTE: having multi-word matches is not recommended
- **[Improvement]** Added `$engine` parameter to `searchwp_query_orderby` filter
- **[Improvement]** Simplified the check for a stalled indexer
- **[Improvement]** Multi-term regex whitelist matches will no longer be tokenized but indexed as a whole for better phrase-matching
- **[Fix]** Fixed an issue where Heartbeat index time updates were not prefixed with "Last indexed"
- **[Fix]** Fixed an issue where the debugger would not properly instantiate thereby preventing additions to the log file
- **[Fix]** Fixed an issue where Heartbeat API-powered timestamp of last index was missing "Last Indexed" phrasing
- **[Fix]** Fixed an issue where in some circumstances content blocks parsed from PDFs would not be properly separated, resulted in the last word of one section being lumped together with the first word of the next section
- **[Fix]** Prevent over-preparation of terms when performing AND logic refinement
- **[Fix]** Check for indexer being disabled when issuing delta updates


### 2.1.3
- **[Improvement]** Better encoding and font support for PDF content extraction
- **[Improvement]** Reduced memory footprint when not indexing PDFs
- **[Fix]** Fixed an issue where `searchwp_settings_cap` was not properly applied
- **[Fix]** Reduced aggressiveness when tokenizing PDF content
- **[Fix]** Fixed thrown exception when parsing specific PDF encodings
- **[Fix]** Fixed a PHP 5.2 issue
- **[Fix]** Corrected an include path for ElementXRef.php


### 2.1
- **[Improvement]** Significant query performance improvement in AND logic pass
- **[Improvement]** Much improved PDF content extraction when using only PHP as opposed to Xpdf Integration (requires PHP5.3+ else SearchWP will fall back to previous method)
- **[New]** New Filter: `searchwp_settings_cap` allows you to customize the capability required to manage SearchWP's settings in the WordPress admin
- **[New]** You can now bulk-reintroduce posts that failed indexing


### 2.0.4
- **[New]** New regex whitelist pattern to support ampersand-joined terms (e.g. M&M)
- **[Fix]** Fixed an issue where toggling whether the indexer was enabled/disabled would sometimes conflict if not done on the SearchWP settings screen
- **[Improvement]** Fixed an issue where umlaut's were incorrectly removed from PDF content when extracted using internal (PHP-based) method
- **[Improvement]** Theme conflict detection now takes into account single line comments (does not cover all commented use cases)
- **[Improvement]** Improved term processing when using built in sanitization prior to searches


### 2.0.3
- **[New]** New Filter: `searchwp_get_custom_fields` allowing developers to pre-fetch (and set) post metadata just before indexing takes place (props Stefan Hans Schonert)
- **[New]** New Filter: `searchwp_term_in` allowing you to modify each term (per term) used in the main search algorithm
- **[Improvement]** Better handling of filtered terms, allowing extensions more control over the actual search query
- **[Improvement]** `searchwp_indexer_loopback_args` is now applied to every HTTP call SearchWP makes


### 2.0.2
- **[New]** New Filter: `searchwp_statistics_cap` allows you to filter which capability is required to view and interact with stats
- **[Improvement]** Ignored queries in search statistics are now stored per user, not globally


### 2.0.1
- **[Fix]** Fixed an issue introduced in 2.0 that prevented the uninstallation routine from properly executing when not using multisite
- **[Improvement]** Resolved an issue in certain hosting environments that may have prevented the indexer from running


### 2.0
- **[New]** Shortcode processing: SearchWP can now process your Shortcodes in a number of ways
- **[New]** New Filter: `searchwp_do_shortcode` allows you to conditionally tell SearchWP to process all Shortcodes
- **[New]** New Filter: `searchwp_set_post` allows you to modify each post object prior to indexing, allowing for conditional processing of Shortcodes and more
- **[New]** New Filter: `searchwp_nuke_on_delete` to trigger Nuke on Delete (*overrides setting!*)
- **[New]** New Filter: `searchwp_exclusive_regex_matches` to force the indexer to prevent indexing exploded regex matches
- **[New]** New Filter: `searchwp_omit_meta_key_{custom_field_key}` allowing for per-key conditional exclusion of post meta when indexing
- **[Improvement]** Refined regex whitelist pattern for matching hyphen-separated-strings
- **[Improvement]** The indexer no longer strips regex matches but instead retains them to better facilitate partial matches
- **[Improvement]** Better language for action/conflict notices
- **[Improvement]** You can now restore dismissed action/conflict notices in case you want to view them again
- **[Improvement]** Slight update to the settings UI (better active tab contrast, lessened border radii)
- **[Improvement]** Better exposure of Statistics feature
- **[Improvement]** Uninstallation routine now better respects multisite environment
- **[Improvement]** You can now exclude by terms that have not been used yet
- **[Improvement]** Better default exclusion of internal metadata when indexing
- **[Fix]** Fixed an issue where regex whitelist matches were not extracted from supplemental search queries during sanitization
- **[Fix]** Fixed an issue that might result in duplication of terms that are integers in the index
- **[Fix]** Fixed a potential issue (during updates) where Supplemental search engine labels would inadvertently have their first letter converted to an 'A'
- **[Fix]** Redundant preparation of search terms when checking for exclusions by weight of -1
- **[Fix]** PHP Warning cleanup


### 1.9.11
- **[Improvement]** Added a regex whitelist pattern for hyphen-separated strings often used for serial numbers
- **[Improvement]** Reduced the overhead of term extraction when processing the regex whitelist which might cause long posts with many regex matches to stall the indexer


### 1.9.10
- **[Fix]** Fixed a regression in version 1.9.8 that prevented the installation of new plugins from .org


### 1.9.9
- **[Fix]** Fixed an issue where extended term-chunking of long posts may not have completed properly


### 1.9.8
- **[Fix]** Fixed an issue where the changelog would not be visible when clicking 'view version details' links
- **[Change]** Automatic load monitoring is again enabled by default
- **[Improvement]** The notice that outputs an indication of posts that failed to index now respects purposefully excluded post IDs via `searchwp_prevent_indexing` filter


### 1.9.7
- **[New]** New Filter: `searchwp_index_comments` allows you to prevent comments from being indexed
- **[Improvement]** Prevented potential edge case where the indexer may stall after completing a delta update
- **[Improvement]** More aggressive implementation of term regex whitelist (matches are now indexed fully in tact and not broken apart)
- **[Fix]** Fixed an issue where problematic posts that failed to index were not properly called out in the WordPress admin
- **[Fix]** Fixed an issue where 'Any' Custom Field may not have applied correctly


### 1.9.5
- **[Fix]** Fixed an issue where `searchwp_in_admin` may not properly hijack search results in the WordPress admin as desired
- **[Improvement]** Only return results from the post type being viewed when `searchwp_in_admin` is enabled and performing a search
- **[Improvement]** Additional optimization and segmentation of settings where appropriate to prevent potential collision
- **[Improvement]** Use the Heartbeat API to dynamically update the Last Indexed time when on post edit screens


### 1.9.4
- **[Fix]** Fixed a CSS rendering issue in Firefox on the Search Stats page
- **[Improvement]** Hardened settings getting and setting mechanism
- **[New]** New Filter: `searchwp_show_conflict_notices` allows you to force-hide any conflict warnings generated by SearchWP


### 1.9.2
- **[New]** Added a number of actions to allow developers to react to various phases of indexing
- **[Fix]** Fixed an issue where setting -1 posts per page was incorrectly utilized when performing searches
- **[Fix]** Fixed an issue that prevented SearchWPSearch instantiation when AJAX calls were made
- **[Improvement]** Modified default term whitelist rules to be more targeted
- **[Improvement]** Reduced the number of notifications displayed upon activation
- **[Improvement]** Reduced the number of queries necessary to store/retrieve various settings
- **[Improvement]** Better data storage so as to work alongside various object caching plugins without stalling the indexer


### 1.9
- **[New]** You can now ignore queries from Search Stats (to help avoid spam getting in the way)
- **[New]** Term Whitelisting: you can now define regex patterns and in doing so better retain specially formatted terms (e.g. dates, phone numbers, function names) in the index that otherwise would have been stripped of punctuation
- **[New]** New Filter: `searchwp_query_orderby` allows open-ended customization of the `ORDER BY` clause of the main search query
- **[New]** New Filter: `searchwp_force_run` allows developers the ability to force SearchWP to run no matter what
- **[New]** New Filter: `searchwp_lenient_accents` allowing for 'lazy' quotes (e.g. searches without quotes will find terms with quotes)
- **[Improvement]** PHP Error cleanup
- **[Improvement]** Revisited index table indices, they're now better optimized which should result in noticeable performance improvements
- **[Improvement]** Load monitoring has been removed as it proved to be holding back the indexer resulting in delayed index times
- **[Improvement]** Attachment indexing has been disabled by default to save the (significant) overhead, but it will enable itself if any of your search engine settings incorporate Media
- **[Improvement]** Refined the number of posts indexed per indexer passed, as always this can be filtered
- **[Improvement]** Reduced the information overload present in the debug log, allowing for easier scanning for issues
- **[Improvement]** Offloaded AJAX handlers to minimize footprint and impact on the indexer
- **[Improvement]** Fixed overflow issues on the Search Stats page
- **[Improvement]** Informational notice linking directly to more information on Filters having everything to do with indexer configuration
- **[Improvement]** Better detection of parallel indexer processes running that could have resulted in duplicate indexing
- **[Improvement]** Indexer pause/unpause has been re-named enable/disable to reduce confusion
- **[Fix]** Fixed an issue where update notifications wouldn't show up in the Network Administration on Multisite


### 1.8.4
- **[Improvement]** Better handling of serialized objects which resulted in __PHP_Incomplete_Class errors
- **[Improvement]** Better enforcement of maximum term length when indexing as defined by the database schema
- **[Improvement]** Better handling of Deadlock cases when indexing
- **[Improvement]** Improved default common/stop words


### 1.8.3
- **[Fix]** Cleanup of PHP Warnings
- **[New]** New Filter: `searchwp_outside_main_query` allowing for specific overrides when performing native searches
- **[Improvement]** Updated translation files (accurate translations will be rewarded, please contact info@searchwp.com)


### 1.8.2
- **[Fix]** Fixed an issue where update notifications would not persist properly


### 1.8.1
- **[Fix]** Fixed an issue where, in certain cases, weight attribution (or a lack thereof) would cause searches to fail


### 1.8
- **[New]** You can now include a LIKE modifier (%) in Custom Field keys (essentially supporting ACF Repeater field (and similar plugins) data storage)
- **[New]** SearchWP will now attempt to detect potential conflicts and add notices in the WordPress admin when it finds potential problems
- **[Fix]** Fixed an issue where custom keyword stemmers would only be used during indexing, not searching
- **[Improvement]** The Custom Fields dropdown on the settings page is no longer limited to the 25 most-used meta keys
- **[Improvement]** The Custom Fields dropdown now uses select2 to make it easier to quickly select your desired meta key
- **[Improvement]** Various improvements to the main settings screen styles
- **[Improvement]** Fixed an issue where Custom Field meta keys would be over-sanitized when saved in the SearchWP options
- **[Improvement]** Update checks are performed less aggressively, reducing some cases of increased latency in the WordPress admin
- **[Improvement]** Better singleton instantiation, fixing an issue with localization and certain hook utilization
- **[Improvement]** Scheduled events are now properly removed upon plugin deactivation
- **[Improvement]** Reduction in query overhead when multiple Custom Field meta keys have the same weight
- **[Improvement]** Reduction in query overhead when multiple taxonomies have the same weight
- **[Improvement]** Refactored the main query algorithm so as to improve maintainability and stability over time
- **[Improvement]** Formatting improvements, code quality improvements
- **[Improvement]** Updated translation files (accurate translations will be rewarded, please contact info@searchwp.com)


### 1.7.2
- **[New]** New Extension: Term Highlight - highlight search terms when outputting results!
- **[New]** New Filter: `searchwp_found_post_objects` allowing for the customization of Post objects returned by searches
- **[Fix]** Fixed an issue where overriding SearchWP File Content would be overwritten when the post got reindexed
- **[Fix]** Hotfix for a potential update issue, if you do not see an update notification in your dashboard, please download from your Account


### 1.7
- **[New]** There is a new Advanced option called Nuke on Delete that adjusts the uninstallation routine to only remove data if you opt in
- **[Improvement]** AND logic queries now force their own index
- **[Improvement]** Removed unused constant
- **[Improvement]** Offloaded update checks to only occur in the admin and therefore reduce overhead on the front end
- **[Improvement]** Update checks are now performed once a day so as to reduce page load latency when working in the WordPress admin
- **[Improvement]** Changed the way license checks were performed to avoid potential unwanted caching issues
- **[Fix]** Fixed invalid textdomain usage for l18n


### 1.6.10
- **[Fix]** Fixed an issue where keyword weights between 0 and 1 were converted to integers
- **[New]** New Filter: `searchwp_post_statuses` allowing for the customization of which post statuses are considered when indexing and searching


### 1.6.9
- **[Fix]** Fixed an issue that may have generated a SQL error after late term sanitization when performing searches
- **[Fix]** Fixed an issue that caused taxonomies to be omitted in searches by default upon activation


### 1.6.8
- **[Fix]** Fixed a regression introduced in 1.6.7 that prevented the 'last indexed' statistic from being properly maintained
- **[New]** New Filter: `searchwp_extra_metadata` allowing you to force additional content into the index per post


### 1.6.7
- **[Change]** Background indexing process has been updated to better accommodate maintenance mode plugins
- **[Fix]** Fixed an issue that may have prevented result exclusion given weight of -1 in some cases
- **[Improvement]** Reduced the overhead of the logs table (note: search stats will be reset to accommodate this)
- **[Improvement]** Miscellaneous code reorganization and optimization


### 1.6.6
- **[New]** New Advanced option to reset Search Stats


### 1.6.5
- **[Improvement]** Better appropriate suppression of WP_Query filters in internal calls
- **[Improvement]** Admin Bar entry better labels whether the indexer is paused


### 1.6.4
- **[Fix]** Admin bar entries now only show up when browsing the WordPress admin and the current user can `update_options`
- **[Fix]** Fixed an issue where overwriting the stored PDF content may not have properly taken place
- **[Improvement]** Initial AND logic pass now assumes keyword stem to create a better initial results pool
- **[Improvement]** When debugging is enabled, an HTML comment block is output at the bottom of pages to indicate what took place during that pageload
- **[Improvement]** Asset URLs in the admin now better respect alternative placement (props Jason C.)


### 1.6.3
- **[Fix]** Fixed an issue where AND logic was wrongly applied to single term searches if you set `searchwp_and_logic_only` to be true
- **[Fix]** Fixed an issue where `searchwp_posts_per_page` was not properly applied to WordPress native searches
- **[New]** New Filter: `searchwp_big_selects` for cases where SearchWP breaches your MySQL-defined `max_join_size`


### 1.6.2
- **[Fix]** Fixed a PHP 5.2 compatibility error (T_PAAMAYIM_NEKUDOTAYIM)


### 1.6.1
- **[Fix]** Fixed an error on plugin deletion via WP admin
- **[Fix]** Fixed an issue where (if you opt to disable automatic delta updates) the queue could be overwritten in some cases
- **[Fix]** Fixed an issue where (in certain circumstances) searches for values only in Custom Field data may yield no results


### 1.6
- **[New]** Added indexer pause toggle to Admin Bar
- **[New]** New Filter: `searchwp_custom_fields` allowing you parse custom fields, telling SearchWP what content you want to be indexed
- **[New]** New Filter: `searchwp_custom_field_{$customFieldName}` performs the same filtration, but for a single Custom Field
- **[New]** New Filter: `searchwp_excluded_custom_fields` allowing you to customize which meta_keys are automatically excluded during indexing
- **[New]** New Filter: `searchwp_background_deltas` allowing you to disable automatic delta index updates (you would then need to set up your own via WP-Cron or otherwise, useful for high traffic sites)
- **[New]** New Filter: `searchwp_weight_threshold` allowing you to specify a minimum weight for search results to be considered (default is zero)
- **[New]** New Filter: `searchwp_indexed_post_types` allowing you to specify which post types are indexed (note: this controls only what post types are indexed, it has no effect on enabling/disabling post types on the SearchWP Settings screen)
- **[New]** New Filter: `searchwp_return_orderby_random` allowing search results to be returned at random
- **[Improvement]** Indexer optimizations in a number of places, index builds should be even faster (and more considerate of server resources)
- **[Improvement]** Auto-throttling now takes into account your max_execution_time so as to not exceed it
- **[Improvement]** Indexer now scales how many terms are processed per pass based on your memory_limit (can still be overridden)
- **[Improvement]** Better handling of potential table deadlock when indexing
- **[Improvement]** Overall reduction in memory usage when indexing
- **[Fix]** Fixed an off-by-one issue when filtering terms by minimum character length when parsing search terms
- **[Fix]** Fixed an issue where the progress meter would automatically dismiss itself after purging the index


### 1.5.5
- **[Improvement]** Better performance on a number of queries throughout
- **[Improvement]** SearchWP will now monitor load averages (on Linux machines) and auto-throttle when loads get too high
- **[Change]** The default indexer pass timeout (throttle) is now 1 second
- **[Fix]** Fixed an issue where Media may not be indexed on upload
- **[Fix]** Fixed an issue where terms in file names may be counted twice when indexing
- **[Improvement]** Many more logging messages included, logs now include internal process identification
- **[Fix]** Fixed an issue with non-blocking requests and their associated timeouts potentially stalling the indexing process


### 1.5
- **[New]** Admin Bar entry (currently displays the last time the current post was indexed)
- **[New]** New Filter: `searchwp_admin_bar` to allow you to disable the Admin Bar entry if you so choose
- **[New]** New Filter: `searchwp_indexer_throttle` allows you to tell the indexer to pause a number of seconds in between passes
- **[Fix]** PHP Warning cleanup
- **[Fix]** Fixed an issue where keyword stems were not fully utilized in AND logic passes
- **[Fix]** Fixed an issue where attachments may not be properly reindexed after an edit
- **[Fix]** Better index cleanup of deleted Media
- **[Improvement]** SearchWP's indexer will now automatically pause/unpause when running WordPress Importer


### 1.4.9
- **[Fix]** Fixed a regression that removed the `searchwp_and_logic_only` filter


### 1.4.8
- **[Fix]** Fixed an issue where the default comment weight was not properly set on install
- **[Improvement]** Better handling of additional reduction of AND pool results
- **[New]** New Filter: `searchwp_return_orderby_date` to allow developers to return results ordered by date instead of weight


### 1.4.7
- **[Fix]** Fixed an issue where the minimum word length was not taken into consideration when sanitizing terms


### 1.4.6
- **[Improvement]** More precise refactor of AND logic to prevent potential false positives
- **[New]** New Filter: `searchwp_and_fields` allows you to limit which field types apply to AND logic (i.e. limit to Title)


### 1.4.5
- **[Fix]** Fixed potential PHP Warnings
- **[New]** You can now use weights of -1 to forcefully *exclude* matches
- **[New]** By default SearchWP will now ignore WordPress core postmeta (e.g. `_edit_lock`)
- **[New]** New Filter: `searchwp_omit_wp_metadata` to include WordPress core postmeta in the index


### 1.4.4
- **[Fix]** Better coverage of deferred index delta updates


### 1.4.3
- **[Improvement]** Better handling of refinement of AND logic in more cases
- **[Improvement]** Better handling of forced AND logic when it returns zero results


### 1.4.2
- **[Fix]** Fixed a potential issue where the search algorithm refinement may be too aggressive
- **[New]** New Filter: `searchwp_custom_stemmer` to allow for custom (usually localized) stemming. *Requires a re-index if utilized*


### 1.4.1
- **[New]** New filter: `searchwp_and_logic_only` to allow you to explicity force SearchWP to use AND logic only
- **[New]** New filter: `searchwp_refine_and_results` tells SearchWP to further restrict AND results to titles
- **[New]** New filter: `searchwp_max_and_results` to allow you to tell SearchWP when you want it to refine AND results


### 1.4
- **[New]** Added a new Advanced setting to allow you to pause the indexer without deactivating SearchWP
- **[New]** New filter: `searchwp_include_comment_author` allows you to enable indexing of comment author
- **[New]** New filter: `searchwp_include_comment_email` allows you to enable indexing of comment author email
- **[New]** New filter: `searchwp_auto_reindex` to allow you to disable automatic reindexing of edited posts
- **[New]** New filter: `searchwp_indexer_paused` to allow you to override the Advanced setting programmatically
- **[Fix]** Fixed an issue where comments were not accurately indexed
- **[Improvement]** Improved stability of results when multiple posts have the same final weight by supplementally sorting by post_date DESC


### 1.3.6
- **[New]** New filter: `searchwp_short_circuit` to allow you to have SearchWP *not* run at runtime. Useful when implementing other plugins that utilize search.


### 1.3.5
- **[Improvement]** Implemented workaround for issues experienced with Post Types Order that prevented search results from appearing


### 1.3.4.1
- **[Fix]** Fixed a bug with weight attribution that resulted from the update in 1.3.4


### 1.3.4
- **[Fix]** Fixed an issue where taxonomy/custom field weight may not have been appropriate attributed when applicable
- **[Fix]** Fixed an issue where 'Any' custom field weight may not have been appropriately applied
- **[Improvement]** Remote debugging info now updates more consistently
- **[Improvement]** Additional method for remote debugging


### 1.3.3
- **[New]** Initial implementation of Remote Debugging (found in Advanced settings)
- **[New]** Extension: Xpdf Integration
- **[New]** New filter: `searchwp_external_pdf_processing` to allow you to use your own PDF processing mechanism
- **[Improvement]** Less strict AND logic used in the main query
- **[Improvement]** Better database environment check (needed as a result of [MySQL bug 41156](http://bugs.mysql.com/bug.php?id=41156))
- **[Improvement]** Additional cleanup of SearchWP options during uninstallation
- **[Improvement]** Force license deactivation during uninstallation


### 1.3.2
- **[New]** SearchWP now defaults to AND logic with search terms, huge performance boost as a result (note: if no posts are found via AND, OR will be used)
- **[New]** New filter: `searchwp_and_logic` to revert back to OR logic
- **[Fix]** Fixed an issue with the new deferred index updates


### 1.3.1
- **[New]** Added ability to 'wake up' the indexer if you feel it has stalled
- **[Fix]** Fixed an issue where Custom Field weights would not be properly retrieved if there were capital letters in the key
- **[Fix]** Fixed an issue with cron scheduling and management
- **[Improvement]** Delta index updates are now performed in the background
- **[Improvement]** Reduced latency between indexer passes
- **[Improvement]** Better tracking of repeated passes on lengthy index entries
- **[Improvement]** Better accommodation of potential indexer pass overlapping and in doing so reduce the liklihood of database table deadlocking
- **[Improvement]** Better notifications regarding license activation
- **[Improvement]** More useful debugger logging
- **[Improvement]** Better index progress display after purging the index


### 1.3
- **[New]** New filter: `searchwp_search_query_order` to allow changing the search query order results at runtime
- **[New]** New filter: `searchwp_max_index_attempts` to allow control over how many times the indexer should try to index a post
- **[New]** New filter: `searchwp_prevent_indexing` to allow exclusion of post IDs from the index process entirely
- **[Improvement]** Better low-level interception of WordPress query process so as to accommodate other plugin workflows
- **[Improvement]** Better realtime monitoring of indexer progress
- **[Improvement]** Better detection and handling of troublesome posts when indexing
- **[Improvement]** Better handling of 'initial index complete' notification after purging and reindexing
- **[Improvement]** Cleaned up purging and uninstallation routines


### 1.2.5
- **[Improvement]** Search statistics are no longer reset along with purging the index
- **[Improvement]** Better options cleanup both during index purges and uninstallations
- **[Improvement]** Improvement in overall indexer performance, not by a large margin, but some


### 1.2.4
- **[Fix]** Fixed an issue where the database environment check was too aggressive and prevented activation before the environment was set up


### 1.2.3
- **[Fix]** Fixed an issue where numeric Custom Field data was not indexed accurately
- **[Improvement]** Better detection for custom database table creation


### 1.2.2
- **[Improvement]** Better accommodation for regression in 1.2.0 that prevented proper taxonomy exclusion


### 1.2.1
- **[Fix]** Fixed an issue where index progress indicator could exceed 100% after disabling attachment indexing
- **[Fix]** Fixed an issue where category exclusion would not always apply to search engine settings


### 1.2.0
- **[Improvement]** Overall reduction in query time when performing searches (sometimes down to 50%!)
- **[Improvement]** Indexing process now handles huge posts in a more efficient way, avoiding PHP timeouts
- **[Improvement]** Better handling of term indexing resulting in a more accurate index
- **[Fix]** Fixed an issue where term IDs were not pulled properly during indexing
- **[Change]** Changed the default weight for Titles so as to better meet user expectations


### 1.1.2
- **[Fix]** Fixed an issue where the WordPress database prefix was hardcoded in certain situations
- **[Improvement]** Removed redundant SQL call resulting in faster search queries


### 1.1.1
- **[Improvement]** More parameters passed to just about every SearchWP hook, please view the documentation for details


### 1.1
- **[New]** A more formal integration of Extensions such that settings screens can be added
- **[New]** You can now limit Media search results by their type (e.g. search only Documents)
- **[New]** Extension: Term Synonyms - manually define term synonyms
- **[New]** Extension: WPML Integration
- **[New]** Extension: Polylang Integration
- **[New]** Extension: bbPress Integration
- **[New]** New filter `searchwp_include` that accepts an array of limiting post IDs during searches
- **[New]** New filter `searchwp_query_main_join` to allow custom joining during the main search query
- **[New]** New filter `searchwp_query_join` to allow custom joining in per-post-type subqueries when searching
- **[New]** New filter `searchwp_query_conditions` to allow custom conditions in per-post-type subqueries when searching
- **[New]** New filter `searchwp_index_attachments` to allow you to disable indexing of Attachments entirely to save index time
- **[Improvement]** Major reduction in query time if you choose to NOT include Media in search results (or limit Media to Documents)
- **[Improvement]** Better edge case coverage to the indexing process; it's now less likely to stall arbitrarily
- **[Improvement]** Better delta index updates by skipping autosaves and revisions more aggressively
- **[Improvement]** Fixed a UI issue where the CPT column on the settings screen may expand beyond the right hand column
- **[Improvement]** Better default weights


### 1.0.10
- **[New]** Added filter `searchwp_common_words`
- **[New]** Added filter `searchwp_enable_parent_attribution_{posttype}`
- **[New]** Added filter `searchwp_enable_attribution_{posttype}`


### 1.0.9
- **[Improvement]** Better cleaning and processing of taxonomy terms
- **[Improvement]** Additional parameter when invoking SearchWPSearch for 3rd party integrations (props Matt Gibbs)
- **[Fix]** Fixed an issue with Firefox not liking SVG files


### 1.0.8
- **[Fix]** Fixed an issue where duplicate terms could get returned when sanitizing
- **[New]** Extension: Fuzzy Searching
- **[New]** Extension: Term Archive Priority
- **[New]** Added filter `searchwp_results` to faciliate filtration of results before they're returned
- **[New]** Added filter `searchwp_query_limit_start` to allow offsetting the main query results
- **[New]** Added filter `searchwp_query_limit_total` to allow offsetting the main query results
- **[New]** Added filter `searchwp_pre_search_terms` to allow filtering search terms before searches run
- **[New]** Added filter `searchwp_load_posts` so as to prevent weighty loading of all post data when all you want is IDs (props Matt Gibbs)
- **[Improvement]** More arguments passed to searchwp_before_query_index and searchwp_after_query_index actions


### 1.0.7
- **[NOTICE]** Due to an indexer update, it is recommended that you purge your index after updating
- **[Improvement]** Better, more performant indexer behavior during updates
- **[Improvement]** Added logging for supplemental searches
- **[Improvement]** Better punctuation handling during indexing and searching
- **[Improvement]** Better cleanup of stored options when applicable
- **[Fix]** Better logging of original search queries compared to what actually gets sent through the algorithm
- **[Fix]** Fixed potential PHP warning


### 1.0.6
- **[Improvement]** Better handling of source code-related indexing and searching
- **[New]** Added filter `searchwp_engine_settings_{$engine}` to allow adjustment of weights at runtime
- **[New]** Added filter `searchwp_max_search_terms` to cap the number of search terms that can be searched for (default 6)
- **[New]** Added filter `searchwp_max_search_terms_supplemental` to cap the number of terms for supplemental searches
- **[New]** Added filter `searchwp_max_search_terms_supplemental_{$engine}` to cap the number of terms for supplemental searches by engine
- **[Fix]** Fixed an issue with empty search queries showing up in search stats
- **[Fix]** Fixed an issue with CSS alignment of search stats
- **[Fix]** Fixed an issue where the indexer would index and then re-index posts when not needed
- **[Fix]** Fixed a MySQL error when logging indexer actions


### 1.0.5
- **[Change]** Updated user-agent of indexer background process for easier debugging
- **[New]** Added initial support for common debugging assistance via searchwp_log action
- **[Improvement]** Better support for WordPress installations in subdirectories
- **[Improvement]** If the initial index is already built by the time you go from activation to settings screen, a notice is displayed
- **[Improvement]** Better support for generating your own pagination with supplemental searches http://d.pr/MXgp
- **[Fix]** Stopped 'empty' search queries from being logged
- **[New]** Added filter `searchwp_index_chunk_size` to adjust how many posts are indexed at a clip


### 1.0.4
- **[Fix]** Much better handling of all UTF-8 characters both when indexing and when searching


### 1.0.3
- **[Fix]** Fixed an issue with the auto-update script not resolving properly
- **[Improvement]** Better handling of special characters both when indexing and querying


### 1.0.2
- **[Fix]** Fixed an issue where Custom Field weights weren't saving properly on the Settings screen


### 1.0.1
- **[Fix]** Fixed an issue that would cause searches to fail if an enabled custom post type had a hyphen in it's name
- **[Fix]** Fixed an off-by-one issue in generating statistical figures


### 1.0.0
- Initial release
