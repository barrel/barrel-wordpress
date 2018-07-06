<?php
/*
Plugin Name: Disable XML RPC
Plugin URI: https://gitlab.com/barrel/barrel-wordpress/tree/master/wp-content/mu-plugins
Description: Common modules for any WordPress website.
Version: 0.1
Author: Barrel
Author URI: https://www.barrelny.com/
*/

add_filter( 'xmlrpc_enabled', '__return_false' );
