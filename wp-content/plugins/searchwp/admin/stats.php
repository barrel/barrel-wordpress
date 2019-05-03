<?php

global $wpdb;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();

// Make sure this user has permission to view stats.
if ( ! is_admin() || ! current_user_can( apply_filters( 'searchwp_statistics_cap', 'publish_posts' ) ) || empty( $user_id ) ) {
	wp_die( esc_html__( 'Invalid request', 'searchwp' ) );
}

// Make sure a valid tab has been chosen.
$engine = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'default';

if ( ! isset( SWP()->settings['engines'][ $engine ] ) ) {
	wp_die( esc_html__( 'Invalid request', 'searchwp' ) );
}

SWP()->ajax->enqueue_script(
	'statistics',
	array(
		'nonces' => array(
			'get_statistics',
			'ignore_search',
			'unignore_search',
			'reset_stats',
		),
		'data'   => array(
			'engine' => $engine,
		),
	)
);

?>
<div class="wrap">
	<h2><?php esc_html_e( 'Search Statistics', 'searchwp' ); ?></h2>
	<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<?php foreach ( SWP()->settings['engines'] as $engine => $engineSettings ) : ?>
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
	<div id="searchwp-statistics"></div>
</div>
