<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( apply_filters( 'searchwp_settings_cap', 'manage_options' ) ) ) {
	die();
}

$searchwp = SWP(); ?>

<?php
/* TODO: Need to localize searchwp.js */
// Since searchwp.js is included inline we can't just localize the script
// so searchwp.js and the handling of searchwp.js needs to be refactored
?>
<div style="display:none;" id="swp-search-placeholder">
	<?php esc_html_e( 'Search for term name', 'searchwp' ); ?>
</div>

<div class="postbox swp-meta-box swp-default-engine metabox-holder swp-jqueryui">

	<?php
		$the_link = admin_url( 'index.php?page=searchwp-stats' ) . '&tab=default';
	?>
	<h3 class="hndle"><span><?php esc_html_e( 'Default Search Engine', 'searchwp' ); ?></span> <a class="swp-engine-stats" href="<?php echo esc_url( $the_link ); ?>"><?php esc_html_e( 'Statistics', 'searchwp' ); ?> &raquo;</a></h3>

	<div class="inside">

		<p><?php esc_html_e( 'These settings will override WordPress default searches. Customize which post types are included in search and how much weight each content type receives.', 'searchwp' ); ?>
			<a class="swp-tooltip" href="#swp-tooltip-overview">?</a></p>

		<div class="swp-tooltip-content" id="swp-tooltip-overview">
			<?php echo wp_kses( __( "Only checked post types will be included in search results. If a post type isn't displayed, ensure <code>exclude_from_search</code> is set to false when registering it.", 'searchwp' ), array( 'code' => array() ) ); ?>
		</div>
		<?php searchwp_engine_settings_template( 'default' ); ?>

	</div>

</div>

<div class="postbox swp-meta-box metabox-holder swp-jqueryui">

	<?php
	// we're only going to output one stats link because it'll clue the user in to there being more
	$supplemental_stats_link = '';
	if ( isset( $searchwp->settings['engines'] ) && is_array( $searchwp->settings['engines'] ) && count( $searchwp->settings['engines'] ) ) {
		foreach ( $searchwp->settings['engines'] as $engineFlag => $engine ) {
			if ( isset( $engine['searchwp_engine_label'] ) && ! empty( $engine['searchwp_engine_label'] ) ) {
				$link_url = admin_url( 'index.php?page=searchwp-stats' ) . '&tab=' . esc_attr( $engineFlag );
				$supplemental_stats_link = '<a class="swp-engine-stats" href="' . esc_url( $link_url ) . '">' . esc_html__( 'Statistics', 'searchwp' ) . ' &raquo;</a>';
				break;
			}
		}
	}
	?>

	<h3 class="hndle"><span><?php esc_html_e( 'Supplemental Search Engines', 'searchwp' ); ?></span> <?php echo wp_kses( $supplemental_stats_link, array( 'a' => array( 'href' => array(), 'class' => array() ) ) ); ?></h3>

	<div class="inside">

		<p><?php esc_html_e( 'Here you can build supplemental search engines to use in specific sections of your site. When used, the default search engine settings are completely ignored.', 'searchwp' ); ?>
			<a class="swp-tooltip" href="#swp-tooltip-supplemental">?</a>
		</p>

		<div class="swp-tooltip-content" id="swp-tooltip-supplemental">
			<?php echo wp_kses( __( "Only checked post types will be included in search results. If a post type isn't displayed, ensure <code>exclude_from_search</code> is set to false when registering it.", 'searchwp' ), array( 'code' => array() ) ); ?>
		</div>

		<script type="text/html" id="tmpl-swp-engine">
			<?php searchwp_engine_settings_template( '{{swp.engine}}' ); ?>
		</script>

		<script type="text/html" id="tmpl-swp-supplemental-engine">
			<?php searchwp_supplemental_engine_settings_template( '{{swp.engine}}' ); ?>
		</script>

		<div class="swp-supplemental-engines-wrapper">
			<ul class="swp-supplemental-engines">
				<?php if ( isset( $searchwp->settings['engines'] ) && is_array( $searchwp->settings['engines'] ) && count( $searchwp->settings['engines'] ) ) : ?>
					<?php foreach ( $searchwp->settings['engines'] as $engineFlag => $engine ) : if ( isset( $engine['searchwp_engine_label'] ) && ! empty( $engine['searchwp_engine_label'] ) ) : ?>
						<?php searchwp_supplemental_engine_settings_template( $engineFlag, $engine['searchwp_engine_label'] ); ?>
					<?php endif; endforeach; ?>
				<?php endif; ?>
			</ul>
			<p>
				<a href="#" class="button swp-add-supplemental-engine"><?php esc_html_e( 'Add New Supplemental Engine', 'searchwp' ); ?></a>
			</p>
		</div>

	</div>

</div>

<div class="swp-settings-footer swp-group">
	<?php submit_button(); ?>
</div>
