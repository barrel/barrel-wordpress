<?php

/**
 * Re-registers jQuery in a safe manner in order to serve the CDN version from Google.
 * @return string the handle of the registered jQuery script
 */
function reregister_jquery() {
	global $wp_version;
	if ( is_admin() || in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) ) return;
	wp_enqueue_script( 'jquery' );
	// Check to see if we're on 3.6 or newer (changed the jQuery handle)
	$jquery_handle = ( version_compare( $wp_version, '3.7', '>=' ) ? 'jquery-core' : 'jquery');
	$wp_jquery_ver = $GLOBALS['wp_scripts']->registered[$jquery_handle]->ver;
	$temp_jquery_ver = '1.12.2';
	$jquery_google_url = '//ajax.googleapis.com/ajax/libs/jquery/'.$temp_jquery_ver.'/jquery.min.js';
	wp_deregister_script( $jquery_handle );
	wp_register_script( $jquery_handle, $jquery_google_url, '', null, true );
	return $jquery_handle;
}

