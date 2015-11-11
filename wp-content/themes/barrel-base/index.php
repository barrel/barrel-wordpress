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

$blog = get_field('blog_page', 'options');
get_header(); 
?>

<main class="blog-page container" role="main">
	
	<div class="row">
	
		<div class="bread-crumbs">
			<ul class="crumbs">
				<li><a href="<?php echo get_permalink($blog); ?>"><?php echo get_the_title($blog); ?></a></li>

				<li><?php 
					if ( is_tag() ) {
						_e('Tagged', 'kindsnacks');
					} elseif ( is_author() ) {
						_e('Author', 'kindsnacks');
					}
				?></a></li>
				<li><?php 
					if ( is_tag() ) {
						echo ucwords(single_cat_title('', false));
					} elseif ( is_author() ) {
						the_author();
					}
				?></li>
			</ul>
		</div>
		
		<div class="col-md-9">
		
			<h4><?php 
				if ( is_tag() ) {
					echo ucwords(single_cat_title('', false));
				} elseif ( is_author() ) {
					_e("Posts by: ", 'kindsnacks');
					the_author();
				}
			?></h4>
	
			<?php 
			if ( have_posts() ) : 
				while ( have_posts() ) : the_post(); 

					include(locate_template('templates/_partials/blog/feed-post.php'));

				endwhile; 
			endif; 
			$prev = get_previous_posts_link('Previous Page'); 
			$next = get_next_posts_link('Next Page');
			
			if ( $prev || $next ) : ?>

			<div class="pagination proxima clearfix<?php echo ($prev && $next) ? ' half-size' : '';?>">
				<?php 
					$nextprevformat = '<div class="page-link">%s</div>';
					if ($prev) printf($nextprevformat, $prev);
					if ($next) printf($nextprevformat, $next);
				?>
			</div>
			<?php endif; ?>
	
		</div>
		
		<div class="col-md-3"></div>
	
	</div>
	 
</main>

<?php get_footer(); ?>