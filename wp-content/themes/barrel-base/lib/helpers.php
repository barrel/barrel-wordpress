<?php

function get_image_with_size( $id, $size ) {
	$img = wp_get_attachment_image_src( $id, $size );

	if ( $img ) {
		return $img[0];
	}

	return '';
}

function get_image_field_with_size( $name, $size ) {
	return get_image_with_size( get_field( $name ), $size );
}

function get_industry_image( $industry_id ) {
	return get_image_with_size( get_field( 'photo', 'industry_' . $industry_id ), 'industry-photo' );
}

function the_image_field_with_size( $name, $size ) {
	echo get_image_field_with_size( $name, $size );
}

function get_thumbnail_url_with_size( $size ) {
	$img = wp_get_attachment_image_src( get_post_thumbnail_id(), $size );

	if ( $img ) {
		return $img[0];
	}

	return '';
}

// numbered pagination
function numbered_pagination($pages = '', $range = 2) {

	$showitems = ($range * 2) + 1;

	global $paged;

	if(empty($paged)) $paged = 1;

	if($pages == ''):
		global $wp_query;

		$pages = $wp_query->max_num_pages;

		if(!$pages):
			$pages = 1;
		endif;

	endif;

	if(1 != $pages) :

		echo '<div class="list-container latest-news__pagination--wrapper"><div class="latest-news__pagination"><ul class="latest-news__pagination--list">' . "\n";

		if($paged > 1):
			printf( '<li class="latest-news__pagination--list__item latest-news__pagination--list__pagi-left">%s</li>' . "\n", get_previous_posts_link('<i class="fa fa-angle-left"></i>') );
		endif;

		for ($i=1; $i <= $pages; $i++):

			if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )):
				$class = ($paged === $i) ? 'latest-news__pagination--list__item--active' : '';
				printf( '<li class="latest-news__pagination--list__item %s"><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $i ) ), $i );
			endif;

		endfor;

		if ($paged < $pages) :
			printf( '<li class="latest-news__pagination--list__item latest-news__pagination--list__pagi-right">%s</li>' . "\n", '<a href="'.esc_url( get_pagenum_link( $paged + 1 ) ).'"><i class="fa fa-angle-right"></i>');
		endif;

		echo '</ul></div></div>' . "\n";

	endif;
}

function get_sticky_news() {
	$ids = get_option( 'sticky_posts' );
	$query = new WP_Query( array(
		'posts_per_page' => 3,
		'post__in' => $ids
	) );

	$sticky = $query->get_posts();
	$count = count( $sticky );

	if ( $count < 3 ) {
		$query = new WP_Query( array(
			'posts_per_page' => 3 - $count,
			'posts__not_in' => $ids,
			'ignore_sticky_posts' => true,
		) );

		$sticky = array_merge( $sticky, $query->get_posts() );
	} if ( $count > 3 ) {
		$sticky = array_slice( $sticky, 0, 3 );
	}

	return $sticky;
}

function the_news_cta_url() {
	$type = get_field( 'type' );

	if ( $type === 'post' ) {
		echo get_the_permalink();
		return;
	}

	echo get_field( 'url' );
}

function the_news_cta_title() {
	$custom = get_field( 'custom_cta_title' );

	if ( $custom ) {
		echo $custom;
		return;
	}

	$type = get_field( 'type' );

	switch ( $type ) {
		case 'post':
			echo __( 'Read More', New_Theme::$text_domain );
			break;
		case 'link':
			echo __( 'Go to Article', New_Theme::$text_domain );
			break;
		case 'pdf':
			echo __( 'View PDF', New_Theme::$text_domain );
			break;
	}
}

function get_portfolio_type() {
	$year = get_field( 'year_sold' );
	if ( ! $year ) {
		return 'current';
	}

	return 'historical';
}

