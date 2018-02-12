<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_Stats
 */
class SearchWP_Stats {

	private $nonce;

	/**
	 * Generate a nonce for $this to use
	 *
	 * @since 2.7.1
	 *
	 * @param string $name
	 */
	function generate_nonce( $name = 'stats' ) {
		$this->nonce = wp_create_nonce( 'swpstats' . sanitize_key( $name ) );
	}

	/**
	 * Verify a submitted nonce
	 *
	 * @since 2.7.1
	 *
	 * @param $nonce
	 * @param string $name
	 *
	 * @return false|int
	 */
	function verify_nonce( $nonce, $name = 'stats' ) {
		return wp_verify_nonce( sanitize_text_field( $nonce ), 'swpstats' . sanitize_key( $name ) );
	}

	/**
	 * Get $this's nonce
	 *
	 * @since 2.7.1
	 *
	 * @return mixed
	 */
	function get_nonce() {
		return $this->nonce;
	}

	/**
	 * Retrieve searches within the past X days for a specific engine, ordered by number of searches
	 *
	 * @param array $args
	 *      ['days']        How many days back to go
	 *      ['engine']      The search engine
	 *      ['exclude']     Search queries to skip (must be md5 hashes!)
	 *      ['limit']       The number of search queries to return
	 *
	 * @return mixed
	 */
	public function get_popular_searches( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'days'      => 1,               // how many days to include
			'engine'    => 'default',       // the engine used
			'exclude'   => array(),         // what queries to ignore
			'limit'     => 10,              // how many to return
			'min_hits'  => 1,               // minimum number of hits (disable with false)
			'max_hits'  => false,           // maximum number of hits (disable with false)
		);

		// process our arguments
		$args = wp_parse_args( $args, $defaults );

		$prefix = $wpdb->prefix;
		$days = absint( $args['days'] );
		$limit = absint( $args['limit'] );

		// build our query
		$sql = "SELECT {$prefix}swp_log.query, count({$prefix}swp_log.query) AS searchcount
			FROM {$prefix}swp_log
			WHERE tstamp > DATE_SUB(NOW(), INTERVAL %d DAY)
			AND {$prefix}swp_log.event = 'search'
			AND {$prefix}swp_log.engine = %s ";

		// determine what to exclude
		$sql .= " AND {$prefix}swp_log.query <> '' ";

		// exclude excluded excludes if there are any
		if ( ! empty ( $args['exclude'] ) ) {
			// add a placeholder for each value in $args['exclude']
			$sql .= " AND md5({$prefix}swp_log.query) NOT IN ( " . implode( ', ', array_fill( 0, count( $args['exclude'] ), '%s' ) ) . " ) ";
		}

		// set min hits
		if ( false !== $args['min_hits'] ) {
			$sql .= " AND {$prefix}swp_log.hits >= %d ";
		}

		// set max hits
		if ( false !== $args['max_hits'] ) {
			$sql .= " AND {$prefix}swp_log.hits <= %d ";
		}

		$sql .= " GROUP BY {$prefix}swp_log.query
			ORDER BY searchcount DESC
			LIMIT %d";

		$values_to_prepare = array( $days, $args['engine'] );

		if ( ! empty ( $args['exclude'] ) ) {
			$values_to_prepare = array_merge( $values_to_prepare, $args['exclude'] );
		}

		if ( false !== $args['min_hits'] ) {
			$values_to_prepare[] = absint( $args['min_hits'] );
		}

		if ( false !== $args['max_hits'] ) {
			$values_to_prepare[] = absint( $args['max_hits'] );
		}

		$values_to_prepare[] = $limit;

