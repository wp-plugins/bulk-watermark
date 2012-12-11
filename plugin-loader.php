<?php
/*
Plugin Name: Bulk Watermark
Plugin URI: http://MyWebsiteAdvisor.com/tools/wordpress-plugins/bulk-watermark/
Description: Add transparent PNG image and text signature watermark to your previously uploaded images.
Version: 1.4.6
Author: MyWebsiteAdvisor
Author URI: http://MyWebsiteAdvisor.com
*/

register_activation_hook(__FILE__, 'bulk_watermark_activate');

function bulk_watermark_activate() {

	// display error message to users
	if ($_GET['action'] == 'error_scrape') {                                                                                                   
		die("Sorry, Bulk Watermark Plugin requires PHP 5.0 or higher. Please deactivate Bulk Watermark Plugin.");                                 
	}

	if ( version_compare( phpversion(), '5.0', '<' ) ) {
		trigger_error('', E_USER_ERROR);
	}
}

// require Bulk Watermark Plugin if PHP 5 installed
if ( version_compare( phpversion(), '5.0', '>=') ) {
	define('BW_LOADER', __FILE__);

	require_once(dirname(__FILE__) . '/bulk-watermark.php');
	require_once(dirname(__FILE__) . '/plugin-admin.php');
	
	$bulk_watermark = new Bulk_Watermark_Admin();

}
?>