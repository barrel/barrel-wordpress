<?php
$module = 'data-module="image"';
if (!empty($cover)) {
  $class .= ' image--cover';
}
if (!empty($contain)) {
  $class .= ' image--contain';
}
if (!empty($top)) {
  $class .= ' image--top';
}
if (empty($sizes)) {
  $sizes = '';
}
if (empty($attributes)) {
  $attributes = '';
}
if (empty($alt)) {
  $alt = '';
}
if (empty($size)) {
	$size = '';
}
if (!isset($use_srcset)) {
  $use_srcset = true;
}
$image_id = empty($id) ? $id : get_field('featured_image', 'options');
?>
<figure class="js-wrap image <?= $class ?>" <?= $module; ?> <?= $attributes; ?>>
  <?php
    the_lazy_img($image_id, $size, 'image__img', $sizes, $alt, $use_srcset);

    if (!empty($content)) {
      echo $content;
    }
  ?>
</figure>
