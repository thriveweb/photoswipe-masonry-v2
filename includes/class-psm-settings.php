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
		add_image_size('photoswipe_thumbnails', self::$photoswipe_options['thumbnail_width'], self::$photoswipe_options['thumbnail_height'], self::$photoswipe_options['crop_thumbnails']);
		add_image_size('photoswipe_full', self::$photoswipe_options['max_image_width'], self::$photoswipe_options['max_image_height'], false);
		add_action('init', array('Settings', 'photoswipe_kses_allow_attributes'));
	}

	public static function photoswipe_kses_allow_attributes() {
		Global $allowedposttags;
		$allowedposttags['a']['data-size'] = array();
	}
}
