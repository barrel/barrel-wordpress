<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SearchWP_Settings_Implementation_Advanced
 */
class SearchWP_Settings_Implementation_Advanced {

	/**
	 * @var array Verified action names
	 */
	private $pending_actions = array();

	private $available_toggles = array();

	private $toggle_nonce_prefix = 'swp_settings_t_';

	private $settings_name = 'advanced';

	/**
	 * SearchWP_Settings_Implementation_Advanced constructor.
	 */
	function __construct() {

		// implement our toggles and handling of those toggles
		add_action( 'wp_ajax_searchwp_advanced_setting_toggle', array( $this, 'handle_ajax_advanced_setting_toggle' ) );

		$this->implement_toggle( array(
			'name'              => 'debugging',
			'description'       => __( 'Debugging enabled', 'searchwp' )
		), array( $this, 'when_toggle_debugging_enabled' ) );

		$this->implement_toggle( array(
			'name'              => 'indexer_alternate',
			'description'       => __( 'Use alternate indexer', 'searchwp' )
		), array( $this, 'when_toggle_indexer_alternate_enabled' ) );

		$this->implement_toggle( array(
			'name'              => 'indexer_aggressiveness',
			'description'       => __( 'Reduced indexer aggressiveness', 'searchwp' )
		), array( $this, 'when_toggle_indexer_aggressiveness_enabled' ) );

		$this->implement_toggle( array(
			'name'              => 'min_word_length',
			'description'       => __( 'Disable minimum word length', 'searchwp' )
		), array( $this, 'when_toggle_min_word_length_enabled' ) );

		$this->implement_toggle( array(
			'name'              => 'admin_search',
			'description'       => __( 'Use SearchWP for Admin/Dashboard searches', 'searchwp' )
		), array( $this, 'when_toggle_admin_search_enabled' ) );

		$this->implement_toggle( array(
			'name'              => 'disable_indexer',
			'description'       => __( 'Prevent the indexer from automatically running', 'searchwp' )
		), array( $this, 'when_toggle_disable_indexer_enabled' ) );

		$this->implement_toggle( array(
			'name'              => 'exclusive_regex_matches',
			'description'       => __( 'Exclusive regex matches', 'searchwp' )
		), array( $this, 'when_toggle_exclusive_regex_matches_enabled' ) );

		$this->implement_toggle( array(
			'name'              => 'toggle_nuke_on_delete',
			'description'       => __( 'Remove <strong>all traces</strong> of SearchWP upon plugin deletion (including index)', 'searchwp' )
		), array( $this, 'when_toggle_toggle_nuke_on_delete_enabled' ) );
	}

	/**
	 * Callback for debugging toggle; enables debugging
	 *
	 * @since 2.8
	 */
	function when_toggle_debugging_enabled() {
		add_filter( 'searchwp_debug', '__return_true', 20 );
	}

	/**
	 * Callback for alternate indexer toggle; enables alternate indexer
	 *
	 * @since 2.8
	 */
	function when_toggle_indexer_alternate_enabled() {
		add_filter( 'searchwp_alternate_indexer', '__return_true', 20 );
	}

	/**
	 * Callback for indexer aggressiveness toggle; scales back how fast the indexer runs
	 *
	 * @since 2.8
	 */
	function when_toggle_indexer_aggressiveness_enabled() {
		add_filter( 'searchwp_index_chunk_size', array( $this, 'modify_searchwp_index_chunk_size' ), 20 );
		add_filter( 'searchwp_process_term_limit', array( $this, 'modify_searchwp_process_term_limit' ), 20 );
	}

	/**
	 * Callback for searchwp_index_chunk_size hook in when_toggle_indexer_aggressiveness_enabled() method
	 *
	 * @since 2.8
	 *
	 * @return int
	 */
	function modify_searchwp_index_chunk_size() {
		return 3;
	}

