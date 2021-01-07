<?php

class BB_Blocks {

  /**
  *
  *  Allowed only blocks
  *
  *  @var array
  */
  protected $allowed_blocks;

  /**
  *
  *  Blocks features supported
  *
  *  @var array
  */
  protected $theme_supports;

  /**
  *
  *  ACF blocks
  *
  *  @var array
  */
  protected $acf_blocks;

  /**
  *
  *  Custom block categories
  *
  *  @var array
  */
  protected $custom_categories;

  /**
  *
  *  Extended styles
  *
  *  @var array
  */
  protected $extended_styles;
  /**
  *
  *  Allow only this default block styles
  * (extended styles will be automatically included)
  *
  *  @var array
  */
  protected $allowed_styles;

  /**
  *
  *  Remove these page templates from editing with Blocks
  *
  *  @var array
  */
  protected $excluded_page_templates;


  /**
  *
  *  Remove these page ids from editing with Blocks
  *
  *  @var array
  */
  protected $excluded_page_ids;

  /**
  *
  *  Block counter
  *
  *  @var number
  */
  static $private_counter = 0;
  static $total_blocks = 0;
  static $is_first = true;

  /**
  *
  *  Add hooks
  *
  */
  public function __construct() {

    add_filter( 'block_categories', array( &$this, 'block_categories' ), 10, 2 );
    add_filter( 'acf/pre_save_block', array($this,'pre_save_block'), 10, 2 );
    add_action( 'acf/init', array($this,'register_acf_block_types'), 10, 3);
    add_action( 'init', array($this,'add_theme_support') );
    add_filter( 'allowed_block_types', array($this,'allowed_block_types') );
    add_action( 'admin_init', array($this,'extend_blocks_styles') );
    add_action( 'admin_footer', array($this,'unregister_blocks_styles'));
    add_filter( 'blocks_can_edit_post_type', array($this,'page_disable_blocks'), 10, 2 );
    add_filter( 'use_block_editor_for_post_type', array($this,'page_disable_blocks'), 10, 2 );
    add_action( 'admin_head', array($this,'page_disable_classic_editor') );

  }

  /**
  *
  *  Equivalent to the_content() WP function
  *  Renders Brrl modules instead
  *
  */
  public function the_content($post = false){
    if(!$post) global $post;
    if ( !empty(apply_filters('the_content_blocks',  self::get_blocks_html($post->post_content))) ) {
      echo apply_filters('the_content_blocks',  self::get_blocks_html($post->post_content));
    } else {
      echo apply_filters('the_content', get_the_content($post));
    }
  }

  /**
  *
  *  ACF Blocks Blocks
  *
  */
  function register_acf_block_types(){
    if(!function_exists('acf_register_block_type') || !isset($this->acf_blocks) || !$this->acf_blocks) return;

    foreach($this->acf_blocks as $block){

      $block['title'] = __($block['title']);
      $block['description'] = __($block['description']);

      $block['render_callback'] = array($this,'render_acf_block');

      acf_register_block_type($block);
    }
  }

  /**
  *
  *  Extends blocks styles
  *
  */
  function extend_blocks_styles(){
    if(!isset($this->extended_styles) || !$this->extended_styles) return;

    foreach($this->extended_styles as $blockName => $styles){

      foreach($styles as $style){

        $style['label'] = __($style['label']);

        register_block_style(
          $blockName,
          $style
        );

      }
    }

  }

