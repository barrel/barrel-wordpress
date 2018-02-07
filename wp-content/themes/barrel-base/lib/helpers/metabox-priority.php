<?php

// move yoast seo metaboxes to bottom
add_filter( 'wpseo_metabox_prio', function () {
  return 'low';
});
