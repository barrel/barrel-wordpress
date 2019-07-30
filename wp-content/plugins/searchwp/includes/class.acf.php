<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SearchWP_ACF is responsible for integration with Advanced Custom Fields.
 * Based on FacetWP's ACF integration class https://facetwp.com/
 *
 * @since 3.0
 */
class SearchWP_ACF {

	public $fields      = array(); // Stores all ACF fields
	public $repeatables = array(); // Stores 'root' field name (no LIKE character added)
	public $meta_keys   = array();

	/**
	 * SearchWP_ACF Constructor.
	 *
	 * @since 3.0
	 */
	public function __construct() {
		add_action( 'searchwp_settings_before\default', array( $this, 'init_meta_groups' ) );

		// Prevent interference with ACF oEmbed.
		add_filter( 'searchwp_short_circuit', array( $this, 'maybe_short_circuit' ), 5 );
	}

	/**
	 * Initialize our custom meta groups for ACF repeatables.
	 *
	 * @since 3.0
	 */
	public function init_meta_groups() {
		global $searchwp;
		$acf_version = acf()->settings['version'];

		if ( ! version_compare( $acf_version, '5.0', '>=' ) ) {
			return;
		}

		$this->get_fields();

		foreach ( SWP()->postTypes as $post_type ) {
			add_filter( 'searchwp_custom_field_keys_' . $post_type, array( $this, 'customize_acf_custom_fields' ), 10, 2 );
		}

		add_filter( 'searchwp_meta_groups', array( $this, 'add_acf_repeatables_meta_group' ), 10, 2 );
	}

	/**
	 * Check to see if any engine is already using one of our repeatables (e.g. set up prior to version 3.0)
	 *
	 * @since 3.0
	 *
	 * @param string $repeatable_key The key to locate in the engine config.
	 * @param string $post_type The post type for this check.
	 *
	 * @return bool Whether the key is being used in the engine config.
	 */
	public function any_engine_maybe_using_repeatable_key( $repeatable_key, $post_type ) {
		$settings = SWP()->settings;

		$in_use = false;

		foreach ( $settings['engines'] as $engine => $config ) {
			foreach ( $config as $post_type => $post_type_config ) {
				if ( empty( $post_type_config['weights']['cf'] ) ) {
					continue;
				}

				$meta_keys = wp_list_pluck( $post_type_config['weights']['cf'], 'metakey' );
				if ( ! empty( $meta_keys ) ) {
					$meta_keys = array_values( $meta_keys );
				}

				$matches = preg_grep( '/^' . $repeatable_key . '_/', $meta_keys );

				if ( empty( $matches ) ) {
					$matches = preg_grep( '/^' . $repeatable_key . '_%/', $meta_keys );
				}

				if ( ! empty( $matches ) ) {
					$in_use = true;
					break;
				}
			}
		}

		return $in_use;
	}

	/**
	 * Customize the Custom Fields dropdown to include only applicable repeatables, remove
	 * ACF reference meta keys, and include our customized repeatables (e.g. prep for Meta Group)
	 *
	 * @since 3.0
	 *
	 * @param array $keys The incoming meta keys.
	 * @param string $post_type The current post type.
	 *
	 * @return array Customized meta keys that meet our criteria.
	 */
	public function customize_acf_custom_fields( $keys, $post_type ) {
		// We want to return only the repeatables that are actually in use for this post type
		$like_matches = array_values( array_intersect( $this->repeatables, $keys ) );

		// Because ACF creates meta keys based on the groups we're trying to set up, they can get extensive.
		// Let's assume that the groups will be used and therefore the children entries are redundant.
		$remove_acf_repeatable_children = apply_filters( 'searchwp_acf_remove_repeatable_children', true, array(
			'post_type' => $post_type,
		) );
		if ( $remove_acf_repeatable_children ) {
			foreach ( $like_matches as $key => $repeatable_key ) {
				$remove_acf_repeatable_child = apply_filters( 'searchwp_acf_remove_repeatable_child', true, array(
					'metakey'   => $repeatable_key,
					'post_type' => $post_type,
				) );
				// Before we remove keys we need to make sure the engine isn't using the keys.
				if ( $remove_acf_repeatable_child && ! $this->any_engine_maybe_using_repeatable_key( $repeatable_key, $post_type ) ) {
					$keys = preg_grep( '/^' . $repeatable_key . '/', $keys, PREG_GREP_INVERT );
					$keys = preg_grep( '/^_' . $repeatable_key . '/', $keys, PREG_GREP_INVERT );
				}
			}
		}

		// ACF also makes 'private' versions of all fields which are references to other IDs used
		// interally by ACF but will likely never be applicable to us, so let's remove them.
		$remove_acf_refs = apply_filters( 'searchwp_acf_remove_field_references', false, array(
			'post_type' => $post_type,
		) );
		if ( $remove_acf_refs ) {
			foreach ( $this->fields as $acf_field_key ) {
				$remove_acf_ref = apply_filters( 'searchwp_acf_remove_field_reference', true, array(
					'metakey'   => $acf_field_key,
					'post_type' => $post_type,
				) );
				if ( $remove_acf_ref ) {
					$keys = preg_grep( '/^_' . $acf_field_key . '/', $keys, PREG_GREP_INVERT );
				}
			}
		}

		// Add our 'LIKE' character last
		foreach ( $like_matches as $key => $like_match ) {
			$like_matches[ $key ] = $like_match . '_%';
		}

		$meta_keys = array_unique( array_merge( $like_matches, $keys ) );

		$this->meta_keys[ $post_type ] = $meta_keys;

		return $meta_keys;
	}

