<section class="hero" data-module="hero">
  <main>
    <?php
      $title = get_field('title');
      $image = get_field('image') ?? get_the_post_thumbnail_url( $post->ID, 'large' );
    ?>
    <h1><?= $title; ?>
    <?php
      the_module('image', array(
        'image' => $image
      ));
    ?>
  </main>
</section>
