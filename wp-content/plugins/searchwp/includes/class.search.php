<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Singleton reference
 */
global $searchwp;


/**
 * Class SearchWPSearch performs search queries on the index
 */
class SearchWPSearch {

	/**
	 * @var string Search engine name
	 * @since 1.0
	 */
	private $engine;

	/**
	 * @var array Terms to search for
	 * @since 1.0
	 */
	private $terms;

	/**
	 * @var mixed|void Stored SearchWP settings
	 * @since 1.0
	 */
	private $settings;

	/**
	 * @var int The page of results to work with
	 * @since 1.0
	 */
	private $page;

	/**
	 * @var int The number of posts per page
	 * @since 1.0
	 */
	private $postsPer;

	/**
	 * @var string The order in which results should be returned
	 * @since 1.0
	 */
	private $order = 'DESC';

	/**
	 * @var int Total number of posts found after performing search
	 */
	public $foundPosts  = 0;

	/**
	 * @var int Total number of pages of results
	 */
	public $maxNumPages = 0;

	/**
	 * @var array Post ID storage
	 */
	public $postIDs = array();

	/**
	 * @var array Post object storage
	 */
	public $posts;

	/**
	 * @var string|array post status(es) to include when indexing
	 *
	 * @since 1.6.10
	 */
	private $post_statuses = 'publish';

	/**
	 * @var SearchWP parent
	 * @since 1.8
	 */
	private $searchwp;

	/**
	 * @var array engine settings
	 * @since 1.8
	 */
	private $engineSettings;

	/**
	 * @var SearchWPStemmer Core keyword stemmer
	 * @since 1.8
	 */
	private $stemmer;

	/**
	 * @var array Excluded post IDs
	 * @since 1.8
	 */
	private $excluded = array();

	/**
	 * @var array Included post IDs
	 * @since 1.8
	 */
	private $included = array();

	/**
	 * @var array Persistent relevant post IDs after various filtration
	 * @since 1.8
	 */
	private $relevant_post_ids = array();

	/**
	 * @var string Core database prefix
	 * @since 1.8
	 */
	private $db_prefix;

	/**
	 * @var string The main search query
	 * @since 1.8
	 */
	private $sql;

	/**
	 * @var string Arbitrary status SQL for the main query
	 * @since 1.8
	 */
	private $sql_status;

	/**
	 * @var string JOIN SQL used throughout the query
	 * @since 1.8
	 */
	private $sql_join;

	/**
	 * @var string Arbitrary SQL conditions used throughout the query
	 * @since 1.8
	 */
	private $sql_conditions;

	/**
	 * @var string Arbitrary WHERE clause used throughout the query
	 * @since 1.8
	 */
	private $sql_term_where;

	/**
	 * @var string Generated SQL based on post IDs to include
	 * @since 1.8
	 */
	private $sql_include;

	/**
	 * @var string Generated SQL based on post IDs to exclude
	 * @since 1.8
	 */
	private $sql_exclude;

	/**
	 * @var array Store the (potentially) filtered terms to save on redundant queries
	 * @since 2.3
	 */
	private $terms_final = array();

	/**
	 * @var array Exact weights of returned results
	 * @since 2.3
	 */
	public $results_weights = array();

	private $tax_count = 0;
	private $meta_count = 0;



	/**
	 * Constructor
	 *
	 * @param array $args
	 * @since 1.0
	 */
	function __construct( $args = array() ) {

		global $wpdb, $searchwp;

		do_action( 'searchwp_log', 'SearchWPSearch __construct()' );

		$defaults = array(
			'terms'             => '',
			'engine'            => 'default',
			'page'              => 1,
			'posts_per_page'    => intval( get_option( 'posts_per_page' ) ),
			'order'             => $this->order,
			'load_posts'        => true,
		);

		$this->db_prefix = $wpdb->prefix . SEARCHWP_DBPREFIX;

		// process our arguments
		$args = apply_filters( 'searchwp_search_args', wp_parse_args( $args, $defaults ) );
		$this->searchwp = SearchWP::instance();

		// instantiate our stemmer for later
		$this->stemmer = new SearchWPStemmer();

		do_action( 'searchwp_log', '$args = ' . var_export( $args, true ) );

		// if we have a valid engine, perform the query
		if ( $this->searchwp->is_valid_engine( $args['engine'] ) ) {
			// this filter is also applied in the SearchWP class search methods
			// TODO: should this be applied in both places? which?
			$sanitizeTerms = apply_filters( 'searchwp_sanitize_terms', true, $args['engine'] );
			if ( ! is_bool( $sanitizeTerms ) ) {
				$sanitizeTerms = true;
			}

			// whitelist search terms
			$pre_whitelist_terms = is_array( $args['terms'] ) ? implode( ' ', $args['terms'] ) : $args['terms'];
			$whitelisted_terms = $this->searchwp->extract_terms_using_pattern_whitelist( $pre_whitelist_terms, false );

			// TODO: if $whitelisted_terms has matches with spaces, there will be dupes: do we need to loop through and remove?

			// store the original search query (e.g. logging)
			$pre_search_original_terms = '';
			if ( ! empty( $searchwp->original_query ) ) {
				$pre_search_original_terms = trim( $searchwp->original_query );
			} elseif ( ! empty( $args['terms'] ) ) {
				// might have been instantiated directly, use the terms from the args
				$pre_search_original_terms = is_array( $args['terms'] ) ? implode( ' ', $args['terms'] ) : $args['terms'];
				$pre_search_original_terms = trim( $pre_search_original_terms );
			}

			if ( $sanitizeTerms ) {
				$terms = $this->searchwp->sanitize_terms( $args['terms'] );
			} else {
				$terms = $args['terms'];
				do_action( 'searchwp_log', 'Opted out of internal sanitization' );
			}

			if ( is_array( $whitelisted_terms ) ) {
				$whitelisted_terms = array_filter( array_map( 'trim', $whitelisted_terms ), 'strlen' );
			}

			if ( is_array( $terms ) ) {
				$terms = array_filter( array_map( 'trim', $terms ), 'strlen' );
				$terms = array_merge( $terms, $whitelisted_terms );
			} else {
				$terms .= ' ' . implode( ' ', $whitelisted_terms );
			}

			// make sure the terms are unique, especially after whitelist matching
			if ( is_array( $terms ) ) {
				$terms = array_unique( $terms );
				$terms = array_filter( $terms, 'strlen' );
			}

			$engine = $args['engine'];

			// allow dev to customize post statuses are included
			$this->post_statuses = (array) apply_filters( 'searchwp_post_statuses', $this->post_statuses, $engine );
			foreach ( $this->post_statuses as $post_status_key => $post_status_value ) {
				$this->post_statuses[ $post_status_key ] = sanitize_key( $post_status_value );
			}

			do_action( 'searchwp_log', '$terms = ' . var_export( $terms, true ) );

			if ( 'DESC' != strtoupper( apply_filters( 'searchwp_search_query_order', $args['order'] ) ) && 'ASC' != strtoupper( $args['order'] ) ) {
				$args['order'] = 'DESC';
			}

			if ( apply_filters( 'searchwp_query_allow_query_string_override_order', true ) ) {
				if ( ! empty( $_GET['order'] ) ) {
					$args['order'] = 'ASC' == strtoupper( $_GET['order'] ) ? 'ASC' : 'DESC';
				}
			}

			// filter the terms just before querying
			$terms = apply_filters( 'searchwp_pre_search_terms', $terms, $engine );

			do_action( 'searchwp_log', 'searchwp_pre_search_terms $terms = ' . var_export( $terms, true ) );

			$this->terms        = $terms;
			$this->engine       = $engine;
			$this->settings     = empty( $searchwp ) ? get_option( SEARCHWP_PREFIX . 'settings' ) : $searchwp->settings;
			$this->page         = absint( $args['page'] );
			$this->postsPer     = intval( $args['posts_per_page'] );
			$this->order        = $args['order'];
			$this->load_posts   = is_bool( $args['load_posts'] ) ? $args['load_posts'] : true;
			$this->offset       = ( isset( $args['offset'] ) && ! empty( $args['offset'] ) ) ? absint( $args['offset'] ) : 0;

			// pull out our engine-specific settings
			$all_settings = SWP()->validate_settings( $this->settings );
			if ( ! isset( $all_settings['engines'] ) || ! isset( $all_settings['engines'][ $this->engine ] ) ) {
				wp_die( esc_html__( 'Engine settings not found', 'searchwp' ) );
			}
			$this->engineSettings = $all_settings['engines'][ $this->engine ];

			// allow filtration of settings at runtime
			$this->engineSettings = apply_filters( "searchwp_engine_settings_{$this->engine}", $this->engineSettings, $this->terms );

			// strip out zero weight taxonomies (to reduce as much of the algorithm as possible)
			if ( is_array( $this->engineSettings ) && ! empty( $this->engineSettings ) ) {
				foreach ( $this->engineSettings as $post_type => $engine_post_type_settings ) {
					if ( isset( $engine_post_type_settings['weights']['tax'] ) && is_array( $engine_post_type_settings['weights']['tax'] ) && ! empty( $engine_post_type_settings['weights']['tax'] ) ) {
						foreach ( $engine_post_type_settings['weights']['tax'] as $taxonomy_name => $weight ) {
							if ( 0 === (int) $weight ) {
								unset( $this->engineSettings[ $post_type ]['weights']['tax'][ $taxonomy_name ] );
							}
						}
					}
				}
			}

			// if it's a native search we can piggyback default includes and excludes
			if ( is_search() ) {
				$this->set_default_include_and_exclude();
			}

			// we're going to exclude entered IDs for the query as a whole
			// need to get these IDs early because if an attributed post ID is excluded we need to omit it from
			// the query entirely
			$this->set_excluded_ids_from_settings();

			// pull any excluded IDs based on taxonomy term
			$this->set_excluded_ids_from_taxonomies();

			if ( ! empty( $this->terms ) ) {

				// perform our query
				$this->posts = $this->query();

				// log this
				$paged = get_query_var( 'paged' );
				$paged = absint( $paged ) > 1;

				// Default is to log if we're not doing an admin column nor are we paging
				$log_default = ! $this->doing_admin_column() && ! $paged;

				if ( ! empty( $pre_search_original_terms ) && apply_filters( 'searchwp_log_search', $log_default, $engine, $pre_search_original_terms, absint( $this->foundPosts ) ) ) {

					$pre_search_original_terms = sanitize_text_field( $pre_search_original_terms );
					$pre_search_original_terms = trim( $pre_search_original_terms );

					// respect database schema
					if ( 200 < strlen( $pre_search_original_terms ) ) {
						$pre_search_original_terms = substr( $pre_search_original_terms, 0, 199 );
					}

					if ( ! empty( $pre_search_original_terms ) ) {
						/** @noinspection PhpUnusedLocalVariableInspection */
						$log_result = $wpdb->insert(
							$this->db_prefix . 'log',
							array(
								'event'    => 'search',
								'query'    => $pre_search_original_terms,
								'hits'     => absint( $this->foundPosts ),
								'engine'   => $engine,
								'wpsearch' => 0,
							),
							array(
								'%s',
								'%s',
								'%d',
								'%s',
								'%d',
							)
						);
					}
				}
			}
		}

	}

	/**
	 * Determine whether we're outputting an admin column
	 *
	 * @return bool
	 */
	function doing_admin_column() {
		$doing_admin_column = false;

		if ( ! is_admin() ) {
			return $doing_admin_column;
		}

		$post_types = get_post_types();

		foreach ( $post_types as $post_type ) {
			if ( did_action( 'manage_' . $post_type . '_posts_custom_column' )
				|| doing_action( 'manage_' . $post_type . '_posts_custom_column' ) ) {
				$doing_admin_column = true;
				break;
			}
		}

		return $doing_admin_column;
	}

	/**
	 * Getter for currently excluded IDs
	 *
	 * @since 2.8.5
	 *
	 * @return array
	 */
	function get_excluded() {
		return $this->excluded;
	}

	/**
	 * Getter for currently included IDs
	 *
	 * @since 2.8.5
	 *
	 * @return array
	 */
	function get_included() {
		return $this->included;
	}


	/**
	 * Piggyback $wp_query arguments to set better include/exclude defaults
	 *
	 * @since 2.5
	 */
	function set_default_include_and_exclude() {

		// We need to ensure that this is in fact the main query, else we can get some
		// wacky behavior from other functionality e.g. pre_get_posts hook usage that
		// in turn uses WP_Query can get disastrous results
		if ( empty( SWP()->isMainQuery ) ) {
			return;
		}

		// set default inclusions (based on $wp_query (other plugins likely do their magic by setting this))
		$wp_query_post__in = get_query_var( 'post__in' );
		if ( ! empty( $wp_query_post__in ) ) {

			if ( ! is_array( $wp_query_post__in ) ) {
				$wp_query_post__in = explode( ',', $wp_query_post__in );
			}

			$this->included = array_map( 'absint', (array) $wp_query_post__in );

			do_action( 'searchwp_log', 'Setting default post__in: ' . implode( ', ', $this->included ) );
		}

		// set default exclusions in the same fashion
		$wp_query_post__not_in = get_query_var( 'post__not_in' );
		if ( ! empty( $wp_query_post__not_in ) ) {

			if ( ! is_array( $wp_query_post__not_in ) ) {
				$wp_query_post__not_in = explode( ',', $wp_query_post__not_in );
			}

			$this->excluded = array_map( 'absint', (array) $wp_query_post__not_in );

			do_action( 'searchwp_log', 'Setting default post__not_in: ' . implode( ', ', $this->excluded ) );
		}
	}


