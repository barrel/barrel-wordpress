<?php
/*
Plugin Name: SearchWP
Plugin URI: https://searchwp.com/
Description: The best WordPress search you can find
Version: 2.9.12
Author: SearchWP, LLC
Author URI: https://searchwp.com/
Text Domain: searchwp

Copyright 2013-2018 SearchWP, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SEARCHWP_VERSION', '2.9.12' );
define( 'SEARCHWP_PREFIX', 'searchwp_' );
define( 'SEARCHWP_DBPREFIX', 'swp_' );
define( 'SEARCHWP_EDD_STORE_URL', 'https://searchwp.com' );
define( 'SEARCHWP_EDD_ITEM_NAME', 'SearchWP' );

// minimum WordPress version requirement
$wp_version = get_bloginfo( 'version' );
if ( version_compare( $wp_version, '3.5', '<' ) ) {
	/** @noinspection PhpIncludeInspection */
	require_once ABSPATH . '/wp-admin/includes/plugin.php';
	deactivate_plugins( __FILE__ );
	wp_die( esc_html( __( 'SearchWP requires WordPress 3.5 or higher. Please upgrade before activating this plugin.' ) ) );
}

// includes
include_once( dirname( __FILE__ ) . '/includes/functions.php' );
include_once( dirname( __FILE__ ) . '/includes/class.stats.php' );

if ( ! class_exists( 'SWP_EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/vendor/SWP_EDD_SL_Plugin_Updater.php' );
}

/**
 * Set up the updater
 *
 * @return SWP_EDD_SL_Plugin_Updater
 */
function searchwp_update_check(){

	// retrieve stored license key
	$license_key = searchwp_get_license_key();

	// instantiate the updater to prep the environment
	$searchwp_edd_updater = new SWP_EDD_SL_Plugin_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'version'   => SEARCHWP_VERSION,        // current version number
			'license'   => $license_key,            // license key (used get_option above to retrieve from DB)
			'item_name' => SEARCHWP_EDD_ITEM_NAME,  // name of this plugin
			'author'    => 'SearchWP, LLC',         // author of this plugin
		)
	);

	return $searchwp_edd_updater;
}
add_action( 'admin_init', 'searchwp_update_check' );

global $searchwp;

/**
 * Class SearchWP
 * @since 1.0
 */
class SearchWP {
	/**
	 * @var string process identifier
	 * @since 1.5.5
	 */
	private $pid;

	/**
	 * @var SearchWP The SearchWP singleton
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * @var string License key
	 */
	public $license;

	/**
	 * @var string License status
	 */
	public $status;

	/**
	 * @var string The plugin directory
	 * @since 1.0
	 */
	public $dir;

	/**
	 * @var string The plugin URL
	 * @since 1.0
	 */
	public $url;

	/**
	 * @var string The plugin version
	 * @since 1.0
	 */
	public $version;

	/**
	 * @var bool Whether a search is taking place right now
	 * @since 1.0
	 */
	public $active = false;

	/**
	 * @var bool Whether SearchWP performed a search on this pageload
	 * @since 1.6.4
	 */
	public $ran = false;


	/**
	 * @var array Stores diagnostic information for debugging
	 * @since 1.6.4
	 */
	public $diagnostics = array();

	/**
	 * @var bool Whether indexing is taking place right now
	 * @since 1.0.6
	 */
	public $indexing = false;

	/**
	 * @var bool Whether we're in WordPress' main query
	 * @since 1.0
	 */
	public $isMainQuery = false;

	/**
	 * @var string Plugin name
	 * @since 1.0
	 */
	public $pluginName = 'SearchWP';

	/**
	 * @var string Plugin textdomain, used in localization
	 * @since 1.0
	 */
	public $textDomain = 'searchwp';

	/**
	 * @var array Stores custom field keys
	 * @since 1.0
	 */
	public $keys;

	/**
	 * @var array Stores all SearchWP settings
	 * @since 1.0
	 */
	public $settings;

	/**
	 * @var array Stores registered post types
	 */
	public $postTypes = array();

	/**
	 * @var string Stores the original (searched for) query
	 */
	public $original_query;

	/**
	 * @var array Common words as specified by Ando Saabas in Sphider http://www.sphider.eu/
	 * @since 1.0
	 */
	public $common = array(
		'a', 'able', 'above', 'across', 'after', 'afterwards', 'again', 'against', 'ago', 'all', 'almost', 'alone',
		'along', 'already', 'also', 'although', 'always', 'am', 'among', 'amongst', 'amoungst', 'amount', 'an', 'and',
		'another', 'any', 'anyhow', 'anyone', 'anything', 'anyway', 'anywhere', 'are', "aren't", 'around', 'as', 'at',
		'back', 'be', 'became', 'because', 'become', 'becomes', 'becoming', 'been', 'before', 'beforehand', 'behind',
		'being', 'below', 'beside', 'besides', 'between', 'beyond', 'both', 'bottom', 'but', 'by', 'call', 'can', "can't",
		'cannot', 'cant', 'co', 'con', 'could', "couldn't", 'couldnt', 'de', 'did', 'do', 'does', "don't", 'done', 'dont',
		'down', 'due', 'during', 'each', 'eg', 'eight', 'either', 'eleven', 'else', 'elsewhere', 'empty', 'enough', 'etc',
		'etc.', 'even', 'ever', 'every', 'everyone', 'everything', 'everywhere', 'except', 'few', 'fifteen', 'fify',
		'fill', 'find', 'first', 'five', 'for', 'former', 'formerly', 'forty', 'found', 'four', 'from', 'front',
		'full', 'further', 'get', 'give', 'go', 'got', 'had', 'has', "hasn't", 'hasnt', 'have', 'he', 'hence', 'her',
		'here', 'hereafter', 'hereby', 'herein', 'hereupon', 'hers', 'herself', 'him', 'himself', 'his', 'how', 'however',
		'hundred', 'i', 'i.e.', 'ie', 'if', 'in', 'inc', 'inc.', 'indeed', 'into', 'is', "isn't", 'it', "it's", 'its',
		'itself', 'just', 'keep', 'last', 'latter', 'latterly', 'least', 'less', 'let', 'like', 'likely', 'ltd', 'ltd.',
		'made', 'many', 'may', 'maybe', 'me', 'meanwhile', 'might', 'mill', 'mine', 'more', 'moreover', 'most', 'mostly',
		'move', 'much', 'must', 'my', 'myself', 'name', 'namely', 'neither', 'never', 'nevertheless', 'next', 'nine',
		'no', 'no-one', 'nobody', 'none', 'noone', 'nor', 'not', 'now', 'of', 'off', 'often', 'old', 'on', 'once', 'one',
		'only', 'onto', 'or', 'other', 'others', 'otherwise', 'our', 'ours', 'ourselves', 'out', 'over', 'own', 'per',
		'perhaps', 'please', 'put', 'rather', 're', 'said', 'same', 'say', 'says', 'see', 'seem', 'seemed', 'seeming',
		'seems', 'serious', 'several', 'she', "she's", 'shes', 'should', 'show', 'side', 'since', 'six', 'sixty',
		'small', 'so', 'some', 'somehow', 'someone', 'something', 'sometime', 'sometimes', 'somewhere', 'still', 'such',
		'take', 'ten', 'than', 'thank', 'that', 'the', 'their', 'theirs', 'them', 'themselves', 'then', 'thence',
		'there', 'thereafter', 'thereby', 'therefore', 'therein', 'thereupon', 'these', 'they', "they're", 'theyre',
		'third', 'this', 'those', 'though', 'three', 'through', 'throughout', 'thru', 'thus', 'time', 'times', 'tis',
		'to', 'together', 'too', 'top', 'toward', 'towards', 'true', 'twas', 'twelve', 'twenty', 'two', 'un', 'under',
		'until', 'up', 'upon', 'us', 'use', 'users', 'very', 'via', 'want', 'wants', 'was', 'way', 'we', 'web', 'well',
		'were', 'what', 'whatever', 'when', 'whence', 'whenever', 'where', 'whereafter', 'whereas', 'whereby', 'wherein',
		'whereupon', 'wherever', 'whether', 'which', 'while', 'whither', 'who', 'whoever', 'whole', 'whom', 'whose',
		'why', 'will', 'with', 'within', 'without', 'would', 'yes', 'yet', 'you', 'your', 'yours', 'yourself', 'yourselves',
	);

	/**
	 * @var array Stores valid weight types
	 */
	public $validTypes = array( 'content', 'title', 'comment', 'comments', 'tax', 'excerpt', 'cf', 'slug' );

	/**
	 * @var array Stores valid search engine option keys
	 */
	public $validOptions = array( 'exclude', 'limit_to', 'attribute_to', 'stem', 'parent', 'mimes' );

	/**
	 * @var int Number of posts found in a query
	 */
	public $foundPosts = 0;

	/**
	 * @var int Number of pages in paginated results
	 */
	public $maxNumPages = 0;

	/**
	 * @var array Stores a purge queue
	 * @since 1.0.7
	 */
	public $purgeQueue = array();

	/**
	 * @var array Database tables utilized
	 * @since 1.2.3
	 */
	private $tables = array(
		array( 'table' => 'cf', 'exists' => false ),  // custom fields
		array( 'table' => 'index', 'exists' => false ),  // main index
		array( 'table' => 'log', 'exists' => false ),  // log
		array( 'table' => 'tax', 'exists' => false ),  // taxonomies
		array( 'table' => 'terms', 'exists' => false ),  // terms
	);

	/**
	 * @var bool Whether the database environment has been properly established
	 * @since 1.2.3
	 */
	public $validDatabaseEnvironment = true;

	/**
	 * @var bool Whether the indexer has been paused by the user
	 * @since 1.4
	 */
	public $paused = false;


	/**
	 * @var bool Overarching (forceful) condition whether to perform a search on this page load
	 */
	private $force_run = false;


	/**
	 * @var array Provide a way for regex to be used to extract matches before they're stripped of their punctuation (e.g. dates)
	 *
	 * @since 1.9
	 */
	private $term_pattern_whitelist = array(

		// these should go from most strict to most loose

		// functions
		"/\b(\\w+?)?\\(|[\\s\\n]\\(/is",

		// Date formats
		'/\b([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\b/is',       // date: YYYY-MM-DD
		'/\b([0-9]{1,2}-[0-9]{1,2}-[0-9]{4})\b/is',       // date: MM-DD-YYYY
		'/\b([0-9]{4}\\/[0-9]{1,2}\\/[0-9]{1,2})\b/is',   // date: YYYY/MM/DD
		'/\b([0-9]{1,2}\\/[0-9]{1,2}\\/[0-9]{4})\b/is',   // date: MM/DD/YYYY

		// IP
		'/\b(\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3})\b/is',    // IPv4

		// initials
		"/\\b((?:[A-Za-z]\\.\\s{0,1})+)/isu",

		// version numbers: 1.0 or 1.0.4 or 1.0.5b1
		'/\b([a-z0-9]+(?:\\.[a-z0-9]+)+)\b/is',

		// serial numbers
		"/\\b([-_]?[[:alnum:]]+(?:[-_]+[[:alnum:]]+)+[-_]?)\\b/isu",  // hyphen/underscore separator

		// strings of digits
		"/\\b(\\d{1,})\\b/is",

		// e.g. M&M, M & M
		"/\\b([[:alnum:]]+\\s?(?:&\\s?[[:alnum:]]+)+)\b/isu",

	);

	/**
	 * @var bool Whether settings were updated
	 * @since 1.9.1
	 */
	public $settings_updated = false;

	/**
	 * @var array Per-post-type-specific weights for search results
	 * @since 2.3
	 */
	public $results_weights = array();


	/**
	 * @var string User capability to modify SearchWP settings in the WordPress admin
	 * @since 2.1
	 */
	public $settings_cap = 'manage_options';

	/**
	 * @var string Endpoint used when indexing (prefixed by site_url())
	 * @since 2.3
	 */
	public $endpoint = '';

	/**
	 * @var bool Whether the alternate indexer should be used
	 * @since 2.5
	 */
	private $alternate_indexer = false;

	/**
	 * @var array Any search query modifications that were applied (e.g. min word length)
	 * @since 2.5
	 */
	private $search_query_mods = array();

	/**
	 * @var SearchWP_Admin_Settings Settings UI utility class
	 * @since 2.6
	 */
	private $settings_utils;

	/**
	 * @var SearchWP_i18n utility class
	 * @since 2.9
	 */
	public $i18n;

	/**
	 * var SearchWP_Admin_Ajax utility class
	 * @since 2.9
	 */
	public $ajax;

	/**
	 * @var SearchWP_Nags utility class to help with notifications in the admin
	 * @since 2.6
	 */
	private $nags_utils;

	/**
	 * @var string The SQL used to generate this set of results
	 * @since 2.5
	 */
	private $search_sql;


	/**
	 * Singleton
	 *
	 * @return SearchWP
	 * @since 1.0
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SearchWP ) ) {

			// store background indexer request
			if ( isset( $_REQUEST['swpnonce'] ) ) {
				searchwp_delete_option( 'indexnonce' );
				searchwp_add_option( 'indexnonce', sanitize_text_field( $_REQUEST['swpnonce'] ) );
			}

			self::$instance = new SearchWP;
			self::$instance->init();

			// process the purge queue once everything is said and done
			add_action( 'shutdown', array( self::$instance, 'setup_purge_queue' ) );

			add_action( 'after_setup_theme', array( self::$instance, 'set_settings_cap' ) );
		}
		return self::$instance;
	}


	/**
	 * Set the capability necessary for interacting with SearchWP's settings in the WordPress admin
	 *
	 * @since 2.1
	 */
	function set_settings_cap() {
		$this->settings_cap = apply_filters( 'searchwp_settings_cap', $this->settings_cap );
	}


