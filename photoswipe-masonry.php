<?php
/*
Plugin Name: Photoswipe Masonry
Plugin URI: http://thriveweb.com.au/the-lab/photoswipe/
Description: This is a image gallery plugin for WordPress built using PhotoSwipe from  Dmitry Semenov.
<a href="http://photoswipe.com/">PhotoSwipe</a>
Author: Dean Oakley
Author URI: http://thriveweb.com.au/
Version: 1.2.3
Text Domain: photoswipe-masonry
*/

/*  Copyright 2010  Dean Oakley  (email : dean@thriveweb.com.au)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Illegal Entry');
}

//============================== PhotoSwipe options ========================//
class photoswipe_plugin_options {

	//Defaults
	public static function pSwipe_getOptions() {

		//Pull from WP options database table
		$options = get_option('photoswipe_options');

		if (!is_array($options)) {

			$options['show_controls'] = false;

			$options['show_captions'] = true;

			$options['use_masonry'] = false;

			$options['thumbnail_width'] = 150;
			$options['thumbnail_height'] = 150;

			$options['max_image_height'] = '2400';
			$options['max_image_width'] = '1800';

			$options['white_theme'] = false;

			update_option('photoswipe_options', $options);
		}
		return $options;
	}


	public static function update() {

		if(isset($_POST['photoswipe_save'])) {

			$options = photoswipe_plugin_options::pSwipe_getOptions();

			$options['thumbnail_width'] = stripslashes($_POST['thumbnail_width']);
			$options['thumbnail_height'] = stripslashes($_POST['thumbnail_height']);

			$options['max_image_width'] = stripslashes($_POST['max_image_width']);
			$options['max_image_height'] = stripslashes($_POST['max_image_height']);

			if (isset($_POST['white_theme'])) {
				$options['white_theme'] = (bool)true;
			} else {
				$options['white_theme'] = (bool)false;
			}

			if (isset($_POST['show_controls'])) {
				$options['show_controls'] = (bool)true;
			} else {
				$options['show_controls'] = (bool)false;
			}

			if (isset($_POST['show_captions'])) {
				$options['show_captions'] = (bool)true;
			} else {
				$options['show_captions'] = (bool)false;
			}

			if (isset($_POST['use_masonry'])) {
				$options['use_masonry'] = (bool)true;
			} else {
				$options['use_masonry'] = (bool)false;
			}

			update_option('photoswipe_options', $options);

		} else {
			photoswipe_plugin_options::pSwipe_getOptions();
		}

		add_submenu_page( 'options-general.php', 'PhotoSwipe options', 'PhotoSwipe', 'edit_theme_options', basename(__FILE__), array('photoswipe_plugin_options', 'display'));
	}


	public static function display() {

		$options = photoswipe_plugin_options::pSwipe_getOptions();
		?>

		<div id="photoswipe_admin" class="wrap">

			<h2>PhotoSwipe Options</h2>

			<p>PhotoSwipe is a image gallery plugin for WordPress built using PhotoSwipe from  Dmitry Semenov.  <a href="http://photoswipe.com/">PhotoSwipe</a></p>

			<form method="post" action="#" enctype="multipart/form-data">

				<div class="ps_border" ></div>

				<p style="font-style:italic; font-weight:normal; color:grey " >Please note: Images that are already on the server will not change size until you regenerate the thumbnails. Use <a title="http://wordpress.org/extend/plugins/ajax-thumbnail-rebuild/" href="http://wordpress.org/extend/plugins/ajax-thumbnail-rebuild/">AJAX thumbnail rebuild</a> </p>

				<div class="fl_box">
					<p>Thumbnail Width</p>
					<p><input type="text" name="thumbnail_width" value="<?php echo($options['thumbnail_width']); ?>" /></p>
				</div>

				<div class="fl_box">
					<p>Thumbnail Height</p>
					<p><input type="text" name="thumbnail_height" value="<?php echo($options['thumbnail_height']); ?>" /></p>
				</div>

				<div class="fl_box">
					<p>Max image width</p>
					<p><input type="text" name="max_image_width" value="<?php echo($options['max_image_width']); ?>" /></p>
				</div>

				<div class="fl_box">
					<p>Max image height</p>
					<p><input type="text" name="max_image_height" value="<?php echo($options['max_image_height']); ?>" /></p>
				</div>

				<div class="ps_border" ></div>

				<p><label><input name="white_theme" type="checkbox" value="checkbox" <?php if($options['white_theme']) echo "checked='checked'"; ?> /> Use white theme?</label></p>

				<p><label><input name="show_captions" type="checkbox" value="checkbox" <?php if($options['show_captions']) echo "checked='checked'"; ?> /> Show captions on thumbnails?</label></p>

				<p><label><input name="use_masonry" type="checkbox" value="checkbox" <?php if($options['use_masonry']) echo "checked='checked'"; ?> />Don't use Masonry?</label></p>


				<p><input class="button-primary" type="submit" name="photoswipe_save" value="Save Changes" /></p>

			</form>

		</div>

		<?php
	}
}


function pSwipe_getOption($option) {
	global $mytheme;
	return $mytheme->option[$option];
}

// register functions
add_action('admin_menu', array('photoswipe_plugin_options', 'update'));

$options = get_option('photoswipe_options');

//image sizes - No cropping for a nice zoom effect
add_image_size('photoswipe_thumbnails', $options['thumbnail_width'] * 2, $options['thumbnail_height'] * 2, false);
add_image_size('photoswipe_full', $options['max_image_width'], $options['max_image_height'], false);

//Admin CSS
function photoswipe_register_head() {

    $url = plugins_url( 'admin.css', __FILE__ );

    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}
add_action('admin_head', 'photoswipe_register_head');

add_action('wp_footer', 'photoswipe_footer');


//Link attachments
function photoswipe_get_attachment_link($link, $id, $size, $permalink, $icon, $text ) {
	if( $permalink === false && !$text && 'none' != $size ) {
		$_post = get_post( $id );

		$image_attributes = wp_get_attachment_image_src( $_post->ID, 'original' );

		if( $image_attributes ) {
			$link = str_replace('<a ', '<a data-size="' . $image_attributes[1] . 'x' . $image_attributes[2] . '" ', $link);
		}
	}

	return $link;
}
add_filter( 'wp_get_attachment_link', 'photoswipe_get_attachment_link', 10, 6 );

//Update embeds on save
function photoswipe_save_post( $post_id, $post, $update ) {
	$post_content = $post->post_content;

	$new_content = preg_replace_callback( '/(<a((?!data\-size)[^>])+href=["\'])([^"\']*)(["\']((?!data\-size)[^>])*><img)/i', 'photoswipe_save_post_callback', $post_content );

	if( !!$new_content && $new_content !== $post_content ) {
		remove_action( 'save_post', 'photoswipe_save_post', 10, 3 );

		wp_update_post( array( 'ID' => $post_id, 'post_content' => $new_content ) );

		add_action( 'save_post', 'photoswipe_save_post', 10, 3 );
	}
}
add_action( 'save_post', 'photoswipe_save_post', 10, 3 );

function photoswipe_save_post_callback( $matches ) {
	$before = $matches[1];
	$image_url = $matches[3];
	$after = $matches[4];

	$id = fjarrett_get_attachment_id_by_url($image_url);

	if( $id ) {
		$image_attributes = wp_get_attachment_image_src( $id, 'original' );
		if( $image_attributes ) {
			$before = str_replace('<a ', '<a class="single_photoswipe" data-size="' . $image_attributes[1] . 'x' . $image_attributes[2] . '" ', $before);
		}
	}

	return $before . $image_url . $after;
}

function photoswipe_kses_allow_attributes() {
	global $allowedposttags;
	$allowedposttags['a']['data-size'] = array();
}
add_action( 'init', 'photoswipe_kses_allow_attributes' );

if( !function_exists('fjarrett_get_attachment_id_by_url') ) :
	/**
	 * Return an ID of an attachment by searching the database with the file URL.
	 *
	 * First checks to see if the $url is pointing to a file that exists in
	 * the wp-content directory. If so, then we search the database for a
	 * partial match consisting of the remaining path AFTER the wp-content
	 * directory. Finally, if a match is found the attachment ID will be
	 * returned.
	 *
	 * @param string $url The URL of the image (ex: http://mysite.com/wp-content/uploads/2013/05/test-image.jpg)
	 *
	 * @return int|null $attachment Returns an attachment ID, or null if no attachment is found
	 */
	function fjarrett_get_attachment_id_by_url( $url ) {
		// Split the $url into two parts with the wp-content directory as the separator
		$parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

		// Get the host of the current site and the host of the $url, ignoring www
		$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
		$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

		// Return nothing if there aren't any $url parts or if the current host and $url host do not match
		if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
			return;
		}

		// Now we're going to quickly search the DB for any attachment GUID with a partial path match
		// Example: /uploads/2013/05/test-image.jpg
		global $wpdb;
		$prefix = is_multisite() ? $wpdb->base_prefix : $wpdb->prefix;

		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );

		// Returns null if no attachment is found
		return $attachment[0];
	}
