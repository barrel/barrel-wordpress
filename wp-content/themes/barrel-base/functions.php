<?php
define('IS_DEV', isset( $_SERVER['HTTP_X_DEV'] ) ? true : false );

// Backend only
include_once( __DIR__ . '/lib/class-theme-init.php' );

// Global helpers
require_once( __DIR__ . '/lib/helpers/wordpress.php' );
require_once( __DIR__ . '/lib/helpers/modules.php' );
require_once( __DIR__ . '/lib/helpers/scripts.php' );
require_once( __DIR__ . '/lib/helpers/media.php' );
require_once( __DIR__ . '/lib/helpers/acf.php' );
