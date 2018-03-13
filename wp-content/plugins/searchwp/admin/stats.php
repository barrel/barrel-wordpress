<?php

global $wpdb;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();

if ( ! is_admin() || ! current_user_can( apply_filters( 'searchwp_statistics_cap', 'publish_posts' ) ) || empty( $user_id ) ) {
	wp_die( esc_html__( 'Invalid request', 'searchwp' ) );
}

if ( isset( $_GET['tab'] ) ) {
	$engine = sanitize_text_field( $_GET['tab'] );
	if ( ! isset( $this->settings['engines'][ $engine ] ) ) {
		wp_die( esc_html__( 'Invalid request', 'searchwp' ) );
	}
}

$stats = new SearchWP_Stats();

// check to see if we need to ignore something
if ( isset( $_GET['nonce'] )
     && isset( $_GET['ignore'] )
     && $stats->verify_nonce( $_GET['nonce'], 'ignore' )
) {
	$query_hash = sanitize_text_field( $_GET['ignore'] ); // param is already an md5 hash
	$stats->ignore_query( $query_hash );
	$this->reset_dashboard_stats_transients();
}

$ignored_queries = $stats->get_ignored_queries();

// we need to use a nonce in our query ignore process
$stats->generate_nonce( 'ignore' );

/**
 * Callback to preface all queries with a link to ignore the query from the stats page
 *
 * @since 2.7.1
 *
 * @param $query
 * @param $args
 */
function searchwp_print_query_ignore_link( $query, $args ) {
	$the_link = admin_url( 'index.php?page=searchwp-stats' ) . '&tab=' . esc_attr( $args['engine'] ) . '&nonce=' . esc_attr( $args['nonce'] ) . '&ignore=' . esc_attr( md5( $query ) );
	?><a class="swp-delete" href="<?php echo esc_url( $the_link ); ?>">x</a><?php
}

add_action( 'searchwp_stats_before_query', 'searchwp_print_query_ignore_link', 10, 2 );

?><div class="wrap">

	<div id="icon-searchwp" class="icon32">
		<img src="<?php echo esc_url( trailingslashit( $this->url ) ); ?>assets/images/searchwp@2x.png" alt="SearchWP" width="21" height="32" />
	</div>

	<h2><?php esc_html_e( 'Search Statistics', 'searchwp' ); ?></h2>

	<br />

	<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<?php foreach ( $this->settings['engines'] as $engine => $engineSettings ) : ?>
			<?php
			$active_tab = '';
			$engine_label = isset( $engineSettings['searchwp_engine_label'] ) ? sanitize_text_field( $engineSettings['searchwp_engine_label'] ) : __( 'Default', 'searchwp' );
			if ( ( isset( $_GET['tab'] ) && $engine === $_GET['tab'] ) || ( ! isset( $_GET['tab'] ) && 'default' === $engine ) ) {
				$active_tab = ' nav-tab-active';
			}
			?>
			<?php
				$the_link = admin_url( 'index.php?page=searchwp-stats' ) . '&tab=' . esc_attr( $engine );
			?>
			<a href="<?php echo esc_url( $the_link ); ?>" class="nav-tab<?php echo esc_attr( $active_tab ); ?>"><?php echo esc_html( $engine_label ); ?></a>
		<?php endforeach; ?>
	</h2>

	<br />

	<div class="swp-searches-chart-wrapper">
		<h3><?php esc_html_e( 'Searches over the past 30 days', 'searchwp' ); ?></h3>
		<div class="swp-searches-chart ct-chart"></div>
	</div>

	<?php
	// generate stats for the past 30 days for each search engine
	$prefix = $wpdb->prefix;
	$engine = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'default';

	if ( isset( $this->settings['engines'][ $engine ] ) ) {
		$engineSettings = $this->settings['engines'][ $engine ];
		$engineLabel = isset( $engineSettings['searchwp_engine_label'] ) ? esc_attr( $engineSettings['searchwp_engine_label'] ) : esc_attr__( 'Default', 'searchwp' );

		$searchesPerDay = $stats->get_search_counts_per_day( 30, $engine );

		$stats->generate_chartist_data( $searchesPerDay );
	}
	?>

	<div class="swp-group swp-stats swp-stats-4">

		<h2><?php esc_html_e( 'Popular Searches', 'searchwp' ); ?></h2>

		<div class="swp-stat postbox swp-meta-box metabox-holder">
			<h3 class="hndle"><span><?php esc_html_e( 'Today', 'searchwp' ); ?></span></h3>

			<div class="inside">
				<?php
					$stats->display(
						$stats->get_popular_searches(
							array(
								'days'      => 1,
								'engine'    => $engine,
								'exclude'   => $ignored_queries,
							)
						),
						$engine
					);
				 ?>
			</div>
		</div>

		<div class="swp-stat postbox swp-meta-box metabox-holder">
			<h3 class="hndle"><span><?php esc_html_e( 'Week', 'searchwp' ); ?></span></h3>

			<div class="inside">
				<?php
				$stats->display(
					$stats->get_popular_searches(
						array(
							'days'      => 7,
							'engine'    => $engine,
							'exclude'   => $ignored_queries,
						)
					),
					$engine
				);
				?>
			</div>
		</div>

		<div class="swp-stat postbox swp-meta-box metabox-holder">
			<h3 class="hndle"><span><?php esc_html_e( 'Month', 'searchwp' ); ?></span></h3>

			<div class="inside">
				<?php
				$stats->display(
					$stats->get_popular_searches(
						array(
							'days'      => 30,
							'engine'    => $engine,
							'exclude'   => $ignored_queries,
						)
					),
					$engine
				);
				?>
			</div>
		</div>

		<div class="swp-stat postbox swp-meta-box metabox-holder">
			<h3 class="hndle"><span><?php esc_html_e( 'Year', 'searchwp' ); ?></span></h3>

			<div class="inside">
				<?php
				$stats->display(
					$stats->get_popular_searches(
						array(
							'days'      => 365,
							'engine'    => $engine,
							'exclude'   => $ignored_queries,
						)
					),
					$engine
				);
				?>
			</div>
		</div>

	</div>

	<div class="swp-group swp-stats swp-stats-4">

		<h2><?php esc_html_e( 'Failed Searches', 'searchwp' ); ?></h2>

		<div class="swp-stat postbox swp-meta-box metabox-holder">
			<h3 class="hndle"><span><?php esc_html_e( 'Past 30 Days', 'searchwp' ); ?></span></h3>

			<div class="inside">
				<?php
				$stats->display(
					$stats->get_popular_searches(
						array(
							'days'      => 30,
							'engine'    => $engine,
							'exclude'   => $ignored_queries,
							'min_hits'  => false,
							'max_hits'  => 0
						)
					),
					$engine
				);
				?>
			</div>
		</div>

	</div>

	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			var searchwp_resize_columns = function() {
				var searchwp_stat_width = $('.swp-stat:first').width();
				$('.swp-stats td div').css('max-width',Math.floor(searchwp_stat_width/2) - 10 );
			};
			searchwp_resize_columns();
			jQuery(window).resize(function(){
				searchwp_resize_columns();
			});

			$('.swp-stats').each(function () {
				var tallest = 0;
				$(this).find('.swp-stat').each(function () {
					if ($(this).outerHeight() > tallest) {
						tallest = $(this).outerHeight();
					}
				}).outerHeight(tallest);
			});
			$('a.swp-delete').click(function(){
				return !!confirm('<?php echo esc_js( __( 'Are you sure you want to ignore this search from all statistics?', 'searchwp' ) ); ?>');
			});
		});
	</script>

</div>
