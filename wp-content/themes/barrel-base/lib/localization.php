<?php

/**
 * Setup Localization Efforts
 */

function bb_theme_localization(){
    load_theme_textdomain(BB_Theme::$text_domain, THEME_DIR . '/languages');
}
add_action('after_setup_theme', 'bb_theme_localization');