endif;


function photoswipe_scripts_method() {

	$options = get_option('photoswipe_options');
	$photoswipe_wp_plugin_path =  plugins_url() . '/photoswipe-masonry' ;

	wp_enqueue_style( 'photoswipe-core-css',	$photoswipe_wp_plugin_path . '/photoswipe-dist/photoswipe.css');


	// Skin CSS file (optional)
    // In folder of skin CSS file there are also:
    // - .png and .svg icons sprite,
    // - preloader.gif (for browsers that do not support CSS animations)
    if($options['white_theme']) wp_enqueue_style( 'white_theme', $photoswipe_wp_plugin_path . '/photoswipe-dist/white-skin/skin.css'  );
    else wp_enqueue_style( 'pswp-skin', $photoswipe_wp_plugin_path . '/photoswipe-dist/default-skin/default-skin.css'  );

	wp_enqueue_script('jquery');

	//Core JS file
	wp_enqueue_script( 'photoswipe', 			$photoswipe_wp_plugin_path . '/photoswipe-dist/photoswipe.min.js');

	wp_enqueue_script( 'photoswipe-masonry-js', $photoswipe_wp_plugin_path . '/photoswipe-masonry.js');

	//UI JS file
	wp_enqueue_script( 'photoswipe-ui-default', $photoswipe_wp_plugin_path . '/photoswipe-dist/photoswipe-ui-default.min.js');

	//Masonry - re-named to move to header
	wp_enqueue_script( 'photoswipe-masonry', 	$photoswipe_wp_plugin_path . '/masonry.pkgd.min.js','','',false);
	//imagesloaded
	wp_enqueue_script( 'imagesloaded', 			$photoswipe_wp_plugin_path . '/imagesloaded.pkgd.min.js','','',false);

}
add_action('wp_enqueue_scripts', 'photoswipe_scripts_method');

