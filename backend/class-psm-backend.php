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
		add_action('admin_menu', array('Backend', 'update'));
		add_action('admin_head', array('Backend', 'set_head'));
		add_action('save_post', array('Backend', 'photoswipe_save_post', 10, 3));
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
				'crop_thumbnails' => (bool) (isset($_POST['crop_thumbnails']) ? true : false),
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
		include_once('psm-backend-head.php');
		echo get_backend_head();
	}

	public static function photoswipe_save_post($post_id, $post, $update) {
		$post_content = $post->post_content;
		$new_content = preg_replace_callback('/(<a((?!data\-size)[^>])+href=["\'])([^"\']*)(["\']((?!data\-size)[^>])*><img)/i', 'photoswipe_save_post_callback', $post_content);
		if ($new_content && $new_content !== $post_content) {
			remove_action('save_post', 'photoswipe_save_post', 10, 3);
			wp_update_post(array('ID' => $post_id, 'post_content' => $new_content));
			add_action('save_post', 'photoswipe_save_post', 10, 3);
		}
	}

	public static function photoswipe_save_post_callback($matches) {
		$before = $matches[1];
		$image_url = $matches[3];
		$after = $matches[4];
		$id = fjarrett_get_attachment_id_by_url($image_url);
		if ($id) {
			$image_attributes = wp_get_attachment_image_src($id, 'original');
			if ($image_attributes) {
				$before = str_replace('<a ', '<a class="single_photoswipe" data-size="' . $image_attributes[1] . 'x' . $image_attributes[2] . '" ', $before);
			}
		}
		return $before . $image_url . $after;
	}
}
