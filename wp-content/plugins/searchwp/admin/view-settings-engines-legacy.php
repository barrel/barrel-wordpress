<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

$parent = SWP();

$parent->define_keys();
$lazy_settings = apply_filters( 'searchwp_lazy_settings', false );

// progress of indexer
$remainingPostsToIndex = searchwp_get_setting( 'remaining', 'stats' );
$progress = searchwp_get_option( 'progress' );
if ( ! $parent->is_using_alternate_indexer() && ( ! is_bool( $remainingPostsToIndex ) || ( is_numeric( $progress ) && $progress > 0 && $progress < 100 ) ) ) {
	$remainingPostsToIndex = absint( $remainingPostsToIndex );
	?>
	<div class="postbox swp-in-progress<?php if ( 0 === $remainingPostsToIndex ) : ?> swp-in-progress-done<?php endif; ?>">
		<div class="swp-progress-wrapper">
			<p class="swp-label"><?php esc_html_e( 'Indexing is', 'searchwp' ); ?>
				<span><?php esc_html_e( 'almost', 'searchwp' ); ?></span> <?php esc_html_e( 'complete', 'searchwp' ); ?>
				<a class="swp-tooltip" href="#swp-tooltip-progress">?</a></p>

			<div class="swp-tooltip-content" id="swp-tooltip-progress">
				<?php esc_html_e( 'This process is running in the background. You can leave this page and the index will continue to be built until completion.', 'searchwp' ); ?>
			</div>
			<div class="swp-progress-track">
				<div class="swp-progress-bar"></div>
			</div>
			<p class="description" style="margin:1em 0 0.4em;"><?php echo wp_kses( sprintf( __( 'The indexer has been <strong>temporarily scaled back</strong> to reduce server load. This is monitored automatically. <a href="%s">More information &raquo;</a>', 'searchwp' ), 'http://searchwp.com/?p=11818' ), array( 'strong' => array(), 'a' => array( 'href' => array() ) ) ); ?></p>
		</div>
	</div>
<?php } ?>
	<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
		<div class="swp-wp-settings-api">
			<?php do_settings_sections( $parent->textDomain ); ?>
			<?php settings_fields( SEARCHWP_PREFIX . 'settings' ); ?>
		</div>
		<script type="text/html" id="tmpl-swp-custom-fields">
			<tr class="swp-custom-field">
				<td class="swp-custom-field-select">
					<!--suppress HtmlFormInputWithoutLabel -->
					<select name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][{{ swp.engine }}][{{ swp.postType }}][weights][cf][{{ swp.arrayFlag }}][metakey]" style="width:80%;">
						<option value="searchwpcfdefault"><?php esc_html_e( 'Any', 'searchwp' ); ?></option>
						<?php if ( ! empty( $parent->keys ) ) : foreach ( $parent->keys as $key ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $key ); ?></option>
						<?php endforeach; endif; ?>
					</select>
					<a class="swp-delete" href="#">&times;</a>
				</td>
				<td>
					<!--suppress HtmlFormInputWithoutLabel -->
					<input type="number" min="-1" step="1" class="small-text" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][{{ swp.engine }}][{{ swp.postType }}][weights][cf][{{ swp.arrayFlag }}][weight]" value="1" />
				</td>
			</tr>
		</script>

		<div id="swp-settings-ui-wrapper" class="swp-loading spinner is-active"></div>
		<?php if ( $lazy_settings ) : ?>
			<div id="swp-settings-ui-wrapper" class="swp-loading spinner is-active"></div>
			<script type="text/javascript">
				<?php /** @noinspection PhpIncludeInspection */ include( $parent->dir . '/assets/js/searchwp.js' ); ?>
				jQuery(document).ready(function($){
					var data = {
						action: 'swp_lazy_settings',
						nonce: '<?php echo esc_js( wp_create_nonce( 'swpsettings' ) ); ?>',
						time: new Date().getTime()
					};
					$.post("<?php echo esc_url( admin_url( 'admin-ajax.php' ) . '?' . time() ); ?>", data, function(response) {
						$('#swp-settings-ui-wrapper').removeClass('swp-loading is-active spinner').html($(response));
						searchwp_settings_handler();
					});
				});
			</script>
		<?php else : ?>
			<div id="swp-settings-hook" class="swp-preload">
				<?php /** @noinspection PhpIncludeInspection */ include( $parent->dir . '/admin/settings.php' ); ?>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					$('#swp-settings-ui-wrapper').remove();
					$('.swp-preload').removeClass('swp-preload is-active spinner');
					<?php /** @noinspection PhpIncludeInspection */ include $parent->dir . '/assets/js/searchwp.js'; ?>
					searchwp_settings_handler();
				});
			</script>
		<?php endif; ?>
	</form>
<?php
