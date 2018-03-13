<?php

global $wp_filesystem;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @noinspection PhpIncludeInspection */
include_once ABSPATH . 'wp-admin/includes/file.php';

/**
 * Class SearchWPIndexer is responsible for generating the search index
 */
class SearchWPIndexer {

	/**
	 * @var object Stores post object during indexing
	 * @since 1.0
	 */
	private $post;

	/**
	 * @var bool Whether there are posts left to index
	 * @since 1.0
	 */
	private $unindexedPosts = false;

	/**
	 * @var int The maximum weight for a single term
	 * @since 1.0
	 */
	private /** @noinspection PhpUnusedPrivateFieldInspection */
		$weightLimit = 500;

	/**
	 * @var bool Whether the indexer should index numbers
	 * @since 1.0
	 */
	private /** @noinspection PhpUnusedPrivateFieldInspection */
		$indexNumbers = false;

	/**
	 * @var int Internal counter
	 * @since 1.0
	 */
	private /** @noinspection PhpUnusedPrivateFieldInspection */
		$count = 0;

	/**
	 * @var array Common words
	 * @since 1.0
	 */
	private $common = array();

	/**
	 * @var int Maximum number of times we should try to index a post
	 */
	private $maxAttemptsToIndex = 2;

	/**
	 * @var bool Whether to index Attachments at all
	 */
	private $indexAttachments = false;

	/**
	 * @var array Character entities as specified by Ando Saabas in Sphider http://www.sphider.eu/
	 * @since 1.0
	 */
	private /** @noinspection PhpUnusedPrivateFieldInspection */
		$entities = array(
			'&amp' => '&', '&apos' => "'", '&THORN;' => 'Þ', '&szlig;' => 'ß', '&agrave;' => 'à', '&aacute;' => 'á',
			'&acirc;' => 'â', '&atilde;' => 'ã', '&auml;' => 'ä', '&aring;' => 'å', '&aelig;' => 'æ', '&ccedil;' => 'ç',
			'&egrave;' => 'è', '&eacute;' => 'é', '&ecirc;' => 'ê', '&euml;' => 'ë', '&igrave;' => 'ì', '&iacute;' => 'í',
			'&icirc;' => 'î', '&iuml;' => 'ï', '&eth;' => 'ð', '&ntilde;' => 'ñ', '&ograve;' => 'ò', '&oacute;' => 'ó',
			'&ocirc;' => 'ô', '&otilde;' => 'õ', '&ouml;' => 'ö', '&oslash;' => 'ø', '&ugrave;' => 'ù', '&uacute;' => 'ú',
			'&ucirc;' => 'û', '&uuml;' => 'ü', '&yacute;' => 'ý', '&thorn;' => 'þ', '&yuml;' => 'ÿ',
			'&Agrave;' => 'à', '&Aacute;' => 'á', '&Acirc;' => 'â', '&Atilde;' => 'ã', '&Auml;' => 'ä',
			'&Aring;' => 'å', '&Aelig;' => 'æ', '&Ccedil;' => 'ç', '&Egrave;' => 'è', '&Eacute;' => 'é', '&Ecirc;' => 'ê',
			'&Euml;' => 'ë', '&Igrave;' => 'ì', '&Iacute;' => 'í', '&Icirc;' => 'î', '&Iuml;' => 'ï', '&ETH;' => 'ð',
			'&Ntilde;' => 'ñ', '&Ograve;' => 'ò', '&Oacute;' => 'ó', '&Ocirc;' => 'ô', '&Otilde;' => 'õ', '&Ouml;' => 'ö',
			'&Oslash;' => 'ø', '&Ugrave;' => 'ù', '&Uacute;' => 'ú', '&Ucirc;' => 'û', '&Uuml;' => 'ü', '&Yacute;' => 'ý',
			'&Yhorn;' => 'þ', '&Yuml;' => 'ÿ',
		);

	/**
	 * @var array Post IDs to forcibly exclude from indexing process
	 */
	private $excludeFromIndex = array();

	/**
	 * @var array|string post type(s) to include when indexing
	 */
	private $postTypesToIndex = 'any';


	/**
	 * @var string|array post status(es) to include when indexing
	 *
	 * @since 1.6.10
	 */
	private $post_statuses = 'publish';


	/**
	 * @var int The maximum length of a term, as defined by the database schema
	 *
	 * @since 1.8.4
	 */
	private $max_term_length = 80;


	/**
	 * @var bool Whether SearchWP will also keep track of accent-less versions of accented terms when indexing
	 *           which allows for 'lazy' searches without accents to show accented results
	 */
	private $lenient_accents = false;


	/**
	 * @var string The indexer validation hash
	 */
	public $hash;

	/**
	 * @var int Threshold (in characters) to trigger big data handling
	 */
	private $big_data_trigger = 10000;


	/**
	 * Constructor
	 *
	 * @param string $hash The key used to validate instantiation
	 * @since 1.0
	 */
	public function __construct( $hash = '' ) {

		$searchwp = SWP();

		do_action( 'searchwp_indexer_pre' );

		$this->init();

		if ( empty( $this->postTypesToIndex ) && empty( $this->indexAttachments ) ) {
			return;
		}

		// If there are no initial settings, there's nothing to do
		$initial_settings = searchwp_get_setting( 'initial_settings' );
		if ( empty( $initial_settings ) ) {
			return;
		}

		// make sure we've got a valid request to index
		wp_cache_delete( 'searchwp_transient', 'options' );
		if ( get_option( 'searchwp_transient' ) !== $hash ) {
			if ( ! empty( $hash ) ) {
				do_action( 'searchwp_log', 'Invalid index request ' . $hash );
			} else {
				do_action( 'searchwp_log', 'External SearchWPIndexer instantiation' );
			}
		} else {

			/**
			 * Allow for some catch-up from the last request
			 */

			// auto-throttle based on load
			$waitTime = 1;
			$waiting = false;

			if ( apply_filters( 'searchwp_indexer_load_monitoring', true ) && function_exists( 'sys_getloadavg' ) ) {
				$load = sys_getloadavg();
				$loadThreshold = abs( apply_filters( 'searchwp_load_maximum', 2 ) );

				// if the load has breached the threshold, scale the wait time
				if ( $load[0] > $loadThreshold ) {
					$waiting = true;
					$waitTime = 4 * floor( $load[0] );
					do_action( 'searchwp_log', 'Load threshold (' . $loadThreshold . ') has been breached! Current load: ' . $load[0] . '. Automatically injecting a wait time of ' . $waitTime );

					// this flag is going to prevent the indexer from jumpstarting which could very well trigger parallel indexers
					searchwp_update_option( 'waiting', true );
				}
			}

			// allow developers to throttle the indexer
			$waitTime = absint( apply_filters( 'searchwp_indexer_throttle', $waitTime ) );
			$iniMaxExecutionTime = absint( ini_get( 'max_execution_time' ) ) - 5;
			if ( $iniMaxExecutionTime < 10 ) {
				$iniMaxExecutionTime = 10;
			}
			if ( $waitTime > $iniMaxExecutionTime ) {
				do_action( 'searchwp_log', 'Requested throttle of ' . $waitTime . 's exceeds max execution time, forcing ' . $iniMaxExecutionTime . 's' );
				$waitTime = $iniMaxExecutionTime;
			}

			$memoryUse = size_format( memory_get_usage() );
			do_action( 'searchwp_log', 'Memory usage: ' . $memoryUse . ' - sleeping for ' . $waitTime . 's' );

			if ( 1 === $waitTime ) {
				// wait time was not adjusted, so we're just going to usleep because 1 second is an eternity
				usleep( 750000 );
			} else {
				sleep( $waitTime );
			}

			if ( $waiting ) {
				searchwp_update_option( 'waiting', false );
			}

			// see if the indexer has stalled
			searchwp_check_for_stalled_indexer();

			// check to see if indexer is already running
			$running = searchwp_get_setting( 'running' );
			if ( empty( $running ) ) {
				do_action( 'searchwp_log', 'Indexer NOW RUNNING' );

				searchwp_set_setting( 'last_activity', current_time( 'timestamp' ), 'stats' );
				searchwp_set_setting( 'running', true );

				do_action( 'searchwp_indexer_running' );

				if ( apply_filters( 'searchwp_remove_pre_get_posts', true ) ) {
					remove_all_actions( 'pre_get_posts' );
					remove_all_filters( 'pre_get_posts' );
				}

				$this->update_running_counts();

				if ( false !== $this->find_unindexed_posts() ) {

					do_action( 'searchwp_indexer_posts' );

					$start_time = time();

					// index this chunk of posts
					$this->index();

					$index_time = time() - $start_time;

					// clean up
					do_action( 'searchwp_log', 'Indexing chunk complete: ' . $index_time . 's' );

					searchwp_set_setting( 'running', false );
					searchwp_set_setting( 'in_process', false, 'stats' );
					searchwp_update_option( 'busy', false );

					// reset the transient
					$this->hash = sprintf( '%.22F', microtime( true ) ); // inspired by $doing_wp_cron
					update_option( 'searchwp_transient', $this->hash, 'no' );

					$destination = esc_url_raw( $searchwp->endpoint . '?swpnonce=' . $this->hash );

					do_action( 'searchwp_log', 'Request index (internal loopback) ' . $destination );

					$timeout = abs( apply_filters( 'searchwp_timeout', 0.02 ) );

					// recursive trigger
					$args = array(
						'body'        => array( 'swpnonce' => $this->hash ),
						'blocking'    => false,
						'user-agent'  => 'SearchWP',
						'timeout'     => $timeout,
						'sslverify'   => false,
					);
					$args = apply_filters( 'searchwp_indexer_loopback_args', $args );
					do_action( 'searchwp_indexer_loopback', $args );

					if ( ! apply_filters( 'searchwp_alternate_indexer', false ) ) {
						wp_remote_post( $destination, $args );
					}
				} else {
					do_action( 'searchwp_log', 'Nothing left to index' );
					do_action( 'searchwp_index_up_to_date' );
					$initial = searchwp_get_setting( 'initial_index_built' );
					if ( empty( $initial ) ) {
						wp_clear_scheduled_hook( 'swp_indexer' ); // clear out the pre-initial-index cron event
						do_action( 'searchwp_log', 'Initial index complete' );
						searchwp_set_setting( 'initial_index_built', true );
						do_action( 'searchwp_index_initial_complete' );
					}
					searchwp_set_setting( 'running', false );
					searchwp_set_setting( 'in_process', false, 'stats' );
					searchwp_update_option( 'busy', false );

					// delta updates may have been triggered, so now that the initial index has been built we can process them
					$purge_queue = searchwp_get_option( 'purge_queue' );
					if ( ! empty( $purge_queue ) ) {
						$timeout = abs( apply_filters( 'searchwp_timeout', 0.02 ) );

						// we don't need a hash because the purge queue is checked per request
						$destination = esc_url_raw( $searchwp->endpoint . '?swpdeltas=swpdeltas&' . sprintf( '%.22F', microtime( true ) ) );

						// recursive trigger
						$args = array(
							'body'        => array( 'swpdeltas' => 'swpdeltas' ),
							'blocking'    => false,
							'user-agent'  => 'SearchWP',
							'timeout'     => $timeout,
							'sslverify'   => false,
						);
						$args = apply_filters( 'searchwp_indexer_loopback_args', $args );

						do_action( 'searchwp_indexer_loopback', $args );

						wp_remote_post( $destination, $args );
					}
				}
			} else {
				do_action( 'searchwp_log', 'SHORT CIRCUIT: Indexer already running' );
			}
		}
	}

