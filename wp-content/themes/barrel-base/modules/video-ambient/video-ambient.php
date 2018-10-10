<div class="video-container">
  <?php if ($mobile_btn) : ?>
    <div class="play-btn">
      <?= _get_svg('play_button'); ?>
    </div>
  <?php endif; ?>
  <video
    class="video--ambient"
    data-module="video-ambient"
    data-src="<?= $src; ?>"
    autoplay="true"
    loop="true"
    muted="true"
    playsinline
    <?= $poster ? "poster=\"$poster\"" : ""; ?>></video>
  <?php if ($video_fallback) :
      the_module('image', array(
        'image' => $video_fallback
      ));
    endif; ?>
</div>
