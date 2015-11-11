<?php

/**
 * =====================================================
 *
 * Setup Localization Efforts
 *
 * =====================================================
 */

function kind_theme_localization(){
    load_theme_textdomain('kindsnacks', THEME_DIR . '/languages');
}
add_action('after_setup_theme', 'kind_theme_localization');

if (!defined('THEME_SAFE_LOCALE')) {
	$_locale = str_replace('_', '-', get_locale());
	define('THEME_SAFE_LOCALE', (!empty($_locale) ? $_locale : 'en-US'));
}

/**
 * =====================================================
 *
 * Include ACF
 *
 * =====================================================
 */
include_once(ABSPATH.'/wp-admin/includes/plugin.php');
$acf_pro_path = 'advanced-custom-fields-pro/acf.php';
if ( is_plugin_active($acf_pro_path) ) : 
else :
	function acf_admin_notice() {
		$screen = get_current_screen();
		if( is_admin() ): ?>

	<div class="error">
	    <p><?php _e( "ACF Pro is required for this theme to function correctly.", 'barrel-base' ); ?></p>
	</div><?php 
		endif;
	}
	add_action( 'admin_notices', 'acf_admin_notice' );
endif;
