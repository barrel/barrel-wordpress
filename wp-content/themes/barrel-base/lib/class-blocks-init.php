<?php

include_once( __DIR__ . '/class-base-blocks.php' );

class Base_Blocks extends BB_Blocks {

  /**
  *
  *  Allowed only blocks
  *
  *  @var array
  */
  protected $allowed_blocks = array(
    // Allowed custom blocks
    'acf/hero',

    // Allowed core blocks
    'core/paragraph',
    'core/list',
    'core/heading',
    'core/image',
    'core/gallery',
    'core/quote',
    'core/block',
    'core/html',
    'core/audio',
    'core/table',
    'core/spacer',
    'core/separator'
  );

  /**
  *
  *  Gutenberg features supported
  *
  *  @var array
  */
  protected $theme_supports = array(
    'align-wide'
  );

  /**
  *
  *  ACF blocks
  *
  *  @var array
  */
  protected $acf_blocks = array(
    array(
      'name' => 'hero',
      'title' => 'Hero',
      'description' => 'Hero at top of page',
      'icon' => 'align-left',
      'category' => 'barrel-base',
      'mode' => 'auto',
      'align' => 'full',
      'post_types' => array('page'),
      'supports' => array('align' => false),
      'keywords' => array('hero'),
      'render_template' => 'modules/hero/hero.php'
    )
  );

  /**
  *
  *  Custom Block Categories
  *
  *  @var array
  */
  protected $custom_categories = array(
    array(
      'slug' => 'barrel-base',
      'title' => 'Barrel Base',
      'icon'  => 'dashicons-welcome-widgets-menus',
    ),
  );

  /**
  *
  *  Extended styles
  *
  *  @var array
  */
  protected $extended_styles = array(
    /*'core/image' => array(
      array(
        'name'         => 'triangle',
        'label'        => 'Triangle'
      )
    )*/
  );

  /**
  *
  *  Allow only this default block styles
  * (extended styles will be automatically included)
  *
  *  @var array
  */
  protected $allowed_styles = array(
    /* 'core/image' => array(
      'default',
      'circle-mask'
    ) */
  );

  /**
  *
  *  Remove these page templates from editing with Gutenberg
  *
  *  @var array
  */
  protected $excluded_page_templates = array(
    // 'templates/page-landing.php'
  );

  /**
  *
  *  Add hooks
  *
  */
  public function __construct() {

    // $this->excluded_page_ids = array(
    //   get_option( 'page_on_front' )
    // );

    parent::__construct();

  }

}
