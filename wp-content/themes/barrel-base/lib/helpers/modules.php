<?php
/**
 * Pass arguments into a module and get returned HTML
 *
 * @param $module_name Name of module
 * @param array $args Key-value pairs which will be extracted as variables in module templates
 * @return string
 */
function get_module( $module_name, $args = array() ) {
  ob_start();
  the_module( $module_name, $args );
  return ob_get_clean();
}

/**
 * Pass arguments into a module and render its HTML output
 * @param $module_name Name of module
 * @param array $args Key-value pairs which will be extracted as variables in module templates
 * @return bool|string
 */
function the_module( $module_name, $args = array() ) {
  if ( empty( $module_name ) ) {
    return;
  }

  extract( $args, EXTR_SKIP );

  include( TEMPLATEPATH . "/modules/$module_name/$module_name.php" );
}