	/**
	 * Initialization routine. Sets version, directory, url, adds WordPress hooks, includes includes, triggers index
	 *
	 * @uses  get_post_types to determine which post types are in use
	 * @since 1.0
	 */
	function init() {

		$this->version  = SEARCHWP_VERSION;
		$this->dir      = dirname( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->pid      = str_replace( '.', '', uniqid( 'swppid', true ) );

		// includes
		include_once( dirname( __FILE__ ) . '/includes/class.i18n.php' );
		include_once( dirname( __FILE__ ) . '/includes/class.debug.php' );
		include_once( dirname( __FILE__ ) . '/includes/class.stemmer.php' );
		include_once( dirname( __FILE__ ) . '/includes/class.document-parser.php' );
		include_once( dirname( __FILE__ ) . '/includes/class.indexer.php' );
		include_once( dirname( __FILE__ ) . '/templates/tmpl.engine.config.php' );
		include_once( dirname( __FILE__ ) . '/templates/tmpl.supplemental.config.php' );
		include_once( dirname( __FILE__ ) . '/includes/class.search.php' );
		include_once( dirname( __FILE__ ) . '/includes/class.upgrade.php' );

		include_once( dirname( __FILE__ ) . '/admin/class.admin-settings.php' );
		include_once( dirname( __FILE__ ) . '/admin/class.extensions.php' );
		include_once( dirname( __FILE__ ) . '/admin/class.nags.php' );

		if ( is_admin() ) {
			include_once( dirname( __FILE__ ) . '/admin/class.conflicts.php' );
			include_once( dirname( __FILE__ ) . '/admin/class.notices.php' );
			include_once( dirname( __FILE__ ) . '/admin/class.dashboard.php' );
			include_once( dirname( __FILE__ ) . '/admin/class.systeminfo.php' );
			include_once( dirname( __FILE__ ) . '/admin/class.ajax.php' );

			// Ajax utility library
			$this->ajax = new SearchWP_Admin_Ajax();
			$this->ajax->init();
		}

		include_once( dirname( __FILE__ ) . '/includes/class.swp-query.php' );

		// instantiate nags
		$this->nags_utils = new SearchWP_Nags();
		$this->nags_utils->init();

		// grab our settings
		$this->settings = get_option( SEARCHWP_PREFIX . 'settings' );
		$this->license  = searchwp_get_license_key();
		$this->status   = get_option( SEARCHWP_PREFIX . 'license_status' );

		// append our indexer-specific settings since they're stored separately
		if ( $indexer_settings = get_option( SEARCHWP_PREFIX . 'indexer' ) ) {

			if ( ! is_array( $indexer_settings ) ) {
				$this->trigger_forced_indexer_chunk();
				$indexer_settings = get_option( SEARCHWP_PREFIX . 'indexer' );
			}

			$this->settings = array_merge( $this->settings, $indexer_settings );
		}

		// instantiate settings UI
		$this->settings_utils = new SearchWP_Admin_Settings();
		$this->settings_utils->init();

		// i18n
		$this->i18n = new SearchWP_i18n();
		$this->i18n->init();

		// implement Advanced settings and License management
		include_once( dirname( __FILE__ ) . '/admin/settings-impl-advanced.php' );
		include_once( dirname( __FILE__ ) . '/admin/settings-impl-license.php' );

		// introduced in version 2.5.7 as per WordPress 4.2
		$this->settings['utf8mb4'] = get_option( SEARCHWP_PREFIX . 'utf8mb4' );

		// hooks
		add_filter( 'block_local_requests',         '__return_false' );
		add_action( 'admin_menu',                   array( $this, 'admin_menu' ) );
		add_action( 'admin_init',                   array( $this, 'activation' ) );
		add_action( 'admin_init',                   array( $this, 'init_settings' ) );
		add_filter( 'cron_schedules',               array( $this, 'add_custom_cron_interval' ) );
		add_action( 'admin_init',                   array( $this, 'schedule_maintenance' ) );
		add_action( 'swp_indexer',                  array( $this, 'do_cron' ) );
		add_action( 'admin_enqueue_scripts',        array( $this, 'assets' ) );
		add_filter( 'heartbeat_received',           array( $this, 'heartbeat_received' ), 10, 2 );
		add_action( 'pre_get_posts',                array( $this, 'check_for_main_query' ), 0 );
		add_action( 'pre_get_posts',                array( $this, 'impose_engine_config' ), 20 );
		add_filter( 'request',                      array( $this, 'filter_request' ) );
		add_filter( 'the_posts',                    array( $this, 'wp_search' ), 0, 2 );
		add_filter( 'posts_request',                array( $this, 'maybe_cancel_wp_query' ), 10, 2 );
		add_action( 'add_meta_boxes',               array( $this, 'document_content_meta_box' ) );
		add_action( 'edit_attachment',              array( $this, 'document_content_save' ) );
		add_action( 'wp_before_admin_bar_render',   array( $this, 'admin_bar_menu' ) );
		add_action( 'shutdown',                     array( $this, 'shutdown' ), 9999 );
		add_action( 'wp_footer',                    array( $this, 'maybe_output_debug' ) );
		add_action( 'wp_footer',                    array( $this, 'admin_bar_entry_for_search' ) );
		add_action( 'wp_loaded',                    array( $this, 'load' ) );
		add_action( 'init',                         array( $this, 'prepare_endpoint' ) );

		add_filter( 'plugin_action_links_searchwp/searchwp.php',  array( $this, 'plugin_update_link' ) );

		// license maintenance (triggered by cron job)
		$license_utils = new SearchWP_Settings_Implementation_License();
		add_action( 'swp_maintenance', array( $license_utils, 'do_maintenance' ) );

		// support WordPress Importer by auto-pausing during imports
		add_action( 'import_start',                 array( $this, 'indexer_pause' ) );
		add_action( 'import_end',                   array( $this, 'indexer_unpause' ) );

		add_action( 'wp_ajax_searchwp_alternate_indexer_trigger', array( $this, 'handle_alternate_indexer_request' ) );

		// internal hooks
		add_filter( 'searchwp_results', array( $this, 'maybe_append_weight_to_result_title' ), 10, 2 );

		// core filter for Media search in the admin
		add_filter( 'ajax_query_attachments_args', array( $this, 'maybe_admin_media_search' ) );
	}

	/**
	 * WordPress adds some query vars for hierarchical post types when searching in the admin
	 * but that interferes with SearchWP's process, so we need to remove the flag that causes trouble
	 *
	 * @since 2.9.4
	 */
	function filter_request( $query_vars ) {
		$search_in_admin = apply_filters( 'searchwp_in_admin', false );

		if ( ! is_admin() || empty( $search_in_admin ) ) {
			return $query_vars;
		}

		if ( ! isset( $query_vars['fields'] ) ) {
			return $query_vars;
		}

		// If 'fields' is set, WP_Query ends up essentially short circuiting when we don't want it to
		unset( $query_vars['fields'] );

		return $query_vars;
	}

	/**
	 * SearchWP allows for developers to set a custom endpoint for indexer communication
	 *
	 * @since 2.3
	 */
	function prepare_endpoint() {
		// allow developer to customize the endpoint
		$this->endpoint = urlencode( apply_filters( 'searchwp_endpoint', '' ) );

		// if there's an endpoint (as opposed to site_url() which is the default) we need to add it
		if ( strlen( $this->endpoint ) ) {
			// note: we're already in the 'init' action so we're going to add our rewrite rule right here
			add_rewrite_rule( '^' . $this->endpoint . '/?', 'index.php?__searchwp_api', 'top' );

			// after this rewrite rule gets hit, the indexer die()s so we don't need a handler
		}

		// if the filtered endpoint does not match the saved endpoint in the settings, we need to flush our rules
		if ( ! isset( $this->settings['endpoint'] ) ) {
			$this->settings['endpoint'] = '';
		}
		if ( $this->endpoint !== $this->settings['endpoint'] ) {
			$this->settings['endpoint'] = sanitize_text_field( $this->endpoint );   // overwrite the setting
			searchwp_set_setting( 'endpoint', $this->endpoint );                    // persist the setting
			flush_rewrite_rules();                                                  // flush the rules
		}

		// finalize the endpoint by prefixing the site_url()
		if ( strlen( $this->endpoint ) ) {
			$this->endpoint = trailingslashit( $this->endpoint );
		} else {
			$this->endpoint = 'index.php';
		}

		// accommodate HTTP Basic Auth
		$root_url = trailingslashit( site_url() );

		$http_basic_auth_creds = apply_filters( 'searchwp_basic_auth_creds', false );
		if ( is_array( $http_basic_auth_creds ) && isset( $http_basic_auth_creds['username'] ) && isset( $http_basic_auth_creds['password'] ) ) {
			$credentials_prepared = urlencode( $http_basic_auth_creds['username'] ) . ':' . urlencode( $http_basic_auth_creds['password'] );
			$root_url = str_replace( '//', '//' . $credentials_prepared . '@', $root_url );
			add_filter( 'searchwp_indexer_loopback_args', array( $this, 'insert_http_auth_creds' ) );
		}

		$this->endpoint = esc_url_raw( $root_url . $this->endpoint );
	}

	/**
	 * Callback that inserts HTTP Basic Auth credentials into indexer request headers
	 *
	 * @since 2.3.4
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	function insert_http_auth_creds( $args ) {
		$http_basic_auth_creds = apply_filters( 'searchwp_basic_auth_creds', false );
		if ( is_array( $http_basic_auth_creds ) && isset( $http_basic_auth_creds['username'] ) && isset( $http_basic_auth_creds['password'] ) ) {
			$args['headers'] = array(
				'Authorization' => 'Basic ' . base64_encode( $http_basic_auth_creds['username'] . ':' . $http_basic_auth_creds['password'] )
			);
		}
		return $args;
	}


	/**
	 * Perform various environment checks/initializations on wp_loaded
	 *
	 * @since 1.8
	 */
	function load() {
		global $wp_query;

		if ( apply_filters( 'searchwp_debug', false ) ) {

			$wp_upload_dir = wp_upload_dir();

			$debug = new SearchWPDebug();
			$debug->init( $wp_upload_dir['basedir'] );
		}

		do_action( 'searchwp_log', ' ' );
		do_action( 'searchwp_log', '========== INIT ' . $this->pid . ' ' . SEARCHWP_VERSION . ' ==========' );
		do_action( 'searchwp_log', ' ' );

		$this->prepare_endpoint();

		// set the registered post types
		$this->postTypes = array_merge(
			array(
				'post'       => 'post',
				'page'       => 'page',
				'attachment' => 'attachment',
			),
			get_post_types(
				array(
					'exclude_from_search' => false,
					'_builtin'            => false,
				)
			)
		);

		// check for upgrade
		new SearchWPUpgrade( $this->version );

		// ensure working database environment
		$this->check_database_environment();

		// devs can customize which post types are indexed, it doesn't make
		// sense to list post types that were excluded (or included (e.g. post types that don't
		// allow filtration of the exclude_from_search arg))
		$this->postTypes = $this->get_indexed_post_types();

		// if the settings were somehow edited directly in the database
		$this->settings = SWP()->validate_settings( $this->settings );

		// allow filtration of what SearchWP considers common words (i.e. ignores)
		$this->common = apply_filters( 'searchwp_common_words', $this->common );

		$this->alternate_indexer = apply_filters( 'searchwp_alternate_indexer', false );

		// one-stop filter to ensure SearchWP fires
		if ( $this->force_run = apply_filters( 'searchwp_force_run', $this->force_run ) ) {
			$wp_query->is_search = true;
		}

		$this->check_if_paused();

		$this->set_index_update_triggers();

		do_action( 'searchwp_load' );

		// handle index and/or purge requests
		$this->update_index();

		// reset short circuit check
		$this->indexing = false;
	}

	function validate_purge_queue( $purge_queue ) {

		if ( ! is_array( $purge_queue ) ) {
			$purge_queue = array();
		}

		if ( empty( $purge_queue ) ) {
			return $purge_queue;
		}

		// If there are duplicate purge queue entries we could very well trigger an infinite loop of delta updates
		$purge_queue = array_unique( $purge_queue );

		// Make sure items in the purge queue are not set to be excluded
		$excluded_ids = apply_filters( 'searchwp_exclude', array(), '', array() );
		$excluded_ids = array_map( 'absint', $excluded_ids );

		if ( ! empty( $excluded_ids ) ) {
			$purge_queue = array_diff( $purge_queue, $excluded_ids );
		}

		$aggressive_cleanup = apply_filters( 'searchwp_aggressive_delta_update', false );

		if ( ! empty( $aggressive_cleanup ) ) {
			// Anything that's excluded can be removed from the index
			$excluded_ids_in_index = $this->filter_posts_in_index( $excluded_ids );

			// Purge anything that is to be excluded anyway
			if ( ! empty( $excluded_ids_in_index ) ) {
				foreach ( $excluded_ids_in_index as $excluded_id ) {
					do_action( 'searchwp_log', 'Purge post (via validate_purge_queue) ' . $excluded_id );
					$this->purge_post( $excluded_id, true );
				}
			}
		}

		return $purge_queue;
	}

	function filter_posts_in_index( $post_ids ) {
		global $wpdb;

		if ( ! is_array( $post_ids ) ) {
			$post_ids = array( $post_ids );
		}

		$post_ids = array_map( 'absint', $post_ids );
		$post_ids = array_unique( $post_ids );

		$post_ids_sql = implode( ', ', $post_ids );

		$index_table = $wpdb->prefix . SEARCHWP_DBPREFIX . 'index';

		$ids_in_index_sql = "SELECT post_id FROM {$index_table} WHERE post_id IN ({$post_ids_sql}) GROUP BY post_id LIMIT 1000";

		$already_indexed = $wpdb->get_col( $ids_in_index_sql );

		$already_indexed = array_map( 'absint', $already_indexed );

		return $already_indexed;
	}

	/**
	 * SearchWP queues up post objects that must be purged, this function records them
	 *
	 * @since 1.3.1
	 */
	function setup_purge_queue() {
		if ( ! empty( $this->purgeQueue ) ) {
			do_action( 'searchwp_log', 'setup_purge_queue() ' . count( $this->purgeQueue ) );
			$existingPurgeQueue = searchwp_get_option( 'purge_queue' );

			if ( is_array( $existingPurgeQueue ) && ! empty( $existingPurgeQueue ) ) {
				foreach ( $existingPurgeQueue as $postToPurge ) {
					$postToPurge = absint( $postToPurge );
					if ( ! isset( $this->purgeQueue[ $postToPurge ] ) ) {
						$this->purgeQueue[ $postToPurge ] = $postToPurge;
					}
				}
			}

			// if the alternative indexer is in play, we need to process
			// the purge queue right now instead of relying on background processing
			if ( $this->alternate_indexer ) {

				foreach ( $this->purgeQueue as $post_to_purge ) {
					$this->purge_post( absint( $post_to_purge ) );
				}

				$this->trigger_index();

				// now that the purge queue has been processed, we need to clear it
				// out so as to prevent the purge collection from taking place
				$this->purgeQueue = array();
			}

			searchwp_update_option( 'purge_queue', $this->purgeQueue );
		}
	}


	/**
	 * Callback to WordPress' shutdown action, used to ensure only a single SearchWP process was running
	 *
	 * @since 1.5.5
	 */
	function shutdown( $forced = false ) {
		if ( $forced ) {
			return;
		}

		do_action( 'searchwp_log', ' ' );
		do_action( 'searchwp_log', '========== END ' . $this->pid . ' ==========' );
		do_action( 'searchwp_log', ' ' );
	}


	/**
	 * Getter for the pid
	 *
	 * @return string process ID
	 * @since 1.5.5
	 */
	function get_pid() {
		return $this->pid;
	}


	/**
	 * Implement necessary hooks for delta index updates
	 *
	 * @since 1.8
	 */
	function set_index_update_triggers() {

		$prevent_triggers = searchwp_get_option( 'prevent_delta_triggers' );
		if ( ! empty( $prevent_triggers ) ) {
			do_action( 'searchwp_log', 'Skipping index update triggers' );
			return;
		}

		if ( is_admin() && current_user_can( 'edit_posts' ) ) {
			add_action( 'save_post', array( $this, 'purge_post_via_edit' ), 999 );
			add_action( 'add_attachment', array( $this, 'purge_post_via_edit' ), 999 );
			add_action( 'edit_attachment', array( $this, 'purge_post_via_edit' ), 999 );
		} elseif ( is_admin() ) {
			do_action( 'searchwp_log', 'User cannot edit_posts, delta hooks omitted' );
		}

		if ( is_admin() && current_user_can( 'delete_posts' ) ) {
			add_action( 'before_delete_post', array( $this, 'purge_post_via_edit' ), 999 );
		} elseif ( is_admin() ) {
			do_action( 'searchwp_log', 'User cannot delete_posts, delta hooks omitted' );
		}

		// we want to purge a post from the index when comments are manipulated
		add_action( 'comment_post',   array( $this, 'purge_post_via_comment' ) );
		add_action( 'edit_comment',   array( $this, 'purge_post_via_comment' ) );
		add_action( 'trash_comment',  array( $this, 'purge_post_via_comment' ) );
		add_action( 'delete_comment', array( $this, 'purge_post_via_comment' ) );

		add_action( 'delete_attachment', array( $this, 'purge_post_via_edit' ), 999 );

		// purge a post from the index when a related term is deleted
		add_action( 'set_object_terms', array( $this, 'purge_post_via_term' ), 10, 6 );
	}


	/**
	 * Add an Update force check on the plugin page
	 *
	 * @param $links
	 *
	 * @return array Links to include on the Plugins page
	 */
	function plugin_update_link( $links ) {
		if ( current_user_can( apply_filters( 'searchwp_settings_cap', 'manage_options' ) ) ) {
			$settings_link = admin_url( 'options-general.php?page=searchwp' );
			$settings_link = '<a href="' . esc_url( $settings_link ) . '">' . __( 'Settings', 'searchwp' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}


	/**
	 * Outputs HTML comments containing diagnostic information about what took place during a single pageload
	 *
	 * @since 1.6.4
	 */
	function maybe_output_debug() {

		if ( apply_filters( 'searchwp_debug', false ) ) { ?>

<!-- [SearchWP] Debug Information

SearchWP performed a search: <?php echo $this->ran ? 'Yes' : 'No'; ?>

Searches performed: <?php echo count( $this->diagnostics ); ?>
<?php $searchCount = 1; foreach ( $this->diagnostics as $diagnostics ) : ?>


== SEARCH <?php echo esc_html( $searchCount ); ?> ==
Search Engine: <?php echo ( isset( $diagnostics['engine'] ) ) ? esc_html( $diagnostics['engine'] ) : '[[ERROR]]'; ?>

Accepted search terms: <?php echo ( is_array( $diagnostics['terms'] ) && ! empty( $diagnostics['terms'] ) ) ? esc_html( implode( ' ', $diagnostics['terms'] ) ) : '[[NONE]]'; ?>

Total results found: <?php echo ( isset( $diagnostics['found_posts'] ) ) ? esc_html( $diagnostics['found_posts'] ) : '[[ERROR]]'; ?>

Total query time: <?php echo ( isset( $diagnostics['profiler'] ) ) ? esc_html( floatval( $diagnostics['profiler']['after'] ) - floatval( $diagnostics['profiler']['before'] ) ) : '[[ERROR]]'; ?>s
Results in this set:
<?php
// grab just post IDs and titles
$postsArePosts = true;
include_once( dirname( __FILE__ ) . '/vendor/class.consoletable.php' );
$debug_table = new SearchWPConsoleTable();
if ( is_array( $diagnostics['posts'] ) && isset( $diagnostics['posts'][0] ) ) {
	if ( is_numeric( $diagnostics['posts'][0] ) ) {
		// developer wanted only post IDs
		$postsArePosts = false;
	}

	$debug_table->add_header( __('Post ID', 'searchwp' ) );
	$debug_table->add_header( __('Weight', 'searchwp' ) );
	$debug_table->add_header( __('Title', 'searchwp' ) );

	foreach ( $diagnostics['posts'] as $key => $post ) {
		// get the proper ID and title
		if ( $postsArePosts ) {
			$post_id = $post->ID;
			$post_title = $post->post_title;
		} else {
			$post_id = $post;
			$post_title = get_the_title( $post );
		}

		// the search just ran so we can reference the result weights
		$weights = $this->results_weights;
		$post_weight = array_key_exists( $post_id, $weights ) ? absint( $weights[ $post_id ]['weight'] ) : false;

		$debug_table->add_row();
		$debug_table->add_column( $post_id );
		$debug_table->add_column( $post_weight );
		$debug_table->add_column( $post_title );

		// update the array key with a streamlined value
		// $output = '[' . $post_id . ']';
		// if ( ! empty( $post_weight ) ) {
		// 	$output .= '(' . $post_weight . ')';
		// }
		// $output .= ' ' . $post_title;
		// echo esc_html( $output ) . "\n";
	}

	echo "\n";
	echo esc_html( strip_tags( $debug_table->get_table() ) );
	echo "\n\n";
} else {
	echo '[[NONE]]';
}
?>
<?php $searchCount++; endforeach; ?>

-->
		<?php }
	}


	/**
	 * Add the SearchWP Admin Bar root menu
	 *
	 * @since 1.5
	 *
	 * @param $name
	 * @param $id
	 * @param bool $href
	 */
	function admin_bar_add_root_menu( $name, $id, $href = false ) {
		global $wp_admin_bar;

		if ( ! is_admin_bar_showing() ) {
			return;
		}

		if ( method_exists( $wp_admin_bar, 'add_menu' ) ) {
			$wp_admin_bar->add_menu( array(
					'id'      => $id,
					'meta'    => array(),
					'title'   => $name,
					'href'    => $href,
				)
			);
		}
	}


	/**
	 * Add a SearchWP Admin Bar sub menu
	 *
	 * @since 1.5
	 *
	 * @param $name
	 * @param $link
	 * @param $root_menu
	 * @param $id
	 * @param bool $meta
	 */
	function admin_bar_add_sub_menu( $name, $link, $root_menu, $id, $meta = false ) {
		global $wp_admin_bar;

		if ( ! is_admin_bar_showing() ) {
			return;
		}

		if ( method_exists( $wp_admin_bar, 'add_menu' ) ) {
			$wp_admin_bar->add_menu( array(
					'parent' => $root_menu,
					'id'     => $id,
					'title'  => $name,
					'href'   => $link,
					'meta'   => $meta,
				)
			);
		}
	}


	/**
	 * Determine the last time a post was indexed
	 *
	 * @since 1.5
	 *
	 * @param $post_id
	 * @param bool $timeDiff
	 *
	 * @return bool|int|string
	 */
	function get_last_indexed_time( $post_id, $timeDiff = false ) {

		if ( empty( $post_id ) ) {
			do_action( 'searchwp_log', 'No $post_id provided' );
			return false;
		}

		$lastIndex = get_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'last_index', true );

		$timestamp = ( ! empty( $lastIndex ) ) ? absint( $lastIndex ) : false;
		$timestamp = ( $timeDiff && $timestamp ) ? human_time_diff( date( 'U', $timestamp ), current_time( 'timestamp' ) ) . __( ' ago', 'searchwp' ) : $timestamp;

		if ( ! empty( $timestamp ) ) {
			do_action( 'searchwp_log', 'get_last_indexed_time(): ' . print_r( $timestamp, true ) );
		}

		return $timestamp;
	}


	/**
	 * Callback to implement the SearchWP Admin Bar menu item
	 *
	 * @since 1.5
	 */
	function admin_bar_menu() {
		global $pagenow, $post, $wpdb;

		if ( ! apply_filters( 'searchwp_admin_bar', true ) ) {
			return;
		}

		// only show in the admin and if user can manage options
		if ( ! is_admin() || ! current_user_can( $this->settings_cap ) ) {
			return;
		}

		// only show if user can manage

		// root menu
		$this->admin_bar_add_root_menu(
			'SearchWP',
			$this->textDomain,
			esc_url( get_admin_url() . 'options-general.php?page=' . $this->textDomain )
		);

		// settings
		$settings_label = __( 'Settings', 'searchwp' );
		$this->admin_bar_add_sub_menu(
			$settings_label,
			esc_url( get_admin_url() . 'options-general.php?page=' . $this->textDomain ),
			$this->textDomain,
			$this->textDomain . '_settings'
		);

		// pause toggle
		$saved_settings = searchwp_get_option( 'advanced' );
		$toggleLabel = isset( $saved_settings['disable_indexer'] ) && ! empty( $saved_settings['disable_indexer'] ) ? __( 'Enable Indexer', 'searchwp' ) : __( 'Disable Indexer', 'searchwp' );
		$this->admin_bar_add_sub_menu(
			$toggleLabel,
			esc_url( add_query_arg( 'swppausenonce', wp_create_nonce( 'swppausenonce' ) ) ),
			$this->textDomain,
			$this->textDomain . '_toggle_pause'
		);

		// last indexed
		switch ( $pagenow ) {
			case 'post.php':
				do_action( 'searchwp_log', 'Current page is post.php' );
				if ( isset( $post->ID ) ) {
					do_action( 'searchwp_log', '$post->ID = ' . $post->ID );

					// we need to pull the purge queue manually to see if this post is currently waiting to be indexed
					$tmpPurgeQueue = searchwp_get_option( 'purge_queue' );
					if ( ! empty( $tmpPurgeQueue ) ) {
						if ( is_array( $tmpPurgeQueue ) ) {
							do_action( 'searchwp_log', 'Temporary purge queue: ' . implode( ', ', $tmpPurgeQueue ) );
						} else {
							do_action( 'searchwp_log', 'Temporary purge queue: ' . print_r( $tmpPurgeQueue, true ) );
						}
					}

					// if we happen to be viewing an edit screen for a post in line to be indexed, say so
					if ( is_array( $tmpPurgeQueue ) && in_array( $post->ID, $tmpPurgeQueue ) ) {
						do_action( 'searchwp_log', 'Currently being indexed' );
						$lastIndexedMessage = __( 'In index queue', 'searchwp' );
					} else {
						// last indexed
						$lastIndexed = $this->get_last_indexed_time( $post->ID, true );

						// there's a chance this functionality was added after a post actually was indexed, so let's check for that
						if ( ! $lastIndexed ) {
							// see if this post ID is in the index
							$post->ID = absint( $post->ID );
							$postInIndex = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swp_index WHERE {$wpdb->prefix}swp_index.post_id = %d LIMIT 1", absint( $post->ID ) ) );
							if ( ! empty( $postInIndex ) ) {
								// if there's a term cache the post is getting chunked right now
								$stored_terms = get_post_meta( $post->ID, '_' . SEARCHWP_PREFIX . 'terms', true );
								if ( ! empty( $stored_terms ) ) {
									$lastIndexedMessage = __( 'This entry is being indexed', 'searchwp' );
								} else {
									$lastIndexedMessage = __( 'This entry is indexed', 'searchwp' );
								}
							} else {
								// check to see if we can give some context as to why a post is not indexed
								$status = get_post_status( $post->ID );
								$applicable_post_statuses = (array) apply_filters( 'searchwp_post_statuses', array( 'publish' ), null );
								if ( ! in_array( $status, $applicable_post_statuses ) ) {
									$lastIndexedMessage = __( 'Not indexed ', 'searchwp' ) . '(' . __( 'status is', 'searchwp' ) . ': <code>' . esc_html( $status ) . '</code>)';
								} else {
									// some unknown problem
									$lastIndexedMessage = __( 'Not indexed', 'searchwp' );
								}
							}
						} else {
							$lastIndexedMessage = __( 'Last indexed', 'searchwp' ) . ' ' . $lastIndexed;
						}

						do_action( 'searchwp_log', $lastIndexedMessage );
					}

					// add the menu item
					$this->admin_bar_add_sub_menu(
						$lastIndexedMessage,
						null,
						$this->textDomain,
						$this->textDomain . '_last_indexed'
					);
				} else {
					do_action( 'searchwp_log', '$post->ID was not defined' );
				}
				break;
		}

		// link to stats
		$this->admin_bar_add_sub_menu(
			__( 'Statistics', 'searchwp' ),
			esc_url( get_admin_url() . 'index.php?page=searchwp-stats' ),
			$this->textDomain,
			$this->textDomain . '_stats'
		);

	}


	/**
	 * Pause the indexer programmatically
	 *
	 * @since 1.5
	 *
	 * Updated in 2.8.2 to work with the Advanced tab toggles instead of it's own option
	 */
	function indexer_pause() {
		do_action( 'searchwp_log', 'indexer_pause()' );

		$this->paused = true;

		$saved_settings = searchwp_get_option( 'advanced' );

		if ( ! is_array( $saved_settings ) ) {
			$saved_settings = array();
		}

		$saved_settings['disable_indexer'] = $this->paused;

		searchwp_update_option( 'advanced', $saved_settings );
	}


	/**
	 * Unpause the indexer programmatically
	 *
	 * @since 1.5
	 */
	function indexer_unpause() {
		do_action( 'searchwp_log', 'indexer_unpause()' );

		$this->paused = false;

		$saved_settings = searchwp_get_option( 'advanced' );

		if ( ! is_array( $saved_settings ) ) {
			$saved_settings = array();
		}

		$saved_settings['disable_indexer'] = $this->paused;

		searchwp_update_option( 'advanced', $saved_settings );

		$this->trigger_index();
	}


	/**
	 * Called from the Advanced Settings page, toggles the global indexer pause flag
	 *
	 * @since 1.4
	 */
	function check_if_paused() {
		$saved_settings = searchwp_get_option( 'advanced' );
		$paused = isset( $saved_settings['disable_indexer'] ) && ! empty( $saved_settings['disable_indexer'] );
		$this->paused = empty( $paused ) ? false : true;
		if (
				( ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'swpadvanced' ) ) &&
				( isset( $_REQUEST['action'] ) && wp_verify_nonce( $_REQUEST['action'], 'swppauseindexer' ) )
				&& current_user_can( $this->settings_cap ) )
				||
				( ( isset( $_REQUEST['swppausenonce'] ) && wp_verify_nonce( $_REQUEST['swppausenonce'], 'swppausenonce' ) )
						&& current_user_can( $this->settings_cap ) )
		) {
			if ( $this->paused ) {
				$this->indexer_unpause();
			} else {
				$this->indexer_pause();
			}
		}

		// allow devs to pause the indexer in realtime
		$this->paused = apply_filters( 'searchwp_indexer_paused', $this->paused ); // legacy

		// enabled is the opposite of paused (if indexer is paused, it should not be enabled)
		$maybe_paused = is_bool( $this->paused ) ? $this->paused : false;
		if ( ! $maybe_paused ) {
			$this->paused = ! apply_filters( 'searchwp_indexer_enabled', true );
		}

		if ( $this->paused ) {
			do_action( 'searchwp_log', 'Checking indexer: PAUSED' );
		}
	}


