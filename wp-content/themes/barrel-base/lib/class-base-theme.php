<?php

abstract class BB_Theme {
  static $text_domain;
  public $acf_json_path;


  /**
   * Constructor: Filters and Actions.
   * @return  void
   */
  public function __construct(){
    $this->remove_emojis();
    add_action( 'init', array( &$this, 'add_post_types' ) );
    add_action( 'init', array( &$this, 'add_taxonomies' ) );
    add_action( 'after_setup_theme', array( &$this, 'add_theme_supports' ) );

    // Disable X-Pingback to header
    add_filter( 'pings_open', '__return_false', PHP_INT_MAX );
    add_filter( 'wp_headers', array( &$this, 'disable_pingbacks' ) );

    add_filter( 'acf/settings/save_json', array( &$this, 'acf_json_save_point' ) );
    add_filter( 'acf/settings/load_json', array( &$this, 'acf_json_load_point' ) );
  }

  abstract protected function add_post_types();

  abstract protected function add_taxonomies();

  abstract protected function add_theme_supports();

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

  public function remove_emojis()
  {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );

    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
  }

  public function disable_pingbacks( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
  }

  /**
   * Register taxonomy
   * @param array $args       - 'name': Taxonomy name
   *                          - 'plural': Plural form for title
   *                          - 'singular': Singular form for title
   *                          - 'slug': Slug used for permalink rewrite
   * @param string $post_type Post type associated with this taxonomy
   */
  protected function add_taxonomy( $args, $post_type ) {
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
      'rewrite'           => $args['rewrite'] ? $args['rewrite'] : array( 'slug' => $args['slug'] ),
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
  protected function add_post_type( $args ) {
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
      'exclude_from_search' => $args['exclude_from_search'],
      'query_var'          => true,
      'rewrite'            => $args['rewrite'] ? $args['rewrite'] : array( 'slug' => $args['slug'], 'with_front' => false ),
      'capability_type'    => 'post',
      'has_archive'        => $args['has_archive'] ? $args['has_archive'] : false,
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => $args['icon'],
      'supports'           => array( 'title', 'editor', 'author', 'thumbnail' )
    );

    register_post_type( $args['name'], $post_type );
  }
}
