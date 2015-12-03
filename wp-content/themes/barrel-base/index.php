<?php
  get_header();

  while ( have_posts() ) { the_post();

    the_module('post');

  }
  ?>
  <?php wp_footer(); ?>
  </body>
</html>
