<article class="post">
  <header>
    <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
    <p><?php the_time() ?> by <?php the_author(); ?><p>
  </header>
  <main>
    <?php the_content(); ?>
  </main>
</article>
