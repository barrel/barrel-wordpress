<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_Conflicts
 */
class SearchWP_Conflicts {

	public $search_template;

	public $search_template_conflicts = array();

	public $filter_conflicts = array();

	// the filters we want to check for conflicts and their associated Knowledge Base resources
	public $filter_checklist = array(
		'pre_get_posts'     => 'https://searchwp.com/?p=10370',
		'the_posts'         => 'https://searchwp.com/?p=10370',
	);

	/**
	 * SearchWP_Conflicts constructor.
	 */
	function __construct() {
		$this->search_template = locate_template( 'search.php' ) ? locate_template( 'search.php' ) : locate_template( 'index.php' );
		$this->check_search_template();
		$this->check_filters();
	}

	/**
	 * Check active theme search template for potential conflicts
	 */
	function check_search_template() {
		global $wp_filesystem;
		/** @noinspection PhpIncludeInspection */
		include_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		$potential_conflicts = array( 'new WP_Query', 'query_posts' );
		if ( method_exists( $wp_filesystem, 'get_contents_array' ) ) {
			$search_template_content = ! empty( $this->search_template ) ? $wp_filesystem->get_contents_array( $this->search_template ) : '';

			if ( ! empty( $search_template_content ) ) {

				foreach ( $search_template_content as $key => $line ) {
					$line = trim( $line );
					foreach ( $potential_conflicts as $potential_conflict ) {
						if ( false !== strpos( $line, $potential_conflict ) ) {
							// make sure the line isn't commented out
							if ( '//' !== substr( $line, 0, 2 ) ) {
								$this->search_template_conflicts[ $key + 1 ][] = $potential_conflict;
							}
						}
					}
				}

			}

		}
	}

	/**
	 * Check for potential filter conflicts
	 */
	function check_filters() {
		if ( is_array( $GLOBALS ) ) {
			if ( isset( $GLOBALS['wp_filter'] ) ) {

				// whitelist which functions are acceptable
				$function_whitelist = array(
					'_close_comments_for_old_posts',    // WordPress core
					'SearchWP::wp_search',              // SearchWP search hijack
					'SearchWP::check_for_main_query',   // SearchWP main query check
					'SearchWP::impose_engine_config',   // SearchWP engine config
				);

				foreach ( $this->filter_checklist as $filter_name => $filter_resolution_url ) {
					if ( isset( $GLOBALS['wp_filter'][ $filter_name ] ) ) {
						foreach ( $GLOBALS['wp_filter'][ $filter_name ] as $filter_priority ) {
							foreach ( $filter_priority as $filter_hook ) {
								if ( isset( $filter_hook['function'] ) ) {

									// the function 'name' is either going to be just that (the function name) or
									// it's also going to include the class name for easier debugging
									// if it's a Closure we'll call that out too
									$function = $filter_hook['function'];
									if ( is_object( $function ) && ( $function instanceof Closure ) ) {
										$function_name = 'Anonymous Function (Closure)';
									} elseif ( is_array( $function ) ) {
										if ( is_object( $filter_hook['function'][0] ) ) {
											$function_name = get_class( $filter_hook['function'][0] ) . '::' . $filter_hook['function'][1];
										} else {
											$function_name = (string) $filter_hook['function'][0] . '::' . $filter_hook['function'][1];
										}
									} else {
										$function_name = $filter_hook['function'];
									}

									if ( ! in_array( $function_name, $function_whitelist, true ) ) {
										// we're going to store all potential conflicts for the warning message
										if ( ! isset( $this->filter_conflicts[ $filter_name ] ) || ! is_array( $this->filter_conflicts[ $filter_name ] ) ) {
											$this->filter_conflicts[ $filter_name ] = array();
										}
										$this->filter_conflicts[ $filter_name ][] = $function_name;
									}
								}
							}
						}
					}
				}
			}
		}
	}

}
