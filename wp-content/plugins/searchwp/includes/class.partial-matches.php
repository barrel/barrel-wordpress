<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWPPartialMatches enables partial term matching during searches.
 *
 * @since 3.0
 */
class SearchWPPartialMatches {

	private $exact_matches = array();
	private $consumed      = array();

	public function __construct() {}

	/**
	 * This function imposes partial matching during searches. Internally it is called only if the Advanced
	 * setting has been ticked/enabled.
	 *
	 * @since 3.0
	 */
	public function init() {
		// Check for exact matches so we can bail out ASAP.
		add_filter( 'searchwp_terms', array( $this, 'set_exact_matches' ), 11, 2 );

		// Fuzzy Matches (deprecated) has a priority of 100, but we want to run last.
		add_filter( 'searchwp_term_in', array( $this, 'find_partial_matches' ), 210, 3 );
	}

	/**
	 * Find any exact matches for this engine.
	 *
	 * @param string $query The full search query.
	 * @param string $engine The engine being used.
	 *
	 * @since 3.0
	 */
	public function set_exact_matches( $query, $engine ) {
		global $wpdb;

		$proceed = apply_filters( 'searchwp_partial_matching_' . $engine, true );

		if ( empty( $proceed ) ) {
			return;
		}

		$term_array = explode( ' ', $query );
		$term_array = array_map( 'trim', $term_array );
		$term_array = array_map( 'sanitize_text_field', $term_array );
		$term_array = array_map( 'strtolower', $term_array );

		if ( empty( $term_array ) ) {
			return;
		}

		$prefix = $wpdb->prefix;

		foreach ( $term_array as $term ) {
			$found_term = $wpdb->get_col(
				$wpdb->prepare( "SELECT term FROM {$prefix}swp_terms WHERE term = %s LIMIT 1", $term )
			);

			if ( $found_term ) {
				if ( ! array_key_exists( $engine, $this->exact_matches ) ) {
					$this->exact_matches[ $engine ] = array();
				}

				$this->exact_matches[ $engine ][] = $term;

				$break_on_first_match = apply_filters( 'searchwp_partial_matches_aggressive', false, array(
					'engine' => $engine,
				) );

				if ( $break_on_first_match ) {
					break;
				}
			}
		}

		return $query;
	}

	public function find_partial_matches( $terms, $engine, $original_prepped_term ) {
		$proceed = apply_filters( 'searchwp_partial_matching_' . $engine, true );

		if ( empty( $proceed ) ) {
			$this->reset();

			return;
		}

		$proceed_despite_exact_matches = apply_filters( 'searchwp_partial_matches_lenient', true, array(
			'engine' => $engine,
		) );

		if ( ! empty( $this->exact_matches[ $engine ] ) && empty( $proceed_despite_exact_matches ) ) {
			$this->reset();

			return $terms;
		}

		$like_terms = $this->find_like_terms( $terms, $engine );

		$like_terms = array_diff( $like_terms, $terms );
		$has_like_terms = ! empty( $like_terms );

		$force_fuzzy = apply_filters( 'searchwp_partial_matches_force_fuzzy', false, array(
			'engine' => $engine,
		) );

		// If we found LIKE terms and don't want to force fuzzy matches, break out
		if ( $has_like_terms && empty( $force_fuzzy ) ) {
			$this->reset();

			return array_merge( (array) $terms, (array) $like_terms );
		}

		$fuzzy_terms = $this->find_fuzzy_matches( $terms, $engine, $original_prepped_term );
		$fuzzy_terms = array_diff( $fuzzy_terms, $terms );

		$all_terms = array_unique( array_merge( (array) $terms, (array) $like_terms, (array) $fuzzy_terms ) );

		$this->reset();

		return $all_terms;
	}

	public function reset() {
		$this->consumed = array();
	}