	/**
	 * Fire request to validate database environment and take proper action if requirements aren't met
	 *
	 * @since 1.3.1
	 */
	function check_database_environment() {
		global $wpdb;

		// make sure the database environment is proper
		if ( false == searchwp_get_setting( 'valid_db_environment' ) ) {
			do_action( 'searchwp_log', 'check_database_environment(): Database environment unconfirmed' );
			$this->validate_database_environment();
		}

		if ( is_admin() && current_user_can( 'manage_options' ) && ! $this->validDatabaseEnvironment ) {
			do_action( 'searchwp_log', 'check_database_environment(): Database environment invalid' );

			// automatically deactivate
			/** @noinspection PhpIncludeInspection */
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
			deactivate_plugins( __FILE__ );

			// determine which table(s) were not created
			$tables = array();
			foreach ( $this->tables as $table ) {
				if ( false === $table['exists'] ) {
					$tables[] = esc_html( $wpdb->prefix . SEARCHWP_DBPREFIX . $table['table'] );
				}
			}

			$message = __( '<p>SearchWP <strong>has been automatically deactivated</strong> because it failed to create necessary database table(s):</p>', 'searchwp' );
			$message .= '<ul><li><code>' . implode( '</code></li><li><code>', $tables ) . '</code></li></ul>';
			$message .= __( '<p>Please ensure the applicable MySQL user has <code>CREATE</code> permissions and try activating again.</p>', 'searchwp' );
			$message .= '<p><a href="' . esc_url( trailingslashit( get_admin_url( 'plugins.php' ) ) ) . '">' . __( 'Back to Plugins', 'searchwp' ) . '</a></p>';

			// In order for the installation routine to run again we need to remove the version flag
			delete_option( SEARCHWP_PREFIX . 'version' );

			// output helpful message and die
			do_action( 'searchwp_log', 'Shutting down after discovering invalid database environment' );
			$this->shutdown( true );

			wp_die( $message );
		}
	}


	/**
	 * Potentially process background index/purge requests
	 *
	 * @since 1.3.1
	 */
	function update_index() {

		searchwp_check_for_stalled_indexer( 360 );

		// store the purge queue... just in case
		$toPurge = searchwp_get_option( 'purge_queue' );
		$validated_purge_queue = $this->validate_purge_queue( $toPurge );

		if ( count( $toPurge ) !== count( $validated_purge_queue ) ) {
			searchwp_update_option( 'purge_queue', $validated_purge_queue );
			$toPurge = $validated_purge_queue;
			do_action( 'searchwp_log', 'Refined purge queue (' . count( $toPurge ) . '): ' . implode( ', ', $toPurge ) );
		}

		$busy = searchwp_get_option( 'busy' );

		wp_cache_delete( 'searchwp_transient', 'options' );

		$purge_transient = get_option( 'swppurge_transient' );
		$purge_nonce = isset( $_REQUEST['swppurge'] ) ? sanitize_text_field( $_REQUEST['swppurge'] ) : '';

		// trigger background indexing
		if ( isset( $_REQUEST['swppurge'] ) && $purge_transient === $purge_nonce ) {
			if ( is_array( $toPurge ) && ! empty( $toPurge ) ) {
				do_action( 'searchwp_log', 'Purge queue (' . count( $toPurge ) . '): ' . implode( ', ', $toPurge ) );
				foreach ( $toPurge as $object_id ) {
					do_action( 'searchwp_log', 'Purge post ' . $object_id );
					$this->purge_post( intval( $object_id ) );
				}
				searchwp_update_option( 'doing_delta', false );
			} else {
				do_action( 'searchwp_log', 'Delta queue is inapplicable' );
			}

			searchwp_update_option( 'purge_queue', array() );
			$this->purgeQueue = array();
			do_action( 'searchwp_log', 'Purge queue processed, trigger_index()' );

			// allow developers the ability to disable automatic reindexing after edits in favor of their own method
			$automaticallyReindex = apply_filters( 'searchwp_auto_reindex', true );
			do_action( 'searchwp_log', '$automaticallyReindex = ' . print_r( $automaticallyReindex, true ) );
			if ( ! $busy && ! $this->paused && $automaticallyReindex ) {
				// if the initial index hasn't been built yet, we don't want this request to double up
				// in the use case where the user is editing posts while the initial index is still being built
				if ( searchwp_get_setting( 'initial_index_built' ) ) {
					// @since 2.9.7 the index trigger was moved to later in the request
					$this->trigger_index();
				}
			}

			do_action( 'searchwp_log', 'Shutting down after purge request and index trigger' );
			$this->shutdown( true );
			die();
		} elseif ( ! $this->paused && ! $this->indexing && ! empty( $_REQUEST['swpnonce'] ) && get_option( 'searchwp_transient' ) === sanitize_text_field( $indexnonce = searchwp_get_option( 'indexnonce' ) ) ) {

			if ( ! $this->alternate_indexer ) {
				$this->indexing = true;
				$hash = sanitize_text_field( $indexnonce );
				searchwp_delete_option( 'indexnonce' );
				do_action( 'searchwp_log', 'Performing background index ' . $hash );
				if ( ! $busy ) {
					searchwp_update_option( 'busy', true );
					do_action( 'searchwp_log', 'OK to index, proceed' );
					new SearchWPIndexer( $hash );
				} else {
					do_action( 'searchwp_log', '!!! Indexer BUSY !!!' );
				}

				exit;
			}
		} elseif ( $this->indexing ) {
			do_action( 'searchwp_log', 'SHORT CIRCUIT: index process already running' );
		}

		$okay_to_delta = true;

		if ( isset( $_REQUEST['swppurge'] ) && $purge_transient !== $purge_nonce ) {
			do_action( 'searchwp_log', 'Invalid delta update request nonce' );
			do_action( 'searchwp_log', '   Nonce: ' . $purge_nonce );
			do_action( 'searchwp_log', '   Local: ' . $purge_transient );

			if ( is_array( $toPurge ) && ! empty( $toPurge ) ) {
				$okay_to_delta = false;
				do_action( 'searchwp_log', 'However, local delta queue passes check' );
				do_action( 'searchwp_log', 'Purge queue (' . count( $toPurge ) . '): ' . implode( ', ', $toPurge ) );
			}
		}

		// check to see if we need to process a purgeQueue
		if ( is_array( $toPurge ) && ! empty( $toPurge ) && false == searchwp_get_setting( 'processing_purge_queue' ) && searchwp_get_setting( 'initial_index_built' ) ) {
			do_action( 'searchwp_log', 'Delta update request: ' . implode( ', ', $toPurge ) );
			if ( apply_filters( 'searchwp_background_deltas', true ) ) {
				// proceed with delta update
				do_action( 'searchwp_log', 'Automatic delta index update' );
				$doing_delta = searchwp_get_option( 'doing_delta' );
				if ( ! $doing_delta ) {

					// prevent delta update cycle by maxing out the number of attempts
					$delta_attempts = absint( searchwp_get_option( 'delta_attempts' ) );

					if ( $delta_attempts > apply_filters( 'searchwp_max_delta_attempts', 5 ) ) {
						do_action( 'searchwp_log', 'TOO MANY DELTA ATTEMPTS, ABORT' );
						return;
					}

					searchwp_update_option( 'delta_attempts', $delta_attempts + 1 );

					// at this point we're viewing the screen that loads after making an edit, so we can't die();
					searchwp_update_option( 'doing_delta', true );

					$invalid_purge_requests = searchwp_get_option( 'purge_queue_req' );
					$invalid_purge_requests = absint( $invalid_purge_requests );

					if ( $okay_to_delta ) {
						if ( ! empty( $invalid_purge_requests ) ) {
							do_action( 'searchwp_log', 'Resetting delta request count' );
							searchwp_update_option( 'purge_queue_req', 0 );
						}
						$this->process_updates();
					} else {
						$new_purge_request_count = $invalid_purge_requests + 1;
						searchwp_update_option( 'purge_queue_req', $new_purge_request_count );
						do_action( 'searchwp_log', '!! Breaking out of delta update to prevent cycling (' . $new_purge_request_count . ')' );
					}
				}
			} else {
				do_action( 'searchwp_log', 'Background delta index update prevented' );
			}
		} else {
			if ( searchwp_get_setting( 'processing_purge_queue' ) ) {
				do_action( 'searchwp_log', 'Cleaning up after processing purge queue' );
				searchwp_set_setting( 'processing_purge_queue', false );
				searchwp_update_option( 'delta_attempts', 0 );
			}
		}

	}


	/**
	 * Perform the delta index updates based on what's changed (the purge queue)
	 *
	 * @since 1.6
	 */
	function process_updates( $conclude = false ) {
		do_action( 'searchwp_log', 'process_updates()' );

		// $debugging_enabled = apply_filters( 'searchwp_debug', false );
		// if ( $debugging_enabled ) {
		// 	$debug = new SearchWPDebug();
		// 	$call_trace = $debug->get_call_trace();
		// 	do_action( 'searchwp_log', print_r( $call_trace, true ) );
		// }

		$hash = sprintf( '%.22F', microtime( true ) ); // inspired by $doing_wp_cron
		update_option( 'swppurge_transient', $hash, 'no' );
		searchwp_set_setting( 'processing_purge_queue', true );

		$destination = esc_url( $this->endpoint . '?swppurge=' . $hash );

		do_action( 'searchwp_log', 'Deferred purge ' . $destination );

		// fire off our background request
		$timeout = abs( apply_filters( 'searchwp_timeout', 0.02 ) );

		$args = array(
			'body'        => array( 'swppurge' => $hash ),
			'blocking'    => false,
			'user-agent'  => 'SearchWP',
			'timeout'     => $timeout,
			'sslverify'   => false,
		);
		$args = apply_filters( 'searchwp_indexer_loopback_args', $args );

		wp_remote_post( $destination, $args );

		if ( ! empty( $conclude ) ) {
			do_action( 'searchwp_log', 'process_updates() has terminated the page load' );
			die();
		}
	}

	/**
	 * Perform an indexer communication test and return the result
	 * @since 2.3.4
	 */
	function get_indexer_communication_result() {

		if ( $this->alternate_indexer ) {
			return;
		}

		$destination = esc_url( $this->endpoint );

		$args = array(
			'blocking'    => true,
			'user-agent'  => 'SearchWP',
			'timeout'     => 5,
			'sslverify'   => false,
		);

		$args = apply_filters( 'searchwp_indexer_com_check_args', $args );

		$response = wp_remote_post( $destination, $args );
		$this->indexer_http_response_handler( $response );

		return $response;
	}

	/**
	 * Handle various outcomes from wp_remote_post() calls
	 * @since 2.3.4
	 *
	 * @param $response
	 */
	function indexer_http_response_handler( $response ) {

		if ( is_wp_error( $response ) ) {
			return;
		}

		$existing_settings = array(
			'basic_auth' => searchwp_get_setting( 'basic_auth' ),
		);

		// check for HTTP Basic Auth that has changed over time
		if ( isset( $response['response']['code'] ) && 401 === (int) $response['response']['code'] ) {
			// HTTP Basic Auth is enabled
			if ( true !== $existing_settings['basic_auth'] ) {
				// SearchWP previously detected that it was not enabled, so flag it as enabled because it is now
				searchwp_set_setting( 'basic_auth', true );
			}
		} elseif ( isset( $response['response']['code'] ) && 401 !== (int) $response['response']['code'] && true == $existing_settings['basic_auth'] ) {
			// SearchWP has the environment flagged as using HTTP Basic Auth, but that is no longer the case
			searchwp_set_setting( 'basic_auth', 'no' );
		}
	}


	/**
	 * Checks to make sure the proper database tables exist
	 *
	 * @since 1.2.3
	 */
	function validate_database_environment() {
		do_action( 'searchwp_log', 'validate_database_environment()' );
		$this->validDatabaseEnvironment = $this->custom_db_tables_exist();
		searchwp_set_setting( 'valid_db_environment', $this->validDatabaseEnvironment );
	}


	/**
	 * Checks whether every necessary custom database table exists
	 *
	 * @since 2.5
	 * @return bool
	 */
	function custom_db_tables_exist() {
		global $wpdb;

		$tables_exist = true;

		foreach ( $this->tables as $tableKey => $tableMeta ) {
			$tableName = $wpdb->prepare( '%s', $wpdb->prefix . SEARCHWP_DBPREFIX . $tableMeta['table'] );
			$tableSQL = $wpdb->get_results( "SHOW TABLES LIKE {$tableName}" , ARRAY_N );
			if ( ! empty( $tableSQL ) ) {
				$this->tables[ $tableKey ]['exists'] = true;
			} else {
				$tables_exist = false;
			}
		}

		return $tables_exist;
	}


	/**
	 * Outputs an upgrade notice on the Plugins page
	 *
	 * @param $plugin_file
	 * @param $plugin_data
	 * @param $status
	 *
	 * @since 1.0
	 */
	function plugin_row( $plugin_file = null, $plugin_data, $status = null ) {
		if ( ! is_null( $plugin_file ) ) {
			$plugin_file = null;
		}
		if ( ! is_null( $status ) ) {
			$status = null;
		}
		do_action( 'searchwp_log', 'plugin_row()' );
		?>
		<tr class="plugin-update-tr searchwp">
			<td colspan="3" class="plugin-update">
				<div class="update-message">
					<?php _e( 'SearchWP must be updated to the latest version to work with ', 'searchwp' ); ?> <?php echo esc_html( $plugin_data['Name'] ); ?>
				</div>
			</td>
		</tr>
	<?php
	}


	/**
	 * Set up and trigger background index call
	 */
	function trigger_index() {
		if ( ! $this->alternate_indexer ) {
			$hash = sprintf( '%.22F', microtime( true ) ); // inspired by $doing_wp_cron
			update_option( 'searchwp_transient', $hash, 'no' );

			$destination = esc_url( $this->endpoint . '?swpnonce=' . $hash );

			do_action( 'searchwp_log', 'trigger_index() ' . $destination );

			$timeout = abs( apply_filters( 'searchwp_timeout', 0.02 ) );

			$args = array(
				'body'       => array( 'swpnonce' => $hash ),
				'blocking'   => false,
				'user-agent' => 'SearchWP',
				'timeout'    => $timeout,
				'sslverify'  => false,
			);
			$args = apply_filters( 'searchwp_indexer_loopback_args', $args );

			wp_remote_post( $destination, $args );
		} else {
			$this->trigger_forced_indexer_chunk();
			// if the chunk size is less than the number of outstanding posts
			// we will need to rely on the cron job to pick up the other edits

			do_action( 'searchwp_log', 'Triggering indexer (alternate indexer)' );
		}
	}


	/**
	 * Checks to see if we're in the main query and stores result as isMainQuery property
	 *
	 * @param WP_Query $query Instance of WP_Query to check
	 * @return mixed $query
	 *
	 * @since 1.0
	 */
	function check_for_main_query( $query ) {
		if ( $query->is_main_query() || $this->force_run ) {
			if ( ! isset( $_GET['swpjumpstart'] ) ) {
				do_action( 'searchwp_log', 'check_for_main_query(): It is the main query' );
			}
			$this->isMainQuery = true;
		} else {
			$this->isMainQuery = false;
		}

		return $query;
	}

	/**
	 * By default SearchWP short circuits on empty search queries, but the engine configuration
	 * itself insofar as exclusion/inclusion should still take place here
	 *
	 * @since 2.8.5
	 *
	 * @param $query
	 */
	function impose_engine_config( $query ) {

		if ( is_admin() || ! $query->is_search || ! isset( $_REQUEST['s'] ) || ( isset( $_REQUEST['s'] ) && strlen( trim( $_REQUEST['s'] ) ) > 0 ) ) {
			// this only applies if it's an empty search outside the admin (which has SearchWP short circuit)
			return;
		}

		if ( apply_filters( 'searchwp_disable_impose_engine_config', false ) ) {
			return;
		}

		$searchwp_search = new SearchWPSearch();

		$search_terms = explode( ' ', get_search_query() );
		$search_terms = array_map( 'sanitize_text_field', $search_terms );
		$search_terms = array_map( 'trim', $search_terms );

		$searchwp_settings = $this->settings['engines']['default'];

		$post_types = array();
		$excluded   = array();
		$included   = array();

		foreach ( $searchwp_settings as $post_type => $post_type_settings ) {

			// enable the post type
			if ( ! empty( $post_type_settings['enabled'] ) ) {
				$post_types[] = $post_type;
			}

			// check for excluded
			$excluded = apply_filters( 'searchwp_exclude', $searchwp_search->get_excluded(), 'default', $search_terms );
			if ( is_array( $excluded ) ) {
				$excluded = array_map( 'absint', $excluded );
			} else {
				$excluded = array();
			}

			// check for included
			$included = apply_filters( 'searchwp_include', $searchwp_search->get_included(), 'default', $search_terms );
			if ( is_array( $included ) ) {
				$included = array_map( 'absint', $included );
			} else {
				$included = array();
			}
		}

		// force post type limit
		$query->set( 'post_type', $post_types );

		// force excluded IDs
		if ( ! empty( $excluded ) ) {
			$query->set( 'post__not_in', $excluded );
		}

		if ( ! empty( $included ) ) {
			$query->set( 'post__in', $included );
		}

	}


	/**
	 * Perform a search query
	 *
	 * @param string $engine The search engine name to use when performing the search
	 * @param        $terms  string|array The search terms to include in the query
	 * @param int    $page   Results are paged, return this page (1 based)
	 *
	 * @return array Search results post IDs ordered by weight DESC
	 * @uses  SearchWPSearch
	 * @since 1.0
	 */
	function search( $engine = 'default', $terms, $page = 1 ) {

		do_action( 'searchwp_log', 'search()' );

		$this->active = true;
		$this->ran = true;

		// at the very least, our terms are the search query
		$terms = $this->original_query = is_array( $terms ) ? trim( implode( ' ', $terms ) ) : trim( (string) $terms );

		// facilitate filtering the actual terms
		$terms = apply_filters( 'searchwp_terms', $terms, $engine );
		do_action( 'searchwp_log', '$terms after searchwp_terms = ' . var_export( $terms, true ) );

		// handle sanitization — this filter is also applied in the SearchWPSearch class constructor
		$sanitizeTerms = apply_filters( 'searchwp_sanitize_terms', true, $engine );
		if ( ! is_bool( $sanitizeTerms ) ) {
			$sanitizeTerms = true;
		}

		// whitelist search terms
		$pre_whitelist_terms = is_array( $terms ) ? implode( ' ', $terms ) : ' ' . $terms . ' ';
		$whitelisted_terms = $this->extract_terms_using_pattern_whitelist( $pre_whitelist_terms, false );

		if ( apply_filters( 'searchwp_exclusive_regex_matches', false ) && ! empty( $whitelisted_terms ) ) {
			$terms = $this->process_exclusive_regex_matches( $terms, $whitelisted_terms );
		}

		// if we should still sanitize our terms, do it
		if ( $sanitizeTerms ) {
			$terms = $this->sanitize_terms( $terms, $engine );
		}

		if ( is_array( $whitelisted_terms ) ) {
			$whitelisted_terms = array_filter( array_map( 'trim', $whitelisted_terms ), 'strlen' );
		}

		if ( is_array( $terms ) ) {
			$terms = array_filter( array_map( 'trim', $terms ), 'strlen' );
			$terms = array_unique( array_merge( $terms, $whitelisted_terms ) );
		} else {
			$terms .= ' ' . implode( ' ', $whitelisted_terms );
		}

		do_action( 'searchwp_log', '$sanitizeTerms = ' . print_r( $sanitizeTerms, true ) );
		do_action( 'searchwp_log', '$terms = ' . print_r( $terms, true ) );

		// set up our engine name
		if ( ! $this->is_valid_engine( $engine ) ) {
			/** @noinspection PhpUndefinedClassInspection */
			return new WP_Error( 'searchwp_invalid_engine', __( 'Invalid SearchWP Engine: ' . $engine, 'searchwp' ) );
		}

		do_action( 'searchwp_log', '$engine = ' . $engine );

		// make sure the search isn't overflowing with terms
		$maxSearchTerms = intval( apply_filters( 'searchwp_max_search_terms', 6, $engine ) );
		do_action( 'searchwp_log', 'searchwp_max_search_terms $maxSearchTerms = ' . $maxSearchTerms );
		$maxSearchTerms = intval( apply_filters( 'searchwp_max_search_terms_supplemental', $maxSearchTerms, $engine ) );
		do_action( 'searchwp_log', 'searchwp_max_search_terms_supplemental $maxSearchTerms = ' . $maxSearchTerms );
		$maxSearchTerms = intval( apply_filters( "searchwp_max_search_terms_{$engine}", $maxSearchTerms ) );
		do_action( 'searchwp_log', 'searchwp_max_search_terms_{$engine} $maxSearchTerms = ' . $maxSearchTerms );

		if ( count( $terms ) > $maxSearchTerms ) {
			$terms = array_slice( $terms, 0, $maxSearchTerms );
			do_action( 'searchwp_log', '$terms = ' . print_r( $terms, true ) );
			$this->register_search_query_modification( 'max_terms' );
		} else {
			do_action( 'searchwp_log', 'Terms within max search terms' );
		}

		// prep our args
		$args = array(
			'engine'         => $engine,
			'terms'          => $terms,
			'page'           => intval( $page ),
			'posts_per_page' => apply_filters( 'searchwp_posts_per_page', intval( get_option( 'posts_per_page' ) ), $engine, $terms, $page )
		);

		do_action( 'searchwp_log', '$args = ' . print_r( $args, true ) );

		// perform the search
		$profiler = array( 'before' => microtime() );
		$searchwp = new SearchWPSearch( $args );
		$profiler['after'] = microtime();

		$this->foundPosts       = intval( $searchwp->foundPosts );
		$this->maxNumPages      = intval( $searchwp->maxNumPages );
		$this->results_weights  = $searchwp->results_weights;
		$this->search_sql       = $searchwp->get_sql();

		// store diagnostics for debugging
		$this->diagnostics[] = array(
			'engine'        => $args['engine'],
			'terms'         => $args['terms'],
			'found_posts'   => $searchwp->foundPosts,
			'posts'         => $searchwp->posts,
			'profiler'      => $profiler,
			'args'          => $args,
		);

		do_action( 'searchwp_log', '$this->foundPosts = ' . $this->foundPosts );
		do_action( 'searchwp_log', '$this->maxNumPages = ' . $this->maxNumPages );

		$this->active = false;

		$results = apply_filters( 'searchwp_results', $searchwp->posts, array(
			'terms'       => $terms,
			'page'        => $args['page'],
			'order'       => 'DESC',
			'foundPosts'  => $this->foundPosts,
			'maxNumPages' => $this->maxNumPages,
			'engine'      => $engine,
		) );

		return $results;
	}

