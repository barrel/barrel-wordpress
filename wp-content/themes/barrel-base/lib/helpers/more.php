<?php

add_filter('the_content', function( $content ){
  if ( is_singular('event') ) {
    global $post;
    $contents = explode( "<!--more-->", $post->post_content );

    if ( count( $contents ) > 1 ) {
      $content = sprintf("<p>") . wpautop($contents[0]) . sprintf(" <a href=\"#\" class=\"js-more-show\">Read More</a></p><div class=\"more-content\">%s</div>", wpautop($contents[1]) . sprintf("<p><a href=\"#\" class=\"js-more-hide\">Show Less</a></p>"));
    }
  }

  return $content;
});

