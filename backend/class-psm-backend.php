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
		ob_start();
		?>
		<div id="photoswipe_admin" class="wrap">
			<h2>PhotoSwipe Options</h2>
			<p>
				PhotoSwipe is a image gallery plugin for WordPress built using PhotoSwipe from  Dmitry Semenov.  <a href="http://photoswipe.com/">PhotoSwipe</a>
			</p>
			<form method="post" action="#" enctype="multipart/form-data">
				<div class="ps_border" ></div>
				<p style="font-style:italic; font-weight:normal; color:grey " >
					Please note: Images that are already on the server will not change size until you regenerate the thumbnails. Use <a title="http://wordpress.org/extend/plugins/ajax-thumbnail-rebuild/" href="http://wordpress.org/extend/plugins/ajax-thumbnail-rebuild/">AJAX thumbnail rebuild</a>
				</p>
				<div class="fl_box">
					<p>Thumbnail Width</p>
					<p>
						<input type="text" name="thumbnail_width" value="<?php echo($options['thumbnail_width']); ?>" />
					</p>
				</div>
				<div class="fl_box">
					<p>Thumbnail Height</p>
					<p>
						<input type="text" name="thumbnail_height" value="<?php echo($options['thumbnail_height']); ?>" />
					</p>
				</div>
				<div class="fl_box">
					<p>Max image width</p>
					<p>
						<input type="text" name="max_image_width" value="<?php echo($options['max_image_width']); ?>" />
					</p>
				</div>
				<div class="fl_box">
					<p>Max image height</p>
					<p>
						<input type="text" name="max_image_height" value="<?php echo($options['max_image_height']); ?>" />
					</p>
				</div>
				<div class="ps_border" ></div>
				<p>
					<label>
						<input name="item_count" type="number" value="<?php if ($options['item_count']) echo $options['item_count']; else echo 10 ?>" max="500" />
						Thumbnails per page
					</label>
				</p>
				<p>
					<label>
						<input name="white_theme" type="checkbox" value="checkbox" <?php if($options['white_theme']) echo "checked='checked'"; ?> />
						Use white theme?
					</label>
				</p>
				<p>
					<label>
						<input name="show_captions" type="checkbox" value="checkbox" <?php if($options['show_captions']) echo "checked='checked'"; ?> />
						Show captions on thumbnails?
					</label>
				</p>
				<p>
					<label>
						<input name="use_masonry" type="checkbox" value="checkbox" <?php if($options['use_masonry']) echo "checked='checked'"; ?> />
						Don't use Masonry?
					</label>
				</p>
				<p>
					<input class="button-primary" type="submit" name="photoswipe_save" value="Save Changes" />
				</p>
			</form>
		</div>
		<?php
		echo ob_get_clean();
	}

	public static function set_head() {
		ob_start(); ?>
		<link rel="stylesheet" type="text/css" href="<?= plugins_url( '/css/style.css', __FILE__ ) ?>" />
		<?php
		echo ob_get_clean();
	}
}