	/**
	 * When debugging is enabled, append the result weight to the title for reference
	 *
	 * @param $posts
	 * @param $search_args
	 *
	 * @return mixed
	 */
	function maybe_append_weight_to_result_title( $posts, /** @noinspection PhpUnusedParameterInspection */ $search_args ) {
		if (
			apply_filters( 'searchwp_debug', false )
			&& apply_filters( 'searchwp_debug_append_weights_to_titles', false )
			&& current_user_can( $this->settings_cap )
			&& ! empty( $posts )
		) {

			// the search just ran so we can reference the result weights
			$weights = $this->results_weights;

			foreach ( $posts as $key => $val ) {
				if ( array_key_exists( $posts[ $key ]->ID, $weights ) ) {
					$posts[ $key ]->post_title .= ' - ' . absint( $weights[ $posts[ $key ]->ID ]['weight'] );
				}
			}
		}

		return $posts;
	}

	/**
	 * Return the last stored search SQL
	 *
	 * @since 2.6
	 *
	 * @return string Last stored search query
	 */
	function get_last_search_sql() {
		return $this->search_sql;
	}


	/**
	 * Determines if an engine name is considered valid (e.g. stored in the settings)
	 *
	 * @param $engineName string The engine name to check
	 *
	 * @return bool
	 */
	public function is_valid_engine( $engineName ) {
		$engineName = sanitize_key( $engineName );
		$validEngine = is_string( $engineName ) && isset( $this->settings['engines'][ $engineName ] );
		do_action( 'searchwp_log', 'is_valid_engine( ' . print_r( $engineName, true ) .  ' ) = ' . var_export( $validEngine, true ) );

		return $validEngine;
	}

	/**
	 * Enforce 4-byte UTF-8 when utf8mb4 is not supported
	 *
	 * @since 2.5.7
	 * @link http://stackoverflow.com/questions/16496554/can-php-detect-4-byte-encoded-utf8-chars
	 *
	 * @param $string
	 *
	 * @return mixed
	 */
	function replace_4_byte( $string ) {
		return preg_replace( '%(?:
              \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
        )%xs', '', $string );
	}


	/**
	 * Removes punctuation
	 * @param $termString string The dirty string
	 *
	 * @return string The cleaned string
	 */
	public function clean_term_string( $termString ) {

		$punctuation = array( '(', ')', '{', '}', '[', ']', '·', "'", '´', '’', '‘', '”', '“', '„', '—', '–', '×', '…', '€', "\n", '.', ',', ';', '`' );

		if ( ! is_string( $termString ) ) {
			$termString = '';
		}

		$termString = trim( $termString );

		if ( function_exists( 'mb_convert_encoding' ) ) {
			$termString = mb_convert_encoding( $termString, 'UTF-8', 'UTF-8' );
		}

		if ( empty( $this->settings['utf8mb4'] ) ) {
			$termString = $this->replace_4_byte( $termString );
		}

		$termString = function_exists( 'mb_strtolower' ) ? mb_strtolower( $termString, 'UTF-8' ) : strtolower( $termString );
		$termString = stripslashes( $termString );

		// remove punctuation
		$termString = str_replace( $punctuation, ' ', $termString );
		$termString = preg_replace( '/[[:punct:]]/uiU', ' ', $termString );
		$termString = preg_replace( '/[\x00-\x1F\x7F\xA0]/u', ' ', $termString );
		$termString = preg_replace( '/[\x{0300}-\x{036f}]+/u', ' ', $termString );
		$termString = preg_replace( '/[^\P{C}\n]+/u', ' ', $termString );

		// remove spaces
		$termString = preg_replace( '/[[:space:]]/uiU', ' ', $termString );

		// final pass
		$termString = sanitize_text_field( $termString );
		$termString = trim( $termString );

		return $termString;
	}


	/**
	 * Sanitizes terms; should be trimmed, single words.
	 *
	 * @param $terms string|array The terms to sanitize
	 *
	 * @param string $engine
	 *
	 * @return array Valid terms
	 */
	public function sanitize_terms( $terms, $engine = 'default' ) {
		$validTerms = array();

		// always going to be a string when a search query is performed
		if ( is_string( $terms ) ) {

			$whitelisted_terms = $this->extract_terms_using_pattern_whitelist( $terms );

			// maybe remove matches so we don't have redundancy, they were buffered with spaces to ensure whole word matching instead of partial matching
			if ( ! empty( $whitelisted_terms ) ) {
				$terms = str_ireplace( $whitelisted_terms, '', $terms );
			}

			// clean up the double space flag we used
			$terms = str_replace( '  ', ' ', $terms );

			// process the (potentially stripped of whitelist matches) string to strip out unwanted punctuation
			$terms = $this->clean_term_string( trim( $terms ) );

			// put the terms in an array
			$terms = ( strpos( $terms, ' ' ) !== false ) ? explode( ' ', $terms ) : array( $terms );

			// after extracting whitelist matches there might be some empty keys
			$terms = array_map( 'trim', $terms );
			$terms = array_filter( $terms, 'strlen' );

			// maybe prepend our whitelisted terms to the final term array
			if ( ! empty( $whitelisted_terms ) && is_array( $whitelisted_terms ) ) {
				$whitelisted_terms = array_map( 'trim', $whitelisted_terms );
				$whitelisted_terms = array_filter( $whitelisted_terms, 'strlen' );
				$terms = array_merge( $whitelisted_terms, $terms );
			}
		}

		if ( is_array( $terms ) ) {

			// loop through each term, check it against the whitelist, and ensure it meets all criteria to be considered valid
			foreach ( $terms as $key => $term ) {

				$whitelist_match_check = ' ' . $term . ' ';

				// first check the term for a whitelist match
				$whitelist_matches = $this->extract_terms_using_pattern_whitelist( $term );

				if ( ! empty( $whitelist_matches ) ) {

					// if there were matches (but it wasn't a complete match) append it to the array for further processing
					$whitelist_extraction_result = str_ireplace( $whitelist_matches, '', $whitelist_match_check );

					// remove the buffer used for full matching
					if ( is_array( $whitelist_matches ) ) {
						$whitelist_matches = array_map( 'trim', $whitelist_matches );
						$whitelist_matches = array_filter( $whitelist_matches, 'strlen' );
					}

					if ( strlen( trim( $whitelist_extraction_result ) ) > 0 ) { // had extra whitespace
						// it was not an exact match so we need to clean what did not match
						$whitelist_extraction_result = $this->clean_term_string( $whitelist_extraction_result );

						// check for spaces in what was left over
						if ( strpos( $whitelist_extraction_result, ' ' ) ) {
							// append the (now separated) terms and the whitelist match(es) to the terms array and essentially short circuit this pass
							$terms = array_merge( $terms, $whitelist_matches, explode( ' ', $whitelist_extraction_result ) );
						} else {
							// append the term and the whitelist match(es) to the terms array and essentially short circuit this pass
							$terms = array_merge( $terms, $whitelist_matches, explode( ' ', $whitelist_extraction_result ) );
						}
					} else {
						// it was an exact match to a pattern in the whitelist, so add the term(s) as-is
						if ( 1 == count( $whitelist_matches ) ) {
							// it was a single match, add as-is to what are considered valid terms
							$validTerms[ $key ] = sanitize_text_field( trim( $whitelist_matches[0] ) );
						} else {
							// append all the matches to this array; they should eventually match exactly
							$terms = array_merge( $terms, $whitelist_matches );
						}
					}
				} else {
					// no whitelist match
					$term = $this->clean_term_string( $term );
					if ( strpos( $term, ' ' ) ) {
						// append the new broken down terms
						$terms = array_merge( $terms, explode( ' ', $term ) );
					} else {
						// proceed
						$excludeCommon = apply_filters( 'searchwp_exclude_common', true );
						if ( ! is_bool( $excludeCommon ) ) {
							$excludeCommon = true;
						}
						$common_words_for_engine = apply_filters( "searchwp_common_words_{$engine}", $this->common );
						if ( ( $excludeCommon && ! in_array( $term, $common_words_for_engine ) ) || ! $excludeCommon ) {
							$minLength = absint( apply_filters( 'searchwp_minimum_word_length', 3 ) );
							if ( $minLength <= strlen( $term ) ) {
								$validTerms[ $key ] = sanitize_text_field( trim( $term ) );
							} else {
								$this->register_search_query_modification( 'min_word_length' );
							}
						} else {
							$this->register_search_query_modification( 'common_word' );
						}
					}
				}
			}
		}

		// after removing punctuation we might have some empty keys
		$validTerms = array_filter( $validTerms, 'strlen' );

		// we also might have duplicates
		$validTerms = array_values( array_unique( $validTerms ) );

		return $validTerms;
	}

	/**
	 * Determine whether to short circuit SearchWP by circumstance
	 *
	 * @sine 2.6.1
	 * @return bool whether to short circuit
	 */
	function maybe_short_circuit() {
		global $wp_query;

		if ( empty( $wp_query ) ) {
			do_action( 'searchwp_log', 'Short circuiting [1]' );
			return true;
		}

		// If there are no initial settings, we can short circuit
		$initial_settings = searchwp_get_setting( 'initial_settings' );
		if ( empty( $initial_settings ) ) {
			do_action( 'searchwp_log', 'Short circuiting [2]' );
			return true;
		}

		// for native searches SearchWP should short-circuit on empty searches so as to match WP default behavior
		// (SearchWP will return no results on empty searches, WordPress returns everything)
		$return = is_search()
			&& $this->isMainQuery
			&& (
				isset( $_REQUEST['s'] )
				&& 0 === strlen( trim( urldecode( $_REQUEST['s'] ) ) )
			);

		if ( $return ) {
			do_action( 'searchwp_log', 'Short circuiting [3]' );
		}

		return $return;
	}


	/**
	 * Prevent WordPress from performing it's own search database call
	 *
	 * @param $query
	 *
	 * @return bool|string
	 * @since 1.1.2
	 */
	function maybe_cancel_wp_query( $sql, $query ) {
		global $wpdb, $wp_query;

		if ( empty( $query ) || empty( $wp_query ) ) {
			return $sql;
		}

		$proceedIfInAdmin = apply_filters( 'searchwp_in_admin', false );
		$overridden       = apply_filters( 'searchwp_force_wp_query', false );
		$shortCircuit     = apply_filters( 'searchwp_short_circuit', $this->maybe_short_circuit(), $this );

		if (
			$this->force_run
			|| (
				! $shortCircuit
				&& ! $overridden
				&& ! (
					is_admin()
					&& ! $proceedIfInAdmin
				)
				&& ! is_feed()
				&& is_search()
				&& $query->is_main_query()
			)
		) {
			// prevent the original search query from running with something that has the least impact
			$sql = "SELECT * FROM $wpdb->posts WHERE 1=0";
			do_action( 'searchwp_log', 'maybe_cancel_wp_query() canceled the query ' );
		}

		return $sql;
	}


	/**
	 * Conditionally trigger Admin Bar entry if SearchWP ran
	 *
	 * @since 2.5
	 */
	function admin_bar_entry_for_search() {
		if ( is_admin_bar_showing() && $this->ran ) {
			$this->admin_bar_search_results_assets(); // already in wp_footer so just fire it directly
			add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar_search_results' ), 999 );
		}
	}


