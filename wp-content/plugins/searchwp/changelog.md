### 3.0.7
- **[Improvement]** Handling of highlighter logic
- **[Improvement]** Highlighter excerpt generation
- **[Improvement]** Debug environment checks/messaging
- **[Improvement]** Provide feedback when synonyms are influenced by other tokenizer rules
- **[Improvement]** Index statistics calculation
- **[Fix]** Issue with multiple-word source terms for synonyms not being processed correctly in all cases
- **[New]** New filter `searchwp_weight_mods_wrap_core_weights` to support additional weight customizations
- **[Update]** Translation source
- **[Update]** Updated updater

### 3.0.6
- **[New]** When enabling Admin search hijacking you must now choose an engine to use for Admin searches
- **[Change]** Regex pattern matches are processed by min word length and stopword removal rules
- **[New]** New filter `searchwp_apply_rules_to_whitelisted_terms` controls whether rules (min word length, stopword removal) applies to whitelisted terms
- **[Fix]** Issue with partial matching when multiple searches are run for a single request
- **[Fix]** Prevent parent attribution when searching in the Admin (would result in false negatives)
- **[Improvement]** Partial match processing
- **[Improvement]** Handling of delta updates to reduce resource usage
- **[Improvement]** System Information is now more comprehensive
- **[Update]** Translation source

### 3.0.5
- **[New]** Pasting of comma separated Stopwords will create individual Stopwords from the list
- **[Fix]** Conditional disabling of partial matches per engine by using provided filter
- **[Fix]** Prevent missing exact matches when finding partial matches
- **[Improvement]** Post types that are excluded from search during registration are now listed out
- **[Improvement]** When Metrics is installed the engine configuration Search Statistics link is correct
- **[Change]** Enabling partial matches no longer gives exact matches full priority, short circuiting on exact matches is now opt in via filter
- **[Change]** Third party dependencies have been reorganized to reduce file path which should help to avoid issues on certain Windows servers
- **[Update]** Translation source
- **[Update]** PHP version compatibility
- **[Update]** Dependency update which brings additional PHP compatibility
- **[Security]** TCPDF security update (which as evaluated could NOT have been exploited)

### 3.0.4
- **[Improvement]** Handling of multiple word highlighting
- **[Improvement]** Better restriction during indexing
- **[Fix]** Issue with Advanced Custom Fields repeater detection
- **[Fix]** Better handling of cached data
- **[Fix]** Prevent unwanted indexer activity when using `searchwp_indexed_post_types`
- **[Change]** Removal of ACF field references is now opt-in
- **[Change]** `searchwp_lenient_accents` now applies during searches as well
- **[New]** Filter `searchwp_lenient_accents_on_search` to allow refined control over leinient accent treatment

### 3.0.3
- **[Fix]** Fixes an issue with checking for unused meta keys when configuring search engines
- **[Fix]** Fixes a potential issue with `searchwp_short_circuit` being incorrectly overridden
- **[Fix]** Fixes an issue with synonyms not working as expected

### 3.0.1
- **[Fix]** Fixes an issue that may prevent Custom Fields from appearing in engine configuration

### 3.0
- **[New]** Advanced Settings screen rebuilt and optimized
- **[New]** `searchwp_legacy_advanced_settings` filter controls whether the legacy Advanced Settings screen is used
- **[New]** Integrated stopword management on the Advanced Settings screen
- **[New]** Default stopwords for the following locales: `CS`, `DA`, `DE`, `EN`, `ES`, `FI`, `GA`, `IT`, `NL`, `PL`, `PT`, `RO`, `RU`, `SV`, `TR`
- **[New]** Suggested stopwords based on existing site content
- **[New]** Integrated Term Synonyms and improved management UI (extension is now deprecated)
- **[New]** Integrated Term Highlight (extension is now deprecated)
- **[New]** Integrated LIKE Terms and Fuzzy Matches (extensions are now deprecated)
- **[New]** Adds setting to parse Shortcodes during indexing (e.g. UI for `searchwp_do_shortcodes`)
- **[New]** `SWP_Query` now has the following methods: `have_posts`, `rewind_posts`, `the_post`, `next_post` allowing for a more traditional Loop
- **[New]** Custom Fields dropdown now supports meta groups
- **[New]** Automatic UI for "repeatable" field groups in Advanced Custom Fields
- **[New]** Statistics screen rebuilt and optimized
- **[New]** Management of ignored searches is now built in to the Stats screen
- **[New]** Resetting of statistics is now built in to the Stats screen
- **[New]** `searchwp_statistics_popular_days_{$days}` filter allows overriding of popular search queries
- **[New]** `searchwp_legacy_stats` filter controls whether the legacy Advanced Settings screen is used
- **[New]** `searchwp_results_found_posts` filter allows modification of SearchWP's found posts
- **[New]** `searchwp_results_max_num_pages` filter allows modification of SearchWP's maximum number of pages
- **[New]** Support for programmatic license management. See `SearchWP_License` class
- **[New]** Adds (dismiss-able) notice during admin searches when admin searches are not hijacked by SearchWP
- **[New]** Adds support for WordPress' block editor during indexing (blocks will be parsed prior to indexing)
- **[Fix]** Adds support for results limiting when parent attribution is enabled
- **[Fix]** Better handling of emoji during indexing
- **[Fix]** Prevent pattern whitelist matches from being counted twice
- **[Fix]** Prevent data mutation when creating multiple supplemental engines at once
- **[Change]** Indexing emoji is now opt-in using the `searchwp_index_emoji` filter

### 2.9.17
- **[Improvement]** Better handling of post status and comment triggers of delta updates, reducing significant overhead in some cases

### 2.9.16
- **[Fix]** Fixes an issue that prevented proper respect of `searchwp_background_deltas`
- **[Fix]** Fixes an issue with debug log permissions in some cases
- **[Fix]** Fixes an issue where Custom Field keys were not accurately retrieved in older versions of WordPress
- **[Fix]** Fixes settings screen JavaScript error in IE11
- **[Fix]** Fixes an issue preventing the application of `searchwp_search_query_order`
- **[Improvement]** Notes the requirement that the index must be rebuilt after ticking checkbox to remove minimum character count
- **[New]** Adds `post_status` parameter to `SWP_Query`
- **[New]** Adds `order` parameter to `SWP_Query`
- **[New]** Adds limited `orderby` parameter to `SWP_Query`

### 2.9.15
- **[Fix]** Fixes an issue where in some cases delta update requests were not processed correctly
- **[Fix]** PHP Warning cleanup
- **[Improvement]** The debug log generation process has been improved and the debug log more streamlined/readable
- **[New]** New filter `searchwp_debug_detailed` to control whether detailed items are logged when debugging is enabled

### 2.9.14
- **[Fix]** Fixes false positive error message relating to HTTP Basic Authentication
- **[Fix]** Resolves an issue preventing translations from loading as expected
- **[Change]** Algorithm SQL has been updated to be more specific when considering Custom Fields and Taxonomies
- **[New]** New filter `searchwp_dashboard_widget_transient_ttl` that allows for customization of cache duration of Dashboard Widget data

### 2.9.13
- **[Fix]** Prevent redundant statistics logging on paginated results when using `SWP_Query`
- **[Fix]** Better handling of taxonomy terms with special characters
- **[Fix]** Fixes PHP Warning and PHP Notice in certain cases

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
- **[Fix]** Fixed an issue causing newly registered taxonomies to be unavailable in settings UI
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
