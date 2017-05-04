<?php

require_once( __DIR__ . '/class-base-theme.php' );

class Base_Theme extends BB_Theme {
	static $text_domain = "base";

	public function __construct(){
		parent::__construct();
		$this->acf_json_path = TEMPLATEPATH . '/acf-json';

		add_action( 'after_setup_theme', array( &$this, 'register_menus' ) );
		add_filter( 'image_size_names_choose', array( &$this, 'image_size_names_choose' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts_and_styles' ) );

		add_action( 'wp_head', array( &$this, 'print_scripts' ) );
		add_action( 'wp_footer', array( &$this, 'the_social_plugins' ) );

		add_filter( 'show_admin_bar', '__return_false' );

		add_action( 'init', array( &$this, 'exclude_attachments_from_search' ) );
		add_action( 'init', array( &$this, 'add_cf_support' ) );

		add_shortcode( 'year', array( &$this, 'shortcode_year' ) );

		add_filter( 'tiny_mce_before_init', array( &$this, 'insert_formats' ) );
		add_filter( 'mce_buttons_2', array( &$this, 'add_mce_button' ), 10, 2 );
		add_filter( 'img_caption_shortcode', array( &$this, 'max_width_caption_shortcode'), 10, 3 );

		add_editor_style();

		add_filter( 'excerpt_more', array( &$this, 'custom_excerpt_end' ) );
		add_filter( 'excerpt_length', array( &$this, 'custom_excerpt_length' ), 999 );
		remove_filter( 'acf_the_content', array( &$this, 'wpautop') );
		add_filter( 'default_page_template_title', array(&$this, 'rename_default_template' ) );

		$this->add_options_page();
		$this->register_image_sizes();
	}


	/**
	 * Add a global Theme Settings page in admin area
	 */
	public function add_options_page() {
		if ( function_exists('acf_add_options_page')) {
			acf_add_options_page( array (
				'page_title' 	=> __( 'Theme Options', self::$text_domain ),
				'menu_title'	=> 'Theme Options',
				'menu_slug' 	=> 'theme-options',
				'capability'	=> 'administrator',
				'redirect'		=> false
			) );
		}
	}

	/**
	 * Register post types used in this theme
	 */
	public function add_post_types(){
		$types = array(
		);

		foreach( $types as $type ) {
			$this->add_post_type($type);
		}
	}

	/**
	 * Register taxonomies used in this theme
	 */
	public function add_taxonomies(){
		$taxonomies = array(
			array(
				'types' => array( 'people'),
				'plural' => 'People Categories',
				'singular' => 'People Category',
				'slug' => 'people-categories',
				'name' => 'people_category'
			)
		);

		foreach( $taxonomies as $taxonomy_args ) {
			$post_type = $taxonomy_args['types'];
			unset($taxonomy_args['types']);
			$this->add_taxonomy( $taxonomy_args, $post_type );
		}
	}

	/**
	 * Add theme support for features required in this theme
	 */
	public function add_theme_supports(){
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption' ) );
		add_theme_support( 'post-thumbnails' );
	}

	/**
	 * Body class when info bar is active
	 * @param  array $class Array of classes
	 * @return array
	 */
	public function body_class( $class ) {
		if ( has_infobar() ) {
			$class[] = ' info-bar-active';
		}
		return $class;
	}

	/**
	 * Add support for Custom Fields in Events
	 */
	public function add_cf_support() {
		add_post_type_support( 'event', 'custom-fields' );
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
	 * Rename Default Template to Basic Page
	 */
	public function rename_default_template() {
		return __('Basic Page', 'base');
	}

	/**
	 * Change width in caption shortcode to max-width for image upload in Wyziwig
	 */
	function max_width_caption_shortcode( $output, $attr, $content ) {

		/* We're not worried abut captions in feeds, so just return the output here. */
		if ( is_feed() )
			return $output;

		/* Set up the default arguments. */
		$defaults = array(
			'id' => '',
			'align' => 'alignnone',
			'width' => '',
			'caption' => '',
			'class' => 'img'
		);

		/* Merge the defaults with user input. */
		$attr = shortcode_atts( $defaults, $attr );

		//* Get media attachment title */
		$id = str_replace('attachment_', '', $attr['id']);
		$attachment = get_post($id);
		$title = $attachment->post_title;
		//$attachment_meta = wp_get_attachment($id);
		//$img_title = $attachment_meta['title'];

		/* If the width is less than 1 or there is no caption, return the content wrapped between the < tags. */
		if ( 1 > $attr['width'] || empty( $attr['caption'] ) )
			return $content;

		/* Set up the attributes for the caption <div>. */
		$attributes = ( !empty( $attr['id'] ) ? ' id="' . esc_attr( $attr['id'] ) . '"' : '' );
		$attributes .= ' class="wp-caption ' . esc_attr( $attr['align'] ) . '"';
		$attributes .= ' style="max-width: ' . esc_attr( $attr['width'] ) . 'px; width: 100%;"';

		/* Open the caption <figure>. */
		$output = '<figure' . $attributes .'>';

		/* Allow shortcodes for the content the caption was created for. */
		$output .= do_shortcode( $content );

		/* Append the caption text. */
		$output .= '<figcaption class="wp-caption-text">' . $attr['caption'] . ' ' . $title . '</figcaption>';

		/* Close the caption </div>. */
		$output .= '</figure>';

		/* Return the formatted, clean caption. */
		return $output;
	}

	/**
	 * Enqueue JavaScript and vendor dependencies
	 */
	public function enqueue_scripts_and_styles() {
		$handle = self::$text_domain;
		$git_version = substr( exec( "git rev-parse HEAD" ), 0, 6 );
		$jquery_handle = reregister_jquery();
		$script_path = THEME_URI . "/assets";
		$wp_vars = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'templateUrl' => get_stylesheet_directory_uri()
		);

		// scripts
		wp_enqueue_script( $handle, "$script_path/js/main.min.js", array( $jquery_handle ), $git_version, true );
		wp_localize_script( $handle, 'wpVars', $wp_vars );

		// styles
		wp_enqueue_style( $handle, "$script_path/css/main.min.css", array(), $git_version );
	}

	/**
	 * Provide size choices for media library
	 * @param  string[] $sizes
	 * @return string[]
	 */
	public function image_size_names_choose( $sizes ) {
		return array_merge( $sizes, array(
			'tiny'   => __( 'Tiny Image', 'base' ),
			'small'  => __( 'Small Image', 'base' ),
			'medium' => __( 'Medium Image', 'base' ),
			'large'  => __( 'Large Image', 'base' ),
		) );
	}

	/**
	 * Print inline scripts and styles
	 */
	public function print_scripts() {
		global $pagenow;
		$this->inline_typekit();
		$this->site_favicons();
	}

	public function inline_typekit() {
		$typekit_id = "";
		if ( empty( $typekit_id ) ) {
			return;
		}
		?>
		<script data-cfasync="false" src="https://use.typekit.net/<?php echo $typekit_id; ?>.js"></script>
		<script data-cfasync="false">try{Typekit.load({ async: true });}catch(e){}</script>
		<?php
	}

	function site_favicons() {
		?>
		<link rel="apple-touch-icon" sizes="57x57" href="<?php echo THEME_URI; ?>/assets/img/favicons/apple-touch-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="<?php echo THEME_URI; ?>/assets/img/favicons/apple-touch-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="<?php echo THEME_URI; ?>/assets/img/favicons/apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="<?php echo THEME_URI; ?>/assets/img/favicons/apple-touch-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="<?php echo THEME_URI; ?>/assets/img/favicons/apple-touch-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="<?php echo THEME_URI; ?>/assets/img/favicons/apple-touch-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="<?php echo THEME_URI; ?>/assets/img/favicons/apple-touch-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="<?php echo THEME_URI; ?>/assets/img/favicons/apple-touch-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo THEME_URI; ?>/assets/img/favicons/apple-touch-icon-180x180.png">
		<link rel="icon" type="image/png" href="<?php echo THEME_URI; ?>/assets/img/favicons/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="<?php echo THEME_URI; ?>/assets/img/favicons/favicon-194x194.png" sizes="194x194">
		<link rel="icon" type="image/png" href="<?php echo THEME_URI; ?>/assets/img/favicons/favicon-96x96.png" sizes="96x96">
		<link rel="icon" type="image/png" href="<?php echo THEME_URI; ?>/assets/img/favicons/android-chrome-192x192.png" sizes="192x192">
		<link rel="icon" type="image/png" href="<?php echo THEME_URI; ?>/assets/img/favicons/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="<?php echo THEME_URI; ?>/assets/img/favicons/manifest.json">
		<link rel="mask-icon" href="<?php echo THEME_URI; ?>/assets/img/favicons/safari-pinned-tab.svg" color="#482b11">
		<link rel="shortcut icon" href="<?php echo THEME_URI; ?>/assets/img/favicons/favicon.ico">
		<meta name="apple-mobile-web-app-title" content="<?php echo get_option( 'blogname' ); ?>">
		<meta name="application-name" content="<?php echo get_option( 'blogname' ); ?>">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="msapplication-TileImage" content="<?php echo THEME_URI; ?>/assets/img/favicons/mstile-144x144.png">
		<meta name="theme-color" content="#482b11">
		<meta name="msapplication-config" content="<?php echo THEME_URI; ?>/assets/img/favicons/browserconfig.xml"/>
		<?php
	}

	/**
	 * Register image sizes to ensure whatever image sizes client uploads will properly get
	 * scaled down to ensure good load time.
	 *
	 * These sizes already account for 2x retina displays.
	 */
	public function register_image_sizes() {

		// large image size is used for full-width cover images
		add_image_size( 'large', 1440 );

		// medium image size is used for featured post thumbnails in list context
		add_image_size( 'medium', 450 );

		// small image size is used for smaller items such as logos
		add_image_size( 'small', 225 );

		// tiny image size is used for thumbnails (especially in WYSIWYG)
		add_image_size( 'tiny', 100 );
	}

	/**
	 * Register navigation menu areas that can be configurable via Appearance -> Menus
	 */
	public function register_menus() {
		register_nav_menus( array(
			'header-primary'     => __( 'Header Primary Menu', self::$text_domain ),
			'header-quick-links' => __( 'Header Quick Links Menu', self::$text_domain ),
			'footer'             => __( 'Footer Menu', self::$text_domain ),
			'footer-meta'        => __( 'Footer Copyright Links', self::$text_domain )
		) );
	}

	/**
	 * Shortcodes
	 */

	public function shortcode_year() {
		return date('Y');
	}

	/**
	 * WYSIWYG / Format Dropdown
	 */
	public function insert_formats( $init_array ) {
		$style_formats = array(
			array(
				'title' => 'Main Heading',
				'block' => 'h4',
				'classes' => 'main-heading',
				'wrapper' => false,
			),
			array(
				'title' => 'Intro Text',
				'block' => 'h3',
				'classes' => 'intro-text',
				'wrapper' => false,
			),
			array(
				'title' => 'Sub Heading',
				'block' => 'p',
				'classes' => 'sub-heading',
				'wrapper' => false,
			),
			array(
				'title' => 'Secondary Sub Heading',
				'block' => 'h5',
				'classes' => 'secondary-sub-heading',
				'wrapper' => false,
			),
			array(
				'title' => 'Button',
				'block' => 'span',
				'classes' => 'button button--primary',
				'wrapper' => true,
			),
			array(
				'title' => 'Byline',
				'block' => 'p',
				'classes' => 'byline',
				'wrapper' => false,
			),
			array(
				'title' => 'Affiliation',
				'block' => 'p',
				'classes' => 'affiliation',
				'wrapper' => false,
			)
		);

		$init_array['style_formats'] = json_encode( $style_formats );

		return $init_array;
	}

	/**
	 * Social Share
	 */
	function the_social_plugins() {
		?>
		<div id="fb-root"></div>
		<script>
			window.fbAsyncInit = function() {
				FB.init({
					appId: '<?php the_field('facebook_app_id', 'option'); ?>',
					xfbml: true,
					version: 'v2.1'
				});
			};

			(function(d, s, id){
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) {return;}
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/sdk.js";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		</script>
		<script type="text/javascript" async src="//platform.twitter.com/widgets.js"></script>
		<?php
	}

	/**
	 * Add Buttons To WP Editor Toolbar.
	 */
	public function add_mce_button( $buttons, $editor_id ){
		/* Add it as first item in the row */
		array_unshift( $buttons, 'styleselect' );
		return $buttons;
	}

	/**
	 * Return the excerpt length 
	 * @param $length 
	 * @return int
	 * @internal modify body as necessary
	 */
	public function custom_excerpt_length( $length ) {
		return 25;
	}

	/**
	 * Return the excerpt ending string
	 * @param $more 
	 * @return string
	 * @internal modify body as necessary
	 */
	public function custom_excerpt_end($more) {
		return '...';
	}

}

new Base_Theme();