	/**
	 * Callback for searchwp_process_term_limit hook in when_toggle_indexer_aggressiveness_enabled() method
	 *
	 * @since 2.8
	 *
	 * @return int
	 */
	function modify_searchwp_process_term_limit() {
		return 250;
	}

	/**
	 * Callback for minimum word length toggle; disables minimum word length
	 *
	 * @since 2.8
	 */
	function when_toggle_min_word_length_enabled() {
		add_filter( 'searchwp_minimum_word_length', array( $this, 'modify_searchwp_minimum_word_length' ), 20 );
	}

	/**
	 * Callback for modify_searchwp_minimum_word_length hook in when_toggle_min_word_length_enabled() method
	 *
	 * @since 2.8
	 *
	 * @return int
	 */
	function modify_searchwp_minimum_word_length() {
		return 1;
	}

	/**
	 * Callback for admin search toggle; enables SearchWP in the WP Admin/Dashboard
	 *
	 * @since 2.8
	 */
	function when_toggle_admin_search_enabled() {
		add_filter( 'searchwp_in_admin', '__return_true', 20 );
	}

	/**
	 * Callback for indexer toggle; disables indexer
	 *
	 * @since 2.8
	 */
	function when_toggle_disable_indexer_enabled() {
		add_filter( 'searchwp_indexer_enabled', '__return_false', 20 );
	}

	/**
	 * Callback for exclusive regex matches toggle; enables exclusive regex matches
	 *
	 * @since 2.8
	 */
	function when_toggle_exclusive_regex_matches_enabled() {
		add_filter( 'searchwp_exclusive_regex_matches', '__return_true', 20 );
	}

	/**
	 * Callback for Nuke on Delete toggle; enables Nuke on Delete
	 *
	 * @since 2.8
	 */
	function when_toggle_toggle_nuke_on_delete_enabled() {
		add_filter( 'searchwp_nuke_on_delete', '__return_true', 20 );
	}

	/**
	 * Callback for toggle AJAX request
	 *
	 * @since 2.8
	 */
	function handle_ajax_advanced_setting_toggle() {
		$name = isset( $_REQUEST['toggle_name'] ) ? $_REQUEST['toggle_name'] : 0;

		check_ajax_referer( $this->toggle_nonce_prefix . $name, 'nonce' );

		if ( ! array_key_exists( $name, $this->available_toggles ) ) {
			die('-1');
		}

		// get the existing value
		$existing_settings = searchwp_get_option( $this->settings_name );
		if ( ! is_array( $existing_settings ) ) {
			$existing_settings = array();
		}

		if ( ! array_key_exists( $name, $existing_settings ) ) {
			$existing_settings[ $name ] = 0;
		}

		// swap it
		$existing_settings[ $name ] = empty( $existing_settings[ $name ] ) ? 1 : 0;

		// save the updated value
		searchwp_update_option( $this->settings_name, $existing_settings );
	}

	/**
	 * Initializer; hook navigation tab (and corresponding view) and any custom functionality
	 */
	function init() {

		// render the 'Advanced' tab on the settings screen
		add_action( 'searchwp_settings_nav_tab', array( $this, 'render_tab_advanced' ), 200 );

		// render the 'Advanced' view when the 'Advanced' tab is viewed
		add_action( 'searchwp_settings_view\advanced', array( $this, 'render_view_advanced' ) );

		// view-specific actions
		add_action( 'searchwp_settings_before\advanced', array( $this, 'maybe_import_settings' ) );

		add_action( 'searchwp_settings_footer', array( $this, 'check_for_db_tables' ) );
	}

	/**
	 * Output a notice on all settings screen if the database tables went missing
	 */
	function check_for_db_tables() {
		$valid_database_environment = SWP()->custom_db_tables_exist();
		if ( ! $valid_database_environment && ( ! isset( $_GET['action'] ) || 'recreate_db_tables' !== $_GET['action'] ) ) {
			?>
			<div id="setting-error-swp_custom_tables" class="error notice">
				<p>
					<strong><?php esc_html_e( 'Database tables missing! Recreate them on the Advanced Settings screen.', 'searchwp' ); ?></strong>
				</p>
			</div>
		<?php
		}
	}