		return $wpdb->get_results(
			$wpdb->prepare(
				$sql,
				array_values( $values_to_prepare )
			)
		);
	}

	/**
	 * Apply markup to stats and echo
	 *
	 * @param $stats array The stats to display
	 *
	 * @param string $engine The engine to use
	 *
	 * @since 2.8
	 */
	function display( $stats, $engine = 'default' ) { ?>
		<?php if ( ! empty( $stats ) ) : ?>
			<?php
				$classes = apply_filters( 'searchwp_stats_table_class', array() );
			?>
			<table cellpadding="0" cellspacing="0" class="<?php echo esc_attr( implode( ' ', (array) $classes ) ); ?>">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Query', 'searchwp' ); ?></th>
						<th><?php esc_html_e( 'Count', 'searchwp' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
					$action_args = array(
						'engine'    => $engine,
						'nonce'     => $this->nonce,
					);
				?>
				<?php foreach ( $stats as $stat ) : ?>
					<tr>
						<td>
							<div title="<?php echo esc_attr( $stat->query ); ?>">
								<?php do_action( 'searchwp_stats_before_query', $stat->query, $action_args ); ?>
								<?php echo esc_html( $stat->query ); ?>
								<?php do_action( 'searchwp_stats_after_query', $stat->query, $action_args ); ?>
							</div>
						</td>
						<td>
							<?php do_action( 'searchwp_stats_before_count', $stat->query, $action_args ); ?>
							<?php echo absint( $stat->searchcount ); ?>
							<?php do_action( 'searchwp_stats_after_count', $stat->query, $action_args ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<p class="description"><?php esc_html_e( 'No searches for this time period.', 'searchwp' ); ?></p>
		<?php endif; ?>
	<?php }

	/**
	 * Retrieve an array of search counts per day (array key is day number)
	 *
	 * @param int $days
	 * @param string $engine
	 *
	 * @return array|null|object
	 */
	function get_search_counts_per_day( $days = 30, $engine = 'default' ) {
		global $wpdb;

		$prefix = $wpdb->prefix;

		$days = absint( $days );

		// retrieve our counts for the past 30 days
		$searchCounts = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT DAY({$prefix}swp_log.tstamp) AS day,
					MONTH({$prefix}swp_log.tstamp) AS month,
					count({$prefix}swp_log.tstamp) AS searches
				FROM {$prefix}swp_log
				WHERE tstamp > DATE_SUB(NOW(), INTERVAL %d day)
					AND {$prefix}swp_log.event = 'search'
					AND {$prefix}swp_log.engine = %s
					AND {$prefix}swp_log.query <> ''
				GROUP BY TO_DAYS({$prefix}swp_log.tstamp)
				ORDER BY {$prefix}swp_log.tstamp DESC",
				$days,
				$engine
			),
			'OBJECT_K'
		);

		// key our array
		$searchesPerDay = array();
		for ( $i = 0; $i <= 30; $i++ ) {
			$searchesPerDay[ strtoupper( date( 'Md', strtotime( '-'. ( $i ) .' days' ) ) ) ] = 0;
		}

		if ( is_array( $searchCounts ) && count( $searchCounts ) ) {
			foreach ( $searchCounts as $searchCount ) {
				$count 		= absint( $searchCount->searches );
				$day 		= ( intval( $searchCount->day ) ) < 10 ? 0 . absint( $searchCount->day ) : absint( $searchCount->day );
				$month 		= ( intval( $searchCount->month ) ) < 10 ? 0 . absint( $searchCount->month ) : absint( $searchCount->month );
				$refdate 	= $month . '/01/' . date( 'Y' );
				$month 		= date( 'M', strtotime( $refdate ) );
				$key 		= strtoupper( $month . $day );

				$searchesPerDay[ $key ] = absint( $count );
			}
		}

		return array_reverse( $searchesPerDay );
	}

	/**
	 * SearchWP uses Chartist https://gionkunz.github.io/chartist-js/ to display results
	 * This method takes a properly prepped (via $this->get_search_counts_per_day()) array
	 * of stats and outputs them as a Chartist graph
	 *
	 * @since 2.7.1
	 *
	 * @param $searchesPerDay array of searches per day to get charted
	 */
	function generate_chartist_data( $searchesPerDay ) {

		// generate the x-axis labels
		$x_axis_labels = array();
		foreach ( $searchesPerDay as $day_key => $day_value ) {
			// keys are stored as 'Md' date format so we'll "decode"
			$x_axis_labels[] = intval( substr( $day_key, 3, 5 ) );
		}

		// dump out the necessary JavaScript vars
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				var chart_data = {
					labels: [<?php echo esc_js( implode( ',', $x_axis_labels ) ); ?>],
					series: [[<?php echo esc_js( implode( ',', $searchesPerDay ) ); ?>]]
				};
				var chart_options = {
					low: 0,
					showArea: true
				};

				function ordinal_suffix_of(i) {
					var j = i % 10,
						k = i % 100;
					if (j == 1 && k != 11) {
						return i + "st";
					}
					if (j == 2 && k != 12) {
						return i + "nd";
					}
					if (j == 3 && k != 13) {
						return i + "rd";
					}
					return i + "th";
				}

				var chart_responsive_options = [
					['screen and (min-width: 1251px)', {
						axisX: {
							labelInterpolationFnc: function(value) {
								value = ordinal_suffix_of(value);
								return value;
							}
						}
					}],
					['screen and (min-width: 751px) and (max-width: 1250px)', {
						axisX: {
							labelInterpolationFnc: function(value) {
								// only show every other day
								if(value%2){
									value = '';
								}else{
									value = ordinal_suffix_of(value);
								}
								return value;
							}
						}
					}],
					['screen and (max-width: 750px)', {
						axisX: {
							labelInterpolationFnc: function(value) {
								// hide the x axis labels
								return '';
							}
						}
					}]];
				Chartist.Line('.swp-searches-chart', chart_data, chart_options, chart_responsive_options );
			});
		</script>
		<?php
	}

	/**
	 * Retrieve all ignored queries
	 *
	 * @param int $user_id
	 *
	 * @since 2.7.1
	 *
	 * @return mixed
	 */
	function get_ignored_queries( $user_id = 0 ) {
		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;

		$ignored_queries = get_user_meta( absint( $user_id ), SEARCHWP_PREFIX . 'ignored_queries', true );

		if ( ! is_array( $ignored_queries ) ) {
			$ignored_queries = array();
		}

		// we might need to update the format of $ignored_queries; 2.4.10 switched to all hashes (both keys and values)
		// to get around some edge cases of crazy search queries not being ignored
		// to check this we'll make sure the key matches the value and if it doesn't we'll run the update routine
		// this has to happen here because ignored queries are stored per-user
		if ( count( $ignored_queries ) ) {
			$ignored_queries_needs_update = true;
			foreach ( $ignored_queries as $key => $val ) {
				if ( $key == $val ) {
					$ignored_queries_needs_update = false;
					break;
				}
				$ignored_queries[ $key ] = md5( $val );
			}
			if ( $ignored_queries_needs_update ) {
				update_user_meta( absint( $user_id ), SEARCHWP_PREFIX . 'ignored_queries', $ignored_queries );
			}
		}

		return $ignored_queries;
	}

	/**
	 * Add a query to the ignored queries for a specific user
	 *
	 * @param $query_hash
	 * @param int $user_id
	 *
	 * @since 2.7.1
	 */
	function ignore_query( $query_hash, $user_id = 0 ) {

		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;

		// $query_hash must be the md5 hash of the ignored query
		if ( ! preg_match('/^[a-f0-9]{32}$/', $query_hash ) ) {
			return;
		}

		// retrieve the existing ignored queries because we're going to add this to the list
		$ignored_queries = $this->get_ignored_queries( $user_id );

		// retrieve the original query
		$query_to_ignore = $this->get_query_from_hash( $query_hash );

		if ( ! empty( $query_to_ignore ) ) {
			$ignored_queries[ $query_hash ] = $query_hash;
		}

		update_user_meta( absint( $user_id ), SEARCHWP_PREFIX . 'ignored_queries', $ignored_queries );
	}

	/**
	 * Retrieve the original query from its md5 hash
	 *
	 * @since 2.7.1
	 *
	 * @param $query_hash
	 *
	 * @return bool
	 */
	function get_query_from_hash( $query_hash ) {
		global $wpdb;

		$prefix = $wpdb->prefix;

		// $query_hash must be the md5 hash of the ignored query
		if ( ! preg_match('/^[a-f0-9]{32}$/', $query_hash ) ) {
			return false;
		}

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT {$prefix}swp_log.query, md5( {$prefix}swp_log.query )
				FROM {$prefix}swp_log
				WHERE md5( {$prefix}swp_log.query ) = %s",
				$query_hash
			)
		);
	}

}