  /**
  *
  *  Remove block styles
  *
  */
  function unregister_blocks_styles(){

    if(!is_admin() || !isset($this->allowed_styles) || !is_array($this->allowed_styles)) return;

    $allowed_styles = $this->allowed_styles;

    if(isset($this->extended_styles) && is_array($this->extended_styles)){

      foreach($this->extended_styles as $blockName => $block_styles){

        if(!isset($allowed_styles[$blockName])) $allowed_styles[$blockName] = array();

        foreach($block_styles as $style){

          $allowed_styles[$blockName][] = $style['name'];

        }

      }

    }

    $allowed_blocks = 'var allowed_blocks = {';

    foreach($this->allowed_styles as $blockName => $styles){

      $allowed_blocks .= '"'.$blockName.'":[';

      $i = 0;

      foreach($styles as $style){
        if($i) $allowed_blocks.=',';
        $allowed_blocks .= '"'.$style.'"';
        $i++;
      }

      $allowed_blocks .= '],';
    }

    $allowed_blocks .= '}';

    ?>

    <script>
      jQuery(document).ready( function($){
        if(typeof wp === 'undefined' || typeof wp.hooks === 'undefined' || typeof wp.hooks.addFilter === 'undefined') return;
        wp.hooks.addFilter('blocks.registerBlockType', 'brrl/filters', function(block){

          <?= $allowed_blocks ?>;

          var filteredStyles = [];

          if(allowed_blocks[block.name]){

            for (var i = 0; i < block.styles.length; i++) {

              if(allowed_blocks[block.name].includes(block.styles[i].name)){

                filteredStyles.push(block.styles[i]);

              }
            }

          }

          block.styles = filteredStyles;

        return block; })
      })
    </script>

    <?php

  }

  /**
  *
  *  Filters allowed blocks
  *
  */
  function allowed_block_types( $allowed_blocks ) {

    if(!isset($this->allowed_blocks) || !$this->allowed_blocks) return $allowed_blocks;
    return $this->allowed_blocks;

  }

  /**
  *
  *  Adds Theme support
  *
  */
  function add_theme_support() {

    if(!isset($this->theme_supports) || !$this->theme_supports) return;

    foreach($this->theme_supports as $feature){
      add_theme_support( $feature );
    }
  }

  /**
  *
  *  Checks if block has a specific style
  *
  */
  static function is_style($block, $style){
    if(!isset($block['attrs']['className']) || strpos($block['attrs']['className'],'is-style-'.$style) === false){
      return false;
    } else {
      return true;
    }
  }

  /**
  *
  *  Add custom block categories
  *
  */
  static function block_categories($categories, $post){
    if ( $post->post_type !== 'page' && $post->post_type !== 'locations' ) {
      return $categories;
    }
    if ( empty($this->custom_categories) ) {
      return $categories;
    }
    $new_categories = array_merge(
      $categories,
      $this->custom_categories
    );
    return $new_categories;
  }

  /**
  *
  *  Add attributes to acf block comments
  *
  */
  static function pre_save_block($attrs) {
    return $attrs;
  }

  /**
  *
  *  Parse and render blocks
  *
  *  @return string
  */
  static function get_blocks_html($content){

    $blocks = self::parse_blocks($content);

    return self::render_blocks($blocks);

  }

  /**
  *
  *  Parse and render blocks
  *
  *  @return string
  */
  static function parse_blocks($content){

    $blocks = parse_blocks($content);

    self::$total_blocks += sizeof($blocks);

    $blocks = self::prepare_blocks($blocks);

    return $blocks;

  }

  /**
  *
  *  Prepare blocks
  *
  */
  static function prepare_blocks($blocks){

    //Hydrate reusable
    $blocks = self::hydrate_reusable_blocks($blocks);

    // Set columns width
    $blocks = self::prepare_columns_width($blocks);

    // Merge columns
    $blocks = self::prepare_columns_merge($blocks);

    //return
    return $blocks;
  }

  /**
  *
  *  Hydrate reusable blocks
  *
  */

  static function hydrate_reusable_blocks($blocks){

    $parsed_blocks = array();

    foreach($blocks as $key => $block){

      $is_reusable = false;

      // Fetch reusable
      if($block['blockName'] === 'core/block' && isset($block['attrs']) && isset($block['attrs']['ref'])) {
        $is_reusable = true;
        $post = get_post( $block['attrs']['ref'] );
        $block = self::parse_blocks($post->post_content);
      }

      //Recursive
      if(isset($block['innerBlocks']) && sizeof($block['innerBlocks']) > 0 ) {
        $block['innerBlocks'] = self::hydrate_reusable_blocks($block['innerBlocks']);
      }

      // Append block or blocks
      if($is_reusable && !isset($block['blockName'])){
        $parsed_blocks = array_merge($parsed_blocks, $block);
      } else {
        $parsed_blocks[] = $block;
      }
    }

    return $parsed_blocks;
  }

