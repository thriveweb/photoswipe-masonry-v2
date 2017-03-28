<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Backend {

	private static $photoswipe_options;

	public static function init() {
		Global $psm_vars;
		self::$photoswipe_options = &$psm_vars['photoswipe_options'];
		// register functions
		add_action('admin_menu', array('Backend', 'update'));
		add_action('admin_head', array('Backend', 'set_head'));
	}

	public static function update() {
		Global $psm_vars;
		if(isset($_POST['photoswipe_save'])) {
			$current_options = self::$photoswipe_options;
			$options_new = array(
				'thumbnail_width' => stripslashes($_POST['thumbnail_width']),
				'thumbnail_height' => stripslashes($_POST['thumbnail_height']),
				'max_image_width' => stripslashes($_POST['max_image_width']),
				'max_image_height' => stripslashes($_POST['max_image_height']),
				'white_theme' => (bool) (isset($_POST['white_theme']) ? true : false),
				'show_controls' => (bool) (isset($_POST['show_controls']) ? true : false),
				'show_captions' => (bool) (isset($_POST['show_captions']) ? true : false),
				'use_masonry' => (bool) (isset($_POST['use_masonry']) ? true : false),
				'item_count' => (int) (isset($_POST['item_count']) ? $_POST['item_count'] : false)
			);
			$psm_vars['photoswipe_options'] = array_merge($current_options, $options_new);
			update_option('photoswipe_options', self::$photoswipe_options);
		} else {
			Photoswipe_Options::get_options();
		}
		add_submenu_page( 'options-general.php', 'PhotoSwipe options', 'PhotoSwipe', 'edit_theme_options', basename(__FILE__), array('Backend', 'display'));
	}

	public static function display() {
		$options = self::$photoswipe_options;
		include_once('settings-view.php');
		echo show_settings_view($options);
	}

	public static function set_head() {
		ob_start(); ?>
		<link rel="stylesheet" type="text/css" href="<?= plugins_url( '/css/style.css', __FILE__ ) ?>" />
		<?php
		echo ob_get_clean();
	}
}