function get_portfolio_industry() {
	$terms = get_the_terms( 0, 'industry' );

	if ( $terms && ! is_wp_error( $terms ) ) {
		return $terms[0]->slug;
	}

	return '';
}

function get_portfolio_industry_name() {
	$terms = get_the_terms( 0, 'industry' );

	if ( $terms && ! is_wp_error( $terms ) ) {
		return $terms[0]->name;
	}

	return '';
}

function is_portfolio_current() {
	return get_portfolio_type() == 'current';
}

function get_team_role() {
	$terms = get_the_terms( get_the_ID(), 'role' );

	if ( $terms && ! is_wp_error( $terms ) ) {
		return $terms;
	}

	return '';
}

function portfolio_carousel( $field_name ) {
	include( locate_template( 'templates/partials/portfolio-carousel.php' ) );
}

function page_hero( $additional_class = "" ) {
	include( locate_template( 'templates/partials/page-hero.php' ) );
}

function sidebar_info( $heading = '', $content = '' ) {
	include( locate_template( 'templates/partials/sidebar-info.php' ) );
}

function sticky_news( $title = 'Featured News' ) {
	include( locate_template( 'templates/partials/sticky-news.php' ) );
}

function get_portfolio_landing_url() {
	$pages = get_posts( $args = array(
		'post_type'  => 'page',
		'meta_query' => array(
			array(
				'key'   => '_wp_page_template',
				'value' => 'templates/portfolio-landing.php'
			)
		)
	) );

	if ( $pages ) {
		return get_permalink( $pages[0]->ID );
	}

	return '';
}

function output_filters( $filter_groups, $args = '' ) {
	$args = wp_parse_args( $args, array(
		'dynamic_grid' => true,
		'target_selector' => '.filter-results',
		'placeholder_selector' => '.filter-placeholder'
	) );

	$classes = 'filters';
	if (! $args['dynamic_grid'] ) {
		$classes .= ' filters--static';
	}

	foreach ( $filter_groups as $key => $group ) {
		$filter_groups[$key]['variant'] = empty( $group['variant'] ) ? 'primary' : $group['variant'];
		$filter_groups[$key]['items'] = array_merge(
			array( '*' => empty( $group['all_text'] ) ? 'All' : $group['all_text'] ),
			$group['items']
		);
	}

	include( locate_template( 'templates/modules/filters.php' ) );
}

function filter_group_links( $group_name, $filter_group ) {
	printf( '<div data-filter-group="%s" class="filter filter--desktop">', $group_name );
	$link = '<a data-filter="%s" href="#" class="filter__link%s">%s</a>';

	$active = true;
	foreach ( $filter_group['items'] as $name => $label ) {
		printf( $link, $name, $active ? ' js-active' : '', $label );

		$active = false;
	}
	echo '</div>';
}

function filter_group_dropdown( $group_name, $filter_group, $variant = false ) {
	$variant_class = empty( $variant ) ? '' : ' filter--' . $variant;

	printf( '<div data-filter-group="%s" class="filter%s">', $group_name, $variant_class);
	printf( '<select class="filter__select filter__select-%s">', $group_name );
	$option = '<option value="%s">%s</option>';

	foreach ( $filter_group['items'] as $name => $label ) {
		printf( $option, $name, $label );
	}
	echo '</select></div>';
}

function get_taxonomy_filter( $taxonomy ) {
	$terms = get_terms( $taxonomy, array( 'hide_empty' => false, 'exclude' => array(17, 19) ) );

	$values = array();

	foreach ( $terms as $term ) {
		$values[$term->slug] = $term->name;
	}

	return $values;
}

function get_our_team_url() {
	$pages = get_posts( $args = array(
		'post_type'  => 'page',
		'meta_query' => array(
			array(
				'key'   => '_wp_page_template',
				'value' => 'templates/team-list-view.php'
			)
		)
	) );

	if ( $pages ) {
		return get_permalink( $pages[0]->ID );
	}

	return '';
}

