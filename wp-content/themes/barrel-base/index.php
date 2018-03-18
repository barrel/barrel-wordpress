<?php
  get_header();

  the_module('hero');

  while ( have_posts() ) {

    the_post();

    the_module('post');

  }

  get_footer();
?>
