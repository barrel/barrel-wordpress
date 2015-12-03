<?php

abstract class BB_Theme {
	static $text_domain;

	/**
	 * Constructor: Filters and Actions.
	 * @return  void
	 */
	public function __construct(){
		add_action( 'init', array(&$this, 'add_post_types') );
		add_action( 'init', array(&$this, 'add_taxonomies') );
		add_action( 'after_setup_theme', array(&$this, 'add_theme_supports') );
	}

	abstract protected function add_post_types();

	abstract protected function add_taxonomies();

	abstract protected function add_theme_supports();
}

class New_Theme extends BB_Theme {
	public $acf_json_path;

	public function __construct(){
		parent::__construct();
		self::$text_domain = "northcastlepartners";
		$this->acf_json_path = THEME_DIR . '/acf-json';

		add_action( 'after_setup_theme', array( &$this, 'register_menus' ) );
		add_filter( 'image_size_names_choose', array( &$this, 'image_size_names_choose' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
		add_action( 'wp_print_scripts', array( &$this, 'print_scripts' ) );

		add_filter( 'acf/settings/save_json', array( &$this, 'acf_json_save_point' ) );
		add_filter( 'acf/settings/load_json', array( &$this, 'acf_json_load_point' ) );
		add_filter( 'nav_menu_css_class', array( &$this, 'menu_css_class' ), 10, 3 );
		add_filter( 'show_admin_bar', '__return_false');
		add_filter( 'wp_nav_menu_items', array( &$this, 'add_search_field' ), 10, 2 );
		add_filter( 'embed_oembed_html', array( &$this, 'wrap_oembed_video' ), 10 );

		add_action( 'admin_menu', array( &$this, 'remove_tag_menu' ) );
		add_action( 'admin_menu', array( &$this, 'rename_post_label' ) );
		add_action( 'admin_init', array( &$this, 'add_editor_style' ) );
		add_action( 'init', array( &$this, 'rename_post_object' ) );
		add_action( 'init', array( &$this, 'exclude_attachments_from_search' ) );

		$this->add_options_page();
		$this->register_image_sizes();
	}

	/**
	 * Rename the default post type label for post admin menu.
	 * @internal If WordPress changes the ordering of these defaults, things will break!!
	 * @return void
	 */
	public function rename_post_label() {
		global $menu;
		global $submenu;
		$menu[5][0] = $submenu['edit.php'][5][0] = 'News';
		$menu[5][6] = 'dashicons-format-aside';
		$submenu['edit.php'][10][0] = 'Add News';
		echo '';
	}

	/**
	 * Remove the default post type tag.
	 * @internal If WordPress changes the ordering of these defaults, things will break!!
	 * @return void
	 */
	function remove_tag_menu() {
	    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
	    echo '';
	}

	/**
	 * Rename the default post type label for post admin pages.
	 */
	public function rename_post_object() {
		global $wp_post_types;
		$new_label = 'News';
		$labels = &$wp_post_types['post']->labels;

		$labels->name =
		$labels->singular_name =
		$labels->menu_name =
		$labels->name_admin_bar =
		$labels->new_item = $new_label;

		$labels->add_new =
		$labels->add_new_item = "Add $new_label";

		$labels->edit_item = "Edit $new_label";
		$labels->view_item = "View $new_label";
		$labels->search_items = "Search $new_label";
		$labels->not_found = "No $new_label found";
		$labels->not_found_in_trash = "No $new_label found in Trash";
		$labels->all_items = "All $new_label";

		return;
	}

	/**
	 * The path where JSON files are created when ACF field groups are saved/updated
	 * @param string path of save point
	 * @return string path of save point
	 * @link http://www.advancedcustomfields.com/resources/local-json/
	 * @link http://www.advancedcustomfields.com/resources/synchronized-json/
	 */
	public function acf_json_save_point( $path ) {
		return $this->acf_json_path;
	}

	/**
	 * The path where JSON files are loaded when ACF field groups are initialized
	 * @param array of string paths of load point(s)
	 * @return array of string paths of load point(s)
	 * @link http://www.advancedcustomfields.com/resources/local-json/
	 * @link http://www.advancedcustomfields.com/resources/synchronized-json/
	 */
	public function acf_json_load_point($paths) {
		return array($this->acf_json_path);
	}

	/**
	 * Add a global Theme Settings page in admin area
	 */
	public function add_options_page() {
		if ( function_exists('acf_add_options_page')) {
			acf_add_options_page( array(
				'page_title' 	=> __( 'Theme Settings', self::$text_domain ),
				'menu_title'	=> 'Theme Settings',
				'menu_slug' 	=> 'theme-settings',
				'capability'	=> 'administrator',
				'redirect'		=> false
			) );
		} elseif ( is_admin() ) {
			wp_die('This theme requires ACF Pro to properly function.');
		}
	}

	/**
	 * Make sure <hr> clears both
	 */
	public function add_editor_style() {
		add_editor_style( 'assets/css/admin.min.css' );
	}

	/**
	 * Register post types used in this theme
	 */
	public function add_post_types(){
		$this->add_post_type( array(
			'singular' => 'Portfolio Company',
			'plural' => 'Portfolio Companies',
			'name' => 'portfolio',
			'slug' => 'portfolio',
			'icon' => 'dashicons-portfolio',
		) );

		$this->add_post_type( array(
			'singular' => 'Team Member',
			'plural' => 'Team Members',
			'name' => 'team',
			'slug' => 'our-team',
			'icon' => 'dashicons-businessman'
		) );
	}

	/**
	 * Append a search form to primary nav menu
	 * @param array $items Nav menu items
	 * @param array $args  Nav menu items after appending search form
	 */
	public function add_search_field( $items, $args ) {
		if ( $args->theme_location != 'primary' ) {
			return $items;
		}

		// TODO: note any potential limitations with this output buffer
		/**
		 * Markup for the navbar search form is separated into a template tag for ease of modifications.
		 * Because we need to output HTML in order for the wp_nav_menu_items filter to work, buffering
		 * is used rather than output directly.
		 */
		ob_start();
		get_template_part( 'templates/partials/search-form' );
		$items .= ob_get_clean();

		return $items;
	}

	/**
	 * Register taxonomies used in this theme
	 */
	public function add_taxonomies(){
		$this->add_taxonomy( array(
			'plural' => 'Industries',
			'singular' => 'Industry',
			'slug' => 'industry',
			'name' => 'industry'
		), 'portfolio' );

		$this->add_taxonomy( array(
			'plural' => 'Roles',
			'singular' => 'Role',
			'slug' => 'role',
			'name' => 'role',
		), 'team' );
	}

	/**
	 * Add theme support for features required in this theme
	 */
	public function add_theme_supports(){
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
	}

	/**
	 * Exclude attachments from search results on the front-end
	 */
	public function exclude_attachments_from_search() {
		if ( is_admin() ) {
			return;
		}

		global $wp_post_types;

		$wp_post_types['attachment']->exclude_from_search = true;
	}

	/**
	 * Re-registers jQuery in a safe manner in order to serve the CDN version from Google.
	 * @return string the handle of the registered jQuery script
	 */
	public function reregister_jquery() {
		global $wp_version;
		if ( is_admin() || in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) ) return;
		wp_enqueue_script( 'jquery' );
		// Check to see if we're on 3.6 or newer (changed the jQuery handle)
		$jquery_handle = ( version_compare( $wp_version, '3.7', '>=' ) ? 'jquery-core' : 'jquery');
		$wp_jquery_ver = $GLOBALS['wp_scripts']->registered[$jquery_handle]->ver;
		$jquery_google_url = '//ajax.googleapis.com/ajax/libs/jquery/'.$wp_jquery_ver.'/jquery.min.js';
		wp_deregister_script( $jquery_handle );
		wp_register_script( $jquery_handle, $jquery_google_url, '', null, true );
		return $jquery_handle;
	}

	/**
	 * Enqueue JavaScript and vendor dependencies
	 */
	public function enqueue_scripts() {
		$jquery_handle = $this->reregister_jquery();
		$wp_vars = array( 'templateUrl' => get_stylesheet_directory_uri() );

		wp_enqueue_script( 'ncp-vendor', get_template_directory_uri() . '/assets/js/vendor.min.js', array( $jquery_handle ), false, true );
		wp_enqueue_script( 'ncp', get_template_directory_uri() . '/assets/js/main.min.js', array( $jquery_handle, 'ncp-vendor' ), false, true );

		wp_localize_script( 'ncp', 'wp_vars', $wp_vars );
	}

	/**
	 * Enqueue main stylesheet
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'ncp', get_template_directory_uri() . '/assets/css/styles.min.css' );
	}

	public function image_size_names_choose( $sizes ) {
		return array_merge( $sizes, array(
			'hero-image' => __( 'Hero Image', self::$text_domain ),
			'portfolio-logo' => __( 'Portfolio Logo', self::$text_domain ),
			'featured-news-image' => __( 'Featured News Image', self::$text_domain ),
			'industry-photo' => __( 'Industry Photo', self::$text_domain ),
		) );
	}

	public function wrap_oembed_video( $html ) {
		return '<div class="post-content__video-wrapper">' . $html . '</div>';
	}

	public function menu_css_class( $classes, $item, $args ) {
		switch ( $args->theme_location ) {
			case 'primary':
				$classes[] = 'navbar__item';
				break;
			case 'secondary':
				$classes[] = 'actions__item';
				break;
			case 'footer':
				$classes[] = 'footer__menu-item';
				break;
		}

		return $classes;
	}

	public function print_scripts() {
		?>

		<script type="text/javascript">
			// add js class right away, preventing flash of content
			if (document.documentElement.classList) {
				document.documentElement.classList.add('js');
				document.documentElement.classList.remove('no-js');
			} else {
				document.documentElement.className = document.documentElement.className.replace(/(^|\s+)no-js(\s+|$)/, '$1js$1');
			}
		</script>

		<?php
	}

	public function register_image_sizes() {
		// TODO: these are very specific, need to document why these were created per instance
		add_image_size( 'hero-image', 2880 );
		add_image_size( 'portfolio-logo', 400 ); // TODO: this and the next are functionally the same thumbnail, refactor to be general
		add_image_size( 'featured-news-image', 400 );
		add_image_size( 'industry-photo', 2560 );
		add_image_size( 'profile-photo', 322, 386, true );
	}

	/**
	 * Register navigation menu areas that can be configurable via Appearance -> Menus
	 */
	public function register_menus() {
		register_nav_menus( array(
			'primary' => __( 'Primary Menu', self::$text_domain ),
			'secondary' => __( 'Secondary Menu', self::$text_domain ),
			'footer' => __( 'Footer Menu', self::$text_domain ),
		) );
	}

	/**
	 * Register taxonomy
	 * @param array $args       - 'name': Taxonomy name
	 *                          - 'plural': Plural form for title
	 *                          - 'singular': Singular form for title
	 *                          - 'slug': Slug used for permalink rewrite
	 * @param string $post_type Post type associated with this taxonomy
	 */
	private function add_taxonomy( $args, $post_type ) {
		$labels = array(
			'name'              => $args['plural'],
			'singular_name'     => $args['singular'],
			'search_items'      => 'Search ' .  $args['plural'],
			'all_items'         => 'All ' .  $args['plural'],
			'parent_item'       => 'Parent ' . $args['singular'],
			'parent_item_colon' => 'Parent ' . $args['singular'] . ':',
			'edit_item'         => 'Edit ' . $args['singular'],
			'update_item'       => 'Update ' . $args['singular'],
			'add_new_item'      => 'Add New ' . $args['singular'],
			'new_item_name'     => 'New ' . $args['singular'] . ' Name',
			'menu_name'         => $args['singular'],
		);

		$tax = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => $args['slug'] ),
		);

