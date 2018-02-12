<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Echoes the markup for a supplemental search engine settings UI
 *
 * @param null $engineName The engine name
 * @param null $engineLabel The engine label
 * @since 1.0
 */
function searchwp_supplemental_engine_settings_template( $engineName = null, $engineLabel = null ) { ?>
	<li class="swp-supplemental-engine">
		<div class="swp-supplemental-engine-controls swp-group">
			<div class="swp-supplemental-engine-name">
				<a href="#" class="swp-supplemental-engine-edit-trigger"><?php
				if ( is_null( $engineLabel ) ) {
					echo '{{swp.engineLabel}}';
				} else {
					echo esc_html( $engineLabel ) . ' <code>' . esc_html( $engineName ) . '</code>';
				} ?>
				</a>
				<!--suppress HtmlFormInputWithoutLabel -->
				<input type="text" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php if ( is_null( $engineName ) ) { ?>{{swp.engine}}<?php } { echo esc_attr( $engineName ); } ?>][searchwp_engine_label]" value="<?php
				if ( is_null( $engineLabel ) ) {
					echo '{{swp.engineLabel}}';
				} else {
					echo esc_attr( $engineLabel );
				}
				?>" />
			</div>
			<div class="swp-supplemental-engine-delete">
				<a href="#" class="button swp-del-supplemental-engine"><?php esc_html_e( 'Remove', 'searchwp' ); ?></a>
			</div>
		</div>
		<div class="swp-supplemental-engine-settings"><?php
		if ( is_null( $engineName ) ) {
			echo '{{swp.engineSettings}}';
		} else {
			searchwp_engine_settings_template( $engineName );
		}
		?></div>
	</li>
<?php
}

// @codingStandardsIgnoreStart
/**
 * @deprecated as of 2.5.7
 *
 * @param null $engineName
 * @param null $engineLabel
 *
 * @return bool
 * @internal param string $engine
 *
 */
function searchwpSupplementalEngineSettingsTemplate( $engineName = null, $engineLabel = null ) {
	searchwp_supplemental_engine_settings_template( $engineName, $engineLabel );
}
// @codingStandardsIgnoreEnd