	/**
	 * The assets for the Admin Bar entry
	 *
	 * @since 2.5
	 */
	function admin_bar_search_results_assets() {
		?>
		<style type="text/css">
			#wpadminbar .searchwp-admin-bar-warning {
				background-color:#b60 !important;
				color:#fff !important;
			}
		</style>
	<?php
	}


	/**
	 * Output an Admin Bar entry on search results pages that can be used
	 * for many things such as notification of search query modification
	 * that devs may not realize is happening
	 *
	 * @since 2.5
	 */
	function admin_bar_search_results() {
		global $wp_admin_bar;

		if ( ! apply_filters( 'searchwp_admin_bar', true ) ) {
			return;
		}

		// as of now we're only going to show an Admin Bar entry if there
		// were search query modifications, to help call out things like
		// minimum word length and common words in a more effective way

		if ( empty( $this->search_query_mods ) ) {
			return;
		}

		$args = array(
			'id'     => 'searchwp-admin-bar-search-results',
			'title'  => 'SearchWP',
		);

		// conditional is redundant but put in place in case non-warning entries are added
		if ( ! empty( $this->search_query_mods ) ) {
			$args['meta'] = array( 'class' => 'searchwp-admin-bar-warning' );
		}

		if ( method_exists( $wp_admin_bar, 'add_menu' ) ) {
			$wp_admin_bar->add_menu( $args );

			if ( ! empty( $this->search_query_mods ) ) {
				foreach ( $this->search_query_mods as $key => $search_query_modification ) {

					// default args
					$sub_menu_args = array(
						'parent' => 'searchwp-admin-bar-search-results',
						'id'     => esc_attr( $key ),
					);

					// add in the search mod args (defined in register_search_query_modification())
					$sub_menu_args = array_merge( $sub_menu_args, $search_query_modification );

					$wp_admin_bar->add_menu( $sub_menu_args );
				}
			}
		}

	}


	/**
	 * Keeps a running log of any modifications applied to a search query for use
	 * in the Admin Bar entry (call it out to developers)
	 *
	 * @since 2.5
	 * @param $modification
	 */
	private function register_search_query_modification( $modification ) {
		$acceptable_mods = array(
			'min_word_length' => array(
				'title' => __( 'Minimum word length triggered', 'searchwp' ),
				'href' => 'https://searchwp.com/?p=6598',
				'meta' => array(
					'class' => 'searchwp-admin-bar-warning',
				),
			),
			'common_word' => array(
				'title' => __( 'Common word (stopword) removed', 'searchwp' ),
				'href' => 'https://searchwp.com/?p=6608',
				'meta' => array(
					'class' => 'searchwp-admin-bar-warning',
				),
			),
			'max_terms' => array(
				'title' => __( 'Search query length too long', 'searchwp' ),
				'href' => 'https://searchwp.com/?p=6664',
				'meta' => array(
					'class' => 'searchwp-admin-bar-warning',
				),
			),
		);

		if ( ! array_key_exists( $modification, $acceptable_mods ) ) {
			return;
		}

		// 'enqueue' this modification for output in the Admin Bar entry
		if ( ! isset( $this->search_query_mods[ $modification ] ) ) {
			$this->search_query_mods[ $modification ] = $acceptable_mods[ $modification ];
		}

		do_action( 'searchwp_log', 'Search query modification: ' . $acceptable_mods[ $modification ]['title'] . ' ' . $acceptable_mods[ $modification ]['href'] );
	}

	/**
	 * Callback for Media Grid Admin searches
	 *
	 * @since 2.8
	 *
	 * @param $args
	 *
	 * @return array
	 */
	function maybe_admin_media_search( $args ) {

		$in_admin = apply_filters( 'searchwp_in_admin', false );

		if ( empty( $in_admin ) ) {
			return $args;
		}

		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error();
		}

		if ( empty( $args['s'] ) ) {
			return $args;
		}

		$query_args = array(
			's'             => sanitize_text_field( $args['s'] ),
			'post_type'     => 'attachment',
			'fields'        => 'ids',
		);

		if ( ! empty( $args['posts_per_page'] ) ) {
			$query_args['posts_per_page'] = absint( $args['posts_per_page'] );
		}

		if ( ! empty( $args['paged'] ) ) {
			$query_args['page'] = absint( $args['paged'] );
		}

		$search_results = new SWP_Query( $query_args );

		$args = array(
			'post__in'      => $search_results->posts,
			'orderby'       => 'post__in',
			'post_type'     => 'attachment',
			'post_status'   => 'inherit',
			's'             => '',
		);

		// Re-implement pagination
		if ( ! empty( $query_args['posts_per_page'] ) ) {
			$args['posts_per_page'] = absint( $query_args['posts_per_page'] );
		}

		if ( ! empty( $query_args['page'] ) ) {
			$args['paged'] = absint( $query_args['paged'] );
		}

		return $args;
	}


	/**
	 * Callback for the_posts filter. Hijacks WordPress searches and returns SearchWP results
	 *
	 * @param $posts array The original posts array from WordPress' query
	 *
	 * @return array The posts in the search results from SearchWP
	 * @uses  SearchWPSearch
	 * @since 1.0
	 */
	function wp_search( $posts ) {
		global $wp_query;

		// On the front end SearchWP searches run before any output so if 'wp' fired we don't want to run anyway
		if ( ! is_admin() && did_action( 'wp' ) ) {
			return $posts;
		}

		// If SearchWP is already active, don't run
		if ( $this->active ) {
			return $posts;
		}

		// Allow developers to NOT use SearchWP if another plugin is using $_GET['s'] for specific functionality
		if ( apply_filters( 'searchwp_short_circuit', $this->maybe_short_circuit(), $this ) ) {
			do_action( 'searchwp_log', 'Short circuiting at this time' );

			return $posts;
		}

		/**
		 * We need to check for the main query here, but the process of acurately checking
		 * for the main query has proven to be FULL of edge cases and that is why this is
		 * ridiculous.
		 */
		$do_we_run = false;

		$wp_query_is_search = is_search();

		if ( empty( $wp_query_is_search ) ) {
			$wp_query_is_search = ! empty( $wp_query->is_search ) ? true : false;
		}

		if ( ( $wp_query_is_search || is_admin() && ! empty( $_REQUEST['s'] ) ) && // What's up, admin?
			( (
				$this->isMainQuery // This is _usually_ correct
				|| ( ! $this->isMainQuery && is_main_query() ) // but sometimes it's not!
			)
			|| $this->force_run )
		) {
			$do_we_run = true;
		}

		// Short circuit if it's not the main query
		if ( ! $do_we_run ) {
			return $posts;
		}

		// TODO: This nested conditional nest has become crazy
		if ( ! $this->force_run ) {
			// make sure we're not in the admin, that we are searching, that it is the main query, and that SearchWP is not active
			$proceedIfInAdmin = apply_filters( 'searchwp_in_admin', false );
			if ( is_admin() && ! $proceedIfInAdmin ) {
				return $posts;
			} elseif ( is_admin() ) {
				// we're going to reset as false to ensure that we have an applicable environment (e.g. no searching Users)
				$proceedIfInAdmin = false;

				// hijack the search engine settings to limit to the current post type when searching post types in the admin
				if ( isset( $this->settings['engines'] ) && is_array( $this->settings['engines'] ) ) {
					// find out what screen we're on
					if ( class_exists( 'WP_Screen' ) && function_exists( 'get_current_screen') ) {
						$current_screen = get_current_screen();
						if ( $current_screen instanceof WP_Screen ) {
							if ( isset( $current_screen->id ) ) {
								if ( 'upload' == $current_screen->id ) {
									// we want to search Media only
									$limit_results_to_post_type = 'attachment';
								} elseif ( isset( $current_screen->post_type ) ) {
									// we want to limit to the current post type
									$limit_results_to_post_type = sanitize_text_field( $current_screen->post_type );
								}
							}
							if ( isset( $limit_results_to_post_type ) ) {
								// we have a valid post type and we're on a valid screen, so let's proceed
								$proceedIfInAdmin = true;

								// for this search, disable all post types except the one we're viewing so we don't cross-pollinate
								//    (e.g. see Pages when searching in Posts because that was enabled in our search engine config)
								foreach ( $this->settings['engines'] as $engine_name => $engine_settings ) {
									foreach ( $engine_settings as $engine_settings_post_type => $engine_settings_post_type_settings ) {
										if ( $limit_results_to_post_type == $engine_settings_post_type && isset( $this->settings['engines'][ $engine_name ][ $engine_settings_post_type ] ) ) {
											if ( isset( $this->settings['engines'][ $engine_name ][ $engine_settings_post_type ]['enabled'] ) && false === $this->settings['engines'][ $engine_name ][ $engine_settings_post_type ]['enabled'] ) {
												$this->settings['engines'][ $engine_name ][ $engine_settings_post_type ]['enabled'] = true;
											}
										} else {
											if ( isset( $this->settings['engines'][ $engine_name ][ $engine_settings_post_type ]['enabled'] ) && true === $this->settings['engines'][ $engine_name ][ $engine_settings_post_type ]['enabled'] ) {
												$this->settings['engines'][ $engine_name ][ $engine_settings_post_type ]['enabled'] = false;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		do_action( 'searchwp_log', 'wp_search()' );

		// a search is currently taking place, let's provide some wicked better results
		$this->active = true;
		$this->ran = true;
		$wp_query_paged = get_query_var( 'paged' );
		$wpPaged = ( intval( $wp_query_paged ) > 0 ) ? intval( $wp_query_paged ) : 1;
		do_action( 'searchwp_log', '$wpPaged = ' . $wpPaged );

		// at the very least, our terms are the search query
		$wp_query_s = get_query_var( 's' );

		if ( empty( $wp_query_s ) ) {
			$wp_query_s = isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '';
		}

		$this->original_query = $wp_query_s;
		$terms_trimmed = trim( $wp_query_s );
		$terms = stripslashes( $terms_trimmed );
		do_action( 'searchwp_log', '$terms = ' . var_export( $terms, true ) );

		// facilitate filtering the actual terms
		$terms = apply_filters( 'searchwp_terms', $terms, 'default' );
		do_action( 'searchwp_log', '$terms after searchwp_terms = ' . var_export( $terms, true ) );

		// we always work with lowercase
		$terms = function_exists( 'mb_strtolower' ) ? mb_strtolower( $terms, 'UTF-8' ) : strtolower( $terms );

		// handle sanitization
		$sanitizeTerms = apply_filters( 'searchwp_sanitize_terms', true, 'default' );
		if ( ! is_bool( $sanitizeTerms ) ) {
			$sanitizeTerms = true;
		}

		// whitelist search terms
		$pre_whitelist_terms = is_array( $terms ) ? implode( ' ', $terms ) : ' ' . $terms . ' ';
		$whitelisted_terms = $this->extract_terms_using_pattern_whitelist( $pre_whitelist_terms, false );

		if ( apply_filters( 'searchwp_exclusive_regex_matches', false ) && ! empty( $whitelisted_terms ) ) {
			$terms = $this->process_exclusive_regex_matches( $terms, $whitelisted_terms );
		}

		// if we should still sanitize our terms, do it
		if ( $sanitizeTerms ) {
			$terms = $this->sanitize_terms( $terms );
		}

		if ( is_array( $whitelisted_terms ) ) {
			$whitelisted_terms = array_filter( array_map( 'trim', $whitelisted_terms ), 'strlen' );
		}

		if ( is_array( $terms ) ) {
			$terms = array_filter( array_map( 'trim', $terms ), 'strlen' );
			$terms = array_unique( array_merge( $terms, $whitelisted_terms ) );
		} else {
			$terms .= ' ' . implode( ' ', $whitelisted_terms );
		}

		do_action( 'searchwp_log', '$terms after sanitization = ' . var_export( $terms, true ) );

		// determine the order from WP_Query
		$wp_query_order = get_query_var( 'order' );

		// Sometimes plugins or code will switch this to ASC but by default we want
		// results sorted DESC by relevancy so we're going to assume that
		$order = 'DESC';
		if ( apply_filters( 'searchwp_force_wpquery_order', false ) ) {
			$order = ( strtoupper( $wp_query_order ) == 'DESC' ) ? 'DESC' : 'ASC';
		}
		do_action( 'searchwp_log', '$order = ' . $order );

		// make sure the search isn't overflowing with terms
		$maxSearchTerms = intval( apply_filters( 'searchwp_max_search_terms', 6, 'default' ) );
		do_action( 'searchwp_log', '$maxSearchTerms = ' . $maxSearchTerms );

		if ( count( $terms ) > $maxSearchTerms ) {
			$terms = array_slice( $terms, 0, $maxSearchTerms );

			// need to tell $wp_query that we hijacked this
			// EDIT: no we don't — we have an Admin Bar entry telling the dev it was changed
			// $wp_query->query['s'] = $wp_query->query_vars['s'] = sanitize_text_field( implode( ' ', $terms ) );

			$this->register_search_query_modification( 'max_terms' );

			do_action( 'searchwp_log', 'Breached max terms count, $terms = ' . var_export( $terms, true ) );
		}

		if ( ! empty( $terms ) ) {

			// get posts_per_page and offset from original $wp_query
			if ( isset( $wp_query->query_vars['posts_per_page'] ) && ! empty( $wp_query->query_vars['posts_per_page'] ) ) {
				$posts_per_page = $wp_query->query_vars['posts_per_page'];
			} else {
				// fall back to the site option
				$posts_per_page = get_option( 'posts_per_page' );
			}

			// accommodate the offset if applicable
			$offset = 0;
			if ( isset( $wp_query->query_vars['offset'] ) && ! empty( $wp_query->query_vars['offset'] ) ) {
				$offset = $wp_query->query_vars['offset'];
			}

			$args = array(
				'terms'             => $terms,
				'page'              => $wpPaged,
				'order'             => $order,
				'posts_per_page'    => apply_filters( 'searchwp_posts_per_page', intval( $posts_per_page ), 'default', $terms, $wpPaged ),
				'offset'            => apply_filters( 'searchwp_query_offset', intval( $offset ), 'default', $terms, $wpPaged ),
			);

			// perform the search
			$profiler = array( 'before' => microtime() );
			$searchwp = new SearchWPSearch( $args );
			$profiler['after'] = microtime();

			$this->active           = false;
			$this->isMainQuery      = false;
			$this->results_weights  = $searchwp->results_weights;

			// we need to tell WP Query about everything that's different as per these better results
			$wp_query->found_posts      = absint( $searchwp->foundPosts );
			$wp_query->max_num_pages    = absint( $searchwp->maxNumPages );

			do_action( 'searchwp_log', 'found_posts = ' . $wp_query->found_posts );
			do_action( 'searchwp_log', 'max_num_pages = ' . $wp_query->max_num_pages );

			// store diagnostics for debugging
			$this->diagnostics[] = array(
				'engine'        => 'default',
				'terms'         => $args['terms'],
				'found_posts'   => $searchwp->foundPosts,
				'posts'         => $searchwp->posts,
				'profiler'      => $profiler,
				'args'          => $args,
			);

			$posts = apply_filters( 'searchwp_results', $searchwp->posts, array(
				'terms'         => $terms,
				'page'          => $wpPaged,
				'order'         => $order,
				'foundPosts'    => $wp_query->found_posts,
				'maxNumPages'   => $wp_query->max_num_pages,
				'engine'        => 'default',
			) );

			$wp_query->posts = $posts;
			$wp_query->post_count = $wp_query->found_posts;

			return $posts;
		} elseif ( is_search() ) {
			// there were no valid search terms so we need to reset everything and return no results
			$this->active            = false;
			$this->isMainQuery       = false;
			$wp_query->found_posts   = 0;
			$wp_query->max_num_pages = 0;
			$posts                   = array();
		}

		return $posts;

	}

	/**
	 * Strip whitelist terms from content so as to make them truly exclusive
	 *
	 * @since 2.6.2
	 *
	 * @param $content
	 * @param $whitelisted_terms
	 *
	 * @return mixed|string
	 */
	function process_exclusive_regex_matches( $content, $whitelisted_terms ) {

		if ( empty( $whitelisted_terms ) ) {
			return $content;
		}

		// add the buffer the entire string so we can whole-word replace
		$content = ' ' . $content . ' ';

		// also need to buffer the whitelisted terms to prevent replacement overrun
		foreach ( (array) $whitelisted_terms as $key => $val ) {
			$whitelisted_terms[ $key ] = ' ' . $val . ' ';
		}

		// remove the matches
		$content = str_ireplace( $whitelisted_terms, ' ', $content );

		// clean up the double space flag we used
		$content = str_replace( '  ', ' ', $content );
		$content = trim( $content );

		return $content;
	}


	/**
	 * Callback for admin_menu action; adds SearchWP link to Settings menu in the WordPress admin
	 *
	 * @since 1.0
	 */
	function admin_menu() {
		$options_page = add_options_page( $this->pluginName, __( $this->pluginName, 'searchwp' ), $this->settings_cap, $this->textDomain, array( $this, 'options_page' ) );
		add_dashboard_page( __( 'Search Statistics', 'searchwp' ), __( 'Search Stats', 'searchwp' ), apply_filters( 'searchwp_statistics_cap', 'publish_posts' ), $this->textDomain . '-stats', array( $this, 'stats_page' ) );

		$show_legacy_ui = apply_filters( 'searchwp_legacy_settings_ui', false );

		if ( ! empty( $show_legacy_ui ) ) {
			add_action( 'load-' . $options_page, array( $this, 'get_indexer_communication_result' ) );
		}
	}


	/**
	 * Callback for admin_enqueue_scripts. Enqueues our assets.
	 *
	 * @param $hook string
	 *
	 * @since 1.0
	 */
	function assets( $hook ) {
		$base_url = trailingslashit( $this->url );

		wp_register_style( 'swp_stats_css', $base_url . 'assets/css/searchwp-stats.css', false, $this->version );

		// chartist
		wp_register_style( 'chartist', $base_url . 'assets/vendor/chartist/chartist.min.css', false, '0.1.15' );
		wp_register_script( 'chartist', $base_url . 'assets/vendor/chartist/chartist.min.js', array(), '0.1.15' );

		if ( 'dashboard_page_searchwp-stats' == $hook ) {
			wp_enqueue_script( 'chartist' );
			wp_enqueue_style( 'chartist' );
			wp_enqueue_style( 'swp_stats_css' );
		}

		if ( 'post.php' == $hook ) {
			wp_enqueue_script( 'heartbeat' );
			add_action( 'admin_print_footer_scripts', array( $this, 'heartbeat_last_indexed' ), 20 );
		}
	}


	/**
	 * Utilize the WordPress Heartbeat API to dynamically update the Last Indexed time after updating posts
	 */
	function heartbeat_last_indexed() {
		global $post;
		?>
		<script>
			(function($){
				// hook into the heartbeat-send
				$(document).on('heartbeat-send', function(e, data) {
					data['searchwp_heartbeat_action'] = 'last_indexed';
					data['searchwp_heartbeat_object'] = <?php echo isset( $post->ID ) ? absint( $post->ID ) : 0; ?>;
				});

				// listen for the custom event "heartbeat-tick" on $(document).
				$(document).on( 'heartbeat-tick', function(e, data) {

					// if our data isn't present, short circuit
					if ( ! data['searchwp_last_indexed'] ) {
						return;
					}

					// update the last indexed time
					$('#wp-admin-bar-searchwp_last_indexed > div').text( "<?php _e( 'Last indexed', 'searchwp' ); ?> " + data['searchwp_last_indexed'] );

				});
			}(jQuery));
		</script>
		<?php
	}


	/**
	 * Callback for the WordPress Heartbeat API. Currently used to dynamically update the Last Index time after editing posts.
	 *
	 * @param $response
	 * @param $data
	 *
	 * @return mixed
	 */
	function heartbeat_received( $response, $data ) {
		// maybe retrieve our last indexed time
		if ( isset( $data['searchwp_heartbeat_action'] ) && 'last_indexed' == $data['searchwp_heartbeat_action'] ) {

			$object_id = absint( $data['searchwp_heartbeat_object'] );

			// Send back the number of complete payments
			$response['searchwp_last_indexed'] = $this->get_last_indexed_time( $object_id, true );

		}
		return $response;
	}


	/**
	 * Outputs the stats page and all stats
	 *
	 * @since 1.0
	 */
	function stats_page() {
		include( dirname( __FILE__ ) . '/admin/stats.php' );
	}


	/**
	 * Truncates log table
	 *
	 * @since 1.6.5
	 */
	function reset_stats() {
		global $wpdb;

		do_action( 'searchwp_log', 'reset_stats()' );

		$prefix = $wpdb->prefix . SEARCHWP_DBPREFIX;

		// truncate the log table
		foreach ( $this->tables as $table ) {
			if ( 'log' == $table['table'] ) {
				$tableName = $wpdb->prepare( '%s', $prefix . $table['table'] );
				if ( "'" == substr( $tableName, 0, 1 ) && "'" == substr( $tableName, strlen( $tableName ) - 1 ) ) {
					$tableName = '`' . substr( $tableName, 1, strlen( $tableName ) - 2 ) . '`';
				}
				$wpdb->query( "TRUNCATE TABLE {$tableName}" );
			}
		}

		$this->reset_dashboard_stats_transients();
		searchwp_set_setting( 'ignored_queries', array() );
	}

	/**
	 * Delete the transient that stores Dashboard stats
	 * @since 2.5.5
	 * @return void
	 */
	private function reset_dashboard_stats_transients() {
		// also remove the transients for Dashboard Widget
		foreach ( $this->settings['engines'] as $engine => $engineSettings ) {
			$transient_today_key = 'swp_stats_' . md5( 'searchwp_widget_stats_today_' . $engine );
			$transient_month_key = 'swp_stats_' . md5( 'searchwp_widget_stats_month_' . $engine );

			delete_transient( $transient_today_key );
			delete_transient( $transient_month_key );
		}
	}


	/**
	 * Completely truncates all index tables, removes all index-related options
	 *
	 * @since 1.0
	 */
	function purge_index() {
		global $wpdb;

		do_action( 'searchwp_log', 'purge_index()' );

		$prefix = $wpdb->prefix . SEARCHWP_DBPREFIX;

		foreach ( $this->tables as $table ) {
			if ( 'log' !== $table['table'] ) {
				$tableName = $wpdb->prepare( '%s', $prefix . $table['table'] );
				if ( "'" == substr( $tableName, 0, 1 ) && "'" == substr( $tableName, strlen( $tableName ) - 1 ) ) {
					$tableName = '`' . substr( $tableName, 1, strlen( $tableName ) - 2 ) . '`';
				}
				$wpdb->query( "TRUNCATE TABLE {$tableName}" );
			}
		}

		// remove all metadata flags
		$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => '_' . SEARCHWP_PREFIX . 'last_index' ) );
		$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => '_' . SEARCHWP_PREFIX . 'attempts' ) );
		$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => '_' . SEARCHWP_PREFIX . 'skip' ) );
		$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => '_' . SEARCHWP_PREFIX . 'skip_doc_processing' ) );
		$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => '_' . SEARCHWP_PREFIX . 'review' ) );

		// Don't use this filter anymore, use searchwp_purge_document_content
		$_legacy_purge_pdf_content = apply_filters( 'searchwp_purge_pdf_content', false );

		if ( apply_filters( 'searchwp_purge_document_content', $_legacy_purge_pdf_content, array() ) ) {
			$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => SEARCHWP_PREFIX . 'content' ) );
		}
		$wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => SEARCHWP_PREFIX . 'pdf_metadata' ) );

		// kill all the options related to the index
		searchwp_wake_up_indexer();
		searchwp_set_setting( 'initial_index_built', false );
		searchwp_set_setting( 'notices', array() );
		searchwp_set_setting( 'valid_db_environment', false );
		searchwp_delete_option( 'indexnonce' );

		delete_option( 'searchwp_transient' );
		delete_option( 'swppurge_transient' );

		// reset the counts
		if ( class_exists( 'SearchWPIndexer' ) ) {
			$indexer = new SearchWPIndexer();
			$indexer->update_running_counts();
		}
	}


	/**
	 * Output the markup for posts that failed to make it into the index
	 *
	 * @since 1.3
	 */
	function show_erroneous_posts() {

		$args = array(
			'posts_per_page'        => -1,
			'post_type'             => 'any',
			'post_status'           => array( 'publish', 'inherit' ),
			'fields'                => 'ids',
			'suppress_filters'      => true,
			'meta_query'    => array(
				'relation'          => 'AND',
				array(
					'key'           => '_' . SEARCHWP_PREFIX . 'last_index',
					'value'         => '', // http://core.trac.wordpress.org/ticket/23268
					'compare'       => 'NOT EXISTS',
					'type'          => 'NUMERIC',
				),
				array( // only want media that hasn't failed indexing multiple times
					'key'           => '_' . SEARCHWP_PREFIX . 'skip',
					'compare'       => 'EXISTS',
					'type'          => 'BINARY',
				)
			)
		);

		if ( ( isset( $_GET['action'] ) && 'reintroduce' == strtolower( $_GET['action'] ) ) && isset( $_GET['swpid'] ) ) {
			$erroneous_post_id = absint( $_GET['swpid'] );
			if ( isset( $_GET['swperroneous'] ) && wp_verify_nonce( $_GET['swperroneous'], 'swperroneouspost' . $erroneous_post_id ) ) {
				// remove the flags preventing the post from being indexed
				$this->purge_post( $erroneous_post_id );
				$this->trigger_index();
			}
		} else {
			if ( isset( $_GET['action'] ) && 'reintroduce_all' == strtolower( $_GET['action'] ) ) {
				if ( isset( $_GET['swperroneouspurge'] ) && wp_verify_nonce( $_GET['swperroneouspurge'], 'swperroneouspurge' ) ) {
					// grab all erroneous posts
					$erroneous_posts = get_posts( $args );
					if ( ! empty( $erroneous_posts ) ) {
						foreach ( $erroneous_posts as $erroneous_post_id ) {
							$this->purge_post( absint( $erroneous_post_id ) );
						}
						$this->trigger_index();
					}
				}
			}
		}

		$erroneousPosts = get_posts( $args );

		// WordPress 4.2 introduced a move to utf8mb4, SearchWP 2.5.7 followed suit
		// but if the conversion from utf8 to utf8mb4 failed, it was logged
		// e.g. if emoji were being used on a site where the upgrade failed, the
		// post would fail to index, so we can call that out right here
		$utf8mb4_failed_upgrade = false;
		if ( searchwp_get_option( 'utf8mb4_upgrade_failed' ) ) {
			$utf8mb4_failed_upgrade = true;
		}

		?>
		<style type="text/css">
			#searchwp-index-errors-notice { display:none; }
		</style>
		<div class="wrap">
			<div id="icon-searchwp" class="icon32">
				<img src="<?php echo esc_url( trailingslashit( $this->url ) ); ?>assets/images/searchwp@2x.png" alt="SearchWP" width="21" height="32" />
			</div>
			<h2><?php echo esc_html( $this->pluginName ) . ' ' . __( 'Outstanding Index Issues' ); ?></h2>
			<?php if ( empty( $erroneousPosts ) ) : ?>
				<p><?php _e( 'Nothing is currently excluded from the indexer.', 'searchwp' ); ?></p>
			<?php else : ?>
				<?php if ( $utf8mb4_failed_upgrade ) : ?>
					<div class="error">
						<p><?php _e( 'SearchWP was unable to fully implement <code>utf8mb4</code> (Emoji) support which may prevent indexing some content. Please open a support ticket for more assistance.', 'searchwp' ); ?></p>
					</div>
				<?php endif; ?>
				<?php
					$nonce_main = wp_create_nonce( 'swperroneous' );
					$link_url = admin_url( 'options-general.php?page=searchwp' ) . '&nonce=' . esc_attr( $nonce_main ) . '&action=reintroduce_all&swperroneouspurge=' . esc_attr( wp_create_nonce( 'swperroneouspurge' ) );
				?>
				<p><?php _e( 'SearchWP was unable to index the following content, and it is actively being excluded from subsequent index runs.', 'searchwp' ); ?> <a href="<?php echo esc_url( $link_url ); ?>" class="button"><?php _e( 'Reintroduce All' ,'searchwp' ); ?></a></p>
				<table class="swp-table swp-erroneous-posts">
					<colgroup>
						<col id="swp-erroneous-posts-titles" />
						<col id="swp-erroneous-posts-action" />
					</colgroup>
					<thead>
					<tr>
						<th><?php _e( 'Title', 'searchwp' ); ?></th>
						<th><?php _e( 'Reintroduce to indexer', 'searchwp' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ( $erroneousPosts as $erroneousPost ) :
						if ( ! isset( $_GET['swpid'] ) || ( isset( $_GET['swpid'] ) && ( absint( $_GET['swpid'] ) != $erroneousPost ) ) ) :
							$post_obj = get_post( $erroneousPost ); ?>
							<tr>
								<td><a href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $erroneousPost ) . '&action=edit' ) ); ?>">
										<?php echo esc_html( get_the_title( $erroneousPost ) ); ?></a>
									<?php if ( 'application/pdf' == $post_obj->post_mime_type ) : ?>
										<br /><span class="description"><?php _e( 'Manually populate PDF content if reintroduction fails', 'searchwp' ); ?></span>
									<?php endif; ?>
								</td>
								<?php
									$link_url = admin_url( 'options-general.php?page=searchwp' ) . '&nonce=' . esc_attr( $nonce_main ) . '&action=reintroduce&swpid=' . absint( $erroneousPost ) . '&swperroneous=' . esc_attr( wp_create_nonce( 'swperroneouspost' . absint( $erroneousPost ) ) );
								?>
								<td><a href="<?php echo esc_url( $link_url ); ?>"><?php _e( 'Reintroduce', 'searchwp' ); ?></a></td>
							</tr>
						<?php endif; endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
			<p>
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=searchwp' ) ); ?>"><?php _e( 'Back to SearchWP Settings', 'searchwp' ); ?></a>
			</p>
		</div>
	<?php
	}


	/**
	 * Output the markup for the advanced settings page
	 *
	 * @since 1.0
	 * @deprecated 2.6
	 */
	function advanced_settings() {}


	/**
	 * Force an indexer hash match and trigger an index update
	 *
	 * @since 2.5
	 *
	 * @return string Indexer hash
	 */
	private function trigger_forced_indexer_chunk() {
		$hash = sprintf( '%.22F', microtime( true ) ); // inspired by $doing_wp_cron
		update_option( 'searchwp_transient', $hash, 'no' );

		// trigger an index of this chunk
		$indexer = new SearchWPIndexer( $hash );
		$indexer->update_running_counts();

		return $indexer->hash;
	}


	/**
	 * AJAX callback for alternate indexer request
	 *
	 * @since 2.5
	 */
	function handle_alternate_indexer_request() {

		check_ajax_referer( 'searchwp_alternate_indexer', 'nonce' );

		if ( ! current_user_can( $this->settings_cap ) ) {
			wp_die( esc_html__( 'Invalid request', 'searchwp' ) );
		}

		// If the index is dirty, we need to purge it first
		$index_dirty = searchwp_get_setting( 'index_dirty' );
		if ( ! empty( $index_dirty ) ) {
			searchwp_set_setting( 'index_dirty', false );
			$this->purge_index();
		}

		$next_hash = $this->trigger_forced_indexer_chunk();

		wp_cache_flush();

		die( json_encode( array( 'hash' => $next_hash ) ) );
	}


	/**
	 * Output the HTML for the alternate indexer
	 *
	 * @since 2.5
	 */
	function alternate_indexer_view() {

		if ( ! current_user_can( $this->settings_cap ) ) {
			wp_die( __( 'Invalid request', 'searchwp' ) );
		}

		$progress = searchwp_get_option( 'progress' );

		$form_action_url_params = array(
			'page' => $this->textDomain,
		);

		$form_action_url = add_query_arg( $form_action_url_params, admin_url( 'options-general.php' ) );
		?>
		<?php if ( isset( $_REQUEST['swpnonce'] ) ) : ?>
			<div class="wrap">
				<h3><?php _e( 'SearchWP Alternate Indexer', 'searchwp' ); ?></h3>
				<?php $nonce = wp_create_nonce( 'searchwp_alternate_indexer' ); ?>
				<p><?php _e( 'Current progress:', 'searchwp' ); ?> <?php echo esc_html( $progress ); ?>%</p>
				<p class="descripttion"><?php _e( 'If the percentage is not increasing it <strong>does not necessarily mean</strong> there is a problem. SearchWP takes multiple passes when indexing, please allow adequate time for the indexer to run.', 'searchwp' ); ?></p>
				<?php if ( absint( $progress ) < 100 ) : ?>
					<p>
						<?php _e( 'Triggering next index chunk, please wait...', 'searchwp' ); ?>
						<span class="spinner is-active" style="display:inline-block;float:none;position:relative;top:-4px;"></span>
					</p>
					<script type="text/javascript">
						jQuery(document).ready(function($){
							// noinspection JSUnresolvedVariable ajaxurl
							$.post(ajaxurl, {
									action: 'searchwp_alternate_indexer_trigger',
									nonce: '<?php echo esc_js( $nonce ); ?>',
									swpnonce: '<?php echo esc_js( $_REQUEST['swpnonce'] ); ?>'
								},
								function (res) {
									if (res.hash) {
										document.location.href = '<?php echo esc_url( $form_action_url ); ?>&swpnonce=' + res.hash;
									} else {
										document.location.href = '<?php echo esc_url( $form_action_url ); ?>';
									}
								}, 'json');
						});
					</script>
				<?php else : ?>
					<p><strong><?php _e( 'Index built!', 'searchwp' ); ?></strong></p>
					<script type="text/javascript">
						document.location.href = '<?php echo esc_url( $form_action_url ); ?>';
					</script>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<?php
			$indexer = new SearchWPIndexer();
			$total = intval( $indexer->count_total_posts() );
			$indexed = intval( $indexer->indexed_count() );

			if ( $total > $indexed ) {
				$num_remaining_posts_to_index = absint( $total - $indexed );
			} else {
				$num_remaining_posts_to_index = 0;
			}

			// if there are no more posts to index, we don't need to output any UI to trigger the index
			if ( empty( $num_remaining_posts_to_index ) ) {
				return;
			}

			$hash = sprintf( '%.22F', microtime( true ) ); // inspired by $doing_wp_cron
			update_option( 'searchwp_transient', $hash, 'no' );
			?>
			<h3><?php _e( 'SearchWP Alternate Indexer', 'searchwp' ); ?></h3>
			<p><?php echo sprintf( __( 'There are <strong>%d</strong> entries left to index. <strong>YOU MUST LEAVE THIS BROWSER WINDOW OPEN</strong> during indexing.', 'searchwp' ), absint( $num_remaining_posts_to_index ) ); ?></p>
			<p class="description"><?php _e( 'This action builds the initial index and only needs to be run once.', 'searchwp' ); ?></p>
			<form method="get" action="<?php echo esc_url( $form_action_url ); ?>">
				<input type="hidden" name="page" value="searchwp" />
				<input type="hidden" name="swpnonce" value="<?php echo esc_attr( $hash ); ?>" />
				<p style="padding-bottom:2em;">
					<button class="button button-primary" type="submit" name="submit"><?php esc_attr_e( 'Build Initial Index', 'searchwp' ); ?></button>
				</p>
			</form>
		<?php endif; ?>
	<?php
	}

	/**
	 * @since 2.6
	 * @return bool
	 */
	function is_using_alternate_indexer() {
		return ! empty( $this->alternate_indexer );
	}

	/**
	 * This functionality needs to be better organized. It is a conglomeration of functionality
	 * that was shoehorned into the settings page display and it can be much better utilized
	 * if broken apart and implemented properly
	 *
	 * TODO: organize this
	 */
	function misc_options_page_pre_hooks() {
		// check to see if we're using the alternate indexer
		if ( $this->alternate_indexer ) {

			// hook in the alternate indexer UI
			add_action( 'searchwp_settings_before\default', array( $this, 'alternate_indexer_view' ) );
			// $this->alternate_indexer_view();

			// is the alternate indexer is in progress?
			if ( isset( $_REQUEST['swpnonce'] ) ) {

				// the settings UI action will never fire because we're going to essentially short-circuit
				// once we've displayed the alternate indexer progress (which we'll do manually now)
				$this->alternate_indexer_view();

				// prevent any more UI from showing up
				return true;
			}
		}

		// check to see if we should show posts that failed indexing
		if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'swperroneous' ) && current_user_can( $this->settings_cap ) ) {
			$this->show_erroneous_posts();
			return true;
		}

		return false;
	}


	/**
	 * Callback for our implementation of add_options_page. Displays our options screen.
	 *
	 * @uses  wpdb
	 * @uses  get_option to get saved SearchWP settings
	 * @since 1.0
	 */
	function options_page() {

		if ( ! current_user_can( apply_filters( 'searchwp_settings_cap', 'manage_options' ) ) ) {
			wp_die( __( 'Invalid request', 'searchwp' ) );
		}

		if ( $this->misc_options_page_pre_hooks() ) {
			return;
		}

		$this->settings_utils->render_header();

		echo '<div class="wrap"><div class="swp-notices swp-group"></div>';

		$this->settings_utils->render_view();
		$this->settings_utils->render_footer();

		echo '</div>';

		// if we're on the main settings page (and not using the alternate indexer) trigger the indexer
		if (
			! $this->alternate_indexer
			&& ! $this->indexing
			&& isset( $_GET['page'] )
			&& 'searchwp' == $_GET['page']
			&& ! isset( $_GET['tab'] )
			&& false == searchwp_get_setting( 'running' ) ) {
			$this->indexing = true;
			$this->trigger_index();
		}

		do_action( 'searchwp_log', 'Shutting down after displaying settings screen' );
		$this->shutdown();
	}

	/**
	 * Export engine configurations as JSON
	 *
	 * @since 2.4.5
	 *
	 * @param $engines string|array Engine(s) to export
	 *
	 * @param bool $encode
	 *
	 * @return mixed|string|void JSON-encoded settings
	 */
	function export_settings( $engines = null, $encode = true ) {
		// default is all engines
		$settings = $this->settings['engines'];

		// single engine
		if ( is_string( $engines ) && array_key_exists( $engines, $this->settings['engines'] ) ) {
			$settings = $this->settings['engines'][ $engines ];
		}

		// array of engines
		if ( is_array( $engines ) ) {
			$settings = array();
			foreach ( $engines as $engine ) {
				if ( is_string( $engine ) && array_key_exists( $engine, $this->settings['engines'] ) ) {
					$settings[ $engine ] = $this->settings['engines'][ $engine ];
				}
			}
		}

		return ! empty( $encode ) ? json_encode( $settings ) : $settings;
	}


	/**
	 * Programmatically set engine configurations
	 *
	 * @since 2.4.5
	 *
	 * @param      $settings_json string JSON-encoded string of engine settings
	 */
	function import_settings( $settings_json ) {

		// back up existing settings before import
		$settings_backups = searchwp_get_option( 'settings_backup' );
		$settings_backups[ current_time( 'timestamp' ) ] = $this->settings;
		searchwp_update_option( 'settings_backup', $settings_backups );

		// parse the import
		$settings_to_import = json_decode( (string) $settings_json );

		if ( false === $settings_to_import ) {
			wp_die( __( 'Invalid settings.', 'searchwp' ) );
		}

		$settings_to_import = $this->object_to_array( $settings_to_import );
		$settings_to_import = $this->validate_settings( array( 'engines' => $settings_to_import ) );
		$settings_to_import = $settings_to_import['engines'];

		foreach ( $this->settings['engines'] as $engine_key => $engine_config ) {
			if ( array_key_exists( $engine_key, $settings_to_import ) ) {
				// overwrite engine config
				$this->settings['engines'][ $engine_key ] = $settings_to_import[ $engine_key ];
				unset( $settings_to_import[ $engine_key ] );
			}
		}

		// if there are any imported engines left over, append them
		if ( count( $settings_to_import ) ) {
			$this->settings['engines'] = array_merge( $this->settings['engines'], $settings_to_import );
		}

		// persist the settings
		update_option( 'searchwp_settings', $this->settings );
	}


	/**
	 * Convert an object into an associative array
	 *
	 * @since 2.4.5
	 *
	 * @param $d
	 *
	 * @return array
	 */
	private function object_to_array( $d ) {
		if ( is_object( $d ) ) {
			$d = get_object_vars( $d );
		}

		if ( is_array( $d ) ) {
			return array_map( array( $this, 'object_to_array' ), $d );
		} else {
			return $d;
		}
	}


	/**
	 * Retrieve Custom Field keys
	 * @since 2.3.1
	 */
	function define_keys() {
		global $wpdb;
		// retrieve custom field keys to include in the Custom Fields weight table select
		/** @noinspection SqlDialectInspection */
		$this->keys = $wpdb->get_col( $wpdb->prepare( "
			SELECT meta_key
			FROM $wpdb->postmeta
			WHERE meta_key != %s
			AND meta_key != %s
			AND meta_key != %s
			AND meta_key != %s
			AND meta_key NOT LIKE %s
			GROUP BY meta_key
		",
			'_' . SEARCHWP_PREFIX . 'indexed',
			'_' . SEARCHWP_PREFIX . 'content',
			'_' . SEARCHWP_PREFIX . 'needs_remote',
			'_' . SEARCHWP_PREFIX . 'skip',
			'_oembed_%'
		) );

		// allow devs to filter this list
		$this->keys = array_unique( apply_filters( 'searchwp_custom_field_keys', $this->keys ) );

		// sort the keys alphabetically
		if ( $this->keys ) {
			natcasesort( $this->keys );
		} else {
			$this->keys = array();
		}
	}


	/**
	 * Register our settings with WordPress
	 *
	 * @uses  add_settings_section as per the WordPress Settings API
	 * @uses  add_settings_field as per the WordPress Settings API
	 * @uses  register_setting as per the WordPress Settings API
	 * @since 1.0
	 */
	function init_settings() {
		add_settings_section(
			SEARCHWP_PREFIX . 'settings',
			'SearchWP Settings',
			array( $this, 'settings_callback' ),
			$this->textDomain
		);

		add_settings_field(
			SEARCHWP_PREFIX . 'settings_field',
			'Settings',
			array( $this, 'settings_field_callback' ),
			$this->textDomain,
			SEARCHWP_PREFIX . 'settings'
		);

		register_setting(
			SEARCHWP_PREFIX . 'settings',
			SEARCHWP_PREFIX . 'settings',
			array( $this, 'validate_settings' )
		);
	}


	/**
	 * Set up WP cron job for maintenance actions
	 *
	 * @since 1.0
	 */
	function schedule_maintenance() {
		if ( ! wp_next_scheduled( 'swp_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'swp_maintenance' );
		}

		if ( ! wp_next_scheduled( 'swp_indexer' ) && ! searchwp_get_setting( 'initial_index_built' ) ) {
			wp_schedule_event( time(), 'swp_frequent', 'swp_indexer' );
		}
	}


	/**
	 * Too keep an eye on the initial index process, we're going to set up a five minute
	 * interval in WP cron
	 *
	 * @param $schedules
	 *
	 * @return mixed
	 * @since 1.0
	 */
	function add_custom_cron_interval( $schedules ) {
		// only add this interval if the initial index has not been completed
		if ( ! isset( $schedules['swp_frequent'] ) && ! searchwp_get_setting( 'initial_index_built' ) ) {
			$schedules['swp_frequent'] = array(
				'interval' => 60 * 30,
				'display'  => __( 'SearchWP Frequent (Every five minutes until initial index is built)' )
			);
		}
		return $schedules;
	}


	/**
	 * Callback to WordPress' hourly cron job
	 *
	 * @since 1.0
	 */
	function do_cron() {
		// if the initial index hasn't been completed, we're going to ping the indexer
		if ( ! searchwp_get_setting( 'initial_index_built' ) ) {
			// fire off a request to the index process
			do_action( 'searchwp_log', 'Request index (cron)' );
			$this->trigger_index();
		}
	}


	/**
	 * Callback from our call to register_setting() in $this->init_settings
	 *
	 * @param $input array The submitted $_POST data
	 *
	 * @return mixed array Validated array of settings
	 * @since 1.0
	 */
	function validate_settings( $input ) {
		$validSettings = $this->settings;
		$validCategories = array( 'engines' );

		// make sure the input is an array
		if ( is_array( $input ) ) {
			// sift through our settings category looking for engine config
			foreach ( $input as $category => $categorySettings ) {
				if ( 'engines' == $category ) {
					// make sure the array key is sanitized
					$sanitizedCategory = sanitize_key( $category );
					$validSettings[ $sanitizedCategory ] = array();
					// only proceed if we have a valid settings category
					if ( in_array( $sanitizedCategory, $validCategories ) ) {
						// we're going to first handle any core settings
						switch ( $sanitizedCategory ) {
							case 'engines':
								foreach ( $categorySettings as $engineName => $engineSettings ) {
									$sanitizedEngineName = empty( $engineSettings['searchwp_engine_label'] ) ? sanitize_key( $engineName ) : str_replace( '-', '_', sanitize_title( $engineSettings['searchwp_engine_label'] ) );

									while ( isset( $validSettings[ $sanitizedCategory ][ $sanitizedEngineName ] ) ) {
										$sanitizedEngineName .= '_copy';
									}

									$validSettings[ $sanitizedCategory ][ $sanitizedEngineName ] = $this->sanitize_engine_settings( $engineSettings );

									if ( ! empty( $engineSettings['searchwp_engine_label'] ) ) {
										$validSettings[ $sanitizedCategory ][ $sanitizedEngineName ]['searchwp_engine_label'] = sanitize_text_field( $engineSettings['searchwp_engine_label'] );
									}
								}
								break;
						}
					}
				}
			}
		}

		return $validSettings;
	}


	/**
	 * Make sure the submitted engine settings match expectations
	 *
	 * @param array $engineSettings
	 *
	 * @return array
	 * @since 1.0
	 */
	function sanitize_engine_settings( $engineSettings = array() ) {
		$validEngineSettings = array();

		if ( is_array( $engineSettings ) ) {

			foreach ( $engineSettings as $postType => $postTypeSettings ) {

				if ( in_array( $postType, $this->postTypes ) ) {

					$validEngineSettings[ $postType ] = array();

					// store a proper 'enabled' setting
					$validEngineSettings[ $postType ]['enabled'] = isset( $postTypeSettings['enabled'] ) && $postTypeSettings['enabled'] ? true : false;

					// store proper weights
					if ( isset( $postTypeSettings['weights'] ) && is_array( $postTypeSettings['weights'] ) ) {

						$validEngineSettings[ $postType ]['weights'] = array();

						foreach ( $postTypeSettings['weights'] as $postTypeWeightKey => $weight ) {
							if ( in_array( $postTypeWeightKey, $this->validTypes ) ) {
								if ( ! is_array( $weight ) ) {
									$weight = strpos( (string) $weight, '.' ) ? floatval( $weight ) : intval( $weight );
									if ( $weight < -1 ) {
										$weight = -1;
									}
									$validEngineSettings[ $postType ]['weights'][ $postTypeWeightKey ] = $weight;
								}
								else {
									// it's either a taxonomy or custom field, comprised of multiple weights
									$validEngineSettings[ $postType ]['weights'][ $postTypeWeightKey ] = array();
									foreach ( $weight as $contentName => $subweight ) { // could just check to see if $contentName is 'tax' or 'cf'...
										if ( ! is_array( $subweight ) ) {
											// taxonomy
											$weightKey = sanitize_text_field( $contentName );
											$subweight = strpos( (string) $subweight, '.' ) ? floatval( $subweight ) : intval( $subweight );
											if ( $subweight < -1 ) {
												$subweight = -1;
											}
											$validEngineSettings[ $postType ]['weights'][ $postTypeWeightKey ][ $weightKey ] = $subweight;
										} else {
											// custom field
											$customFieldFlag = sanitize_text_field( $contentName );
											$weight = strpos( (string) $subweight['weight'], '.' ) ? floatval( $subweight['weight'] ) : intval( $subweight['weight'] );
											if ( $weight < -1 ) {
												$weight = -1;
											}
											if ( isset( $subweight['metakey'] ) && isset( $subweight['weight'] ) ) {
												$validEngineSettings[ $postType ]['weights'][ $postTypeWeightKey ][ $customFieldFlag ] = array(
													'metakey' => sanitize_text_field( $subweight['metakey'] ),
													'weight'  => $weight,
												);
											}
										}
									}
								}
							}
						}
					}

					// dynamically add our taxonomies to valid options array
					$taxonomies = get_object_taxonomies( $postType );
					if ( is_array( $taxonomies ) && count( $taxonomies ) ) {
						foreach ( $taxonomies as $taxonomy ) {
							$taxonomy = get_taxonomy( $taxonomy );
							$this->validOptions[] = 'exclude_' . $taxonomy->name;
							$this->validOptions[] = 'limit_to_' . $taxonomy->name;
						}
					}

					// store proper options
					if ( isset( $postTypeSettings['options'] ) && is_array( $postTypeSettings['options'] ) ) {
						foreach ( $postTypeSettings['options'] as $engineOptionName => $engineOptionValue ) {
							if ( in_array( $engineOptionName, $this->validOptions ) ) {

								switch ( $engineOptionName ) {
									case 'exclude':
									case 'limit_to':
									case 'mimes':
										if ( 'mimes' === $engineOptionName && is_string( $engineOptionValue ) && '0' === trim( $engineOptionValue ) ) {
											// The "All Documents" mime grouping by itself will be a value of zero, which is normally
											// unwanted but in this case it's very important
											$engineOptionValue = '0';
										} else {
											// we want a comma separated string of integers
											$engineOptionValue = $this->get_integer_csv_string_from_string_or_array( $engineOptionValue );
										}
										break;

									case 'attribute_to':
										// this can only be a post ID
										$engineOptionValue = absint( $engineOptionValue );
										break;

									case 'stem':
										// this is a bool (either 1 or 0)
										$engineOptionValue = absint( $engineOptionValue );
										break;

									case 'parent':
										// this is a bool (either 1 or 0)
										$engineOptionValue = absint( $engineOptionValue );
										break;

									default:
										// it's a taxonomy exclusion or limiter
										if (
											'exclude_' == substr( $engineOptionName, 0, 8 )
											|| 'limit_to_' == substr( $engineOptionName, 0, 9 )
										) {
											$engineOptionValue = $this->get_integer_csv_string_from_string_or_array( $engineOptionValue );
										} else {
											$engineOptionValue = 0;
										}
										break;
								}

								// setting value has been enforced as a string
								// which may consist of a bool (1 or 0) or a comma separated list of integers
								// but everything is based on that assumption of it being a string in all cases
								$validEngineSettings[ $postType ]['options'][ $engineOptionName ] = sanitize_text_field( $engineOptionValue );
							}
						}
					}
				}
			}
		}

		return $validEngineSettings;
	}


	/**
	 * Generate a string of comma separated integers from an existing string of
	 * comma separated integers or an array of integers
	 *
	 * @since 2.5.6
	 *
	 * @param string|array $source Array of integers or string of (maybe comma separated) integers
	 *
	 * @return string Comma separated string of integers
	 */
	function get_integer_csv_string_from_string_or_array( $source = '' ) {

		if ( ! is_string( $source ) && ! is_array( $source ) || empty( $source ) ) {
			return '';
		}

		// always want a string
		if ( is_array( $source ) ) {
			$source = implode( ', ', $source );
		}

		// check to see whether the string is already comma separated
		if ( false !== strpos( $source, ',' ) ) {
			$source = explode( ',' , $source );
			$source = array_map( 'trim', $source );
			$source = array_map( 'absint', $source );
			$source = array_unique( $source );
			$source = implode( ', ', $source );
		} else {
			$source = (string) absint( $source );
		}

		return (string) $source;

	}


	/**
	 * Callback from our call to add_settings_section() in $this->init_settings
	 *
	 * @since 1.0
	 */
	function settings_callback() {}


	/**
	 * Callback from our call to add_settings_field() in $this->init_settings. Outputs our (hidden) input field to
	 * accommodate the Settings API
	 *
	 * @since 1.0
	 */
	function settings_field_callback() {
		?><!--suppress HtmlFormInputWithoutLabel -->
		<input type="text" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings" id="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings" value="SearchWP" /><?php
	}


	/**
	 * Purge a post from the index when it is edited
	 *
	 * @param $post_id int The edited post
	 */
	function purge_post_via_edit( $post_id ) {

		// make sure we want to actually purge it
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! isset( $this->purgeQueue[ $post_id ] ) ) {

			// Implement a threshold to prevent redundant purge
			$last_indexed_time = $this->get_last_indexed_time( $post_id, false );
			$last_indexed_seconds_ago = current_time( 'timestamp' ) - $last_indexed_time;

			$last_indexed_threshold = apply_filters( 'searchwp_last_indexed_threshold', 4 );

			if ( absint( $last_indexed_seconds_ago ) > absint( $last_indexed_threshold ) ) {
				$this->purgeQueue[ $post_id ] = $post_id;
				do_action( 'searchwp_log', 'purge_post_via_edit() ' . $post_id );
			} else {
				do_action( 'searchwp_log', 'purge_post_via_edit() cancelled, last edit threshold breached for ' . $post_id );
			}
		} else {
			do_action( 'searchwp_log', 'Prevented duplicate purge purge_post_via_edit() ' . $post_id );
		}
	}

	/**
     *
     * !!!!!!!!!!!!!!!!!! NOT USABLE AT THIS TIME
	 * Purge a post from the index when its metadata is edited
	 *
	 * @param $meta_id
	 * @param $object_id
	 * @param $meta_key
	 * @param $_meta_value
	 *
	 * @return void
	 */
	private function _purge_post_via_update_post_metadata( $meta_id, $object_id, $meta_key, $_meta_value ) {

		if ( true ) {
			return;
		}

		// There are certain meta keys we don't want to consider, else we'd be constantly purging posts
		if ( in_array( $meta_key, array(
			'_edit_lock',
			'_edit_last',
			'_wp_old_slug',
		) ) ) {
			return;
		}

		// Prevent redundancy; this hook is fired for each meta record for a post
		remove_filter( 'update_post_metadata', array( $this, 'purge_post_via_update_post_metadata' ), 999, 5 );

		// Extracted document content is a special case
		if ( apply_filters( 'searchwp_purge_document_content', false, array( 'post_id' => $object_id ) ) ) {
			delete_post_meta( $object_id, SEARCHWP_PREFIX . 'content' );
			delete_post_meta( $object_id, SEARCHWP_PREFIX . 'pdf_metadata' );
		}

		// We need to manually force the purge of this post because many times shutdown is not fired
		if ( ! isset( $this->purgeQueue[ $object_id ] ) ) {
			$this->purgeQueue[ $object_id ] = $object_id;
			do_action( 'searchwp_log', 'purge_post_via_edit_meta() ' . $object_id );
			$this->setup_purge_queue();
		}

		return;
	}


	/**
	 * Removes all record of a post and it's content from the index and triggers a reindex
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	function purge_post( $post_id, $skip_purge_queue = false ) {
		global $wpdb;

		$post_id = absint( $post_id );

		do_action( 'searchwp_log', 'purge_post() ' . $post_id );

		if ( empty( $skip_purge_queue ) ) {
			$this->purgeQueue[ $post_id ] = $post_id;
		}

		// remote it from the index
		$wpdb->delete( $wpdb->prefix . SEARCHWP_DBPREFIX . 'index', array( 'post_id' => $post_id ), array( '%d' ) );
		$wpdb->delete( $wpdb->prefix . SEARCHWP_DBPREFIX . 'tax', array( 'post_id' => $post_id ), array( '%d' ) );
		$wpdb->delete( $wpdb->prefix . SEARCHWP_DBPREFIX . 'cf', array( 'post_id' => $post_id ), array( '%d' ) );

		// remove the postmeta
		delete_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'last_index' );
		delete_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'attempts' );
		delete_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'skip' );
		delete_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'review' );
		delete_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'terms' );

		if ( apply_filters( 'searchwp_purge_document_content', false, array( 'post_id' => $post_id ) ) ) {
			delete_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'content' );
			delete_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'pdf_metadata' );
		}

		return true;
	}


	/**
	 * Callback for actions related to comments changing
	 *
	 * @uses $this->purge_post to clear out the post content from the index and trigger a reindex entirely
	 *
	 * @param $id
	 */
	function purge_post_via_comment( $id ) {
		if ( apply_filters( 'searchwp_index_comments', true ) ) {
			$comment   = get_comment( $id );
			$object_id = absint( $comment->comment_post_ID );

			if ( ! isset( $this->purgeQueue[ $object_id ] ) ) {
				$this->purgeQueue[ $object_id ] = $object_id;
				do_action( 'searchwp_log', 'purge_post_via_comment() ' . $object_id );
			} else {
				do_action( 'searchwp_log', 'Prevented duplicate purge purge_post_via_comment() ' . $object_id );
			}
		}
	}


	/**
	 * Add a post to a purge queue after any of it's terms were changed
	 *
	 * @param $object_id
	 * @param $terms
	 * @param $tt_ids
	 * @param $taxonomy
	 * @param $append
	 * @param $old_tt_ids
	 */
	function purge_post_via_term( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( false !== wp_is_post_revision( $object_id ) ) {
			return;
		}

		$object_id = absint( $object_id );

		// prevent repeated purging of the same post
		if ( ! isset( $this->purgeQueue[ $object_id ] ) ) {

			// Only mark for purge if the taxonomy applies
			if ( $this->is_used_taxonomy( $taxonomy ) ) {
				$this->purgeQueue[ $object_id ] = $object_id;
				do_action( 'searchwp_log', 'purge_post_via_term() ' . $object_id );
			} else {
				do_action( 'searchwp_log', 'purge_post_via_term() skipped, taxonomy not used: ' . $taxonomy );
			}
		}
		else {
			do_action( 'searchwp_log', 'Prevented duplicate purge purge_post_via_term() ' . $object_id );
		}
	}


	/**
	 * Trigger a reindex
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function trigger_reindex() {
		// check capabilities
		if (
			! current_user_can( 'edit_posts' ) &&
			! current_user_can( 'edit_pages' ) &&
			! current_user_can( $this->settings_cap )
		) {
			do_action( 'searchwp_log', 'Failed capabilities check in triggerReindex()' );
			return false;
		}

		do_action( 'searchwp_log', 'Request index (reindex)' );
		$this->trigger_index();

		return true;
	}


	/**
	 * Callback for plugin activation, outputs admin notice
	 *
	 * @since 1.0
	 */
	function activation() {
		if ( false == searchwp_get_setting( 'activated' ) ) {
			searchwp_set_setting( 'activated', true );

			// Initialize indexer counts
			if ( class_exists( 'SearchWPIndexer' ) ) {
				$indexer = new SearchWPIndexer();
				$indexer->update_running_counts();
			}

			// Redirect to settings screen
			$settings_screen_url = add_query_arg(
				array(
					'page' => 'searchwp',
					'welcome' => '1',
				),
				esc_url( admin_url( 'options-general.php' ) )
			);

			wp_safe_redirect( $settings_screen_url );
		}
	}


	/**
	 * Register meta box for document content textarea
	 *
	 * @since 1.0
	 */
	function document_content_meta_box() {
		add_meta_box(
			'searchwp_doc_content',
			__( 'SearchWP File Content', 'searchwp' ),
			array( $this, 'document_content_meta_box_markup' ),
			'attachment'
		);
	}

	function get_mimes_from_settings_ids( $mimes ) {

		// We need an array of integers that match the 'reference' IDs used in the settings that correlate to mime type groups
		if ( ! is_array( $mimes ) ) {
			if ( false !== strpos( $mimes, ',' ) ) {
				$mimes = explode( ',', $mimes );
			} else {
				$mimes = array( $mimes );
			}
		}

		$mimes = array_map( 'absint', $mimes );

		$targetedMimes  = array();

		// TODO: Better system for this
		$mimeref = array(
			'image' => array(
				'image/jpeg',
				'image/gif',
				'image/png',
				'image/bmp',
				'image/tiff',
				'image/x-icon',
			),
			'video' => array(
				'video/x-ms-asf',
				'video/x-ms-wmv',
				'video/x-ms-wmx',
				'video/x-ms-wm',
				'video/avi',
				'video/divx',
				'video/x-flv',
				'video/quicktime',
				'video/mpeg',
				'video/mp4',
				'video/ogg',
				'video/webm',
				'video/x-matroska',
			),
			'text' => array(
				'text/plain',
				'text/csv',
				'text/tab-separated-values',
				'text/calendar',
				'text/richtext',
				'text/css',
				'text/html',
			),
			'audio' => array(
				'audio/mpeg',
				'audio/x-realaudio',
				'audio/wav',
				'audio/ogg',
				'audio/midi',
				'audio/x-ms-wma',
				'audio/x-ms-wax',
				'audio/x-matroska',
			),
			'application' => array(
				'application/rtf',
				'application/javascript',
				'application/pdf',
				'application/x-shockwave-flash',
				'application/java',
				'application/x-tar',
				'application/zip',
				'application/x-gzip',
				'application/rar',
				'application/x-7z-compressed',
				'application/x-msdownload',
			),
			'msoffice' => array(
				'application/msword',
				'application/vnd.ms-powerpoint',
				'application/vnd.ms-write',
				'application/vnd.ms-excel',
				'application/vnd.ms-access',
				'application/vnd.ms-project',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'application/vnd.ms-word.document.macroEnabled.12',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
				'application/vnd.ms-word.template.macroEnabled.12',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'application/vnd.ms-excel.sheet.macroEnabled.12',
				'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
				'application/vnd.ms-excel.template.macroEnabled.12',
				'application/vnd.ms-excel.addin.macroEnabled.12',
				'application/vnd.openxmlformats-officedocument.presentationml.presentation',
				'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
				'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
				'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
				'application/vnd.openxmlformats-officedocument.presentationml.template',
				'application/vnd.ms-powerpoint.template.macroEnabled.12',
				'application/vnd.ms-powerpoint.addin.macroEnabled.12',
				'application/vnd.openxmlformats-officedocument.presentationml.slide',
				'application/vnd.ms-powerpoint.slide.macroEnabled.12',
				'application/onenote',
			),
			'openoffice' => array(
				'application/vnd.oasis.opendocument.text',
				'application/vnd.oasis.opendocument.presentation',
				'application/vnd.oasis.opendocument.spreadsheet',
				'application/vnd.oasis.opendocument.graphics',
				'application/vnd.oasis.opendocument.chart',
				'application/vnd.oasis.opendocument.database',
				'application/vnd.oasis.opendocument.formula',
			),
			'wordperfect' => array(
				'application/wordperfect',
			),
			'iwork' => array(
				'application/vnd.apple.keynote',
				'application/vnd.apple.numbers',
				'application/vnd.apple.pages',
			),
		);

		foreach ( $mimes as $mimeKey )  {
			switch ( intval( $mimeKey ) ) {
				case 1: // PDFs
					$targetedMimes = array_merge( $targetedMimes, array( 'application/pdf' ) );
					break;
				case 2: // Plain Text
					$targetedMimes = array_merge( $targetedMimes, $mimeref['text'] );
					break;
				case 3: // Images
					$targetedMimes = array_merge( $targetedMimes, $mimeref['image'] );
					break;
				case 4: // Video
					$targetedMimes = array_merge( $targetedMimes, $mimeref['video'] );
					break;
				case 5: // Audio
					$targetedMimes = array_merge( $targetedMimes, $mimeref['audio'] );
					break;
				case 6: // Office Documents
					$targetedMimes = array_merge( $targetedMimes,
						$mimeref['msoffice']
					);
					break;
				case 7: // OpenOffice Documents
					$targetedMimes = array_merge( $targetedMimes,
						$mimeref['openoffice']
					);
					break;
				case 8: // iWork Documents
					$targetedMimes = array_merge( $targetedMimes,
						$mimeref['iwork']
					);
					break;
				default: // All Documents (which is stored as zero)
					$targetedMimes = array_merge( $targetedMimes,
						$mimeref['text'],
						$mimeref['application'],
						$mimeref['msoffice'],
						$mimeref['openoffice'],
						$mimeref['wordperfect'],
						$mimeref['iwork']
					);
					break;
			}
		}

		// remove dupes
		$targetedMimes = array_unique( $targetedMimes );

		return $targetedMimes;
	}

	/**
	 * Output the markup for the document content meta box
	 *
	 * @param $post
	 *
	 * @since 1.0
	 */
	function document_content_meta_box_markup( $post ) {
		$existingContent = get_post_meta( $post->ID, SEARCHWP_PREFIX . 'content', true );
		$pdf_metadata = get_post_meta( $post->ID, SEARCHWP_PREFIX . 'pdf_metadata', true );

		$supportedMimeTypes = array(
			'text/plain',
			'text/csv',
			'text/tab-separated-values',
			'text/calendar',
			'text/richtext',
			'text/css',
			'text/html',
			'application/pdf',
			'application/rtf',
			'application/msword',
			'application/vnd.ms-powerpoint',
			'application/vnd.ms-write',
			'application/vnd.ms-excel',
			'application/vnd.ms-access',
			'application/vnd.ms-project',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-word.document.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'application/vnd.ms-word.template.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.ms-excel.sheet.macroEnabled.12',
			'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'application/vnd.ms-excel.template.macroEnabled.12',
			'application/vnd.ms-excel.addin.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.template',
			'application/vnd.ms-powerpoint.template.macroEnabled.12',
			'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.slide',
			'application/vnd.ms-powerpoint.slide.macroEnabled.12',
			'application/onenote',
			'application/vnd.oasis.opendocument.text',
			'application/vnd.oasis.opendocument.presentation',
			'application/vnd.oasis.opendocument.spreadsheet',
			'application/vnd.oasis.opendocument.graphics',
			'application/vnd.oasis.opendocument.chart',
			'application/vnd.oasis.opendocument.database',
			'application/vnd.oasis.opendocument.formula',
			'application/wordperfect',
			'application/vnd.apple.keynote',
			'application/vnd.apple.numbers',
			'application/vnd.apple.pages',
		);

		if ( in_array( $post->post_mime_type, $supportedMimeTypes ) ) : ?>
			<?php $file_content_limit = absint( apply_filters( 'searchwp_file_content_limit', 1000000 ) ); ?>
			<?php if ( $file_content_limit > strlen( $existingContent ) ) : ?>
				<?php wp_nonce_field( 'searchwpdoc', 'searchwp_doc_nonce' ); ?>
				<p><?php esc_html_e( 'The content below will be indexed for this file. If you are experiencing unexpected search results, ensure accuracy here.', 'searchwp' ); ?></p>
				<!--suppress HtmlFormInputWithoutLabel -->
				<textarea style="display:block;width:100%;height:300px;" name="searchwp_doc_content"><?php if ( $existingContent ) { echo esc_textarea( $existingContent ); } ?></textarea>
				<div style="display:none !important;overflow:hidden !important;">
					<!--suppress HtmlFormInputWithoutLabel -->
					<textarea style="display:block;width:100%;height:300px;" name="searchwp_doc_content_original"><?php if ( $existingContent ) { echo esc_textarea( $existingContent ); } ?></textarea>
				</div>
			<?php else : ?>
				<?php
				if ( function_exists( 'mb_strlen' ) ) {
					$size = mb_strlen( $existingContent, '8bit' );
				} else {
					$size = strlen( $existingContent );
				}
				$sample = wordwrap( $existingContent, 1000 );
				$sample = explode( "\n", $sample );
				$sample = array_slice( $sample, 0, 100 );
				$sample = implode( ' ', $sample );
				unset( $existingContent );
				?>

				<p><?php echo wp_kses( sprintf( __( '<strong>NOTE:</strong> This content is too long to display (%s). Here is a sample from the indexed content:', 'searchwp' ), size_format( $size, 2 ) ), array( 'strong' => array() ) ); ?></p>

				<!--suppress HtmlFormInputWithoutLabel -->
				<textarea style="display:block;width:100%;height:9em;" disabled="disabled"><?php echo esc_textarea( $sample ); ?></textarea>

				<p><?php echo wp_kses( __( "To override this limit you must add the following to your theme's <code>functions.php</code>:", 'searchwp' ), array( 'code' => array() ) ); ?></p>
				<!--suppress HtmlFormInputWithoutLabel -->
				<textarea style="display:block;width:100%;height:8em;" disabled="disabled">function searchwp_file_content_limit( $limit ) {
	return <?php echo absint( $size + 100 ); ?>;
}

add_filter( 'searchwp_file_content_limit', 'my_searchwp_file_content_limit' );</textarea>
			<?php endif; ?>
			<?php
			if ( ! empty( $pdf_metadata ) ) {
				$this->echo_indexed_pdf_metadata( $pdf_metadata );
			}
			?>
		<?php else : ?>
			<p><?php esc_html_e( 'Only plain text files, PDFs, and Office documents are supported at this time.', 'searchwp' ); ?></p>
		<?php endif;
	}


	/**
	 * Output the PDF metadata that was indexed in the SearchWP File Contents Meta box
	 *
	 * @since 2.5
	 * @param array     $pdf_metadata   PDF metadata to echo
	 */
	private function echo_indexed_pdf_metadata( $pdf_metadata ) {
		?>
			<div class="searchwp-indexed-pdf-metadata">
				<h3><?php _e( 'Indexed PDF Metadata', 'searchwp' ); ?></h3>
				<table>
					<thead>
					<tr>
						<th><?php _e( 'Key', 'searchwp' ); ?></th>
						<th><?php _e( 'Value', 'searchwp' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ( $pdf_metadata as $key => $val ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $key ); ?></strong></td>
							<td>
								<?php
								if ( is_array( $val ) ) {
									$val = array_map( 'esc_html', $val );
									echo implode( '<br />', $val );
								} else {
									echo esc_html( $val );
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<style type="text/css">
				.searchwp-indexed-pdf-metadata {
					padding-top:1em;
					opacity:0.7;
				}
				#poststuff .searchwp-indexed-pdf-metadata h3,
				.searchwp-indexed-pdf-metadata h3 {
					padding-left:0;
					padding-bottom:0.5em;
				}
				.searchwp-indexed-pdf-metadata table {
					width:100%;
					border-collapse: collapse;
				}
				.searchwp-indexed-pdf-metadata td {
					padding:0.5em 0;
					border-top:1px solid #eee;
				}
				.searchwp-indexed-pdf-metadata table thead {
					display:none;
				}
			</style>
		<?php
	}


	/**
	 * Callback fired when saving documents, saves document content
	 *
	 * @param $post_id
	 *
	 * @since 1.0
	 */
	function document_content_save( $post_id ) {
		// check capability
		if ( ! isset( $_REQUEST['post_type'] ) ) {
			return;
		}

		if ( 'attachment' == $_REQUEST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		}
		else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		// check intent
		if ( ! isset( $_POST['searchwp_doc_nonce'] ) || ! wp_verify_nonce( $_POST['searchwp_doc_nonce'], 'searchwpdoc' ) ) {
			return;
		}

		$originalContent = isset( $_POST['searchwp_doc_content_original'] ) ? sanitize_text_field( $_POST['searchwp_doc_content_original'] ) : '';
		$editedContent   = isset( $_POST['searchwp_doc_content'] ) ? sanitize_text_field( $_POST['searchwp_doc_content'] ) : '';
		$alreadySkipped  = get_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'skip_doc_processing', true );

		// check to see if the doc content is different than what it was
		if ( $alreadySkipped || ( md5( $originalContent ) != md5( $editedContent ) ) ) {
			do_action( 'searchwp_log', 'File content was edited by hand, saving' );
			$postID = false;
			if ( isset( $this->post ) ) {
				$postID = $this->post->ID;
			} elseif ( is_numeric( $post_id ) ) {
				$postID = $post_id;
			}
			if ( $postID ) {
				update_post_meta( $post_id, '_' . SEARCHWP_PREFIX . 'skip_doc_processing', true );
				update_post_meta( $post_id, SEARCHWP_PREFIX . 'content', $editedContent );
				// TODO: better handling of non-auto-indexed file formats ($this->post is not defined for those attachments)
				delete_post_meta( $postID, '_' . SEARCHWP_PREFIX . 'attempts' );
				delete_post_meta( $postID, '_' . SEARCHWP_PREFIX . 'skip' );

				// allow this post to be indexed on the next pass
				$this->purge_post( $postID );
			}
		}

	}


	/**
	 * By default we strip all punctuation from content before indexing it. Unfortunately that level of aggressiveness
	 * results in the loss of some data (e.g. dates), so this method will allow us to whitelist regex patterns that
	 * excuse matches from being lost in the sanitization process
	 *
	 * @param      $content string raw content
	 * @param bool $buffer whether to include a buffer before and after each whitelisted term
	 *
	 * @return array found matches
	 * @since 1.9
	 */
	function extract_terms_using_pattern_whitelist( $content, $buffer = true ) {

		$content = ' ' . $content . ' ';
		$matches = array();
		$term_pattern_whitelist = apply_filters( 'searchwp_term_pattern_whitelist', $this->term_pattern_whitelist );
		$term_pattern_whitelist = array_unique( $term_pattern_whitelist );

		if ( is_array( $term_pattern_whitelist ) && ! empty( $term_pattern_whitelist ) ) {

			foreach ( $term_pattern_whitelist as $term_pattern ) {

				preg_match_all( $term_pattern, $content, $pattern_matches );

				if ( ! empty( $pattern_matches ) ) {

					foreach ( $pattern_matches as $pattern_match ) {

						if ( is_array( $pattern_match ) && ! empty( $pattern_match ) && ! empty( $content ) ) {

							$matches = array_merge( $matches, $pattern_match );

							// let the developer remove these matches ASAP to prevent further collisions (but not during indexing, we always want to index multiple matches!)
							if ( ! did_action( 'searchwp_indexer_running' ) && apply_filters( 'searchwp_exclusive_regex_matches', false ) ) {
								$content = trim( str_ireplace( $matches, ' ', ' ' . $content . ' ' ) );
							}

							foreach ( $matches as $matches_key => $match ) {
								$match_trimmed = trim( $match );
								$match_lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $match_trimmed, 'UTF-8' ) : strtolower( $match_trimmed );
								$matches[ $matches_key ] = ' ' . $match_lower . ' '; // add a buffer for whole word matching
							}
						}
					}
				}
			}
		}

		// all matches are (usually) buffered with spaces to allow string replacement
		$buffer = $buffer ? ' ' : '';
		foreach ( $matches as $match_key => $match ) {
			$matches[ $match_key ] = $buffer . trim( $match ) . $buffer;
		}

		$matches = array_unique( $matches );
		$matches = array_map( 'sanitize_text_field', $matches );
		$matches = array_filter( array_map( 'trim', $matches ), 'strlen' );

		return $matches;
	}

	/**
	 * Determine a canonical list of enabled post types (as far as search engine configs are concerned)
	 *
	 * @since 2.6
	 *
	 * @return array
	 */
	function get_enabled_post_types_across_all_engines() {
		$enabled_post_types = array();

		// If searching in the Admin is enabled, we're going to force enable all post types
		// otherwise searching a disabled post type will return nothing, so we have to deal
		// with that overhead here to avoid that problem
		$advanced_settings = searchwp_get_option( 'advanced' );
		$admin_search_enabled = ! empty( $advanced_settings['admin_search'] );

		foreach ( $this->settings['engines'] as $engine => $post_types ) {
			foreach ( $post_types as $post_type => $post_type_settings ) {
				if (
					( $admin_search_enabled // Does not matter if it's enabled for the engine, index anyway
					  || ( isset( $post_type_settings['enabled'] ) && ! empty( $post_type_settings['enabled'] ) )
					) && post_type_exists( $post_type ) ) {
					$enabled_post_types[] = $post_type;
				}
			}
		}

		return array_unique( $enabled_post_types );
	}

	/**
	 * Return all IDs that are excluded based on ALL engine rules
	 *
	 * e.g. If an ID is excluded from the default engine but NOT excluded from
	 * a supplemental engine, it should NOT be excluded from the index
	 *
	 * @since 2.9.0
	 */
	function get_post__not_in_across_all_engines( $existing ) {
		return $this->get_post_ids_for_rules( $existing, 'exclude' );
	}


	/**
	 * Return all IDs that are limited to based on ALL engine rules
	 *
	 * e.g. If an ID is limited to from the default engine but NOT limited
	 * to from a supplemental engine, it should NOT be limited in the index
	 *
	 * @since 2.9.0
	 */
	function get_post__in_across_all_engines( $existing, $post_type = 'post' ) {
		$post_type_exists = post_type_exists( $post_type );
		if ( ! $post_type_exists ) {
			return $existing;
		}

		return $this->get_post_ids_for_rules( $existing, 'limit_to', $post_type );
	}

	/**
	 * Generates a proper tax query that takes into account Rules across all engines
	 *
	 * @since 2.9.0
	 */
	function get_post_type_tax_query_for_rules( $post_type = 'post', $rules = 'exclude' ) {
		if ( $rules !== 'exclude' ) {
			$rules = 'limit_to';
		}

		// We're going to return a tax_query that meets our criteria
		$tax_query = array();

		$post_type_exists = post_type_exists( $post_type );
		if ( ! $post_type_exists ) {
			return $tax_query;
		}

		$taxonomies = get_object_taxonomies( $post_type );

		if ( empty( $taxonomies ) ) {
			return $tax_query;
		}

		// Loop through post type taxonomies to find rules across all engines for that post type
		foreach ( $taxonomies as $taxonomy ) {

			$tax_term_ids = array();

			foreach ( $this->settings['engines'] as $engine => $post_types ) {

				// Does the post type exist in the engine?
				if ( ! isset( $post_types[ $post_type ] ) ) {
					continue;
				}

				// Is the post type enabled for this engine?
				if ( empty( $post_types[ $post_type ]['enabled'] ) ) {
					continue;
				}

				// This is only applicable if the post type is in fact enabled
				$tax_term_ids[ $engine ] = array();

				// Is there an applicable rule for this taxonomy for this post type in this engine?
				if (
					empty( $post_types[ $post_type ]['options'] )
					|| empty( $post_types[ $post_type ]['options'][ $rules . '_' . $taxonomy ] )
				) {
					continue;
				}

				// Retrieve the term IDs for this taxonomy for this post type for this engine
				$term_ids = explode( ',', $post_types[ $post_type ]['options'][ $rules . '_' . $taxonomy ] );

				if ( ! is_array( $term_ids ) ) {
					$term_ids = array( absint( $term_ids ) );
				}

				if ( empty( $term_ids ) ) {
					continue;
				}

				$term_ids = array_map( 'absint', $term_ids );
				$tax_term_ids[ $engine ] = $term_ids;
			}

			// We only want IDs that are shared across ALL engines
			// If there's only one array element we'll take that
			if ( count( $tax_term_ids ) > 1 ) {
				$tax_term_ids = call_user_func_array( 'array_intersect', $tax_term_ids );
			} else {
				$tax_term_ids = array_values( $tax_term_ids );
				$tax_term_ids = $tax_term_ids[0];
			}

			if ( empty( $tax_term_ids ) ) {
				continue;
			}

			$tax_term_ids = array_map( 'absint', $tax_term_ids );
			$tax_term_ids = array_unique( $tax_term_ids );

			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field' => 'term_id',
				'terms' => $tax_term_ids,
				'operator' => $rules === 'limit_to' ? 'IN' : 'NOT IN',
			);
		}

		// If we're limiting, we can use an OR relation, but if we're excluding
		// we need to use an AND relation so as to accommodate use cases of exclusion
		// in one engine but not another.
		if ( ! empty( $tax_query ) ) {
			$tax_query['relation'] = $rules === 'limit_to' ? 'OR' : 'AND';
		}

		// Now we have the term IDs across all engines, we need to build our tax_query compatible arg
		return $tax_query;
	}

	/**
	 * Generates an array of post IDs based on rules across all engines
	 *
	 * @since 2.9.0
	 */
	function get_post_ids_for_rules( $existing_ids, $rules = 'exclude', $only_post_type = false ) {
		if ( $rules !== 'exclude' ) {
			$rules = 'limit_to';
		}
		$ids = array();

		if ( ! is_array( $existing_ids ) ) {
			$existing_ids = explode( ',', $existing_ids );
			$existing_ids = array_map( 'absint', $existing_ids );
			$existing_ids = array_unique( $existing_ids );
		}

		foreach ( $this->settings['engines'] as $engine => $post_types ) {
			$ids[ $engine ] = array();

			foreach ( $post_types as $post_type => $post_type_settings ) {

				if ( $only_post_type && $only_post_type !== $post_type ) {
					continue;
				}

				// Exclusion rules allow for comma separated IDs, limit to rules do not
				if ( 'exclude' === $rules ) {
					// Excluded IDs will be listed as comma separated
					if ( isset( $post_type_settings['options']['exclude'] ) && ! empty( $post_type_settings['options']['exclude'] ) ) {

						$post_type_excluded_ids = $post_type_settings['options']['exclude'];

						// Maybe stored as a comma separated string of integers
						$comma_strpos = strpos( $post_type_excluded_ids, ',' );
						if ( is_string( $post_type_excluded_ids ) && false !== $comma_strpos ) {
							$post_type_excluded_ids = explode( ',', $post_type_excluded_ids );
						} else {
							if ( is_string( $post_type_excluded_ids ) ) {
								$post_type_excluded_ids = array( absint( $post_type_excluded_ids ) );
							} else {
								$post_type_excluded_ids = array();
							}
						}

						if ( is_array( $post_type_excluded_ids ) && ! empty( $post_type_excluded_ids ) ) {
							$post_type_excluded_ids = array_map( 'absint', $post_type_excluded_ids );
							$ids[ $engine ] = array_merge( $ids[ $engine ], $post_type_excluded_ids );
						}
					}
				}

				// Engines can also exclude by taxonoy term
				$taxonomies = get_object_taxonomies( $post_type );
				if ( ! is_array( $taxonomies ) || empty( $taxonomies ) ) {
					continue;
				}

				foreach ( $taxonomies as $taxonomy ) {
					$taxonomy = get_taxonomy( $taxonomy );
					if ( isset( $post_type_settings['options'][ $rules . '_' . $taxonomy->name ] ) ) {
						$term_ids = explode( ',', $post_type_settings['options'][ $rules . '_' . $taxonomy->name ] );

						if ( ! is_array( $term_ids ) ) {
							$term_ids = array( absint( $term_ids ) );
						}

						if ( empty( $term_ids ) ) {
							continue;
						}

						$term_ids = array_map( 'absint', $term_ids );

						// Determine which post(s) have this term
						$args = array(
							'posts_per_page'    => -1,
							'fields'            => 'ids',
							'post_type'         => $post_type,
							'suppress_filters'  => true,
							'tax_query'         => array(
								array(
									'taxonomy'  => $taxonomy->name,
									'field'     => 'term_id',
									'terms'     => $term_ids,
								),
							)
						);

						// Media won't be published
						if ( 'attachment' == $post_type ) {
							$args['post_status'] = 'inherit';
						}

						// To prevent outside influence of this query we're going to remove all of the filters
						// This is only an indexer request so it won't cause any outside influence
						if ( apply_filters( 'searchwp_remove_pre_get_posts', true ) ) {
							remove_all_actions( 'pre_get_posts' );
							remove_all_filters( 'pre_get_posts' );
						}

						$ids_from_rules_by_taxonomy_term = get_posts( $args );

						if ( ! empty( $ids_from_rules_by_taxonomy_term ) ) {
							$ids[ $engine ] = array_merge( $ids[ $engine ], $ids_from_rules_by_taxonomy_term );
						}
					}
				}
			}
		}

		// We only want IDs that are shared across ALL engines
		if ( count( $ids ) > 1 ) {
			$ids = call_user_func_array( 'array_intersect', $ids );
		} else {
			$ids = $ids['default'];
		}

		// Now we can merge the common IDs with the existing exclusions
		$ids = array_merge( $ids, $existing_ids );
		$ids = array_map( 'absint', $ids );
		$ids = array_unique( $ids );

		return $ids;
	}

	/**
	 * Retrieve an array of indexed post type names
	 *
	 * @since  2.6.2
	 * @return array Post type names
	 */
	function get_indexed_post_types() {

		$indexed_post_types = apply_filters( 'searchwp_indexed_post_types', $this->postTypes );

		if ( is_array( $indexed_post_types ) ) {
			$indexed_post_types = array_merge( $this->postTypes, $indexed_post_types );
			$indexed_post_types = array_unique( $indexed_post_types );
		}

		return $indexed_post_types;
	}

	/**
	 * Determine if keyword stemming is supported in this locale
	 *
	 * @since 2.6.2
	 * @return bool
	 */
	function is_stemming_supported_in_locale() {
		$locale = apply_filters( 'searchwp_locale_override', get_locale() );

		$supported_default_locales = array(
			'en_US',
			'en_AU',
			'en_CA',
			'en_ZA',
			'en_GB',
		);

		return ( in_array( $locale, $supported_default_locales ) || apply_filters( 'searchwp_keyword_stem_locale', false, $locale ) );
	}

	/**
	 * Determine whether the submitted meta key is used in any engine
	 *
	 * @since 2.9.0
	 */
	function is_used_meta_key( $meta_key, $the_post ) {
		$used = false;

		foreach ( $this->settings['engines'] as $engine => $post_types ) {
			foreach ( $post_types as $post_type => $post_type_settings ) {

				if ( $post_type !== $the_post->post_type ) {
					continue;
				}

				if ( ! isset( $post_type_settings['enabled'] ) || empty( $post_type_settings['enabled'] ) ) {
					continue;
				}

				if ( empty( $post_type_settings['weights'] ) ) {
					continue;
				}

				if ( empty( $post_type_settings['weights']['cf'] ) ) {
					continue;
				}

				$engine_post_type_meta_keys = array();
				$engine_post_type_meta_key_pairs = $post_type_settings['weights']['cf'];

				foreach ( $engine_post_type_meta_key_pairs as $engine_post_type_meta_key_pair ) {
					$engine_post_type_meta_keys[] = $engine_post_type_meta_key_pair['metakey'];
				}

				// If an 'Any Custom Field' is added, it means all Custom Fields apply
				if (
					in_array( 'searchwpcfdefault', $engine_post_type_meta_keys )
					|| in_array( 'searchwp cf default', $engine_post_type_meta_keys )
					|| in_array( $meta_key, $engine_post_type_meta_keys )
				) {
					$used = true;
					break;
				} else {
					// Check for LIKE matches to meta key names
					foreach ( $engine_post_type_meta_keys as $engine_meta_key ) {
						if ( false === strpos( $engine_meta_key, '%' ) ) {
							continue;
						}

						$engine_meta_key_parts = explode( '%', $engine_meta_key );

						foreach ( $engine_meta_key_parts as $engine_meta_key_part ) {
							if ( false !== strpos( $meta_key, $engine_meta_key_part ) ) {
								$used = true;
								break;
							}
						}

						if ( $used ) {
							break;
						}
					}
				}
			}

			if ( $used ) {
				break;
			}
		}

		$used = apply_filters( 'searchwp_is_used_meta_key', $used, $meta_key );
		$used = ! empty( $used );

		return $used;
	}

	/**
	 * Determine whether the submitted taxonomy is used in any engine
	 *
	 * @since 2.9.0
	 */
	function is_used_taxonomy( $taxonomy ) {
		$used = false;

		foreach ( $this->settings['engines'] as $engine => $post_types ) {
			foreach ( $post_types as $post_type => $post_type_settings ) {

				if ( ! isset( $post_type_settings['enabled'] ) || empty( $post_type_settings['enabled'] ) ) {
					continue;
				}

				if ( empty( $post_type_settings['weights'] ) ) {
					continue;
				}

				if ( empty( $post_type_settings['weights']['tax'] ) ) {
					continue;
				}

				$engine_post_type_taxonomies = $post_type_settings['weights']['tax'];

				if ( ! array_key_exists( $taxonomy, $engine_post_type_taxonomies ) ) {
					continue;
				}

				$weight = intval( $engine_post_type_taxonomies[ $taxonomy ] );

				if ( $weight > 0 ) {
					$used = true;
					break;
				}
			}

			if ( $used ) {
				break;
			}
		}

		$used = apply_filters( 'searchwp_is_used_taxonomy', $used, $taxonomy );
		$used = ! empty( $used );

		return $used;
	}

	/**
	 * Returns a default configuration for post type
	 *
	 * @since 2.9
	 */
	public function get_default_config_for_post_type( $post_type ) {
		if ( ! post_type_exists( $post_type ) ) {
			return array();
		}

		// Bare essentials, disabled
		$config = array(
			'enabled' => false,
			'weights' => array(
				'cf' => new stdClass(),
				'tax' => array(),
			),
			'options' => array(
				'exclude' => '',
				'attribute_to' => '',
				'parent' => '',
			),
		);

		// Weights depend on supported features; set the defaults
		$supports = searchwp_get_supports_for_post_type( $post_type );
		$max_weight = apply_filters( 'searchwp_weight_max', 100 );
		foreach ( $supports as $supported => $label ) {
			switch ( $supported ) {
				case 'title':
					$weight = absint( $max_weight * 0.8 );
					break;
				case 'content':
					$weight = absint( $max_weight * 0.05 );
					break;
				case 'slug':
					$weight = absint( $max_weight * 0.6 );
					break;
				case 'excerpt':
					$weight = absint( $max_weight * 0.4 );
					break;
				case 'comments':
					$weight = absint( $max_weight * 0.01 );
					break;
				default:
					$weight = absint( $max_weight * 0.01 );
			}
			$config['weights'][ $supported ] = $weight;
		}

		// Set up placeholders for taxonomy weights and taxonomy excludes, they're expected
		$taxonomies = get_object_taxonomies( $post_type );
		foreach ( $taxonomies as $taxonomy ) {
			$config['weights']['tax'][ $taxonomy ] = 0;
		}

		return $config;
	}

	/**
	 * Deprecated functions — most are internal and only deprecated because camelCase
	 */

	// @codingStandardsIgnoreStart
	/**
	 * @deprecated as of 2.5.7
	 */
	function checkDatabaseEnvironment() {
		$this->check_database_environment();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function setupPurgeQueue() {
		$this->setup_purge_queue();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function getPid() {
		return $this->get_pid();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function maybeOutputDebug() {
		$this->maybe_output_debug();
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $name
	 * @param $id
	 * @param bool $href
	 */
	function adminBarAddRootMenu( $name, $id, $href = false ) {
		$this->admin_bar_add_root_menu( $name, $id, $href );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $name
	 * @param $link
	 * @param $root_menu
	 * @param $id
	 * @param bool $meta
	 *
	 * @internal param bool $href
	 */
	function adminBarAddSubMenu( $name, $link, $root_menu, $id, $meta = false ) {
		$this->admin_bar_add_sub_menu( $name, $link, $root_menu, $id, $meta );
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function adminBarMenu() {
		$this->admin_bar_menu();
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $post_id
	 * @param bool $timeDiff
	 */
	function getLastIndexedTime( $post_id, $timeDiff = false ) {
		$this->get_last_indexed_time( $post_id, $timeDiff );
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function indexerPause() {
		$this->indexer_pause();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function indexerUnpause() {
		$this->indexer_unpause();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function checkIfPaused() {
		$this->check_if_paused();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function validateDatabaseEnvironment() {
		$this->validate_database_environment();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function processUpdates() {
		$this->process_updates();
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param null $plugin_file
	 * @param $plugin_data
	 * @param null $status
	 */
	function pluginRow( $plugin_file = null, $plugin_data, $status = null ) {
		$this->plugin_row( $plugin_file, $plugin_data, $status );
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function triggerIndex() {
		$this->trigger_index();
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $query
	 *
	 * @return mixed
	 */
	function checkForMainQuery( $query ) {
		return $this->check_for_main_query( $query );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $engineName
	 *
	 * @return bool
	 */
	function isValidEngine( $engineName ) {
		return $this->is_valid_engine( $engineName );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $terms
	 * @param string $engine
	 *
	 * @return array
	 */
	function sanitizeTerms( $terms, $engine = 'default' ) {
		return $this->sanitize_terms( $terms, $engine );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $query
	 *
	 * @return bool|string
	 */
	function maybeCancelWpQuery( $query ) {
		return $this->maybe_cancel_wp_query( $query );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $posts
	 *
	 * @return array
	 */
	function wpSearch( $posts ) {
		return $this->wp_search( $posts );
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function resetStats() {
		$this->reset_stats();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function purgeIndex() {
		$this->purge_index();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function getEnvironment() {
		return false;
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function showErroneousPosts() {
		$this->show_erroneous_posts();
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	function validateSettings( $input ) {
		return $this->validate_settings( $input );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	function sanitizeEngineSettings( $settings ) {
		return $this->sanitize_engine_settings( $settings );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $post_id
	 */
	function purgePostViaEdit( $post_id ) {
		$this->purge_post_via_edit( $post_id );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $post_id
	 */
	function purgePost( $post_id ) {
		$this->purge_post( $post_id );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $id
	 */
	function purgePostViaComment( $id ) {
		$this->purge_post_via_comment( $id );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $object_id
	 * @param $terms
	 * @param $tt_ids
	 * @param $taxonomy
	 * @param $append
	 * @param $old_tt_ids
	 */
	function purgePostViaTerm( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		$this->purge_post_via_term( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids );
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function triggerReindex() {
		$this->trigger_reindex();
	}
	// @codingStandardsIgnoreEnd

}

/**
 * Deactivation routine
 */
if ( ! function_exists( 'swp_deactivate' ) ) {
	function swp_deactivate() {

		// remove cron jobs
		$swp_maintenance_timestamp = wp_next_scheduled( 'swp_maintenance' );
		if ( $swp_maintenance_timestamp ) {
			wp_unschedule_event( $swp_maintenance_timestamp, 'swp_maintenance' );
		}
		$swp_indexer_timestamp = wp_next_scheduled( 'swp_indexer' );
		if ( $swp_indexer_timestamp ) {
			wp_unschedule_event( $swp_indexer_timestamp, 'swp_indexer' );
		}

		// remove database validation flag
		searchwp_set_setting( 'valid_db_environment', false );
	}
}

register_deactivation_hook( __FILE__, 'swp_deactivate' );

if ( ! function_exists( 'swp_init' ) ) {
	/**
	 * The one true SearchWP
	 *
	 * @return SearchWP SearchWP singleton
	 * @since 1.0
	 */
	function swp_init() {
		global $searchwp;

		if ( is_admin() || apply_filters( 'searchwp_init', true ) ) {
			$searchwp = SearchWP::instance();
		}

		if ( isset( $_GET['swpjumpstart'] ) ) {
			$waiting = searchwp_get_option( 'waiting' );
			if ( ! $waiting ) {
				searchwp_wake_up_indexer();
			}
		}

		return $searchwp;
	}
}

// @codingStandardsIgnoreStart
if ( ! function_exists( 'SWP' ) ) {

	/**
	 * Set up a reference function instead of using a global
	 * if the function already exists DO NOT INIT
	 *
	 * @since 2.3
	 *
	 * @return SearchWP
	 */
	function SWP() {
		return SearchWP::instance();
	}

	// initialize SearchWP Singleton
	swp_init();

	add_action( 'wp_ajax_swp_progress', 'searchwp_get_indexer_progress' );
	add_action( 'wp_ajax_swp_conflict', 'swp_dismiss_filter_conflict' );
}
// @codingStandardsIgnoreEnd
