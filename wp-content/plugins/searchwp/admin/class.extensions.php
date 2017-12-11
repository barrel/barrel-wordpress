<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_Extensions
 */
class SearchWP_Extensions {

	private $extensions;

	/**
	 * SearchWP_Extensions constructor.
	 */
	function __construct() {
		add_action( 'init', array( $this, 'back_compat' ) );
	}

	/**
	 * Initialize Extensions
	 */
	function init() {

		add_action( 'searchwp_load', array( $this, 'prime_extensions' ) );

		add_action( 'searchwp_settings_nav_tab', array( $this, 'render_tab_extensions' ), 2000 );
		add_action( 'searchwp_settings_view\extensions', array( $this, 'render_view_extension' ) );

		add_action( 'searchwp_settings_footer', array( $this, 'render_extensions_dropdown' ) );
	}

	/**
	 * Output the Extensions tab on the settings screen
	 */
	function render_tab_extensions() {
		if ( ! empty( $this->extensions ) ) {
			searchwp_get_nav_tab( array(
				'tab'   => 'extensions',
				'label' => __( 'Extensions', 'searchwp' ),
			) );
		}
	}

	/**
	 * Render the view for the extension
	 */
	function render_view_extension() {
		// check to see if we need to display an extension settings page
		if ( ! empty( $this->extensions ) && isset( $_GET['extension'] ) ) {
			foreach ( $this->extensions as $extension => $attributes ) { // find out which extension we're working with
				if ( isset( $attributes->slug ) && $attributes->slug === $_GET['extension'] ) {
					if ( method_exists( $this->extensions[ $extension ], 'view' ) ) {
						?>
						<div class="wrap" id="searchwp-<?php echo esc_attr( $attributes->slug ); ?>-wrapper">
							<div id="icon-options-general" class="icon32"><br /></div>
							<div class="<?php echo esc_attr( $attributes->slug ); ?>-container">
								<h2>SearchWP <?php echo esc_html( $attributes->name ); ?></h2>
								<?php $this->extensions[ $extension ]->view(); ?>
							</div>
							<p class="searchwp-extension-back">
								<a href="<?php echo esc_url( admin_url( 'options-general.php?page=searchwp' ) ); ?>"><?php esc_html_e( 'Back to SearchWP Settings', 'searchwp' ); ?></a>
							</p>
						</div>
					<?php
					}
					break;
				}
			}
			return;
		}
	}

	/**
	 * Perform initial Extension setup
	 *
	 * @since 1.3.1
	 */
	function prime_extensions() {

		$searchwp = SWP();

		// implement extensions
		$this->extensions = apply_filters( 'searchwp_extensions', array() );

		if ( is_array( $this->extensions ) && ! empty( $this->extensions ) ) {
			foreach ( $this->extensions as $extension => $path ) {
				$class_name = 'SearchWP' . $extension;

				if ( ! class_exists( $class_name ) && file_exists( $path ) ) {
					/** @noinspection PhpIncludeInspection */
					include_once( $path );
				}

				$this->extensions[ $extension ] = new $class_name( $this );

				// add plugin row action
				if ( isset( $this->extensions[ $extension ]->min_searchwp_version ) && version_compare( $searchwp->version, $this->extensions[ $extension ]->min_searchwp_version, '<' ) ) {
					do_action( 'searchwp_log', 'after_plugin_row_' . plugin_basename( $path ) );
					add_action( 'after_plugin_row_' . plugin_basename( $path ), array( $this, 'plugin_row' ), 11, 3 );
				}
			}
		}
	}

