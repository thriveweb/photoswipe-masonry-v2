<?php
/*
Plugin Name: Photoswipe Masonry
Plugin URI: http://thriveweb.com.au/the-lab/photoswipe/
Description: This is a image gallery plugin for WordPress built using PhotoSwipe from  Dmitry Semenov.
<a href="http://photoswipe.com/">PhotoSwipe</a>
Author: Dean Oakley
Author URI: http://thriveweb.com.au/
Version: 1.2.6
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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Illegal Entry');
}

require_once(plugin_dir_path( __FILE__ ) . 'includes/class-photoswipe-options.php');
require_once(plugin_dir_path( __FILE__ ) . 'includes/class-psm-settings.php');
require_once(plugin_dir_path( __FILE__ ) . 'backend/class-psm-backend.php');
require_once(plugin_dir_path( __FILE__ ) . 'frontend/class-psm-frontend.php');
require_once(plugin_dir_path( __FILE__ ) . 'includes/psm-functions.php');

$psm_vars = array (
	'plugin_url_path' => plugins_url() . '/photoswipe-masonry',
	'photoswipe_options' => Photoswipe_Options::get_options()
);

//init backend and register functions
Settings::set();
Backend::init();
Frontend::init();
