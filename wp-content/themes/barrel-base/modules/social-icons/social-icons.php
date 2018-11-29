<?php if( have_rows('social_media_links', 'options') ): ?>
	<nav class="social-icons<?php if( !empty($class) ) : echo ' ' . $class; endif; ?>">
		<ul class="social-icons-list">
			<?php
				while( have_rows('social_media_links', 'options') ): the_row();
				$social_platform = strtolower(get_sub_field('label'));
			?>
				<li class="social-icons-list__icon social-icons-list__icon--<?= $social_platform; ?>">
					<a class="social-icons-list__link" href="<?php echo the_sub_field('url'); ?>" target="_blank" alt="Go to <?= $social_platform; ?>">
						<?= _get_svg($social_platform); ?>
					</a>
				</li>
			<?php endwhile; ?>
		</ul>
	</nav>
<?php endif; ?>
