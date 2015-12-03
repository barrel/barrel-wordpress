<?php
add_action( 'after_setup_theme', function() {

  add_theme_support( 'title-tag' );
  add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption' ) );

} );

add_action( 'wp_enqueue_scripts', function() {

  $theme = wp_get_theme();
  $theme_ver = $theme->version;

   wp_enqueue_style( 'main',
    get_template_directory_uri().'/styles/main.min.css',
    array(),
    $theme_ver
  );

  wp_enqueue_script( 'main',
    get_template_directory_uri().'/scripts/main.min.js',
    array( 'jquery' ),
    $theme_ver,
    true
  );

} );