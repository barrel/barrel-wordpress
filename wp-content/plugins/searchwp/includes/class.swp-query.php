<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SWP_Query
 */
class SWP_Query {

	/**
	 * Search query
	 *
	 * @since 2.6
	 * @access public
	 * @var string
	 */
	public $s;

	/**
	 * Engine to use
	 *
	 * @since 2.6
	 * @access public
	 * @var array
	 */
	public $engine;

	/**
	 * Pagination limiter
	 *
	 * @since 2.6
	 * @access public
	 * @var int
	 */
	public $posts_per_page = 10;

	/**
	 * Whether to load post objects (vs. IDs only)
	 *
	 * @since 2.6
	 * @access public
	 * @var bool
	 */
	public $load_posts = true;

	/**
	 * Whether to load post objects (vs. IDs only)
	 *
	 * @since 2.6.2
	 * @access public
	 * @var string
	 */
	public $fields = 'all';

	/**
	 * Whether to use paging
	 *
	 * @since 2.6
	 * @access public
	 * @var bool
	 */
	public $nopaging = false;

	/**
	 * The page of results to display
	 *
	 * @since 2.6
	 * @access public
	 * @var int
	 */
	public $paged = 1;

	/**
	 * Post type(s) limiter
	 *
	 * @since 2.6.2
	 * @access public
	 * @var array
	 */
	public $post_type = array();

	/**
	 * Results pool limiter
	 *
	 * @since 2.6
	 * @access public
	 * @var array
	 */
	public $post__in = array();

	/**
	 * Results pool exclusions
	 *
	 * @since 2.6
	 * @access public
	 * @var array
	 */
	public $post__not_in = array();

	/**
	 * Taxonomy query, as passed to get_tax_sql()
	 *
	 * @since 2.6
	 * @access public
	 * @var object WP_Tax_Query
	 */
	public $tax_query = array();

	/**
	 * Metadata query container
	 *
	 * @since 2.6
	 * @access public
	 * @var object WP_Meta_Query
	 */
	public $meta_query = array();

	/**
	 * Date query container
	 *
	 * @since 2.6
	 * @access public
	 * @var object WP_Date_Query
	 */
	public $date_query = false;

	/**
	 * List of posts
	 *
	 * @since 2.6
	 * @access public
	 * @var array
	 */
	public $posts;

	/**
	 * List of weights for returned posts
	 *
	 * @since 2.6
	 * @access public
	 * @var array
	 */
	public $posts_weights;

	/**
	 * The amount of posts for the current query
	 *
	 * @since 2.6
	 * @access public
	 * @var int
	 */
	public $post_count = 0;

	/**
	 * The amount of found posts for the current query
	 *
	 * If limit clause was not used, equals $post_count
	 *
	 * @since 2.6
	 * @access public
	 * @var int
	 */
	public $found_posts = 0;

	/**
	 * The amount of pages
	 *
	 * @since 2.6
	 * @access public
	 * @var int
	 */
	public $max_num_pages = 0;

	/**
	 * The SQL used to generate search results
	 *
	 * @since 2.6
	 * @access public
	 * @var string
	 */
	public $request;

