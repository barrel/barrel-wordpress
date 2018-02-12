<?php

/**
 * Uninstall SearchWP completely
 */

global $wpdb;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

include_once( 'searchwp.php' );

function searchwp_maybe_nuke() {
	global $wpdb;

	// maybe nuke all data and settings
	$swp_live_settings = searchwp_get_option( 'advanced' );
	$swp_nuke_on_delete = isset( $swp_live_settings['nuke_on_delete'] ) && ! empty( $swp_live_settings['nuke_on_delete'] );

	// Check for legacy nuke on delete option
	if ( empty( $swp_nuke_on_delete ) ) {
		$swp_live_settings = get_option( SEARCHWP_PREFIX . 'settings' );
		$swp_nuke_on_delete = isset( $swp_live_settings['nuke_on_delete'] ) ? $swp_live_settings['nuke_on_delete'] : false;
	}

	$swp_multisite = is_multisite() && function_exists( 'get_current_site' ) ? get_current_site() : null;

	if ( ! empty( $swp_nuke_on_delete ) || get_option( SEARCHWP_PREFIX . 'nuke_on_delete' ) || apply_filters( 'searchwp_nuke_on_delete', false, $swp_multisite ) ) {

		// purge the index including all post meta
		$searchwp = new SearchWP();
		$searchwp->purge_index();

		// deactivate the license
		if ( class_exists( 'SearchWP_Settings_Implementation_License' ) ) {
			$license_manager = new SearchWP_Settings_Implementation_License();
			$license_manager->deactivate_license();
		}

		// drop all custom database tables
		$tables = array( 'cf', 'index', 'log', 'media', 'tax', 'terms' );

		foreach ( $tables as $table ){
			$tableName = $wpdb->prefix . SEARCHWP_DBPREFIX . $table;

			// make sure the table exists
			if ( $tableName == $wpdb->get_var( "SHOW TABLES LIKE '$tableName'" ) ) {
				// drop it
				$sql = "DROP TABLE $tableName";
				$wpdb->query( $sql );
			}
		}

		// delete all plugin settings
		delete_option( SEARCHWP_PREFIX . 'settings' );
		delete_option( SEARCHWP_PREFIX . 'settings_backup' );
		delete_option( SEARCHWP_PREFIX . 'indexer' );
		delete_option( SEARCHWP_PREFIX . 'purge_queue' );
		delete_option( SEARCHWP_PREFIX . 'version' );
		delete_option( SEARCHWP_PREFIX . 'progress' );
		delete_option( SEARCHWP_PREFIX . 'license_key' );
		delete_option( SEARCHWP_PREFIX . 'paused' );
		delete_option( SEARCHWP_PREFIX . 'last_activity' );
		delete_option( SEARCHWP_PREFIX . 'busy' );
		delete_option( SEARCHWP_PREFIX . 'doing_delta' );
		delete_option( SEARCHWP_PREFIX . 'utf8mb4' );
		delete_option( SEARCHWP_PREFIX . 'advanced' );
		delete_option( SEARCHWP_PREFIX . 'waiting' );
		delete_option( SEARCHWP_PREFIX . 'delta_attempts' );

		// remove transients
		delete_option( 'searchwp_transient' );
		delete_option( 'swppurge_transient' );
	}
}

if ( is_multisite() ) {
	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
	if ( $blogs ) {
		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog['blog_id'] );
			searchwp_maybe_nuke();
			restore_current_blog();
		}
	}
} else {
	searchwp_maybe_nuke();
}
