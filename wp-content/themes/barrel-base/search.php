<?php
/**
 * Template: Search
 */

get_header(); ?>

<main role="main">
	
	<div class="ajax-loading">
		<i class="yellow"></i>
		<i class="red"></i>
		<i class="green"></i>
		<i class="blue"></i>
	</div>
	
	<section id="Store">
		<header class="store-header just align-left">
			<h1><?php printf(__("Search results for '%s'", 'kindsnacks'), get_query_var('s')); ?></h1>
		</header>
		<section id="Products" class="feature-grid">
			<?php
			if ( have_posts() ):
				include( locate_template('templates/_partials/products/grid.php') );
			else :
				printf("<p class=\"search__no-results\">%s</p>", __('Your search returned no results.', 'kindsnacks'));
			endif; 
			?>
		</section>
	
	</section>

</main>

<?php get_footer(); ?>