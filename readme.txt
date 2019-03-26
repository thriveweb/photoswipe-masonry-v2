=== PhotoSwipe Masonry 2.0 ===
Contributors: deanoakley, vweltje
Author: Web Design Gold Coast
Author URI: http://thriveweb.com.au/
Plugin URI: http://thriveweb.com.au/the-lab/photoswipe-v2
Developers: Dean Oakley | Vincent Weltje | Alex Frith | Eric Jinks | Samantha Scott
Tags: photoswipe, gallery, image gallery, website gallery, photoalbum, photogallery, photo, plugin, images, slideshow, short code, responsive, native gallery
Requires at least: 3.0
Tested up to: 4.9.6
Stable tag: 2.0

PhotoSwipe Masonry 2.0 takes advantage of the built in gallery features of WordPress. The gallery is built using PhotoSwipe from Dmitry Semenov.

== Description ==

PhotoSwipe Masonry 2.0 is an image gallery plugin for WordPress built using PhotoSwipe from Dmitry Semenov. (http://photoswipe.com/ "PhotoSwipe")
PhotoSwipe Masonry 2.0 is an extension of our previous release, and takes advantage of the built in gallery features of WordPress.
Simply use the WordPress admin to create a gallery and insert it in the page.
You may need to adjust the size of the thumbnails to suit your theme in the settings.

Options are under Settings > PhotoSwipe 2.0

The PhotoSwipe Masonry 2.0 gallery plugin allows you to:

* Upload multiple images at once
* Easily order images via drag and drop
* Add a title and caption

Via the options panel you can modify:

* Thumbnail size
* Full image size

Some other features include:

* Keyboard control
* Supports multiple galleries


Filter:
Removes menu page for non admins ( thanks to lucspe )
add_filter( 'photoswipe_menu_capability', 'photoswipe_menu_custom_cap' );
function photoswipe_menu_custom_cap() {
    return 'manage_options';
}


See a demo here: (http://thriveweb.com.au/the-lab/photoswipe-v2 "PhotoSwipe Masonry 2.0")

Want to contribute? See the GitHub repo (https://github.com/thriveweb/photoswipe-masonry-v2 "github.com/thriveweb/photoswipe-masonry-v2")

== Installation ==

1. Upload `/photoswipe-masonry-v2/` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Upload some photos to the post or page where you want the gallery
4. Use WordPress to create a gallery and insert it in the page

== Screenshots ==

1. Screenshot Default gallery layout
2. Screenshot Popup layout

== Changelog ==

= 2.0 =
* Better code structure
* Fixed lazy loading bugs
* Added option to resize thumbnails after changing image dimensions
* Added a nice and clean backend view
