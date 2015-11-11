<?php
/**
 * Template: Post Single
 */

$blog_page = get_field('blog_page', 'options');
get_header();?>

<main class="blog blog-single container" role="main">

	<div class="row">
	
		<div class="bread-crumbs">
			<ul class="crumbs">
				<li><a href="<?php echo get_permalink($blog_page); ?>"><?php echo get_the_title($blog_page); ?></a></li>
				<li><?php the_title() ?></li>
			</ul>
		</div>
		
		<div class="col-md-9">
		
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
		
			include(locate_template('templates/_partials/blog/feed-post.php')); ?>
		
			<section class="tags">
				<span>Tags:</span>
				<?php echo get_the_tag_list(); ?>
			</section>
		
			<section class="contributor-details">
			
				<?php echo get_avatar($post->post_author);?>
			
				<div class="inner">
					<p>
						<span class="bold"><?php echo the_author_meta('display_name'); ?></span>
						<span class="user-type"><?php echo user_level_name(get_the_author_meta('user_level')); ?></span>
					</p>
					<p class="description"><?php echo the_author_meta('description'); ?></p>
					<a class="break-word" href="<?php echo the_author_meta('user_url'); ?>" target="_blank"><?php echo the_author_meta('user_url'); ?></a>
				</div>
		
			</section>
		
			<?php endwhile; endif; ?>
		
		</div>
		
		<div class="col-md-3">
		
			<?php get_template_part( 'templates/_partials/blog/single', 'sidebar'); ?>
			
		</div>
	
	</div>
	 
</main>

<?php get_footer(); ?>