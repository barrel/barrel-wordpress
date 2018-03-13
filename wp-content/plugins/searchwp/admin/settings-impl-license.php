<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SearchWP_Settings_Implementation_License
 */
class SearchWP_Settings_Implementation_License {

	/**
	 *
	 */
	function init() {

		// render the 'License' tab on the settings screen
		add_action( 'searchwp_settings_nav_tab', array( $this, 'render_tab_license' ), 9999 );

		// render the 'License' view when the 'License' tab is viewed
		add_action( 'searchwp_settings_view\license', array( $this, 'render_view_license' ) );

		// internal actions for processing license activation/deactivation
		add_action( 'admin_init', array( $this, 'init_settings' ), 1 );
		add_action( 'admin_init', array( $this, 'activate_license' ), 1 );
		add_action( 'admin_init', array( $this, 'deactivate_license_check' ), 1 );

		add_action( 'searchwp_settings_footer', array( $this, 'maybe_inactive_license' ) );
	}

	/**
	 * Callback to render the settings nav for the License screen
	 */
	function render_tab_license() {
		if ( current_user_can( apply_filters( 'searchwp_settings_cap', 'manage_options' ) ) ) {
			$searchwp = SWP();
			$status = $searchwp->status;
			$classes = ( false === $status || 'valid' !== $status ) ? 'searchwp-tab-license-inactive' : '';
			searchwp_get_nav_tab( array(
				'tab'       => 'license',
				'label'     => ( false === $status || 'valid' !== $status ) ? __( 'Activate License', 'searchwp' ) : __( 'License', 'searchwp' ),
				'classes'   => $classes,
			) );
		}
	}

