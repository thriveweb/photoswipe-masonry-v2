<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Settings {

  private static $photoswipe_options;

  public static function set() {
    Global $psm_vars;
    self::$photoswipe_options = &$psm_vars['photoswipe_options'];

    //image sizes - No cropping for a nice zoom effect
    add_image_size('photoswipe_thumbnails', self::$photoswipe_options['thumbnail_width'] * 2, self::$photoswipe_options['thumbnail_height'] * 2, false);
    add_image_size('photoswipe_full', self::$photoswipe_options['max_image_width'], self::$photoswipe_options['max_image_height'], false);
  }
}
