<header class="header" data-module="header">

	<div>
		<?php if ( is_front_page() ) : ?>
			<h1>
				<?php bloginfo( 'name' ); ?>
			</h1>
			<h2><?php bloginfo( 'description' ); ?></h2>
		<?php else : ?>
			<p>
				<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			</p>
			<p><?php bloginfo( 'description' ); ?></p>
		<?php endif; ?>
	</div>

	<?php
		wp_nav_menu( array(
			'container' => 'nav'
		) );
	?>

	<?php if( have_rows('social_media_links', 'options') ): ?>
		<nav>
			<ul>
				<?php while( have_rows('social_media_links', 'options') ): the_row(); ?>
					<li>
						<a href="<?php echo the_sub_field('url'); ?>" target="_blank">
							<?php echo the_sub_field('label'); ?>
						</a>
					</li>
				<?php endwhile; ?>
			</ul>
		</nav>
	<?php endif; ?>

	<?php get_search_form(); ?>

</header>