add_shortcode( 'gallery', 'photoswipe_shortcode' );
add_shortcode( 'photoswipe', 'photoswipe_shortcode' );


function photoswipe_shortcode( $attr ) {

	global $post;
	global $photoswipe_count;

	$options = get_option('photoswipe_options');

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) ) {
			$attr['orderby'] = 'post__in';
		}
		$attr['include'] = $attr['ids'];
	}

	$args = shortcode_atts(array(
		'id' 				=> intval($post->ID),
		'show_controls' 	=> $options['show_controls'],
		'columns'    => 3,
		'size'       => 'thumbnail',
		'order'      => 'DESC',
		'orderby'    => 'menu_order ID',
		'include'    => '',
		'exclude'    => ''
	), $attr);

	$photoswipe_count += 1;
	$post_id = intval($post->ID) . '_' . $photoswipe_count;


	$output_buffer='';

	    if ( !empty($args['include']) ) {

			//"ids" == "inc"

			$include = preg_replace( '/[^0-9,]+/', '', $args['include'] );
			$_attachments = get_posts( array('include' => $args['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $args['order'], 'orderby' => $args['orderby']) );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}

		} elseif ( !empty($args['exclude']) ) {
			$exclude = preg_replace( '/[^0-9,]+/', '', $args['exclude'] );
			$attachments = get_children( array('post_parent' => $args['id'], 'exclude' => $args['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $args['order'], 'orderby' => $args['orderby']) );
		} else {

			$attachments = get_children( array('post_parent' => $args['id'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $args['order'], 'orderby' => $args['orderby']) );

		}

		$columns = intval($args['columns']);
        $itemwidth = $columns > 0 ? floor(100/$columns) : 100;


		$output_buffer .= "

		<style type='text/css'>

			/* PhotoSwipe Plugin */
			.psgal {
				margin: auto;
				padding-bottom:40px;

				-webkit-transition: all 0.4s ease;
				-moz-transition: all 0.4s ease;
				-o-transition: all 0.4s ease;
				transition: all 0.4s ease;

				opacity:0.1;
				";

				if($options['use_masonry']) $output_buffer .="opacity:1; text-align:center;";

				$output_buffer .= "

			}

			.psgal.photoswipe_showme{
				opacity:1;
			}

			.psgal figure {
				float: left;

				";

				if($options['use_masonry']) $output_buffer .="float:none; display:inline-block;;";

				$output_buffer .= "

				text-align: center;
				width: ".$options['thumbnail_width']."px;

				padding:5px;
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
			";

			if(!$options['show_captions']) $output_buffer .="

			.photoswipe-gallery-caption{
				display:none;
			}

			";

			$output_buffer .= "
		</style>";

		$size_class = sanitize_html_class( $args['size'] );
		$output_buffer .=' <div style="clear:both"></div>

		<div id="psgal_'.$post_id.'" class="psgal gallery-columns-'.$columns.' gallery-size-'.$size_class.'" itemscope itemtype="http://schema.org/ImageGallery" >';


		if ( !empty($attachments) ) {
			foreach ( $attachments as $aid => $attachment ) {

				$thumb = wp_get_attachment_image_src( $aid , 'photoswipe_thumbnails');

				$full = wp_get_attachment_image_src( $aid , 'photoswipe_full');

				$_post = get_post($aid);

				$image_title = esc_attr($_post->post_title);
				$image_alttext = get_post_meta($aid, '_wp_attachment_image_alt', true);
				$image_caption = $_post->post_excerpt;
				$image_description = $_post->post_content;

				$output_buffer .='
				<figure class="msnry_item" itemscope itemtype="http://schema.org/ImageObject">
					<a href="'. $full[0] .'" itemprop="contentUrl" data-size="'.$full[1].'x'.$full[2].'" data-caption="'. $image_caption .'" >
				        <img src='. $thumb[0] .' itemprop="thumbnail" alt="'.$image_alttext.'"  />
				    </a>
				    <figcaption class="photoswipe-gallery-caption" >'. $image_caption .'</figcaption>
			    </figure>
				';

			}
		}



		$output_buffer .="</div>

		<div style='clear:both'></div>

		<script type='text/javascript'>

			var container_".$post_id." = document.querySelector('#psgal_".$post_id."');
			var msnry;

			// initialize  after all images have loaded
			imagesLoaded( container_".$post_id.", function() {

				";

				if(!$options['use_masonry']){
					 $output_buffer .="

						// initialize Masonry after all images have loaded
						new Masonry( container_".$post_id.", {
						  // options...
						  itemSelector: '.msnry_item',
						  //columnWidth: ".$options['thumbnail_width'].",
						  isFitWidth: true
						});

						(container_".$post_id.").className += ' photoswipe_showme';

						";
				}

				$output_buffer .="

			});


		</script>

	";


		return $output_buffer;
}


function photoswipe_footer() {
	echo <<<EOF
			<!-- Root element of PhotoSwipe. Must have class pswp. -->
			<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

			    <!-- Background of PhotoSwipe.
			         Its a separate element, as animating opacity is faster than rgba(). -->
			    <div class="pswp__bg"></div>

			    <!-- Slides wrapper with overflow:hidden. -->
			    <div class="pswp__scroll-wrap">

			        <!-- Container that holds slides.
			                PhotoSwipe keeps only 3 slides in DOM to save memory. -->
			        <div class="pswp__container">
			            <!-- dont modify these 3 pswp__item elements, data is added later on -->
			            <div class="pswp__item"></div>
			            <div class="pswp__item"></div>
			            <div class="pswp__item"></div>
			        </div>

			        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
			        <div class="pswp__ui pswp__ui--hidden">

			            <div class="pswp__top-bar">

			                <!--  Controls are self-explanatory. Order can be changed. -->

			                <div class="pswp__counter"></div>

			                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>

			                <button class="pswp__button pswp__button--share" title="Share"></button>

			                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

			                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

			                <!-- Preloader demo http://codepen.io/dimsemenov/pen/yyBWoR -->
			                <!-- element will get class pswp__preloader--active when preloader is running -->
			                <div class="pswp__preloader">
			                    <div class="pswp__preloader__icn">
			                      <div class="pswp__preloader__cut">
			                        <div class="pswp__preloader__donut"></div>
			                      </div>
			                    </div>
			                </div>
			            </div>

			            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
			                <div class="pswp__share-tooltip"></div>
			            </div>

			            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
			            </button>

			            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
			            </button>

			            <div class="pswp__caption">
			                <div class="pswp__caption__center"></div>
			            </div>

			          </div>

			        </div>

			</div>
EOF;

}