/**
 * Get truncated content with highlight search keywords from About ACF
 */
function get_search_excerpt_from_about_acf($content) {
	$all_parent_fields = array('about_us');
	$all_sub_fields = array('about_title', 'about_content', 'about_button_text');

	return get_search_excerpt_from_acf_repeater_fields($all_parent_fields, $all_sub_fields, $content);
}

/**
 * Get truncated content with highlight search keywords from Hero ACF
 */
function get_search_excerpt_from_hero_acf($content) {
	$all_fields = array('hero_headline', 'hero_intro');

	return get_search_excerpt_from_acf_fields($all_fields, $content);
}

/**
 * Get truncated content with highlight search keywords from Portfolio ACF
 */
function get_search_excerpt_from_portfolio_acf($content) {
	$all_fields = array('year_of_investment', 'year_sold', 'type_of_sale');

	return get_search_excerpt_from_acf_fields($all_fields, $content);;
}

/**
 * Get truncated content with highlight search keywords from Homepage ACF
 */
function get_search_excerpt_from_homepage_acf($content) {
	$all_fields = array('hero_headline', 'hero_intro', 'cornerstones_description', 'cornerstones_cta_title', 'industry_verticals_description');
	$all_parent_fields = array('hero_ctas', 'cornerstones_values');
	$all_sub_fields = array('title');

	$content = get_search_excerpt_from_acf_fields($all_fields, $content);
	return get_search_excerpt_from_acf_repeater_fields($all_parent_fields, $all_sub_fields, $content);
}

/**
 * Get truncated content with highlight search keywords from ACF fields
 */
function get_search_excerpt_from_acf_fields($all_fields, $content) {
	if (empty($content)) {
		foreach ($all_fields as $field) {
			if (!empty($content)) {
				break;
			}
			$content = get_search_excerpt(get_field($field));
		}
	}
	return $content;
}

/**
 * Get truncated content with highlight search keywords from ACF repeater fields
 */
function get_search_excerpt_from_acf_repeater_fields($all_parent_fields, $all_sub_fields, $content) {
	if (empty($content)) {
		foreach ($all_parent_fields as $parent_field) {
			if (!empty($content)) {
				break;
			}
			while( have_rows($parent_field) ) {
				the_row();
				if (!empty($content)) {
					break;
				}
				foreach ($all_sub_fields as $sub_field) {
					if (!empty($content)) {
						break;
					}
					$content = get_search_excerpt(get_sub_field($sub_field));
				}
			}
		}
	}
	return $content;
}

/**
 * Return truncated search excerpt
 */
function get_search_excerpt($content){
	return apply_filters('highlight_search', $content);
}

/**
 * Return truncated content with highlight search keywords
 */
function highlight_search( $content, $limit = 220) {
	if ( !is_admin() ) {
		$content = strip_tags($content);
		$search_terms = explode(' ', get_search_query());

		$lowest = 99999;

		for ($i = 0; $i < count($search_terms); $i++) {
			$str = strtolower($search_terms[$i]);

			$first = strpos(strtolower($content), $str);

			if ($first < $lowest) {
				$lowest = $first;
			}
		}

		$truncate_from = $lowest < 50 ? 0 : $lowest-50;

		$content = truncate($content, $limit, $truncate_from);

		for ($i = 0; $i < count($search_terms); $i++) {
			$str = strtolower($search_terms[$i]);
			$content = preg_replace_callback("/(?![^<]*>)$str/i", function ($matches){
				return sprintf('<span class="search__highlight">%s</span>', $matches[0]);
			}, $content);
		}

		return $content;
	}
}
add_filter( 'highlight_search', 'highlight_search', 10 );

/**
 * Truncator
 */
