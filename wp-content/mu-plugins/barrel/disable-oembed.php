<?php
/*
Plugin Name: Disable Oembed Scripts
Plugin URI: https://gitlab.com/barrel/barrel-wordpress/tree/master/wp-content/mu-plugins
Description: Common modules for any WordPress website.
Version: 0.1
Author: Barrel
Author URI: https://www.barrelny.com/
*/

add_action( 'wp_footer', function () {
	wp_deregister_script( 'wp-embed' );
} );