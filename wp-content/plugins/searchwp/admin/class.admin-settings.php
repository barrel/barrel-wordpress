<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_Admin_Settings
 */
class SearchWP_Admin_Settings {

	function init() {

		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ), 999 );
		add_action( 'wp_ajax_swp_lazy_settings', array( $this, 'view_settings' ) );

		// register internal tabs (default (engine settings) has hard-coded as first)
		add_action( 'searchwp_settings_nav_tab', array( $this, 'render_tab_help' ), 1000 );

		// register internal views
		add_action( 'searchwp_settings_view\default',   array( $this, 'render_view_engine' ) );
		add_action( 'searchwp_settings_view\help',      array( $this, 'render_view_help' ) );

		do_action( 'searchwp_settings_init' );
	}

	/**
	 * Enqueue all the stats needed for the settings page
	 *
	 * @param $hook
	 */
	function assets( $hook ) {

		// we only want our assets on our Settings page
		if ( ! in_array( $hook, array( 'settings_page_searchwp' ), true ) ) {
			return;
		}

		$parent = SWP();
		$base_url = trailingslashit( $parent->url );

		// some plugins bundle old versions of libraries we need, we might need to handle that (by undoing the registration)
		if ( wp_script_is( 'select2', 'registered' ) ) {
			wp_dequeue_style( 'select2' );
			wp_deregister_style( 'select2' );
			wp_dequeue_script( 'select2' );
			wp_deregister_script( 'select2' );
		}

		wp_register_style( 'select2',               $base_url . 'assets/vendor/select2/css/select2.min.css', null, '4.0.2', 'screen' );
		wp_register_style( 'swp_admin_css',         $base_url . 'assets/css/searchwp.css', false, SEARCHWP_VERSION );
		wp_register_style( 'swp_settings_css',      $base_url . 'assets/css/searchwp-settings.css', false, SEARCHWP_VERSION );
		wp_register_style( 'swp_settings_tabs_css', $base_url . 'assets/css/searchwp-settings-tabs.css', false, SEARCHWP_VERSION );

		wp_register_script( 'select2',              $base_url . 'assets/vendor/select2/js/select2.min.js', array( 'jquery' ), '4.0.2', false );
		wp_register_script( 'swp_admin_js',         $base_url . 'assets/js/searchwp.js', array( 'jquery', 'select2' ), SEARCHWP_VERSION );
		wp_register_script( 'swp_progress',         $base_url . 'assets/js/searchwp-progress.js', array( 'jquery' ),  SEARCHWP_VERSION );

		add_thickbox();

		wp_enqueue_style( 'swp_admin_css' );
		wp_enqueue_style( 'swp_settings_css' );
		wp_enqueue_style( 'swp_settings_tabs_css' );
		wp_enqueue_style( 'select2' );

		// Always need jQuery
		wp_enqueue_script( 'jquery' );

		// Only need these assets if we're using the legacy UI
		$show_legacy_ui = apply_filters( 'searchwp_legacy_settings_ui', false );
		if ( $show_legacy_ui ) {
			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( 'select2' );

			// wp_enqueue_script( 'swp_admin_js' ); // this is echo'd directly into the page

			// only trigger the progress script if it's not the alternative indexer
			if ( ! isset( $_GET['nonce'] ) && ! $parent->is_using_alternate_indexer() ) {
				// if a nonce was set we're dealing with advanced settings which might be purging the index
				// if this script were included the background process would be invoked, we don't want that right now
				wp_localize_script( 'swp_progress', 'ajax_object',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => wp_create_nonce( 'swpprogress' ),
					)
				);
				wp_enqueue_script( 'swp_progress' );
			}
		}
	}

	/**
	 * Outputs the logo and navigation tabs for all settings screens
	 */
	function render_header() {
		do_action( 'searchwp_settings_before_header' );

		// use the active admin color scheme hover color
		global $_wp_admin_css_colors;
		$current_color = get_user_option( 'admin_color' );
		$current_colors = isset( $_wp_admin_css_colors[ $current_color ] ) ? $_wp_admin_css_colors[ $current_color ] : $_wp_admin_css_colors[0];
		$link_normal_color = isset( $current_colors->colors[2] ) ? $current_colors->colors[2] : '#0073aa';
		$link_hover_color  = isset( $current_colors->colors[3] ) ? $current_colors->colors[3] : '#2ea2cc';

		?>
		<div class="swp-header">
			<p class="searchwp-logo" title="SearchWP">
				<svg width="43" height="66" viewBox="0 0 43 66" xmlns="http://www.w3.org/2000/svg"><title>SearchWP</title><g transform="translate(.6567 .9104)" fill="none" fill-rule="evenodd"><ellipse stroke="#839788" stroke-width=".6687" fill="#FAFAFA" cx="21.0092" cy="34.1409" rx="12.6604" ry="26.8334"/><path d="M8.1347 44.5495s5.625-5.7126 11.822-10.7107c6.6311-5.3483 13.8342-9.982 13.8342-9.982" stroke="#839788" stroke-width="1.4079"/><path d="M34.005 44.5495S28.38 38.837 22.183 33.8388c-6.6312-5.3483-13.8343-9.982-13.8343-9.982" stroke="#839788" stroke-width="1.4079"/><path d="M36.7457 10.3164c.6243 0 1.1713.5848 1.2204 1.3062 0 0 2.8327 10.052 3.5164 20.1006.7785 11.441-.592 22.8786-.592 22.8786.0491.7214-.512 1.3061-1.2531 1.3061H2.244c-.7404 0-1.3011-.5847-1.2507-1.3061 0 0-1.5704-11.3114-.762-22.8786.6934-9.9224 3.7655-20.1006 3.7655-20.1006.0504-.7214.5979-1.3062 1.2227-1.3062h31.5262zM7.1515 15.3825s-1.4238 9.3137-1.8953 17.7297c-.4761 8.4966 0 16.9126 0 16.9126-.0405.7222.5179 1.3077 1.2461 1.3077h28.8853c.7286 0 1.2876-.5853 1.2485-1.3077 0 0 .4603-8.4194 0-16.9126-.4563-8.4194-1.8331-17.7297-1.8331-17.7297-.0392-.7222-.5867-1.3076-1.2222-1.3076H8.376c-.6358 0-1.184.5853-1.2245 1.3076z" stroke="#839788" stroke-width=".6687" fill="#BFCDC2"/><path d="M8.506 55.506l-.7835 4.881c-.1192.7427.4395 1.3447 1.2503 1.3447h23.9817c.8097 0 1.3825-.6035 1.2798-1.3447l-.6761-4.881c-.1029-.7427-.7864-1.3448-1.529-1.3448H10.0645c-.7416 0-1.4397.6035-1.5586 1.3447z" stroke="#839788" stroke-width=".6687" fill="#BFCDC2"/><path d="M3.7322 61.7385l-.4483 1.3487c-.2469.7425.1543 1.3445.894 1.3445h33.7064c.7406 0 1.1222-.6023.8526-1.3445l-.49-1.3487c-.2696-.7425-1.0431-1.3445-1.7257-1.3445H5.4165c-.6834 0-1.4376.6023-1.6843 1.3445zM9.2265 8.223L8.78 14.785c-.0505.7431.5087 1.3455 1.2513 1.3455h21.9645c.7416 0 1.2964-.6035 1.2393-1.3454l-.505-6.562c-.0571-.7431-.671-1.3455-1.3732-1.3455H10.5877c-.7012 0-1.3108.6036-1.3612 1.3455zM15.6424 1.3444l-.4105 1.8535c-.1645.7425.303 1.3444 1.0434 1.3444h9.3913c.7406 0 1.2241-.6032 1.0805-1.3444l-.3593-1.8535C26.2438.602 25.6202 0 24.9956 0h-7.924c-.6249 0-1.265.6032-1.4292 1.3444z" stroke="#839788" stroke-width=".6687" fill="#BFCDC2"/><rect stroke="#839788" stroke-width=".6687" fill="#BFCDC2" x="11.7738" y="4.0836" width="18.7811" height="3.3647" rx="1.6823"/><ellipse fill="#839788" cx="28.7603" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="27.2618" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="25.3352" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="23.8367" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="22.1241" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="20.4115" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="18.699" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="16.9864" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="15.4879" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="13.7754" cy="5.8778" rx="1" ry="1"/><path d="M10.5742 16.2767L9.0282 19.14c-.175.3242.2832.587 1.0258.587h21.9645c.7417 0 1.1952-.2633 1.0137-.587l-1.6055-2.8634c-.1818-.3243-.8211-.5871-1.4299-.5871H11.992c-.608 0-1.243.2633-1.4177.587zM31.489 51.3789l1.5459-2.8634c.175-.3243-.2832-.5871-1.0258-.5871H10.0445c-.7416 0-1.1951.2633-1.0136.587l1.6055 2.8635c.1818.3242.8211.587 1.4299.587h18.0049c.608 0 1.243-.2633 1.4177-.587z" stroke="#839788" stroke-width=".6687" fill="#BFCDC2"/></g></svg>
			</p>
			<h2 class="nav-tab-wrapper swp-header-nav" id="swp-header-nav">
				<?php
				// default tab (engine settings)
				searchwp_get_nav_tab();

				// all other tabs
				do_action( 'searchwp_settings_nav_tab' );
				?>
			</h2>
		</div>
		<style type="text/css">
			#swp-header-nav .nav-tab {
				color:<?php echo esc_html( $link_normal_color ); ?>;
			}
			#swp-header-nav .nav-tab:hover {
				color:<?php echo esc_html( $link_hover_color ); ?>;
			}
			.searchwp-tab-license-inactive span {
				color:#fff;
				background-color: #d54e21;
			}
			.searchwp-tab-license-inactive:hover span {
				color:#fff;
				background-color: #d54e21;
			}

			.searchwp-tab-license-inactive.nav-tab-active span,
			.searchwp-tab-license-inactive.nav-tab-active:hover span{
				color:inherit;
				background-color:transparent;
			}
			#wpbody .notice,
			#wpbody .updated,
			#wpbody .update-nag,
			#wpbody .screen-meta-links {
				display:none;
			}

			#wpbody .notice.searchwp-notice-persist,
			#wpbody .updated.searchwp-notice-persist {
				display: block;
			}
		</style>
		<?php
		do_action( 'searchwp_settings_after_header' );
	}

	function render_tab_help() {
		searchwp_get_nav_tab( array(
			'tab'   => 'help',
			'label' => __( 'Support', 'searchwp' ),
		) );
	}

	/**
	 * Output footer content
	 */
	function render_footer() {
		do_action( 'searchwp_settings_footer' ); ?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				var $notices = $('#wpbody .notice, #wpbody .updated, #wpbody-content > .error, #wpbody-content > .info');
				if ($notices.length) {
					if (!$notices.hasClass('searchwp-notice-persist')) {
						$notices.removeClass('updated').addClass('searchwp-updated').appendTo('.swp-notices');
					}
				}
			});
		</script>
		<?php
	}

	/**
	 * Fires the action that needs to be registered by views to display the view itself
	 */
	function render_view() {
		$view = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'default';

		// allow view-specific actions to be taken
		do_action( "searchwp_settings_before\\{$view}" );

		// render the view
		do_action( "searchwp_settings_view\\{$view}" );

		// allow post-render view-specific actions to be taken
		do_action( "searchwp_settings_after\\{$view}" );
	}

	/**
	 * Main (engines) settings view
	 */
	function render_view_engine() {

		$show_legacy_ui = apply_filters( 'searchwp_legacy_settings_ui', false );

		if ( empty( $show_legacy_ui ) ) {
			include( dirname( __FILE__ ) . '/view-settings-engines.php' );
		} else {
			// output a notice for the initial index being built
			$notices = searchwp_get_setting( 'notices' );
			$initial_notified = ( is_array( $notices ) && in_array( 'initial', $notices, true ) ) ? true : false;
			if ( searchwp_get_setting( 'initial_index_built' ) && ! $initial_notified ) : ?>
				<div class="updated">
					<p><?php esc_html_e( 'Initial index has been built, the progress bar will be hidden until it is needed again.', 'searchwp' ); ?></p>
				</div>
				<?php
				if ( is_array( $notices ) ) {
					$notices[] = 'initial';
				} else {
					$notices = array( 'initial' );
				}
				searchwp_set_setting( 'notices', $notices );
				?>
			<?php endif;

			include( dirname( __FILE__ ) . '/view-settings-engines-legacy.php' );
		}
	}

	/**
	 * Help view
	 */
	function render_view_help() {
		include( dirname( __FILE__ ) . '/view-settings-help.php' );
	}

	/**
	 * Outputs the settings page HTML (in place to (hopefully) get around aggressive page caching in the WP admin)
	 *
	 * @since 2.3
	 */
	function view_settings() {
		$parent = SWP();
		$parent->define_keys();
		$parent->get_indexer_communication_result();

		/** @noinspection PhpIncludeInspection */
		include( $parent->dir . '/admin/settings.php' );
	}
}

