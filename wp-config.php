<?php
/**
 * This config file is yours to hack on. It will work out of the box on Pantheon
 * but you may find there are a lot of neat tricks to be used here.
 *
 * See our documentation for more details:
 *
 * https://pantheon.io/docs
 */

/**
 * Pantheon platform settings. Everything you need should already be set.
 */
if (file_exists(dirname(__FILE__) . '/wp-config-pantheon.php') && isset($_ENV['PANTHEON_ENVIRONMENT'])) {
	require_once(dirname(__FILE__) . '/wp-config-pantheon.php');

/**
 * Local configuration information.
 *
 * If you are working in a local/desktop development environment and want to
 * keep your config separate, we recommend using a 'wp-config-local.php' file,
 * which you should also make sure you .gitignore.
 */
} elseif (file_exists(dirname(__FILE__) . '/wp-config-local.php') && !isset($_ENV['PANTHEON_ENVIRONMENT'])){
	# IMPORTANT: ensure your local config does not include wp-settings.php
	require_once(dirname(__FILE__) . '/wp-config-local.php');

/**
 * This block will be executed if you are NOT running on Pantheon and have NO
 * wp-config-local.php. Insert alternate config here if necessary.
 *
 * If you are only running on Pantheon, you can ignore this block.
 */
} else {
	define('DB_NAME',          'database_name');
	define('DB_USER',          'database_username');
	define('DB_PASSWORD',      'database_password');
	define('DB_HOST',          'database_host');
	define('DB_CHARSET',       'utf8');
	define('DB_COLLATE',       '');
	define('AUTH_KEY',         'put your unique phrase here');
	define('SECURE_AUTH_KEY',  'put your unique phrase here');
	define('LOGGED_IN_KEY',    'put your unique phrase here');
	define('NONCE_KEY',        'put your unique phrase here');
	define('AUTH_SALT',        'put your unique phrase here');
	define('SECURE_AUTH_SALT', 'put your unique phrase here');
	define('LOGGED_IN_SALT',   'put your unique phrase here');
	define('NONCE_SALT',       'put your unique phrase here');
}


/** Standard wp-config.php stuff from here on down. **/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * You may want to examine $_ENV['PANTHEON_ENVIRONMENT'] to set this to be
 * "true" in dev, but false in test and live.
 */
if ( !empty( $_SERVER['PANTHEON_ENVIRONMENT'] ) && ( "cli" !== php_sapi_name() ) ) {
  // set debug to true in all environments except live
  if ( "live" !== $_SERVER['PANTHEON_ENVIRONMENT'] && !defined( 'WP_DEBUG' ) ) {
    define('WP_DEBUG', true);
  }

  // upgrade to https if headers forwarded from CDN like Cloudflare and terminating https
  if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) 
    && 'https' == strtolower( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ) {
    $_SERVER['HTTPS'] = 'on';
  }

  // some services use SERVER_NAME, which is unreliable here. This seems to fix those issues.
  $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
  $_SERVER['SERVER_PORT'] = ( 
    isset( $_SERVER['HTTP_X_SSL'] ) && 'ON' === strtoupper( $_SERVER['HTTP_X_SSL'] ) ||
    @$_SERVER['HTTPS'] === 'on'
  ) ? 443 : 80;

  // Redirect Logic
  $primary_domain = "example.org";
  $redirect_domains = array(
    "live-example.pantheonsite.io",
  );
  $protocol = "https";
  $with_www = "www."; // set to empty string for false
  $_http_host = str_replace( "www.", "", $_SERVER['HTTP_HOST'] );
  $_request_uri = $_SERVER['REQUEST_URI'];
  $_url_redirect = "$protocol://$with_www" . $primary_domain . $_request_uri;
  
  if ( in_array( $_SERVER['PANTHEON_ENVIRONMENT'], array( "live" ) ) ) {
    require_once(dirname(__FILE__) . '/pantheon-redirects.php');

    // automatically redirect if host other than primary domain is detected
    if ( in_array( $_http_host, $redirect_domains ) ) {
      header("HTTP/1.1 301 Moved Permanently");
      header("Location: $_url_redirect");
      exit;
    }

    // automatically redirect specific paths from old site
    foreach( $one_to_ones as $requestPath => $redirect_to ) {
      if ( strpos( $_request_uri, $requestPath ) !== false ) {
        header("HTTP/1.1 301 Moved Permanently"); 
        header("Location: $redirect_to"); 
        exit;
      }
    }

    // automatically redirect based on rules
    foreach( $regex_rules as $regex => $replace ) {
      if ( @preg_match( $regex, $_request_uri ) ) {
        $replacement = preg_replace( $regex, $replace, $_request_uri, -1 );
        header("HTTP/1.1 301 Moved Permanently"); 
        header("Location: $replacement"); 
        exit;
      }
    }

    // Require HTTPS when $protocol set to https
    if ( "https" == $protocol && ( !isset( $_SERVER['HTTP_USER_AGENT_HTTPS'] ) 
    || $_SERVER['HTTP_USER_AGENT_HTTPS'] != 'ON' ) ) {
      header("HTTP/1.1 301 Moved Permanently");
      header("Location: $_url_redirect");
      exit();
    }
  }
}
if ( ! defined( 'WP_DEBUG' ) ) {
	define('WP_DEBUG', false);
}

/* That's all, stop editing! Happy Pressing. */




/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
