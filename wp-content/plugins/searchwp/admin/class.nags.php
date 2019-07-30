<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Handle admin-side notifications (and their dismissals)
 *
 * Class SearchWP_Nags
 */
class SearchWP_Nags {

	/**
	 * SearchWP_Nags constructor.
	 */
	function __construct() {}

	function init() {
		// nag to call out indexer aggressiveness customization (disabled for now)
		// add_action( 'searchwp_settings_after_header', array( $this, 'settings_indexer_nag' ) );

		// call out an empty license
		add_action( 'searchwp_settings_after_header', array( $this, 'settings_license_nag' ) );

		// call out a version of MySQL known to have bugs that are likely to affect SearchWP
		add_action( 'searchwp_settings_after_header', array( $this, 'settings_mysql_version_nag' ) );

		// Searching in admin without interception enabled
		add_action( 'admin_footer', array( $this, 'admin_search_nag' ) );
	}

	/**
	 * Implement a nag
	 *
	 * @param array $args
	 *
	 * @return array|bool
	 */
	function implement_nag( $args = array() ) {

		$defaults = array(
			'name' => 'nag',
			'nonce' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$searchwp = SWP();

		if ( empty( $args['name'] ) ) {
			return false;
		}

		if ( empty( $args['nonce'] ) ) {
			$args['nonce'] = $args['name'];
		}

		$nag_name   = sanitize_text_field( $args['name'] );
		$nonce_key  = sanitize_text_field( $args['nonce'] );

		if (
			isset( $_REQUEST[ $nonce_key ] )
			&& wp_verify_nonce( $_REQUEST[ $nonce_key ], $nag_name )
			&& current_user_can( $searchwp->settings_cap )
		) {
			// this key stores all the dismissed nags
			$dismissed = searchwp_get_setting( 'dismissed' );

			if ( is_array( $dismissed ) ) {
				if ( isset( $dismissed['nags'] ) && is_array( $dismissed['nags'] ) ) {
					$dismissed['nags'][] = $nag_name;
				} else {
					$dismissed['nags'] = array( $nag_name );
				}
			} else {
				$dismissed = array(
					'nags' => array( $nag_name )
				);
			}
			searchwp_set_setting( 'dismissed', $dismissed );
		}

		$nags = searchwp_get_setting( 'nags', 'dismissed' );
		$nag_dismissed = is_array( $nags ) && in_array( $nag_name, $nags, true );

		$dismissal_link = add_query_arg(
			array(
				'page' => 'searchwp',
				$nonce_key => wp_create_nonce( $nag_name ),
			)
		);

		return array(
			'name'              => $nag_name,
			'nonce'             => $nonce_key,
			'dismissed'         => $nag_dismissed,
			'dismissal_link'    => $dismissal_link,
		);
	}

	/**
	 * Output the indexer aggressiveness nag
	 */
	function settings_indexer_nag() {
		$nag = $this->implement_nag( array(
			'name'      => 'indexer',
			'nonce'     => 'searchwpnaginonce',
		) );

		if ( ! $nag['dismissed'] ) : ?>
			<div class="updated swp-progress-notes">
				<p class="description"><?php echo wp_kses( sprintf( __( 'The SearchWP indexer runs as fast as it can without overloading your server; there are filters to customize it\'s aggressiveness. <a href="%s">Find out more &raquo;</a> <a class="swp-dismiss" href="%s">Dismiss</a>', 'searchwp' ), 'http://searchwp.com/?p=11818', esc_url( $nag['dismissal_link'] ) ) , array( 'a' => array( 'class' => array(), 'href' => array() ) ) ); ?></p>
			</div>
		<?php endif;
	}

	/**
	 * Output the admin_search nag
	 */
	function admin_search_nag() {
		$nag = $this->implement_nag( array(
			'name'      => 'admin_search',
			'nonce'     => 'swpadminsearchnag',
		) );

		$search_in_admin = apply_filters( 'searchwp_in_admin', false );

		$dismiss = remove_query_arg( 'page', $nag['dismissal_link'] );

		if ( is_admin() && is_search() && empty( $search_in_admin ) && ! $nag['dismissed'] ) : ?>
			<div class="notice notice-error" style="position: relative; padding-right: 38px;">
				<p><?php echo wp_kses( sprintf( __( 'SearchWP is NOT intercepting admin searches <a href="%s">Find out more &raquo;</a>', 'searchwp' ), 'http://searchwp.com/?p=161276' ) , array( 'a' => array( 'class' => array(), 'href' => array() ) ) ); ?></p>
				<a style="text-decoration: none;" href="<?php echo esc_url( $dismiss ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>
			</div>
		<?php endif;
	}

	/**
	 * Output a nag if admin searching is enabled but this post type wasn't added to the admin engine
	 *
	 * @since 3.0.6
	 */
	function admin_search_post_type_nag() {
		$nag = $this->implement_nag( array(
			'name'      => 'admin_search_post_type',
			'nonce'     => 'swpadminsearchtypenag',
		) );

		$dismiss = remove_query_arg( 'page', $nag['dismissal_link'] );

		if ( is_admin() && is_search() && ! $nag['dismissed'] ) : ?>
			<div class="notice notice-error" style="position: relative; padding-right: 38px;">
				<p><?php echo wp_kses( sprintf( __( 'This post type is <strong>NOT</strong> added to your SearchWP admin engine. The default WordPress search results are shown. <a href="%s">Find out more &raquo;</a>', 'searchwp' ), 'http://searchwp.com/?p=161276' ) , array( 'a' => array( 'class' => array(), 'href' => array() ), 'strong' => array() ) ); ?></p>
				<a style="text-decoration: none;" href="<?php echo esc_url( $dismiss ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>
			</div>
		<?php endif;
	}

	function debug_filesize_nag() {
		$nag = $this->implement_nag( array(
			'name'      => 'debug_log_size',
			'nonce'     => 'swpdebuglogsizenag',
		) );

		$dismiss = remove_query_arg( 'page', $nag['dismissal_link'] );

		if ( is_admin() && ! $nag['dismissed'] ) : ?>
			<div class="notice notice-error" style="position: relative; padding-right: 38px;">
				<p>
					<?php
					echo wp_kses(
						sprintf(
							// Translators: placeholder is the folder path to the debug log file.
							__( 'Your SearchWP debug log has exceeded 2MB in size. You can delete %1$s when you are done.', 'searchwp' ),
							'<code>~/' . searchwp_get_relative_upload_path() . '/searchwp-debug.text</code>'
						),
						array(
							'code' => array(),
						)
					);
					?>
				</p>

				<a style="text-decoration: none;" href="<?php echo esc_url( $dismiss ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>
			</div>
		<?php endif;
	}

	/**
	 * Output the license nag
	 */
	function settings_license_nag() {
		$nag = $this->implement_nag( array(
			'name'      => 'license',
			'nonce'     => 'searchwpnagnnonce',
		) );

		$searchwp = SWP();

		$notices = searchwp_get_setting( 'notices' );
		$initial_notified = ( is_array( $notices ) && in_array( 'initial', $notices, true ) ) ? true : false;

		if (
			false === $initial_notified // don't show unless the initial index has been built
			&& ! empty( $searchwp->license ) // only show if a license has been entered
			&& ( isset( $searchwp->status ) && 'valid' !== $searchwp->status ) // and the license is not valid
			&& ! $nag['dismissed'] // and the nag hasn't been dismissed
			&& apply_filters( 'searchwp_initial_license_nag', true ) // and let devs hide it anyway
		) : ?>
			<div id="setting-error-settings_updated" class="updated settings-error swp-license-nag">
				<p><?php esc_html_e( 'In order to receive updates and support, you must have an active license.', 'searchwp' ); ?> <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'searchwp', 'tab' => 'license' ), admin_url( 'options-general.php' ) ) ); ?>"><?php esc_html_e( 'Manage License', 'searchwp' ); ?></a> <a href="<?php echo esc_url( SEARCHWP_EDD_STORE_URL ); ?>"><?php esc_html_e( 'Purchase License', 'searchwp' ); ?></a> <a href="<?php echo esc_url( $nag['dismissal_link'] ); ?>"><?php esc_html_e( 'Dismiss', 'searchwp' ); ?></a></p>
			</div>
		<?php endif;
	}

	/**
	 * Output MySQL buggy version nag
	 */
	function settings_mysql_version_nag() {
		global $wpdb;

		$nag = $this->implement_nag( array(
			'name'      => 'mysql_version',
			'nonce'     => 'searchwpnagvnonce',
		) );

		if ( ! version_compare( '5.2', $wpdb->db_version(), '<' )  && ! $nag['dismissed'] ) : ?>
			<div class="updated settings-error">
				<p><?php echo wp_kses( sprintf( __( 'Your server is running MySQL version %1$s which may prevent search results from appearing due to <a href="http://bugs.mysql.com/bug.php?id=41156">bug 41156</a>. Please update MySQL to a more recent version (at least 5.2).', 'searchwp' ), $wpdb->db_version() ), array( 'a' => array( 'href' => array() ) ) ); ?> <a href="<?php echo esc_url( $nag['dismissal_link'] ); ?>"><?php esc_html_e( 'Dismiss', 'searchwp' ); ?></a></p>
			</div>
		<?php endif;
	}
}
