<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Frontend {

	private static $photoswipe_options;
	private static $plugin_url_path;

	public static function init() {
		Global $psm_vars;
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
		Global $post;
		Global $photoswipe_count;
		if (!empty($attr['ids'])) {
			if (empty($attr['orderby'])) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
		}
		$args = shortcode_atts(array(
			'id' => intval($post->ID),
			'show_controls' => self::$photoswipe_options['show_controls'],
			'columns' => 3,
			'size' => 'thumbnail',
			'order' => 'DESC',
			'orderby' => 'menu_order ID',
			'include' => '',
			'exclude' => '',
			'item_count' => self::$photoswipe_options['item_count']
		), $attr, 'gallery');
		$photoswipe_count += 1;
		$post_id = intval($post->ID) . '_' . $photoswipe_count;
		if (!empty($args['include'])) {
			$include = preg_replace('/[^0-9,]+/', '', $args['include']);
			$_attachments = get_posts(array(
				'include' => $args['include'],
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => $args['order'],
				'orderby' => $args['orderby']
			));
			$attachments = array();
			foreach ($_attachments as $key => $val) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif (!empty($args['exclude'])) {
			$exclude = preg_replace('/[^0-9,]+/', '', $args['exclude']);
			$attachments = get_children(array(
				'post_parent' => $args['id'],
				'exclude' => $args['exclude'],
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => $args['order'],
				'orderby' => $args['orderby']
			));
		} else {
			$attachments = get_children(array(
				'post_parent' => $args['id'],
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => $args['order'],
				'orderby' => $args['orderby']
			));
		}
		$columns = intval($args['columns']);
		$itemwidth = ($columns > 0 ? floor(100 / $columns) : 100);

		ob_start();
		?>
		<style type='text/css'>
		/* PhotoSwipe Plugin */
		.psgal {
			margin: auto;
			padding-bottom:40px;
			-webkit-transition: all 0.4s ease;
			-moz-transition: all 0.4s ease;
			-o-transition: all 0.4s ease;
			transition: all 0.4s ease;
			<?php if(self::$photoswipe_options['use_masonry']) : ?>
			opacity: 1;
			text-align: center;
			<?php endif; ?>
		}

		.psgal.photoswipe_showme{
			opacity:1;
		}

		.psgal figure {
			float: left;
			<?php if(self::$photoswipe_options['use_masonry']) : ?>
			float: none;
			display: inline-block;
			<?php endif; ?>
			text-align: center;
			width: <?= self::$photoswipe_options['thumbnail_width'] . 'px' ?>;
			padding: 5px;
			margin: 0px;
			box-sizing:border-box;
		}

		.psgal a{
			display:block;
		}

		.psgal img {
			margin:auto;
			max-width:100%;
			width: auto;
			height: auto;
			border: 0;
		}

		.psgal figure figcaption{
			font-size:13px;
		}

		.msnry{
			margin:auto;
		}

		.pswp__caption__center{
			text-align: center;
		}

		<?php if(!self::$photoswipe_options['show_captions']) : ?>
		.photoswipe-gallery-caption{
			display:none;
		}
		<?php endif; ?>

		</style>

		<div style="clear:both"></div>
		<div id="psgal_<?= $post_id ?>" class="psgal gallery-columns-<?= $columns ?> gallery-size-<?= sanitize_html_class($args['size']) ?>" itemscope itemtype="http://schema.org/ImageGallery">
			<?php if (!empty($attachments)) :
				$i = 0;
				foreach ($attachments as $aid => $attachment) :
					$i++;
					$img_srcset = wp_get_attachment_image_srcset($aid, 'photoswipe_thumbnails');
					$thumb = wp_get_attachment_image_src($aid , 'photoswipe_thumbnails');
					$full = wp_get_attachment_image_src($aid , 'photoswipe_full');
					$_post = get_post($aid);
					$image_title = esc_attr($_post->post_title);
					$image_alttext = get_post_meta($aid, '_wp_attachment_image_alt', true);
					$image_caption = $_post->post_excerpt;
					$image_description = $_post->post_content;
					?>
					<figure class="msnry_item" itemscope itemtype="http://schema.org/ImageObject" <?= ($i > $args['item_count'] ? 'style="display:none;"' : '') ?>>
						<a href="<?= $full[0] ?>" itemprop="contentUrl" data-size="<?= $full[1] . 'x' . $full[2] ?>" data-caption="<?= $image_caption ?>">
							<img
							data-src="<?= $thumb[0] ?>"
							src="<?= ($i <= $args['item_count'] ? $thumb[0] : '') ?>"
							itemprop="thumbnail"
							alt="<?= $image_alttext ?>" />
						</a>
						<figcaption class="photoswipe-gallery-caption"><?= $image_caption ?></figcaption>
					</figure>
				<?php endforeach;
			endif; ?>
		</div>
		<div style='clear:both'></div>
		<script type='text/javascript'>
		var container_<?= $post_id ?> = document.querySelector('#psgal_<?= $post_id ?>');
		var grid_<?= $post_id ?>;

		<?php if(!self::$photoswipe_options['use_masonry']) : ?>
		// initialize Masonry after all images have loaded
		grid_<?= $post_id ?> = jQuery('#psgal_<?= $post_id ?>').masonry({
			// options...
			itemSelector: '.msnry_item',
			//columnWidth: ".self::$photoswipe_options['thumbnail_width'].",
			isFitWidth: true
		});

		grid_<?= $post_id ?>.imagesLoaded().progress( function() {
			grid_<?= $post_id ?>.masonry('layout');
		});
		<?php endif; ?>

		if (jQuery('#psgal_<?= $post_id ?> .msnry_item:last-of-type').index() + 1 > <?= $args['item_count'] ?>) {
			var loadCount_<?= $post_id ?> = 1;
			var loadingImages_<?= $post_id ?> = false;

			jQuery(document).on('scroll', function() {
				var lastImgNth_<?= $post_id ?> = (<?= $args['item_count'] ?> * loadCount_<?= $post_id ?>),
				lastImg_<?= $post_id ?> = jQuery('#psgal_<?= $post_id ?> .msnry_item:nth-child(' + lastImgNth_<?= $post_id ?> + ')');

				if (!loadingImages_<?= $post_id ?> && (lastImg_<?= $post_id ?>.length && (jQuery(document).scrollTop() + (jQuery(window).height() / 2)) >= (lastImg_<?= $post_id ?>.offset().top + lastImg_<?= $post_id ?>.height()))) {
					loadCount_<?= $post_id ?>++;
					loadingImages_<?= $post_id ?> = true;

					for (var i = 1; i <= <?= $args['item_count'] ?>; i++) {
						if (i >= (jQuery('#psgal_<?= $post_id ?> .msnry_item:last-of-type').index() + 1)) {
							return;
						}

						var img = jQuery('#psgal_<?= $post_id ?> .msnry_item:nth-child(' + (lastImgNth_<?= $post_id ?> + i) + ') a img');
						img.attr('src', img.data('src'));
					}

					<?php if(!self::$photoswipe_options['use_masonry']) : ?>
					grid_<?= $post_id ?>.imagesLoaded().progress( function() {
						jQuery('#psgal_<?= $post_id ?> .msnry_item').css('display', 'block');
						grid_<?= $post_id ?>.masonry('layout');
						loadingImages_<?= $post_id ?> = false;
					});
					<?php endif; ?>
				}
			});
		}
		</script>
		<?php
		return ob_get_clean();
	}
}
