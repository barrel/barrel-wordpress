<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWPHighlighter enables term highlighting.
 *
 * @since 3.0
 */
class SearchWPHighlighter {

	public $number_of_words;
	public $common;
	public $min_word_length;
	private $search_args;
	private $prepped_terms;
	private $highlight_el;

	public function __construct() {
		$this->common          = SWP()->common;
		$this->highlight_el    = 'mark';
		$this->number_of_words = 55;
		$this->min_word_length = 3;
	}

	public function init() {
		$this->number_of_words = absint( apply_filters( 'searchwp_th_num_words', $this->number_of_words ) );
		$this->min_word_length = absint( apply_filters( 'searchwp_minimum_word_length', $this->min_word_length ) );

		$use_span_instead_of_mark = apply_filters( 'searchwp_th_use_span', false );
		if ( $use_span_instead_of_mark ) {
			$this->highlight_el = 'span';
		}

		// This is using an action because Term Highlight (deprecated when 3.0 was released) may be active.
		add_action( 'wp', 'searchwp_init_global_highlight_functions' );
	}

	public function setup_auto_highlight() {
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'searchwp_load_posts', array( $this, 'maybe_load_posts' ), 10, 2 );
		add_filter( 'searchwp_found_post_objects', array( $this, 'highlight_posts' ), 10, 2 );
		add_action( 'wp', array( $this, 'wp' ) );
	}

	public function wp() {
		if ( ! apply_filters( 'searchwp_th_in_admin', ! is_admin() ) ) {
			return;
		}

		$automatically_filter_excerpt = apply_filters( 'searchwp_th_auto_filter_excerpt', true );

		if ( $automatically_filter_excerpt && is_search() ) {
			add_filter( 'get_the_excerpt', array( $this, 'auto_excerpt' ) );
		}
	}

	/**
	 * Determine whether to load post objects
	 *
	 * @param $load_posts
	 * @param $search_args
	 *
	 * @return bool
	 */
	public function maybe_load_posts( $load_posts, $search_args ) {

		if ( ! apply_filters( 'searchwp_th_in_admin', ! is_admin() ) ) {
			return $load_posts;
		}

		if ( isset( $load_posts ) ) {
			$load_posts = null;
		}

		$excluded_engines = apply_filters( 'searchwp_th_excluded_engines', array() );

		return ! in_array( $search_args['engine'], $excluded_engines, true );
	}

	/**
	 * Apply highlighting to known post object properties
	 *
	 * @param $posts
	 * @param $search_args
	 *
	 * @return mixed
	 */
	public function highlight_posts( $posts, $search_args ) {

		if ( ! apply_filters( 'searchwp_th_in_admin', ! is_admin() ) ) {
			return $posts;
		}

		$this->search_args = $search_args;

		if ( is_array( $posts ) && ! empty( $posts ) ) {

			$terms = $search_args['terms'];

			foreach ( $posts as $key => $val ) {
				$posts[ $key ]->post_title = $this->apply_highlight( $posts[ $key ]->post_title, $terms );

				if ( apply_filters( 'searchwp_th_auto_highlight_content', true ) ) {
					$posts[ $key ]->post_content = $this->apply_highlight( $posts[ $key ]->post_content, $terms );
					$posts[ $key ]->post_excerpt = $this->apply_highlight( $posts[ $key ]->post_excerpt, $terms );
				}
			}
		}

		return $posts;
	}

	/**
	 * Prepare (tokenize) terms
	 *
	 * @param $terms
	 *
	 * @return mixed|string|void
	 */
	function prep_terms( $terms ) {

		global $wpdb;

		if ( ! empty( $this->prepped_terms ) ) {
			return $this->prepped_terms;
		}

		$searchwp = SWP();
		if ( ! is_array( $terms ) ) {
			$original_terms = explode( ' ', $terms );
		} else {
			$original_terms = $terms;
		}

		$whitelisted_terms = array();

		// allow developers to manually define which variable should be used for the search term
		$terms = apply_filters( 'searchwp_th_query', $terms );

		if ( empty( $terms ) ) {
			$terms = get_search_query();
		}

		// make sure it's a string
		if ( is_array( $terms ) ) {
			$terms = implode( ' ', $terms );
		} else {
			$terms = (string) $terms;
		}

		// check against the regex pattern whitelist
		$terms = ' ' . $terms . ' ';
		if ( method_exists( $searchwp, 'extract_terms_using_pattern_whitelist' ) ) { // added in SearchWP 1.9.5
			// extract terms based on whitelist pattern, allowing for approved indexing of terms with punctuation
			$whitelisted_terms = $searchwp->extract_terms_using_pattern_whitelist( $terms );

			// add the buffer so we can whole-word replace
			$terms = '  ' . trim( $terms ) . '  ';

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

		// make sure it's an array
		if ( ! is_array( $terms ) ) {
			$terms = array( $terms );
		}

		// if stemming is enabled, append the stems of all terms
		$engine = $this->search_args['engine'];
		$stemming_enabled = false;
		if ( ! empty( $searchwp->settings['engines'][ $engine ] ) ) {
			foreach ( $searchwp->settings['engines'][ $engine ] as $post_type => $post_type_settings ) {
				if ( ! empty( $post_type_settings['options']['stem'] ) ) {
					$stemming_enabled = true;
					break;
				}
			}
		}

		$terms = array_filter( $terms, 'strlen' );

		$stems = array();
		if ( $stemming_enabled && class_exists( 'SearchWPStemmer' ) ) {

			$stemmer = new SearchWPStemmer();

			foreach ( $terms as $term ) {

				// append stems to the array
				$unstemmed = $term;
				$maybe_stemmed = apply_filters( 'searchwp_custom_stemmer', $unstemmed );

				// if the term was stemmed via the filter use it, else generate our own
				$stem = ( $unstemmed === $maybe_stemmed ) ? $stemmer->stem( $term ) : $maybe_stemmed;

				$stems[] = $stem;
			}

			$terms = array_merge( $terms, $stems );
			$terms = array_unique( $terms );

			// we also need the inverse (grab all of the source terms that have the same stem)
			if ( ! empty( $stems ) ) {
				$prefix = $wpdb->prefix . SEARCHWP_DBPREFIX;
				$prepare = array();
				foreach ( $stems as $stem ) {
					$prepare[] = '%s';
				}
				$sql = "SELECT term
					FROM {$prefix}terms
					WHERE stem IN ( " . implode( ',', $prepare ) . " )";
				$prepared = $wpdb->prepare( $sql, $stems );
				$source_terms = $wpdb->get_col( $prepared );

				$terms = array_merge( $terms, $source_terms );
				$terms = array_unique( $terms );
			}
		}

		// make sure the search query has priority so it's processed first
		if ( ! is_array( $original_terms ) ) {
			$original_terms = array( $original_terms );
		}
		$terms = array_merge( $original_terms, $terms );
		$terms = array_unique( $terms );

		// TODO: BEGIN REFACTOR002

		// apply the same term processing that SearchWP core would
		// (which requires the search query be formatted as an array)
		if ( ! is_array( $terms ) ) {
			$terms = explode( ' ', $terms );
		}

		foreach ( $terms as $key => $term ) {
			$these_terms = apply_filters( 'searchwp_term_in', array( $term ), 'searchwp_term_highlight', $term );

			if ( ! empty( $these_terms ) ) {
				$terms = array_merge( $terms, $these_terms );
			}
		}

		// implode back into a string because that's what we're working with in this context
		$terms = array_unique( $terms );

		// END REFACTOR002

		// sanitize
		$terms = array_map( 'sanitize_text_field', $terms );

		$this->prepped_terms = $terms;

		return $this->prepped_terms;
	}

	function auto_excerpt( $excerpt ) {
		return $this->apply_highlight( $excerpt, SWP()->original_query );
	}

	/**
	 * This extension does the best it can to automatically highlight content retrieved in search results, but since
	 * SearchWP can search anything, there are many things that cannot be automatically highlighted such as custom field
	 * content, taxonomy terms, and comment content. This utility function aims to make highlighting that content easier
	 *
	 * @param $content
	 * @param null $terms
	 *
	 * @return mixed
	 */
	function apply_highlight( $content, $terms = null ) {
		// if a highlight was already found, return it
		if ( false !== strpos( $content, 'searchwp-highlight' ) ) {
			return $content;
		}

		if ( empty( $terms ) ) {
			return $content;
		}

		$terms = $this->prep_terms( $terms );
		$terms = array_filter( $terms, 'strlen' );

		$content = $this->pre_process_content( $content );

		// Step 1: See if there's a whole match for the original query.
		$whole_match = preg_quote( SWP()->original_query, '/' );

		if ( apply_filters( 'searchwp_th_partial_matches', false ) ) {
			$maybe_highlight = preg_replace( "/$whole_match(?!([^<]+)?>)/iu", "<" . $this->highlight_el . " class='searchwp-highlight'>$0</" . $this->highlight_el . ">", $content );
		} else {
			$maybe_highlight = preg_replace( "/\b$whole_match\b(?!([^<]+)?>)/iu", "<" . $this->highlight_el . " class='searchwp-highlight'>$0</" . $this->highlight_el . ">", $content );
		}

		// Step 2: See if there's a mach for the term list itself.
		if ( false === strpos( $maybe_highlight, 'searchwp-highlight' ) ) {
			$whole_match = preg_quote( implode( ' ', $terms ), '/' );

			if ( apply_filters( 'searchwp_th_partial_matches', false ) ) {
				$maybe_highlight = preg_replace( "/$whole_match(?!([^<]+)?>)/iu", "<" . $this->highlight_el . " class='searchwp-highlight'>$0</" . $this->highlight_el . ">", $content );
			} else {
				$maybe_highlight = preg_replace( "/\b$whole_match\b(?!([^<]+)?>)/iu", "<" . $this->highlight_el . " class='searchwp-highlight'>$0</" . $this->highlight_el . ">", $content );
			}
		}

		// Step 3: Fall back to individual matches
		if ( false === strpos( $maybe_highlight, 'searchwp-highlight' ) ) {
			foreach ( $terms as $term ) {
				if ( ( ! is_array( $this->common ) || ( is_array( $this->common ) && ! in_array( $term, $this->common, true ) ) ) && $this->min_word_length <= strlen( $term ) ) {
					$term = preg_quote( $term, '/' );
					// allow devs to highlight partial matches
					if ( apply_filters( 'searchwp_th_partial_matches', false ) ) {
						$content = preg_replace( "/$term(?!([^<]+)?>)/iu", "<" . $this->highlight_el . " class='searchwp-highlight'>$0</" . $this->highlight_el . ">", $content );
					} else {
						$content = preg_replace( "/\b$term\b(?!([^<]+)?>)/iu", "<" . $this->highlight_el . " class='searchwp-highlight'>$0</" . $this->highlight_el . ">", $content );
					}
				}

				// if a highlight was found, break out now; extensions such as LIKE Terms can
				// cause unwanted results with multiple (not matching) highlights
				if ( apply_filters( 'searchwp_term_highlight_break_on_first_match', true ) && false !== strpos( $content, 'searchwp-highlight' ) ) {
					break;
				}
			}
		} else {
			// found a whole match
			$content = $maybe_highlight;
		}

		return $content;
	}

	/**
	 * Extract an excerpt with any number of words that should include one or more of the search terms
	 *
	 * @param null $terms
	 */
	function the_excerpt( $terms = null ) {
		echo wp_kses_post( $this->get_the_excerpt( $terms ) );
	}

	/**
	 * Determine an excerpt to use
	 *
	 * @param null $terms
	 * @param string $excerpt
	 * @param bool $apply_native_wp_filter
	 *
	 * @return string
	 */
	function get_the_excerpt( $terms = null, $excerpt = '', $apply_native_wp_filter = true ) {
		$post = get_post();

		if ( empty( $post ) || is_null( $post ) ) {
			return '';
		}

		$original_terms = $terms;

		if ( is_array( $original_terms ) ) {
			$original_terms_lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( implode( ' ', $original_terms ) ) : strtolower( implode( ' ', $original_terms ) );
		} else {
			$original_terms_lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( (string) $original_terms ) : strtolower( (string) $original_terms );
		}

		$terms = $this->prep_terms( $terms );
		$terms = array_map( 'trim', $terms );
		$terms = array_filter( $terms, 'strlen' );

		if ( post_password_required() ) {
			return apply_filters( 'searchwp_th_password_required_message', __( 'There is no excerpt because this is a protected post.' ) );
		}

		// by default we're going to use the post excerpt (in case there are no terms in the excerpt)
		$excerpt = empty( $excerpt ) ? $post->post_excerpt : $excerpt;

		if ( empty( $terms ) ) {
			return get_the_excerpt( $post->ID );
		}

		$excerpt = str_replace( array( "\r", "\n" ), ' ', $excerpt );
		$excerpt = sanitize_text_field( $excerpt );

		// grab all of the content and break it out into a clean array
		$haystack = empty( $excerpt ) ? $post->post_content : $excerpt;
		$haystack = preg_replace( "/\r\n|\r|\n/", ' ', $haystack );
		$haystack = $this->pre_process_content( $haystack );
		$haystack = wp_strip_all_tags( $haystack );

		$haystack_tmp = $haystack;

		$haystack = explode( ' ', $haystack );
		$haystack = array_filter( $haystack );
		$haystack = array_values( $haystack );

		$haystack_lower = function_exists( 'mb_strtolower' ) ? array_map( 'mb_strtolower', $haystack ) : array_map( 'strtolower', $haystack );

		$terms = function_exists( 'mb_strtolower' ) ? array_map( 'mb_strtolower', $terms ) : array_map( 'strtolower', $terms );

		// First check for a whole match, that'd be the least amount of work to do
		$terms_pos = stripos( $haystack_tmp, $original_terms_lower );
		if ( false !== $terms_pos ) {

			$original_terms_in_excerpt = substr( $haystack_tmp, $terms_pos, strlen( implode( ' ', $original_terms ) ) );
			$whole_match_flag_alpha    = substr( $haystack_tmp, 0, $terms_pos );
			$whole_match_flag_beta     = substr( $haystack_tmp, $terms_pos + strlen( $original_terms_in_excerpt) );

			// we're going to piece together our match using the two generated chunks, concatenated with the original search
			$chunks_1 = explode( ' ', trim( $whole_match_flag_alpha ) );
			$chunks_1 = count( $chunks_1 ) == 1 && empty( $chunks_1[0] ) ? array() : $chunks_1;
			$chunks_2 = explode( ' ', trim( $whole_match_flag_beta ) );
			$chunks_2 = count( $chunks_2 ) == 1 && empty( $chunks_2[0] ) ? array() : $chunks_2;

			$buffer       = floor( ( $this->number_of_words - str_word_count( $original_terms_lower ) ) / 2 );
			$buffer_alpha = count( $chunks_2 ) < $buffer ? $buffer + ( $buffer - count( $chunks_2 ) ) : $buffer;
			$buffer_alpha = empty( $chunks_1 ) ? 0 : $buffer_alpha;
			$buffer_beta  = count( $chunks_1 ) < $buffer ? $buffer + ( $buffer - count( $chunks_1 ) ) : $buffer;
			$buffer_beta  = empty( $chunks_2 ) ? 0 : $buffer_beta;

			$excerpt_alpha = $buffer_alpha ? array_slice( $chunks_1, 0 - $buffer_alpha ) : array();
			$excerpt_beta  = $buffer_beta ? array_slice( $chunks_2, 0, $buffer_beta ) : array();

			$excerpt = implode( ' ', $excerpt_alpha ) . ' ';
			$excerpt .= '<' . $this->highlight_el . ' class="searchwp-highlight">' . esc_html( $original_terms_in_excerpt ) . '</' . $this->highlight_el . '>';

			// Pad with a trailing space unless the first item in $excerpt_beta is punctuation.
			// TODO: This may not always be applicable/true, but our use case was a period after the highlight.
			$trailing_pad = ' ';
			if ( ! empty( $excerpt_beta ) && ! empty( $excerpt_beta[0] ) && 0 === strlen( $excerpt_beta[0] ) ) {
				if ( preg_match( '/([[:punct:]])/iu', (string) preg_quote( $excerpt_beta[0], '/' ) ) ) {
					$trailing_pad = '';
				}
			}

			$excerpt .= $trailing_pad . implode( ' ', $excerpt_beta );

			if ( $apply_native_wp_filter ) {
				$excerpt = apply_filters( 'get_the_excerpt', $excerpt );
			}

			return $excerpt;
		}

		// find the first applicable search term (based on character length)
		$flag = false;
		foreach ( $terms as $term ) {
			if (
				! in_array( $term, $this->common, true )
				&& $this->min_word_length <= strlen( $term )
				&& in_array( $term, $haystack_lower, true )
			) {
				$flag = $term;
				break;
			}
		}

		// if the first pass didn't yield a result, it's likely that the match is flanked by punctuation
		// or a stem was searched for but there's only a non-stem match
		if ( empty( $flag ) ) {
			// put the string back to find the match itself
			$haystack_tmp = implode( ' ', $haystack_lower );
			foreach ( $terms as $term ) {
				if ( false !== strpos( $haystack_tmp, $term ) ) {
					// this term is in the string somewhere, find the first occurrence
					$pattern = "/\b([[:punct:]]*" . $term . "[[:punct:]]*)\b/iu";
					preg_match( $pattern, preg_quote( $haystack_tmp, '/' ), $matches );
					if ( ! empty( $matches ) ) {
						// use this new flag
						$flag = $matches[0];
						break;
					}
				}
			}
		}

		// Determine which occurrence of the flag to utilize when scouting a highlight
		$flag_occurrence = absint( apply_filters( 'searchwp_term_highlight_occurrence', 1, array(
			'query' => $original_terms,
		) ) );

		// There's a chance the occurrence is too high (e.g. there's only one match but the dev wants the 2nd)
		// so let's keep track of the occurrences and fall back if we have to...
		$occurrences = array();

		if ( ! empty( $flag ) ) {
			foreach ( $haystack as $haystack_key => $haystack_term ) {
				$haystack_term = function_exists( 'mb_strtolower' ) ? mb_strtolower( $haystack_term ) : strtolower( $haystack_term );

				if ( ! apply_filters( 'searchwp_th_partial_matches', false ) ) {
					// find an exact match
					$found_occurrence = preg_replace( "/\p{P}/u", '', $haystack_term ) === $flag;
				} else {
					// find a partial match
					$found_occurrence = false !== strpos( $haystack_term, $flag );
				}

				if ( $found_occurrence ) {
					$occurrences[] = $haystack_key;

					// Stop checking as soon as we have enough occurrences
					if ( count( $occurrences ) >= $flag_occurrence ) {
						break;
					}
				}
			}
		}

		if ( ! empty( $occurrences ) ) {

			// Check to make sure the desired occurrence actually occurs
			if ( $flag_occurrence > count( $occurrences ) ) {
				$flag_occurrence = count( $occurrences );
			}

			preg_match( '/' . preg_quote( $haystack_term, '/' ) . '/i', $haystack_tmp, $term_pos, PREG_OFFSET_CAPTURE );

			$occurrence_position = absint( $term_pos[ $flag_occurrence - 1 ][1] );

			$original_terms_in_excerpt = substr( $haystack_tmp, $occurrence_position, strlen( $haystack_term ) );

			$match_flag_alpha = substr( $haystack_tmp, 0, $occurrence_position );
			$match_flag_beta  = substr( $haystack_tmp, $occurrence_position + strlen( $haystack_term ) );

			// we're going to piece together our match using the two generated chunks, concatenated with the original search
			$chunks_1 = explode( ' ', trim( $match_flag_alpha ) );
			$chunks_1 = array_filter( $chunks_1 );
			$chunks_1 = array_values( $chunks_1 );
			$chunks_1 = count( $chunks_1 ) == 1 && empty( $chunks_1[0] ) ? array() : $chunks_1;

			$chunks_2 = explode( ' ', trim( $match_flag_beta ) );
			$chunks_2 = array_filter( $chunks_2 );
			$chunks_2 = array_values( $chunks_2 );
			$chunks_2 = count( $chunks_2 ) == 1 && empty( $chunks_2[0] ) ? array() : $chunks_2;

			$buffer       = floor( ( $this->number_of_words - 1 ) / 2 );
			$buffer_alpha = count( $chunks_2 ) < $buffer ? $buffer + ( $buffer - count( $chunks_2 ) ) : $buffer;
			$buffer_alpha = empty( $chunks_1 ) ? 0 : $buffer_alpha;
			$buffer_beta  = count( $chunks_1 ) < $buffer ? $buffer + ( $buffer - count( $chunks_1 ) ) : $buffer;
			$buffer_beta  = empty( $chunks_2 ) ? 0 : $buffer_beta;

			$excerpt_alpha = $buffer_alpha ? array_slice( $chunks_1, 0 - $buffer_alpha ) : array();
			$excerpt_beta  = $buffer_beta ? array_slice( $chunks_2, 0, $buffer_beta ) : array();

			$excerpt = implode( ' ', $excerpt_alpha ) . ' ';
			$excerpt .= $original_terms_in_excerpt;
			$excerpt .= ' ' . implode( ' ', $excerpt_beta );

			$excerpt = $this->apply_highlight( $excerpt, $terms );
		} else {
			// This is worst case, nothing was found, so just make sure to truncate it to the proper number of terms
			$excerpt = array_slice( $haystack, 0, $this->number_of_words );
			$excerpt = implode( ' ', $excerpt );

			$excerpt = $this->apply_highlight( $excerpt, $terms );
		}

		if ( $apply_native_wp_filter ) {
			$excerpt = apply_filters( 'get_the_excerpt', $excerpt );
		}

		return $excerpt;
	}

	/**
	 * Pre-process content (e.g. Shortcodes, custom)
	 *
	 * @param $content
	 *
	 * @return string
	 */
	function pre_process_content( $content ) {
		// Unserialization should be handled natively by WordPress

		// Shortcode handling
		if ( apply_filters( 'searchwp_th_strip_shortcodes', true ) ) {
			$content = strip_shortcodes( $content );
		} elseif ( apply_filters( 'searchwp_th_do_shortcode', true ) ) {
			$content = do_shortcode( $content );
		}

		$content = apply_filters( 'searchwp_th_pre_process_content', $content );

		return $content;
	}

	/**
	 * Flatten a multidimensional array into a single string that we can work with
	 *
	 * @param array $array The source array.
	 *
	 * @return array The flattened array.
	 *
	 * @since 3.0
	 */
	function array_flatten( $array ) {
		$return = '';

		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$return .= ' ' . $this->array_flatten( $value );
			} else {
				$return .= ' ' . $value;
			}
		}

		return $return;
	}
}