function truncate($string, $limit, $start = 0) {
	$ellipses_end = false;
	$total_length = strlen($string);

	if($total_length > $limit){
		$end = $start + $limit;

		if($end < $total_length)
			$ellipses_end = true;

		$subtract = ($ellipses_end && $start) ?
			8 :
			($ellipses_end || $start) ?
				4 : 0;

		$end = $ellipses_end ? $end - $subtract : $end;

		$sub_string = substr($string, $start, $end-$start);

		$first_space = $start ? strpos($sub_string, ' ') : $start;
		$last_space = $end < $total_length ? strrpos($sub_string, ' ') : strlen($sub_string);

		$nice_sub_string = substr($sub_string, $first_space, $last_space-$first_space);

		$before = $start ? '...&nbsp;' : '';
		$after = $ellipses_end ? '&nbsp;...' : '';

		return $before.$nice_sub_string.$after;
	}
	return $string;
};

/**
 * List all the custom fields we want to include in our search query
 * @return array list of custom fields
 */
function list_searcheable_acf(){
	$list_searcheable_acf = array(
		// About ACF
		"about_title",
		"about_content",
		"about_button_text",
		// Hero ACF
		"hero_headline",
		"hero_intro",
		// Homepage ACF
		"title",
		"cornerstones_description",
		"cornerstones_cta_title",
		"industry_verticals_description",
		// Portfolio Individual ACF
		"year_of_investment",
		"year_sold",
		"type_of_sale"
	);
	return $list_searcheable_acf;
}
/**
 * Search that encompasses ACF/advanced custom fields and taxonomies and split expression before request
 * @param  string      $where    the initial "where" part of the search query]
 * @param  object                 $wp_query 
 * @return string      $where    the "where" part of the search query as we customized
 * see https://vzurczak.wordpress.com/2013/06/15/extend-the-default-wordpress-search/
 * credits to Vincent Zurczak for the base query structure/spliting tags section
 */
function advanced_custom_search( $where, &$wp_query ) {
	global $wpdb;

	if ( empty( $where ))
		return $where;

	// get search expression
	$terms = $wp_query->query_vars[ 's' ];

	// explode search expression to get search terms
	$exploded = explode( ' ', $terms );
	if( $exploded === FALSE || count( $exploded ) == 0 )
		$exploded = array( 0 => $terms );

	// reset search in order to rebuilt it as we wish
	$where = '';

	// get searcheable_acf, a list of advanced custom fields you want to search content in
	$list_searcheable_acf = list_searcheable_acf();
	foreach( $exploded as $tag ) :
		$where .= "
			AND (
				(wp_posts.post_title LIKE '%$tag%')
				OR (wp_posts.post_content LIKE '%$tag%')
				OR EXISTS (
					SELECT * FROM wp_postmeta
						WHERE post_id = wp_posts.ID
							AND (";
		foreach ($list_searcheable_acf as $searcheable_acf) :
			if ($searcheable_acf == $list_searcheable_acf[0]):
				$where .= " (meta_key LIKE '%" . $searcheable_acf . "%' AND meta_value LIKE '%$tag%') ";
			else :
				$where .= " OR (meta_key LIKE '%" . $searcheable_acf . "%' AND meta_value LIKE '%$tag%') ";
			endif;
		endforeach;
			$where .= ")
				)
				OR EXISTS (
					SELECT * FROM wp_comments
					WHERE comment_post_ID = wp_posts.ID
						AND comment_content LIKE '%$tag%'
				)
				OR EXISTS (
					SELECT * FROM wp_terms
					INNER JOIN wp_term_taxonomy
						ON wp_term_taxonomy.term_id = wp_terms.term_id
					INNER JOIN wp_term_relationships
						ON wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id
					WHERE (
					taxonomy = 'post_tag'
						OR taxonomy = 'category'
						OR taxonomy = 'myCustomTax'
					)
						AND object_id = wp_posts.ID
						AND wp_terms.name LIKE '%$tag%'
				)
		)";
	endforeach;
	return $where;
}
add_filter( 'posts_search', 'advanced_custom_search', 500, 2 );
