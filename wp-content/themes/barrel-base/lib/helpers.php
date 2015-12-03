<?php
// Render a module from the "modules" directory
function the_module($module_name = '') {
  if(empty($module_name)) {
    return false;
  }

  locate_template( "/modules/$module_name/$module_name.php", true, false );
}

function get_module($module_name = '') {
  if(empty($module_name)) {
    return false;
  }

  ob_start();

  the_module($module_name);

  $html = ob_get_contents();

  ob_end_clean();

  return $html;
}
