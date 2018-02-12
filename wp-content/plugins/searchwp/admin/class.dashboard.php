<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_Dashboard is responsible for displaying Dashboard Widgets in the WordPress admin
 *
 * @since 2.8
 */
class SearchWP_Dashboard {

	/**
	 * Set up our hooks
	 *
	 * @since 2.4
	 */
	function setup() {
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_widget' ) );
	}

	/**
	 * Callback to add the Widget to the Dashboard
	 *
	 * @since 2.4
	 */
	function add_widget() {
		if (
			apply_filters( 'searchwp_dashboard_widget', true )
			&& current_user_can( apply_filters( 'searchwp_dashboard_widget_cap', apply_filters( 'searchwp_settings_cap', 'manage_options' ) ) )
		) {
			wp_add_dashboard_widget(
				'searchwp_stats',
				__( 'Search Statistics', 'searchwp' ),
				array( $this, 'echo_widget' )
			);
		}
	}

	/**
	 * Enqueue our assets
	 *
	 * @since 2.8
	 *
	 * @param $hook
	 */
	function assets( $hook ) {
		if (
			is_admin() && 'index.php' === $hook // only on the Dashboard
			&& apply_filters( 'searchwp_dashboard_widget', true )
			&& apply_filters( 'searchwp_dashboard_widget_cap',
			apply_filters( 'searchwp_settings_cap', 'manage_options' ) )
		) {
			$searchwp = SWP();

			wp_enqueue_script( 'searchwp-tabs', trailingslashit( $searchwp->url ) . 'assets/js/searchwp-tabs.js', array( 'jquery' ), $searchwp->version );
			wp_enqueue_script( 'searchwp-dashboard', trailingslashit( $searchwp->url ) . 'assets/js/searchwp-dashboard.js', array( 'jquery', 'searchwp-tabs' ), $searchwp->version );

			wp_enqueue_style( 'searchwp-tabs', trailingslashit( $searchwp->url ) . 'assets/css/searchwp-tabs.css', null, $searchwp->version );
			wp_enqueue_style( 'searchwp-dashboard', trailingslashit( $searchwp->url ) . 'assets/css/searchwp-dashboard.css', array( 'searchwp-tabs' ), $searchwp->version );
		}
	}

	/**
	 * Output the Widget markup
	 *
	 * @since 2.8
	 */
	function echo_widget() {
		if ( class_exists( 'SearchWP_Stats' ) ) {
			$stats = new SearchWP_Stats();
			$searchwp = SWP();
			?>
			<div class="searchwp-dashboard-widget">
				<div class="searchwp-dashboard-stats">
					<?php if ( isset( $searchwp->settings['engines'] ) && ! empty( $searchwp->settings['engines'] ) ) : ?>
						<ul class="searchwp-tabs-nav">
							<?php foreach ( $searchwp->settings['engines'] as $engine => $engineSettings ) :
									$engine_label = isset( $engineSettings['searchwp_engine_label'] ) ? $engineSettings['searchwp_engine_label'] : __( 'Default', 'searchwp' ); ?>
								<li><a href="#swp-<?php echo esc_attr( $engine ); ?>"><?php echo esc_html( $engine_label ); ?></a></li>
							<?php endforeach; ?>
						</ul>
						<div class="searchwp-tabs-content">
							<?php foreach ( $searchwp->settings['engines'] as $engine => $engineSettings ) : ?>
								<?php
								/** @noinspection PhpUnusedLocalVariableInspection */
								$engine_label = isset( $engineSettings['searchwp_engine_label'] ) ? $engineSettings['searchwp_engine_label'] : __( 'Default', 'searchwp' );
								?>
								<div class="searchwp-widget-tab-wrapper ui-helper-clearfix" id="swp-<?php echo esc_attr( $engine ); ?>">
									<?php
									$transient_today_key = 'swp_stats_' . md5( 'searchwp_widget_stats_today_' . $engine );
									if ( false === ( $popular_searches_today = get_transient( $transient_today_key ) ) ) {
										/** @noinspection PhpInternalEntityUsedInspection */
										$popular_searches_today = $stats->get_popular_searches( array( 'days' => 1, 'engine' => $engine ) );
											set_transient( $transient_today_key, $popular_searches_today, 12 * HOUR_IN_SECONDS );
									}

									$transient_month_key = 'swp_stats_' . md5( 'searchwp_widget_stats_month_' . $engine );
									if ( false === ( $popular_searches_month = get_transient( $transient_month_key ) ) ) {
										/** @noinspection PhpInternalEntityUsedInspection */
										$popular_searches_month = $stats->get_popular_searches( array( 'days' => 30, 'engine' => $engine ) );
											set_transient( $transient_month_key, $popular_searches_month, 12 * HOUR_IN_SECONDS );
									}
									?>
									<div class="searchwp-stats-segment searchwp-stats-today">
										<h4><?php esc_html_e( 'Today', 'searchwp' ); ?></h4>
										<?php $this->echo_stats( $popular_searches_today ); ?>
									</div>
									<div class="searchwp-stats-segment searchwp-stats-month">
										<h4><?php esc_html_e( 'Past 30 Days', 'searchwp' ); ?></h4>
										<?php $this->echo_stats( $popular_searches_month ); ?>
									</div>
									<div class="searchwp-stats-segment-next">
										<?php
											$the_link = admin_url( 'index.php?page=searchwp-stats' ) . '&tab=' . esc_attr( $engine );
										?>
										<p><a href="<?php echo esc_url( $the_link ); ?>" class="button"><?php esc_html_e( 'View Full Stats', 'searchwp' ); ?></a></p>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php }
	}

	/**
	 * Apply markup to stats and echo
	 *
	 * @param $stats array The stats to display
	 *
	 * @since 2.8
	 */
	function echo_stats( $stats ) { 
		$stats_obj = new SearchWP_Stats();
		$stats_obj->display( $stats );
	}
}

// liftoff
$searchwp_dashboard = new SearchWP_Dashboard();
$searchwp_dashboard->setup();
