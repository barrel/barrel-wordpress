<?php

/**
 * Re-registers jQuery in a safe manner in order to serve the CDN version from Google.
 * @return string the handle of the registered jQuery script
 */
function jquery_handle() {
  global $wp_version;

  return ( version_compare( $wp_version, '3.7', '>=' ) ? 'jquery-core' : 'jquery');
}

function jquery_deregister()
{
  $jquery_handle = jquery_handle();
  $exclude_pages = [ 'wp-login.php', 'wp-register.php' ];

  if ( is_admin() || in_array( $GLOBALS['pagenow'], $exclude_pages ) )
  {
    return false;
  }

  wp_deregister_script( $jquery_handle );

  return true;
}

function jquery_reregister()
{
  $jquery_wp_ver = $GLOBALS['wp_scripts']->registered[$jquery_handle]->ver ?? '1.12.2';
  $jquery_google = "//ajax.googleapis.com/ajax/libs/jquery/$jquery_wp_ver/jquery.min.js";
  $exclude_pages = [ 'wp-login.php', 'wp-register.php' ];

  wp_enqueue_script( 'jquery' );
  if ( jquery_deregister() )
  {
    wp_register_script( jquery_handle(), $jquery_google, null, null, true );
  }

}
