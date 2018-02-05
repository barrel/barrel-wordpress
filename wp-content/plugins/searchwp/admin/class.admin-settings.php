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
			<p class="searchwp-logo" title="SearchWP"><span class="screen-reader-text">SearchWP</span></p>
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
		</style>
		<?php
		do_action( 'searchwp_settings_after_header' );
	}

	function render_tab_help() {
		searchwp_get_nav_tab( array(
			'tab'   => 'help',
			'label' => __( 'Help', 'searchwp' ),
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
					$notices.removeClass('updated').addClass('searchwp-updated').appendTo('.swp-notices');
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
