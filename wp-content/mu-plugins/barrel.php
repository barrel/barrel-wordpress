<?php
/*
Plugin Name: Barrel
Plugin URI: https://www.barrelny.com/
Description: Common modules for any WordPress website.
Version: 0.1
Author: Barrel
Author URI: https://www.barrelny.com/
*/

if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) :

require_once( 'barrel/disable-oembed.php' );
require_once( 'barrel/disable-xml-rpc-client.php' );
require_once( 'barrel/enable-theme-scripts.php' );

endif; # Ensuring that this is on Pantheon
