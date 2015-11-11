<?php
/**
 * Blank 404 file
 *
 */

get_header(); ?>

<main class="page-404 container" role="main">
	<div class="padding-container">
		<div class="titleText"><h4><?php _e('Oops', 'kindsnacks'); ?></h4></div>
		<div class="subText">
			<p><?php _e('To ease the frustration of this 404 error, have a compliment', 'kindsnacks'); ?></p>
			<p class="complimentContainer"><?php _e("You're the best.", 'kindsnacks'); ?></p>
		</div>
		<div class="circleBase type2"><div class="circleBase type1"><p class="circleText"><?php _e('CLICK', 'kindsnacks'); ?></p></div></div>
	</div>
</main>
	
<?php get_footer(); ?>