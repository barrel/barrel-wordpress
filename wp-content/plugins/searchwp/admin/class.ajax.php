<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_Admin_Ajax is responsible for implementing admin-only AJAX callbacks
 *
 * @since 2.8
 */
class SearchWP_Admin_Ajax {

	/**
	 * SearchWP_Admin_Ajax constructor.
	 */
	function __construct() {
		add_action( 'wp_ajax_searchwp_get_tax_terms', array( $this, 'get_tax_terms' ) );
		add_action( 'wp_ajax_searchwp_get_meta_keys', array( $this, 'get_meta_keys' ) );
	}

	/**
	 * Retrieve and return taxonomy terms encoded as JSON, formatted for select2
	 *
	 * @since 2.8
	 */
	function get_tax_terms() {
		if ( empty( $_REQUEST['tax'] ) || ! taxonomy_exists( $_REQUEST['tax'] ) ) {
			wp_send_json_error();
		}
		
		$tax = sanitize_text_field( $_REQUEST['tax'] );
		
		check_ajax_referer( 'swp_tax_terms_' . $tax );

		if ( empty( $_REQUEST['q'] ) ) {
			echo wp_json_encode( array() );
		}

		// search for terms
		$taxonomy_args = array(
			'hide_empty' => false,
			'name__like' => sanitize_text_field( $_REQUEST['q'] ),
			'fields'     => 'id=>name',
		);

		$terms = get_terms( $tax, $taxonomy_args );

		$response = array(
			'total_count'           => count( $terms ),
			'incomplete_results'    => false,
			'items'                 => array(),
		);

		foreach ( $terms as $term_id => $term ) {
			$response['items'][] = array(
				'id'    => $term_id,
				'text'  => $term,
			);
		}

		echo wp_json_encode( $response );

		die();
	}

	/**
	 * Retrieve and return unique meta_key values encoded as JSON, formatted for select2 autocomplete
	 *
	 * @since 2.8
	 */
	function get_meta_keys() {

		global $wpdb;

		check_ajax_referer( 'swp_search_meta_keys' );

		if ( empty( $_REQUEST['q'] ) ) {
			echo wp_json_encode( array() );
		}

		// search for keys
		/** @noinspection SqlDialectInspection */
		$meta_keys = $wpdb->get_col( $wpdb->prepare( "
			SELECT meta_key
			FROM $wpdb->postmeta
			WHERE meta_key != %s
			AND meta_key != %s
			AND meta_key != %s
			AND meta_key != %s
			AND meta_key NOT LIKE %s
			AND meta_key LIKE %s
			GROUP BY meta_key
		",
			'_' . SEARCHWP_PREFIX . 'indexed',
			'_' . SEARCHWP_PREFIX . 'content',
			'_' . SEARCHWP_PREFIX . 'needs_remote',
			'_' . SEARCHWP_PREFIX . 'skip',
			'_oembed_%',
			'%' . $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) . '%'
		) );

		// allow devs to filter this list
		$meta_keys = array_unique( apply_filters( 'searchwp_custom_field_keys', $meta_keys ) );

		// sort the keys alphabetically
		if ( $meta_keys ) {
			natcasesort( $meta_keys );
		} else {
			$meta_keys = array();
		}

		$response = array(
			'total_count'           => count( $meta_keys ),
			'incomplete_results'    => false,
			'items'                 => array(),
		);

		foreach ( $meta_keys as $meta_key ) {
			$response['items'][] = array(
				'id'    => $meta_key,
				'text'  => $meta_key,
			);
		}

		echo wp_json_encode( $response );

		die();
	}
}

// Kickoff
new SearchWP_Admin_Ajax();