	/**
	 * Render the tab if current user has appropriate capability
	 */
	function render_tab_advanced() {
		if ( current_user_can( apply_filters( 'searchwp_settings_cap', 'manage_options' ) ) ) {
			searchwp_get_nav_tab( array(
				'tab'   => 'advanced',
				'label' => __( 'Advanced', 'searchwp' ),
			) );
		}
	}

	/**
	 * Fully implements an action in the UI. An action is a button that when clicked (and verified) fires the passed $callback.
	 *
	 * @param $args
	 * @param $callback
	 *
	 * @return bool
	 */
	function implement_action( $args, $callback ) {
		$defaults = array(
			'name'                  => '',
			'label'                 => '',
			'heading'               => '',
			'description'           => '',
			'results_message'       => __( 'Done', 'searchwp' ),
			'results_classes'       => 'updated',
			'hide_after_trigger'    => false,
		);

		$args = wp_parse_args( $args, $defaults );

		$nonce_prefix = 'swp_settings_a_';

		// first we process the callback if the proper trigger is in place and the nonce validates
		if ( isset( $_GET['action'] ) && isset( $_GET['nonce'] ) && $args['name'] === $_GET['action'] ) {
			if ( wp_verify_nonce( sanitize_text_field( $_GET['nonce'] ), $nonce_prefix . sanitize_text_field( $args['name'] ) ) && current_user_can( SWP()->settings_cap ) ) {
				$this->pending_actions[] = sanitize_text_field( $args['name'] );
				// fire the callback
				call_user_func_array( $callback, array() );
				?>
				<?php if ( ! empty( $args['results_message'] ) ) : ?>
					<div class="<?php echo esc_attr( $args['results_classes'] ); ?>"><p><?php echo wp_kses_post( $args['results_message'] ); ?></p></div>
				<?php endif; ?>
				<?php

				if ( ! empty( $args['hide_after_trigger'] ) ) {
					return true;
				}
			} else {
				wp_die( esc_html__( 'Invalid request', 'searchwp' ) );
			}
		}

		// heading will fall back to label if it's not set
		if ( empty( $args['heading'] ) ) {
			$args['heading'] = $args['label'];
		}

		// every action gets a nonce
		$nonce = wp_create_nonce( $nonce_prefix . sanitize_text_field( $args['name'] ) );
		$the_link = add_query_arg(
			array(
				'action' => $args['name'],
				'nonce'  => $nonce,
			)
		);

		?>
			<a class="button searchwp-action-trigger" style="vertical-align:middle;" id="swp-indexer-<?php echo esc_attr( $args['name'] ); ?>" href="<?php echo esc_url( $the_link ); ?>"><?php echo esc_html( $args['label'] ); ?></a>
		<?php

		return true;
	}

