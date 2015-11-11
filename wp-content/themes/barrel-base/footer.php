		<?php get_template_part( 'templates/_partials/global/redirect'); ?>

		<!--------------------------------------------------
		Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
		--------------------------------------------------->
		<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 979382605;
		var google_custom_params = window.google_tag_params;
		var google_remarketing_only = true;
		/* ]]> */
		</script>
		<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
		
		<noscript>
			<div style="display:inline;">
			<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/979382605/?value=0&amp;guid=ON&amp;script=0"/>
			</div>
		</noscript>
		
		<?php shared_module( Array('name'=>'footer','lang'=>THEME_SAFE_LOCALE) ); ?>
		
		<?php
			/*
			 * Write all JS files that belong before the closing body tag.
			 * Add more by configuring them in config/<environment>.config.php
			 */
			wp_footer(); ?>
		
		<script type="text/javascript" src="<?php echo get_protocol() . KIND_SHARED_URL; ?>/scripts/kindsnacks-modules.js"></script>

		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', '<?php the_field('analytics_code', 'options') ?>', '<?php echo substr( SITE_URL, strpos( SITE_URL, ':', 0) + 3	); ?>');
			ga('send', 'pageview');
		</script>
								
		<!-- Google Code for Remarketing Tag -->
		<?php echo stripslashes(get_option('before_body_tag_close'))?>	
	</body>
</html>
