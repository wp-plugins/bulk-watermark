<?php
/*
Plugin Name: Bulk Watermark
Plugin URI: http://MyWebsiteAdvisor.com/tools/wordpress-plugins/signature-watermark/
Description: Add transparent PNG image and text signature watermark to your previously uploaded images.
Version: 1.1
Author: MyWebsiteAdvisor
Author URI: http://MyWebsiteAdvisor.com
*/

register_activation_hook(__FILE__, 'bulk_watermark_activate');

// display error message to users
if ($_GET['action'] == 'error_scrape') {                                                                                                   
    die("Sorry, Bulk Watermark Ultra Plugin requires PHP 5.0 or higher. Please deactivate Bulk Watermark Plugin.");                                 
}

function bulk_watermark_activate() {
	if ( version_compare( phpversion(), '5.0', '<' ) ) {
		trigger_error('', E_USER_ERROR);
	}
}

// require Signature Watermark Ultra Plugin if PHP 5 installed
if ( version_compare( phpversion(), '5.0', '>=') ) {
	define('TW_LOADER', __FILE__);

	require_once(dirname(__FILE__) . '/bulk-watermark.php');
	require_once(dirname(__FILE__) . '/plugin-admin.php');
	
	$bulk_watermark = new Bulk_Watermark_Admin();

}
?>