  /**
  *
  *  Set col widths
  *
  */
  static function prepare_columns_width($blocks){
    foreach($blocks as &$block){

      //Block null
      if(! $block['blockName'] ) continue;

      //Core/columns
      if( $block['blockName'] === 'core/columns' ){
        $width = 100;
        $flex = 0;

        foreach($block['innerBlocks'] as $innerBlock){
          if(!isset($innerBlock['attrs'])) $innerBlock['attrs'] = array();
          if(isset($innerBlock['attrs']['width'])){
            $width -= $innerBlock['attrs']['width'];
          } else {
            $flex++;
          }
        }

        if($width > 0){
          if($flex > 0) $width = (float) $width / (int) $flex;
          foreach($block['innerBlocks'] as $key => $innerBlock){
            if(!isset($innerBlock['attrs']['width'])){
              $block['innerBlocks'][$key]['attrs']['width'] = $width;
            }
          }
        }
      }

      //Recursive
      if(isset($block['innerBlocks']) && sizeof($block['innerBlocks']) > 0 ) {
        $block['innerBlocks'] = self::prepare_columns_width($block['innerBlocks']);
      }
    }

    return $blocks;
  }

  /**
  *
  *  Merge cols
  *
  */
  static function prepare_columns_merge($blocks){
    $blocks_new = array();
    $is_columns = false;
    $i = -1;
    foreach($blocks as $block){
      if(!$block['blockName']) continue;
      if($is_columns && $block['blockName'] === 'core/columns'){
        $blocks_new[$i]['innerBlocks'] = array_merge($blocks_new[$i]['innerBlocks'],$block['innerBlocks']);
      } else {
        $blocks_new[] = $block;
        if($block['blockName'] === 'core/columns'){
          $is_columns = true;
        } else {
          $is_columns = false;
        }
        $i++;
      }
    }
    foreach($blocks_new as &$block){
      if(isset($block['innerBlocks']) && sizeof($block['innerBlocks']) > 0 ) {
        $block['innerBlocks'] = self::prepare_columns_merge($block['innerBlocks']);
      }
    }
    return $blocks_new;
  }


  /**
  *
  *  Render an array of blocks into an html string
  *
  *  @return object
  */
  static function render_blocks(&$blocks, $parentBlock = false){
    $html = '';

    foreach($blocks as &$block){

      if(!isset($block['blockName']) && (!$block['innerHTML'] || $block['innerHTML'] == '') ) continue;

      if(!isset($block['blockName'])) $block['blockName'] = 'core/classic-editor';

      $block['parentBlock'] = false;
      if($parentBlock) {
        $block['parentBlock'] = $parentBlock;
        unset($block['parentBlock']['innerBlocks']);
      }

      $block = self::render_block($block);

      if(
        !empty($block['blockName']) &&
        $block['blockName'] == 'core/html' &&
        !empty($block['innerContent']) &&
        is_array($block['innerContent']) &&
        (strpos($block['innerContent'][0], 'iframe') !== false ||
        strpos($block['innerContent'][0], 'blockquote') !== false)
      ) {
        $html .= '<div class="align-c post-single__iframe-wrapper">';
        $html .= $block['innerContent'][0];
        $html .= '</div>';
      } else {
        $html .= $block['innerHTML'];
      }
    }

    return $html;
  }