	/**
	 * Extensions that have a settings screen receive an entry in the Extensions dropdown
	 * which is injected via JavaScript to the standard settings navigation tabs
	 */
	function render_extensions_dropdown() {
		if ( ! empty( $this->extensions ) ) : ?>
			<div id="searchwp-extensions-dropdown">
				<ul class="swp-dropdown-menu">
					<?php foreach ( $this->extensions as $extension ) : ?>
						<?php if ( ! empty( $extension->public ) && isset( $extension->slug ) && isset( $extension->name ) ) : ?>
							<?php
							$the_link = add_query_arg(
								array(
									'page'      => 'searchwp',
									'tab'       => 'extensions',
									'extension' => $extension->slug,
								),
								admin_url( 'options-general.php' )
							);
							?>
							<li><a href="<?php echo esc_url( $the_link ); ?>"><?php echo esc_html( $extension->name ); ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					var $extensions_toggler, $extensions_dropdown, offset_y, offset_x;
					$extensions_toggler = $('.nav-tab-extensions');
					$extensions_dropdown = $('#searchwp-extensions-dropdown');
					offset_y = 0;
					offset_x = 0;

					// prep the UI
					$extensions_dropdown.hide();
					$extensions_toggler.data('showing',false);

					// bind the click
					$extensions_toggler.click(function(e){
						e.preventDefault();
						if($extensions_toggler.data('showing')){
							$extensions_dropdown.hide();
							$extensions_toggler.data('showing',false);
							$extensions_toggler.removeClass('searchwp-showing-dropdown');
							$extensions_dropdown.removeClass('searchwp-sub-menu-active');
						}else{
							offset_y = $extensions_toggler.position().top + $extensions_toggler.outerHeight() - 1; // border
							offset_x = $extensions_toggler.position().left - parseInt( $extensions_toggler.css('paddingLeft').replace('px',''),10) - 7; // 7px offset

							if($extensions_toggler.hasClass('nav-tab-active')){
								$extensions_dropdown.addClass('searchwp-sub-menu-active');
								offset_y+=1;
							}else{
								$extensions_dropdown.removeClass('searchwp-sub-menu-active');
							}
							$extensions_dropdown.css('top',offset_y+'px').css('left',offset_x+'px').show();
							$extensions_toggler.data('showing',true);
							$extensions_toggler.addClass('searchwp-showing-dropdown');
						}
					});
				});
			</script>
		<?php endif;
	}

	/**
	 * The SearchWP Extension API changed in version 2.6, so we need a back compat handler
	 */
	function back_compat() {

		// Diagnostics uses form submissions
		if (
			isset( $_REQUEST['page'] )
			&& 'searchwp' === $_REQUEST['page']
			&& isset( $_REQUEST['extension'] )
			&& 'diagnostics' === $_REQUEST['extension']
			&& isset( $_REQUEST['nonce'] )
			&& ! isset( $_REQUEST['tab'] )
		) {
			// redirect to the proper URL as of SearchWP 2.6
			$diagnostics_endpoint = add_query_arg(
				array(
					'page'                          => 'searchwp',
					'tab'                           => 'extensions',
					'extension'                     => 'diagnostics',
					'nonce'                         => $_REQUEST['nonce'],
				),
				admin_url( 'options-general.php' )
			);

			if ( isset( $_REQUEST['searchwp_diagnostics_action'] ) ) {
				$diagnostics_endpoint = add_query_arg(
					array(
						'searchwp_diagnostics_action' => $_REQUEST['searchwp_diagnostics_action'],
					),
					$diagnostics_endpoint
				);
			}

			if ( isset( $_REQUEST['searchwp_diagnostics_nonce'] ) ) {
				$diagnostics_endpoint = add_query_arg(
					array(
						'searchwp_diagnostics_nonce' => $_REQUEST['searchwp_diagnostics_nonce'],
					),
					$diagnostics_endpoint
				);
			}

			if ( isset( $_REQUEST['searchwp_diagnostics_hash'] ) ) {
				$diagnostics_endpoint = add_query_arg(
					array(
						'searchwp_diagnostics_hash' => $_REQUEST['searchwp_diagnostics_hash'],
					),
					$diagnostics_endpoint
				);
			}

			if ( isset( $_REQUEST['swp_diagnostics_post_id'] ) ) {
				$diagnostics_endpoint = add_query_arg(
					array(
						'swp_diagnostics_post_id' => $_REQUEST['swp_diagnostics_post_id'],
					),
					$diagnostics_endpoint
				);
			}

			wp_safe_redirect( $diagnostics_endpoint );
		}
	}
}

$searchwp_extensions = new SearchWP_Extensions();
$searchwp_extensions->init();