	/**
	 * Fully implements a toggle (checkbox) in the UI. Also implements the callback for each
	 * toggle, fired when the toggle is enabled.
	 *
	 * @param $args
	 * @param $callback
	 *
	 * @since 2.8
	 *
	 * @return bool
	 */
	function implement_toggle( $args, $callback ) {
		$defaults = array(
			'name'                  => '',
			'description'           => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$field_name = sanitize_text_field( $args['name'] );

		// make our (valid) callback for this toggle available, it'll get called based on the field name
		$this->available_toggles[ $field_name ] = array(
			'name'      => $field_name,
			'args'      => $args,
			'callback'  => $callback,
		);

		// get the stored options
		$saved_settings = searchwp_get_option( $this->settings_name );

		// if toggle is enabled, fire the callback
		if (
			is_array( $saved_settings)
			&& array_key_exists( $field_name, $saved_settings )
			&& ! empty( $saved_settings[ $field_name ] )
		) {
			call_user_func_array( $callback, array() );
		}

	}

	/**
	 * Output the toggle form element
	 *
	 * @param $toggle
	 */
	private function output_toggle( $toggle ) {

		$saved_settings = searchwp_get_option( $this->settings_name );

		if ( ! is_array( $saved_settings ) ) {
			$saved_settings = array();
		}

		if ( ! array_key_exists( $toggle['name'], $saved_settings ) ) {
			$saved_settings[ $toggle['name'] ] = 0;
		}

		$nonce = wp_create_nonce( $this->toggle_nonce_prefix . sanitize_text_field( $toggle['name'] ) ); // already prefixed

		?>
		<div class="searchwp-checkbox">
			<input type="checkbox" data-toggle_name="<?php echo esc_attr( $toggle['name'] ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>" name="<?php echo esc_attr( $toggle['name'] ); ?>" id="<?php echo esc_attr( $toggle['name'] ); ?>" value="1" <?php checked( $saved_settings[ $toggle['name'] ], 1 ); ?>/>
			<label for="<?php echo esc_attr( $toggle['name'] ); ?>">
				<?php echo wp_kses( $toggle['args']['description'], array( 'a', array( 'href' => array() ), 'strong' => array() ) ); ?>
			</label>
		</div>
		<?php
	}

	/**
	 * Render view callback
	 */
	function render_view_advanced() { ?>
		<div class="searchwp-advanced-settings-wrapper swp-group">
			<div class="searchwp-advanced-settings-actions">
				<div class="postbox swp-meta-box metabox-holder searchwp-settings-action">
					<h3 class="hndle">
						<span><?php esc_html_e( 'Actions', 'searchwp' ); ?></span>
						<a class="searchwp-trigger-help" href="https://searchwp.com/docs/settings/#advanced" target="_blank">Help &raquo;</a>
					</h3>
					<div class="inside">
						<?php

						$valid_database_environment = SWP()->custom_db_tables_exist();
						if ( ! $valid_database_environment ) {
							$this->implement_action( array(
								'name'               => 'recreate_db_tables',
								'label'              => __( 'Recreate Database Tables', 'searchwp' ),
								'description'        => __( "SearchWP's database tables cannot be found. This may happen if a site migration was incomplete. Recreate the tables and initiate an index build.", 'searchwp' ),
								'results_message'    => sprintf( __( 'Database tables created! <a href="%s">Rebuild index &raquo;</a>', 'searchwp' ), admin_url( 'options-general.php?page=searchwp' ) ),
								'hide_after_trigger' => true,
							), array( $this, 'recreate_db_tables' ) );
						}

						$this->implement_action( array(
							'name'              => 'index_reset',
							'label'             => __( 'Reset Index', 'searchwp' ),
							'description'       => __( '<strong>Completely</strong> empty the index. <em>Search statistics will be left as is.</em>', 'searchwp' ),
							'results_message'   => sprintf( __( 'The index <strong>has been emptied</strong>. <a href="%s">Rebuild index &raquo;</a>', 'searchwp' ), admin_url( 'options-general.php?page=searchwp' ) ),
						), array( $this, 'reset_index' ) );

						$this->implement_action( array(
							'name'              => 'indexer_wake',
							'label'             => __( 'Wake Up Indexer', 'searchwp' ),
							'description'       => __( 'If the indexer appears to have stalled, try waking it up.', 'searchwp' ),
							'results_message'   => sprintf( __( 'Attempted to wake up the indexer. <a href="%s">View progress &raquo;</a>', 'searchwp' ), admin_url( 'options-general.php?page=searchwp' ) ),
						), array( $this, 'indexer_wake' ) );

						$this->implement_action( array(
							'name'              => 'stats_reset',
							'label'             => __( 'Reset Statistics', 'searchwp' ),
							'description'       => __( '<strong>Completely</strong> reset your Search Statistics. <em>Existing index will be left as is.</em>', 'searchwp' ),
							'results_message'   => __( 'Search statistics reset', 'searchwp' ),
						), array( $this, 'reset_stats' ) );

						$this->implement_action( array(
							'name'              => 'conflict_notices_reset',
							'label'             => __( 'Restore Conflict Notices', 'searchwp' ),
							'description'       => __( 'Restore all dismissed conflict notifications.', 'searchwp' ),
							'results_message'   => __( 'Conflict notices restored', 'searchwp' ),
						), array( $this, 'conflict_notices_reset' ) );

						?>
					</div>
				</div>
				<div class="postbox swp-meta-box metabox-holder searchwp-settings-action">
					<h3 class="hndle">
						<span><?php esc_html_e( 'Settings', 'searchwp' ); ?></span>
						<b class="searchwp-tag searchwp-tag-success" id="searchwp-tag-settings-saved"><?php esc_html_e( 'Saved!', 'searchwp' ); ?></b>
						<a class="searchwp-trigger-help" href="https://searchwp.com/docs/settings/#advanced" target="_blank">Help &raquo;</a>
					</h3>
					<div class="inside">
						<?php
							foreach ( $this->available_toggles as $toggle ) {
								$this->output_toggle( $toggle );
							}
						?>

						<!--suppress JSUnusedLocalSymbols -->
						<script type="text/javascript">
							jQuery(document).ready(function($){

								var $tag = $('#searchwp-tag-settings-saved');
								$tag.css('opacity',0.01);

								$(document).on('change','.searchwp-checkbox input',function(){
									var data = {
										action: 'searchwp_advanced_setting_toggle',
										toggle_name: $(this).data('toggle_name'),
										nonce: $(this).data('nonce'),
										time: new Date().getTime()
									};

									// fire off the toggle request
									$.post(ajaxurl + '?' + data.time, data, function(response) {
										$tag.fadeTo('fast', 1, function(){
											setTimeout(function(){
												$tag.fadeTo('slow',0.01);
											},600);
										});
									});
								});
							});
						</script>

					</div>
				</div>
			</div>
			<div class="searchwp-advanced-settings-stats" style="visibility: hidden;">
				<div class="postbox swp-meta-box metabox-holder searchwp-settings-stats">
					<h3 class="hndle">
						<span><?php esc_html_e( 'Index Statistics', 'searchwp' ); ?></span>
					</h3>
					<?php $stats = SWP()->settings['stats']; ?>
					<div class="inside">
						<p><?php echo wp_kses( sprintf( __( 'The indexer reacts to edits made and will apply updates accordingly. <a href="%s" target="_blank">More information &raquo;</a>', 'searchwp' ), 'https://searchwp.com/docs/kb/how-searchwp-works/' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ); ?></p>
						<?php
						$advanced_settings = searchwp_get_option( 'advanced' );
						if ( ! empty( $advanced_settings['admin_search'] ) ) :
							?>
							<p class="description"><?php esc_html_e( 'Admin/Dashboard searching has been enabled, which requires additional resources. Disabled post types will ONLY be utilized when searching in the Admin/Dashboard, not the front end.', 'searchwp' ); ?></p>
						<?php endif; ?>
						<table class="searchwp-data-vis" cellpadding="0" cellspacing="0">
							<tbody>
								<?php if ( isset( $stats['last_activity'] ) ) : ?>
									<tr>
										<th><?php esc_html_e( 'Last Activity', 'searchwp' ); ?></th>
										<td>
											<?php echo esc_html( date_i18n( get_option( 'date_format' ), $stats['last_activity'] ) ); ?>
											<?php echo esc_html( date( 'H:i:s', $stats['last_activity'] ) ); ?>
										</td>
									</tr>
								<?php endif; ?>
								<?php if ( isset( $stats['done'] ) ) : ?>
									<tr>
										<th><?php esc_html_e( 'Indexed', 'searchwp' ); ?></th>
										<td><code><?php echo absint( $stats['done'] ); ?></code> <?php echo 1 === absint( $stats['done'] ) ? esc_html__( 'entry', 'searchwp' ) : esc_html__( 'entries', 'searchwp' ); ?></td>
									</tr>
								<?php endif; ?>
								<?php if ( isset( $stats['remaining'] ) ) : ?>
									<tr>
										<th><?php esc_html_e( 'Unindexed', 'searchwp' ); ?></th>
										<td><code><?php echo absint( $stats['remaining'] ); ?></code> <?php echo 1 === absint( $stats['remaining'] ) ? esc_html__( 'entry', 'searchwp' ) : esc_html__( 'entries', 'searchwp' ); ?></td>
									</tr>
								<?php endif; ?>
								<?php
									$indexer = new SearchWPIndexer();
									$row_count = $indexer->get_main_table_row_count();
								?>
								<tr>
									<th><?php esc_html_e( 'Main row count', 'searchwp' ); ?></th>
									<td><code><?php echo absint( $row_count ); ?></code> <?php echo 1 === absint( $row_count ) ? esc_html__( 'row', 'searchwp' ) : esc_html__( 'rows', 'searchwp' ); ?></td>
								</tr>
							</tbody>
						</table>
						<p class="description"><?php esc_html_e( 'Note: the index is always kept as small as possible.', 'searchwp' ); ?></p>
					</div>
				</div>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {

//					var $stats_meta_box = $('.searchwp-settings-stats'),
//						$actions_meta_boxes = $('.searchwp-common-actions');
//
//					if($stats_meta_box.outerHeight()<$actions_meta_boxes.outerHeight()){
//						$stats_meta_box.height($actions_meta_boxes.outerHeight()-$stats_meta_box.css('marginTop').replace('px','')-$stats_meta_box.css('marginBottom').replace('px','')-2);
//					}

					$('#swp-indexer-index_reset').click(function () {
						if (confirm('<?php echo esc_js( __( 'Are you SURE you want to delete the entire SearchWP index?', 'searchwp' ) ); ?>')) {
							return confirm('<?php echo esc_js( __( 'Are you completely sure? THIS CAN NOT BE UNDONE!', 'searchwp' ) ); ?>');
						}
						return false;
					});
					$('#swp-indexer-stats_reset').click(function () {
						if (confirm('<?php echo esc_js( __( 'Are you SURE you want to completely reset your Search Stats?', 'searchwp' ) ); ?>')) {
							return confirm('<?php echo esc_js( __( 'Are you completely sure? THIS CAN NOT BE UNDONE!', 'searchwp' ) ); ?>');
						}
						return false;
					});
					$('.searchwp-show-less-common-actions a').click(function(e){
						e.preventDefault();
						$('.searchwp-show-less-common-actions').hide();
						$('.searchwp-less-common-actions').show();
					});
				});
			</script>
		</div>
		<?php
		include dirname( __FILE__ ) . '/export-import.php';
	}

	/**
	 * Returns whether an action name has fully passed the nonce check
	 *
	 * @param $action_name
	 *
	 * @return bool
	 */
	function is_valid_action_request( $action_name ) {
		return in_array( $action_name, $this->pending_actions, true );
	}

	/**
	 * Callback for Reset Index action
	 */
	function reset_index() {
		if ( ! $this->is_valid_action_request( 'index_reset' ) ) {
			return;
		}

		// Reset the dirty index flag used by Vue
		searchwp_set_setting( 'index_dirty', false );

		do_action( 'searchwp_log', 'Resetting the index' );
		SWP()->purge_index();
	}

	/**
	 * Callback for Reset Stats action
	 */
	function reset_stats() {
		if ( ! $this->is_valid_action_request( 'stats_reset' ) ) {
			return;
		}

		do_action( 'searchwp_log', 'Resetting stats' );
		SWP()->reset_stats();
	}

	/**
	 * Callback for Wake Indexer action
	 */
	function indexer_wake() {
		if ( ! $this->is_valid_action_request( 'indexer_wake' ) ) {
			return;
		}

		do_action( 'searchwp_log', 'Waking up the indexer' );
		searchwp_wake_up_indexer();
		SWP()->trigger_index();
	}

	/**
	 * Callback for Toggle Indexer action
	 */
	function indexer_toggle() {
		if ( ! $this->is_valid_action_request( 'indexer_toggle' ) ) {
			return;
		}

		$paused = searchwp_get_option( 'paused' );
		$paused = empty( $paused ) ? false : true;

		// we have to output custom messaging here because these functions fire too late to reflect a proper status
		if ( $paused ) {
			SWP()->indexer_unpause();
			?><style type="text/css">.swp-notices .updated { display:none !important; }</style><?php
		} else {
			SWP()->indexer_pause();
			?><div class="updated notice"><p><?php echo wp_kses_post( __( 'The SearchWP indexer is currently <strong>disabled</strong>', 'searchwp' ) ); ?></p></div><?php
		}
	}

	/**
	 * Callback for Toggle Indexer action
	 */
	function toggle_nuke_on_delete() {
		if ( ! $this->is_valid_action_request( 'toggle_nuke_on_delete' ) ) {
			return;
		}

		$nuke_on_delete = searchwp_get_setting( 'nuke_on_delete' );
		$nuke_on_delete = empty( $nuke_on_delete ) ? false : true;

		// we have to output custom messaging here because these functions fire too late to reflect a proper status
		if ( $nuke_on_delete ) {
			searchwp_set_setting( 'nuke_on_delete', false );
			?><?php
		} else {
			searchwp_set_setting( 'nuke_on_delete', true );
			?><div class="updated notice"><p><?php echo wp_kses( __( 'Nuke on Delete <strong>enabled</strong>', 'searchwp' ), array( 'strong' => array() ) ); ?></p></div><?php
		}
	}

	/**
	 * Callback if user chose to restore conflict notices
	 */
	function conflict_notices_reset() {
		if ( ! $this->is_valid_action_request( 'conflict_notices_reset' ) ) {
			return;
		}

		$existing_dismissals = searchwp_get_setting( 'dismissed' );
		$existing_dismissals['filter_conflicts'] = array();
		searchwp_set_setting( 'dismissed', $existing_dismissals );
	}

	/**
	 * Callback if user chose to recreate custom database tables
	 */
	function recreate_db_tables() {
		if ( ! $this->is_valid_action_request( 'recreate_db_tables' ) ) {
			return;
		}

		$upgrader = new SearchWPUpgrade();
		$upgrader->create_tables();

		SWP()->purge_index();

		$database_tables_recreated = SWP()->custom_db_tables_exist();

		if ( ! $database_tables_recreated ) {
			?>
			<div class="error notice">
				<p><?php esc_html_e( 'There was an error recreating the database tables.', 'searchwp' ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Callback if user chose to import settings
	 */
	function maybe_import_settings() {
		if ( isset( $_POST['searchwp_action'] )
		     && isset( $_REQUEST['_wpnonce'] )
		     && wp_verify_nonce( $_REQUEST['_wpnonce'], 'searchwp_import_engine_config' )
		     && 'import_engine_config' === $_POST['searchwp_action']
		     && isset( $_REQUEST['searchwp_import_source'] )
		) {
			$settings_to_import = stripslashes( $_REQUEST['searchwp_import_source'] );
			SWP()->import_settings( $settings_to_import );
			?>
			<div class="updated">
				<p><?php esc_html_e( 'Settings imported', 'searchwp' ); ?></p>
			</div>
		<?php
		}
	}
}

$searchwp_advanced_settings = new SearchWP_Settings_Implementation_Advanced();
$searchwp_advanced_settings->init();