	public function find_like_terms( $terms, $engine ) {
		global $wpdb, $searchwp;

		$original = $terms;
		$this->original_search = $original;

		$prefix = $wpdb->prefix;

		if ( is_string( $terms ) ) {
			$terms = explode( ' ', $terms );
		}

		// check against the regex pattern whitelist
		$terms = ' ' . implode( ' ', $terms ) . ' ';
		$whitelisted_terms = array();

		if ( method_exists( $searchwp, 'extract_terms_using_pattern_whitelist' ) ) { // added in SearchWP 1.9.5
			// extract terms based on whitelist pattern, allowing for approved indexing of terms with punctuation
			$whitelisted_terms = $searchwp->extract_terms_using_pattern_whitelist( $terms );

			// add the buffer so we can whole-word replace
			$terms = '  ' . $terms . '  ';

			// remove the matches
			if ( ! empty( $whitelisted_terms ) ) {
				$terms = str_ireplace( $whitelisted_terms, '', $terms );
			}

			// clean up the double space flag we used
			$terms = str_replace( '  ', ' ', $terms );
		}

		// rebuild our terms array
		$terms = explode( ' ', $terms );

		// maybe append our whitelist
		if ( is_array( $whitelisted_terms ) && ! empty( $whitelisted_terms ) ) {
			$whitelisted_terms = array_map( 'trim', $whitelisted_terms );
			$terms = array_merge( $terms, $whitelisted_terms );
		}

		$terms = array_map( 'trim', $terms );
		$terms = array_filter( $terms, 'strlen' );
		$terms = array_map( 'sanitize_text_field', $terms );

		// dynamic minimum character length
		$minCharLength = absint( apply_filters( 'searchwp_like_min_length', 2 ) ) - 1;

		// Filter out $terms based on min length
		foreach ( $terms as $key => $term ) {
			if ( strlen( $term ) < $minCharLength ) {
				unset ( $terms[ $key ] );
			}
		}

		$terms = array_values( $terms );

		$likeTerms = array();

		if ( ! empty( $terms ) ) {

			// by default we will compare to both the term and the stem, but give developers the option to prevent comparison to the stem
			$term_or_stem = 'stem';
			if ( ! apply_filters( 'searchwp_like_stem', false, $terms, $engine ) ) {
				$term_or_stem = 'term';
			}

			$sql = "SELECT {$term_or_stem} FROM {$prefix}swp_terms WHERE CHAR_LENGTH({$term_or_stem}) > {$minCharLength} AND (";

			$wildcard_before = apply_filters( 'searchwp_like_wildcard_before', true );
			if ( ! empty( $wildcard_before ) ) {
				$wildcard_before = '%';
			} else {
				$wildcard_before = '';
			}

			$wildcard_after = apply_filters( 'searchwp_like_wildcard_after', true );
			if ( ! empty( $wildcard_after ) ) {
				$wildcard_after = '%';
			} else {
				$wildcard_after = '';
			}

			// need to query for LIKE matches in terms table and append them
			$count = 0;
			foreach ( $terms as $term ) {
				if ( $count > 0 ) {
					$sql .= ' OR ';
				}
				if ( 'stem' == $term_or_stem ) {
					$sql .= $wpdb->prepare( ' ( term LIKE %s OR stem LIKE %s ) ', $wildcard_before . $wpdb->esc_like( $term ) . $wildcard_after, $wildcard_before . $wpdb->esc_like( $term ) . $wildcard_after );
				} else {
					$sql .= $wpdb->prepare( ' ( term LIKE %s ) ', $wildcard_before . $wpdb->esc_like( $term ) . $wildcard_after );
				}
				$count ++;
			}
			$sql .= ')';

			$likeTerms = $wpdb->get_col( $sql );
		}

		$term = array_values( array_unique( array_merge( $likeTerms, $terms ) ) );

		$term = array_map( 'sanitize_text_field', $term );

		// Allow LIKE terms to be used more than once?
		$exclude_consumed = apply_filters( 'searchwp_like_aggressive', false );

		if ( ! empty( $term ) && $exclude_consumed ) {
			$term = array_diff( $term, $this->consumed );
			if ( empty( $term ) ) {
				$term = (array) $original;
			}
		}

		$this->consumed = array_unique( array_merge( $this->consumed, $term ) );

		return $term;
	}

	/**
	 * Find fuzzy matches using MySQL's SOUNDEX feature
	 *
	 * @param $terms
	 * @param $engine
	 * @param $original_prepped_term
	 *
	 * @return array
	 */
	public function find_fuzzy_matches( $terms, $engine, $original_prepped_term ) {
		global $wpdb, $searchwp;

		if ( isset( $engine ) ) {
			$engine = null;
		}

		$prefix = $wpdb->prefix;

		// there has to be at least a term
		if ( ! is_array( $terms ) || empty( $terms ) ) {
			return $terms;
		}

		// by default we're only going to apply fuzzy logic if we need to (e.g. confirmed misspelling)
		$missing_match = '';
		$found_term = $wpdb->get_col( $wpdb->prepare( "SELECT term FROM {$prefix}swp_terms WHERE term = %s LIMIT 1", $original_prepped_term ) );

		if ( empty( $found_term ) ) {
			$missing_match = $original_prepped_term;
		}

		// if everything was an exact match there's no more work to do
		if ( ! empty( $missing_match ) ) {

			// dynamic minimum character length
			$minCharLength = absint( apply_filters( 'searchwp_fuzzy_min_length', 3 ) ) - 1;

			$sql = "SELECT term FROM {$prefix}swp_terms WHERE CHAR_LENGTH(term) > {$minCharLength} AND (";

			// need to query for fuzzy matches in terms table and append them
			$count = 0;
			$the_terms = array();
			foreach ( $terms as $term ) {

				if ( $count > 0 ) {
					$sql .= ' OR ';
				}

				// check for the number of digits (e.g. SKUs being sent through would result in disaster)
				preg_match_all( '/[0-9]/', $term, $digits );
				$percentDigits = ! empty( $digits ) && isset( $digits[0] ) ? ( count( $digits[0] ) / strlen( $term ) ) * 100 : 0;

				$percentDigitsThreshold = absint( apply_filters( 'searchwp_fuzzy_digit_threshold', 10 ) );
				if ( $percentDigits < $percentDigitsThreshold ) {
					$sql .= $wpdb->prepare( ' SOUNDEX(term) LIKE SOUNDEX( %s ) ', $term );
					$the_terms[] = $term;
				}

				$count++;
			}

			$sql .= ')';

			$wickedFuzzyTerms = array();

			if ( ! empty( $the_terms ) ) {
				$wickedFuzzyTerms = $wpdb->get_col( $sql );
			}

			// depending on whether we actually used SOUNDEX, we need to trim out potential results
			// determine whether each match should be included based on how many characters match
			$threshold = absint( apply_filters( 'searchwp_fuzzy_threshold', 70 ) );

			if ( $threshold > 100 ) {
				$threshold = 100;
			}

			// loop through all of the wicked fuzzy terms and pluck out what's really relevant
			$actualTerms = array();
			if ( ! empty( $wickedFuzzyTerms ) ) {
				foreach ( $wickedFuzzyTerms as $wickedFuzzyTerm ) {
					foreach ( $terms as $term ) {

						similar_text( $wickedFuzzyTerm, $term, $percent );

						if ( $percent > $threshold ) {
							$actualTerms[] = $wickedFuzzyTerm;
						}
					}
				}
			}

			// clean up our dupes
			if ( ! empty( $actualTerms ) ) {
				$terms = array_values( array_unique( $actualTerms ) );
				$terms = array_map( 'sanitize_text_field', $terms );
			}
		}

		return $terms;
	}
}
