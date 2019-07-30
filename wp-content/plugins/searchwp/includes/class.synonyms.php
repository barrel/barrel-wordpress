<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_Synonyms is responsible for synonym definition and handling
 *
 * @since 3.0
 */
class SearchWP_Synonyms {

	private $prefix = 'swp_termsyn_'; // Synonyms was originally an extension, keeping this prefix for back compat.
	private $synonyms;

	function __construct() {
		$this->synonyms = get_option( $this->prefix . 'settings' );

		// When Term Synonyms was an extension there was a uniqid flag used for the array.
		if ( is_array( $this->synonyms ) ) {
			$this->synonyms = array_values( $this->synonyms );
		}
	}

	function init() {
		add_filter( 'searchwp_pre_search_terms', array( $this, 'find' ), 5, 2 );
	}

	function get() {
		return $this->synonyms;
	}

	function update( $synonyms ) {
		foreach ( (array) $synonyms as $key => $synonymDefinition ) {

			// prepare the term
			$synonyms[ $key ]['term'] = trim( sanitize_text_field( $synonymDefinition['term'] ) );

			if ( empty( $synonymDefinition['synonyms'] ) ) {
				// no synonyms? kill it
				unset( $synonyms[ $key ] );
			} else {
				// sanitize the synonyms
				$synonyms_synonyms = explode( ',', trim( sanitize_text_field( $synonymDefinition['synonyms'] ) ) );
				$synonyms_synonyms = array_map( 'trim', $synonyms_synonyms );
				$synonyms_synonyms = array_map( 'sanitize_text_field', $synonyms_synonyms );

				$synonyms[ $key ]['synonyms'] = $synonyms_synonyms;

				// make sure there isn't synonymception
				if ( $synonyms[ $key ]['term'] == $synonyms[ $key ]['synonyms'] ) {
					unset( $synonyms[ $key ] );
				} else {
					// finalize the replace bool
					if ( isset( $synonyms[ $key ]['replace'] ) && 'false' !== $synonyms[ $key ]['replace'] && ! empty( $synonyms[ $key ]['replace'] ) ) {
						$synonyms[ $key ]['replace'] = true;
					} else {
						$synonyms[ $key ]['replace'] = false;
					}
				}
			}
		}

		// deliver sanitized results
		$synonyms = array_values( $synonyms );

		update_option( $this->prefix . 'settings', $synonyms ); // This is a legacy key used when this was a standalone Extension.

		return $synonyms;
	}

	/**
	 * Retrieve synonyms
	 *
	 * @param $term
	 *
	 * @return array
	 */
	function find( $term ) {
		if ( empty( $term ) || empty( $this->synonyms ) ) {
			return $term;
		}

		$synonyms = $this->synonyms;

		// convert everything to lowercase
		if ( ! empty( $synonyms ) ) {
			foreach ( $synonyms as $synonym_id => $synonym ) {
				if ( ! empty( $synonyms[ $synonym_id ]['term'] ) ) {
					if ( function_exists( 'mb_strtolower' ) ) {
						$synonyms[ $synonym_id ]['term'] = mb_strtolower( $synonyms[ $synonym_id ]['term'] );
					} else {
						$synonyms[ $synonym_id ]['term'] = strtolower( $synonyms[ $synonym_id ]['term'] );
					}
				}

				if ( is_array( $synonyms[ $synonym_id ]['synonyms'] ) && ! empty( $synonyms[ $synonym_id ]['synonyms'] ) ) {
					if ( function_exists( 'mb_strtolower' ) ) {
						array_map( 'mb_strtolower', $synonyms[ $synonym_id ]['synonyms'] );
					} else {
						array_map( 'strtolower', $synonyms[ $synonym_id ]['synonyms'] );
					}
				}
			}
		}

		// we expect $term to be an array
		if ( is_string( $term ) ) {
			$term = array( $term );
		}

		if ( is_array( $term ) && is_array( $synonyms ) && ! empty( $synonyms ) ) {
			foreach ( $synonyms as $synonym ) {
				$whole_phrase_match = strtolower( trim( implode( ' ', $term ) ) ) === strtolower( trim( $synonym['term'] ) );

				if ( $whole_phrase_match || in_array( $synonym['term'], $term, true ) ) {

					// there is a match, handle it

					// break out where applicable
					if ( is_array( $synonym['synonyms'] ) && ! empty( $synonym['synonyms'] ) ) {
						foreach ( $synonym['synonyms'] as $key => $maybe_synonym ) {
							if ( false !== strpos( $maybe_synonym, ' ' ) ) {
								$maybe_synonym = explode( ' ', $maybe_synonym );

								unset( $synonym['synonyms'][ $key ] );

								$synonym['synonyms'] = array_merge( $synonym['synonyms'], $maybe_synonym );
								$synonym['synonyms'] = array_values( $synonym['synonyms'] );
							}
						}
					}

					// merge everything together
					$term = array_merge( $term, $synonym['synonyms'] );
				}
			}
		}

		// LASTLY handle any Replacements
		if ( is_array( $term ) && ! empty( $term ) && is_array( $synonyms ) && ! empty( $synonyms ) ) {
			foreach ( $term as $key => $potential_replacement ) {
				foreach ( $synonyms as $synonym ) {
					if ( ! empty( $synonym['replace'] ) && $synonym['term'] == $potential_replacement ) {
						unset( $term[ $key ] );
					}
				}
			}
		}

		$term = array_values( array_unique( $term ) );
		$term = array_map( 'sanitize_text_field', $term );

		if ( function_exists( 'mb_strtolower' ) ) {
			$term = array_map( 'mb_strtolower', $term );
		} else {
			$term = array_map( 'strtolower', $term );
		}

		return $term;
	}
}