	/**
	 * Perform a query on the index
	 *
	 * @return array Posts returned by the query
	 * @since 1.0
	 */
	function query() {
		do_action( 'searchwp_log', 'query()' );

		do_action( 'searchwp_before_query_index', array(
				'terms'     => $this->terms,
				'engine'    => $this->engine,
				'settings'  => $this->settings,
				'page'      => $this->page,
				'postsPer'  => $this->postsPer,
			) );

		$this->query_for_post_ids();

		$swpargs = array(
			'terms'     => $this->terms,
			'engine'    => $this->engine,
			'settings'  => $this->settings,
			'page'      => $this->page,
			'postsPer'  => $this->postsPer,
		);

		do_action( 'searchwp_after_query_index', $swpargs );

		// facilitate filtration of returned results
		$this->postIDs = apply_filters( 'searchwp_query_results', $this->postIDs, $swpargs );

		if ( empty( $this->postIDs ) ) {
			return array();
		}

		// our post IDs will have already been filtered based on the engine settings, so we want to query for
		// anything that matches our post IDs
		$args = array(
			'posts_per_page'    => count( $this->postIDs ),
			'post_type'         => SWP()->get_indexed_post_types(),
			'post_status'       => 'any',   // we've already filtered our post statuses in the original query
			'post__in'          => $this->postIDs,
			'orderby'           => 'post__in',
		);

		// we want ints all the time
		$this->postIDs = array_map( 'absint', $this->postIDs );

		if ( $this->load_posts && true === apply_filters( 'searchwp_load_posts', true, $swpargs ) ) {

			// we don't want anything interfering with us getting our posts
			if ( apply_filters( 'searchwp_remove_pre_get_posts_during_search', false ) ) {
				remove_all_actions( 'pre_get_posts' );
				remove_all_filters( 'pre_get_posts' );
			}

			$posts = apply_filters( 'searchwp_found_post_objects', get_posts( $args ), $swpargs );
		} else {
			$posts = $this->postIDs;
		}

		return $posts;
	}


	/**
	 * Ensures that all post types in settings still exist
	 *
	 * @since 1.8
	 */
	private function validate_post_types() {

		// devs can customize which post types are indexed, it doesn't make
		// sense to list post types that were excluded
		$indexed_post_types = apply_filters( 'searchwp_indexed_post_types', $this->searchwp->postTypes );

		if ( is_array( $indexed_post_types ) ) {
			foreach ( $this->engineSettings as $postType => $postTypeSettings ) {
				if ( ! in_array( $postType, $indexed_post_types ) || ! post_type_exists( $postType ) ) {
					unset( $this->engineSettings[ $postType ] );
				}
			}
		}
	}


