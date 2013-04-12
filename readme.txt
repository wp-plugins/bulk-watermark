=== Bulk Watermark ===
Name: Bulk Watermark
Contributors: MyWebsiteAdvisor, ChrisHurst
Tags: Watermark, Signature, Images, Image, Picture, Pictures, Photo, Photos, Upload, Post, Plugin, Page, Admin
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.6.6
Donate link: http://MyWebsiteAdvisor.com/donations/

Adds an image and/or text watermark to all uploaded images, using PNG images with transparency.


== Description ==

This plugin allows you to watermark your previously uploaded images using a PNG image file with transparency as well as text signature.  
The user friendly settings page allows for control over the appearance of your watermark.  
You can set your watermarks to always be a specific percentage of the target image.  
This plugin will add the watermarks to ALL of the images in your WordPress uploads directory.
Please BACK UP all of your uploads via FTP before using this plugin!


<a href="http://mywebsiteadvisor.com/products-page/premium-wordpress-plugins/bulk-watermark-ultra/">**Upgrade to Bulk Watermark Ultra**</a> for advanced
watermark features including:

* Fully Adjustable Text and Image Watermark Positions
* Highest Quality Watermarks using Image Re-sampling rather than Re-sizing
* Lifetime Priority Support and Update License


Check out the [Bulk Watermark Plugin for WordPress Video Tutorial](http://www.youtube.com/watch?v=XkFXBjfzw2I&hd=1):

http://www.youtube.com/watch?v=XkFXBjfzw2I&hd=1



Developer Website: http://MyWebsiteAdvisor.com/

Plugin Support: http://MyWebsiteAdvisor.com/support/

Plugin Page: http://MyWebsiteAdvisor.com/tools/wordpress-plugins/bulk-watermark/

Video Tutorial: http://mywebsiteadvisor.com/learning/video-tutorials/bulk-watermark-tutorial/




Requirements:

* PHP v5.0+
* WordPress v3.3+
* GD extension for PHP
* FreeType extension for PHP



To-do:


== Installation ==

1. Upload `bulk-watermark/` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Bulk Watermark settings and enable Bulk Watermark Plugin.


Check out the [Bulk Watermark Plugin for WordPress Video Tutorial](http://www.youtube.com/watch?v=XkFXBjfzw2I&hd=1):

http://www.youtube.com/watch?v=XkFXBjfzw2I&hd=1




== Frequently Asked Questions ==

= Plugin doesn't work ... =

Please specify as much information as you can to help us debug the problem. 
Check in your error.log if you can. 
Please send screenshots as well as a detailed description of the problem.



= Error message says that I don't have GD or FreeType extension installed =

Contact your hosting provider and ask them to enable GD extension for your host,  
GD extension is required for watermarking.
FreeType extension is required for text watermarks.




= Error message says that I need to enable the allow_url_fopen option =

Contact your hosting provider and ask them to enable allow_url_fopen, most likely in your php.ini  
It may be necessary to create a php.ini file inside of the wp-admin directory to enable the allow_url_fopen option.



= How do I Remove Watermarks? =

This plugin permenantly alters the images to contain the watermarks, so the watermarks can not be removed. 
If you want to simply test this plugin, or think you may want to remove the watermarks, you need to make a backup of your images before you use the plugin to add watermarks.
<a href="http://wordpress.org/extend/plugins/simple-backup/">**Try Simple Backup Plugin**</a>



= How can I Adjust the Location of the Watermarks? = 

We have a premium version of this plugin that adds the capability to adjust the locations of the watermarks.
The positions can be adjusted both vertically and horizontally.

<a href="http://mywebsiteadvisor.com/products-page/premium-wordpress-plugins/bulk-watermark-ultra/">**Upgrade to Bulk Watermark Ultra**</a> for advanced
watermark features including:

* Fully Adjustable Text and Image Watermark Positions
* Highest Quality Watermarks using Image Re-sampling rather than Re-sizing
* Lifetime Priority Support and Update License




= How do I generate the Highest Quality Watermarks? = 

We recommend that your watermark image be roughly the same width as the largest images you plan to watermark.
That way the watermark image will be scaled down, which will work better than making the watermark image larger in order to fit.

We also have a premium version of this plugin that adds the capability to resample the watermark image, rather than simply resize it.
This results in significantly better looking watermarks.

<a href="http://mywebsiteadvisor.com/products-page/premium-wordpress-plugins/bulk-watermark-ultra/">**Upgrade to Bulk Watermark Ultra**</a> for advanced
watermark features including:

* Fully Adjustable Text and Image Watermark Positions
* Highest Quality Watermarks using Image Re-sampling rather than Re-sizing
* Lifetime Priority Support and Update License



Check out the [Bulk Watermark Plugin for WordPress Video Tutorial](http://www.youtube.com/watch?v=XkFXBjfzw2I&hd=1):

http://www.youtube.com/watch?v=XkFXBjfzw2I&hd=1






Developer Website: http://MyWebsiteAdvisor.com/

Plugin Support: http://MyWebsiteAdvisor.com/support/

Plugin Page: http://MyWebsiteAdvisor.com/tools/wordpress-plugins/bulk-watermark/

Video Tutorial: http://mywebsiteadvisor.com/learning/video-tutorials/bulk-watermark-tutorial/




== Screenshots ==

1. Finished Example Image
2. Watermark Settings
3. Watermark Settings Preview
4. Select Images to Watermark with Image Preview Popup
5. Watermarks getting applied to the selected images





== Changelog ==

= 1.6.6 =
* updated links to plugin page

= 1.6.5 =
* added .jpeg to the list of allowed file types

= 1.6.4 =
* updated plugin FAQs
* updated readme file

= 1.6.3 =
* removed FilesystemIterator which was causing compatibility issues because it is only available in php 5.3+
* updated contextual help, removed deprecated filter and updated to preferred method
* added uninstall and deactivation functions to clear plugin settings


= 1.6.2 =
* updated the watermark application system to resolve issues with locating the correct directory.
* updated support links
* updated readme file
* updated plugin upgrades information


= 1.6.1 =
* updated readme file
* added plugin upgrades tab to settings page


= 1.6.0 =
* rebuilt plugin using settings API
* added tabbed navigation for plugin settings and tools
* updated readme file, due to the add_help_tab() function, the plugin requires at least WordPress version 3.3
* added notification about required version if an older version of WP is installed
* added tutorial video to plugin
* fixed issues with the plugin reading uploads directory path correctly
* updated screenshots



= 1.5.1 =
* added label elements around checkboxes to make the label text clickable.
* added function exists check for the sys_getloadavg function so it does not cause fatal errors on MS Windows Servers
* added ability to watermark png and gif images.


= 1.5 =
* updated readme file.
* fixed several issues causing warnings and notices in debug.log
* added plugin version to plugin diagnostic screen.
* added select all/deselect all button for selecting which files to add watermarks to.



= 1.4.7 =
* verified compatibility with WordPress v3.5

= 1.4.6 =
* fixed issue with watermarked images not displaying properly due to browser caching issues

= 1.4.5 =
* fixed several improper opening php tags

= 1.4.4 =
* added better screenshots to demonstrate how the plugin works

= 1.4.3 =
* added plugin meta row link to rate this plugin
* minor cleanup of old unused code

= 1.4.2 =
* added link to rate and review this plugin on WordPress.org.

= 1.4.1 =
* updated plugin activation php version check which was causing out of place errors.

= 1.4 =
* updated contextual help menu system, added faqs
* fixed broken links


= 1.3 =
* added more debug info, updated links, added basic plugin help menu


= 1.2 =
* added more debug info to help with troubleshooting


= 1.1 =
* re-arranged layout of admin page
* changed behavior of directory selector so it does not automatically list all files in uploads root which caused issues on sites with very large numbers of files


= 1.0 =
* Initial release









