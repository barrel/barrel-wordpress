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
if (!isset($use_srcset)) {
  $use_srcset = true;
}
?>
<figure class="js-wrap image <?= $class ?>" <?= $module; ?> <?= $attributes; ?>>
  <?php
    if (!empty($image)) {
      the_lazy_img($image, $size, 'image__img', $sizes, $alt, $use_srcset);
    }

    if (!empty($content)) {
      echo $content;
    }
  ?>
</figure>
