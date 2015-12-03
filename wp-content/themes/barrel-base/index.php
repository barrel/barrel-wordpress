<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?>>
  <?php
  the_module('header');

  while ( have_posts() ) { the_post();

    the_module('post');

  }
  ?>
  <?php wp_footer(); ?>
  </body>
</html>
