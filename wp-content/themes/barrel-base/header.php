<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]> <html class="no-js ie lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]> <html class="no-js ie lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]> <html class="no-js ie ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]> <!--><html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<?php
			/*
			 * Write all JS and CSS files that belong in the header.
			 * Add more by configuring them in config/<environment>.config.php
			 */
			wp_head();
		?>

		<link rel="shortcut icon" href="<?php bloginfo('stylesheet_directory') ?>/favicon.ico" type="image/x-icon" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	</head>

	<body <?php body_class(); ?>>
