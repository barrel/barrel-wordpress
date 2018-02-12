<?php

global $wpdb;

if ( ! defined( 'ABSPATH' ) || ! class_exists( 'SearchWP_System_Info' ) || ! class_exists( 'SearchWP_Conflicts' ) ) {
	exit;
}

?>

<div class="searchwp-create-support-ticket">
	<h2><?php esc_html_e( 'SearchWP Help', 'searchwp' ); ?></h2>
	<?php if ( 'valid' !== SWP()->status ) { ?>
		<p><?php echo wp_kses( __( 'Support is available only to <strong>active license holders</strong>. You must activate your license to receive support. If you do not have a license you may purchase one at any time.', 'searchwp' ), array( 'strong' => array() ) ); ?></p>
		<p><a class="button" href="<?php echo esc_url( add_query_arg( array( 'page' => 'searchwp', 'tab' => 'license' ) ), admin_url( 'options-general.php' ) ); ?>"><?php esc_html_e( 'Activate License', 'searchwp' ); ?></a> <a class="button-primary" href="https://searchwp.com/buy/"><?php esc_html_e( 'Purchase License', 'searchwp' ); ?></a></p>
		<style type="text/css">
			.swpnotice {
				text-align:center;
				border:1px solid #fae985;
				background:#FFF9D4;
				color:#424242;
				font-weight:bold;
				padding:1em;
				border-radius:1px;
			}
		</style>
	<?php } else {
	$current_user = wp_get_current_user();
	$conflicts_var = '';
	$conflicts = new SearchWP_Conflicts();
	if ( ! empty( $conflicts->search_template_conflicts ) ) {
		// strip out the full disk path
		$search_template = str_replace( get_theme_root(), '', $conflicts->search_template );
		$conflicts_var = $search_template . ':';
		foreach ( $conflicts->search_template_conflicts as $line_number => $the_conflicts ) {
			$conflicts_var .= $line_number . ',';
		}
		$conflicts_var = substr( $conflicts_var, 0, strlen( $conflicts_var ) - 1 ); // trim off the trailing comma
	}

	/** @noinspection PhpUndefinedFieldInspection */
	$iframe_url = add_query_arg( array(
		'support'       => 1,
		'f'             => 6,
		'dd'            => 0,
		'dt'            => 0,
		'license'       => SWP()->license,
		'email'         => urlencode( $current_user->user_email ),
		'url'           => urlencode( home_url() ),
		'env'           => defined( 'WPE_APIKEY' ) ? 'wpe' : 0, // WP Engine has it's own set of problems so it's good to know right away
		'wpegov'        => defined( 'WPE_GOVERNOR' ) && false === WPE_GOVERNOR ? 1 : 0, // whether WPE governor has been disabled
		'conflicts'     => urlencode( $conflicts_var ),
		'searchwp_v'    => urlencode( get_option( 'searchwp_version' ) ),
		'wp_v'          => urlencode( get_bloginfo( 'version' ) ),
		'php_v'         => urlencode( PHP_VERSION ),
		'mysql_v'       => urlencode( $wpdb->db_version() ),
	), 'https://searchwp.com/gfembed/' );
	$ticket_create_url = $iframe_url; ?>
	<div class="searchwp-ticket-create-wrapper">
		<iframe src="<?php echo esc_url( $ticket_create_url ); ?>" frameborder="0"></iframe>
	</div>
	<?php } ?>
</div>

<hr />

<div class="searchwp-system-info">
	<h3><?php esc_html_e( 'System Information', 'searchwp' ); ?></h3>
	<?php $search_template = locate_template( 'search.php' ) ? locate_template( 'search.php' ) : locate_template( 'index.php' ); ?>
	<p><?php echo wp_kses( sprintf( __( 'When submitting this information to support staff it will also be helpful if you can create a <a href="%s">Gist</a> of your search results template which is found here:', 'searchwp' ), 'https://gist.github.com' ), array( 'a' => array( 'href' => array() ) ) ); ?></p>
	<p><code><?php echo esc_html( $search_template ); ?></code></p>
	<p class="description"><?php echo wp_kses( sprintf( __( 'Please provide this information (ideally as a link to a <a href="%s">Gist</a>) when requested by support staff', 'searchwp' ), 'https://gist.github.com' ), array( 'a' => array( 'href' => array() ) ) ); ?></p>
	<?php
	$searchwp_system_info = new SearchWP_System_Info();
	$searchwp_system_info->output();
	?>
</div>