/**
 * Build markup for a navigation tab
 *
 * @param $args
 */
function searchwp_get_nav_tab( $args = array() ) {

	$defaults = array(
		'tab'                   => '',
		'label'                 => __( 'Settings', 'searchwp' ),
		'classes'               => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$link   = admin_url( 'options-general.php?page=searchwp' );
	$class  = 'nav-tab';
	$tab    = $args['tab'];
	$label  = $args['label'];

	if ( ! empty( $args['classes'] ) ) {
		$class .= ' ' . $args['classes'];
	}

	// check for active state
	if (
		( empty( $tab ) && ! isset( $_GET['tab'] ) ) ||
		( isset( $_GET['tab'] ) && $tab === $_GET['tab'] )
	) {
		$class .= ' nav-tab-active';
	}

	// every tab gets a nonce
	if ( ! empty( $tab ) ) {
		wp_create_nonce( 'swpnav' . $tab );
	}

	// build the base link
	if ( ! empty( $tab ) ) {
		$class .= ' nav-tab-' . sanitize_title( $tab );
		$link = add_query_arg(
			array(
				'tab' => $tab,
			),
			$link
		);
	}
	?>
	<a href="<?php echo esc_url( $link ); ?>" class="<?php echo esc_attr( $class ); ?>">
		<span><?php echo esc_html( $label ); ?></span>
	</a>
<?php
}
