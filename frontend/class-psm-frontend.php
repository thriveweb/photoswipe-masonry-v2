<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Frontend {

	private static $current_post;
	private static $photoswipe_options;
	private static $plugin_url_path;

	public static function init() {
		Global $post;
		Global $psm_vars;
		self::$current_post = &$post;
		self::$photoswipe_options = &$psm_vars['photoswipe_options'];
		self::$plugin_url_path = &$psm_vars['plugin_url_path'];
		add_action('wp_footer', array('Frontend', 'set_photoswipe_footer'));
		add_filter('wp_get_attachment_link', array('Frontend', 'get_attachment_link'), 10, 6);
		add_action('wp_enqueue_scripts', array('Frontend', 'add_scripts'));
		add_shortcode('gallery', array('Frontend', 'shortcode'));
		add_shortcode('photoswipe', array('Frontend', 'shortcode'));
	}

	public static function set_photoswipe_footer() {
		include_once('photoswipe-footer.php');
	}

	public static function get_attachment_link($link, $id, $size, $permalink, $icon, $text ) {
		if($permalink === false && !$text && 'none' != $size) {
			$_post = get_post($id);
			$image_attributes = wp_get_attachment_image_src($_post->ID, 'original');
			if($image_attributes) {
				$link = str_replace('<a ', '<a data-size="' . $image_attributes[1] . 'x' . $image_attributes[2] . '" ', $link);
			}
		}
		return $link;
	}

	public static function add_scripts() {
		self::add_stylesheets();
		wp_enqueue_script('jquery');
		wp_enqueue_script('photoswipe', self::$plugin_url_path . '/dist/photoswipe/photoswipe.min.js');
		wp_enqueue_script('photoswipe-masonry-js', self::$plugin_url_path . '/frontend/js/photoswipe-masonry.js');
		wp_enqueue_script('photoswipe-lazyload-js', self::$plugin_url_path . '/frontend/js/photoswipe-lazyload.js');
		wp_enqueue_script('photoswipe-ui-default', self::$plugin_url_path . '/dist/photoswipe/photoswipe-ui-default.min.js');
		wp_enqueue_script('photoswipe-masonry', 	self::$plugin_url_path . '/frontend/js/masonry.pkgd.min.js', '', '', false);
		wp_enqueue_script('photoswipe-imagesloaded', self::$plugin_url_path . '/frontend/js/imagesloaded.pkgd.min.js');
	}

	private static function add_stylesheets() {
		wp_enqueue_style('photoswipe-core-css', self::$plugin_url_path . '/dist/photoswipe/photoswipe.css');
		if (self::$photoswipe_options['white_theme']) {
			wp_enqueue_style('white_theme', self::$plugin_url_path . '/dist/photoswipe/white-skin/skin.css');
		} else {
			wp_enqueue_style('pswp-skin', self::$plugin_url_path . '/dist/photoswipe/default-skin/default-skin.css');
		}
	}

	public static function shortcode($attr) {
		Global $photoswipe_count;
		$photoswipe_count += 1;
		$post_id = intval(self::$current_post->ID) . '_' . $photoswipe_count;
		$args = self::get_attributes_args($attr);
		$attachments = self::get_post_attachments($args);
		$columns = intval($args['columns']);
		$itemwidth = ($columns > 0 ? floor(100 / $columns) : 100);
		include_once('photoswipe-styling.php');
		include_once('photoswipe-html.php');
		include_once('photoswipe-script.php');
		ob_start();
		echo get_style(self::$photoswipe_options);
		echo get_html($post_id, $columns, $args, $attachments, self::$photoswipe_options);
		echo get_script($post_id, $args, self::$photoswipe_options);
		return ob_get_clean();
	}

	private static function get_attributes_args($attr) {
		if (!empty($attr['ids'])) {
			if (empty($attr['orderby'])) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
		}
		return shortcode_atts(array(
			'id' => intval(self::$current_post->ID),
			'show_controls' => self::$photoswipe_options['show_controls'],
			'columns' => 3,
			'size' => 'thumbnail',
			'order' => 'DESC',
			'orderby' => 'menu_order ID',
			'include' => '',
			'exclude' => '',
			'item_count' => self::$photoswipe_options['item_count']
		), $attr, 'gallery');
	}

	private static function get_post_attachments($args) {
		$attachments = array();
		$opts = array(
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'order' => $args['order'],
			'orderby' => $args['orderby']
		);
		if (!empty($args['include'])) {
			$opts['include'] = $args['include'];
			$_attachments = get_posts($opts);
			foreach ($_attachments as $key => $val) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} else {
			$opts['post_parent'] = $args['id'];
			if (!empty($args['exclude'])) {
				$opts['exclude'] = $args['id'];
			}
			$attachments = get_children($opts);
		}
		return $attachments;
	}
}