	/**
	 * Determine whether any post types are enabled
	 *
	 * @return bool Whether there are any enabled post types
	 * @since 1.8
	 */
	private function any_enabled_post_types() {
		$enabled_post_type = false;

		// check to make sure that at least one post type is enabled for this engine
		if ( is_array( $this->engineSettings ) ) {
			foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
				if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] ) {
					$enabled_post_type = true;
					break;
				}
			}
		}

		return $enabled_post_type;
	}


	/**
	 * Set excluded IDs as per the engine settings
	 *
	 * @since 1.8
	 */
	function set_excluded_ids_from_settings() {
		$excludeIDs = apply_filters( 'searchwp_prevent_indexing', $this->excluded ); // catch anything that shouldn't have been indexed anyway
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {

			if ( empty( $postTypeWeights['enabled'] ) ) {
				continue;
			}

			// store our exclude clause
			if ( isset( $postTypeWeights['options']['exclude'] ) && ! empty( $postTypeWeights['options']['exclude'] ) ) {

				$postTypeExcludeIDs = $postTypeWeights['options']['exclude'];

				// stored as a comma separated string of integers
				if ( is_string( $postTypeExcludeIDs ) && false !== strpos( $postTypeExcludeIDs, ',' ) ) {
					$postTypeExcludeIDs = explode( ',', $postTypeExcludeIDs );
				} else {
					if ( is_string( $postTypeExcludeIDs ) ) {
						$postTypeExcludeIDs = array( absint( $postTypeExcludeIDs ) );
					} else {
						$postTypeExcludeIDs = array();
					}
				}
			} else {
				$postTypeExcludeIDs = array();
			}

			if ( ! empty( $postTypeExcludeIDs ) && is_array( $postTypeExcludeIDs ) ) {
				foreach ( $postTypeExcludeIDs as $postTypeExcludeID ) {
					$excludeIDs[] = absint( $postTypeExcludeID );
				}
			}
		}

		if ( ! is_array( $excludeIDs ) ) {
			$excludeIDs = array();
		} else {
			$excludeIDs = array_map( 'absint', $excludeIDs );
		}

		$excludeIDs = array_unique( $excludeIDs );

		do_action( 'searchwp_log', '$excludeIDs = ' . var_export( $excludeIDs, true ) );

		$this->excluded = $excludeIDs;
	}


	/**
	 * Set excluded IDs based on taxonomy terms in the settings
	 *
	 * @since 1.8
	 */
	function set_excluded_ids_from_taxonomies() {
		add_filter( 'searchwp_force_wp_query', '__return_true' ); // we're going to be firing a WP_Query and want it to finish
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {

			if ( empty( $postTypeWeights['enabled'] ) ) {
				continue;
			}

			$taxonomies = get_object_taxonomies( $postType );
			if ( is_array( $taxonomies ) && count( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {

					$taxonomy = get_taxonomy( $taxonomy );

					if ( isset( $postTypeWeights['options'][ 'exclude_' . $taxonomy->name ] ) ) {

						$excludedTerms = explode( ',', $postTypeWeights['options'][ 'exclude_' . $taxonomy->name ] );

						if ( ! is_array( $excludedTerms ) ) {
							$excludedTerms = array( intval( $excludedTerms ) );
						}

						if ( ! empty( $excludedTerms ) ) {
							foreach ( $excludedTerms as $excludedKey => $excludedValue ) {
								$excludedTerms[ $excludedKey ] = intval( $excludedValue );
							}
						}

						// determine which post(s) have this term
						$args = array(
							'posts_per_page'    => -1,
							'fields'            => 'ids',
							'post_type'         => $postType,
							'suppress_filters'  => true,
							'tax_query'         => array(
								array(
									'taxonomy'  => $taxonomy->name,
									'field'     => 'id',
									'terms'     => $excludedTerms,
								),
							)
						);


						// Media won't be published
						if ( 'attachment' == $postType ) {
							$args['post_status'] = 'inherit';
						}

						$excludedByTerm = new WP_Query( $args );

						if ( ! empty( $excludedByTerm ) ) {
							$this->excluded = array_merge( $this->excluded, $excludedByTerm->posts );
						}
					}
				}
			}
		}
		remove_filter( 'searchwp_force_wp_query', '__return_true' );

		do_action( 'searchwp_log', 'After taxonomy exclusion $excludeIDs = ' . var_export( $this->excluded, true ) );
	}

	/**
	 * Get an array of IDs for posts that have been limited using engine rules
	 *
	 * @since 2.9.8
	 */
	function get_included_ids_from_taxonomies_for_post_type( $post_type = 'post' ) {
		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}

		add_filter( 'searchwp_force_wp_query', '__return_true' ); // we're going to be firing a WP_Query and want it to finish

		$limited_ids = false;
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {

			if ( $postType !== $post_type || empty( $postTypeWeights['enabled'] ) ) {
				continue;
			}

			$taxonomies = get_object_taxonomies( $postType );
			if ( is_array( $taxonomies ) && count( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {

					$taxonomy = get_taxonomy( $taxonomy );

					if ( isset( $postTypeWeights['options'][ 'limit_to_' . $taxonomy->name ] ) ) {

						$includedTerms = explode( ',', $postTypeWeights['options'][ 'limit_to_' . $taxonomy->name ] );

						if ( ! is_array( $includedTerms ) ) {
							$includedTerms = array( intval( $includedTerms ) );
						}

						if ( ! empty( $includedTerms ) ) {
							foreach ( $includedTerms as $includedKey => $includedValue ) {
								$includedTerms[ $includedKey ] = intval( $includedValue );
							}
						}

						// determine which post(s) have this term
						$args = array(
							'posts_per_page'    => -1,
							'fields'            => 'ids',
							'post_type'         => $postType,
							'suppress_filters'  => true,
							'tax_query'         => array(
								array(
									'taxonomy'  => $taxonomy->name,
									'field'     => 'id',
									'terms'     => $includedTerms,
								),
							)
						);

						// Media won't be published
						if ( 'attachment' == $postType ) {
							$args['post_status'] = 'inherit';
						}

						$includedByTerm = new WP_Query( $args );

						$limited_ids = $includedByTerm->posts;
						break;
					}
				}
			}

			break;
		}
		remove_filter( 'searchwp_force_wp_query', '__return_true' );

		do_action( 'searchwp_log', 'After taxonomy limiter $includeIDS = ' . var_export( $limited_ids, true ) );

		return $limited_ids;
	}

	/**
	 * Set included IDs based on taxonomy terms in the settings
	 * NOTE: This will return IDs regardless of post type, use with caution
	 *
	 * @since 2.9
	 */
	function set_included_ids_from_taxonomies() {
		add_filter( 'searchwp_force_wp_query', '__return_true' ); // we're going to be firing a WP_Query and want it to finish

		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {

			if ( empty( $postTypeWeights['enabled'] ) ) {
				continue;
			}

			$taxonomies = get_object_taxonomies( $postType );
			if ( is_array( $taxonomies ) && count( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {

					$taxonomy = get_taxonomy( $taxonomy );

					if ( isset( $postTypeWeights['options'][ 'limit_to_' . $taxonomy->name ] ) ) {

						$includedTerms = explode( ',', $postTypeWeights['options'][ 'limit_to_' . $taxonomy->name ] );

						if ( ! is_array( $includedTerms ) ) {
							$includedTerms = array( intval( $includedTerms ) );
						}

						if ( ! empty( $includedTerms ) ) {
							foreach ( $includedTerms as $includedKey => $includedValue ) {
								$includedTerms[ $includedKey ] = intval( $includedValue );
							}
						}

						// determine which post(s) have this term
						$args = array(
							'posts_per_page'    => -1,
							'fields'            => 'ids',
							'post_type'         => $postType,
							'suppress_filters'  => true,
							'tax_query'         => array(
								array(
									'taxonomy'  => $taxonomy->name,
									'field'     => 'id',
									'terms'     => $includedTerms,
								),
							)
						);

						// Media won't be published
						if ( 'attachment' == $postType ) {
							$args['post_status'] = 'inherit';
						}

						$includedByTerm = new WP_Query( $args );

						if ( ! empty( $includedByTerm->posts ) ) {
							$this->included = array_merge( $this->included, $includedByTerm->posts );
						} else {
							$this->included = array_merge( $this->included, array( 0 ) );
						}
					}
				}
			}
		}
		remove_filter( 'searchwp_force_wp_query', '__return_true' );

		do_action( 'searchwp_log', 'After taxonomy limiter $includeIDS = ' . var_export( $this->included, true ) );
	}


	/**
	 * Determine which field types should be considered for AND logic
	 *
	 * @since 1.8
	 */
	private function get_and_fields( $post_type = '' ) {

		// If an invalid post type is submitted, revert to global AND fields across all engine post types
		if ( ! empty( $post_type ) && ! post_type_exists( $post_type ) ) {
			$post_type = '';
		}

		// allow devs to filter which fields should be included for AND checks
		$andFieldsDefaults = array( 'title', 'content', 'slug', 'excerpt', 'comment', 'tax', 'meta' );

		// Store which AND fields the engine actually uses
		$theseAndFields = array();

		// If we're doing a search any default AND field has a weight of zero, it doesn't apply
		if ( did_action( 'searchwp_before_query_index' ) ) {
			foreach ( $this->settings['engines'][ $this->engine ] as $engine_post_type => $post_type_settings ) {
				// If the post type is enabled, it doesn't matter
				if ( empty( $post_type_settings['enabled'] ) ) {
					continue;
				}

				// Allow restriction to single post type
				if ( ! empty( $post_type ) && $post_type !== $engine_post_type ) {
					continue;
				}

				if ( isset( $post_type_settings['weights'] ) && is_array( $post_type_settings['weights'] ) ) {
					foreach ( $post_type_settings['weights'] as $field_type => $weight ) {

						// 'cf' is used for Custom Fields in the Settings but it's confusing; it's meta here
						if ( 'cf' === $field_type ) {
							$field_type = 'meta';
						}

						if ( in_array( $field_type, $andFieldsDefaults, true ) ) {

							if ( is_numeric( $weight ) && ! empty( $weight ) ) {
								$theseAndFields[] = $field_type;
							} elseif ( is_array( $weight) && ! empty( $weight ) ) {
								foreach ( $weight as $kweight ) {
									if ( is_numeric( $kweight ) && ! empty( $kweight ) ) {
										$theseAndFields[] = $field_type;
										break;
									} elseif ( is_array( $kweight) && isset( $kweight['weight'] ) ) { // overly complex data model
										if ( is_numeric( $kweight['weight'] ) && ! empty( $kweight['weight'] ) ) {
											$theseAndFields[] = $field_type;
											break;
										}
									}
								}
							}
						}
					}
				}
			}

			$theseAndFields = array_unique( $theseAndFields );
		}

		$andFields = $theseAndFields;
		if ( ! empty( $post_type ) ) {
			$andFields = apply_filters( "searchwp_and_fields_{$post_type}", $andFields, $this->engine );
		}
		$andFields = apply_filters( 'searchwp_and_fields', $andFields );

		// validate AND fields
		if ( is_array( $andFields ) && ! empty( $andFields ) ) {
			$strtolower_function = function_exists( 'mb_strtolower' ) ? 'mb_strtolower' : 'strtolower';
			$andFields = array_map( $strtolower_function, $andFields );
			foreach ( $andFields as $andFieldKey => $andField ) {
				if ( ! in_array( $andField, $andFieldsDefaults, true ) ) {
					// invalid field, kill it
					unset( $andFields[ $andFieldKey ] );
				}
			}
		} else {
			// returned not an array, so reset it (which will basically invalidate AND searching)
			$andFields = array();
		}

		do_action( 'searchwp_log', '$andFields = ' . implode( ', ', $andFields ) );

		return $andFields;
	}


	/**
	 * Use AND logic to find post IDs that have all search terms
	 *
	 * @param $andFields array The AND fields to consider when applying logic
	 * @param $andTerm string The keyword
	 *
	 * @return array The applicable Post IDs
	 * @since 1.8
	 */
	private function get_post_ids_via_and( $andFields, $andTerm, $postType = '' ) {

		global $wpdb;

		// If an invalid post type is submitted, revert to global AND for all post types (not ideal)
		if ( ! empty( $postType ) && ! post_type_exists( $postType ) ) {
			$postType = '';
		}

		// we're going to utilize $andFields to build our query based on what the dev wants to count for AND queries
		$andFieldsCoalesce = $this->get_and_field_coalesce( $andFields );

		// in order to save having to scrub through every enabled post type
		// we're just going to assume a stem here and limit the result pool as quickly as possible
		// since the main query will take into consideration the additional limitation of the stem

		$unstemmed = $andTerm;
		$maybeStemmed = apply_filters( 'searchwp_custom_stemmer', $unstemmed );

		// if the term was stemmed via the filter use it, else generate our own
		$originalAndTerm = ( $unstemmed == $maybeStemmed ) ? $this->stemmer->stem( $andTerm ) : $maybeStemmed;

		$andTerm = $wpdb->prepare( '%s', $originalAndTerm );
		$andTermLower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $andTerm, 'UTF-8' ) : strtolower( $andTerm );
		$relevantTermWhere = " {$this->db_prefix}terms.stem = " . $andTermLower;

		// as an optimization we're going to break up this query into three 'parts'
		//  1) SELECT against the index table to find out where this term appears at least once
		//  2) SELECT against the cf table
		//  3) SELECT against the tax table
		//
		// all three will be UNIONed but all three are also filterable so we need to build this query carefully
		// and completely based on $andFields (which is an array of fields to consider)

		$andTermSQL = '';

		$active_post_types_for_this_engine = array();

		if ( empty( $postType ) ) {
			foreach ( $this->settings['engines'][ $this->engine ] as $post_type => $settings ) {
				if ( ! empty( $settings['enabled'] ) ) {
					$active_post_types_for_this_engine[] = $post_type;
				}
			}
		} else {
			// Limiting to a single post type was added as a bugfix in 2.9.9 so we're essentially hacking
			// the way this function works by limiting to a single post type as though it was the only
			// enabled post type for the engine
			$active_post_types_for_this_engine = array( $postType );
		}

		if ( empty( $active_post_types_for_this_engine ) ) {
			return array();
		}

		$clause_count = 0;

		$post_parents = array();

		// first SQL segment is against the index table
		if ( ! empty( $andFieldsCoalesce ) ) {
			$clause_count++;
			// we do in fact want to run query 1
			$andTermSQL .= "
                SELECT {$this->db_prefix}index.post_id, {$wpdb->prefix}posts.post_parent,
                       SUM({$andFieldsCoalesce}) as termcount
                FROM {$this->db_prefix}index FORCE INDEX (termindex)
                LEFT JOIN {$this->db_prefix}terms
                    ON {$this->db_prefix}index.term = {$this->db_prefix}terms.id
                LEFT JOIN {$wpdb->prefix}posts
                	ON {$wpdb->prefix}posts.ID = {$this->db_prefix}index.post_id
                WHERE {$relevantTermWhere}
                	AND {$wpdb->prefix}posts.post_type IN ( " . implode( ', ', array_fill( 0, count( $active_post_types_for_this_engine ), '%s' ) ) . " ) GROUP BY post_id HAVING termcount > 0 ";
		}

		// next SQL segment is against the cf table
		if ( in_array( 'meta', $andFields ) ) {
			$clause_count++;

			if ( ! empty( $andTermSQL ) ) {
				$andTermSQL .= ' UNION ';
			}

			// we want to apply AND logic to the cf table
			$andTermSQL .= "
                SELECT {$this->db_prefix}cf.post_id, {$wpdb->prefix}posts.post_parent, SUM(`count`) as termcount
                FROM {$this->db_prefix}cf FORCE INDEX (term)
                LEFT JOIN {$this->db_prefix}terms
                    ON {$this->db_prefix}cf.term = {$this->db_prefix}terms.id
                LEFT JOIN {$wpdb->prefix}posts
                	ON {$wpdb->prefix}posts.ID = {$this->db_prefix}cf.post_id
                WHERE {$relevantTermWhere}
                	AND {$wpdb->prefix}posts.post_type IN ( " . implode( ', ', array_fill( 0, count( $active_post_types_for_this_engine ), '%s' ) ) . " ) GROUP BY post_id HAVING termcount > 0 ";
		}

		// last SQL segment is against the tax table
		if ( in_array( 'tax', $andFields ) ) {
			$clause_count++;

			if ( ! empty( $andTermSQL ) ) {
				$andTermSQL .= ' UNION ';
			}

			// we want to apply AND logic to the cf table
			$andTermSQL .= "
                SELECT {$this->db_prefix}tax.post_id, {$wpdb->prefix}posts.post_parent, SUM(`count`) as termcount
                FROM {$this->db_prefix}tax FORCE INDEX (term)
                LEFT JOIN {$this->db_prefix}terms
                    ON {$this->db_prefix}tax.term = {$this->db_prefix}terms.id
                LEFT JOIN {$wpdb->prefix}posts
                	ON {$wpdb->prefix}posts.ID = {$this->db_prefix}tax.post_id
                WHERE {$relevantTermWhere}
                	AND {$wpdb->prefix}posts.post_type IN ( " . implode( ', ', array_fill( 0, count( $active_post_types_for_this_engine ), '%s' ) ) . " ) GROUP BY post_id HAVING termcount > 0";
		}

		$postsWithTermPresent = array();

		$values_to_prepare = array();
		for ( $i = 0; $i < $clause_count; $i++ ) {
			$values_to_prepare = array_merge( $values_to_prepare, $active_post_types_for_this_engine );
		}

		$postsWithTermPresentRef = $wpdb->get_results(
			$wpdb->prepare(
				$andTermSQL,
				$values_to_prepare
			)
		);

		// we retrieved both the post ID and the post_parent (to account for attribution) so let's merge them
		if ( is_array( $postsWithTermPresentRef ) && ! empty( $postsWithTermPresentRef ) ) {
			foreach ( $postsWithTermPresentRef as $post_ref ) {
				if ( isset( $post_ref->post_id ) && ! empty( $post_ref->post_id ) ) {
					$postsWithTermPresent[] = absint( $post_ref->post_id );
				}
				if ( isset( $post_ref->post_parent ) && ! empty( $post_ref->post_parent ) ) {
					$post_parents[] = absint( $post_ref->post_parent );
				}
			}
		}

		if ( ! empty( $post_parents ) ) {

			$post_parents = array_map( 'absint', $post_parents );
			$post_parents = array_unique( $post_parents );

			// Check to make sure the parents in fact have all terms
			$searchwp_index_table = $wpdb->prefix . SEARCHWP_DBPREFIX . 'index';
			$searchwp_terms_table = $wpdb->prefix . SEARCHWP_DBPREFIX . 'terms';

			$sql = "
				SELECT      {$searchwp_index_table}.id
				FROM        {$searchwp_index_table}
				LEFT JOIN   {$searchwp_terms_table}
				            ON {$searchwp_terms_table}.id = {$searchwp_index_table}.term
				WHERE       {$searchwp_terms_table}.stem = %s
							AND {$searchwp_index_table}.id IN ( " . implode( ', ', array_fill( 0, count( $post_parents ), '%d' ) ) . " )
				";

			$originalAndTermLower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $originalAndTerm, 'UTF-8' ) : strtolower( $originalAndTerm );
			$parent_values_to_prep = array_merge( array( $originalAndTermLower ), $post_parents );
			$parent_sql = $wpdb->prepare( trim( $sql ), $parent_values_to_prep );
			$parents_with_term = $wpdb->get_col( $parent_sql );

			if ( ! empty( $parents_with_term ) ) {
				$postsWithTermPresent = array_merge( $postsWithTermPresent, $parents_with_term );
			}
		}

		// even though we're using UNION, we will likely have duplicate post_ids because each row will have different term counts
		if ( is_array( $postsWithTermPresent ) && ! empty( $postsWithTermPresent ) ) {
			$postsWithTermPresent = array_unique( $postsWithTermPresent );
		}

		// Make sure to take into account excluded IDs
		$postsWithTermPresent = array_diff( $postsWithTermPresent, $this->excluded );

		if ( ! empty( $postsWithTermPresent ) ) {
			do_action( 'searchwp_log', 'Algorithm AND logic pass: ' . implode( ', ', $postsWithTermPresent ) );
		}

		return $postsWithTermPresent;
	}


	/**
	 * Generate the SQL used in AND field logic
	 *
	 * @param $andFields array The AND fields to consider when applying logic
	 *
	 * @return string SQL to use in the main query
	 * @since 1.8
	 */
	private function get_and_field_coalesce( $andFields ) {
		$coalesceFields = array();

		// we're going to utilize $andFields to build our query based on what the dev wants to count for AND queries
		foreach ( $andFields as $andField ) {
			switch ( $andField ) {
				case 'tax':
					// taxonomy has been broken out into UNION as of version 2.0.5
					break;
				case 'meta':
					// cf has been broken out into UNION as of 2.0.5
					break;
				default:
					$andFieldTable = 'index';
					$andFieldColumn = sanitize_text_field( $andField );
					$coalesceFields[] = "COALESCE({$this->db_prefix}{$andFieldTable}.{$andFieldColumn},0)";
					break;
			}
		}

		if ( ! empty( $coalesceFields ) ) {
			$andFieldsCoalesce = implode( ' + ', $coalesceFields );
		} else {
			$andFieldsCoalesce = '';
		}

		return $andFieldsCoalesce;
	}

	/**
	 * If applicable, limit posts using AND logic
	 *
	 * @since 1.8
	 */
	private function maybe_do_and_logic() {
		// AND logic only applies if there's more than one term (and the dev doesn't disable it)
		$doAnd = ( count( $this->terms ) > 1 && apply_filters( 'searchwp_and_logic', true ) ) ? true : false;
		do_action( 'searchwp_log', '$doAnd = ' . var_export( $doAnd, true ) );

		$andTerms = array();

		// AND logic is going to be different per post type, so we need to determine relevant IDs across the entire engine
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] ) {
				// AND fields need to be defined per post type as well
				$and_fields_for_post_type = $this->get_and_fields( $postType );

				$andTerms[ $postType ] = array();

				if ( $doAnd && is_array( $and_fields_for_post_type ) && ! empty( $and_fields_for_post_type ) ) {
					// Assume AND logic is going to happen
					$applicableAndResults = true;

					// We need to find posts that have all search terms for this post type
					foreach ( $this->terms as $andTerm ) {

						$postsWithTermPresent = $this->get_post_ids_via_and( $and_fields_for_post_type, $andTerm, $postType );

						do_action( 'searchwp_log', '$postsWithTermPresent (' . $postType . ') = ' . implode( ', ', $postsWithTermPresent ) );

						$andTerms[ $postType ][] = $postsWithTermPresent;
					}

					// Current status: Each element of $andTerms[ $postType ] is an array of
					// post IDs that contains that term, we need to intersect all of them to ensure AND logic
					if ( isset( $andTerms[ $postType ] ) && is_array( $andTerms[ $postType ] ) && count( $andTerms[ $postType ] ) > 1 ) {
						$andTerms[ $postType ] = call_user_func_array( 'array_intersect', $andTerms[ $postType ] );
					} else {
						$andTerms[ $postType ] = array();
					}
				}
			}
		}

		// $andTerms contains a breakdown of AND results per post type
		// If there are are post types with no AND logic matches we can strip those out now
		foreach ( $andTerms as $post_type => $potential_and_logic_ids ) {
			if ( empty( $potential_and_logic_ids ) ) {
				unset( $andTerms[ $post_type ] );
			}
		}

		// We now have a reduced array of potential AND logic IDs per post type,
		// but in order to find the IDs that satisfy AND logic, we need to intersect
		$relevantPostIds = array();
		foreach ( $andTerms as $post_type => $potential_and_logic_ids ) {
			if ( ! empty( $potential_and_logic_ids ) ) {
				$relevantPostIds = array_merge( $relevantPostIds, $potential_and_logic_ids );
			}
		}
		$relevantPostIds = array_unique( $relevantPostIds );

		$this->relevant_post_ids = array_map( 'absint', $relevantPostIds );
	}


	/**
	 * If a weight is < 0 any results need to be forcefully excluded
	 *
	 * @since 1.8
	 */
	private function exclude_posts_by_weight() {
		global $wpdb;

		// we need to check for exclusions at this point (weights of < zero)
		$andTerms = array();
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] && count( $postTypeWeights['weights'] ) ) {
				foreach ( $postTypeWeights['weights'] as $type => $weight ) {
					foreach ( $this->terms as $andTerm ) {
						$applicableExclusion = false;

						// determine whether we want a term match or stem match
						$andTermPrepared = $wpdb->prepare( '%s', $andTerm );
						$andTermLower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $andTermPrepared, 'UTF-8' ) : strtolower( $andTermPrepared );
						if ( ! isset( $postTypeWeights['options']['stem'] ) || empty( $postTypeWeights['options']['stem'] ) ) {
							$relavantTermWhere = " {$this->db_prefix}terms.term = " . $andTermLower;
						} else {
							$unstemmed = $andTerm;
							$maybeStemmed = apply_filters( 'searchwp_custom_stemmer', $unstemmed );

							// if the term was stemmed via the filter use it, else generate our own
							$andTerm = ( $unstemmed == $maybeStemmed ) ? $this->stemmer->stem( $andTerm ) : $maybeStemmed;

							$relavantTermWhere = " {$this->db_prefix}terms.stem = " . $wpdb->prepare( '%s', $andTerm );
						}

						$andInternalSQL = "
                            SELECT {$this->db_prefix}index.post_id
                            	FROM {$this->db_prefix}index
                            LEFT JOIN {$wpdb->posts}
                            	ON {$this->db_prefix}index.post_id = {$wpdb->posts}.ID
                            LEFT JOIN {$this->db_prefix}terms
                            	ON {$this->db_prefix}index.term = {$this->db_prefix}terms.id
                            LEFT JOIN {$this->db_prefix}cf
                            	ON {$this->db_prefix}index.post_id = {$this->db_prefix}cf.post_id
                            LEFT JOIN {$this->db_prefix}tax
                            	ON {$this->db_prefix}index.post_id = {$this->db_prefix}tax.post_id
                            WHERE {$relavantTermWhere} ";

						if ( ! empty( $this->relevant_post_ids ) ) {
							$this->relevant_post_ids = array_map( 'absint', $this->relevant_post_ids );
							$relevantIDsSQL = implode( ',', $this->relevant_post_ids );
							$andInternalSQL .= " AND {$this->db_prefix}index.post_id IN ({$relevantIDsSQL}) ";
						}

						$andInternalSQL .= ' AND ( ';

						// $weight will sometimes be an array (taxonomies and custom fields)
						if ( ! is_array( $weight ) && intval( $weight ) < 0 ) {
							$applicableExclusion = true;
							switch ( $type ) {
								case 'title':
									$andInternalSQL .= " {$this->db_prefix}index.title > 0  OR ";
									break;
								case 'content':
									$andInternalSQL .= " {$this->db_prefix}index.content > 0  OR ";
									break;
								case 'slug':
									$andInternalSQL .= " {$this->db_prefix}index.slug > 0  OR ";
									break;
								case 'excerpt':
									$andInternalSQL .= " {$this->db_prefix}index.excerpt > 0  OR ";
									break;
								case 'comment':
									$andInternalSQL .= " {$this->db_prefix}index.comment > 0  OR ";
									break;
							}
						} else {
							// it's either a taxonomy or custom field, so we need to handle it a bit differently
							if ( 'tax' == $type ) {
								foreach ( $weight as $postTypeTax => $postTypeTaxWeight ) {
									if ( intval( $postTypeTaxWeight ) < 0 ) {
										$applicableExclusion = true;

										// taxonomy name has already been validated by always safest to escape
										if ( ! taxonomy_exists( $postTypeTax ) ) {
											wp_die( 'Invalid request', 'searchwp' );
										}
										$postTypeTax = $wpdb->prepare( '%s', $postTypeTax );

										$andInternalSQL .= " ( {$this->db_prefix}tax.taxonomy = {$postTypeTax} AND {$this->db_prefix}tax.count > 0 )  OR ";
									}
								}
							} elseif ( 'cf' == $type ) {
								foreach ( $weight as $postTypeCustomField ) {

									if ( ! is_array( $postTypeCustomField ) ) {
										continue;
									}

									if ( isset( $postTypeCustomField['weight'] ) ) {
										$postTypeCustomFieldWeight = $postTypeCustomField['weight'];
									}

									if ( isset( $postTypeCustomField['metakey'] ) ) {
										$postTypeCustomFieldMetakey = $postTypeCustomField['metakey'];
									}

									if ( intval( $postTypeCustomFieldWeight ) < 0 ) {
										$applicableExclusion = true;

										// field name has already been validated by always safest to escape
										$postTypeCustomFieldKey = $wpdb->prepare( '%s', $postTypeCustomFieldMetakey );

										$andInternalSQL .= " ( {$this->db_prefix}cf.metakey = {$postTypeCustomFieldKey} AND {$this->db_prefix}cf.count > 0 )  OR ";
									}
								}
							}
						}

						// trim off the extra OR
						$andInternalSQL = substr( $andInternalSQL, 0, strlen( $andInternalSQL ) - 4 ) . " ) AND {$wpdb->posts}.post_type = '{$postType}' GROUP BY {$this->db_prefix}index.post_id";

						// if this exclusion is applicable, grab post IDs that trigger the exclusion
						if ( $applicableExclusion ) {
							$postsWithTerm = $wpdb->get_col( $andInternalSQL );

							// add these post IDs to the heap (we're going to make it unique later)
							$andTerms = array_merge( $andTerms, array_map( 'absint', $postsWithTerm ) );
						}
					}
				}
			}
		}

		// $andTerms is a conglomerate pile of post IDs violating the exclusion rule
		$andTerms = array_unique( $andTerms );

		// merge the weight-based exlusions on to the main excludes
		$excludeIDs = array_merge( $this->excluded, $andTerms );

		// make sure everything is an int
		if ( ! empty( $excludeIDs ) ) {
			$excludeIDs = array_map( 'absint', $excludeIDs );
		}

		$this->excluded = $excludeIDs;
	}


	/**
	 * Find posts that meet AND logic limitations in the title only
	 *
	 * @return array|mixed Applicable Post IDs
	 * @since 1.8
	 */
	private function get_post_ids_via_and_in_title() {
		global $wpdb;

		// find posts where all terms appear in the title
		$andTerms = array();
		$applicableAndResults = true;
		$relevantPostIds = $this->relevant_post_ids;

		if ( ! empty( $this->relevant_post_ids ) ) {
			$this->relevant_post_ids = array_map( 'absint', $this->relevant_post_ids );
		}
		if ( ! empty( $this->excluded ) ) {
			$this->excluded = array_map( 'absint', $this->excluded );
		}

		$intermediateIncludeSQL = ( ! empty( $this->relevant_post_ids ) ) ? " AND {$this->db_prefix}index.post_id IN (" . implode( ',', $this->relevant_post_ids ) . ') ' : '';
		$intermediateExcludeSQL = ( ! empty( $this->excluded ) ) ? " AND {$this->db_prefix}index.post_id NOT IN (" . implode( ',', $this->excluded ) . ') ' : '';

		// grab posts with each term in the title
		foreach ( $this->terms as $andTerm ) {
			// determine whether we want a term match or stem match
			$andTermLower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $andTerm, 'UTF-8' ) : strtolower( $andTerm );
			if ( ! isset( $postTypeWeights['options']['stem'] ) || empty( $postTypeWeights['options']['stem'] ) ) {
				$relavantTermWhere = $wpdb->prepare( " {$this->db_prefix}terms.term = %s ", $andTermLower );
			} else {
				$unstemmed = $andTermLower;
				$maybeStemmed = apply_filters( 'searchwp_custom_stemmer', $unstemmed );

				// if the term was stemmed via the filter use it, else generate our own
				$andTerm = ( $unstemmed == $maybeStemmed ) ? $this->stemmer->stem( $andTerm ) : $maybeStemmed;

				$relavantTermWhere = $wpdb->prepare( " {$this->db_prefix}terms.stem = %s ", $andTerm );
			}

			$postsWithTermInTitle = $wpdb->get_col(
				"SELECT post_id
                FROM {$this->db_prefix}index
                LEFT JOIN {$this->db_prefix}terms
                ON {$this->db_prefix}index.term = {$this->db_prefix}terms.id
                WHERE {$relavantTermWhere}
                {$intermediateExcludeSQL}
                {$intermediateIncludeSQL}
                AND {$this->db_prefix}index.title > 0"
			);

			if ( ! empty( $postsWithTermInTitle ) ) {
				$andTerms[] = $postsWithTermInTitle;
			} else {
				// since no posts were found with this term in the title, our AND logic fails
				$applicableAndResults = false;
				break;
			}
		}

		// find the common post IDs across the board
		if ( $applicableAndResults ) {
			$relevantPostIds = call_user_func_array( 'array_intersect', $andTerms );
			do_action( 'searchwp_log', 'Algorithm AND refinement pass: ' . implode( ', ', $relevantPostIds ) );
		}

		return $relevantPostIds;
	}


	/**
	 * Opens the main query SQL
	 *
	 * @since 1.8
	 */
	private function query_open() {
		global $wpdb;

		$this->sql = "SELECT SQL_CALC_FOUND_ROWS {$wpdb->prefix}posts.ID AS post_id, ";
	}


	/**
	 * Generate the SQL that calculates overall weight for a post type for a search term
	 *
	 * @since 1.8
	 */
	private function query_sum_post_type_weights() {
		// sum our final weights per post type
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] ) {
				$termCounter = 1;
				$this->sql .= 'SUM( ';
				if ( empty( $postTypeWeights['options']['attribute_to'] ) ) {
					/** @noinspection PhpUnusedLocalVariableInspection */
					foreach ( $this->terms as $term ) {
						$this->sql .= "COALESCE(term{$termCounter}.`{$postType}weight`,0) + ";
						$termCounter++;
					}
				} else {
					/** @noinspection PhpUnusedLocalVariableInspection */
					foreach ( $this->terms as $term ) {
						$this->sql .= "COALESCE(term{$termCounter}.`{$postType}attr`,0) + ";
						$termCounter++;
					}
				}
				$this->sql = substr( $this->sql, 0, strlen( $this->sql ) - 2 ); // trim off the extra +
				$this->sql .= " ) AS `final{$postType}weight`, ";
			}
		}
	}


	/**
	 * Generate the SQL that calculates the overall weight for a search term
	 *
	 * @since 1.8
	 */
	private function query_sum_final_weight() {
		global $wpdb;
		// build our final, overall weight
		$this->sql .= ' SUM( ';
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] ) {
				$termCounter = 1;
				if ( empty( $postTypeWeights['options']['attribute_to'] ) ) {
					/** @noinspection PhpUnusedLocalVariableInspection */
					foreach ( $this->terms as $term ) {
						$this->sql .= "COALESCE(term{$termCounter}.`{$postType}weight`,0) + ";
						$termCounter++;
					}
				} else {
					/** @noinspection PhpUnusedLocalVariableInspection */
					foreach ( $this->terms as $term ) {
						$this->sql .= "COALESCE(term{$termCounter}.`{$postType}attr`,0) + ";
						$termCounter++;
					}
				}
			}
		}

		$this->sql = substr( $this->sql, 0, strlen( $this->sql ) - 2 ); // trim off the extra +
		$this->sql .= " ) AS finalweight FROM {$wpdb->prefix}posts ";
	}


	/**
	 * Check whether parent attribution is used anywhere for the current engine
	 * This needs to be checked because it has a big effect on how aggressive we can make the overall search query
	 *
	 * @since 2.4.1
	 *
	 * @return bool Whether attribution is applied anywhere given the current engine settings
	 */
	function maybe_attribution_anywhere() {
		$attribution_post_types = array();
		$attributed_post_ids = array();
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if (
				isset( $postTypeWeights['enabled'] )
				&& true == $postTypeWeights['enabled']
				&&
				(
					(
						isset( $postTypeWeights['options']['parent'] )
						&& ! empty( $postTypeWeights['options']['parent'] )
					)
					||
					(
						isset( $postTypeWeights['options']['attribute'] )
						&& ! empty( $postTypeWeights['options']['attribute'] )
					)
					||
					(
						isset( $postTypeWeights['options']['attribute_to'] )
						&& ! empty( $postTypeWeights['options']['attribute_to'] )
					)
				)
			) {
				$attribution_post_types[] = $postType;

				if ( isset( $postTypeWeights['options']['attribute_to'] )
					&& ! empty( $postTypeWeights['options']['attribute_to'] ) ) {
					$attributed_post_ids[] = absint( $postTypeWeights['options']['attribute_to'] );
				}

				if ( isset( $postTypeWeights['options']['attribute'] )
				     && ! empty( $postTypeWeights['options']['attribute'] ) ) {
					$attributed_post_ids[] = absint( $postTypeWeights['options']['attribute'] );
				}

				break;
			}
		}

		return array( 'post_types' => $attribution_post_types, 'post_ids' => $attributed_post_ids );
	}


	/**
	 * Generate the SQL that defines post type weight
	 *
	 * @since 1.8
	 */
	private function query_post_type_weight() {
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] && empty( $postTypeWeights['options']['attribute_to'] ) ) {
				$this->sql .= ", COALESCE(`{$postType}weight`,0) AS `{$postType}weight` ";
			}
		}
	}


	/**
	 * Generate the SQL that defines attributed post type weight
	 *
	 * @since 1.8
	 */
	private function query_post_type_attributed() {
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] && ! empty( $postTypeWeights['options']['attribute_to'] ) ) {
				$attributedTo = absint( $postTypeWeights['options']['attribute_to'] );
				// make sure we're not excluding the attributed post id
				if ( ! in_array( $attributedTo, $this->excluded ) ) {
					$this->sql .= ", COALESCE(`{$postType}attr`,0) as `{$postType}attr` ";
				} else {
					wp_die( 'Search configuration issue: attribution target is excluded' );
				}
			}
		}
	}


	/**
	 * Generate the SQL that totals the post weight totals
	 *
	 * @since 1.8
	 */
	private function query_post_type_weight_total() {
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] && empty( $postTypeWeights['options']['attribute_to'] ) ) {
				$this->sql .= " COALESCE(`{$postType}weight`,0) +";
			}
		}
	}


	/**
	 * Generate the SQL that totals the attributed post weight totals
	 *
	 * @since 1.8
	 */
	private function query_post_type_attributed_total() {
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] && ! empty( $postTypeWeights['options']['attribute_to'] ) ) {
				$attributedTo = absint( $postTypeWeights['options']['attribute_to'] );
				// make sure we're not excluding the attributed post id
				if ( ! in_array( $attributedTo, $this->excluded ) ) {
					$this->sql .= " COALESCE(`{$postType}attr`,0) +";
				}
			}
		}
	}


	/**
	 * Generate the SQL that opens the per-term sub-query
	 *
	 * @since 1.8
	 */
	private function query_open_term() {
		global $wpdb;

		$this->sql .= 'LEFT JOIN ( ';

		// our final query cap
		$this->sql .= "SELECT {$wpdb->prefix}posts.ID AS post_id ";

		// implement our post type weight column
		$this->query_post_type_weight();

		// implement our post type attributed weight column
		$this->query_post_type_attributed();

		$this->sql .= ' , ';

		// concatenate our total weight with post type weight
		$this->query_post_type_weight_total();

		// concatenate our total weight with our attributed weight
		$this->query_post_type_attributed_total();

		$this->sql = substr( $this->sql, 0, strlen( $this->sql ) - 2 ); // trim off the extra +

		$this->sql .= ' AS weight ';
		$this->sql .= " FROM {$wpdb->prefix}posts ";
	}


	/**
	 * Limit results pool by mime type
	 *
	 * @param $mimes array Mime types to include
	 * @since 1.8
	 */
	private function query_limit_by_mimes( $mimes ) {
		global $wpdb;

		$targetedMimes = SWP()->get_mimes_from_settings_ids( $mimes );

		if ( empty( $targetedMimes ) ) {
			return;
		}

		if ( is_array( $targetedMimes ) ) {
			foreach ( $targetedMimes as $key => $val ) {
				$targetedMimes[ $key ] = $wpdb->prepare( '%s', $val );
			}
		}

		// we have an array of keys that match MIME types (not subtypes) that we can limit to by appending this condition
		$this->sql_status .= " AND {$wpdb->prefix}posts.post_type = 'attachment' AND {$wpdb->prefix}posts.post_mime_type IN ( " . implode( ',', $targetedMimes ) . ' ) ';
	}


	/**
	 * Generate the SQL that totals Custom Field weight
	 *
	 * @param $weights array|int Custom Field weights from SearchWP settings
	 *
	 * @return string SQL to use in the main query
	 * @since 1.8
	 */
	private function query_coalesce_custom_fields( $weights) {
		$coalesceCustomFields = '0 +';
		$this->meta_count = 0;
		if ( isset( $weights ) && is_array( $weights ) && ! empty( $weights ) ) {

			// first we'll try to merge any matching weight meta_keys so as to save as many JOINs as possible
			$optimized_weights = array();
			$like_weights = array();
			foreach ( $weights as $post_type_meta_guid => $post_type_custom_field ) {
				$custom_field_weight = absint( $post_type_custom_field['weight'] );
				$post_type_custom_field_key = $post_type_custom_field['metakey'];

				// allow developers to implement LIKE matching on custom field keys
				if ( false == strpos( $post_type_custom_field_key, '%' ) ) {
					$optimized_weights[ $custom_field_weight ][] = $post_type_custom_field_key;
				} else {
					$like_weights[] = array(
						'metakey'   => $post_type_custom_field_key,
						'weight'    => $custom_field_weight,
					);
				}
			}

			$totalCustomFields = count( $optimized_weights ) + count( $like_weights );

			for ( $i = 0; $i < $totalCustomFields; $i++ ) {
				$coalesceCustomFields .= ' COALESCE(cfweights' . $i . '.cfweight' . $i . ',0) + ';
			}

			$this->meta_count = $totalCustomFields;
		}
		$coalesceCustomFields = substr( $coalesceCustomFields, 0, strlen( $coalesceCustomFields ) - 2 );

		return $coalesceCustomFields;
	}


	/**
	 * Generate the SQL that totals taxonomy weight
	 *
	 * @param $weights array|int Taxonomy weights from SearchWP settings
	 *
	 * @return string SQL to use in the main query
	 * @since 1.8
	 */
	private function query_coalesce_taxonomies( $weights ) {
		$coalesceTaxonomies = '0 +';
		$this->tax_count = 0;
		if ( isset( $weights ) && is_array( $weights ) && ! empty( $weights ) ) {

			// first we'll try to merge any matching weight taxonomies so as to save as many JOINs as possible
			$optimized_weights = array();
			foreach ( $weights as $taxonomy_name => $taxonomy_weight ) {
				$taxonomy_weight = absint( $taxonomy_weight );
				$optimized_weights[ $taxonomy_weight ][] = $taxonomy_name;
			}

			$totalTaxonomies = count( $optimized_weights );

			for ( $i = 0; $i < $totalTaxonomies; $i++ ) {
				$coalesceTaxonomies .= ' COALESCE(taxweights' . $i . '.taxweight' . $i . ',0) + ';
			}

			$this->tax_count = $totalTaxonomies;
		}

		$coalesceTaxonomies = substr( $coalesceTaxonomies, 0, strlen( $coalesceTaxonomies ) - 2 );

		return $coalesceTaxonomies;
	}


	/**
	 * Generate the SQL used to open the per-post type sub-query
	 *
	 * @param $args array Arguments for the post type
	 * @since 1.8
	 */
	private function query_post_type_open( $args ) {
		global $wpdb;

		$defaults = array(
			'post_type'         => 'post',
			'post_column'       => 'ID',
			'title_weight'      => function_exists( 'searchwp_get_engine_weight' ) ? searchwp_get_engine_weight( 'title' ) : 20,
			'slug_weight'       => function_exists( 'searchwp_get_engine_weight' ) ? searchwp_get_engine_weight( 'slug' ) : 10,
			'content_weight'    => function_exists( 'searchwp_get_engine_weight' ) ? searchwp_get_engine_weight( 'content' ) : 2,
			'comment_weight'    => function_exists( 'searchwp_get_engine_weight' ) ? searchwp_get_engine_weight( 'comment' ) : 1,
			'excerpt_weight'    => function_exists( 'searchwp_get_engine_weight' ) ? searchwp_get_engine_weight( 'excerpt' ) : 6,
			'custom_fields'     => 0,
			'taxonomies'        => 0,
			'attributed_to'     => false,
		);

		// process our arguments
		$args = wp_parse_args( $args, $defaults );

		if ( ! post_type_exists( $args['post_type'] ) ) {
			wp_die( 'Invalid request', 'searchwp' );
		}

		$post_type = $args['post_type'];

		$post_column = $args['post_column'];
		if ( ! in_array( $post_column, array( 'post_parent', 'ID' ) ) ) {
			$post_column = 'ID';
		}

		$title_weight   = absint( $args['title_weight'] );
		$slug_weight    = absint( $args['slug_weight'] );
		$content_weight = absint( $args['content_weight'] );
		$comment_weight = absint( $args['comment_weight'] );
		$excerpt_weight = absint( $args['excerpt_weight'] );

		$this->sql .= "
            LEFT JOIN (
                SELECT {$wpdb->prefix}posts.{$post_column} AS post_id,
                    ( SUM( {$this->db_prefix}index.title ) * {$title_weight} ) +
                    ( SUM( {$this->db_prefix}index.slug ) * {$slug_weight} ) +
                    ( SUM( {$this->db_prefix}index.content ) * {$content_weight} ) +
                    ( SUM( {$this->db_prefix}index.comment ) * {$comment_weight} ) +
                    ( SUM( {$this->db_prefix}index.excerpt ) * {$excerpt_weight} ) +
                    {$args['custom_fields']} + {$args['taxonomies']}";

		// allow developers to inject their own weight modifications
		$this->sql .= apply_filters( 'searchwp_weight_mods', '', array(
			'engine' => $this->engine,
		) );

		// the identifier is different if we're attributing
		$this->sql .= ! empty( $args['attributed_to'] ) ? " AS `{$post_type}attr` " : " AS `{$post_type}weight` " ;

		$this->sql .= "
            FROM {$this->db_prefix}terms
            LEFT JOIN {$this->db_prefix}index ON {$this->db_prefix}terms.id = {$this->db_prefix}index.term
            LEFT JOIN {$wpdb->prefix}posts ON {$this->db_prefix}index.post_id = {$wpdb->prefix}posts.ID
            {$this->sql_join}
        ";
	}


	/**
	 * Generate the SQL that extracts Custom Field weights
	 *
	 * @param $postType string The post type
	 * @param $weights array Custom Field weights from SearchWP Settings
	 * @since 1.8
	 */
	private function query_post_type_custom_field_weights( $postType, $weights ) {
		global $wpdb;

		$i = 0;

		// first we'll try to merge any matching weight meta_keys so as to save as many JOINs as possible
		$optimized_weights = array();
		$like_weights = array();
		foreach ( $weights as $post_type_meta_guid => $post_type_custom_field ) {

			$custom_field_weight = $post_type_custom_field['weight'];
			$post_type_custom_field_key = $post_type_custom_field['metakey'];

			if ( false !== strpos( $custom_field_weight, '.' ) ) {
				$custom_field_weight = (string) abs( floatval( $custom_field_weight ) );
			} else {
				$custom_field_weight = (string) absint( $custom_field_weight );
			}

			// allow developers to implement LIKE matching on custom field keys
			if ( false == strpos( $post_type_custom_field_key, '%' ) ) {
				$optimized_weights[ $custom_field_weight ][] = $post_type_custom_field_key;
			} else {
				$like_weights[] = array(
					'metakey'   => $post_type_custom_field_key,
					'weight'    => $custom_field_weight,
				);
			}
		}

		$column = 'ID';

		// our custom fields are now keyed by their weight, allowing us to group Custom Fields with the
		// same weight together in the same LEFT JOIN
		foreach ( $optimized_weights as $weight_key => $meta_keys_for_weight ) {
			$post_meta_clause = '';
			if ( ! in_array( 'searchwpcfdefault', str_ireplace( ' ', '', $meta_keys_for_weight ) ) ) {

				if ( is_array( $meta_keys_for_weight ) ) {
					foreach ( $meta_keys_for_weight as $key => $val ) {
						$meta_keys_for_weight[ $key ] = $wpdb->prepare( '%s', $val );
					}
				}

				$post_meta_clause = ' AND ' . $this->db_prefix . 'cf.metakey IN (' . implode( ',', $meta_keys_for_weight ) . ')';
			}
			$weight_key = floatval( $weight_key );
			$this->sql .= "
                LEFT JOIN (
                    SELECT {$wpdb->prefix}posts.{$column} as post_id, ( SUM( COALESCE(`{$this->db_prefix}cf`.`count`, 0) ) * {$weight_key} ) AS cfweight{$i}
                    FROM {$this->db_prefix}terms
                    LEFT JOIN {$this->db_prefix}cf ON {$this->db_prefix}terms.id = {$this->db_prefix}cf.term
                    LEFT JOIN {$wpdb->prefix}posts ON {$this->db_prefix}cf.post_id = {$wpdb->prefix}posts.ID
                    {$this->sql_join}
                    WHERE {$this->sql_term_where}
                    {$this->sql_status}
                    AND {$wpdb->prefix}posts.post_type = '{$postType}'
                    {$this->sql_exclude}
                    {$this->sql_include}
                    {$post_meta_clause}
                    {$this->sql_conditions}
                    GROUP BY post_id
                ) cfweights{$i} USING(post_id)";
			$i++;
		}

		// there also may be LIKE weights, though, so we need to build out that SQL as well
		if ( ! empty( $like_weights ) ) {
			foreach ( $like_weights as $like_weight ) {
				$like_weight['metakey'] = $wpdb->prepare( '%s', $like_weight['metakey'] );
				$like_weight['weight'] = floatval( $like_weight['weight'] );
				$post_meta_clause = ' AND ' . $this->db_prefix . 'cf.metakey LIKE ' . $like_weight['metakey'];
				$this->sql .= "
                LEFT JOIN (
                    SELECT {$wpdb->prefix}posts.{$column} as post_id, ( SUM( COALESCE(`{$this->db_prefix}cf`.`count`, 0) ) * {$like_weight['weight']} ) AS cfweight{$i}
                    FROM {$this->db_prefix}terms
                    LEFT JOIN {$this->db_prefix}cf ON {$this->db_prefix}terms.id = {$this->db_prefix}cf.term
                    LEFT JOIN {$wpdb->prefix}posts ON {$this->db_prefix}cf.post_id = {$wpdb->prefix}posts.ID
                    {$this->sql_join}
                    WHERE {$this->sql_term_where}
                    {$this->sql_status}
                    AND {$wpdb->prefix}posts.post_type = '{$postType}'
                    {$this->sql_exclude}
                    {$this->sql_include}
                    {$post_meta_clause}
                    {$this->sql_conditions}
                    GROUP BY post_id
                ) cfweights{$i} USING(post_id)";
				$i++;
			}
		}

	}


	/**
	 * Generate the SQL that extracts taxonomy weights
	 *
	 * @param $postType string The post type
	 * @param $weights array Taxonomy weights from SearchWP Settings
	 * @since 1.8
	 */
	private function query_post_type_taxonomy_weights( $postType, $weights) {
		global $wpdb;

		$i = 0;

		// first we'll try to merge any matching weight taxonomies so as to save as many JOINs as possible
		$optimized_weights = array();
		foreach ( $weights as $taxonomy_name => $taxonomy_weight ) {
			$taxonomy_weight = absint( $taxonomy_weight );
			$optimized_weights[ $taxonomy_weight ][] = $taxonomy_name;
		}

		foreach ( $optimized_weights as $postTypeTaxWeight => $postTypeTaxonomies ) {

			$postTypeTaxWeight = absint( $postTypeTaxWeight );

			if ( is_array( $postTypeTaxonomies ) ) {
				foreach ( $postTypeTaxonomies as $key => $val ) {
					$postTypeTaxonomies[ $key ] = $wpdb->prepare( '%s', $val );
				}
			}

			$this->sql .= "
                LEFT JOIN (
                    SELECT {$this->db_prefix}tax.post_id, ( SUM( {$this->db_prefix}tax.count ) * {$postTypeTaxWeight} ) AS taxweight{$i}
                    FROM {$this->db_prefix}terms
                    LEFT JOIN {$this->db_prefix}tax ON {$this->db_prefix}terms.id = {$this->db_prefix}tax.term
                    LEFT JOIN {$wpdb->prefix}posts ON {$this->db_prefix}tax.post_id = {$wpdb->prefix}posts.ID
                    {$this->sql_join}
                    WHERE {$this->sql_term_where}
                    {$this->sql_status}
                    AND {$wpdb->prefix}posts.post_type = '{$postType}'
                    {$this->sql_exclude}
                    {$this->sql_include}
                    AND {$this->db_prefix}tax.taxonomy IN (" . implode( ',', $postTypeTaxonomies ) . ")
                    {$this->sql_conditions}
                    GROUP BY {$this->db_prefix}tax.post_id
                ) taxweights{$i} USING(post_id)";
			$i++;
		}
	}


	/**
	 * Generate the SQL that closes the per-post type sub-query
	 *
	 * @param string $postType The post type
	 * @param bool|int $attribute_to The attribution target post ID (if applicable)
	 *
	 * @since 1.8
	 */
	private function query_post_type_close( $postType, $attribute_to = false ) {
		global $wpdb;

		if ( ! post_type_exists( $postType ) ) {
			wp_die( 'Invalid request', 'searchwp' );
		}

		$post_type_group_by = apply_filters( 'searchwp_post_type_group_by_clause', array( "{$wpdb->prefix}posts.ID" ) );
		$post_type_group_by = array_map( 'esc_sql', $post_type_group_by );
		$post_type_group_by = implode( ', ', $post_type_group_by );

		// cap off each enabled post type subquery
		$this->sql .= "
            WHERE {$this->sql_term_where}
            {$this->sql_status}
            AND {$wpdb->prefix}posts.post_type = '{$postType}'
            {$this->sql_exclude}
            {$this->sql_include}
            {$this->sql_conditions}
			GROUP BY {$post_type_group_by}";

		// @since 2.9.0
		$this->sql .= $this->only_full_group_by_fix_for_post_type();

		if ( isset( $attribute_to ) && ! empty( $attribute_to ) ) {
			// $attributedTo was defined in the initial conditional
			$attributedTo = absint( $attribute_to );
			$this->sql .= ") `attributed{$postType}` ON $attributedTo = {$wpdb->prefix}posts.ID";
		} else {
			$this->sql .= ") AS `{$postType}weights` ON `{$postType}weights`.post_id = {$wpdb->prefix}posts.ID";
		}
	}

	/**
	 * MySQL 5.7 has sql_mode=only_full_group_by on by default so we need to accommodate
	 * our various COALESCE columns by adding to the GROUP BY clause to satisfy the mode
	 *
	 * This is run for each post type within each term
	 */
	private function only_full_group_by_fix_for_post_type() {
		if ( empty( $this->tax_count  ) && empty( $this->meta_count ) ) {
			return '';
		}

		$taxonomies = array();
		$custom_fields = array();

		for ( $i = 0; $i < $this->tax_count; $i++ ) {
			$taxonomies[] = 'taxweights' . $i . '.taxweight' . $i;
		}

		$meta = array();
		for ( $i = 0; $i < $this->meta_count; $i++ ) {
			$custom_fields[] = 'cfweights' . $i . '.cfweight' . $i;
		}

		return ',' . implode( ',', array_merge( $taxonomies, $custom_fields ) );
	}


	/**
	 * Generate the SQL that limits search results to a specific minimum weight per post type
	 *
	 * @since 1.8
	 */
	private function query_limit_post_type_to_weight() {
		$this->sql .= ' WHERE ';

		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] && empty( $postTypeWeights['options']['attribute_to'] ) ) {
				$this->sql .= " COALESCE(`{$postType}weight`,0) +";
			}
		}

		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] && ! empty( $postTypeWeights['options']['attribute_to'] ) ) {
				$attributedTo = absint( $postTypeWeights['options']['attribute_to'] );
				// make sure we're not excluding the attributed post id
				if ( ! in_array( $attributedTo, $this->excluded ) ) {
					$this->sql .= " COALESCE(`{$postType}attr`,0) +";
				}
			}
		}

		$this->sql = substr( $this->sql, 0, strlen( $this->sql ) - 2 ); // trim off the extra +
		$this->sql .= ' > ' . absint( apply_filters( 'searchwp_weight_threshold', 0 ) ) . ' ';
	}


	/**
	 * Generate the SQL that limits search results to a specific minimum weight overall
	 *
	 * @since 1.8
	 */
	private function query_limit_to_weight() {
		$this->sql .= ' WHERE   ';

		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] ) {
				$termCounter = 1;
				if ( empty( $postTypeWeights['options']['attribute_to'] ) ) {
					/** @noinspection PhpUnusedLocalVariableInspection */
					foreach ( $this->terms as $term ) {
						$this->sql .= "COALESCE(term{$termCounter}.`{$postType}weight`,0) + ";
						$termCounter++;
					}
				} else {
					/** @noinspection PhpUnusedLocalVariableInspection */
					foreach ( $this->terms as $term ) {
						$this->sql .= "COALESCE(term{$termCounter}.`{$postType}attr`,0) + ";
						$termCounter++;
					}
				}
			}
		}

		$this->sql = substr( $this->sql, 0, strlen( $this->sql ) - 2 ); // trim off the extra +
		$this->sql .= ' > ' . absint( apply_filters( 'searchwp_weight_threshold', 0 ) ) . ' ';
	}


	/**
	 * Dynamically generate SQL query based on engine settings and retrieve a weighted, ordered list of posts
	 *
	 * @return bool|array Post IDs found in the index
	 * @since 1.0
	 */
	function query_for_post_ids() {
		global $wpdb;

		do_action( 'searchwp_log', 'query_for_post_ids()' );

		// check to make sure there are settings for the current engine
		if ( ! isset( $this->settings['engines'][ $this->engine ] ) && is_array( $this->settings['engines'][ $this->engine ] ) ) {
			return false;
		}

		// check to make sure we actually have terms to search
		// TODO: refactor this
		if ( empty( $this->terms ) ) {
			// short circuit
			$this->foundPosts = 0;
			$this->maxNumPages = 0;
			$this->postIDs = array();

			do_action( 'searchwp_log', 'No terms, short circuit' );

			return false;
		}

		// check to make sure that all post types in the settings are still in fact registered and active
		// e.g. in case a Custom Post Type was saved in the settings but no longer exists
		$this->validate_post_types();

		// we might need to short circuit for a number of reasons
		if ( ! $this->any_enabled_post_types() ) {
			do_action( 'searchwp_log', 'No enabled post types, short circuit' );

			return false;
		}

		// allow devs to filter excluded IDs
		$this->excluded = apply_filters( 'searchwp_exclude', $this->excluded, $this->engine, $this->terms );
		if ( is_array( $this->excluded ) ) {
			$this->excluded = array_map( 'absint', $this->excluded );
		}

		// perform our AND logic before getting started
		// e.g. we're going to limit to posts that have all of the search terms
		$this->maybe_do_and_logic();

		$this->exclude_posts_by_weight();

		// Build exclusion SQL
		$this->sql_exclude = ( ! empty( $this->excluded ) ) ? " AND {$wpdb->prefix}posts.ID NOT IN (" . implode( ',', $this->excluded ) . ') ' : '';

		// if there's an insane number of posts returned, we're dealing with a site with a lot of similar content
		// so we need to trim out the initial results by relevance before proceeding else we'll have a wicked slow query

		// NOTE: this only applies if titles have weights for all enabled post types, so we must check that first
		$able_to_refine_results = true;
		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] ) {
				$title_weight = isset( $postTypeWeights['weights']['title'] ) ? absint( $postTypeWeights['weights']['title'] ) : 0;
				if ( 0 == $title_weight ) {
					// at least one title weight is zero so we are NOT ABLE to refine results any
					// further because the post IDs we find when refining by title will not apply
					// in the main search query since those title hits are worth nothing
					$able_to_refine_results = false;

					do_action( 'searchwp_log', 'Unable to further refine results' );

					break;
				}
			}
		}

		// if the include pool has not been limited, do that
		if ( empty( $this->included ) ) {
			$parity = count( $this->terms );
			$maxNumAndResults = absint( apply_filters( 'searchwp_max_and_results', 300 ) );
			if (
				$parity > 1
				&& $able_to_refine_results
				&& apply_filters( 'searchwp_refine_and_results', true )
				&& count( $this->relevant_post_ids ) > $maxNumAndResults
			) {
				$this->relevant_post_ids = $this->get_post_ids_via_and_in_title();

				do_action( 'searchwp_log', 'Refining AND results based on Title' );
			}

			// make sure we've got an array of unique integers
			$this->relevant_post_ids = array_map( 'absint', array_unique( $this->relevant_post_ids ) );
		} else {
			$this->relevant_post_ids = $this->included;
		}

		// allow devs to filter included post IDs
		add_filter( 'searchwp_force_wp_query', '__return_true' );
		$this->included = apply_filters( 'searchwp_include', $this->relevant_post_ids, $this->engine, $this->terms );
		remove_filter( 'searchwp_force_wp_query', '__return_true' );

		// allow devs to force AND logic all the time, no matter what (if there was more than one search term)
		$forceAnd = ( count( $this->terms ) > 1 && apply_filters( 'searchwp_and_logic_only', false ) ) ? true : false;

		// if it was totally empty and AND logic is forced, we'll hit a SQL error, so populate it with an impossible ID
		if ( empty( $this->included ) && $forceAnd ) {
			$this->included = array( 0 );
		}

		if ( is_array( $this->included ) ) {
			$this->included = array_map( 'absint', $this->included );
		}

		$this->sql_include = ( ( is_array( $this->included ) && ! empty( $this->included ) ) || $forceAnd ) ? " AND {$wpdb->prefix}posts.ID IN (" . implode( ',', $this->included ) . ') ' : '';

		/**
		 * Build the search query
		 */
		$this->query_open();

		// allow for injection into main SELECT
		$select_inject = trim( (string) apply_filters( 'searchwp_query_select_inject', '' ) );
		if ( ! empty( $select_inject ) ) {
			// we're automatically going to append the comma, so if it was returned we can kill it
			if ( ',' == substr( $select_inject, -1 ) ) {
				$select_inject = substr( $select_inject, 0, strlen( $select_inject ) - 1 );
			}
			$this->sql .= ' ' . $select_inject . ' , ';
		}

		$this->query_sum_post_type_weights();
		$this->query_sum_final_weight();

		// allow for pre-algorithm join
		$this->sql = ' ' . (string) apply_filters( 'searchwp_query_main_join', $this->sql, $this->engine ) . ' ';

		// loop through each submitted term
		$termCounter = 1;
		foreach ( $this->terms as $term ) {

			$this->query_open_term();

			// build our post type queries
			foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
				if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] ) {
					// TODO: store our post format clause and integrate
					// TODO: store our post status clause and integrate

					// prep the term
					$prepped_term           = $this->prep_term( $term, $postTypeWeights );
					$term                   = $prepped_term['term'];
					$term_or_stem           = $prepped_term['term_or_stem'];
					$original_prepped_term  = $prepped_term['original_prepped_term'];
					$this->cache_term_final( $term );

					// build our final term WHERE
					if ( ! in_array( $term_or_stem, array( 'term', 'stem' ) ) ) {
						wp_die( 'Invalid request', 'searchwp' );
					}
					$this->sql_term_where = " {$this->db_prefix}terms." . $term_or_stem . ' IN (' . implode( ',', $term ) . ')';
					/** @noinspection PhpUnusedLocalVariableInspection */
					$last_term = $term;

					// if it's an attachment we need to force 'inherit'
					$post_statuses = $postType == 'attachment' ? array( 'inherit' ) : $this->post_statuses;

					if ( is_array( $post_statuses ) ) {
						foreach ( $post_statuses as $key => $val ) {
							$post_statuses[ $key ] = $wpdb->prepare( '%s', $val );
						}
					}

					$this->sql_status = "AND {$wpdb->prefix}posts.post_status IN ( " . implode( ',', $post_statuses ) . ' ) ';

					// determine whether we need to limit to a mime type
					if ( isset( $postTypeWeights['options']['mimes'] ) && '' !== $postTypeWeights['options']['mimes'] ) {

						// stored as an array of integers that correlate to mime type groups
						$mimes = explode( ',', $postTypeWeights['options']['mimes'] );
						$mimes = array_map( 'absint', $mimes );

						$this->query_limit_by_mimes( $mimes );
					}

					// Take into consideration the engine limiter rules FOR THIS POST TYPE
					$limited_ids = $this->get_included_ids_from_taxonomies_for_post_type( $postType );
					// Function returns false if not applicable
					if ( is_array( $limited_ids ) && ! empty( $limited_ids ) ) {
						$limited_ids = array_map( 'absint', $limited_ids );
						$limited_ids = array_unique( $limited_ids );
						$this->sql_status .= " AND {$wpdb->prefix}posts.post_type = '{$postType}' AND {$wpdb->prefix}posts.ID IN ( " . implode( ',', $limited_ids ) . ' ) ';
					}

					// reset back to our original term
					$term = $original_prepped_term;

					// we need to use absint because if a weight was set to -1 for exclusion, it was already forcefully excluded
					$titleWeight    = isset( $postTypeWeights['weights']['title'] )   ? absint( $postTypeWeights['weights']['title'] )   : 0;
					$slugWeight     = isset( $postTypeWeights['weights']['slug'] )    ? absint( $postTypeWeights['weights']['slug'] )    : 0;
					$contentWeight  = isset( $postTypeWeights['weights']['content'] ) ? absint( $postTypeWeights['weights']['content'] ) : 0;
					$excerptWeight  = isset( $postTypeWeights['weights']['excerpt'] ) ? absint( $postTypeWeights['weights']['excerpt'] ) : 0;

					if ( apply_filters( 'searchwp_index_comments', true ) ) {
						$commentWeight = isset( $postTypeWeights['weights']['comment'] ) ? absint( $postTypeWeights['weights']['comment'] ) : 0;
					} else {
						$commentWeight = 0;
					}

					// build the SQL to accommodate Custom Fields
					$custom_field_weights = isset( $postTypeWeights['weights']['cf'] ) ? $postTypeWeights['weights']['cf'] : 0;
					$coalesceCustomFields = $this->query_coalesce_custom_fields( $custom_field_weights );

					// build the SQL to accommodate Taxonomies
					$taxonomy_weights = isset( $postTypeWeights['weights']['tax'] ) ? $postTypeWeights['weights']['tax'] : 0;
					$coalesceTaxonomies = $this->query_coalesce_taxonomies( $taxonomy_weights );

					// allow additional tables to be joined
					$this->sql_join = apply_filters( 'searchwp_query_join', '', $postType, $this->engine );
					if ( ! is_string( $this->sql_join ) ) {
						$this->sql_join = '';
					}

					// allow additional conditions
					$this->sql_conditions = apply_filters( 'searchwp_query_conditions', '', $postType, $this->engine );
					if ( ! is_string( $this->sql_conditions ) ) {
						$this->sql_conditions = '';
					}

					// if we're dealing with attributed weight we need to make sure that the attribution target was not excluded
					$excludedByAttribution = false;
					$attributedTo = false;
					if ( isset( $postTypeWeights['options']['attribute_to'] ) && ! empty( $postTypeWeights['options']['attribute_to'] ) ) {
						$postColumn = 'ID';
						$attributedTo = absint( $postTypeWeights['options']['attribute_to'] );
						if ( in_array( $attributedTo, $this->excluded ) ) {
							$excludedByAttribution = true;
						}
					} else {
						// if it's an attachment and we want to attribute to the parent, we need to set that here
						$postColumn = ! empty( $postTypeWeights['options']['parent'] ) ? 'post_parent' : 'ID';
					}

					// open up the post type subquery if not excluded by attribution
					if ( ! $excludedByAttribution ) {
						$post_type_params = array(
							'post_type'         => $postType,
							'post_column'       => $postColumn,
							'title_weight'      => $titleWeight,
							'slug_weight'       => $slugWeight,
							'content_weight'    => $contentWeight,
							'comment_weight'    => $commentWeight,
							'excerpt_weight'    => $excerptWeight,
							'custom_fields'     => isset( $coalesceCustomFields ) ? $coalesceCustomFields : '',
							'taxonomies'        => isset( $coalesceTaxonomies ) ? $coalesceTaxonomies : '',
							'attributed_to'     => $attributedTo,
						);
						$this->query_post_type_open( $post_type_params );

						// handle custom field weights
						if ( isset( $postTypeWeights['weights']['cf'] ) && is_array( $postTypeWeights['weights']['cf'] ) && ! empty( $postTypeWeights['weights']['cf'] ) ) {
							$this->query_post_type_custom_field_weights( $postType, $postTypeWeights['weights']['cf'] );
						}

						// handle taxonomy weights
						if ( isset( $postTypeWeights['weights']['tax'] ) && is_array( $postTypeWeights['weights']['tax'] ) && ! empty( $postTypeWeights['weights']['tax'] ) ) {
							$this->query_post_type_taxonomy_weights( $postType, $postTypeWeights['weights']['tax'] );
						}

						// close out the per-post type sub-query
						$attribute_to = isset( $postTypeWeights['options']['attribute_to'] ) ? absint( $postTypeWeights['options']['attribute_to'] ) : false;
						$this->query_post_type_close( $postType, $attribute_to );
					}
				}
			}

			$this->sql .= " LEFT JOIN {$this->db_prefix}index ON {$this->db_prefix}index.post_id = {$wpdb->prefix}posts.ID ";
			$this->sql .= " LEFT JOIN {$this->db_prefix}terms ON {$this->db_prefix}terms.id = {$this->db_prefix}index.term ";

			// make sure we're only getting posts with actual weight
			$this->query_limit_post_type_to_weight();

			$this->sql .= $this->query_limit_pool_by_stem();

			$this->sql .= $this->post_status_limiter_sql( $this->engineSettings );

			$this->sql .= ' GROUP BY post_id';

			$this->sql .= $this->only_full_group_by_fix_for_term();

			$this->sql .= " ) AS term{$termCounter} ON term{$termCounter}.post_id = {$wpdb->prefix}posts.ID ";

			$termCounter++;
		}

		/**
		 * END LOOP THROUGH EACH SUBMITTED TERM
		 */

		// make sure we're only getting posts with actual weight
		$this->query_limit_to_weight();

		$this->sql .= $this->post_status_limiter_sql( $this->engineSettings );

		$modifier = ( $this->postsPer < 1 ) ? 1 : $this->postsPer; // if posts_per_page is -1 there's no offset
		$start = ! empty( $this->offset ) ? $this->offset : intval( ( $this->page - 1 ) * $modifier );
		$total = intval( $this->postsPer );
		$order = $this->order;

		// accommodate a custom offset
		$start = absint( apply_filters( 'searchwp_query_limit_start', $start, $this->page, $this->engine ) );
		$total = absint( apply_filters( 'searchwp_query_limit_total', $total, $this->page, $this->engine ) );

		$extraWhere = apply_filters( 'searchwp_where', '', $this->engine );
		$this->sql .= ' ' . $extraWhere . ' ';

		// allow developers to order by date
		$orderByDate = apply_filters( 'searchwp_return_orderby_date', false, $this->engine );
		$finalOrderBySQL = $orderByDate ? " ORDER BY post_date {$order}, finalweight {$order} " : " ORDER BY finalweight {$order}, post_date DESC ";

		// allow developers to return completely random results that meet the minumum weight
		if ( apply_filters( 'searchwp_return_orderby_random', false, $this->engine ) ) {
			$finalOrderBySQL = ' ORDER BY RAND() ';
		}

		// allow for arbitrary ORDER BY filtration
		$finalOrderBySQL = apply_filters( 'searchwp_query_orderby', $finalOrderBySQL, $this->engine );

		if ( apply_filters( 'searchwp_query_allow_query_string_override_orderby', true ) ) {
			if ( ! empty( $_GET['orderby'] ) ) {
				$query_orderby = $this->get_query_string_orderby();

				if ( ! empty( $query_orderby ) ) {
					$finalOrderBySQL = esc_sql( " ORDER BY {$query_orderby} {$order}" );
				}
			}
		}

		// make sure we limit the overall wp_posts pool to what was returned in the subqueries
		if ( $forceAnd ) {
			for ( $i = 1; $i <= count( $this->terms ); $i++ ) {
				$this->sql .= " AND {$wpdb->prefix}posts.ID IN (term" . $i . '.post_id) ';
			}
		} else {
			$end_cap_limiter = '';
			for ( $i = 1; $i <= count( $this->terms ); $i++ ) {
				$end_cap_limiter .= 'term' . $i . '.post_id,';
			}
			$this->sql .= " AND {$wpdb->prefix}posts.ID IN (" . substr( $end_cap_limiter, 0, strlen( $end_cap_limiter ) - 1 ) . ') ';
		}

		// also limit the wp_posts pool taking into consideration exclusions
		$this->sql .= $this->sql_exclude;

		// group the results
		$this->sql .= " GROUP BY post_id ";
		// $this->sql .= $this->only_full_group_by_fix_for_query();
		$this->sql .= $finalOrderBySQL . ' ';

		if ( $this->postsPer > 0 ) {
			$this->sql .= " LIMIT {$start}, {$total}";
		}

		$this->sql = str_replace( "\n", ' ', $this->sql );
		$this->sql = str_replace( "\t", ' ', $this->sql );

		// allow BIG_SELECTS
		$bigSelects = apply_filters( 'searchwp_big_selects', false );
		if ( $bigSelects ) {
			$wpdb->query( 'SET SQL_BIG_SELECTS=1' );
		}

		// retrieve all results and associated weights (SQL was prepared throughout generation)
		$searchwp_query_results = $wpdb->get_results( $this->sql );

		// if there was in fact a SQL_BIG_SELECTS error let's grab it and try the query again
		if ( isset ( $wpdb->last_error ) && false !== strpos( $wpdb->last_error, 'SQL_BIG_SELECTS' ) && current_user_can( apply_filters( 'searchwp_settings_cap', 'manage_options' ) ) ) {
			do_action( 'searchwp_log', "!!! SQL_BIG_SELECTS error detected, please add_filter( 'searchwp_big_selects', '__return_true' );" );
			// show an entry in the admin bar if it's visible
			if ( is_admin_bar_showing() ) {
				add_action( 'wp_footer', array( $this, 'admin_bar_sql_big_selects_notice_assets' ) );
				add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar_sql_big_selects_notice' ), 999 );
			} else {
				// TODO: the query failed, so no results are showing, we can't filter titles or content, so we need to do something
			}
		}

		// format the results
		$postIDs = array(); // going to store all of the returned post IDs
		$this->results_weights = array(); // store all of the specific weights
		if ( ! empty( $searchwp_query_results ) ) {
			foreach ( $searchwp_query_results as $searchwp_query_result ) {

				// store the weights for this post
				$weights = array(
					'post_id'       => null,
					'weight'        => null,
					'post_types'    => array()
				);

				// the results returned are just the table results from the query, let's format them a bit
				foreach ( $searchwp_query_result as $searchwp_query_result_key => $searchwp_query_result_value ) {
					switch ( $searchwp_query_result_key ) {
						case 'post_id' :
							$postIDs[] = absint( $searchwp_query_result->post_id );
							$weights['post_id'] = absint( $searchwp_query_result->post_id );
							break;
						case 'finalweight' :
							$weights['weight'] = absint( $searchwp_query_result_value );
							break;
						default :
							$weight_key = str_replace( array( 'final', 'weight' ), '', $searchwp_query_result_key );
							$weights['post_types'][ $weight_key ] = absint( $searchwp_query_result_value );
							break;
					}
				}

				$this->results_weights[ $searchwp_query_result->post_id ] = $weights;
			}
		}

		do_action( 'searchwp_log', 'Search results: ' . implode( ', ', $postIDs ) );

		// retrieve how many total posts were found without the limit
		$this->foundPosts = (int) $wpdb->get_var(
			apply_filters_ref_array(
				'found_posts_query',
				array( 'SELECT FOUND_ROWS()', &$wpdb )
			)
		);

		// store an accurate max_num_pages for $wp_query
		$this->maxNumPages = ( $this->postsPer < 1 ) ? 1 : ceil( $this->foundPosts / $this->postsPer );

		// store our post IDs
		$this->postIDs = $postIDs;

		return true;
	}

	/**
	 * Related to only_full_group_by_fix_for_post_type() but runs for the whole query
	 */
	function only_full_group_by_fix_for_query() {
		$return = array();

		$total_terms = count( $this->terms );

		for ( $i = 1; $i <= $total_terms; $i++ ) {
			foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
				if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] ) {
					if ( empty( $postTypeWeights['options']['attribute_to'] ) ) {
						$return[] = 'term' . $i . '.`' . $postType . 'weight`';
					} else {
						$return[] = 'term' . $i . '.`' . $postType . 'attr`';
					}
				}
			}
		}

		return ' ,' . implode( ',', $return ) . ' ';
	}

	/**
	 * Use query string to force final orderby
	 */
	function get_query_string_orderby() {
	    global $wpdb;

		if ( empty( $_GET['orderby'] ) ) {
			return '';
		}

		$this_orderby = strtolower( $_GET['orderby'] );

		// Keys are the query string, values are the database column
		$allowed_orderbys = array(
			'title' => 'post_title'
		);

		if ( ! array_key_exists( $this_orderby, $allowed_orderbys ) ) {
		    return '';
        }

		return $wpdb->posts . '.' . $allowed_orderbys[ $this_orderby ];
	}

	/**
	 * Callback when an error was detected during the search, outputs CSS for the Admin bar
	 *
	 * @since 2.3.2
	 */
	function admin_bar_sql_big_selects_notice_assets() {
		?><style type="text/css">
			#wpadminbar #wp-admin-bar-searchwp-sql-big-selects-notice,
			#wpadminbar #wp-admin-bar-searchwp-sql-big-selects-notice > a {
				background-color:#c00 !important;
				color:#fff !important;
			}
		</style><?php
	}


	/**
	 * Output a notice in the Admin Bar so users can quickly fix issues with known fixes
	 *
	 * @since 2.3.2
	 */
	function admin_bar_sql_big_selects_notice() {
		global $wp_admin_bar;

		if ( method_exists( $wp_admin_bar, 'add_menu' ) ) {
			$args = array(
				'id'     => 'searchwp-sql-big-selects-notice',
				'title'  => __( 'SearchWP Error', 'searchwp' ),
				'href'   => 'https://searchwp.com/docs/hooks/searchwp_big_selects/',
			);

			$wp_admin_bar->add_menu( $args );

			$wp_admin_bar->add_menu( array(
				'parent'  => 'searchwp-sql-big-selects-notice',
				'id'      => 'searchwp-sql-big-selects-notice-sub',
				'title'   => __( 'View SQL_BIG_SELECTS Fix', 'searchwp' ),
				'href'    => 'https://searchwp.com/docs/hooks/searchwp_big_selects/',
			) );
		}
	}


	/**
	 * Cache the final term(s) after filtering to prevent redundant queries
	 *
	 * @param $term
	 *
	 * @since 2.3
	 */
	function cache_term_final( $term ) {
		// $term has been prepared already
		$this->terms_final = array_merge( $this->terms_final, $term );
		$this->terms_final = array_filter( $this->terms_final, 'strlen' );
		$this->terms_final = array_unique( $this->terms_final );
	}


	/**
	 * @param $term
	 *
	 * @param $postTypeWeights
	 *
	 * @return array
	 */
	function prep_term( $term, $postTypeWeights ) {
		global $wpdb;

		$original_prepped_term = $term;
		$term = function_exists( 'mb_strtolower' ) ? mb_strtolower( $term, 'UTF-8' ) : strtolower( $term );

		// determine whether we're stemming or not
		$term_or_stem = 'term';
		if ( isset( $postTypeWeights['options']['stem'] ) && ! empty( $postTypeWeights['options']['stem'] ) ) {
			// build our stem
			$term_or_stem = 'stem';
			$unstemmed = $term;
			$maybeStemmed = apply_filters( 'searchwp_custom_stemmer', $unstemmed );

			// if the term was stemmed via the filter use it, else generate our own
			$term = ( $unstemmed == $maybeStemmed ) ? $this->stemmer->stem( $term ) : $maybeStemmed;
		}

		// set up our term operator (e.g. LIKE terms or fuzzy matching)

		// since we're going to allow extending the term WHERE SQL, we need to force $term as an array
		// because in many cases with extensions it will be
		$term = array( $term );

		// let extensions filter this all day
		$term = apply_filters( 'searchwp_term_in', $term, $this->engine, $original_prepped_term );

		// prepare our terms
		if ( ! is_array( $term ) ) {
			$term = explode( ' ', $term );
		}

		if ( empty( $term ) ) {
			// if it got messed with so bad it's no longer an array, we're going to revert
			$term = array( $original_prepped_term );
		}

		$term = array_unique( $term );

		// hopefully the developer sanitized their terms, but they might have prepared them (i.e. they're wrapped in single quotes)
		foreach ( $term as $raw_term_key => $raw_term ) {
			if ( "'" == substr( $raw_term, 0, 1 ) && "'" == substr( $raw_term, strlen( $raw_term ) - 1 ) ) {
				$raw_term = substr( $raw_term, 1, strlen( $raw_term ) - 2 );
			}
			$raw_term = trim( sanitize_text_field( $raw_term ) );
			$raw_term_prepared = $wpdb->prepare( '%s', $raw_term );
			$raw_term_lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $raw_term_prepared, 'UTF-8' ) : strtolower( $raw_term_prepared );
			$term[ $raw_term_key ] = $raw_term_lower;
		}

		return array( 'term' => $term, 'term_or_stem' => $term_or_stem, 'original_prepped_term' => $original_prepped_term );
	}

	/**
	 * Related to only_full_group_by_fix_for_post_type() but runs for each term in the query
	 * and includes the COALESCED columns for
	 */
	private function only_full_group_by_fix_for_term() {
		$post_types = array();

		foreach ( $this->engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] ) {
				if ( empty( $postTypeWeights['options']['attribute_to'] ) ) {
					$post_types[] = '`' . $postType . 'weights`.`' . $postType . 'weight`';
				} else {
					$post_types[] = '`attributed' . $postType . '`.`' . $postType . 'attr`';
				}
			}
		}

		if ( empty( $post_types ) ) {
			return '';
		}

		return ' ,' . implode( ',', $post_types ) . ' ';
	}

	/**
	 * Apply a limiter based on the term stem(s)
	 *
	 * @internal param $terms
	 *
	 * @return string
	 *
	 * @since 2.3
	 */
	function query_limit_pool_by_stem() {
		global $wpdb;
		$sql = '';

		// limit the full pool to search term(s) stem
		if ( is_array( $this->terms_final ) && count( $this->terms_final ) ) {

			// if stemming was enabled, the terms have already been stemmed
			$limiter_sql = " ( {$this->db_prefix}terms.term IN (" . implode( ',', $this->terms_final ) . ") OR {$this->db_prefix}terms.stem IN (" . implode( ',', $this->terms_final ) . ') ) ';

			// if attribution is concerned, the post_parent likely WILL NOT have the term or stem, so we need to accommodate
			// by adding a conditional that excuses attributed post types that do not have any terms/stems
			$post_types_with_attribution = $this->maybe_attribution_anywhere();

			if ( is_array( $post_types_with_attribution['post_types'] ) ) {
				foreach ( $post_types_with_attribution['post_types'] as $key => $val ) {
					$post_types_with_attribution['post_types'][ $key ] = $wpdb->prepare( '%s', $val );
				}
			}

			if ( ! empty( $post_types_with_attribution['post_types'] ) ) {
				$limiter_sql .= " OR ( {$wpdb->posts}.post_type NOT IN (" . implode( ',', $post_types_with_attribution['post_types'] ) . ') ) ';

				// if we can also allow specific post IDs, do that
				if ( count( $post_types_with_attribution['post_ids'] ) ) {
					$attributed_post_ids = array_map( 'absint', $post_types_with_attribution['post_ids'] );
					$limiter_sql .= " OR {$wpdb->posts}.ID IN (" . implode( ',', $attributed_post_ids ) . ') ';
				}
			}

			// let it rip
			$this->sql .= ' AND ( ' . $limiter_sql . ' ) ';
		}

		return $sql;
	}

	/**
	 * Retrieve post IDs of parents
	 *
	 * @since 2.6.1
	 * @return array
	 */
	function get_attributed_parent_ids() {
		global $wpdb;

		// if attribution is concerned, the post_parent likely WILL NOT have the term or stem, so we need to accommodate
		// by adding a conditional that excuses attributed post types that do not have any terms/stems
		$post_types_with_attribution = $this->maybe_attribution_anywhere();

		if ( is_array( $post_types_with_attribution['post_types'] ) ) {
			foreach ( $post_types_with_attribution['post_types'] as $key => $val ) {
				$post_types_with_attribution['post_types'][ $key ] = $wpdb->prepare( '%s', $val );
			}
		}

		// by default $post_types_with_attribution['post_ids'] stores any specific attribution target IDs
		$post_types_with_attribution['post_ids'] = array_map( 'absint', $post_types_with_attribution['post_ids'] );

		// if an entire post type needs attribution...
		if ( ! empty( $post_types_with_attribution['post_types'] ) ) {

			// this has potential to be a performance nightmare because we are essentially looking for *anything* in the
			// entire index that is not an attributed post type (because it can't be) and that it also has neither a term
			// nor a stem (else it would show up as a result of the main query limiter) so let's try to reduce that pool
			// by grabbing the parent IDs of posts that do have the term(s) and using those as attribution IDs

			// grab post_parent of all attributed post types that DO have the search phrases
			$attributed_post_parents_sql = "
				SELECT DISTINCT {$wpdb->posts}.post_parent
				FROM {$wpdb->posts}
				LEFT JOIN {$this->db_prefix}index ON {$this->db_prefix}index.post_id = wp_posts.ID
				LEFT JOIN {$this->db_prefix}terms ON {$this->db_prefix}terms.id = {$this->db_prefix}index.term
				WHERE {$wpdb->posts}.post_parent > 0
				AND (
					{$this->db_prefix}terms.term IN (" . implode( ',', $this->terms_final ) . ")
					OR {$this->db_prefix}terms.stem IN (" . implode( ',', $this->terms_final ) . ")
				)
				AND {$wpdb->posts}.post_type IN (" . implode( ',', $post_types_with_attribution['post_types'] ) . ')';

			$attributed_post_parent_ids = $wpdb->get_col( $attributed_post_parents_sql );
			$attributed_post_parent_ids = array_map( 'absint', $attributed_post_parent_ids );

			// if we found IDs merge them to any single attributed IDs
			if ( count( $attributed_post_parent_ids ) ) {
				$post_types_with_attribution['post_ids'] = array_merge( $post_types_with_attribution['post_ids'], $attributed_post_parent_ids );
			}
		}

		// make sure we have all ints
		$post_types_with_attribution['post_ids'] = array_map( 'absint', $post_types_with_attribution['post_ids'] );
		$post_types_with_attribution['post_ids'] = array_unique( $post_types_with_attribution['post_ids'] );

		return $post_types_with_attribution['post_ids'];
	}


	/**
	 * Generate the SQL used to limit the results pool as much as possible while considering enabled post types
	 *
	 * @param $engineSettings array The engine settings from the SearchWP settings
	 *
	 * @return string
	 */
	private function post_status_limiter_sql( $engineSettings ) {
		global $wpdb;

		$prefix = $wpdb->prefix;
		$sql    = '';

		// add more limiting
		$finalPostTypes = array();
		$finalPostTypesIncludesAttachments = false;
		foreach ( $engineSettings as $postType => $postTypeWeights ) {
			if ( isset( $postTypeWeights['enabled'] ) && true == $postTypeWeights['enabled'] ) {
				if ( 'attachment' == $postType ) {
					$finalPostTypesIncludesAttachments = true;
				} else {
					$finalPostTypes[] = $postType;
				}
			}
		}

		$sql .= ' AND ( ';

		// based on whether attachments are the ONLY enabled post type, we'll build out this statement
		if ( ! empty( $finalPostTypes ) ) {

			$post_statuses = $this->post_statuses;
			if ( is_array( $post_statuses ) ) {
				foreach ( $post_statuses as $key => $val ) {
					$post_statuses[ $key ] = $wpdb->prepare( '%s', $val );
				}
			}

			if ( is_array( $finalPostTypes ) ) {
				foreach ( $finalPostTypes as $key => $val ) {
					$finalPostTypes[ $key ] = $wpdb->prepare( '%s', $val );
				}
			}

			$sql .= " ( {$prefix}posts.post_status IN (" . implode( ',', $post_statuses ) . ")  AND {$prefix}posts.post_type IN (" . implode( ',', $finalPostTypes ) . ') ) ';

			// this OR should be put in place only if there are other enabled post types, else the limiter will get picked up 6 lines down
			if ( $finalPostTypesIncludesAttachments ) {
				$sql .= ' OR ';
			}
		}

		if ( $finalPostTypesIncludesAttachments ) {
			$sql .= "{$prefix}posts.post_type = 'attachment' ";
		}

		$sql .= ' ) ';

		return $sql;
	}


	/**
	 * Returns the maximum number of pages of results
	 *
	 * @return int The total number of pages
	 * @since 1.0.5
	 */
	function get_max_num_pages() {
		return $this->maxNumPages;
	}


	/**
	 * Returns the number of found posts
	 *
	 * @return int The total number of posts
	 * @since 1.0.5
	 */
	function get_found_posts() {
		return $this->foundPosts;
	}


	/**
	 * Returns the number of the current page of results
	 *
	 * @return int The current page
	 * @since 1.0.5
	 */
	function get_page() {
		return $this->page;
	}

	/**
	 * Returns the SQL being used to get search results
	 *
	 * @return string The SQL in use
	 * @since 2.6
	 */
	function get_sql() {
		return $this->sql;
	}

	// @codingStandardsIgnoreStart
	/**
	 * @deprecated as of 2.5.7
	 */
	function queryForPostIDs() {
		return $this->query_for_post_ids();
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	function postStatusLimiterSQL( $settings ) {
		return $this->post_status_limiter_sql( $settings );
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function getMaxNumPages() {
		return $this->get_max_num_pages();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function getFoundPosts() {
		return $this->get_found_posts();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function getPage() {
		return $this->get_page();
	}
	// @codingStandardsIgnoreEnd

}
