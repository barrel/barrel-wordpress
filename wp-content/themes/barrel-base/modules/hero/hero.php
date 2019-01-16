<section class="hero" data-module="hero">
  <main>

    <?php
    $image = get_the_post_thumbnail_url( $post->ID, 'large' );
    the_module('image', array(
      'image' => $image
    ));
    ?>
  </main>
</section>