	/**
	 * Set up our ACF meta group for the Custom Fields dropdown in the engine config.
	 *
	 * @since 3.0
	 *
	 * @param array $meta_groups The incoming meta groups.
	 * @param array $args The arguments for this callback.
	 *
	 * @return array The customized meta groups including our ACF repeatables.
	 */
	public function add_acf_repeatables_meta_group( $meta_groups, $args ) {
		// We want to return only the repeatables that are actually in use for this post type
		// Which means we need to check the existing meta keys for our partial match

		// $this->repeatables does NOT have the % flag, but because we have already
		// filtered the meta keys via customize_acf_custom_fields() they DO
		// So we need to do our work after having added our own % flag first.
		$repeatables = array();
		foreach ( $this->repeatables as $repeatable ) {
			$repeatables[] = $repeatable . '_%';
		}
		$meta_keys = array_values( array_intersect( $repeatables, $this->meta_keys[ $args['post_type'] ] ) );

		$meta_groups['searchwp_acf_repeatables'] = array(
			'label'    => __( 'ACF Repeatable', 'searchwp' ),
			'metakeys' => $meta_keys,
		);

		$meta_groups = apply_filters( 'searchwp_meta_groups_acf_repeatables', $meta_groups, $args );

		return $meta_groups;
	}

	/**
	 * Retrieve all registered ACF fields
	 *
	 * @since 3.0
	 */
	public function get_fields() {
		add_action( 'pre_get_posts', array( $this, 'suppress_filters' ) );
		$field_groups = acf_get_field_groups();
		remove_action( 'pre_get_posts', array( $this, 'suppress_filters' ) );

		$fields = array();

		foreach ( $field_groups as $field_group ) {
			$fields = acf_get_fields( $field_group );

			if ( ! empty( $fields ) ) {
				$this->get_repeatable_keys( $fields, $field_group );
			}
		}

		return $this->repeatables;
	}

	/**
	 * Recursive function to find all repeatable ACF fields keys.
	 *
	 * @since 3.0
	 *
	 * @param array $fields The fields for this field group.
	 * @param array $field_group The field group itself.
	 */
	public function get_repeatable_keys( $fields, $field_group ) {
		foreach ( $fields as $field ) {
			$this->fields[] = $field['name'];

			if ( 'repeater' == $field['type'] || 'group' == $field['type'] ) {
				if ( empty( $field['sub_fields'] ) ) {
					continue;
				}

				$this->repeatables[] = $field['name'];
				$this->get_repeatable_keys( $field['sub_fields'], $field_group );
			}

			if ( 'flexible_content' == $field['type'] ) {
				$this->repeatables[] = $field['name'];
				foreach ( (array) $field['layouts'] as $layout ) {
					if ( empty( $field['sub_fields'] ) ) {
						continue;
					}

					$this->get_repeatable_keys( $layout['sub_fields'], $field_group );
				}
			}
		}
	}

	/**
	 * Callback to suppress filters when retrieving ACF Field Groups
	 *
	 * @since 3.0
	 */
	public function suppress_filters( $query ) {
		$query->set( 'suppress_filters', true );
	}

	/**
	 * Callback to short circuit SearchWP if ACF is performing internal functionality.
	 *
	 * @since 3.0
	 */
	public function maybe_short_circuit( $short_circuit ) {
		$acf_actions = apply_filters( 'searchwp_acf_short_circuit_actions', array(
			'acf/fields/oembed/search',
			'acf/fields/post_object/query',
			'acf/fields/relationship/query',
		) );

		return $short_circuit ? $short_circuit : isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $acf_actions );
	}
}

if ( function_exists( 'acf' ) ) {
	new SearchWP_ACF();
}
