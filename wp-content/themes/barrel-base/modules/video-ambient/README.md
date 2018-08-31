# Summary
This module is to be used for video backgrounds. It will lazy-load, autoplay, and loop the video file provided in the background.

# Inclusion
It should be included inside of the container that holds it. The video is styles to fill the full space of whatever container it's included it, so this module should not have any specific sizing in it. 

To include this module in some other module (hero.php, for example):

```php
<div class="hero-container">
    <?php the_module('video-ambient', array(
        'src' => $video_src,
        'mobile_button' => true,
        'poster' => $poster_image_file,
        'video_fallback' => $fallback_image_file
    ));
</div>
```

# Parameters
- `$src` (string) required - The url of the video file to be used.
- `$mobile_button` (bool) - Whether or not to use a play button on mobile. If false, video should revert to image on mobile (this may be a TODO).
- `$poster` (string) - The image to be used for the video poster. This is likely what will be displayed on mobile before video is played, although it may cause some counter-intuitive display behavior. We may want to remove this and simply use an image instead. 
- `$video_fallback` (string) - The path to the image that should be used as a fallback incase the video is not loaded or available. 


# TODO
- Respect the `mobile_button` condition for mobile behavior. If false, video is `display:none` and mobile image is displayed. If true, video poster is diplayed with a play button over top of it. Play button will pull from `assets/svg/play-button.svg`.
