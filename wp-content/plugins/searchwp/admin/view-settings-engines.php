<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

$indexer = new SearchWPIndexer();
$indexer->update_running_counts();

// The data store
$data = array();

$nonces = array(
	'basic_auth',
	'engines',
	'get_index_stats',
	'get_tax_terms',
	'index_dirty',
	'reset_index',
	'initial_settings',
	'legacy_engines',
);

// We need a reference to all post type objects, their taxonomies, and their metadata
foreach ( SWP()->postTypes as $post_type ) {
	$post_type_obj = get_post_type_object( $post_type );

	// Get all the taxonomies for this post type in the format Vue wants
	$taxonomy_objects = get_object_taxonomies( $post_type, 'objects' );
	$taxonomies = array();
	foreach ( $taxonomy_objects as $taxonomy_key => $taxonomy ) {

		$tax_label = $taxonomy->label;

		if ( apply_filters( 'searchwp_engine_use_taxonomy_name', false ) ) {
			$tax_label = $taxonomy->name;
		}

		$taxonomies[] = array(
			'name' => $taxonomy->name,
			'value' => $taxonomy->name,
			'label' => $tax_label,
		);

		$nonces[] = 'tax_' . $taxonomy->name . '_' . $post_type;
	}

	$data['objects'][ $post_type ] = array(
		'name' => $post_type,
		'label' => $post_type_obj->labels->name,
		'taxonomies' => $taxonomies,
		'meta_keys' => searchwp_get_meta_keys_for_post_type( $post_type ),
		'supports' => searchwp_get_supports_for_post_type( $post_type_obj ),
	);

	if ( 'attachment' == $post_type
			|| apply_filters( "searchwp_enable_parent_attribution_{$post_type}", false )
	) {
		$data['objects'][ $post_type ]['attribution'] = 'parent';
	} else {
		$data['objects'][ $post_type ]['attribution'] = 'id';
	}
}

// We need the index stats
$data['index_stats'] = SWP()->ajax->get_index_stats();

// Determine stemming support
$data['stemming_supported'] = SWP()->is_stemming_supported_in_locale();

$max_weight = apply_filters( 'searchwp_weight_max', 100 );

$enabled_post_types = SWP()->get_enabled_post_types_across_all_engines();

// Misc data
$data['misc'] = array(
	'max_weight' => absint( $max_weight ),
	'alternate_indexer' => SWP()->is_using_alternate_indexer(),
	'initial_settings_saved' => searchwp_get_setting( 'initial_settings' ),
	'legacy_settings' => searchwp_get_setting( 'legacy_engines' ),
	'index_dirty' => searchwp_get_setting( 'index_dirty' ),
	'ziparchive' => class_exists( 'ZipArchive' ),
	'domdocument' => class_exists( 'DOMDocument' ),
	'empty_engines' => empty( $enabled_post_types ),
	'stats_url' => admin_url( 'index.php?page=searchwp-stats' ),
	'excluded_from_search' => get_post_types(
		array(
			'exclude_from_search' => true,
			'_builtin'            => false,
		)
	),
	'mimes' => array(
		__( 'All Documents', 'searchwp' ),
		__( 'PDFs', 'searchwp' ),
		__( 'Plain Text', 'searchwp' ),
		__( 'Images', 'searchwp' ),
		__( 'Video', 'searchwp' ),
		__( 'Audio', 'searchwp' ),
		__( 'Office Documents', 'searchwp' ),
		__( 'OpenOffice Documents', 'searchwp' ),
		__( 'iWork Documents', 'searchwp' ),
	),
);

$advanced_settings = searchwp_get_option( 'advanced' );
$data['misc']['admin_search'] = ! empty( $advanced_settings['admin_search'] );

// We need the configurations for all existing engines
$data['engines'] = searchwp_get_setting( 'engines' );

if ( empty( $data['engines'] ) ) {
	$data['engines']['default'] = $data['engine_model'];
	unset( $data['engines']['default']['searchwp_engine_label'] );
}

// Taxonomy rules are stored as CSV strings, but we need formatted objects for Vue dropdowns
$data = SWP()->ajax->normalize_taxonomy_options( $data, 'exclude_' );
$data = SWP()->ajax->normalize_taxonomy_options( $data, 'limit_to_' );

// Ensure that all expected attributes are in place and formatted
$data = SWP()->ajax->normalize_post_types_to_objects( $data );

// Provide Vue with an accurate engine model to use when creating supplemental engines
$data['engine_model'] = SWP()->ajax->generate_engine_model( $data );

SWP()->ajax->enqueue_script(
	'settings',
	array(
		'nonces' => $nonces,
		'data' => $data,
	)
);

?>

<div id="searchwp-settings"></div>
