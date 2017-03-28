<?php

if (!function_exists('fjarrett_get_attachment_id_by_url')) :
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
	function fjarrett_get_attachment_id_by_url($url) {
		$parsed_url = explode( parse_url(WP_CONTENT_URL, PHP_URL_PATH), $url);
		$this_host = str_ireplace('www.', '', parse_url(home_url(), PHP_URL_HOST));
		$file_host = str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
		if (!isset($parsed_url[1]) || empty($parsed_url[1]) || ($this_host != $file_host)) {
			return;
		}
		global $wpdb;
		$prefix = is_multisite() ? $wpdb->base_prefix : $wpdb->prefix;
		$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$prefix}posts WHERE guid RLIKE %s;", $parsed_url[1]));
		return $attachment[0];
	}
endif;
