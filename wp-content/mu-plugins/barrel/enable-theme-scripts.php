<?php
/*
Plugin Name: Enable Theme Scripts
Plugin URI: https://gitlab.com/barrel/barrel-wordpress/tree/master/wp-content/mu-plugins
Description: Common modules for any WordPress website.
Version: 0.1
Author: Barrel
Author URI: https://www.barrelny.com/
*/

define('BRRL_PLUGIN_DIR',str_replace('\\','/',dirname(__FILE__)));

if ( !class_exists( 'B_Tag_Manager' ) ) {

	class B_Tag_Manager {

		const INSERT_HEADER = 'brrl_insert_header';
		const INSERT_FOOTER = 'brrl_insert_footer';
		const INSERT_AFTERBODY = 'brrl_insert_afterbody';

		function __construct() {

			add_action( 'init', array( &$this, 'init' ) );
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			add_action( 'wp_head', array( &$this, 'wp_head' ) );
			add_action( 'wp_footer', array( &$this, 'wp_footer' ) );
			add_action( 'after_body_open', array( &$this, 'after_body_open' ) );

		}

		function init() {
			load_plugin_textdomain( 'barrel-tag-manager', false, dirname( plugin_basename ( __FILE__ ) ).'/lang' );
		}

		function admin_init() {

			// register settings for sitewide script
			register_setting( 'barrel-tag-manager', self::INSERT_HEADER, 'trim' );
			register_setting( 'barrel-tag-manager', self::INSERT_FOOTER, 'trim' );
			register_setting( 'barrel-tag-manager', self::INSERT_AFTERBODY, 'trim' );

			// add meta box to all post types
			foreach ( get_post_types( '', 'names' ) as $type ) {
				add_meta_box(
					'brrl_post_meta_single', 
					esc_html__( 'Insert Script to &lt;head&gt;', 'barrel-tag-manager' ), 
					array( &$this, 'meta_setup' ), 
					$type, 
					'normal', 
					'high'
				);
			}

			add_action( 'save_post', array( &$this, 'post_meta_save' ) );
		}

		// adds menu item to wordpress admin dashboard
		function admin_menu() {
			$page = add_submenu_page( 
				'options-general.php', 
				__('Script/Tag Manager', 'barrel-tag-manager'), 
				__('Script/Tag Manager', 'barrel-tag-manager'), 
				'manage_options', 
				__FILE__, 
				array( &$this, 'options_html' ) );
			}

		function wp_head() {
			$meta = get_option( self::INSERT_HEADER, '' );
			if ( $meta != '' ) {
				echo $meta, "\n";
			}

			$brrl_post_meta = get_post_meta( get_the_ID(), '_post_head_script' , TRUE );
			if ( $brrl_post_meta != '' ) {
				echo $brrl_post_meta['head_script_code'], "\n";
			}

		}

		function wp_footer() {
			if ( !is_admin() && !is_feed() && !is_robots() && !is_trackback() ) {
				$text = get_option( self::INSERT_FOOTER, '' );
				$text = convert_smilies( $text );
				$text = do_shortcode( $text );

				if ( $text != '' ) {
					echo $text, "\n";
				}
			}
		}

		function after_body_open() {
			if ( !is_admin() && !is_feed() && !is_robots() && !is_trackback() ) {
				$text = get_option( self::INSERT_AFTERBODY, '' );
				$text = convert_smilies( $text );
				$text = do_shortcode( $text );

				if ( $text != '' ) {
					echo $text, "\n";
				}
			}
		}

		function options_html() {
			// Load options page
			require_once(BRRL_PLUGIN_DIR . '/inc/options.php');
		}

		function meta_setup() {
			global $post;
	
			// using an underscore, prevents the meta variable
			// from showing up in the custom fields section
			$meta = get_post_meta($post->ID,'_post_head_script',TRUE);
	
			// instead of writing HTML here, lets do an include
			include_once(BRRL_PLUGIN_DIR . '/inc/meta.php');
	
			// create a custom nonce for submit verification later
			echo '<input type="hidden" name="brrl_post_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
		}
	
		function post_meta_save($post_id) {
			// authentication checks
	
			// make sure data came from our meta box
			if ( ! isset( $_POST['brrl_post_meta_noncename'] )
				|| !wp_verify_nonce($_POST['brrl_post_meta_noncename'],__FILE__)) return $post_id;
	
			// check user permissions
			if ( $_POST['post_type'] == 'page' ) {
	
				if (!current_user_can('edit_page', $post_id)) 
					return $post_id;
	
			} else {
	
				if (!current_user_can('edit_post', $post_id)) 
					return $post_id;
	
			}
	
			$current_data = get_post_meta($post_id, '_post_head_script', TRUE);
	
			$new_data = $_POST['_post_head_script'];
	
			self::post_meta_clean($new_data);
	
			if ($current_data) {
	
				if (is_null($new_data)) delete_post_meta($post_id,'_post_head_script');
	
				else update_post_meta($post_id,'_post_head_script',$new_data);
	
			} elseif (!is_null($new_data)) {
	
				add_post_meta($post_id,'_post_head_script',$new_data,TRUE);
	
			}
	
			return $post_id;
		}
	
		public static function post_meta_clean(&$arr) {
	
			if (is_array($arr)) {
	
				foreach ($arr as $i => $v) {
	
					if (is_array($arr[$i])) {
						self::post_meta_clean($arr[$i]);
	
						if (!count($arr[$i])) {
							unset($arr[$i]);
						}
	
					} else {
	
						if (trim($arr[$i]) == '') {
							unset($arr[$i]);
						}
					}
				}
	
				if (!count($arr)) {
					$arr = NULL;
				}
			}
		}
	
		
	}

	$brrl_script_tag_manager = new B_Tag_Manager();
}