		register_taxonomy( $args['name'], $post_type, $tax );
	}

	/**
	 * Register post type
	 * @param array $args - 'name': Name of post type 
	 *                    - 'plural': Plural form of the post type title
	 *                    - 'singular': Singular form of the post type title
	 *                    - 'slug': Slug used for permalink rewrite
	 *                    - 'icon': Name of icon class
	 */
	private function add_post_type( $args ) {
		$labels = array(
			'name'               => $args['plural'],
			'singular_name'      => $args['singular'],
			'menu_name'          => $args['plural'],
			'name_admin_bar'     => $args['singular'],
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New ' . $args['singular'],
			'new_item'           => 'New ' . $args['singular'],
			'edit_item'          => 'Edit ' . $args['singular'],
			'view_item'          => 'View ' . $args['singular'],
			'all_items'          => 'All ' . $args['plural'],
			'search_items'       => 'Search ' .  $args['plural'],
			'parent_item_colon'  => 'Parent ' . $args['plural'] . ':',
			'not_found'          => 'No ' . $args['plural'] . ' found.',
			'not_found_in_trash' => 'No ' . $args['plural'] . ' found in Trash.'
		);

		$post_type = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', self::$text_domain ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $args['slug'] ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'menu_icon'          => $args['icon'],
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail' )
		);

		register_post_type( $args['name'], $post_type );	
	}
}

new New_Theme();
