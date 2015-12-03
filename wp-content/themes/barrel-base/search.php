<?php
/**
 * Template Name: Search
 */
?>

<?php get_header(); ?>

<section class="section search__results-container content">

  <h2 class="search__title">Search Results</h2>
  <form id="search-box" class="search__form" action="<?php echo home_url(); ?>">
  	<input class="search__input" type="text" name="s" placeholder="Search" value="<?php echo  get_search_query(); ?>">
  	<i class="fa fa-search search__submit"></i>
  </form>

  <?php
  global $wp_query;
  $total = $wp_query->found_posts;
  ?>

  <div class="list-container search__container">

    <div class="latest-news__heading search__heading">
      <span class="h6 search__total-results"><?php echo sprintf("%s results total", $total); ?></span>
    </div>

    <?php if (have_posts()) : ?>

      <?php while (have_posts()): the_post(); ?>

        <?php $post_type = get_post_type();
        $content = get_search_excerpt(get_the_content());
        if (empty($content)) {
          $content = get_search_excerpt(get_the_title());
        }

        if ($post_type == 'page'):
          $template = basename(get_page_template());
          switch($template):
            case 'home.php':
              $content = get_search_excerpt_from_homepage_acf($content);
              break;
            case 'news-media.php':
            case 'portfolio-landing.php':
            case 'team-list-view.php':
              $content = get_search_excerpt_from_hero_acf($content);
              break;
            case 'our-cornerstones.php':
            case 'the-story.php':
              $content = get_search_excerpt_from_hero_acf($content);
              $content = get_search_excerpt_from_about_acf($content);
              break;
          endswitch;
        elseif($post_type == 'portfolio'):
          $content = get_search_excerpt_from_portfolio_acf($content);
        endif; ?>

    		<div class="latest-news__single search__content">
    			<div class="latest-news__single--metadata">
    				<div class="latest-news__single--date search__category"><?php echo ($post_type == 'post') ? 'News & Media': $post_type; ?></div>
    			</div>
    			<div class="latest-news__single--content">
    				<div class="latest-news__single--title h5"><?= the_title(); ?></div>
    				<p class="latest-news__single--exceprt">
    					<?php echo $content; ?>
    				</p>
    				<div class="latest-news__single--link search__read-more">
    					<a href="<?= the_permalink(); ?>">Read More</a>
    				</div>
    			</div>
    		</div>

      <?php endwhile; ?>

    <?php else: ?>

      <p class="latest-news__single--exceprt search__not-found"><?php the_field('not_found_text', 'option'); ?></p>

    <?php endif; ?>

  </div>

  <?php numbered_pagination($wp_query->max_num_pages, 2); ?>

</section>

<div class="search__divider"></div>

<section class="about the-story search__about">

  <div class="about-us">

    <div class="about-container">

      <?php while( have_rows('about_us', 'option') ): the_row(); ?>

        <div class="about-content">
          <h3><?php the_sub_field('about_title'); ?></h3>
          <?php the_sub_field('about_content'); ?>
          <div class="cta about-cta">
        		<a href="<?php the_sub_field('about_page'); ?>" class="button"><?php the_sub_field('about_button_text'); ?></a>
        	</div>
        </div>

      <?php endwhile; ?>

    </div>

  </div>

</section>

<?php get_footer(); ?>
