<?php

// based on Easy Digital Downloads System Info by Chris Christoff

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_System_Info
 */
class SearchWP_System_Info {

	private $searchwp;

	/**
	 * SearchWP_System_Info constructor.
	 */
	function __construct() {
		$this->searchwp = SWP();
	}

	/**
	 * Output System Info
	 */
	function output() {
		global $wpdb;

		$theme_data = wp_get_theme();
		/** @noinspection PhpUndefinedFieldInspection */
		$theme = $theme_data->Name . ' ' . $theme_data->Version;

		// Try to identifty the hosting provider
		$host = false;
		if ( defined( 'WPE_APIKEY' ) ) {
			$host = 'WP Engine';
			if ( defined( 'WPE_GOVERNOR' ) && false === WPE_GOVERNOR ) {
				$host .= ' (WPE_GOVERNOR disabled)';
			} else {
				$host .= ' (WPE_GOVERNOR enabled)';
			}
		} elseif ( defined( 'PAGELYBIN' ) ) {
			$host = 'Pagely';
		}

		$utf8mb4_failed_upgrade = false;
		if ( searchwp_get_option( 'utf8mb4_upgrade_failed' ) ) {
			$utf8mb4_failed_upgrade = true;
		}

		?>
	<form action="" method="post" dir="ltr">
		<textarea readonly="readonly" onclick="this.focus();this.select()" class="searchwp-system-info-textarea" name="searchwp-sysinfo" title="<?php esc_attr_e( 'To copy the system info, click below then press CTRL + C (PC) or CMD + C (Mac).', 'searchwp' ); ?>">
### Begin System Info ###

## Please include this information when posting support requests ##

<?php if ( $utf8mb4_failed_upgrade ) : ?>
Failed utf8mb4 upgrade:   Yes
<?php endif; ?>

Multisite:                <?php echo esc_textarea( is_multisite() ? 'Yes' : 'No' ) . "\n"; ?>

SITE_URL:                 <?php echo esc_url( site_url() ) . "\n"; ?>
HOME_URL:                 <?php echo esc_url( home_url() ) . "\n"; ?>

SearchWP Version:         <?php echo esc_textarea( $this->searchwp->version ) . "\n"; ?>
SearchWP License:         <?php echo esc_textarea( searchwp_get_license_key() ) . "\n"; ?>
WordPress Version:        <?php echo esc_textarea( get_bloginfo( 'version' ) ) . "\n"; ?>
Permalink Structure:      <?php echo esc_textarea( get_option( 'permalink_structure' ) ) . "\n"; ?>
Active Theme:             <?php echo esc_textarea( $theme ) . "\n"; ?>
<?php if ( $host ) : ?>
Host:                     <?php echo esc_textarea( $host ) . "\n"; ?>
<?php endif; ?>

Registered Post Stati:    <?php echo esc_textarea( implode( ', ', get_post_stati() ) ) . "\n"; ?>

PHP Version:              <?php echo esc_textarea( PHP_VERSION ) . "\n"; ?>
MySQL Version:            <?php echo esc_textarea( $wpdb->db_version() ) . "\n"; ?>
Web Server Info:          <?php echo esc_textarea( $_SERVER['SERVER_SOFTWARE'] ) . "\n"; ?>

WordPress Memory Limit:   <?php echo esc_textarea( WP_MEMORY_LIMIT ) . "\n"; ?>
PHP Safe Mode:            <?php echo esc_textarea( ini_get( 'safe_mode' ) ? 'Yes' : 'No' ) . "\n"; ?>
PHP Memory Limit:         <?php echo esc_textarea( ini_get( 'memory_limit' ) ) . "\n"; ?>
PHP Time Limit:           <?php echo esc_textarea( ini_get( 'max_execution_time' ) ) . "\n"; ?>

WP_DEBUG:                 <?php echo esc_textarea( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'Enabled' : 'Disabled' ) . "\n"; ?>

WP Table Prefix:          <?php echo 'Length: '. absint( strlen( $wpdb->prefix ) ); echo ' Status:'; if ( strlen( $wpdb->prefix ) > 16 ) { echo ' ERROR: Too Long'; } else { echo ' Acceptable'; } echo "\n"; ?>

<?php
$request['cmd'] = '_notify-validate';

$params = array(
	'sslverify'		=> false,
	'timeout'		=> 60,
	'user-agent'	=> 'SearchWP',
	'body'			=> $request,
);

$response = wp_remote_post( 'https://searchwp.com/', $params );

if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
	$WP_REMOTE_POST = 'wp_remote_post() works' . "\n";
} else {
	$WP_REMOTE_POST = 'wp_remote_post() does not work' . "\n";
}
?>
WP Remote Post:           <?php echo esc_textarea( $WP_REMOTE_POST ); ?>

TEMPLATES:

search.php                <?php echo file_exists( get_stylesheet_directory() . '/search.php' ) ? 'Yes' : 'No'; ?>


POTENTIAL TEMPLATE CONFLICTS:

<?php
$conflicts = new SearchWP_Conflicts();
if ( ! empty( $conflicts->search_template_conflicts ) ) {
	foreach ( $conflicts->search_template_conflicts as $line_number => $the_conflicts ) {
		echo esc_textarea( 'Line ' . absint( $line_number ) . ': ' . implode( ', ', $the_conflicts ) ) . "\n";
	}
} else {
	echo "NONE\n";
}
?>

POTENTIAL FILTER CONFLICTS

<?php
if ( ! empty( $conflicts->filter_conflicts ) ) {
	foreach ( $conflicts->filter_conflicts as $filter_name => $potential_conflict ) {
		foreach ( $potential_conflict as $conflict ) {
			echo esc_textarea( $filter_name . ' => ' . $conflict ) . "\n";
		}
	}
} else {
	echo "NONE\n";
}
?>

ACTIVE PLUGINS:

<?php
$plugins = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $plugins as $plugin_path => $plugin ) {
	// if the plugin isn't active, don't show it.
	if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
		continue;
	}

	echo esc_textarea( $plugin['Name'] . ': ' . $plugin['Version'] ) . "\n";
}

