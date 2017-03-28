<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Frontend {

	public static function init() {
		add_action('wp_footer', array('Frontend', 'set_photoswipe_footer'));
	}

	public static function set_photoswipe_footer() {
		include_once('photoswipe-footer.php');
	}
}
