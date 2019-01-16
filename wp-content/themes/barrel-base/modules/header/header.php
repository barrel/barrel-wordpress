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

  <?php the_module('social-icons'); ?>

  <?php get_search_form(); ?>

</header>