	/**
	 * Constructor; fires the search, results are stored in the posts property
	 *
	 * @since 2.6
	 *
	 * @param array $args
	 */
	function __construct( $args = array() ) {

		$defaults = array(
			's'                 => '',
			'engine'            => 'default',
			'posts_per_page'    => intval( get_option( 'posts_per_page' ) ),
			'load_posts'        => true,
			'fields'            => 'all',
			'nopaging'          => false,
			'page'              => null,
			'paged'             => 1,
			'post__in'          => array(),
			'post__not_in'      => array(),
			'post_type'         => array(),
			'tax_query'         => array(),
			'meta_query'        => array(),
			'date_query'        => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		// maybe disable paging via nopaging arg
		if ( $args['nopaging'] ) {
			$args['posts_per_page'] = -1;
		}

		// support for fields argument
		if ( 'ids' === $args['fields'] ) {
			$args['load_posts'] = false;
		}

		// WP_Query uses 'paged' so give that precedence
		if ( ! is_null( $args['page'] ) && is_numeric( $args['page'] ) ) {
			$args['paged'] = intval( $args['page'] );
		}

		// set up properties based on arguments
		$args = apply_filters( 'searchwp_swp_query_args', $args );
		if ( is_array( $args ) ) {
			foreach ( $args as $property => $val ) {
				$this->__set( $property, $val );
			}
		}

		// prep the query based on args
		$this->maybe_post_type();
		$this->maybe_post__in();
		$this->maybe_post__not_in();
		$this->maybe_tax_query();
		$this->maybe_meta_query();
		$this->maybe_date_query();

		// retrieve the results
		$this->get_search_results();
	}

	/**
	 * Implementation of get_posts() method for consistency; fires a search and returns posts
	 *
	 * @since 2.6
	 *
	 * @return array
	 */
	function get_posts() {
		$this->get_search_results();

		return $this->posts;
	}

	/**
	 * Magic getter
	 *
	 * @since 2.6
	 *
	 * @param $property
	 *
	 * @return null
	 */
	public function __get( $property ) {
		if ( property_exists( $this, $property ) ) {
			return $this->$property;
		}

		return null;
	}

	/**
	 * Magic setter
	 *
	 * @since 2.6
	 *
	 * @param $property
	 * @param $value
	 *
	 * @return $this
	 */
	public function __set( $property, $value ) {
		if ( property_exists( $this, $property ) ) {
			$this->$property = $value;
		}

		return $this;
	}

	/**
	 * Support post_type argument which allows limiting to a post type
	 * and in doing so overriding the submitted engine settings
	 *
	 * @since 2.6.2
	 */
	function maybe_post_type() {
		if ( empty( $this->post_type ) ) {
			return;
		}

		$this->post_type = (array) $this->post_type;
		add_filter( 'searchwp_engine_settings_' . $this->engine, array( $this, 'set_post_types' ) );

		// clean up once this query runs
		add_action( 'searchwp_swp_query_shutdown', array( $this, 'unset_post_type' ) );
	}

	/**
	 * Callback to unset engine settings customization
	 *
	 * @since 2.7.1
	 */
	function unset_post_type() {
		remove_filter( 'searchwp_engine_settings_' . $this->engine, array( $this, 'set_post_types' ) );
	}

	/**
	 * Callback for post_type argument that ensures only passed post types are enabled in the current engine config
	 *
	 * @param $engine_settings
	 *
	 * @return mixed
	 * @since 2.6.2
	 */
	function set_post_types( $engine_settings ) {
		foreach ( $engine_settings as $post_type => $post_type_settings ) {

			// account for (i.e. skip) the search engine label storage
			if ( 'searchwp_engine_label' === $post_type ) {
				continue;
			}

			$engine_settings[ $post_type ]['enabled'] = in_array( $post_type, $this->post_type, true );
		}

		return $engine_settings;
	}

	/**
	 * Support post__in argument which allows limitation of potential results
	 * pool either by array of post IDs or by string of comma separated post IDs
	 *
	 * @since 2.6
	 */
	function maybe_post__in() {
		if ( empty( $this->post__in ) ) {
			return;
		}

		if ( ! is_array( $this->post__in ) ) {
			$this->post__in = SWP()->get_integer_csv_string_from_string_or_array( $this->post__in );
		}

		// make sure they're all ints
		$this->post__in = array_map( 'absint', $this->post__in );

		// remove invalid IDs
		$this->post__in = array_filter( $this->post__in );

		add_filter( 'searchwp_include', array( $this, 'searchwp_include' ), 10, 3 );

		// clean up once this query runs
		add_action( 'searchwp_swp_query_shutdown', array( $this, 'unset_include' ) );
	}

	/**
	 * Callback for searchwp_include filter which was triggered by the post__in parameter
	 *
	 * @since 2.6
	 *
	 * @param $ids
	 * @param $engine
	 * @param $terms
	 *
	 * @return array
	 */
	function searchwp_include( $ids, $engine, /** @noinspection PhpUnusedParameterInspection */ $terms ) {
		if ( $this->engine === $engine && ! empty( $this->post__in ) && is_array( $this->post__in ) ) {

			// Assume post__in overrides other filters expicitly
			if ( apply_filters( 'searchwp_swp_query_post__in_explicit', true ) ) {
				$ids = $this->post__in;
			} else {
				$ids = array_merge( $ids, $this->post__in );
				$ids = array_unique( $ids );
			}

		}

		return $ids;
	}

	/**
	 * Callback to unset searchwp_include hook put in place
	 *
	 * @since 2.7.1
	 */
	function unset_include() {
		remove_filter( 'searchwp_include', array( $this, 'searchwp_include' ), 10 );
	}

	/**
	 * Support post__not_in argument which allows exclusion of potential results
	 * pool either by array of post IDs or by string of comma separated post IDs
	 *
	 * @since 2.6
	 */
	function maybe_post__not_in() {
		if ( empty( $this->post__not_in ) ) {
			return;
		}

		if ( ! is_array( $this->post__not_in ) ) {
			$this->post__not_in = SWP()->get_integer_csv_string_from_string_or_array( $this->post__not_in );
		}

		// make sure they're all ints
		$this->post__not_in = array_map( 'absint', $this->post__not_in );

		// remove invalid IDs
		$this->post__not_in = array_filter( $this->post__not_in );

		add_filter( 'searchwp_exclude', array( $this, 'searchwp_exclude' ), 10, 3 );

		// clean up once this query runs
		add_action( 'searchwp_swp_query_shutdown', array( $this, 'unset_exclude' ) );
	}

	/**
	 * Callback for searchwp_exclude filter which was triggered by the post__not_in parameter
	 *
	 * @since 2.6
	 *
	 * @param $ids
	 * @param $engine
	 * @param $terms
	 *
	 * @return array
	 */
	function searchwp_exclude( $ids, $engine, /** @noinspection PhpUnusedParameterInspection */ $terms ) {
		if ( $this->engine === $engine && ! empty( $this->post__not_in ) && is_array( $this->post__not_in ) ) {

			// Assume post__not_in overrides other filters expicitly
			if ( apply_filters( 'searchwp_swp_query_post__not_in_explicit', true ) ) {
				$ids = $this->post__not_in;
			} else {
				$ids = array_merge( $ids, $this->post__not_in );
				$ids = array_unique( $ids );
			}

		}

		return $ids;
	}

	/**
	 * Callback to remove searchwp_exclude hook
	 *
	 * @since 2.7.1
	 */
	function unset_exclude() {
		remove_filter( 'searchwp_exclude', array( $this, 'searchwp_exclude' ), 10 );
	}

	/**
	 * Convert tax_query to something SearchWP understands
	 *
	 * @since 2.6
	 */
	function maybe_tax_query() {
		if ( empty( $this->tax_query ) || ! is_array( $this->tax_query ) ) {
			return;
		}

		// we need to first tap into the main JOIN of the SearchWP main algorithm query
		add_filter( 'searchwp_query_main_join', array( $this, 'tax_query' ), 10, 2 );

		// we also need to utilize the main WHERE of the the SearchWP main algorithm query
		add_filter( 'searchwp_where', array( $this, 'tax_where' ), 10, 2 );

		// clean up once this query runs
		add_action( 'searchwp_swp_query_shutdown', array( $this, 'unset_tax_query' ) );
	}

	/**
	 * Let WP_Query generate the SQL we need for our tax query limiter, we can use it in
	 * the main SearchWP algorithm
	 *
	 * @since 2.6
	 *
	 * @param $sql
	 * @param $engine
	 *
	 * @return string
	 */
	function tax_query( $sql, $engine ) {
		if ( $engine !== $this->engine || empty( $this->tax_query ) || ! is_array( $this->tax_query ) ) {
			return $sql;
		}

		global $wpdb;

		$tax_query = new WP_Tax_Query( (array) $this->tax_query );

		$tq_sql = $tax_query->get_sql(
			$wpdb->posts,
			'ID'
		);

		return $sql . $tq_sql['join'];
	}

	/**
	 * Inject the tax_query SQL in SearchWP's WHERE
	 *
	 * @since 2.6
	 *
	 * @param $sql
	 * @param $engine
	 *
	 * @return string
	 */
	function tax_where( $sql, $engine ) {
		if ( $engine !== $this->engine || empty( $this->tax_query ) || ! is_array( $this->tax_query ) ) {
			return $sql;
		}

		global $wpdb;

		$tax_query = new WP_Tax_Query( (array) $this->tax_query );

		$tq_sql = $tax_query->get_sql(
			$wpdb->posts,
			'ID'
		);

		return $sql . $tq_sql['where'];
	}

	/**
	 * Callback to unset the tax_query hooks
	 *
	 * @since 2.7.1
	 */
	function unset_tax_query() {
		remove_filter( 'searchwp_query_main_join', array( $this, 'tax_query' ), 10 );
		remove_filter( 'searchwp_where', array( $this, 'tax_where' ), 10 );
	}

	/**
	 * Convert meta_query to something SearchWP understands
	 *
	 * @since 2.6
	 */
	function maybe_meta_query() {
		if ( empty( $this->meta_query ) || ! is_array( $this->meta_query ) ) {
			return;
		}

		// we need to first tap into the main JOIN of the SearchWP main algorithm query
		add_filter( 'searchwp_query_main_join', array( $this, 'meta_query' ), 10, 2 );

		// we also need to utilize the main WHERE of the the SearchWP main algorithm query
		add_filter( 'searchwp_where', array( $this, 'meta_where' ), 10, 2 );

		// clean up once this query runs
		add_action( 'searchwp_swp_query_shutdown', array( $this, 'unset_meta_query' ) );
	}

	/**
	 * Let WP_Query generate the SQL we need for our meta query limiter, we can use it in
	 * the main SearchWP algorithm
	 *
	 * @since 2.6
	 *
	 * @param $sql
	 * @param $engine
	 *
	 * @return string
	 */
	function meta_query( $sql, $engine ) {
		if ( $engine !== $this->engine || empty( $this->meta_query ) || ! is_array( $this->meta_query ) ) {
			return $sql;
		}

		global $wpdb;

		$meta_query = new WP_Meta_Query( $this->meta_query );

		$mq_sql = $meta_query->get_sql(
			'post',
			$wpdb->posts,
			'ID',
			null
		);

		return $sql . $mq_sql['join'];
	}

	/**
	 * Inject the meta_query SQL in SearchWP's WHERE
	 *
	 * @since 2.6
	 *
	 * @param $sql
	 * @param $engine
	 *
	 * @return string
	 */
	function meta_where( $sql, $engine ) {
		if ( $engine !== $this->engine || empty( $this->meta_query ) || ! is_array( $this->meta_query ) ) {
			return $sql;
		}

		global $wpdb;

		$meta_query = new WP_Meta_Query( $this->meta_query );

		$mq_sql = $meta_query->get_sql(
			'post',
			$wpdb->posts,
			'ID',
			null
		);

		return $sql . $mq_sql['where'];
	}

	/**
	 * Callback to unset the meta_query hooks
	 *
	 * @since 2.7.1
	 */
	function unset_meta_query() {
		remove_filter( 'searchwp_query_main_join', array( $this, 'meta_query' ), 10 );
		remove_filter( 'searchwp_where', array( $this, 'meta_where' ), 10 );
	}

	/**
	 * Convert date_query to something SearchWP understands
	 *
	 * @since 2.6
	 */
	function maybe_date_query() {
		if ( empty( $this->date_query ) || ! is_array( $this->date_query ) ) {
			return;
		}

		// we need to utilize the main WHERE of the the SearchWP main algorithm query
		add_filter( 'searchwp_where', array( $this, 'date_where' ), 10, 2 );

		// clean up once this query runs
		add_action( 'searchwp_swp_query_shutdown', array( $this, 'unset_date_query' ) );
	}

	/**
	 * Inject the date_query SQL in SearchWP's WHERE
	 *
	 * @since 2.6
	 *
	 * @param $sql
	 * @param $engine
	 *
	 * @return string
	 */
	function date_where( $sql, $engine ) {
		if ( $engine !== $this->engine || empty( $this->date_query ) || ! is_array( $this->date_query ) ) {
			return $sql;
		}

		$date_query = new WP_Date_Query( (array) $this->date_query );

		$dq_sql = $date_query->get_sql();

		return $sql . $dq_sql;
	}

	/**
	 * Callback to unset the date query hook
	 *
	 * @since 2.7.1
	 */
	function unset_date_query() {
		remove_filter( 'searchwp_where', array( $this, 'date_where' ), 10 );
	}

	/**
	 * Retrieve the number of posts per page to return
	 *
	 * @since 2.6
	 *
	 * @return int Number of posts per page
	 */
	function get_posts_per_page() {
		return intval( $this->posts_per_page );
	}

	/**
	 * Retrieve search results from SearchWP
	 *
	 * @since 2.6
	 */
	function get_search_results() {

		$swp_query = SWP();

		add_filter( 'searchwp_posts_per_page', array( $this, 'get_posts_per_page' ) );

		if ( ! $this->load_posts ) {
			add_filter( 'searchwp_load_posts', '__return_false' );
		}

		$this->posts = $swp_query->search( $this->engine, $this->s, $this->paged );

		// store the SQL used to get this results set
		$this->request = $swp_query->get_last_search_sql();

		$this->found_posts      = intval( $swp_query->foundPosts );
		$this->post_count       = count( $this->posts );
		$this->max_num_pages    = intval( $swp_query->maxNumPages );
		$this->posts_weights    = $swp_query->results_weights;

		if ( empty( $this->posts ) || 0 === count( $this->posts ) ) {
			$this->found_posts      = 0;
			$this->post_count       = 0;
			$this->max_num_pages    = 0;
			$this->posts_weights    = array();
		}

		remove_filter( 'searchwp_posts_per_page', array( $this, 'get_posts_per_page' ) );

		if ( ! $this->load_posts ) {
			remove_filter( 'searchwp_load_posts', '__return_false' );
		}

		do_action( 'searchwp_swp_query_shutdown' );
	}

}