function searchwp_init_global_highlight_functions() {
	/**
	 * Automatically generate an excerpt that has at least one search term in it, whether the content is inside
	 * the main editor or within any Custom Field (if the data is a string).
	 *
	 * @param int $post_id
	 * @param string $custom_field
	 * @param null $query
	 *
	 * @return string
	 */
	if ( ! function_exists( 'searchwp_term_highlight_get_the_excerpt_global' ) ) {
		function searchwp_term_highlight_get_the_excerpt_global( $post_id = 0, $custom_field = '', $query = null ) {
			global $post;

			$highlighter = new SearchWPHighlighter();
			$highlighter->init();

			if ( empty( $post ) || is_null( $post ) || ! class_exists( 'SearchWPIndexer' ) ) {
				return '';
			}

			$original_post = $post;

			if ( empty( $post_id ) ) {
				// Use global post ID if available.
				if ( isset( $post->ID ) && ! empty( $post->ID ) ) {
					$post_id = $post->ID;
				} else {
					// Get global post ID instead.
					if ( function_exists( 'get_the_ID' ) ) {
						$post_id = get_the_ID();
					}
				}

				// couldn't retrieve the post ID so we need to short circuit.
				if ( empty( $post_id ) ) {
					return '';
				}
			}

			if ( empty( $query ) ) {
				$query = get_search_query();
			}

			$query = $highlighter->prep_terms( $query );

			$excerpt = '';
			$default_excerpt = '';

			if ( empty( $custom_field ) ) {
				// retrieve the default excerpt
				$post_id = absint( $post_id );
				$post = get_post( $post_id );
				setup_postdata( $post );

				// grab all content (default excerpt and all Custom Fields) and concatenate it
				$excerpt = $default_excerpt = $highlighter->get_the_excerpt( $query, null, false );
			} else {
				// a custom field was specified so we're going to use that to generate the excerpt
				$custom_field = sanitize_text_field( $custom_field );
			}

			$indexer = new SearchWPIndexer();

			// exclude all the keys that are excluded in SearchWP itself
			$excluded_custom_field_keys = apply_filters( 'searchwp_excluded_custom_fields', array(
				'_edit_lock',
				'_wp_page_template',
				'_wp_attached_file',
				'_edit_last',
				'_wp_old_slug',
				'_searchwp_indexed',
				'_searchwp_last_index',
			) );

			if ( empty( $custom_field ) && false === strpos( $excerpt, 'searchwp-highlight' ) ) {
				// wasn't found in the main excerpt, so we're going to loop through the Custom Fields until we find one
				// custom fields next
				$custom_field_keys = apply_filters( 'searchwp_th_meta_keys', get_post_custom_keys( $post_id ) );

				if ( ! empty( $custom_field_keys ) ) {
					$better_excerpt = false;
					$the_post = get_post( $post_id );
					foreach ( $custom_field_keys as $custom_field_key ) {

						if ( function_exists( 'SWP' ) && method_exists( SWP(), 'is_used_meta_key' ) ) {
							if ( ! SWP()->is_used_meta_key( $custom_field_key, $the_post ) ) {
								continue;
							}
						}

						if ( ! in_array( $custom_field_key, $excluded_custom_field_keys, true ) ) {

							$meta_value = get_post_meta( $post_id, $custom_field_key );
							$meta_value = apply_filters( 'searchwp_th_pre_process_meta_value', $meta_value, $custom_field_key, $post_id );

							foreach ( $meta_value as $meta_value_entry ) {
								// Find a reduced case of the target term(s)
								$reduced_meta_value = (string) $indexer->parse_variable_for_terms( $meta_value_entry );

								$this_custom_field_value = $highlighter->pre_process_content( $reduced_meta_value );
								$excerpt = $highlighter->get_the_excerpt( $query, $this_custom_field_value, false );

								if ( false !== strpos( $excerpt, 'searchwp-highlight' ) ) {

									// Because we had to avoid using the output from pre_process_content() which destroys all formatting
									// we could technically have any kind of data type here (e.g. multidimensional array) so we need to
									// work around that by making the meta record a string if it's not one
									if ( is_array( $meta_value_entry ) ) {
										$meta_value_entry = $highlighter->array_flatten( $meta_value_entry );
									}

									// Redefine to the original excerpt because right now it's the reduced value
									$excerpt = $highlighter->get_the_excerpt( $query, $meta_value_entry, false );
									$better_excerpt = true;

									break;
								}
							}

							// If we found a better excerpt in a custom field, break out
							if ( ! empty( $better_excerpt ) ) {
								break;
							}
						}
					}

					if ( ! $better_excerpt ) {
						$excerpt = $default_excerpt;
					}
				}
			} elseif ( ! empty( $custom_field ) ) {
				$custom_field_value = get_post_meta( $post_id, $custom_field, true );
				$custom_field_value = $highlighter->pre_process_content( $custom_field_value );
				$excerpt = $highlighter->get_the_excerpt( $query, $custom_field_value, false );
			}

			// last resort: try to pluck an excerpt from the post content even when
			// a proper Excerpt was defined (but did not have a highlight match)
			$proper_excerpt = $excerpt; // save this for later in case the post
			// content doesn't have a match either
			if ( false === strpos( $excerpt, 'searchwp-highlight' ) ) {
				$post_content = ! empty( $post->post_content ) ? apply_filters( 'the_content', $post->post_content ) : $excerpt;
				$post_content = $highlighter->pre_process_content( $post_content );

				$excerpt = $highlighter->get_the_excerpt( $query, $post_content );

				// if the post content didn't have a match either, fall back to the proper Excerpt
				if ( false === strpos( $excerpt, 'searchwp-highlight' ) ) {
					$excerpt = $proper_excerpt;
				}
			}

			// reset the post object
			$post = $original_post;

			// return the best excerpt we could find...
			return $excerpt;
		}
	}

	/**
	 * @param int $post_id
	 * @param string $custom_field
	 * @param null $query
	 */
	if ( ! function_exists( 'searchwp_term_highlight_the_excerpt_global' ) ) {
		function searchwp_term_highlight_the_excerpt_global( $post_id = 0, $custom_field = '', $query = null ) {
			echo wp_kses_post( stripslashes( searchwp_term_highlight_get_the_excerpt_global( $post_id, $custom_field, $query ) ) );
		}
	}
}