  /**
  *
  *  Render block
  *
  *  @return object  block object with innerHTML rendered as Brrl modules
  *
  */
  static function render_block(&$block, $content = '', $is_preview = false, $post_id = 0 ){

    // Vars
    $block['blockName'] = $block['blockName'] ? $block['blockName'] : $block['name'];
    $block['is_preview'] = $is_preview;
    $is_acf_block = (strpos($block['blockName'], 'acf/') !== false);
    $acf_name = $block['blockName'];
    $name = $block['blockName'];
    $name = str_replace('/','-',$name);
    $block['block_class'] = $name;

    // Set ACF meta
    if($is_acf_block && !$is_preview) {
      acf_setup_meta( $block['attrs']['data'], $block['attrs']['id'], true );
    }

    // ACF Attrs
    if($is_acf_block) {
      self::hydrate_acf_attrs($block, $acf_name);
    }

    // Nested blocks
    if( $block['innerBlocks'] && sizeof($block['innerBlocks']) > 0 ){
      $block['innerHTML'] = self::render_blocks($block['innerBlocks'], $block);
    }

    // Block data
    global $post;
    if($post && !isset($block['post'])) $block['post'] = $post;
    self::$private_counter++;
    $block = array_merge($block['attrs'], $block);
    $block['block_counter'] = self::$private_counter;
    $block['is_first'] = self::$is_first;
    self::$is_first = false;
    $block['is_last'] = (self::$private_counter === self::$total_blocks);
    $block['block'] =  $block;

    // Render module
    if(!empty($block['render_template']) && file_exists( TEMPLATEPATH . '/' . $block['render_template'] )){
      $block['innerHTML'] = self::get_block_module_by_path($block['render_template'], $block);
    } else if(!file_exists( TEMPLATEPATH . "/modules/$name/$name.php" )){
      $block['innerHTML'] = do_shortcode($block['innerHTML']);
    } else {
      $block['innerHTML'] = get_module($name, $block);
    }

    $block['innerHTML'] = strip_shortcodes($block['innerHTML']);

    $block['innerHTML'] = apply_filters('brrl_render_block_html', $block['innerHTML'], $block);

    // Reset ACF meta
    if($is_acf_block && !$is_preview) {
      acf_reset_meta( $block['attrs']['id'] );
    }

    // Return hydrated block
    return $block;
  }

  /**
   * Pass arguments into a module and get returned HTML
   *
   * @param $render_template Path to module
   * @param array $args Key-value pairs which will be extracted as variables in module templates
   * @return string
   */
  static function get_block_module_by_path($render_template, $args){
    ob_start();
    extract( $args, EXTR_SKIP );
    include( TEMPLATEPATH . '/' . $render_template);
    return ob_get_clean();
  }

  /**
  *
  *  Render ACF block
  *
  *  Used as callback for acf_register_block_type()
  *
  *  @return object  block object
  *
  */
  static function render_acf_block($block, $content = '', $is_preview = false, $post_id = 0 ){

    // Get hydrated block
    $block = self::render_block($block, $content, $is_preview, $post_id);

    // Print on Admin preview
    if($is_preview) {
      echo $block['innerHTML'];
      ?>
      <script>
        setTimeout(function(){
          console.log('ACF block rendered');
        },10);
      </script>
      <?php
    }
    return $block;
  }

  /**
  *
  *  Hydrate ACF fields as Block attrs
  *
  *  @return object  block object
  *
  */
  static function hydrate_acf_attrs(&$block, $acf_name){
    if(!$block['attrs']) $block['attrs'] = array();

    $fields = get_fields();
    $fields = $fields ? $fields : array();

    if (!empty($acf_name)) {
      $block_type = acf_get_block_type($acf_name);
      if ($block_type && !empty($block_type['render_template'])) {
        $block['render_template'] = $block_type['render_template'];
      }
    }

    $block['attrs'] = array_merge($block['attrs'], array_merge(
      $fields,
      array(
        'raw' => get_fields(null, false)
      )
    ));
    return $block;
  }

  /**
   * Disable Editor
   *
  **/

  /**
   * Templates and Page IDs without editor
   *
   */
  function page_disable_editor( $id = false ) {

    if( empty( $id ) )
      return false;

    $id = intval( $id );
    $template = get_page_template_slug( $id );

    return in_array( $id, $this->excluded_page_ids ?? array() ) || in_array( $template, $this->excluded_page_templates  ?? array() );
  }

  /**
   * Disable Blocks by template
   *
   */
  function page_disable_blocks( $can_edit, $post_type ) {

    if( ! ( is_admin() && !empty( $_GET['post'] ) ) )
      return $can_edit;

    if( $this->page_disable_editor( $_GET['post'] ) )
      $can_edit = false;

    return $can_edit;

  }

  /**
   * Disable Classic Editor by template
   *
   */
  function page_disable_classic_editor() {

    $screen = get_current_screen();
    if( 'page' !== $screen->id || ! isset( $_GET['post']) )
      return;

    if( $this->page_disable_editor( $_GET['post'] ) ) {
      remove_post_type_support( 'page', 'editor' );
    }

  }

}
