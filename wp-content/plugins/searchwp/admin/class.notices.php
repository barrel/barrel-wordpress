<?php

global $wp_filesystem;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/** @noinspection PhpIncludeInspection */
include_once ABSPATH . 'wp-admin/includes/file.php';

/**
 * Class SearchWPAdminNotices is responsible for displaying notices in the WordPress admin
 */
class SearchWPAdminNotices extends SearchWP {

	function admin_notices() {
		add_action( 'admin_notices', array( $this, 'media_note' ), 9999 );
		add_action( 'admin_notices', array( $this, 'conflicts' ), 9999 );
		add_action( 'admin_notices', array( $this, 'indexer_disabled' ), 9999 );
		add_action( 'admin_notices', array( $this, 'failed_index' ), 9999 );
		add_action( 'admin_notices', array( $this, 'missing_integrations' ), 9999 );
		add_action( 'admin_notices', array( $this, 'log_file_size_warning' ), 9999 );
		add_action( 'admin_notices', array( $this, 'http_basic_auth' ), 9999 );
	}

	/**
	 * Detect whether the site is using HTTP Basic Auth, as that prevents the indexer from working
	 * @since 2.3.4
	 *
	 * @return bool
	 */
	function http_basic_auth(){

		// As of 2.9.0 this only applies for the legacy UI
		$show_legacy_ui = apply_filters( 'searchwp_legacy_settings_ui', false );
		if ( empty( $show_legacy_ui ) ) {
			return;
		}

		$basic_auth = searchwp_get_setting( 'basic_auth' );

		// determine if the environment has already been verified; don't want redundant HTTP requests on every page load
		if ( 'no' === $basic_auth ) {
			return;
		}

		// check to see if the credentials are already provided
		$http_basic_auth_creds = apply_filters( 'searchwp_basic_auth_creds', false );
		if ( true === $basic_auth && is_array( $http_basic_auth_creds ) && isset( $http_basic_auth_creds['username'] ) && isset( $http_basic_auth_creds['password'] ) ) {
			return;
		}

		$searchwp = SWP();
		$response = $searchwp->get_indexer_communication_result();
		if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 401 === (int) $response['response']['code'] ) {
			searchwp_set_setting( 'basic_auth', true );
			?>
			<div class="error" id="searchwp-http-basic-auth">
				<p><?php echo wp_kses( sprintf( __( 'SearchWP has detected HTTP Basic Authentication, in order for the indexer to operate as expected you must provide credentials via the <a href="%s"><code>searchwp_basic_auth_creds</code></a> hook, or disable HTTP Basic Authentication.', 'searchwp' ), 'https://searchwp.com/docs/hooks/searchwp_basic_auth_creds/' ), array( 'a' => array( 'href' => array() ), 'code' => array() ) ); ?></p>
			</div>
		<?php
		} else {
			// flag the environment as 'good'
			if ( ! is_wp_error( $response ) ) {
				searchwp_set_setting( 'basic_auth', 'no' );
			}
		}
	}

	/**
	 * If the debug log is over 2MB
	 */
	function log_file_size_warning() {
		$wp_upload_dir = wp_upload_dir();

		$logfile = trailingslashit( $wp_upload_dir['basedir'] ) . 'searchwp-debug.txt';

		// if the logfile is over a 2MB it's likely the developer forgot to disable debugging
		if ( file_exists( $logfile ) && absint( filesize( $logfile ) ) > 2097151 ) :
		?>
			<div class="error" id="searchwp-log-file-size">
				<p><?php echo wp_kses( __( 'Your SearchWP debug log is quite large. Please remember to disable debugging and delete <code>~/wp-content/uploads/searchwp-debug.text</code> when you are done.', 'searchwp' ), array( 'code' => array() ) ); ?></p>
			</div>
		<?php endif;
	}

	/**
	 * If we're running plugins with known SearchWP integrations that are missing
	 */
	function missing_integrations() {
		$integration_extensions = array(
			'bbpress' => array(
				'plugin' => array(
					'file' => 'bbpress/bbpress.php',
					'name' => 'bbPress',
					'url' => 'https://wordpress.org/plugins/bbpress/',
				),
				'integration' => array(
					'file' => 'searchwp-bbpress/searchwp-bbpress.php',
					'name' => 'bbPress Integration',
					'url' => 'https://searchwp.com/docs/extensions/bbpress-integration/',
				),
			),
			'wpml' => array(
				'plugin' => array(
					'file' => 'sitepress-multilingual-cms/sitepress.php',
					'name' => 'WPML',
					'url' => 'http://wpml.org/',
				),
				'integration' => array(
					'file' => 'searchwp-wpml/searchwp-wpml.php',
					'name' => 'WPML Integration',
					'url' => 'https://searchwp.com/docs/extensions/wpml-integration/',
				),
			),
			'polylang' => array(
				'plugin' => array(
					'file' => 'polylang/polylang.php',
					'name' => 'Polylang',
					'url' => 'https://wordpress.org/plugins/polylang/',
				),
				'integration' => array(
					'file' => 'searchwp-polylang/searchwp-polylang.php',
					'name' => 'Polylang Integration',
					'url' => 'https://searchwp.com/docs/extensions/polylang-integration/',
				),
			),
			'woocommerce' => array(
				'plugin' => array(
					'file' => 'woocommerce/woocommerce.php',
					'name' => 'WooCommerce',
					'url' => 'https://wordpress.org/plugins/woocommerce/',
				),
				'integration' => array(
					'file' => 'searchwp-woocommerce/searchwp-woocommerce.php',
					'name' => 'WooCommerce Integration',
					'url' => 'https://searchwp.com/docs/extensions/woocommerce-integration/',
				),
			),
			'wpjobmanager' => array(
				'plugin' => array(
					'file' => 'wp-job-manager/wp-job-manager.php',
					'name' => 'WP Job Manager',
					'url' => 'https://wordpress.org/plugins/wp-job-manager/',
				),
				'integration' => array(
					'file' => 'searchwp-wp-job-manager-integration/searchwp-wp-job-manager-integration.php',
					'name' => 'WP Job Manager Integration',
					'url' => 'https://searchwp.com/docs/extensions/wp-job-manager-integration/',
				),
			),
			'privatecontent' => array(
				'plugin' => array(
					'file' => 'private-content/private_content.php',
					'name' => 'PrivateContent',
					'url' => 'http://codecanyon.net/item/privatecontent-multilevel-content-plugin/1467885',
				),
				'integration' => array(
					'file' => 'searchwp-privatecontent/searchwp-privatecontent.php',
					'name' => 'PrivateContent Integration',
					'url' => 'https://searchwp.com/docs/extensions/privatecontent-integration/',
				),
			),
			// TODO: figure out how to handle multiple parent themes and any number of child themes?
			//			'directorypress' => array(
			//				'theme' => array(
			//					'file' => '',
			//					'name' => 'DirectoryPress',
			//					'url' => 'http://directorypress.net/',
			//				),
			//				'integration' => array(
			//					'file' => 'searchwp-directorypress/searchwp-directorypress.php',
			//					'name' => 'DirectoryPress Integration',
			//					'url' => 'https://searchwp.com/docs/extensions/directorypress-integration/',
			//				),
			//			),
		);

		$missing_integrations = array();
		foreach ( $integration_extensions as $integration_extension_key => $integration_extension ) {
			if ( isset( $integration_extension['plugin'] ) && is_plugin_active( $integration_extension['plugin']['file'] ) && ! is_plugin_active( $integration_extension['integration']['file'] ) ) {
				$missing_integrations[] = $integration_extension_key;
			}
			if ( isset( $integration_extension['theme'] ) ) {
				$theme = wp_get_theme();
				if ( $integration_extension['theme']['file'] === $theme->get( 'Template' ) ) {
					$missing_integrations[] = $integration_extension_key;
				}
			}
		}

		if ( ! empty( $missing_integrations ) && apply_filters( 'searchwp_missing_integration_notices', true ) ) { ?>
			<?php foreach ( $missing_integrations as $missing_integration ) : ?>
				<?php
				$plugin         = isset( $integration_extensions[ $missing_integration ]['plugin'] ) ? $integration_extensions[ $missing_integration ]['plugin']['name'] : $integration_extensions[ $missing_integration ]['theme']['name'];
				$url            = $integration_extensions[ $missing_integration ]['integration']['url'];
				$integration    = $integration_extensions[ $missing_integration ]['integration']['name'];
				?>
				<div class="error" id="searchwp-missing-integrations-notice">
					<p><strong><?php esc_html_e( 'Missing SearchWP integration', 'searchwp' ); ?>:</strong> <?php echo wp_kses( sprintf( __( 'In order for SearchWP to work with %s you will need to install and activate the <a href="%s">%s</a> Extension.', 'searchwp' ), esc_html( $plugin ), esc_url( $url ), esc_html( $integration ) ), array( 'a' => array( 'href' => array() ) ) ); ?> <?php echo wp_kses( sprintf( __( 'To dismiss this notice please see <a href="%s">these docs</a>.', 'searchwp' ), 'https://searchwp.com/?p=31517' ), array( 'a' => array( 'href' => array() ) ) ); ?></p>
				</div>
			<?php endforeach; ?>
		<?php }
	}

	/**
	 * Check for erroneous posts that were not indexed after multiple attempts
	 */
	function failed_index() {

		// allow dev to forcefully omit posts from being indexed
		$exclude_from_index = apply_filters( 'searchwp_prevent_indexing', array() );
		if ( ! is_array( $exclude_from_index ) ) {
			$exclude_from_index = array();
		}
		$exclude_from_index = array_map( 'absint', $exclude_from_index );

		$args = array(
			'posts_per_page'        => -1,
			'post_type'             => 'any',
			'post_status'           => array( 'publish', 'inherit' ),
			'post__not_in'          => $exclude_from_index,
			'fields'                => 'ids',
			'meta_query'    => array(
				'relation'          => 'AND',
				array(
					'key'           => '_' . SEARCHWP_PREFIX . 'last_index',
					'value'         => '', // http://core.trac.wordpress.org/ticket/23268
					'compare'       => 'NOT EXISTS',
					'type'          => 'NUMERIC',
				),
				array( // only want media that hasn't failed indexing multiple times
					'key'           => '_' . SEARCHWP_PREFIX . 'skip',
					'compare'       => 'EXISTS',
					'type'          => 'BINARY',
				)
			)
		);

		$erroneousPosts = get_posts( $args );

		if ( ! empty( $erroneousPosts ) && apply_filters( 'searchwp_failed_index_notice', true, $erroneousPosts ) ) : ?>
			<div class="updated error" id="searchwp-index-errors-notice">
				<?php
					$the_link = admin_url( 'options-general.php?page=searchwp' ) . '&nonce=' . esc_attr( wp_create_nonce( 'swperroneous' ) );
				?>
				<p><?php esc_html_e( 'SearchWP failed to index', 'searchwp' ); ?> <strong><?php echo absint( count( $erroneousPosts ) ); ?></strong> <?php if ( 1 === count( $erroneousPosts ) ) { esc_html_e( 'post', 'searchwp' ); } else { esc_html_e( 'posts', 'searchwp' ); } ?>. <a href="<?php echo esc_url( $the_link ); ?>"><?php esc_html_e( 'View details', 'searchwp' ); ?> &raquo;</a></p>
			</div>
		<?php endif;
	}

	/**
	 * If the indexer is disabled
	 */
	function indexer_disabled() {
		$saved_settings = searchwp_get_option( 'advanced' );
		$paused = isset( $saved_settings['disable_indexer'] ) && ! empty( $saved_settings['disable_indexer'] );

		if ( $paused ) {
			?>
			<div class="updated">
				<p><?php echo wp_kses( __( 'The SearchWP indexer is currently <strong>disabled</strong>', 'searchwp' ), array( 'strong' => array() ) ); ?></p>
			</div>
		<?php
		}
	}

	/**
	 * If a filter conflict was detected, we need to set up our AJAX dismissal
	 *
	 * @since 1.8
	 */
	function filter_conflict_javascript() {
		?>
		<script type="text/javascript" >
			jQuery(document).ready(function($) {
				var data = { action: 'swp_conflict'},
					$body = $('body');
				$body.on('click','a.swp-dismiss-conflict',function(){
					data.swphash = $(this).data('hash');
					data.swpnonce = $(this).data('nonce');
					data.swpfilter = $(this).data('filter');
					// noinspection JSUnresolvedVariable ajaxurl
					$.post(ajaxurl, data, function(response) {});
					$(this).parents('.updated').remove();
					return false;
				}).on('click','.swp-conflict-toggle',function(){
					var $target = $($(this).attr('href'));
					if($target.is(':visible')){
						$target.hide();
					}else{
						$target.show();
					}
					return false;
				});
			});
		</script>
	<?php
	}

	/**
	 * Detect whether other plugins are using the hooks SearchWP absolutely depends on as they're likely to cause interference
	 */
	function conflicts() {
		// allow developers to disable potential conflict notices if they want
		$maybe_debugging = apply_filters( 'searchwp_debug', false );
		$show_conflict_notices = apply_filters( 'searchwp_show_conflict_notices', $maybe_debugging );

		if ( false === $show_conflict_notices || ! class_exists( 'SearchWP_Conflicts' ) ) {
			return;
		}

		$conflicts = new SearchWP_Conflicts();

		// whether the JavaScript for these notices has been output
		$javascript_deployed = false;

		// output a notification if there are potential query_posts or WP_Query conflicts in search.php
		if ( $conflicts->search_template && $show_conflict_notices ) {
			if ( ! empty( $conflicts->search_template_conflicts ) ) {
				add_action( 'admin_footer', array( $this, 'filter_conflict_javascript' ) );
				$javascript_deployed = true;
				?>
				<div class="updated">
					<p><?php echo wp_kses( __( 'SearchWP has detected a <strong>theme conflict</strong> with the active theme.', 'searchwp' ), array( 'strong' => array() ) ); ?> <a class="swp-conflict-toggle swp-theme-conflict-show" href="#searchwp-conflict-theme"><?php esc_html_e( 'More info &raquo;', 'searchwp' ); ?></a></p>
					<div id="searchwp-conflict-theme" style="background:#fafafa;border:1px solid #eaeaea;padding:0.6em 1.2em;border-radius:2px;margin-bottom:1em;display:none;">
						<p><?php echo wp_kses( __( "In order for SearchWP to display it's results, occurrences of <code>new WP_Query</code> and <code>query_posts()</code> must be removed from your search results template.", 'searchwp' ), array( 'code' => array() ) ); ?></p>
						<p>
							<strong><?php esc_html_e( 'File location', 'searchwp' ); ?>:</strong>
							<code><?php echo esc_html( $conflicts->search_template ); ?></code>
						</p>
						<?php foreach ( $conflicts->search_template_conflicts as $line_number => $conflicts ) : ?>
							<?php $conflicts = array_map( 'esc_html', $conflicts ); ?>
							<p>
								<strong><?php esc_html_e( 'Line', 'searchwp' ); ?>: <?php echo absint( $line_number ); ?></strong>
								<code><?php echo wp_kses( implode( '</code>, <code>', $conflicts ), array( 'code' => array() ) ); ?></code>
							</p>
						<?php endforeach; ?>
						<p><?php esc_html_e( 'Please ensure the offending lines are removed from the theme template to avoid conflicts with SearchWP. When removed, this notice will disappear. You may also dismiss this message using', 'searchwp' ); ?></p>
						<p class="description"><?php echo wp_kses( __( "You may dismiss this (and all like this) message by adding <code>add_filter( 'searchwp_show_conflict_notices', '__return_false' );</code> to your theme's <code>functions.php</code>.", 'searchwp' ), array( 'code' => array() ) ); ?></p>
					</div>
				</div>
			<?php
			}
		}

		// output a notification if there are potential action/filter conflicts
		$show_filter_notices = apply_filters( 'searchwp_show_filter_conflict_notices', false );
		if ( $show_filter_notices && $show_conflict_notices && ! empty( $conflicts->filter_conflicts ) ) {
			foreach ( $conflicts->filter_conflicts as $filter_name => $potential_conflict ) {
				$show_conflict = true;

				// user may have already dismissed this conflict so let's check
				$existing_dismissals = searchwp_get_setting( 'dismissed' );

				// dismissals are stored as hashes of the hooks as they were when the dismissal was enabled
				$conflict_hash = md5( wp_json_encode( $potential_conflict ) );
				$conflict_nonce = wp_create_nonce( 'swpconflict_' . $filter_name );

				// check to see if this particular filter conflict was already dismissed
				if ( is_array( $existing_dismissals ) ) {
					if ( isset( $existing_dismissals['filter_conflicts'] ) && is_array( $existing_dismissals['filter_conflicts'] ) ) {
						if ( in_array( $conflict_hash, $existing_dismissals['filter_conflicts'], true ) ) {
							$show_conflict = false;
						}
					}
				}

				if ( $show_conflict ) {
					// dump out the JavaScript that allows dismissals
					if ( ! $javascript_deployed ) {
						add_action( 'admin_footer', array( $this, 'filter_conflict_javascript' ) );
						$javascript_deployed = true;
					}
					?>
					<div class="updated">
						<p><?php echo wp_kses( sprintf( __( 'SearchWP has detected a <strong>potential (<em>not guaranteed</em>)</strong> action/filter conflict with <code>%s</code> caused by an active plugin or the active theme.', 'searchwp' ), esc_html( $filter_name ) ), array( 'strong' => array(), 'em' => array(), 'code' => array() ) ); ?> <a class="swp-conflict-toggle swp-filter-conflict-show" href="#searchwp-conflict-<?php echo esc_attr( $filter_name ); ?>"><?php esc_html_e( 'More info &raquo;', 'searchwp' ); ?></a></p>
						<div id="searchwp-conflict-<?php echo esc_attr( $filter_name ); ?>" style="background:#fafafa;border:1px solid #eaeaea;padding:0.6em 1.2em;border-radius:2px;margin-bottom:1em;display:none;">
							<p><?php echo wp_kses( __( '<strong>This is simply a <em>preliminary</em> detection of a <em>possible</em> conflict.</strong> Many times these detections can be <strong>safely dismissed</strong>', 'searchwp' ), array( 'strong' => array(), 'em' => array() ) ); ?></p>
							<p><?php echo wp_kses( __( '<em>If (and only if) you are experiencing issues</em> with search results not changing or not appearing, the following Hooks (put in place by other plugins or your active theme) <em>may be</em> contributing to the problem:', 'searchwp' ), array( 'em' => array() ) ); ?></p>
							<ol>
								<?php foreach ( $potential_conflict as $conflict ) : ?>
									<?php
									// if it was class based we'll break out the class
									if ( strpos( $conflict, '::' ) ) {
										$conflict = explode( '::', $conflict );
										$conflict = '<code>' . esc_html( $conflict[1] ) . '</code> ' . __( '(method) in', 'searchwp' ) . ' <code>' . esc_html( $conflict[0] ) . '</code>' . __( ' (class)', 'searchwp' );
									} else {
										$conflict = '<code>' . esc_html( $conflict ) . '</code> ' . __( '(function)', 'searchwp' );
									}
									?>
									<li><?php echo wp_kses( $conflict, array( 'code' => array() ) ); ?></li>
								<?php endforeach; ?>
							</ol>
							<?php
							$filter_resolution_url = '#';
							if ( is_array( $conflicts->filter_checklist ) && array_key_exists( $filter_name, $conflicts->filter_checklist ) ) {
								$filter_resolution_url = esc_url( $conflicts->filter_checklist[ $filter_name ] );
							}
							?>
							<p><?php echo wp_kses( sprintf( __( '<strong>If you believe there to be a conflict (e.g. search results not showing up):</strong> use this information you can determine how to best disable this interference. For more information please see <a href="%s">this Knowledge Base article</a>.', 'searchwp' ), esc_url( $filter_resolution_url ) ), array( 'strong' => array(), 'a' => array( 'href' => array() ) ) ); ?></p>
							<p><a class="button swp-dismiss-conflict" href="#" data-hash="<?php echo esc_attr( $conflict_hash ); ?>" data-nonce="<?php echo esc_attr( $conflict_nonce ); ?>" data-filter="<?php echo esc_attr( $filter_name ); ?>"><?php esc_html_e( 'Dismiss this message', 'searchwp' ); ?></a></p>
						</div>
					</div>
				<?php }
			}
		}
	}

	/**
	 * SearchWP by default does not index Media for the following reasons:
	 *      - minimal number of users enable it
	 *      - it bloats the index quite a bit
	 * As a result searching for Media in the WordPress admin will not work properly unless it's enabled
	 */
	function media_note() {
		if ( class_exists( 'WP_Screen' ) ) {
			$current_screen = get_current_screen();
			if ( $current_screen instanceof WP_Screen ) {
				if ( isset( $current_screen->id ) ) {
					if ( is_search() && 'upload' === $current_screen->id ) {

						// we're on the search results of the Media page in the WP admin, as a result of that the
						// search engine settings have been hijacked and limited to Media only, so we need to retrieve
						// the engine settings from the database (which are unaltered) because we need to check to see
						// whether Media may not be indexed at all

						$live_engine_settings = searchwp_get_option( 'settings' );
						$index_attachments_from_settings = false;
						if ( isset( $live_engine_settings['engines'] ) && is_array( $live_engine_settings['engines'] ) ) {
							foreach ( $live_engine_settings['engines'] as $engine ) {
								if ( isset( $engine['attachment'] ) && isset( $engine['attachment']['enabled'] ) && true === $engine['attachment']['enabled'] ) {
									$index_attachments_from_settings = true;
									break;
								}
							}
						}

						$maybe_index_attachments = apply_filters( 'searchwp_index_attachments', $index_attachments_from_settings );
						$maybe_search_in_admin = apply_filters( 'searchwp_in_admin', false );

						// if Media isn't explicity indexed and searching in the admin is enabled and we're on
						// the search results screen for Media, tell the user that results might be incomplete
						if ( ! $maybe_index_attachments && $maybe_search_in_admin ) {
							?><div class="updated">
								<p><?php echo wp_kses( __( '<strong>Potentially incomplete results:</strong> Since you <em>do not have Media enabled</em> for any search engine, you should implement the <code>searchwp_index_attachments</code> hook to ensure Media is properly indexed by SearchWP. Once attachment indexing has been enabled, ensure there is no progress bar on the SearchWP Settings screen, confirming all Media is indexed.', 'searchwp' ), array( 'strong' => array(), 'em' => array(), 'code' => array() ) ); ?></p>
							</div>
						<?php }
					}
				}
			}
		}
	}

}

$searchwp_admin_notices = new SearchWPAdminNotices();
$searchwp_admin_notices->admin_notices();
