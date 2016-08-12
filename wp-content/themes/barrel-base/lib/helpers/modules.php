<?php
// Render a module from the "modules" directory
function the_module($module_name = '') {
  if(empty($module_name)) {
    return false;
  }
  echo get_module($module_name);
}

function get_module($module_name = '') {
  if(empty($module_name)) {
    return false;
  }

  ob_start();

  include( get_template_directory() . "/modules/$module_name/$module_name.php" );

  return ob_get_clean();
}