if ( is_multisite() ) :
	?>

	NETWORK ACTIVE PLUGINS:

	<?php
	$plugins = wp_get_active_network_plugins();
	$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

	foreach ( $plugins as $plugin_path ) {
		$plugin_base = plugin_basename( $plugin_path );

		// If the plugin isn't active, don't show it.
		if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
			continue;
		}

		$plugin = get_plugin_data( $plugin_path );

		echo esc_textarea( $plugin['Name'] . ' :' . $plugin['Version'] ) . "\n";
	}

endif; ?>

STATS:

<?php

if ( isset( $this->searchwp->settings['stats'] ) ) {
	if ( ! empty( $this->searchwp->settings['stats']['last_activity'] ) ) {
		$this->searchwp->settings['stats']['last_activity'] = human_time_diff( $this->searchwp->settings['stats']['last_activity'], current_time( 'timestamp' ) ) . ' ago';
	}
	echo esc_textarea( print_r( $this->searchwp->settings['stats'], true ) );
	echo "\n";
} else {
	echo esc_textarea( print_r( get_option( SEARCHWP_PREFIX . 'settings' ), true ) );
	echo "\n";
}

echo 'Index up to date: ';
$index_dirty = searchwp_get_setting( 'index_dirty' );
if ( $index_dirty ) {
	echo 'No';
} else {
	echo 'Yes';
}
echo "\n";

$indexer = new SearchWPIndexer();
$row_count = $indexer->get_main_table_row_count();
echo 'Main table row count: ';
echo absint( $row_count );
echo "\n";
if ( isset( $this->searchwp->settings['running'] ) ) {
	echo 'Running: ';
	echo ! empty( $this->searchwp->settings['running'] ) ? 'Yes' : 'No';
	echo "\n";
}
if ( isset( $this->searchwp->settings['busy'] ) ) {
	echo 'Busy: ';
	echo ! empty( $this->searchwp->settings['busy'] ) ? 'Yes' : 'No';
	echo "\n";
}
if ( isset( $this->searchwp->settings['doing_delta'] ) ) {
	echo 'Doing Delta: ';
	echo ! empty( $this->searchwp->settings['running'] ) ? 'Yes' : 'No';
	echo "\n";
}
if ( isset( $this->searchwp->settings['processing_purge_queue'] ) ) {
	echo 'Processing Purge Queue: ';
	echo ! empty( $this->searchwp->settings['processing_purge_queue'] ) ? 'Yes' : 'No';
	echo "\n";
}
if ( isset( $this->searchwp->settings['paused'] ) ) {
	echo 'Paused: ';
	echo ! empty( $this->searchwp->settings['paused'] ) ? 'Yes' : 'No';
	echo "\n";
}
?>

SETTINGS:

<?php if ( isset( $this->searchwp->settings['engines'] ) ) { echo esc_textarea( print_r( $this->searchwp->settings['engines'], true ) ); } ?>

PURGE QUEUE:

<?php echo isset( $this->searchwp->settings['purgeQueue'] ) ? esc_textarea( print_r( $this->searchwp->settings['purgeQueue'], true ) ) : '[Empty]'; ?>


### End System Info ###</textarea></form>
	<?php }

}