	function init() {
		// init
		$this->common = SWP()->common;

		// by default let's only grab 'enabled' post types across the board (so as to keep the index size at a minimum)
		$this->postTypesToIndex = SWP()->get_enabled_post_types_across_all_engines();

		$this->big_data_trigger = absint( apply_filters( 'searchwp_term_count_limit', $this->big_data_trigger ) );

		$this->lenient_accents = apply_filters( 'searchwp_leinant_accents', $this->lenient_accents ); // deprecated
		$this->lenient_accents = apply_filters( 'searchwp_lenient_accents', $this->lenient_accents );

		// dynamically decide whether we're going to index Attachments based on whether Media is enabled for any search engine
		$index_attachments_from_settings = false;
		if ( in_array( 'attachment', $this->postTypesToIndex, true ) ) {
			$index_attachments_from_settings = true;
		}

		// allow dev to completely disable indexing of Attachments to save indexing time
		$this->indexAttachments = apply_filters( 'searchwp_index_attachments', $index_attachments_from_settings );
		if ( ! is_bool( $this->indexAttachments ) ) {
			$this->indexAttachments = false;
		}

		// allow dev to customize post statuses are included
		$this->post_statuses = (array) apply_filters( 'searchwp_post_statuses', $this->post_statuses, null );
		foreach ( $this->post_statuses as $post_status_key => $post_status_value ) {
			$this->post_statuses[ $post_status_key ] = sanitize_key( $post_status_value );
		}

		// allow dev to forcefully omit posts from being indexed
		$this->excludeFromIndex = apply_filters( 'searchwp_prevent_indexing', array() );
		if ( ! is_array( $this->excludeFromIndex ) ) {
			$this->excludeFromIndex = array();
		}

		// UPDATE @since 2.9.0 the indexer is even more restricted in that there are
		// taxonomy limiters in place that can either exclude or limit to taxonomy
		// terms. There's no reason to index posts that are excluded, and we can reduce
		// the size of the index when limiters are in play, so we'll do that
		if ( apply_filters( 'searchwp_indexer_apply_engines_rules', true ) ) {
			$post__not_in = SWP()->get_post__not_in_across_all_engines( $this->excludeFromIndex );
			if ( ! empty( $post__not_in ) ) {
				$this->excludeFromIndex = array_merge( $this->excludeFromIndex, $post__not_in );
			}

			$this->excludeFromIndex = apply_filters( 'searchwp_indexer_excluded_by_rules', $this->excludeFromIndex );
		}

		$this->excludeFromIndex = array_map( 'absint', $this->excludeFromIndex );
		$this->excludeFromIndex = array_unique( $this->excludeFromIndex );

		// allow dev to forcefully omit post types that would normally be indexed
		$this->postTypesToIndex = apply_filters( 'searchwp_indexed_post_types', $this->postTypesToIndex );

		// attachments cannot be included here, to omit attachments use the searchwp_index_attachments filter
		// so we have to check to make sure attachments were not included
		if ( is_array( $this->postTypesToIndex ) ) {
			foreach ( $this->postTypesToIndex as $key => $postType ) {
				$post_type_lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $postType, 'UTF-8' ) : strtolower( $postType );
				if ( 'attachment' === $post_type_lower ) {
					unset( $this->postTypesToIndex[ $key ] );
				}
			}
		} elseif ( 'attachment' === strtolower( $this->postTypesToIndex ) ) {
			$this->postTypesToIndex = 'any';
		}
	}

	/**
	 * Retrieve the number of rows in the main index table
	 *
	 * @return int The number of rows in the main index table
	 */
	public function get_main_table_row_count() {
		global $wpdb;

		$index_table = $wpdb->prefix . SEARCHWP_DBPREFIX . 'index';
		/** @noinspection SqlDialectInspection */
		$row_count = $wpdb->get_var( "SELECT COUNT(id) FROM {$index_table}" );

		return absint( $row_count );
	}


	/**
	 * Determine the number of posts left to index, total post count, and how many posts have been indexed already
	 *
	 * @since 1.0
	 */
	function update_running_counts() {
		$total = intval( $this->count_total_posts() );
		$indexed = intval( $this->indexed_count() );

		// edge case: if an index was performed and attachments indexed, then the user decides to disable
		// the indexing of attachments, the indexed count could potentially be greater than the total
		if ( $indexed > $total ) {
			$indexed = $total;
		}

		$remaining = intval( $total - $indexed );

		searchwp_set_setting( 'total', $total, 'stats' );
		searchwp_set_setting( 'remaining', $remaining, 'stats' );
		searchwp_set_setting( 'done', $indexed, 'stats' );

		$percent_progress = ( $total > 0 ) ? ( ( $total - $remaining ) / $total ) * 100 : 0;
		$percent_progress = number_format( $percent_progress, 2, '.', '' );
		searchwp_update_option( 'progress', $percent_progress );

		do_action( 'searchwp_log', 'Updating counts: ' . $total . ' ' . $remaining . ' ' . $indexed );

		if ( $remaining < 1 ) {
			do_action( 'searchwp_log', 'Setting initial' );
			searchwp_set_setting( 'initial_index_built', true );
		}
	}


	/**
	 * Sets post property
	 *
	 * @param $post object WordPress Post object
	 * @since 1.0
	 */
	function set_post( $post ) {
		$this->post = apply_filters( 'searchwp_pre_set_post', $post );

		// append Custom Field data
		$this->post->custom = get_post_custom( $post->ID );

		// roll our own maybe_unserialize_deep
		foreach ( $this->post->custom as $meta_key => $meta_value ) {
			if ( is_array( $meta_value ) ) {
				foreach ( $meta_value as $key => $val ) {
					$meta_value[ $key ] = maybe_unserialize( $val );
				}
			}
			$this->post->custom[ $meta_key ] = $meta_value;
		}

		// allow dev the option to parse Shortcodes
		if ( apply_filters( 'searchwp_do_shortcode', false, $post, 'post_content', false ) ) {
			$this->post->post_content = do_shortcode( $this->post->post_content );
		}
		if ( ! empty( $this->post->custom ) ) {
			foreach ( $this->post->custom as $post_custom_key => $post_custom_value ) {
				if ( apply_filters( 'searchwp_do_shortcode', false, $post, 'custom_field', $post_custom_key ) ) {
					$this->post->custom[ $post_custom_key ] = $this->do_shortcode_deep( $post_custom_value );
				}
			}
		}

		// allow developer the ability to manually manipulate the post content or Custom Field data
		$this->post = apply_filters( 'searchwp_set_post', $this->post );
	}

	/**
	 * Process potential Shortcodes
	 *
	 * @since 2.6.2
	 *
	 * @param $content
	 *
	 * @return string
	 */
	function do_shortcode_deep( $content ) {

		if ( is_array( $content ) ) {
			foreach ( $content as $key => $val ) {
				$content[ $key ] = $this->do_shortcode_deep( $val );
			}
		} else {
			$content = do_shortcode( $content );
		}

		return $content;
	}


	/**
	 * Count the total number of posts in this WordPress installation
	 *
	 * @return int Total number of posts
	 * @since 1.0
	 */
	function count_total_posts() {

		$total_posts = 0;

		if ( ! empty( $this->indexAttachments ) && ! in_array( 'attachment', $this->postTypesToIndex ) ) {
			$this->postTypesToIndex[] = 'attachment';
		}

		if ( empty( $this->postTypesToIndex ) ) {
			return $total_posts;
		}

		foreach ( $this->postTypesToIndex as $post_type ) {
			$args = array(
				'posts_per_page'    => 1,
				'post_type'         => $post_type,
				'post_status'       => $post_type === 'attachment' ? 'inherit' : $this->post_statuses,
				'post__not_in'      => $this->excludeFromIndex,
				'suppress_filters'  => true,
				'cache_results'     => false,
				'meta_query'        => array(
					array(
						'key'           => '_' . SEARCHWP_PREFIX . 'skip',
						'value'         => '',	// only want media that hasn't failed indexing multiple times
						'compare'       => 'NOT EXISTS',
						'type'          => 'BINARY',
					),
				),
			);

			// See note in find_unindexed_posts about 2.9.0
			if ( apply_filters( 'searchwp_indexer_apply_engines_rules', true ) ) {
				$args['tax_query'] = SWP()->get_post_type_tax_query_for_rules( $post_type, 'limit_to' );
			}

			// allow devs to have more control over what is considered unindexed
			if ( 'attachment' !== $post_type ) {
				$args = apply_filters( 'searchwp_indexer_unindexed_args', $args );
			} else {
				// Apply mime type exclusions if applicable
				$limited_mime_types = $this->get_global_mime_limit();

				if ( ! empty( $limited_mime_types ) ) {
					$args['post_mime_type'] = $limited_mime_types;
				}

				$args = apply_filters( 'searchwp_indexer_unindexed_media_args', $args );
			}

			$total_post_type_ref = new WP_Query( $args );
			$total_post_type = absint( $total_post_type_ref->found_posts );

			$total_posts += $total_post_type;
		}

		return $total_posts;
	}


	/**
	 * Count the number of posts that have been indexed
	 *
	 * @return int Number of posts that have been indexed
	 * @since 1.0
	 */
	function indexed_count() {

		$postTypesToCount = $this->postTypesToIndex;

		if ( $this->indexAttachments && ! in_array( 'attachment', $this->postTypesToIndex ) ) {
			$postTypesToCount[] = 'attachment';
		}

		$args = array(
			'posts_per_page'    => 1,
			'post_type'         => $postTypesToCount,
			'post_status'       => $this->post_statuses,
			'suppress_filters'  => true,
			'cache_results'     => false,
			'meta_query'        => array(
				'relation'          => 'AND',
				array(
					'key'           => '_' . SEARCHWP_PREFIX . 'last_index',
					'compare'       => 'EXISTS',
					'type'          => 'NUMERIC',
				),
				array(
					'key'           => '_' . SEARCHWP_PREFIX . 'skip',
					'value'         => '',	// only want media that hasn't failed indexing multiple times
					'compare'       => 'NOT EXISTS',
					'type'          => 'BINARY',
				)
			),
			// TODO: should we include 'exclude_from_search' for accuracy?
		);

		if ( $this->indexAttachments ) {
			$args['post_status'] = 'any';
		}

		$indexed = new WP_Query( $args );

		return absint( $indexed->found_posts );
	}


	/**
	 * Query for posts that have not been indexed yet
	 *
	 * @return array|bool Posts (max 10) that have yet to be indexed
	 * @since 1.0
	 */
	function find_unindexed_posts() {

		$indexChunk = apply_filters( 'searchwp_index_chunk_size', 10 );

		// Media will be done last
		if ( ! empty( $this->indexAttachments ) && ! in_array( 'attachment', $this->postTypesToIndex ) ) {
			$this->postTypesToIndex[] = 'attachment';
		}

		if ( empty( $this->postTypesToIndex ) ) {
			return false;
		}

		foreach ( $this->postTypesToIndex as $post_type ) {

			$args = array(
				'posts_per_page'    => intval( $indexChunk ),
				'post_type'         => $post_type,
				'post_status'       => $post_type === 'attachment' ? 'inherit' : $this->post_statuses,
				'post__not_in'      => $this->excludeFromIndex,
				'suppress_filters'  => true,
				'cache_results'     => false,
				'no_found_rows'     => true,
				'meta_query'        => array(
					'relation'      => 'AND',
					array(
						'key'         => '_' . SEARCHWP_PREFIX . 'last_index',
						'value'       => '',	// http://core.trac.wordpress.org/ticket/23268
						'compare'     => 'NOT EXISTS',
						'type'        => 'NUMERIC',
					),
					array( // only want media that hasn't failed indexing multiple times
						'key'         => '_' . SEARCHWP_PREFIX . 'skip',
						'compare'     => 'NOT EXISTS',
						'type'        => 'BINARY',
					),
					array( // if a PDF was flagged during indexing, we don't want to keep trying
						'key'         => '_' . SEARCHWP_PREFIX . 'review',
						'compare'     => 'NOT EXISTS',
						'type'        => 'BINARY',
					),
				),
			);

			// TODO if searching in the admin is enabled, should we exclude anything? what if
			// site owner wants unrestricted search in the admin, but restricted on the front end?

			// @since 2.9.0 the index can be limited to certain taxonomy terms which could
			// greatly reduce the overall index size in certain circumstances but this requires that we
			// iterate through each post type to deterine what should be indexed
			if ( apply_filters( 'searchwp_indexer_apply_engines_rules', true ) ) {
				$args['tax_query'] = SWP()->get_post_type_tax_query_for_rules( $post_type, 'limit_to' );
			}

			// allow devs to have more control over what is considered unindexed
			if ( 'attachment' !== $post_type ) {
				$args = apply_filters( 'searchwp_indexer_unindexed_args', $args );
			} else {
				// Apply mime type exclusions if applicable
				$limited_mime_types = $this->get_global_mime_limit();

				if ( ! empty( $limited_mime_types ) ) {
					$args['post_mime_type'] = $limited_mime_types;
				}

				$args = apply_filters( 'searchwp_indexer_unindexed_media_args', $args );
			}

			$unindexedPosts = get_posts( $args );

			if ( ! empty( $unindexedPosts ) ) {
				$this->unindexedPosts = $unindexedPosts;
				break;
			}
		}

		return $this->unindexedPosts;
	}

	function get_global_mime_limit() {
		$limit = array();

		$engines = isset( SWP()->settings['engines'] ) ? SWP()->settings['engines'] : array();

		if ( empty( $engines ) ) {
			return $limit;
		}

		$index_all_mimes = false;

		foreach ( $engines as $engine => $engine_settings ) {
			foreach ( $engine_settings as $post_type => $post_type_settings ) {
				if ( ! isset( $post_type_settings['enabled'] ) || empty( $post_type_settings['enabled'] ) ) {
					continue;
				}

				if ( 'attachment' !== $post_type ) {
					continue;
				}

				$mimes_for_this_engine = isset( $post_type_settings['options']['mimes'] ) ? $post_type_settings['options']['mimes'] : '';
				$mimes_for_this_engine_string = (string) $mimes_for_this_engine;

				// This check is a bit strange because the All Documents mime group is represented by string '0'
				if ( empty( $mimes_for_this_engine ) && '' === trim( $mimes_for_this_engine_string ) ) {
					// If there are no limiters we have to index all mime types
					$index_all_mimes = true;
					break;
				}

				// Store these mime limits for this engine, because we need GLOBAL rules
				// in other words if there are multiple engines but mime limits only on one engine,
				// we cannot limit the mime type in the index because the other engine will be missing results
				$limit[ $engine ] = $mimes_for_this_engine;
			}

			if ( ! empty( $index_all_mimes ) ) {
				break;
			}
		}

		// If at some point we determined that all mimes need to be index, bail out
		if ( ! empty( $index_all_mimes ) ) {
			return array();
		}

		// We need to find GLOBAL mime limits across all engines
		// So we'll be mashing all of the engine mime limits together
		$global_limit = array();
		foreach ( $limit as $engine => $mime_ids ) {
			if ( false !== strpos( $mime_ids, ',' ) ) {
				$mime_ids = explode( ',', $mime_ids );
			} else {
				$mime_ids = array( $mime_ids );
			}

			$global_limit = array_merge( $global_limit, $mime_ids );
		}

		$global_limit = array_map( 'absint', $global_limit );
		$global_limit = array_unique( $global_limit );

		// This query arg needs the actual mime type(s), not the IDs SearchWP uses in its settings
		$global_limit = SWP()->get_mimes_from_settings_ids( $global_limit );

		return $global_limit;
	}

	/**
	 * Checks the stored in-process post IDs and existing index to ensure a rogue parallel indexer is not running
	 *
	 * @since 1.9
	 */
	function check_for_parallel_indexer() {
		global $wpdb;

		if ( is_array( $this->unindexedPosts ) && count( $this->unindexedPosts ) ) {
			// prevent parallel indexers
			$ids_to_index = array();
			foreach ( $this->unindexedPosts as $unindexed_post ) {
				$ids_to_index[] = (int) $unindexed_post->ID;
			}
			reset( $this->unindexedPosts );

			// check what's in process *right now*
			$in_process = searchwp_get_setting( 'in_process', 'stats' );
			if ( is_array( $in_process ) ) {
				$in_process = array_intersect( $ids_to_index, $in_process );
			}

			// check the index too
			$ids_to_index = array_map( 'absint', $ids_to_index );
			$ids_to_index_sql = implode( ',', $ids_to_index );
			$index_table = $wpdb->prefix . SEARCHWP_DBPREFIX . 'index';
			$ids_to_index_sql = "SELECT post_id FROM {$index_table} WHERE post_id IN ({$ids_to_index_sql}) GROUP BY post_id LIMIT 100";
			$already_indexed = $wpdb->get_col( $ids_to_index_sql );
			$already_indexed = array_map( 'absint', $already_indexed );

			// if it's in the index, force the indexed flag
			if ( is_array( $already_indexed ) && ! empty( $already_indexed ) ) {
				foreach ( $already_indexed as $already_indexed_key => $already_indexed_id ) {
					do_action( 'searchwp_log', (int) $already_indexed_id . ' is already in the index' );

					// if we're not dealing with a term queue, mark this post as indexed
					if ( ! get_post_meta( (int) $already_indexed_id, '_' . SEARCHWP_PREFIX . 'terms', true ) ) {
						update_post_meta( (int) $already_indexed_id, '_' . SEARCHWP_PREFIX . 'last_index', current_time( 'timestamp' ) );
					} else {
						// this is a term chunk update, not a conflict
						unset( $already_indexed[ $already_indexed_key ] );
					}
				}
			}

			// combine the two results so we have one collection of conflicts
			$conflicts = is_array( $in_process ) ? array_values( array_merge( (array) $in_process, (array) $already_indexed ) ) : (array) $already_indexed;

			if ( ! empty( $conflicts ) ) {
				do_action( 'searchwp_log', 'Parallel indexer detected when attempting to index: ' . implode( ', ', $conflicts ) );
				die();
			}

			searchwp_set_setting( 'in_process', $ids_to_index, 'stats' );
		}
	}

	/**
	 * Extract PDF meta (PHP 5.3+)
	 *
	 * @deprecated in version 2.8
	 *
	 * @param $post_id
	 *
	 * @return array
	 */
	function extract_pdf_metadata( $post_id ) {
		$parser = new SearchWPDocumentParser( $post_id );
		return $parser->extract_pdf_metadata( $post_id );
	}

	/**
	 * Extract plain text from PDF
	 *
	 * @deprecated in version 2.8
	 *
	 * @param $post_id
	 *
	 * @return string
	 */
	function extract_pdf_text( $post_id ) {
		$parser = new SearchWPDocumentParser( $post_id );
		return $parser->extract_pdf_text( $post_id );
	}

	/**
	 * Index posts stored in $this->unindexedPosts
	 *
	 * @since 1.0
	 */
	function index() {
		$this->check_for_parallel_indexer();

		if ( is_array( $this->unindexedPosts ) && count( $this->unindexedPosts ) ) {

			do_action( 'searchwp_indexer_pre_chunk', $this->unindexedPosts );

			// all of the IDs to index have not been indexed, proceed with indexing them
			while ( ( $unindexedPost = current( $this->unindexedPosts ) ) !== false ) {
				$this->set_post( $unindexedPost );

				// log the attempt
				$count = get_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'attempts', true );
				if ( false === $count ) {
					$count = 0;
				} else {
					$count = intval( $count );
				}

				$count++;

				// increment our counter to prevent the indexer getting stuck on a gigantic PDF
				update_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'attempts', $count );
				do_action( 'searchwp_log', 'Attempt ' . $count . ' at indexing ' . $this->post->ID );

				// if we breached the maximum number of attempts, flag it to skip
				$this->maxAttemptsToIndex = absint( apply_filters( 'searchwp_max_index_attempts', $this->maxAttemptsToIndex ) );
				if ( intval( $count ) > $this->maxAttemptsToIndex ) {

					do_action( 'searchwp_log', 'Too many indexing attempts on ' . $this->post->ID . ' (' . $this->maxAttemptsToIndex . ') - skipping' );
					// flag it to be skipped
					update_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'skip', true );

				} else {

					// check to see if we're running a second pass on terms
					$termCache = false;
					$term_cache_chunks = get_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'terms', false );

					// the term cache is chunked in case of big data so put it back together
					if ( is_array( $term_cache_chunks ) && ! empty( $term_cache_chunks ) ) {
						$termCache = array();
						foreach ( $term_cache_chunks as $term_cache_chunk ) {
							$termCache = array_merge( $termCache, $term_cache_chunk );
						}
					}

					if ( ! is_array( $termCache ) ) {

						do_action( 'searchwp_index_post', $this->post );

						// if it's an attachment, we want the permalink
						$slug = 'attachment' === $this->post->post_type ? str_replace( get_bloginfo( 'wpurl' ), '', get_permalink( $this->post->ID ) ) : '';

						// we allow users to override the extracted content from documents, if they have done so this flag is set
						$skipDocProcessing = get_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'skip_doc_processing', true );
						$omitDocProcessing = apply_filters( 'searchwp_omit_document_processing', false );

						// storage
						$pdf_metadata = '';

						if ( 'attachment' === $this->post->post_type && ! $skipDocProcessing && ! $omitDocProcessing ) {

							$parser = new SearchWPDocumentParser( $this->post->ID );

							// Check for existing document content in case this is an index rebuilt and the PDF
							// parsing already happened, we can use that here instead and save the trouble
							$document_content = get_post_meta( $this->post->ID, SEARCHWP_PREFIX . 'content', true );

							if ( empty( $document_content ) ) {
								$document_content = $parser->extract_document_content();
							}

							if ( false === $document_content ) {
								// flag it for further review
								update_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'review', true );
								update_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'skip', true );
							} else {
								$document_content = trim( $document_content );

								if ( ! empty( $document_content ) ) {

									if ( function_exists( 'mb_convert_encoding' ) ) {

										$is_utf8 = in_array( get_option( 'blog_charset' ), array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ), true );

										if ( $is_utf8 ) {
											$document_content = mb_convert_encoding( $document_content, 'UTF-8' );
										}

									}

									$document_content = sanitize_text_field( $document_content );
									delete_post_meta( $this->post->ID, SEARCHWP_PREFIX . 'content' );
									update_post_meta( $this->post->ID, SEARCHWP_PREFIX . 'content', $document_content );
								}
							}

							// if it's a PDF, document the PDF metadata
							if ( 'application/pdf' === $this->post->post_mime_type ) {

								$pdf_metadata = $parser->extract_pdf_metadata( $this->post->ID );

								if ( false !== $pdf_metadata ) {
									// allow developers to filter the metadata
									$pdf_metadata = apply_filters( 'searchwp_pdf_metadata', $pdf_metadata, $this->post->ID );

									// allow developers to store metadata as they wish
									do_action( 'searchwp_index_pdf_metadata', $pdf_metadata, $this->post->ID );

									delete_post_meta( $this->post->ID, SEARCHWP_PREFIX . 'pdf_metadata' );
									update_post_meta( $this->post->ID, SEARCHWP_PREFIX . 'pdf_metadata', $pdf_metadata );
								}
							}
						}

						$postTerms              = array();
						$postTerms['title']     = $this->index_title();
						$postTerms['slug']      = $this->index_slug( str_replace( '/', ' ', $slug ) );
						$postTerms['content']   = $this->index_content();
						$postTerms['excerpt']   = $this->index_excerpt();

						if ( apply_filters( 'searchwp_index_comments', true ) ) {
							$postTerms['comments'] = $this->index_comments();
						}

						// index taxonomies
						$taxonomies = get_object_taxonomies( $this->post->post_type );

						// let devs filter which taxonomies should be indexed for this post
						$taxonomies = apply_filters( 'searchwp_indexer_taxonomies', $taxonomies, $this->post );

						if ( ! empty( $taxonomies ) ) {
							while ( ( $taxonomy = current( $taxonomies ) ) !== false ) {

								// if there's no weight, it's meaningless
								$used_taxonomy = SWP()->is_used_taxonomy( $taxonomy );
								if ( $used_taxonomy ) {
									$terms = get_the_terms( $this->post->ID, $taxonomy );
									$terms = apply_filters( 'searchwp_indexer_taxonomy_terms', $terms, $taxonomy, $this->post );
									if ( ! empty( $terms ) ) {
										$postTerms['taxonomy'][ $taxonomy ] = $this->index_taxonomy_terms( $taxonomy, $terms );
									}
								}

								next( $taxonomies );
							}
							reset( $taxonomies );
						}

						// index custom fields
						$customFields = apply_filters( 'searchwp_get_custom_fields', $this->post->custom, $this->post->ID );

						// if it was a PDF let's ensure that our content is in the list
						if ( ! empty( $document_content ) && is_array( $customFields ) && ! array_key_exists( 'searchwp_content', $customFields ) ) {
							$customFields['searchwp_content'] = $document_content;
						}

						if ( ! empty( $pdf_metadata ) ) {
							$customFields['searchwp_pdf_metadata'] = $pdf_metadata;
						}

						// reset document content and text content to prevent it from being used on subsequent index calls for this chunk
						$document_content = '';
						/** @noinspection PhpUnusedLocalVariableInspection */
						$pdf_metadata = '';

						if ( ! empty( $customFields ) ) {
							while ( ( $customFieldValue = current( $customFields ) ) !== false ) {
								$customFieldName = key( $customFields );

								$excluded_meta_keys = searchwp_get_excluded_meta_keys();

								// allow developers to conditionally omit specific custom fields
								$excluded_by_engine = ! SWP()->is_used_meta_key( $customFieldName, $this->post );

								// Additional processing (e.g. oembeds have their own hashed meta key)
								if ( empty( $excluded_by_engine ) && apply_filters( 'searchwp_indexer_additional_meta_exclusions', true ) ) {
									$excluded_by_engine = 0 === strpos( $customFieldName, '_oembed_' );
								}

								$omit_this_custom_field = apply_filters( 'searchwp_omit_meta_key', $excluded_by_engine, $customFieldName, $this->post );
								$omit_this_custom_field = apply_filters( "searchwp_omit_meta_key_{$customFieldName}", $omit_this_custom_field, $this->post );

								if ( ! in_array( $customFieldName, $excluded_meta_keys, true ) && ! $omit_this_custom_field ) {
									// allow devs to swap out their own content
									// e.g. parsing ACF Relationship fields (that store only post IDs) to actually retrieve that content at runtime
									$customFieldValue = apply_filters( 'searchwp_custom_fields', $customFieldValue, $customFieldName, $this->post );
									$customFieldValue = apply_filters( "searchwp_custom_field_{$customFieldName}", $customFieldValue, $this->post );
									$postTerms['customfield'][ $customFieldName ] = $this->index_custom_field( $customFieldName, $customFieldValue );
								}

								next( $customFields );
							}
							reset( $customFields );
						}

						// allow developer to store arbitrary information a la Custom Fields (without them actually being Custom Fields)
						$extraMetadata = apply_filters( 'searchwp_extra_metadata', false, $this->post );
						if ( $extraMetadata ) {
							if ( is_array( $extraMetadata ) ) {
								foreach ( $extraMetadata as $extraMetadataKey => $extraMetadataValue ) {
									// TODO: make sure there are no collisions?
									// while( isset( $postTerms['customfield'][$extraMetadataKey] ) ) {
									//    $extraMetadataKey .= '_';
									// }
									$postTerms['customfield'][ $extraMetadataKey ] = $this->index_custom_field( $extraMetadataKey, $extraMetadataValue );
								}
							}
						}

						// we need to break out the terms from all of this content
						$termCountBreakout = array();

						if ( is_array( $postTerms ) && count( $postTerms ) ) {
							foreach ( $postTerms as $type => $terms ) {
								switch ( $type ) {
									case 'title':
									case 'slug':
									case 'content':
									case 'excerpt':
									case 'comments':
										if ( is_array( $terms ) && count( $terms ) ) {
											foreach ( $terms as $term ) {

												$term_id = '_' . md5( $term['term'] );

												// make sure the array has a key for this term
												if ( ! isset( $termCountBreakout[ $term_id ] ) ) {
													$termCountBreakout[ $term_id ] = array(
														'term' => $term['term'],
														'counts' => array(),
													);
												}

												// make sure the counts array for this term has a key for this type
												if ( ! isset( $termCountBreakout[ $term_id ]['counts'][ $type ] ) ) {
													$termCountBreakout[ $term_id ]['counts'][ $type ] = array();
												}

												// add the counts for this term for this type
												$termCountBreakout[ $term_id ]['counts'][ $type ] = absint( $term['count'] );
											}
										}
										break;

									case 'taxonomy':
									case 'customfield':
										if ( is_array( $terms ) && count( $terms ) ) {
											foreach ( $terms as $name => $nameTerms ) {
												if ( is_array( $nameTerms ) && count( $nameTerms ) ) {
													foreach ( $nameTerms as $nameTerm ) {

														$term_id = '_' . md5( $nameTerm['term'] );

														// make sure the array has a key for this term
														if ( ! isset( $termCountBreakout[ $term_id ] ) ) {
															$termCountBreakout[ $term_id ] = array(
																'term' => $nameTerm['term'],
																'counts' => array(),
															);
														}

														// make sure the counts array for this term has a key for this type
														if ( ! isset( $termCountBreakout[ $term_id ]['counts'][ $type ] ) ) {
															$termCountBreakout[ $term_id ]['counts'][ $type ] = array();
														}

														// make sure the type key has an array for the name
														if ( ! isset( $termCountBreakout[ $term_id ]['counts'][ $type ][ $name ] ) ) {
															$termCountBreakout[ $term_id ]['counts'][ $type ][ $name ] = array();
														}

														// add the counts for this term for this type
														$termCountBreakout[ $term_id ]['counts'][ $type ][ $name ] = absint( $nameTerm['count'] );
													}
												}
											}
										}
										break;

								}
							}
						}
					} else {
						$termCountBreakout = $termCache;

						// if there was a term cache, this repeated processing doesn't count, so decrement it
						delete_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'attempts' );
						delete_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'skip' );
					}

					// unless the term chunk limit says otherwise, we're going to flag this as being OK to log as indexed
					$flagAsIndexed = true;

					// we now have a multidimensional array of terms with counts per type in $termCountBreakout
					// if the term count is huge, we need to split up this process so as to avoid
					// hitting upper PHP execution time limits (term insertion is heavy), so we'll chunk the array of terms

					$termChunkMax = 500;

					// try to set a better default based on php.ini's memory_limit
					$memoryLimit = ini_get( 'memory_limit' );
					if ( preg_match( '/^(\d+)(.)$/', $memoryLimit, $matches ) ) {
						if ( 'M' === $matches[2] ) {
							$termChunkMax = ( (int) $matches[1] ) * 7;  // 7 terms per MB RAM
						} else {
							// memory was set in K...
							$termChunkMax = 100;
						}
					}

					$termChunkLimit = apply_filters( 'searchwp_process_term_limit', $termChunkMax );

					if ( count( $termCountBreakout ) > $termChunkLimit ) {
						$acceptableTermCountBreakout = array_slice( $termCountBreakout, 0, $termChunkLimit, true );

						// if we haven't pulled all of the terms, we can't consider this post indexed...
						if ( $termChunkLimit < count( $termCountBreakout ) - 1 ) {
							$flagAsIndexed = false;

							// save the term breakout so we don't have to do it again
							$remainingTerms = array_slice( $termCountBreakout, $termChunkLimit, null, true );

							// we could be dealing with big data (i.e. parsed document) so we need to chunk
							// the array of remaining terms as well, else we hit limits in update_post_meta()
							$remaining_terms_chunks = array_chunk( $remainingTerms, $termChunkLimit, true );
							unset( $remainingTerms );

							// clear out any existing cache
							delete_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'terms' );

							// add our chunks
							foreach ( $remaining_terms_chunks as $key => $remaining_terms_chunk ) {
								add_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'terms', $remaining_terms_chunk );
								unset( $remaining_terms_chunks[ $key ] );
							}
						}

						// set the acceptable breakout as the main breakout
						$termCountBreakout = $acceptableTermCountBreakout;
						unset( $acceptableTermCountBreakout );
					}

					// there's a chance that all of the terms were filtered out and if there
					// is nothing to index this post would never be flagged to skip resulting
					// in an endless indexer loop
					if ( ! empty( $termCountBreakout ) ) {
						$terms_recorded = $this->record_post_terms( $termCountBreakout );

						unset( $termCountBreakout );

						// flag the post as indexed
						if ( $flagAsIndexed ) {
							// clean up our stored term array if necessary
							if ( $termCache ) {
								delete_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'terms' );
							}

							// clean up the attempt counter
							delete_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'attempts' );
							delete_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'skip' );

							// flag as indexed (if terms were successfully indexed)
							if ( false !== $terms_recorded ) {
								update_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'last_index', current_time( 'timestamp' ) );
							}
						}
					} else {
						// there were no terms so we need to skip this post by flagging it as indexed
						delete_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'attempts' );
						delete_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'skip' );
						update_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'last_index', current_time( 'timestamp' ) );
					}
				}
				next( $this->unindexedPosts );
			}
			reset( $this->unindexedPosts );

			do_action( 'searchwp_indexer_post_chunk' );
		}
	}


	/**
	 * Insert an array of terms into the terms table and retrieve all term IDs from submitted terms
	 *
	 * @since 1.0
	 *
	 * @param array $termsArray
	 *
	 * @return array
	 */
	function pre_process_terms( $termsArray = array() ) {
		global $wpdb;

		if ( ! is_array( $termsArray ) || empty( $termsArray ) ) {
			return array();
		}

		// get our database vars prepped
		$termsTable = $wpdb->prefix . SEARCHWP_DBPREFIX . 'terms';

		$stemmer = new SearchWPStemmer();

		$terms = $newTerms = $newTermsSQL = array();

		while ( ( $counts = current( $termsArray ) ) !== false ) {
			$termToAdd = (string) $counts['term'];

			// WordPress 4.2 added emoji support which caused problems for the array storage
			// of terms and their term counts since the terms themselves were array keys
			// and PHP doesn't allow emoji in array keys so the array keys were switched to
			// an underscore-prefixed md5 value and the term stored within that

			// generate the reverse (UTF-8)
			preg_match_all( '/./us', $termToAdd, $contentr );
			$revTerm = join( '', array_reverse( $contentr[0] ) );

			// find the stem
			$unstemmed = $termToAdd;
			$maybeStemmed = apply_filters( 'searchwp_custom_stemmer', $unstemmed );

			// if the term was stemmed via the filter use it, else generate our own
			$stem = ( $unstemmed === $maybeStemmed ) ? $stemmer->stem( $termToAdd ) : $maybeStemmed;

			// store the record
			$terms[] = $wpdb->prepare( '%s', $termToAdd );
			$newTermsSQL[] = '(%s,%s,%s)';
			$newTerms = array_merge( $newTerms, array( $termToAdd, $revTerm, $stem ) );
			next( $termsArray );
		}
		reset( $termsArray );

		// insert all of the terms into the terms table so each gets an ID
		$attemptCount = 1;
		$maxAttempts = absint( apply_filters( 'searchwp_indexer_max_attempts', 4 ) ) + 1;  // try to recover 5 times

		$insert_sql = $wpdb->prepare( "INSERT IGNORE INTO {$termsTable} (term,reverse,stem) VALUES " . implode( ',', $newTermsSQL ), $newTerms );

		$insert_result = $wpdb->query( $insert_sql );

		while ( ( is_wp_error( $insert_result ) || false === $insert_result ) && $attemptCount < $maxAttempts ) {
			// sometimes a deadlock can happen, wait a second then try again
			do_action( 'searchwp_log', 'INSERT Deadlock ' . $attemptCount . '/' . $maxAttempts );
			sleep( 3 );
			$attemptCount++;

			// try the insert again
			$insert_result = $wpdb->query( $insert_sql );
		}

		// deadlocking could be a red herring, there's a remote chance the database table
		// doesn't even exist, so we need to handle that
		if ( ( is_wp_error( $insert_result ) || false === $insert_result ) ) {
			do_action( 'searchwp_log', 'Post failed indexing, flagging ' . $this->post->ID );

			// this will call out this post as problematic in the WP admin
			update_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'attempts', absint( $this->maxAttemptsToIndex ) + 1 );
			update_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'skip', true );
			delete_post_meta( $this->post->ID, '_' . SEARCHWP_PREFIX . 'last_index' );

			die(); // this is only an issue if there was a catastrophic problem (e.g. database tables didn't exist)

		} elseif ( $attemptCount > 1 ) {
			do_action( 'searchwp_log', 'Recovered from Deadlock at ' . $attemptCount . '/' . $maxAttempts );
		}

		// retrieve IDs for all terms
		$terms_sql = "-- noinspection SqlDialectInspection
					SELECT id, term FROM {$termsTable} WHERE term IN( " . implode( ',', $terms ) . ' )';  // already prepared earlier in this method
		$termIDs = $wpdb->get_results( $terms_sql, 'OBJECT_K' );

		// match term IDs to original terms with counts
		if ( is_array( $termIDs ) ) {
			while ( ( $termIDMeta = current( $termIDs ) ) !== false ) {

				/** @noinspection PhpUnusedLocalVariableInspection */
				$termID = key( $termIDs );

				// append the term ID to the original $termsArray
				while ( ( $counts = current( $termsArray ) ) !== false ) {
					$termsArrayTerm = (string) $counts['term'];
					if ( $termsArrayTerm === $termIDMeta->term ) {
						$term_id = '_' . md5( $termIDMeta->term );
						if ( isset( $termIDMeta->id ) ) {
							$termsArray[ $term_id ]['id'] = absint( $termIDMeta->id );
						}
						break;
					}
					next( $termsArray );
				}
				reset( $termsArray );
				next( $termIDs );
			}
			reset( $termIDs );
		}

		return $termsArray;
	}


	/**
	 * Insert terms with counts into the database
	 *
	 * @param array $termsArray The terms to insert
	 * @return bool Whether the insert was successful
	 * @since 1.0
	 */
	function record_post_terms( $termsArray = array() ) {
		global $wpdb;

		if ( ! is_array( $termsArray ) || empty( $termsArray ) ) {
			return false;
		}

		$success = true;	// track whether or not the database insert went okay

		// get our database vars prepped
		$termsTable = $wpdb->prefix . SEARCHWP_DBPREFIX . 'terms';

		// retrieve IDs for all terms
		$termsArray = $this->pre_process_terms( $termsArray );

		if ( empty( $termsArray ) ) {
			// something went quite wrong
			return false;
		}

		// storage in prep for bulk INSERTs
		$indexTerms       = $indexTermsSQL        = array();
		$customFieldTerms = $customFieldTermsSQL  = array();
		$taxonomyTerms    = $taxonomyTermsSQL     = array();

		// insert terms into index
		while ( ( $term = current( $termsArray ) ) !== false ) {
			$key = trim( (string) $term['term'] );

			if ( ! empty( $term ) && ! empty ( $key ) ) {

				// if an ID is somehow missing, grab it
				// TODO: determine if this is still (ever) an issue
				if ( ! isset( $term['id'] ) ) {
					/** @noinspection SqlDialectInspection */
					$term['id'] = $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM ' . $termsTable . ' WHERE term = %s', $key ) );
				}

				$termID = isset( $term['id'] ) ? absint( $term['id'] ) : 0;

				// insert the counts for our standard fields
				$indexTermsSQL[] = '(%d,%d,%d,%d,%d,%d,%d)';
				$indexTerms = array_merge( $indexTerms, array(
					$termID,
					isset( $term['counts']['content'] )  ? absint( $term['counts']['content'] )     : 0,
					isset( $term['counts']['title'] )    ? absint( $term['counts']['title'] )       : 0,
					isset( $term['counts']['comments'] ) ? absint( $term['counts']['comments'] )    : 0,
					isset( $term['counts']['excerpt'] )  ? absint( $term['counts']['excerpt'] )     : 0,
					isset( $term['counts']['slug'] )     ? absint( $term['counts']['slug'] )        : 0,
					absint( $this->post->ID ),
				) );

				// insert our custom field counts
				if ( isset( $term['counts']['customfield'] ) && is_array( $term['counts']['customfield'] ) && count( $term['counts']['customfield'] ) ) {

					while ( ( $customFieldCount = current( $term['counts']['customfield'] ) ) !== false ) {
						$customField = key( $term['counts']['customfield'] );
						$customFieldTermsSQL[] = '(%s,%d,%d,%d)';
						$customFieldTerms = array_merge( $customFieldTerms, array(
							$customField,
							isset( $term['id'] ) ? absint( $term['id'] ) : 0,
							absint( $customFieldCount ),
							absint( $this->post->ID ),
						) );
						next( $term['counts']['customfield'] );
					}
					reset( $term['counts']['customfield'] );
				}

				// index our taxonomy counts
				if ( isset( $term['counts']['taxonomy'] ) && is_array( $term['counts']['taxonomy'] ) && count( $term['counts']['taxonomy'] ) ) {
					while ( ( $taxonomyCount = current( $term['counts']['taxonomy'] ) ) !== false ) {
						$taxonomyName = key( $term['counts']['taxonomy'] );
						$taxonomyTermsSQL[] = '(%s,%d,%d,%d)';
						$taxonomyTerms = array_merge( $taxonomyTerms, array(
							$taxonomyName,
							isset( $term['id'] ) ? absint( $term['id'] ) : 0,
							absint( $taxonomyCount ),
							absint( $this->post->ID ),
						) );
						next( $term['counts']['taxonomy'] );
					}
					reset( $term['counts']['taxonomy'] );
				}
			}
			next( $termsArray );
		}
		reset( $termsArray );

		// INSERT index terms
		if ( ! empty( $indexTerms ) ) {
			$indexTable = $wpdb->prefix . SEARCHWP_DBPREFIX . 'index';
			$wpdb->query(
				$wpdb->prepare( "INSERT INTO {$indexTable} (term,content,title,comment,excerpt,slug,post_id) VALUES " . implode( ',', $indexTermsSQL ), $indexTerms )
			);
		}

		// INSERT custom field terms
		if ( ! empty( $customFieldTerms ) ) {
			$cfTable = $wpdb->prefix . SEARCHWP_DBPREFIX . 'cf';
			$wpdb->query(
				$wpdb->prepare( "INSERT INTO {$cfTable} (metakey,term,count,post_id) VALUES " . implode( ',', $customFieldTermsSQL ), $customFieldTerms )
			);
		}

		// INSERT taxonomy terms
		if ( ! empty( $taxonomyTerms ) ) {
			$taxTable = $wpdb->prefix . SEARCHWP_DBPREFIX . 'tax';
			$wpdb->query(
				$wpdb->prepare( "INSERT INTO {$taxTable} (taxonomy,term,count,post_id) VALUES " . implode( ',', $taxonomyTermsSQL ), $taxonomyTerms )
			);
		}

		return $success;
	}


	/**
	 * Remove accents from the submitted string
	 *
	 * @param string $string The string from which to remove accents
	 * @return string
	 * @since 1.0
	 */
	function remove_accents( $string ) {
		$original_string = $string;

		$conversions = array(
			'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ã' => 'a', 'Ä' => 'a', 'Å' => 'a', 'Æ' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a',
			'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Õ' => 'o', 'Ö' => 'o', 'Ø' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
			'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ð' => 'e',
			'Ç' => 'c', 'ç' => 'c',
			'Ð' => 'd',
			'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
			'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
			'Ñ' => 'n', 'ñ' => 'n',
			'Þ' => 't',
			'ß' => 's',
			'ÿ' => 'y', 'ý' => 'y',

			// greek
			'Ά' => 'Α', 'ά' => 'α', 'Έ' => 'Ε', 'έ' => 'ε', 'Ή' => 'Η', 'ή' => 'η', 'Ί' => 'Ι', 'ί' => 'ι', 'Ό' => 'Ο', 'ό' => 'ο', 'Ύ' => 'Υ', 'ύ' => 'υ', 'Ώ' => 'Ω', 'ώ' => 'ω', 'ϊ' => 'ι', 'ϋ' => 'υ', 'Ϊ' => 'ι', 'Ϋ' => 'Υ',
		);

		// this spelling mistake made it to release... ugh
		$conversions = apply_filters( 'searchwp_leinent_accents_conversions', $conversions );

		$string = strtr(
			$string,
			// let developers customize the conversion table
			apply_filters( 'searchwp_lenient_accents_conversions', $conversions )
		);

		// also a spelling mistake that made it to release
		$string = apply_filters( 'searchwp_leinent_accent_result', $string, $original_string );

		// let developers 'fix' an incorrect conversion
		$string = apply_filters( 'searchwp_lenient_accent_result', $string, $original_string );

		return $string;
	}


	/**
	 * Determine keyword weights for a given string. Our 'weights' are not traditional, but instead simple counts
	 * so as to facilitate changing weights on the fly and not having to reindex. Actual weights are computed at
	 * query time.
	 *
	 * @param string $string The string from which to obtain weights
	 * @return array Terms and their correlating counts
	 * @since 1.0
	 */
	function get_term_counts( $string = '' ) {

		$searchwp = SWP();
		$wordArray = array();

		if ( is_string( $string ) && ! empty( $string ) ) {

			// we need to extract whitelist matches here
			$string = ' ' . $string . ' ';  // we need front and back spaces so we can perform exact matches when whitelisting

			// extract terms based on whitelist pattern, allowing for approved indexing of terms with punctuation
			$whitelisted_terms = $searchwp->extract_terms_using_pattern_whitelist( $string );

			$string_lowercase = function_exists( 'mb_strtolower' ) ? mb_strtolower( $string, 'UTF-8' ) : strtolower( $string );
			$string = trim( $string_lowercase );

			if ( false !== strpos( $string, ' ' ) ) {
				$exploded = explode( ' ', $string );
			} else {
				$exploded = array( $string );
			}

			// append our whitelist
			if ( is_array( $whitelisted_terms ) && ! empty( $whitelisted_terms ) ) {
				$whitelisted_terms = array_map( 'trim', $whitelisted_terms );
				$whitelisted_terms = array_filter( $whitelisted_terms, 'strlen' );
				if ( ! empty( $whitelisted_terms ) ) {
					$exploded = array_merge( $exploded, $whitelisted_terms );
				}
			}

			// ensure word length obeys database schema
			if ( is_array( $exploded ) && ! empty( $exploded ) ) {
				foreach ( $exploded as $term_key => $term_term ) {
					$exploded[ $term_key ] = trim( $term_term );
					if ( strlen( $term_term ) > $this->max_term_length ) {
						// just drop it, it's useless anyway
						unset( $exploded[ $term_key ] );
					} else {
						// accommodate accent-less searches (e.g. allow accented search results with non-accented search terms)
						// this happens with WordPress taxonomy terms (WP strips them out)
						if ( $this->lenient_accents ) {
							$without_accent = $this->remove_accents( $term_term );
							$without_accent = function_exists( 'mb_strtolower' ) ? mb_strtolower( $without_accent, 'UTF-8' ) : strtolower( $without_accent );
							if ( $without_accent !== $term_term ) {
								// "duplicate" the term with this accent-less version
								$exploded[] = $without_accent;
							}
						}
					}
				}
				$exploded = array_values( $exploded );
				$wordArray = $this->get_word_count_from_array( $exploded );
			}
		}

		return $wordArray;
	}


	/**
	 * Determine a word count for the submitted array.
	 *
	 * Modified version of Sphider's unique_array() by Ando Saabas, http://www.sphider.eu/
	 *
	 * @param array $arr
	 * @return array
	 * @since 1.0
	 */
	function get_word_count_from_array( $arr = array() ) {
		$newarr = array();

		// set the minimum character length to count as a valid term
		$minLength = apply_filters( 'searchwp_minimum_word_length', 3 );

		while ( ( $term = current( $arr ) ) !== false ) {
			if ( ! in_array( $term, $this->common, true ) && ( strlen( $term ) >= absint( $minLength ) ) ) {
				$key = md5( $term );
				if ( ! isset( $newarr[ $key ] ) ) {
					$newarr[ $key ] = array(
						'term'  => sanitize_text_field( $term ),
						'count' => 1,
					);
				} else {
					$newarr[ $key ]['count'] = absint( $newarr[ $key ]['count'] ) + 1;
				}
			}
			next( $arr );
		}
		reset( $arr );

		$newarr = array_values( $newarr );

		return $newarr;
	}


	/**
	 * Retrieve only the term content from the submitted string
	 *
	 * Modified from Sphider by Ando Saabas, http://www.sphider.eu/
	 *
	 * @param string $content The source content, can include markup
	 * @return string The content without markup or character encoding
	 * @since 1.0
	 */
	function clean_content( $content = '', $skip_extra_processing = false ) {

		$searchwp = SWP();

		if ( is_array( $content ) || is_object( $content ) ) {
			$content = $this->parse_variable_for_terms( $content );
		}

		// allow developers the ability to customize content where necessary (e.g. remove TM symbols)
		$content = apply_filters( 'searchwp_indexer_pre_process_content', $content );

		if ( function_exists( 'mb_convert_encoding' ) ) {
			$content = mb_convert_encoding( $content, 'UTF-8', 'UTF-8' );
		}

		if ( empty( $searchwp->settings['utf8mb4'] ) ) {
			$content = $searchwp->replace_4_byte( $content );
		}

		// we want to extract potentially valuable content from certain HTML attributes
		$accepted_attributes = apply_filters( 'searchwp_indexer_tag_attributes', array(
			'a'     => array( 'title' ),
			'img'   => array( 'alt', 'src', 'longdesc', 'title' ),
			'input' => array( 'placeholder', 'value' ),
		) );

		// parse $content as a DOMDocument and if applicable extract the accepted attribute content
		$attribute_content = array();
		$content = trim( $content );
		if ( ! empty( $accepted_attributes )
		     && ! empty( $content )
		     && is_array( $accepted_attributes )
		     && class_exists( 'DOMDocument' )
		     && function_exists( 'libxml_use_internal_errors' )
		) {
			$dom = new DOMDocument();
			libxml_use_internal_errors( true );
			$dom->loadHTML( $content );
			// loop through our accepted tags
			foreach ( $accepted_attributes as $tag => $attributes ) {
				// grab any $tag matches
				$node_list = $dom->getElementsByTagName( $tag );
				for ( $i = 0; $i < $node_list->length; $i++ ) {
					$node = $node_list->item( $i );
					if ( $node->hasAttributes() ) {
						foreach ( $node->attributes as $attr ) {
							if ( isset( $attr->name ) && in_array( $attr->name, $attributes, true ) ) {
								$attribute_content[] = sanitize_text_field( $attr->nodeValue );
							}
						}
					}
				}
			}
		}

		// append the attribute content to our main content block
		if ( ! empty( $attribute_content ) ) {
			$content .= ' ' . implode( ' ', $attribute_content );
		}

		// we need front and back spaces so we can perform exact matches when whitelisting
		$content = ' ' . $content . ' ';  // we need front and back spaces so we can perform exact matches when whitelisting

		// extract terms based on whitelist pattern, allowing for approved indexing of terms with punctuation
		$whitelisted_terms = $searchwp->extract_terms_using_pattern_whitelist( $content );

		// when indexing we do not want to remove the matches; we're going to run everything through
		// the regular sanitization so as to open the possibility for better partial matching (especially
		// when taking into consideration the use of LIKE Terms or another extension)

		// there may be times however, that the developer does in fact want matches to be exclusively kept together
		if ( ! $skip_extra_processing && apply_filters( 'searchwp_exclusive_regex_matches', false ) && ! empty( $whitelisted_terms ) ) {

			// add the buffer the entire string so we can whole-word replace
			$content = '  ' . $content . '  ';

			// also need to buffer the whitelisted terms to prevent replacement overrun
			foreach ( $whitelisted_terms as $key => $val ) {
				$whitelisted_terms[ $key ] = ' ' . $val . ' ';
			}

			// remove the matches
			$content = str_ireplace( $whitelisted_terms, ' ', $content );

			// remove the term buffer
			$whitelisted_terms = array_map( 'trim', $whitelisted_terms );

			// clean up the double space flag we used
			$content = str_replace( '  ', ' ', $content );
		}

		// buffer tags with spaces before removing them
		$content = preg_replace ( '/<[^>]*>/', ' \\0 ', $content );

		$content = preg_replace( '/&nbsp;/', ' ', $content );

		if ( ! $skip_extra_processing ) {
			$content = function_exists( 'mb_strtolower' ) ? mb_strtolower( $content, 'UTF-8' ) : strtolower( $content );
		}

		// <br> tags can be problematic on their own if there's no whitespace surrounding
		// what should be separate lines of text, so we'll manually do that prior to stripping
		$content = str_replace( array( '<br />', '<br/>', '<br>' ), ' ', $content );

		// since we've extracted and appended the attribute content we can strip the tags entirely
		$content = strip_tags( $content );

		$content = stripslashes( $content );

		// remove punctuation
		$punctuation = array( '(', ')', '·', "'", '´', '’', '‘', '”', '“', '„', '—', '–', '×', '…', '€', '\n', '.', ',', '/', '\\', '|', '[', ']', '{', '}', '•', '`' );
		$content = str_replace( $punctuation, ' ', $content );
		$content = preg_replace( '/[[:punct:]]/uiU', ' ', $content );
		$content = preg_replace( '/[[:space:]]/uiU', ' ', $content );
		$content = preg_replace( '/\\n|\\R/uiU', ' ', $content );

		// append our whitelist
		if ( ! $skip_extra_processing && is_array( $whitelisted_terms ) && ! empty( $whitelisted_terms ) ) {
			$whitelisted_terms = array_map( 'trim', $whitelisted_terms );
			$whitelisted_terms = array_filter( $whitelisted_terms, 'strlen' );
			$content .= ' ' . implode( ' ' , $whitelisted_terms );
		}

		$content = sanitize_text_field( $content );
		$content = trim( $content );

		return $content;
	}


	/**
	 * Get the term counts for a title
	 *
	 * @param string $title The title to index
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function index_title( $title = '' ) {
		$title = ( ! is_string( $title ) || empty( $title ) ) && ! empty( $this->post->post_title ) ? $this->post->post_title : $title;
		$title = $this->clean_content( $title );

		if ( ! empty( $title ) && is_string( $title ) ) {
			return $this->get_term_counts( $title );
		} else {
			return false;
		}
	}


	/**
	 * Index the filename itself
	 *
	 * @param string $filename The filename to index
	 * @return array|bool
	 */
	function index_filename( $filename = '' ) {
		$fullFilename = explode( '.', basename( $filename ) );
		if ( isset( $fullFilename[0] ) ) {
			$filename = $fullFilename[0]; // don't care about extension
		}

		if ( ! empty( $filename ) && is_string( $filename ) ) {
			return $this->get_term_counts( $filename );
		} else {
			return false;
		}
	}


	/**
	 * Get the term counts for a filename
	 *
	 * @param string $filename The filename to index
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 * @deprecated 1.5.1
	 */
	function extract_filename_terms( $filename = '' ) {
		// try to retrieve keywords from filename, explode by '-' or '_'
		$fullFilename = explode( '.', basename( $filename ) );

		if ( isset( $fullFilename[0] ) ) {
			$fullFilename = $fullFilename[0]; // don't care about extension
		}

		// first explode by hyphen, then explode those pieces by underscore
		$filenamePieces = array();

		$filenameFirstPass = explode( '-', $fullFilename );
		if ( count( $filenameFirstPass ) > 1 ) {

			while ( ( $filenameSegment = current( $filenameFirstPass ) ) !== false ) {
				$filenamePieces[] = $filenameSegment;
				next( $filenameFirstPass );
			}
			reset( $filenameFirstPass );
		} else {
			$filenamePieces = array( $fullFilename );
		}

		while ( ( $filenamePiece = current( $filenamePieces ) ) !== false ) {
			$filenameSecondPass = explode( '-', $filenamePiece );
			if ( count( $filenameSecondPass ) > 1 ) {
				while ( ( $filenameSegment = current( $filenameSecondPass ) ) !== false ) {
					$filenamePieces[] = $filenameSegment;
					next( $filenameSecondPass );
				}
				reset( $filenameSecondPass );
			} else {
				$filenamePieces[] = $filenamePiece;
			}
			next( $filenamePieces );
		}
		reset( $filenamePieces );

		// if we found some pieces we'll put them back together, if not we'll use the original
		$filename = is_array( $filenamePieces ) ? implode( ' ', $filenamePieces ) : $filename;

		return $filename;
	}


	/**
	 * Get the term counts for a slug
	 *
	 * @param string $slug The slug to index
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function index_slug( $slug = '' ) {
		$slug = ( ! is_string( $slug ) || empty( $slug ) ) && ! empty( $this->post->post_name ) ? $this->post->post_name : $slug;
		$slug = str_replace( '-', ' ', $slug );
		$slug = $this->clean_content( $slug );

		if ( ! empty( $slug ) && is_string( $slug ) ) {
			return $this->get_term_counts( $slug );
		} else {
			return false;
		}
	}


	/**
	 * Get the term counts for a content block
	 *
	 * @param string $content The content to index
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function index_content( $content = '' ) {
		$content = ( ! is_string( $content ) || empty( $content ) ) && ! empty( $this->post->post_content ) ? $this->post->post_content : $content;

		$content = $this->clean_content( $content );

		if ( ! empty( $content ) && is_string( $content ) ) {
			return $this->get_big_data_term_count( $content );
		} else {
			return false;
		}
	}


	/**
	 * Get the term counts for a comment
	 *
	 * @return array Terms and their associated counts
	 * @since 1.0
	 */
	function index_comments() {
		// TODO: short circuit on pingback/trackback?

		// index comments
		$comments_args = array(
			'status'	=> 'approve',
			'post_id'	=> $this->post->ID,
		);

		do_action( 'searchwp_indexer_pre_get_comments' );

		$comments = get_comments( apply_filters( 'searchwp_indexer_comments_args', $comments_args ) );

		$commentTerms = array();
		if ( ! empty( $comments ) ) {
			while ( ( $comment = current( $comments ) ) !== false ) {

				$comment = apply_filters( 'searchwp_indexer_comment', $comment );

				$author   = isset( $comment->comment_author ) && ! empty( $comment->comment_author ) ? $comment->comment_author : null;
				$email    = isset( $comment->comment_author_email ) && ! empty( $comment->comment_author_email ) ? $comment->comment_author_email : null;

				$comment  = isset( $comment->comment_content ) && ! empty( $comment->comment_content ) ? $comment->comment_content : $comment;
				$comment  = $this->clean_content( $comment );

				// grab all the comment data
				$author   = ! empty( $author ) && is_string( $author ) ? $author : '';
				$email    = ! empty( $email ) && is_string( $email ) ? $email : '';
				$comment  = ! empty( $comment ) && is_string( $comment ) ? $comment : '';

				$commentTerms[] = $comment;
				unset( $comment );

				if ( apply_filters( 'searchwp_include_comment_author', false ) ) {
					$commentTerms[] = sanitize_text_field( $author );
				}

				if ( apply_filters( 'searchwp_include_comment_email', false ) ) {
					$commentTerms[] = sanitize_text_field( $email );
				}
				next( $comments );
			}
			reset( $comments );
		}

		$commentTerms = $this->get_big_data_term_count( implode( ' ', $commentTerms ) );

		return $commentTerms;
	}


	/**
	 * Index the terms within a taxonomy
	 *
	 * @param null|string $taxonomy The taxonomy name
	 * @param array $terms The terms to index
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function index_taxonomy_terms( $taxonomy = null, $terms = array() ) {
		// get just the term strings
		$cleanTerms = array();
		if ( is_array( $terms ) && ! empty( $terms ) ) {
			while ( ( $term = current( $terms ) ) !== false ) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$termsKey = key( $terms );
				$term_string_to_index = $term->name;

				$context = array(
					'SWP'       => $this,
					'taxonomy'  => $taxonomy,
					'term'      => $term,
				);

				if ( apply_filters( 'searchwp_indexer_taxonomy_term_index_slug', false, $context ) ) {
					$term_string_to_index .= ' ' . $term->slug;
				}

				$term_string_to_index = apply_filters( 'searchwp_indexer_taxonomy_term', $term_string_to_index, $context );
				$term_string_to_index = $this->clean_content( $term_string_to_index );

				$cleanTerms[] = $term_string_to_index;
				next( $terms );
			}
			reset( $terms );
		}

		$cleanTerms = trim( implode( ' ', $cleanTerms ) );

		if ( ! empty( $cleanTerms ) && is_string( $cleanTerms ) && ! empty( $taxonomy ) && is_string( $taxonomy ) ) {
			return $this->get_term_counts( $cleanTerms );
		} else {
			return false;
		}
	}


	/**
	 * Get the term counts for an excerpt
	 *
	 * @param string $excerpt The excerpt to index
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function index_excerpt( $excerpt = '' ) {
		$excerpt = ( ! is_string( $excerpt ) || empty( $excerpt ) ) && ! empty( $this->post->post_excerpt ) ? $this->post->post_excerpt : $excerpt;
		$excerpt = $this->clean_content( $excerpt );

		if ( ! empty( $excerpt ) && is_string( $excerpt ) ) {
			return $this->get_term_counts( $excerpt );
		} else {
			return false;
		}
	}


	/**
	 * Extract term counts from potentially big data
	 *
	 * @since 2.8
	 *
	 * @param $string
	 *
	 * @return array
	 */
	function get_big_data_term_count( $string ) {
		if ( $this->big_data_trigger < strlen( $string ) ) {

			$counts = array();

			// chunk
			$parts = explode( "\n", wordwrap( $string, $this->big_data_trigger ) );
			$total_parts = count( $parts );

			// count terms in each chunk
			for ( $i = 0; $i < $total_parts; $i++ ) {

				$part_term_counts = $this->get_term_counts( $parts[ $i ] );

				if ( 0 === $i ) {
					// on the first pass this count chunk is it
					$counts = $part_term_counts;
				} else {
					// we need to merge this count chunk with the counts;
					// begin by looping through this count chunk
					foreach ( $part_term_counts as $key => $part_term_count ) {

						$term_hash = md5( $part_term_count['term'] );
						if ( array_key_exists( $term_hash, $counts ) ) {
							// this term was already counted, so we need to increment the count
							$counts[ $term_hash ]['count'] += $part_term_count['count'];
						} else {
							// this term has not been counted yet, so append it
							$counts[] = $part_term_count;
						}

						unset( $part_term_counts[ $key ] );
					}
				}

				unset( $parts[ $i ] );
			}

		} else {
			$counts = $this->get_term_counts( $string );
		}

		return array_values( $counts );
	}


	/**
	 * Index a Custom Field, no matter what format
	 *
	 * @param null $customFieldName Custom Field meta key
	 * @param mixed $customFieldValue Custom field value
	 * @return array|bool Terms and their associated counts
	 * @since 1.0
	 */
	function index_custom_field( $customFieldName = null, $customFieldValue ) {
		// custom fields can be pretty much anything, so we need to make sure we're unserializing, json_decoding, etc.
		$customFieldValue = $this->parse_variable_for_terms( $customFieldValue );

		if ( ! empty( $customFieldName ) && is_string( $customFieldName ) && ! empty( $customFieldValue ) && is_string( $customFieldValue ) ) {
			return $this->get_big_data_term_count( $customFieldValue );
		} else {
			return false;
		}
	}


	/**
	 * Retrieve terms from any kind of variable, even serialized and json_encode()ed values
	 *
	 * Modified from pods_sanitize() written by Scott Clark for Pods http://pods.io
	 *
	 * @param mixed $input Variable from which to obtain terms
	 * @return string Term list
	 * @since 1.0
	 */
	function parse_variable_for_terms( $input ) {
		$output = '';

		// check to see if it's encoded
		if ( is_string( $input ) ) {
			if ( is_null( $json_decoded_input = json_decode( $input, true ) ) ) {
				$input = maybe_unserialize( $input );
			} else {
				if ( ! is_numeric( $input ) ) {
					$input = $json_decoded_input;
				}
			}
		}

		// proceed with decoded input
		if ( is_string( $input ) ) {
			$output = $this->clean_content( $input );
		} elseif ( is_array( $input ) || is_object( $input ) ) {
			foreach ( (array) $input as $key => $val ) {
				$array_output = $this->parse_variable_for_terms( $val );
				if ( ! is_object( $array_output ) && 'object' === gettype( $array_output ) ) {
					// we hit a __PHP_Incomplete_Class Object because a serialized object was unserialized
					$incomplete_class_output = '';
					/** @noinspection PhpWrongForeachArgumentTypeInspection */
					foreach ( $array_output as $array_output_key => $array_output_val ) {
						$incomplete_class_output .= ' ' . $this->parse_variable_for_terms( $array_output_val );
					}
					$array_output = $incomplete_class_output;
				}
				$output .= ' ' . $array_output;
			}
		} elseif ( ! is_bool( $input ) ) {
			// Make it a string
			$output = (string) $input;
		}

		return $output;
	}

	// @codingStandardsIgnoreStart
	/**
	 * @deprecated as of 2.5.7
	 */
	function updateRunningCounts() {
		$this->update_running_counts();
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $post
	 */
	function setPost( $post ) {
		$this->set_post( $post );
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function countTotalPosts() {
		return $this->count_total_posts();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function indexedCount() {
		return $this->indexed_count();
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function findUnindexedPosts() {
		return $this->find_unindexed_posts();
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $terms
	 *
	 * @return array
	 */
	function preProcessTerms( $terms ) {
		return $this->pre_process_terms( $terms );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $terms
	 *
	 * @return array
	 */
	function recordPostTerms( $terms ) {
		return $this->record_post_terms( $terms );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param string $string
	 *
	 * @return array
	 */
	function getTermCounts( $string = '' ) {
		return $this->get_term_counts( $string );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $array
	 *
	 * @return array
	 * @internal param string $string
	 *
	 */
	function getWordCountFromArray( $array ) {
		return $this->get_word_count_from_array( $array );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $content
	 *
	 * @return array
	 * @internal param string $string
	 *
	 */
	function cleanContent( $content ) {
		return $this->clean_content( $content );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param string $title
	 *
	 * @return array
	 * @internal param string $string
	 *
	 */
	function indexTitle( $title = '' ) {
		return $this->index_title( $title );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param string $filename
	 *
	 * @return array
	 * @internal param string $string
	 *
	 */
	function indexFilename( $filename = '' ) {
		return $this->index_filename( $filename );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param string $filename
	 *
	 * @return array
	 * @internal param string $string
	 *
	 */
	function extractFilenameTerms( $filename = '' ) {
		/** @noinspection PhpDeprecationInspection */
		return $this->extract_filename_terms( $filename );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param string $slug
	 *
	 * @return array
	 * @internal param string $filename
	 *
	 * @internal param string $string
	 */
	function indexSlug( $slug = '' ) {
		return $this->index_slug( $slug );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $content
	 *
	 * @return array
	 * @internal param string $string
	 *
	 */
	function indexContent( $content ) {
		return $this->index_content( $content );
	}

	/**
	 * @deprecated as of 2.5.7
	 */
	function indexComments() {
		return $this->index_comments();
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param null $taxonomy
	 * @param array $terms
	 *
	 * @return array|bool
	 */
	function indexTaxonomyTerms( $taxonomy = null, $terms = array() ) {
		return $this->index_taxonomy_terms( $taxonomy, $terms );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param string $excerpt
	 *
	 * @return array|bool
	 */
	function indexExcerpt( $excerpt = '' ) {
		return $this->index_excerpt( $excerpt );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param null $name
	 * @param $value
	 *
	 * @return array|bool
	 */
	function indexCustomField( $name = null, $value ) {
		return $this->index_custom_field( $name, $value );
	}

	/**
	 * @deprecated as of 2.5.7
	 *
	 * @param $var
	 *
	 * @return array|bool
	 * @internal param null $name
	 * @internal param $value
	 *
	 */
	function parseVariableForTerms( $var ) {
		return $this->parse_variable_for_terms( $var );
	}
	// @codingStandardsIgnoreEnd

}
