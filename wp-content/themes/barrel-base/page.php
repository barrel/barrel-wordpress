<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 */

get_header(); ?>

<main class="default-page" role="main">

	<?php get_template_part( 'templates/_partials/default/header' ); ?>
	
	<div class="content container">
		<div class="inner">
			
			<?php 
			if ( have_posts() ) :
				while ( have_posts() ) : the_post(); 
					the_content();
				endwhile;
			endif; 
			?>
			
		</div>
	</div>
</main>

<?php get_footer(); ?>