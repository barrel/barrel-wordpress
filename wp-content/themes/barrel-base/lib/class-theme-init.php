<?php

require_once( __DIR__ . '/class-base-theme.php' );

class Base_Theme extends BB_Theme {
  static $text_domain = 'barrel-base';

  public function __construct(){
    parent::__construct();
    $this->acf_json_path = TEMPLATEPATH . '/acf-json';
    $this->cpt_json_path = TEMPLATEPATH . '/cpt-json';

    add_action( 'after_setup_theme', array( &$this, 'register_menus' ) );
    add_filter( 'image_size_names_choose', array( &$this, 'image_size_names_choose' ) );
    add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts_and_styles' ) );

    add_action( 'wp_head', array( &$this, 'print_scripts_head_meta' ) );
    add_action( 'wp_footer', array( &$this, 'print_scripts_before_body_end' ) );

    add_filter( 'show_admin_bar', '__return_false' );

    add_action( 'init', array( &$this, 'exclude_attachments_from_search' ) );

    add_shortcode( 'year', array( &$this, 'shortcode_year' ) );

    add_filter( 'tiny_mce_before_init', array( &$this, 'insert_formats' ) );
    add_filter( 'mce_buttons_2', array( &$this, 'add_mce_button' ), 10, 2 );

    add_editor_style();

    // if you want to prevent acf from filtering wysiwyg editor fields
    // remove_filter( 'acf_the_content', array( &$this, 'wpautop') );
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
        'page_title'  => __( 'Theme Options', self::$text_domain ),
        'menu_title'  => 'Theme Options',
        'menu_slug'   => 'theme-options',
        'capability'  => 'administrator',
        'redirect'    => false
      ) );
    }
  }

  /**
   * Register post types used in this theme
   */
  public function add_post_types()
  {
    $ctp_post_types = $this->cpt_config_data();
    $types = array(
    );

    foreach( $types as $type ) {
      $this->add_post_type($type);
    }
  }

  /**
   * Register taxonomies used in this theme
   */
  public function add_taxonomies()
  {
    $cpt_taxonomies = $this->cpt_config_data( false );
    $taxonomies = array(
    );

    foreach( $taxonomies as $taxonomy_args ) {
      $post_type = $taxonomy_args['types'];
      unset($taxonomy_args['types']);
      $this->add_taxonomy( $taxonomy_args, $post_type );
    }
  }

  public function cpt_config_data( $is_post_type = true )
  {
    $cpt_key_name = $is_post_type ? 'cptui_post_types' : 'cptui_taxonomies';
    $cpt_json_file = $this->cpt_json_path . "/$cpt_key_name.json";
    $cpt_saved_data = get_option( $cpt_key_name, array() );

    // create our data cpt dir if not exists
    if ( !file_exists( dirname( $cpt_json_file ) ) )
    {
      @mkdir( dirname( $cpt_json_file ), 0777, true );
    }

    if ( !empty( $cpt_saved_data ) ) {
      $cpt_json_data = json_encode( $cpt_saved_data, JSON_PRETTY_PRINT );

      // create the file if not exists yet, or update if changed
      if ( !file_exists( $cpt_json_file ) )
      {
        @file_put_contents( $cpt_json_file, $cpt_json_data );
      }
      else
      {
        $theme_cpt_json_data = @file_get_contents( $cpt_json_file );
        if ( $cpt_json_data !== $theme_cpt_json_data )
        {
          @file_put_contents( $cpt_json_file, $cpt_json_data );
        }
      }
    }
    else
    {
      // no saved data, check files, load data
      $theme_cpt_json_data = @file_get_contents( $cpt_json_file );
      if ( !empty( $theme_cpt_json_data ) )
      {
        $cpt_saved_data = json_decode( $theme_cpt_json_data, true );
        update_option( $cpt_key_name, $cpt_saved_data );
      }
    }

    return $cpt_saved_data;
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
    return $class;
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
  public function rename_default_template()
  {
    return __('Basic Page', 'barrel-base');
  }

  /**
   * Enqueue JavaScript and vendor dependencies
   */
  public function enqueue_scripts_and_styles() {
    $handle = self::$text_domain;
    $git_version = substr( exec( "git rev-parse HEAD" ), 0, 6 );
    $version = empty( $git_version ) ? wp_get_theme()->get( 'Version' ) : $git_version;

    try {
      jquery_deregister();
    }
    catch ( Exception $ex ){}

    $script_path = THEME_URI . "/assets";

    // associative array with key-value pairs to be json encoded
    $wp_vars = array(
    );

    // scripts
    wp_enqueue_script( $handle, "$script_path/main.min.js", null, $version, ( IS_DEV ? false : true ) );
    if ( !empty( $wp_vars ) )
    {
      wp_localize_script( $handle, 'wpVars', $wp_vars );
    }

    // styles
    if ( !IS_DEV ) {
      wp_enqueue_style( $handle, "$script_path/main.min.css", array(), $version, 'all' );
    }
  }

  /**
   * Provide size choices for media library
   * @param  string[] $sizes
   * @return string[]
   */
  public function image_size_names_choose( $sizes ) {
    return array_merge( $sizes, array(
      'tiny'   => __( 'Tiny Image', 'barrel-base' ),
      'small'  => __( 'Small Image', 'barrel-base' ),
      'medium' => __( 'Medium Image', 'barrel-base' ),
      'large'  => __( 'Large Image', 'barrel-base' ),
    ) );
  }

  /**
   * Print inline scripts and styles
   */
  public function print_scripts_head_meta()
  {
    global $pagenow;
    $this->site_favicons();
  }

  private function site_favicons()
  {
    $favi = THEME_URI . '/assets/img/favicon/'; ?>

    <link rel="apple-touch-icon" sizes="180x180" href="<?= $favi; ?>apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $favi; ?>favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $favi; ?>favicon-16x16.png">
    <link rel="manifest" href="<?= $favi; ?>manifest.json">
    <link rel="mask-icon" href="<?= $favi; ?>safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="<?= $favi; ?>favicon.ico">
    <meta name="msapplication-config" content="<?= $favi; ?>browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
  <?php
  }

  public function print_scripts_before_body_end ()
  {
    $tracking_scripts = get_field( 'tracking_scripts', 'options' );

    // dangerously output code that is a script, style, or meta tag
    echo strip_tags( $tracking_scripts, '<script><style><meta>' );
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
        'classes' => 'main-heading',
        'wrapper' => true,
      ),
      array(
        'title' => 'Sub Heading',
        'classes' => 'sub-heading',
        'wrapper' => false,
      ),
      array(
        'title' => 'Secondary Sub Heading',
        'classes' => 'secondary-sub-heading',
        'wrapper' => true,
      ),
      array(
        'title' => 'Intro Text',
        'classes' => 'intro-text',
        'wrapper' => true,
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

    );

    $init_array['style_formats'] = json_encode( $style_formats );

    return $init_array;
  }

  /**
   * Add Buttons To WP Editor Toolbar.
   */
  public function add_mce_button( $buttons, $editor_id ){
    /* Add it as first item in the row */
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
  }

}

new Base_Theme();