	/**
	 * Outputs a notice that a license has been entered but it's invalid
	 */
	function maybe_inactive_license() {
		$searchwp = SWP();
		if ( ( false !== $searchwp->license && '' !== $searchwp->license ) && 'valid' !== $searchwp->status ) : ?>
			<div id="setting-error-settings_updated" class="error settings-error updated notice">
				<p><?php echo wp_kses( __( 'A license key was found, but it is <strong>inactive</strong>. Automatic updates <em>will not be available</em> until your license is activated.', 'searchwp' ), array( 'strong' => array(), 'em' => array() ) ); ?> <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'searchwp', 'tab' => 'license' ), admin_url( 'options-general.php' ) ) ); ?>"><?php esc_html_e( 'Manage License', 'searchwp' ); ?> &raquo;</a></p>
				<p><?php echo wp_kses( sprintf( __( 'Having trouble activating your license? Please see <a href="%s">this KB article &raquo;</a>' , 'searchwp' ), 'https://searchwp.com/?p=29213' ), array( 'a' => array( 'href' => array() ) ) ); ?></p>
			</div>
		<?php endif;
	}

	/**
	 * Returns human-readable time until an active license expires
	 *
	 * @return string
	 */
	function get_time_until_expiration() {
		// license expiration is stored as a timestamp or 'never'
		$expiration = get_option( SEARCHWP_PREFIX . 'license_expiration' );

		if ( 'never' === $expiration ) {
			return 'never';
		}

		$license_expiration = absint( trim( $expiration ) );
		$license_expiration_readable = $license_expiration ? human_time_diff( current_time( 'timestamp' ), $license_expiration ) : __( 'License not active', 'searchwp' );

		return $license_expiration_readable;
	}

	/**
	 * Outputs the HTML to manage the license key
	 */
	function render_view_license() {
		$searchwp = SWP();

		$license = get_option( SEARCHWP_PREFIX . 'license_key' );
		$license_coded = false;

		// Only display the license if it is in fact stored in the database, allow constant or filter definition to obscure it
		if ( defined( 'SEARCHWP_LICENSE_KEY' ) ) {
			$license = '*************';
			$license_coded = true;
		}

		$filtered_license = apply_filters( 'searchwp_license_key', '' );
		if ( ! empty( $filtered_license ) ) {
			$license = '*************';
			$license_coded = true;
		}

		$status = $searchwp->status;
		?>
		<div class="searchwp-license-settings-wrapper swp-group">
			<div class="postbox swp-meta-box metabox-holder searchwp-settings-license">
				<h3 class="hndle">
					<span><?php esc_html_e( 'Manage Your SearchWP License', 'searchwp' ); ?>
						<?php if ( false !== $status && 'valid' === $status ) : ?> <b class="active"><?php esc_html_e( 'Active', 'searchwp' ); ?></b><?php else : ?><b class="inactive"><?php esc_html_e( 'Inactive', 'searchwp' ); ?></b><?php endif; ?>
					</span></h3>
				<div class="inside">
					<?php if ( false !== $status && 'valid' === $status ) : ?>
						<p><?php esc_html_e( 'Your SearchWP license is currently active.', 'searchwp' ); ?></p>
					<?php else : ?>
						<p><?php esc_html_e( 'SearchWP requires an active license to receive automatic upates and support. Enter your license key to activate it.', 'searchwp' ); ?></p>
					<?php endif; ?>
					<form method="post" action="options.php">
						<?php settings_fields( SEARCHWP_PREFIX . 'license' ); ?>
						<?php if ( ! empty( $license_coded ) ) : ?>
							<?php if ( defined( 'SEARCHWP_LICENSE_KEY' ) ) : ?>
								<p><strong><?php echo esc_html_e( 'Note:', 'searchwp' ); ?></strong> <?php echo esc_html_e( 'Your license key is populated using this constant:', 'searchwp' ); ?> <code>SEARCHWP_LICENSE_KEY</code></p>
							<?php else : ?>
								<p><strong><?php echo esc_html_e( 'Note:', 'searchwp' ); ?></strong> <?php echo esc_html_e( 'Your license key is populated using this hook:', 'searchwp' ); ?> <code>searchwp_license_key</code></p>
							<?php endif; ?>
						<?php endif; ?>
						<p>
							<!--suppress HtmlFormInputWithoutLabel -->
							<input id="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>license_key" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>license_key" type="text" class="regular-text" value="<?php echo esc_attr( $license ); ?>" <?php if ( ! empty( $license_coded ) ) : ?>disabled="disabled" <?php endif; ?>/>
							<?php if ( ! empty( $license_coded ) ) : ?>
								<input id="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>license_key_coded" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>license_key_coded" type="hidden" value="1"/>
							<?php endif; ?>
							<?php if ( false !== $status && 'valid' === $status ) { ?>
								<?php wp_nonce_field( 'searchwp_edd_license_deactivate_nonce', 'searchwp_edd_license_deactivate_nonce' ); ?>
								<input type="submit" class="button-secondary" name="swp_edd_license_deactivate" value="<?php esc_html_e( 'Deactivate', 'searchwp' ); ?>" />
							<?php } else {
								wp_nonce_field( 'searchwp_edd_license_activate_nonce', 'searchwp_edd_license_activate_nonce' ); ?>
								<input type="submit" class="button-secondary" name="swp_edd_license_activate" value="<?php esc_html_e( 'Activate', 'searchwp' ); ?>" />
							<?php } ?>
						</p>
					</form>
					<?php if ( false !== $status && 'valid' === $status ) : ?>
						<p class="description"><?php
						$expiration = $this->get_time_until_expiration();
						if ( 'never' === $expiration ) {
							echo esc_html_e( 'Does not expire', 'searchwp' );
						} else {
							echo esc_html( sprintf( __( 'Active for another %s', 'searchwp' ), $expiration ) );
						}
						?></p>
					<?php else : ?>
						<p class="description"><?php echo wp_kses( sprintf( __( 'Your license key is available both on your payment receipt and in your <a href="%s">Account</a>', 'searchwp' ), 'https://searchwp.com/account/' ), array( 'a' => array( 'href' => array() ) ) ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<style type="text/css">
			.swp-notices .updated {
				display: none !important;
			}
			.searchwp-settings-license h3 b {
				display:inline-block;
				border-radius:2px;
				color:#fff;
				font-weight:normal;
				padding:0.3em 0.6em 0.4em;
				margin-left:0.7em;
				font-size:0.8em;
				line-height:1;
				position:relative;
				top:-0.1em;
			}
			.searchwp-settings-license h3 b.active {
				background:#75A575;
			}
			.searchwp-settings-license h3 b.inactive {
				background:#C55959;
			}
		</style>
		<?php
	}

	/**
	 * Activate license
	 *
	 * @return bool Whether the license was activated
	 * @since 1.0
	 */
	function activate_license() {
		// listen for our activate button to be clicked
		if ( isset( $_POST['swp_edd_license_activate'] ) ) {

			do_action( 'searchwp_log', 'activate_license()' );

			// run a quick security check
			if ( ! check_admin_referer( 'searchwp_edd_license_activate_nonce', 'searchwp_edd_license_activate_nonce' ) ) {
				return false; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			$license = searchwp_get_license_key();
			$license = sanitize_text_field( $license );

			// edge case: a license was deactivated and removed and this is a subsequent re-activation...
			// the database record is empty because the Settings API hasn't saved it yet, but it's still in POST
			if ( empty( $license ) && isset( $_REQUEST['searchwp_license_key'] ) && ! empty( $_REQUEST['searchwp_license_key'] ) ) {
				$license = sanitize_text_field( $_REQUEST['searchwp_license_key'] );
			}

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'url'        => esc_url( home_url() ),
				'item_name'  => urlencode( SEARCHWP_EDD_ITEM_NAME ) // the name of our product in EDD
			);

			// Call the custom API.
			$api_args = array(
				'timeout'   => 30,
				'sslverify' => false,
				'body'      => $api_params,
			);
			$response = wp_remote_post( SEARCHWP_EDD_STORE_URL, $api_args );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "valid" or "invalid"
			$status = sanitize_text_field( $license_data->license );
			$result = update_option( SEARCHWP_PREFIX . 'license_status', $status );

			// also record the expiration date
			if ( isset( $license_data->expires ) ) {

				if ( 'lifetime' !== $license_data->expires ) {
					$expiration = $license_data->expires;
					$expiration = date( 'U', strtotime( $expiration ) );
					$expiration = absint( $expiration );
				} else {
					$expiration = 'never';
				}

				update_option( SEARCHWP_PREFIX . 'license_expiration', $expiration );
			}

			return true;
		}

		return false;
	}

	/**
	 * Check to see if we need to deactivate the license
	 *
	 * @return bool
	 * @since 1.0
	 */
	function deactivate_license_check() {
		// listen for our activate button to be clicked
		if ( isset( $_POST['swp_edd_license_deactivate'] ) ) {

			do_action( 'searchwp_log', 'deactivate_license_check()' );

			// run a quick security check
			if ( ! check_admin_referer( 'searchwp_edd_license_deactivate_nonce', 'searchwp_edd_license_deactivate_nonce' ) ) {
				return false; // get out if we didn't click the Activate button
			}

			$this->deactivate_license();

			return true;
		}

		return false;
	}

	/**
	 * Deactivate license
	 *
	 * @return bool
	 * @since 1.0
	 */
	function deactivate_license() {
		do_action( 'searchwp_log', 'deactivate_license()' );

		// retrieve the license from the database
		$license = searchwp_get_license_key();
		$license = sanitize_text_field( $license );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'url'        => esc_url( home_url() ),
			'item_name'  => urlencode( SEARCHWP_EDD_ITEM_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$api_args = array(
			'timeout'   => 30,
			'sslverify' => false,
			'body'      => $api_params,
		);
		$response = wp_remote_post( SEARCHWP_EDD_STORE_URL, $api_args );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( 'deactivated' === $license_data->license ) {
			delete_option( SEARCHWP_PREFIX . 'license_status' );
			delete_option( SEARCHWP_PREFIX . 'license_expiration' );
		}

		return true;
	}

	/**
	 * Callback that initializes the license key storage
	 */
	function init_settings() {
		register_setting(
			SEARCHWP_PREFIX . 'license',
			SEARCHWP_PREFIX . 'license_key',
			array( $this, 'sanitize_license' )
		);
	}

	/**
	 * Sanitize the license
	 *
	 * @param $new
	 *
	 * @return mixed
	 * @since 1.0
	 */
	function sanitize_license( $new ) {

		$old = searchwp_get_license_key();

		// This only applies if license is NOT populated via constant/hook
		if ( ! isset( $_REQUEST[ SEARCHWP_PREFIX . 'license_key_coded'] ) ){
			if ( $old && $old !== $new ) {
				delete_option( SEARCHWP_PREFIX . 'license_status' ); // new license has been entered, so must reactivate
				delete_option( SEARCHWP_PREFIX . 'license_expiration' );
			}
		}

		return $new;
	}

	/**
	 * Perform periodic maintenance
	 *
	 * @return bool
	 * @since 1.0
	 */
	function do_maintenance() {
		do_action( 'searchwp_log', 'do_maintenance()' );

		$license = searchwp_get_license_key();
		$license = sanitize_text_field( $license );

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license,
			'item_name'  => urlencode( SEARCHWP_EDD_ITEM_NAME )
		);

		$api_args = array(
			'timeout'   => 30,
			'sslverify' => false,
			'body'      => $api_params,
		);
		$response = wp_remote_post( SEARCHWP_EDD_STORE_URL, $api_args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( 'valid' !== $license_data->license ) {
			do_action( 'searchwp_log', 'License not valid' );
			delete_option( SEARCHWP_PREFIX . 'license_status' );
			delete_option( SEARCHWP_PREFIX . 'license_expiration' );
		}

		return true;
	}

}

$license_settings = new SearchWP_Settings_Implementation_License();
$license_settings->init();

