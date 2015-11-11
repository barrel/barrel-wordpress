<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]> <html class="no-js ie lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]> <html class="no-js ie lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]> <html class="no-js ie ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]> <!--><html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title><?php wp_title( '|', true, 'right' );  ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<?php if ( is_category() || is_tag() ||	 is_tax()) : ?><meta name="robots" content="noindex,follow" /><?php endif; ?>

		<?php
			/*
			 * Write all JS and CSS files that belong in the header.
			 * Add more by configuring them in config/<environment>.config.php
			 */
			wp_head();
		?>
		<link rel="shortcut icon" href="<?php bloginfo('stylesheet_directory') ?>/favicon.ico" type="image/x-icon" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo get_protocol() . KIND_SHARED_URL; ?>/styles/kindsnacks-modules.css">
		
		<!--[if IE 8]>
			<link rel="stylesheet" type="text/css" href="<?php echo get_protocol() . KIND_SHARED_URL; ?>/styles/kindsnacks-modules-ie8.css" />
			<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory')?>/css/ie8.min.css" />
			<script src="<?php bloginfo('stylesheet_directory')?>/js/src/vendor/respond.min.js"></script>
		<![endif]-->

	</head>

	<body <?php body_class(); ?>>
		<?php echo stripslashes(get_option('after_body_tag_open'))?>  
		
		<!--
		Start of DoubleClick Floodlight Tag: Please do not remove
		Activity name of this tag: KIND_Site_Retargeting_2015
		URL of the webpage where the tag is expected to be placed: http://www.kindsnacks.com/
		This tag must be placed between the <body> and </body> tags, as close as possible to the opening tag.
		Creation Date: 01/05/2015
		-->
		<script type="text/javascript">
			var axel = Math.random() + "";
			var a = axel * 10000000000000;
			document.write('<iframe src="https://4288039.fls.doubleclick.net/activityi;src=4288039;type=KIND_00;cat=KIND_0;ord=' + a + '?" width="1" height="1" frameborder="0" style="display:none"></iframe>');
		</script>
		
		<noscript>
			<iframe src="https://4288039.fls.doubleclick.net/activityi;src=4288039;type=KIND_00;cat=KIND_0;ord=1?" width="1" height="1" frameborder="0" style="display:none"></iframe>
		</noscript>
		
		<!-- End of DoubleClick Floodlight Tag: Please do not remove -->
		<!--[if lt IE 7]>
			<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
		<![endif]-->
		<script>(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"4031165"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");</script>

		<?php shared_module( Array('name'=>'header','lang'=> THEME_SAFE_LOCALE) ); ?>