<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Returns the saved weight (or a default if there's no saved data)
 *
 * @param array $weights
 * @param string $type
 * @param $subtype
 * @return int
 * @since 1.0
 */
function searchwp_get_engine_weight( $weights = array(), $type = 'title', $subtype = null )  {
	$weight = 1;

	if ( ! is_array( $weights ) ) {
		$weights = array();
	}

	switch ( $type ) {
		case 'title':
			$weight = isset( $weights['title'] ) ? floatval( $weights['title'] ) : 20;
			break;

		case 'content':
			$weight = isset( $weights['content'] ) ? floatval( $weights['content'] ) : 2;
			break;

		case 'slug':
			$weight = isset( $weights['slug'] ) ? floatval( $weights['slug'] ) : 10;
			break;

		case 'tax':
			$weight = 5;
			if ( is_string( $subtype ) && isset( $weights['tax'][ $subtype ] ) ) {
				$weight = floatval( $weights['tax'][ $subtype ] );
			}
			break;

		case 'excerpt':
			$weight = isset( $weights['excerpt'] ) ? floatval( $weights['excerpt'] ) : 6;
			break;

		case 'comment':
			$weight = isset( $weights['comment'] ) ? floatval( $weights['comment'] ) : 1;
			break;
	}

	return esc_attr( $weight );
}


/**
 * Echoes the markup for the search engine settings UI
 *
 * @param string $engine The engine name
 * @return bool
 * @since 1.0
 */
function searchwp_engine_settings_template( $engine = 'default' ) {

	$searchwp = SWP();

	$settings = $searchwp->settings;
	$engine = sanitize_key( $engine );

	if ( 'default' != $engine && is_array( $settings ) && ! array_key_exists( 'engines', $settings ) ) {
		if ( ! is_array( $settings['engines'] ) || ! array_key_exists( $engine, $settings['engines'] ) ) {
			return false;
		}
	}

	$engineSettings = isset( $settings['engines'] ) && isset( $settings['engines'][ $engine ] ) ? $settings['engines'][ $engine ] : false;

	// set up nonce for Custom Field searching
	$cf_nonce = wp_create_nonce( 'swp_search_meta_keys' );

	// retrieve list of all post types
	$post_types = array_merge(
		array(
			'post'          => 'post',
			'page'          => 'page',
			'attachment'    => 'attachment',
		),
		get_post_types(
			array(
				'exclude_from_search'   => false,
				'_builtin'              => false,
			)
		)
	);

	// devs can customize which post types are indexed, it doesn't make
	// sense to list post types that were excluded (or included (e.g. post types that don't
	// allow filtration of the exclude_from_search arg))
	$indexed_post_types = apply_filters( 'searchwp_indexed_post_types', $searchwp->postTypes );
	if ( is_array( $indexed_post_types ) ) {
		$indexed_post_types = array_merge( $post_types, $indexed_post_types );
		$indexed_post_types = array_unique( $indexed_post_types );
	}

	if ( is_array( $indexed_post_types ) ) {
		$post_types = $indexed_post_types;
	}

	if ( 'swpengine' == $engine ) {
		$engine = '{{ swp.engine }}';
	}

	?>

<div class="swp-tabbable swp-group">
	<ul class="swp-nav swp-tabs">
		<?php foreach ( $post_types as $post_type ) : $post_type = get_post_type_object( $post_type ); ?>
			<?php if ( 'attachment' != $post_type->name ) : ?>
				<?php
					$engine = esc_attr( $engine );
					$post_type->name = esc_attr( $post_type->name );
				?>
				<li data-swp-engine="swp-engine-<?php echo esc_attr( $engine ); ?>-<?php echo esc_attr( $post_type->name ); ?>" class="">
					<span>
						<?php $enabled = ! empty( $engineSettings[ $post_type->name ]['enabled'] ); ?>
						<input type="checkbox" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][enabled]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>" value="1" <?php checked( $enabled ); ?>/>
						<label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>" title="<?php echo esc_attr( $post_type->labels->name ); ?>"><?php echo esc_html( $post_type->labels->name ); ?></label>
					</span>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
		<li data-swp-engine="swp-engine-<?php echo esc_attr( $engine ); ?>-attachment" class="">
			<span>
				<?php $enabled = ! empty( $engineSettings['attachment']['enabled'] ); ?>
				<!--suppress HtmlFormInputWithoutLabel -->
				<input type="checkbox" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][attachment][enabled]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_attachment" value="1" <?php checked( $enabled ); ?>/>
				<label for="swp_engine_<?php echo esc_attr( $engine ); ?>_posts"><?php esc_html_e( 'Media', 'searchwp' ); ?></label>
			</span>
		</li>
	</ul>
	<div class="swp-tab-content">
		<?php
		$ziparchive_available = class_exists( 'ZipArchive' );
		$domdocument_available = class_exists( 'DOMDocument' );
		?>
		<?php foreach ( $post_types as $post_type ) : $post_type = get_post_type_object( $post_type ); ?>
			<?php
				$engine = esc_attr( $engine );
				$post_type->name = esc_attr( $post_type->name );
			?>
			<div class="swp-engine swp-engine-<?php echo esc_attr( $engine ); ?> swp-group swp-tab-pane" id="swp-engine-<?php echo esc_attr( $engine ); ?>-<?php echo esc_attr( $post_type->name ); ?>">
				<h4 class="swp-post-type-heading"><?php echo esc_html( $post_type->label ); ?></h4>
				<?php $weights = ! empty( $engineSettings[ $post_type->name ]['weights'] ) ? $engineSettings[ $post_type->name ]['weights'] : array(); ?>
				<div class="swp-tooltip-content" id="swp-tooltip-weights-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">
					<?php echo wp_kses( __( 'These values add weight to results.<br /><br />A weight of 1 is neutral<br />Between 0 &amp; 1 lowers result weight<br />Over 1 increases result weight<br />Zero omits the result<br /><span class="searchwp-weight-warning">-1 excludes matches</span>', 'searchwp' ), array( 'br' => array(), 'span' => array( 'class' => array() ) ) ); ?>
				</div>

				<!-- <p class="description" style="padding-bottom:10px;"><?php esc_html_e( 'Applicable entries', 'searchwp' ); ?>: <?php $count_posts = wp_count_posts( $post_type->name ); echo 'attachment' != $post_type->name ? absint( $count_posts->publish ) : absint( $count_posts->inherit ); ?></p> -->

				<div class="swp-engine-weights">
					<table>
						<colgroup>
							<col class="swp-col-content-type" />
							<col class="swp-col-content-weight" />
						</colgroup>
						<thead>
							<tr>
								<th><?php esc_html_e( 'Content Type', 'searchwp' ); ?></th>
								<th><?php esc_html_e( 'Weight', 'searchwp' ); ?> <a class="swp-tooltip" href="#swp-tooltip-weights-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">?</a></th>
							</tr>
						</thead>
						<tbody>

							<?php if ( post_type_supports( $post_type->name, 'title' ) ) : ?>
								<tr>
									<td><label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_weights_title"><?php esc_html_e( 'Title', 'searchwp' ); ?></label></td>
									<td><input type="number" min="-1" step="0.1" class="small-text" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][weights][title]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_weights_title" value="<?php echo esc_attr( searchwp_get_engine_weight( $weights, 'title' ) ); ?>" /></td>
								</tr>
							<?php endif; ?>
							<?php if ( post_type_supports( $post_type->name, 'editor' ) || 'attachment' == $post_type->name ) : ?>
								<tr>
									<td><label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_weights_content"><?php if ( 'attachment' != $post_type->name ) { esc_html_e( 'Content', 'searchwp' ); } else { esc_html_e( 'Description', 'searchwp' ); } ?></label></td>
									<td><input type="number" min="-1" step="0.1" class="small-text" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][weights][content]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_weights_content" value="<?php echo esc_attr( searchwp_get_engine_weight( $weights, 'content' ) ); ?>" /></td>
								</tr>
							<?php endif; ?>
							<?php if ( 'page' == $post_type->name || $post_type->publicly_queryable ) : ?>
								<tr>
									<td><label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_weights_slug"><?php esc_html_e( 'Slug', 'searchwp' ); ?></label></td>
									<td><input type="number" min="-1" step="0.1" class="small-text" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][weights][slug]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_weights_slug" value="<?php echo esc_attr( searchwp_get_engine_weight( $weights, 'slug' ) ); ?>" /></td>
								</tr>
							<?php endif; ?>
							<?php
							$taxonomies = apply_filters( 'searchwp_lightweight_settings', false ) ? array() : get_object_taxonomies( $post_type->name );
							if ( is_array( $taxonomies ) && count( $taxonomies ) ) {
								foreach ( $taxonomies as $taxonomy ) {
									if ( 'post_format' != $taxonomy ) { // we don't want Post Formats here
										$taxonomy  = get_taxonomy( $taxonomy );
										$tax_label = ! empty( $taxonomy->labels->name ) ? $taxonomy->labels->name : $taxonomy->name;
										?>
										<tr>
											<td><label
													for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_weights_tax_<?php echo esc_attr( $taxonomy->name ); ?>"><?php echo esc_html( $tax_label ); ?></label>
											</td>
											<td><input type="number" min="-1" step="0.1" class="small-text"
											           name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][weights][tax][<?php echo esc_attr( $taxonomy->name ); ?>]"
											           id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_weights_tax_<?php echo esc_attr( $taxonomy->name ); ?>"
											           value="<?php echo esc_attr( searchwp_get_engine_weight( $weights, 'tax', $taxonomy->name ) ); ?>"/>
											</td>
										</tr>
									<?php }
								}
							}
							?>
							<?php if ( post_type_supports( $post_type->name, 'excerpt' ) || 'attachment' == $post_type->name ) : ?>
								<tr>
									<td><label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_weights_excerpt"><?php if ( 'attachment' != $post_type->name ) { esc_html_e( 'Excerpt', 'searchwp' ); } else { esc_html_e( 'Caption', 'searchwp' ); } ?></label></td>
									<td><input type="number" min="-1" step="0.1" class="small-text" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][weights][excerpt]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_weights_excerpt" value="<?php echo esc_attr( searchwp_get_engine_weight( $weights, 'excerpt' ) ); ?>" /></td>
								</tr>
							<?php endif; ?>
							<?php if ( post_type_supports( $post_type->name, 'comments' ) && 'attachment' != $post_type->name ) : ?>
								<?php if ( apply_filters( 'searchwp_index_comments', true ) ) : ?>
									<tr>
										<td><label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_weights_comment"><?php esc_html_e( 'Comments', 'searchwp' ); ?></label></td>
										<td><input type="number" min="-1" step="0.1" class="small-text" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][weights][comment]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_weights_comment" value="<?php echo esc_attr( searchwp_get_engine_weight( $weights, 'comment' ) ); ?>" /></td>
									</tr>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ( 'attachment' == $post_type->name ) : ?>
								<?php
								// check to see if the PDF weight has already been stored
								// if not, use default content weight
								$pdfweight = searchwp_get_engine_weight( $weights, 'content' );
								if ( isset( $engineSettings[ $post_type->name ]['weights']['cf'] ) && is_array( $engineSettings[ $post_type->name ]['weights']['cf'] ) && ! empty( $engineSettings[ $post_type->name ]['weights']['cf'] ) ) {
									$cfWeights = $engineSettings[ $post_type->name ]['weights']['cf'];
									foreach ( $cfWeights as $cfFlag => $cfWeight ) {
										if ( SEARCHWP_PREFIX . 'content' == $cfWeight['metakey'] ) {
											$pdfweight = floatval( $cfWeight['weight'] );
											break;
										}
									}
								}

								$arrayFlag = str_replace( '.', '', uniqid( 'swpp', true ) );
								?>
								<tr class="swp-custom-field">
									<td class="swp-custom-field-select">
										<label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_<?php echo esc_attr( $arrayFlag ); ?>_weight"><?php esc_html_e( 'Document content', 'searchwp' ); ?></label>
									</td>
									<td>
										<input type="hidden" style="display:none;" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][weights][cf][<?php echo esc_attr( $arrayFlag ); ?>][metakey]" value="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>content" />
										<input type="number" min="-1" step="0.1" class="small-text" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][weights][cf][<?php echo esc_attr( $arrayFlag ); ?>][weight]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_<?php echo esc_attr( $arrayFlag ); ?>_weight" value="<?php echo esc_attr( $pdfweight ); ?>" />
									</td>
								</tr>
								<?php
								// check to see if the PDF weight has already been stored
								// if not, use default content weight
								$pdf_meta_weight = searchwp_get_engine_weight( $weights, 'content' );
								if ( isset( $engineSettings[ $post_type->name ]['weights']['cf'] ) && is_array( $engineSettings[ $post_type->name ]['weights']['cf'] ) && ! empty( $engineSettings[ $post_type->name ]['weights']['cf'] ) ) {
									$cfWeights = $engineSettings[ $post_type->name ]['weights']['cf'];
									foreach ( $cfWeights as $cfFlag => $cfWeight ) {
										if ( SEARCHWP_PREFIX . 'pdf_metadata' == $cfWeight['metakey'] ) {
											$pdf_meta_weight = floatval( $cfWeight['weight'] );
											break;
										}
									}
								}

								$arrayFlag = str_replace( '.', '', uniqid( 'swpp', true ) );
								?>
								<tr class="swp-custom-field">
									<td class="swp-custom-field-select">
										<label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_<?php echo esc_attr( $arrayFlag ); ?>_weight"><?php esc_html_e( 'PDF metadata', 'searchwp' ); ?></label>
									</td>
									<td>
										<input type="hidden" style="display:none;" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][weights][cf][<?php echo esc_attr( $arrayFlag ); ?>][metakey]" value="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>pdf_metadata" />
										<input type="number" min="-1" step="0.1" class="small-text" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][weights][cf][<?php echo esc_attr( $arrayFlag ); ?>][weight]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_<?php echo esc_attr( $arrayFlag ); ?>_weight" value="<?php echo esc_attr( $pdf_meta_weight ); ?>" />
									</td>
								</tr>

							<?php endif; ?>

							<tr class="swp-custom-fields-heading">
								<td colspan="2">
									<strong><?php esc_html_e( 'Custom Fields', 'searchwp' ); ?></strong>
								</td>
							</tr>

							<?php if ( isset( $engineSettings[ $post_type->name ]['weights']['cf'] ) && is_array( $engineSettings[ $post_type->name ]['weights']['cf'] ) && ! empty( $engineSettings[ $post_type->name ]['weights']['cf'] ) ) : $cfWeights = $engineSettings[ $post_type->name ]['weights']['cf']; ?>
							<?php foreach ( $cfWeights as $cfFlag => $cfWeight ) : $arrayFlag = str_replace( '.', '', uniqid( 'swpp', true ) ); ?>
								<?php if ( SEARCHWP_PREFIX . 'content' != $cfWeight['metakey'] && SEARCHWP_PREFIX . 'pdf_metadata' != $cfWeight['metakey'] ) : /* handled elsewhere specifically */ ?>
									<tr class="swp-custom-field">
										<td class="swp-custom-field-select">
											<!--suppress HtmlFormInputWithoutLabel -->
											<select data-nonce="<?php echo esc_attr( $cf_nonce ); ?>" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][weights][cf][<?php echo esc_attr( $arrayFlag ); ?>][metakey]" style="width:80%;">
												<option value="searchwpcfdefault" <?php selected( $cfWeight['metakey'], 'searchwpcfdefault' ); ?>><?php esc_html_e( 'Any', 'searchwp' ); ?></option>
												<?php if ( ! empty( $searchwp->keys ) ) : foreach ( $searchwp->keys as $key ) : ?>
													<?php
														$meta_key_lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $cfWeight['metakey'], 'UTF-8' ) : strtolower( $cfWeight['metakey'] );
														$this_key_lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $key, 'UTF-8' ) : strtolower( $key );
													?>
													<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $meta_key_lower, $this_key_lower ); ?>><?php echo esc_html( $key ); ?></option>
												<?php endforeach; endif; ?>
											</select>
											<a class="swp-delete" href="#">x</a>
										</td>
										<td>
											<!--suppress HtmlFormInputWithoutLabel -->
											<input type="number" min="-1" step="0.1" class="small-text" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][weights][cf][<?php echo esc_attr( $arrayFlag ); ?>][weight]" value="<?php echo esc_attr( $cfWeight['weight'] ); ?>" />
										</td>
									</tr>
								<?php endif; ?>
							<?php endforeach; endif; ?>

							<tr>
								<td colspan="2">
									<a class="button swp-add-custom-field" href="#" data-engine="<?php echo esc_attr( $engine ); ?>" data-posttype="<?php echo esc_attr( $post_type->name ); ?>"><?php esc_html_e( 'Add Custom Field', 'searchwp' ); ?></a>
									<a class="swp-tooltip swp-tooltip-custom-field" href="#swp-tooltip-custom-field-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">?</a>
									<div class="swp-tooltip-content" id="swp-tooltip-custom-field-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">
										<?php esc_html_e( 'Include Custom Field data in search results. Meta values do not need to be plain strings, available keywords in metadata are extracted and indexed.', 'searchwp' ); ?>
									</div>
								</td>
							</tr>

						</tbody>
					</table>
				</div>
				<div class="swp-engine-options">
					<?php $options = ! empty( $engineSettings[ $post_type->name ]['options'] ) ? $engineSettings[ $post_type->name ]['options'] : array(); ?>
					<table>
						<tbody>
							<tr>
								<td>
									<label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_exclude"><?php esc_html_e( 'Exclude IDs: ', 'searchwp' ); ?></label>
								</td>
								<td>
									<?php
										$options['exclude'] = isset( $options['exclude'] ) ? (string) $options['exclude'] : '0';
										$options['exclude'] = SWP()->get_integer_csv_string_from_string_or_array( $options['exclude'] );
									?>
									<input type="text" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][options][exclude]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_exclude" placeholder="<?php esc_attr_e( 'Comma separated IDs', 'searchwp' ); ?>" value="<?php if ( ! empty( $options['exclude'] ) ) { echo esc_attr( $options['exclude'] ); } ?>" /> <a class="swp-tooltip" href="#swp-tooltip-exclude-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">?</a>
									<div class="swp-tooltip-content" id="swp-tooltip-exclude-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">
										<?php esc_html_e( 'Comma separated post IDs. Will be excluded entirely, even if attributed to.', 'searchwp' ); ?>
									</div>
								</td>
							</tr>
							<?php
							if ( is_array( $taxonomies ) && count( $taxonomies ) ) :
								foreach ( $taxonomies as $taxonomy ) {
									$taxonomy = get_taxonomy( $taxonomy );
									$nonce = wp_create_nonce( 'swp_tax_terms_' . $taxonomy->name );
									?>
									<tr>
										<td>
											<label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_exclude_<?php echo esc_attr( $taxonomy->name ); ?>">
												<?php echo esc_html( sprintf( __( 'Exclude %s:', 'searchwp' ), esc_attr( $taxonomy->labels->name ) ) ); ?>
											</label>
										</td>
										<td>
											<?php
											// retrieve our stored exclusions
											$excluded = isset( $options[ 'exclude_' . $taxonomy->name ] ) ? explode( ',', $options[ 'exclude_' . $taxonomy->name ] ) : array();
											if ( ! empty( $excluded ) ) {
												$excluded = array_map( 'absint', $excluded );
											}
											?>
											<select class="swp-exclude-select" data-searchable="true" data-tax="<?php echo esc_attr( $taxonomy->name ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][options][exclude_<?php echo esc_attr( $taxonomy->name ); ?>][]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_exclude_<?php echo esc_attr( $taxonomy->name ); ?>" multiple data-placeholder="<?php esc_attr_e( 'Leave blank to omit', 'searchwp' ); ?>" style="width:170px;">

												<?php
												if ( ! empty( $excluded ) ) {
													$taxonomy_args = array(
														'hide_empty' => false,
														'include'    => $excluded,
														'fields'     => 'id=>name',
													);
													$excluded_terms = get_terms( $taxonomy->name, $taxonomy_args );

													foreach ( $excluded_terms as $excluded_term_id => $excluded_term ) {
														?><option value="<?php echo absint( $excluded_term_id ); ?>" selected="selected"><?php echo esc_html( $excluded_term ); ?></option><?php
													}
												}
												?>
											</select>
											<a class="swp-tooltip" href="#swp-tooltip-exclude-<?php echo esc_attr( $post_type->name ); ?>-<?php echo esc_attr( $taxonomy->name ); ?>">?</a>
											<div class="swp-tooltip-content" id="swp-tooltip-exclude-<?php echo esc_attr( $post_type->name ); ?>-<?php echo esc_attr( $taxonomy->name ); ?>">
												<?php esc_html_e( 'Entries with these will be excluded entirely, even if attributed to.', 'searchwp' ); ?>
											</div>
										</td>
									</tr>
								<?php }
							endif; ?>
							<?php if ( 'attachment' == $post_type->name ) : ?>
								<tr>
									<td>
										<label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_mimes">
											<?php esc_html_e( 'Limit File Type(s) to', 'searchwp' ); ?>:
										</label>
									</td>
									<td>
										<?php
										$mimes = array(
											__( 'All Documents', 'searchwp' ),
											__( 'PDFs', 'searchwp' ),
											__( 'Plain Text', 'searchwp' ),
											__( 'Images', 'searchwp' ),
											__( 'Video', 'searchwp' ),
											__( 'Audio', 'searchwp' ),
											__( 'Office Documents', 'searchwp' ),
											__( 'OpenOffice Documents', 'searchwp' ),
											__( 'iWork Documents', 'searchwp' ),
										);
										// retrieve our stored exclusions
										$limitedMimes = isset( $options['mimes'] ) ? explode( ',', $options['mimes'] ) : array();
										if ( ! empty( $limitedMimes ) ) {
											$limitedMimes = array_map( 'absint', $limitedMimes );
										}
										?>
										<select class="swp-exclude-select" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][options][mimes][]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_mimes" multiple data-placeholder="<?php esc_attr_e( 'Leave blank to omit', 'searchwp' ); ?>" style="width:170px;">
											<?php for ( $i = 0; $i < count( $mimes ); $i++ ) : ?>
												<option value="<?php echo esc_attr( $i ); ?>"<?php if ( in_array( $i, $limitedMimes ) ) { ?> selected="selected"<?php } ?>><?php echo esc_html( $mimes[ $i ] ); ?></option>
											<?php endfor; ?>
										</select>
										<a class="swp-tooltip" href="#swp-tooltip-limit-<?php echo esc_attr( $post_type->name ); ?>-mime-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">?</a>
										<div class="swp-tooltip-content" id="swp-tooltip-limit-<?php echo esc_attr( $post_type->name ); ?>-mime-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">
											<?php esc_html_e( 'If populated, Media results will be limited to these Media types', 'searchwp' ); ?>
										</div>
									</td>
								</tr>
							<?php endif; ?>
							<?php if ( 'attachment' == $post_type->name || apply_filters( "searchwp_enable_parent_attribution_{$post_type->name}", false ) ) : ?>
								<tr>
									<td><?php esc_html_e( 'Attribute post parent', 'searchwp' ); ?></td>
									<td>
										<?php $enabled = ! empty( $options['parent'] ); ?>
										<input type="checkbox" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][options][parent]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_parent" value="1" <?php checked( $enabled ); ?>/>
										<label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_parent"><?php esc_html_e( 'Enabled', 'searchwp' ); ?></label>
										<a class="swp-tooltip" href="#swp-tooltip-parent-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">?</a>
										<div class="swp-tooltip-content" id="swp-tooltip-parent-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">
											<?php esc_html_e( 'When enabled, search weights will be applied to the post parent, not the post GUID', 'searchwp' ); ?>
										</div>
									</td>
								</tr>
							<?php elseif ( apply_filters( "searchwp_enable_attribution_{$post_type->name}", true ) ) : ?>
								<tr>
									<td>
										<label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_attribute"><?php esc_html_e( 'Attribute search results to ', 'searchwp' ); ?></label>
									</td>
									<td>
										<input type="number" min="1" step="1" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][options][attribute_to]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_attribute_to" value="<?php if ( ! empty( $options['attribute_to'] ) ) { echo esc_attr( absint( $options['attribute_to'] ) ); } ?>" placeholder="<?php esc_attr_e( 'Single post ID', 'searchwp' ); ?>" />
										<a class="swp-tooltip" href="#swp-tooltip-attribute-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">?</a>
										<div class="swp-tooltip-content" id="swp-tooltip-attribute-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">
											<?php echo wp_kses( __( "<strong>Expects single post ID</strong><br/>If permalinks for this post type should not be included in search results, you can have it's search weight count toward another post ID.", 'searchwp' ), array( 'strong' => array(), 'br' => array() ) ); ?>
										</div>
									</td>
								</tr>
							<?php endif; ?>
							<?php  if ( SWP()->is_stemming_supported_in_locale() ) : ?>
								<tr>
									<td><?php esc_html_e( 'Use keyword stem', 'searchwp' ); ?></td>
									<td>
										<?php $enabled = ! empty( $options['stem'] ); ?>
										<input type="checkbox" name="<?php echo esc_attr( SEARCHWP_PREFIX ); ?>settings[engines][<?php echo esc_attr( $engine ); ?>][<?php echo esc_attr( $post_type->name ); ?>][options][stem]" id="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_stem" value="1" <?php checked( $enabled ); ?>/>
										<label for="swp_engine_<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>_stem"><?php esc_html_e( 'Enabled', 'searchwp' ); ?></label>
										<a class="swp-tooltip" href="#swp-tooltip-stem-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">?</a>
										<div class="swp-tooltip-content" id="swp-tooltip-stem-<?php echo esc_attr( $engine ); ?>_<?php echo esc_attr( $post_type->name ); ?>">
											<?php echo wp_kses( __( '<em>May increase search latency</em><br />For example: when enabled, searches for <strong>fishing</strong> and <strong>fished</strong> will generate the same results. When disabled, results may be different.', 'searchwp' ), array( 'em' => array(), 'br' => array(), 'strong' => array() ) ); ?>
										</div>
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

				<div style="clear:both;padding-top:2em;">
					<?php if ( 'attachment' === $post_type->name && empty( $ziparchive_available ) ) : ?>
						<p class="description" style="padding-bottom:10px;"><?php echo wp_kses( __( '<strong>Note:</strong> <code>ZipArchive</code> is not available to PHP. As a result, Office document content will not be indexed.', 'searchwp' ), array( 'strong' => array(), 'code' => array() ) ); ?></p>
					<?php endif; ?>
					<?php if ( 'attachment' === $post_type->name && empty( $domdocument_available ) ) : ?>
						<p class="description" style="padding-bottom:10px;"><?php echo wp_kses( __( '<strong>Note:</strong> <code>DOMDocument</code> is not available to PHP. As a result, Office document content will not be indexed.', 'searchwp' ), array( 'strong' => array(), 'code' => array() ) ); ?></p>
					<?php endif; ?>
				</div>

			</div>
		<?php endforeach; ?>
	</div>
</div><?php

	return true;
}

// @codingStandardsIgnoreStart
/**
 * @deprecated as of 2.5.7
 *
 * @param array $weights
 * @param string $type
 * @param null $subtype
 *
 * @return int
 */
function searchwpGetEngineWeight( $weights = array(), $type = 'title', $subtype = null ) {
	return searchwp_get_engine_weight( $weights, $type, $subtype );
}

/**
 * @deprecated as of 2.5.7
 *
 * @param string $engine
 *
 * @return bool
 */
function searchwpEngineSettingsTemplate( $engine = 'default' ) {
	return searchwp_engine_settings_template( $engine );
}
// @codingStandardsIgnoreEnd
