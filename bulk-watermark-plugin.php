<?php



class Bulk_Watermark_Plugin{


	//plugin version number
	private $version = "1.6.2";
	
	private $debug = false;


	//holds settings page class
	private $settings_page;
	
	//holds watermark tools
	private $tools;

	
	//options are: edit, upload, link-manager, pages, comments, themes, plugins, users, tools, options-general
	private $page_icon = "options-general"; 	
	
	//settings page title, to be displayed in menu and page headline
	private $plugin_title = "Bulk Watermark";
	
	//page name
	private $plugin_name = "bulk-watermark";
	
	//will be used as option name to save all options
	private $setting_name = "bulk-watermark-settings";
	
	private $youtube_id = "XkFXBjfzw2I";

	
	//holds plugin options
	private $opt = array();


	public $plugin_path;
	public $plugin_dir;
	public $plugin_url;
	


	//initialize the plugin class
	public function __construct() {
	
		$this->plugin_path = DIRECTORY_SEPARATOR . str_replace(basename(__FILE__), null, plugin_basename(__FILE__));
		$this->plugin_dir = WP_PLUGIN_DIR . $this->plugin_path;
		$this->plugin_url = WP_PLUGIN_URL . $this->plugin_path;
		
		$this->opt = get_option($this->setting_name);
		
		$this->tools = new Bulk_Watermark_Tools;
		$this->tools->opt = $this->opt;
		
		if(isset($_GET['action']) && isset($_GET['page']) && "watermark_preview" == $_GET['action'] && "bulk-watermark-settings" == $_GET['page'] ){
			$this->tools->do_watermark_preview();
			die();
		}
				
	
		//check pluign settings and display alert to configure and save plugin settings
		add_action( 'admin_init', array(&$this, 'check_plugin_settings') );
		
		//initialize plugin settings
        add_action( 'admin_init', array(&$this, 'settings_page_init') );
		
		//create menu in wp admin menu
        add_action( 'admin_menu', array(&$this, 'admin_menu') );
		
		//add help menu to settings page
		add_filter( 'contextual_help', array(&$this,'admin_help'), 10, 3);	
		
		// add plugin "Settings" action on plugin list
		add_action('plugin_action_links_' . plugin_basename(BW_LOADER), array(&$this, 'add_plugin_actions'));
		
		// add links for plugin help, donations,...
		add_filter('plugin_row_meta', array(&$this, 'add_plugin_links'), 10, 2);
	
		//setup javascript files	
		//add_action('admin_enqueue_scripts', array($this, 'setup_watermark_scripts'));
	
		
	}


	//setup the plugin settings page
	public function settings_page_init() {

		$this->settings_page  = new Bulk_Watermark_Settings_Page( $this->setting_name );
		
		//$this->settings_page->extra_tabs = array(array('id'=>'backup', 'title'=>'Backup Manager', 'link' => admin_url()."tools.php?page=backup_manager"));
		
        //set the settings
        $this->settings_page->set_sections( $this->get_settings_sections() );
        $this->settings_page->set_fields( $this->get_settings_fields() );
		$this->settings_page->set_sidebar( $this->get_settings_sidebar() );

		$this->build_optional_tabs();
		
        //initialize settings
        $this->settings_page->init();
    }


	function setup_watermark_scripts() {
		wp_enqueue_script(
			'watermark_preview',
			plugins_url('/watermark.js', __FILE__)
		);
	}    





	public function check_plugin_settings(){
		if( isset($_GET['page']) ){
			if ($_GET['page'] == "bulk-watermark-settings"  ){
			
				$this->update_plugin_settings();
				
				if(false === get_option($this->setting_name)){
					$link = admin_url()."options-general.php?page=bulk-watermark-settings&tab=watermark_settings";
					$message = '<div class="error"><p>Welcome!<br>This plugin needs to be configured before you watermark your images.';
					$message .= '<br>Please Configure and Save the <a href="%1$s">Plugin Settings</a> before you continue!!</p></div>';
					echo sprintf($message, $link);
					
					
				}
			}
		}
	}




	private function update_plugin_settings(){
	
		if(false === get_option($this->setting_name)){
		
			$old_options = array(
				'watermark_on'       => array(),
				'watermark_type' =>	'text-image',
				'watermark_text' => array(
					'text' => '&copy; MyWebsiteAdvisor',
					'font' => 'Rage.ttf',
					'width' => 50,
					'transparency' => 70,
					'color' => 'ffffff'
				),
				'watermark_image'	=> array(
					'url' => null,
					'width' => 50
				)
			);
			
			
			$new_settings= array(
				'watermark_settings' => array(
					'watermark_type' 				=> $old_options['watermark_type'],
					'watermark_image_url' 			=> $old_options['watermark_image']['url'],
					'watermark_image_width' 		=> $old_options['watermark_image']['width'],
					'watermark_text' 				=> $old_options['watermark_text']['text'],
					'watermark_text_width' 			=> $old_options['watermark_text']['width'],
					'watermark_text_color' 			=> $old_options['watermark_text']['color'],
					'watermark_text_transparency' 	=> $old_options['watermark_text']['transparency'],
					'watermark_font' 				=> $old_options['watermark_text']['font']
				)
			);
			
			update_option($this->setting_name, $new_settings);
			
			delete_option('watermark_on');
			delete_option('watermark_type');
			delete_option('watermark_text');
			delete_option('watermark_image');
			
		
		}
	}



   /**
     * Returns all of the settings sections
     *
     * @return array settings sections
     */
    public function get_settings_sections() {
	
		$settings_sections = array(
			array(
				'id' => 'watermark_settings',
				'title' => __( 'Watermark Settings', $this->plugin_name )
			)
			
		);

								
        return $settings_sections;
    }
	
	
	
    /**
     * Returns all of the settings fields
     *
     * @return array settings fields
     */
    public function get_settings_fields() {
	
		$image_watermark_fields = array(
			array(
				'name' => 'watermark_image_url',
				'label' => __( 'Watermark Image URL', $this->plugin_name ),
				'desc' => 'Configure the Watermark Image URL',
				'type' => 'url',
				'value' => ""
			),
			array(
				'name' => 'watermark_image_width',
				'label' => __( 'Watermark Image Width', $this->plugin_name ),
				'desc' => 'Configure the Watermark Image Width (Percentage)',
				'type' => 'percentage',
				'default' => "50"
			)
		);
			
			
			
		$fonts = $this->get_font_list();
		
		
		
		$fonts_select = array(
			'name' => 'watermark_font',
			'label' => __( 'Watermark Font', $this->plugin_name ),
			'desc' => 'Select a Watermark Text Font',
			'type' => 'select',
			'options' => $fonts
		);
		
			
		$text_watermark_fields = array(
			
			array(
				'name' => 'watermark_text',
				'label' => __( 'Watermark Text', $this->plugin_name ),
				'desc' => 'Configure the Watermark Text',
				'type' => 'text',
				'default' => "&copy; MyWebsiteAdvisor.com"
			),
			array(
				'name' => 'watermark_text_width',
				'label' => __( 'Watermark Text Width', $this->plugin_name ),
				'desc' => 'Configure the Watermark Text Width (Percentage)',
				'type' => 'percentage',
				'default' => "50"
			),
			array(
				'name' => 'watermark_text_color',
				'label' => __( 'Watermark Text Color', $this->plugin_name ),
				'desc' => 'Configure the Watermark Text Color (FFFFFF is White)',
				'type' => 'text',
				'default' => "FFFFFF"
			),
			array(
				'name' => 'watermark_text_transparency',
				'label' => __( 'Watermark Text Transparency', $this->plugin_name ),
				'desc' => 'Configure the Watermark Text Transparency (Percentage)',
				'type' => 'percentage',
				'default' => "70"
			), 
			$fonts_select
			
		);
	
		$settings_fields = array(
			'watermark_settings' => array(
				array(
                    'name' => 'watermark_type',
                    'label' => __( 'Watermark Type', $this->plugin_name ),
                    'desc' => 'Select a Watermark Type',
                    'type' => 'radio',
                    'options' => array(
						'text-image' => 'Text and Image',
                        'text-only' => 'Text Only',
                        'image-only' => 'Image Only'
                    )
                )
			)
		);
			
			
			if(isset($this->opt['watermark_settings']['watermark_type'])){
				switch( $this->opt['watermark_settings']['watermark_type']){
					case "text-image":
						$settings_fields['watermark_settings'] = array_merge_recursive($settings_fields['watermark_settings'], $image_watermark_fields);
						$settings_fields['watermark_settings'] = array_merge_recursive($settings_fields['watermark_settings'], $text_watermark_fields);
						break;
					case "text-only":
						$settings_fields['watermark_settings'] = array_merge_recursive($settings_fields['watermark_settings'], $text_watermark_fields);
						break;
					case "image-only":
						$settings_fields['watermark_settings'] = array_merge_recursive($settings_fields['watermark_settings'], $image_watermark_fields);
						break;	
					
				}
			}
			

		
        return $settings_fields;
    }
	
	
	
	
	
	
	
	


	//plugin settings page template
	public function plugin_settings_page(){
	
		echo "<style> 
		.form-table{ clear:left; } 
		.nav-tab-wrapper{ margin-bottom:0px; }
		</style>";
		
		echo $this->display_social_media(); 
		
        echo '<div class="wrap" >';
		
			echo '<div id="icon-'.$this->page_icon.'" class="icon32"><br /></div>';
			
			echo "<h2>".$this->plugin_title." Plugin Settings</h2>";
			
			$this->settings_page->show_tab_nav();
			
			echo '<div id="poststuff" class="metabox-holder has-right-sidebar">';
			
				echo '<div class="inner-sidebar">';
					echo '<div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">';
					
						$this->settings_page->show_sidebar();
					
					echo '</div>';
				echo '</div>';
			
				echo '<div class="has-sidebar" >';			
					echo '<div id="post-body-content" class="has-sidebar-content">';
						
						$this->settings_page->show_settings_forms();
						
					echo '</div>';
				echo '</div>';
				
			echo '</div>';
			
        echo '</div>';
		
    }



   	public function admin_menu() {
		
        $this->page_menu = add_options_page( $this->plugin_title, $this->plugin_title, 'manage_options',  $this->setting_name, array($this, 'plugin_settings_page') );
    }




	public function admin_help($contextual_help, $screen_id, $screen){
	
		global $simple_backup_file_manager_page;
		
		if ( $screen_id == $this->page_menu || $screen_id == $simple_backup_file_manager_page ) {
				
			$support_the_dev = $this->display_support_us();
			$screen->add_help_tab(array(
				'id' => 'developer-support',
				'title' => "Support the Developer",
				'content' => "<h2>Support the Developer</h2><p>".$support_the_dev."</p>"
			));
			
			
			$video_code = "<style>
		.videoWrapper {
			position: relative;
			padding-bottom: 56.25%; /* 16:9 */
			padding-top: 25px;
			height: 0;
		}
		.videoWrapper iframe {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}
		</style>";
		
			$video_id = $this->youtube_id;
			$video_code .= '<div class="videoWrapper"><iframe width="640" height="360" src="http://www.youtube.com/embed/'.$video_id.'?rel=0&vq=hd720" frameborder="0" allowfullscreen></iframe></div>';

			$screen->add_help_tab(array(
				'id' => 'tutorial-video',
				'title' => "Tutorial Video",
				'content' => "<h2>{$this->plugin_title} Tutorial Video</h2><p>$video_code</p>"
			));
			
			$screen->add_help_tab(array(
				'id' => 'plugin-support',
				'title' => "Plugin Support",
				'content' => "<h2>{$this->plugin_title} Support</h2><p>For {$this->plugin_title} Plugin Support please visit <a href='http://mywebsiteadvisor.com/support/' target='_blank'>MyWebsiteAdvisor.com</a></p>"
			));
	
	
			$screen->add_help_tab(array(
				'id' => 'upgrade_plugin',
				'title' => __( 'Plugin Upgrades', $this->plugin_name ),
				'content' => $this->get_plugin_upgrades()		
			));		
			

			$screen->set_help_sidebar("<p>Please Visit us online for more Free WordPress Plugins!</p><p><a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/' target='_blank'>MyWebsiteAdvisor.com</a></p><br>");
			
		}
			
		

	}
	
	
	
	private function do_diagnostic_sidebar(){
	
		ob_start();
		
			echo "<p>Plugin Version: $this->version</p>";
				
			echo "<p>Server OS: ".PHP_OS." (" . strlen(decbin(~0)) . " bit)</p>";
			
			echo "<p>Required PHP Version: 5.0+<br>";
			echo "Current PHP Version: " . phpversion() . "</p>";
			
			

			$gdinfo = gd_info();
		
			if($gdinfo){
				echo '<p>GD Support Enabled!<br>';
				if($gdinfo['FreeType Support']){
					 echo 'FreeType Support Enabled!</p>';
				}else{
					echo "Please Configure FreeType!</p>";
				}
			}else{
				echo "<p>Please Configure GD!</p>";
			}
			
			
			
			if( ini_get('safe_mode') ){
				echo "<p><font color='red'>PHP Safe Mode is enabled!<br><b>Disable Safe Mode in php.ini!</b></font></p>";
			}else{
				echo "<p>PHP Safe Mode: is disabled!</p>";
			}
			
			if( ini_get('allow_url_fopen')){
				echo "<p>PHP allow_url_fopen: is enabled!</p>";
			}else{
				echo "<p><font color='red'>PHP allow_url_fopen: is disabled!<br><b>Enable allow_url_fopen in php.ini!</b></font></p>";
			}
			

			if( ini_get('disable_functions') !== '' ){
				echo "<p><font color='red'>Disabled PHP Functions Detected!<br><b>Please enable these functions in php.ini!</b></font></p>";
			}else{
				echo "<p>Disabled PHP Functions: None Found!</p>";
			}

			
			echo "<p>Memory Use: " . number_format(memory_get_usage()/1024/1024, 1) . " / " . ini_get('memory_limit') . "</p>";
			
			echo "<p>Peak Memory Use: " . number_format(memory_get_peak_usage()/1024/1024, 1) . " / " . ini_get('memory_limit') . "</p>";
			
			if(function_exists('sys_getloadavg')){
				$lav = sys_getloadavg();
				echo "<p>Server Load Average: ".$lav[0].", ".$lav[1].", ".$lav[2]."</p>";
			}	
		
			
	
		return ob_get_clean();
				
	}
	
	
	
	
	
	
	private function get_settings_sidebar(){
	
		$plugin_resources = "<p><a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/bulk-watermark/' target='_blank'>Plugin Homepage</a></p>
			<p><a href='http://mywebsiteadvisor.com/learning/video-tutorials/bulk-watermark-tutorial/'  target='_blank'>Plugin Tutorial</a></p>
			<p><a href='http://mywebsiteadvisor.com/support/'  target='_blank'>Plugin Support</a></p>
			<p><a href='http://mywebsiteadvisor.com/contact-us/'  target='_blank'>Contact Us</a></p>
			<p><a href='http://wordpress.org/support/view/plugin-reviews/bulk-watermark?rate=5#postform'  target='_blank'>Rate and Review This Plugin</a></p>";
	
		$more_plugins = "<p><a href='http://mywebsiteadvisor.com/tools/premium-wordpress-plugins/'  target='_blank'>Premium WordPress Plugins!</a></p>
			<p><a href='http://profiles.wordpress.org/MyWebsiteAdvisor/'  target='_blank'>Free Plugins on Wordpress.org!</a></p>
			<p><a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/'  target='_blank'>Free Plugins on MyWebsiteAdvisor.com!</a></p>";
	
		$follow_us = "<p><a href='http://facebook.com/MyWebsiteAdvisor/'  target='_blank'>Follow us on Facebook!</a></p>
			<p><a href='http://twitter.com/MWebsiteAdvisor/'  target='_blank'>Follow us on Twitter!</a></p>
			<p><a href='http://www.youtube.com/mywebsiteadvisor'  target='_blank'>Watch us on YouTube!</a></p>
			<p><a href='http://MyWebsiteAdvisor.com/'  target='_blank'>Visit our Website!</a></p>";
	
		$upgrade = "	<p>
			<a href='http://mywebsiteadvisor.com/products-page/premium-wordpress-plugin/bulk-watermark-ultra/'  target='_blank'>Upgrade to Bulk Watermark Ultra!</a><br />
			<br />
			<b>Features:</b><br />
			-Higher Quality Watermarks!<br />
			-Fully Adjustable Watermark Locations!<br />
			-Compatible with third party gallery plugins and themes that store images in the WordPress /uploads directory!.<br />
			-And Much More!<br />
			 </p>
			<p>Click Here for <a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/watermark-plugins-for-wordpress/' target='_blank'>More Watermark Plugins</a></p>
			<p>-<a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/bulk-watermark/' target='_blank'>Bulk Watermark</a></p>
			<p>-<a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/signature-watermark/' target='_blank'>Signature Watermark</a></p>
			<p>-<a href='http://mywebsiteadvisor.com/tools/wordpress-plugins/transparent-image-watermark/' target='_blank'>Transparent Image Watermark</a></p>
			</p>";
	
		$sidebar_info = array(
			array(
				'id' => 'diagnostic',
				'title' => 'Plugin Diagnostic Check',
				'content' => $this->do_diagnostic_sidebar()		
			),
			array(
				'id' => 'resources',
				'title' => 'Plugin Resources',
				'content' => $plugin_resources	
			),
			array(
				'id' => 'upgrade',
				'title' => 'Plugin Upgrades',
				'content' => $upgrade	
			),
			array(
				'id' => 'more_plugins',
				'title' => 'More Plugins',
				'content' => $more_plugins	
			),
			array(
				'id' => 'follow_us',
				'title' => 'Follow MyWebsiteAdvisor',
				'content' => $follow_us	
			)
		);
		
		return $sidebar_info;

	}






		//build optional tabs, using debug tools class worker methods as callbacks
	private function build_optional_tabs(){
		if(true === $this->debug){
			//general debug settings
			$plugin_debug = array(
				'id' => 'plugin_debug',
				'title' => __( 'Plugin Settings Debug', $this->plugin_name ),
				'callback' => array(&$this, 'show_plugin_settings')
			);
	
			//$enabled = isset($this->opt['debug_settings']['enable_display_plugin_settings']) ? $this->opt['debug_settings']['enable_display_plugin_settings'] : 'false';
			//if( $enabled === 'true' ){ 	
			$this->settings_page->add_section( $plugin_debug );
			//}
		}
		
		$plugin_tutorial = array(
			'id' => 'plugin_tutorial',
			'title' => __( 'Plugin Tutorial Video', $this->plugin_name ),
			'callback' => array(&$this, 'show_plugin_tutorual')
		);
		$this->settings_page->add_section( $plugin_tutorial );
		
		
		$watermark_preview = array(
			'id' => 'watermark_preview',
			'title' => __( 'Watermark Preview', $this->plugin_name ),
			'callback' => array(&$this, 'show_watermark_preview')
		);
		$this->settings_page->add_section( $watermark_preview );
		
		
		$apply_watermark = array(
			'id' => 'apply_watermark',
			'title' => __( 'Apply Bulk Watermark', $this->plugin_name ),
			'callback' => array(&$this, 'bulk_watermark_manager')
		);
		$this->settings_page->add_section( $apply_watermark );
		
		$upgrade_plugin = array(
			'id' => 'upgrade_plugin',
			'title' => __( 'Plugin Upgrades', $this->plugin_name ),
			'callback' => array(&$this, 'show_plugin_upgrades')
		);
		$this->settings_page->add_section( $upgrade_plugin );
	}
	


	public function show_plugin_tutorual(){
	
		echo "<style>
		.videoWrapper {
			position: relative;
			padding-bottom: 56.25%; /* 16:9 */
			padding-top: 25px;
			height: 0;
		}
		.videoWrapper iframe {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}
		</style>";

		$video_id = $this->youtube_id;
		echo sprintf( '<div class="videoWrapper"><iframe width="640" height="360" src="http://www.youtube.com/embed/%1$s?rel=0&vq=hd720" frameborder="0" allowfullscreen ></iframe></div>', $video_id);
		
	
	}
	
	
	public function get_plugin_upgrades(){
	
		ob_start();
		$this->show_plugin_upgrades();
		return ob_get_clean();	
	}


	public function show_plugin_upgrades(){
		
		//bulk watermark ultra
		$html = "</form><h2>Upgrade to Bulk Watermark Ultra Today!</h2>";
		
		$html .= "<style>
			ul.upgrade_features li { list-style-type: disc; }
			ul.upgrade_features  { margin-left:30px;}
		</style>";
		
		$html .= "<script>
				
			function  bulk_watermark_upgrade(){
        		window.open('http://mywebsiteadvisor.com/products-page/premium-wordpress-plugin/bulk-watermark-ultra/');
        		return false;
			}
			
			
			function  try_sig_watermark(){
        		window.open('http://wordpress.org/extend/plugins/signature-watermark/');
        		return false;
			}
					
						
			function  sig_watermark_learn_more(){
        		window.open('http://mywebsiteadvisor.com/tools/wordpress-plugins/signature-watermark/');
        		return false;
			}
		
			function  bulk_watermark_learn_more(){
        		window.open('http://mywebsiteadvisor.com/tools/wordpress-plugins/bulk-watermark/');
        		return false;
			}
			
			
			function compare_watermark_plugins(){
        		window.open('http://mywebsiteadvisor.com/tools/wordpress-plugins/watermark-plugins-for-wordpress/');
        		return false				
			}
			
			
		</script>";
		
		
		$html .= "<b>Premium Features include:</b>";
		
		$html .= "<ul class='upgrade_features'>";
		$html .= "<li>Fully Adjustable Text and Image Watermark Positions</li>";
		$html .= "<li>Highest Quality Watermarks using Image Re-sampling rather than Re-sizing</li>";
		$html .= "<li>Priority Support License</li>";
		$html .= "</ul>";
		
		$html .=  '<div style="padding-left: 1.5em; margin-left:5px;">';
		$html .= "<p class='submit'>";
		$html .= "<input type='submit' class='button-primary' value='Upgrade to Bulk Watermark Ultra &raquo;' onclick='return bulk_watermark_upgrade()'> &nbsp;";
		$html .= "<input type='submit' class='button-secondary' value='Learn More &raquo;' onclick='return bulk_watermark_learn_more()'>";
		$html .= "</p>";
		$html .=  "</div>";

		$html .=  "<hr/>";

		//signature watermark 
		$html .= "<h2>Also Try Signature Watermark!</h2>";
		$html .= "<b>Signature Watermark can produce the exact same watermarks as Bulk Watermark, however it works differently.</b><br>Signature Watermark Plugin adds watermarks to each new image as they are uploaded.<br>Bulk Watermark Plugin adds watermarks to images which have already been uploaded to your Media Library.</b>";
		
		$html .=  '<div style="padding-left: 1.5em; margin-left:5px;">';
		$html .= "<p class='submit'>";
		$html .= "<input type='submit' class='button-primary' value='Try Signature Watermark &raquo;' onclick='return try_sig_watermark()'> &nbsp;";
		$html .= "<input type='submit' class='button-secondary' value='Learn More &raquo;' onclick='return sig_watermark_learn_more()'>";
		$html .= "</p>";
		$html .=  "</div>";
		
		$html .=  "<hr/>";

		$html .=  '<div style="padding-left: 1.5em; margin-left:5px;">';
		$html .= "<p class='submit'><input type='submit' class='button-primary' value='Click Here to Compare All of Our Watermark Plugins &raquo;' onclick='return compare_watermark_plugins()'></p>";
		$html .=  "</div>";
		
		echo $html;
	}




	public function bulk_watermark_manager(){
	
			echo "<style>	  
			  p#watermark_preview_popup{
	position:fixed;
	border:1px solid #ccc;
	background:#333;
	padding:5px;
	display:none;									 
	color:#fff;
}

#watermark_preview_popup img{
	 max-width:300px;  
	 max-height:300px;                                         
} 
	</style>";
	
	 echo  "<script type='text/javascript' src='"."../".PLUGINDIR . "/". dirname(plugin_basename (__FILE__))."/watermark.js'></script>";                        
                      
		echo "<script type='text/javascript'>
				jQuery(document).ready(function(){
					imagePreview();
				});															
			  </script>"; 
			  
			  

	
	
		if(!isset($_POST['base_dir'])){
			$upload_dir   = wp_upload_dir();
			$base_dir = $upload_dir['basedir'];
		}else{
			//$base_dir = $_SERVER['DOCUMENT_ROOT'] . $_POST['base_dir'];
			$base_dir = $_POST['base_dir'];
		}
		
		$dir_info = $this->tools->list_directories($base_dir);
		

		echo "</form><form method='post'><p class='submit'><select name='base_dir'>";
			echo "<option value=''>Select a Directory...</option>";
			foreach($dir_info as $dir){
				$selected = "";
				if(isset($_POST['base_dir']) && $_POST['base_dir'] == $dir){
					$selected = "selected='selected'";
				}
				echo "<option $selected>$dir</option>";
				
			}
		echo "</select> ";
		echo "<input type='submit' class='button-primary' ></p>";
		echo "</form>";

	
		
		if(isset($_POST['base_dir']) and $_POST['base_dir'] != ''){
			$file_info = $this->tools->list_files($base_dir);
		
			//echo "<b>" . count($file_info) . "</b> files found in: <b>" . str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_dir) . "</b><br>";
			echo "<b>" . count($file_info) . "</b> files found in: <b>" .  $base_dir . "</b><br>";
			echo "<br>";
			
			echo "<form method='post'>";
			echo "<input type='hidden' name='bulk_watermark_action'>";
			echo "<input type='hidden' name='base_dir' value='".$_POST['base_dir']."'>";
			echo "<div style='overflow-y:scroll; height:250px; border:1px solid grey; padding:5px;'>";
			foreach($file_info as $file){
				echo $file;
			}
			echo "</div>";
			echo "<br>";
			echo "<p><strong><font color='red'>NOTICE:<br>Watermarking Images with this plugin is permanent, watermarks can not be removed.<br>You should make a backup of your images before you apply the watermarks!</font></strong></p>";
			//echo "<br>";
			echo "<p class='submit'>";
			echo "<input type='button' class='checkall button' value='Select All' onclick='select_all_image_sizes()'>  ";
			echo "<input type='submit' class='button-primary' value='Apply Bulk Watermark'>";
			echo "</p>";
			echo "</form>";
			
			echo "<script>
			
				function select_all_image_sizes(){
							if('Select All' == jQuery('.checkall').val()){
								jQuery('.bulk_watermark_file_select').attr('checked', 'checked');
								jQuery('.checkall').val('Unselect All');
							}else{
								jQuery('.bulk_watermark_file_select').removeAttr('checked');
								jQuery('.checkall').val('Select All');  
							}
						}
				</script>";
			
		}
					
					
		if(array_key_exists('bulk_watermark_action', $_POST)) {
			$this->tools->apply_bulk_watermark($_POST['bulk_file_list']);
		}
	
	}
	
	

	public function show_watermark_preview(){
		$img_url = admin_url()."options-general.php?page=".$this->setting_name."&action=watermark_preview";
		echo "<img src=$img_url width='100%'>";
		echo "<p><strong>You can customize the preview image by replacing the image named ";
		echo " <a href='".$this->plugin_url."example.jpg' target='_blank'>'example.jpg'</a> in the plugin directory.</strong></p>";
	}



 

	// displays the plugin options array
	public function show_plugin_settings(){
				
		echo "<pre>";
			print_r($this->opt);
		echo "</pre>";
			
	}







	/**
	 * Add "Settings" action on installed plugin list
	 */
	public function add_plugin_actions($links) {
		array_unshift($links, '<a href="options-general.php?page=' . $this->setting_name . '">' . __('Settings') . '</a>');
		
		return $links;
	}
	
	
	
	/**
	 * Add links on installed plugin list
	 */
	public function add_plugin_links($links, $file) {
		if($file == plugin_basename(BW_LOADER)) {
			$upgrade_url = 'http://mywebsiteadvisor.com/products-page/premium-wordpress-plugin/bulk-watermark-ultra/';
			$links[] = '<a href="'.$upgrade_url.'" target="_blank" title="Click Here to Upgrade this Plugin!">Upgrade Plugin</a>';
			
			$tutorial_url = 'http://mywebsiteadvisor.com/learning/video-tutorials/bulk-watermark-tutorial/';
			$links[] = '<a href="'.$tutorial_url.'" target="_blank" title="Click Here to View the Plugin Video Tutorial!">Tutorial Video</a>';
			
			$rate_url = 'http://wordpress.org/support/view/plugin-reviews/' . basename(dirname(__FILE__)) . '?rate=5#postform';
			$links[] = '<a href="'.$rate_url.'" target="_blank" title="Click Here to Rate and Review this Plugin on WordPress.org">Rate This Plugin</a>';
		}
		
		return $links;
	}
	
	
	
	public function display_support_us(){
				
		$string = '<p><b>Thank You for using the '.$this->plugin_title.' Plugin for WordPress!</b></p>';
		$string .= "<p>Please take a moment to <b>Support the Developer</b> by doing some of the following items:</p>";
		
		$rate_url = 'http://wordpress.org/support/view/plugin-reviews/' . basename(dirname(__FILE__)) . '?rate=5#postform';
		$string .= "<li><a href='$rate_url' target='_blank' title='Click Here to Rate and Review this Plugin on WordPress.org'>Click Here</a> to Rate and Review this Plugin on WordPress.org!</li>";
		
		$string .= "<li><a href='http://facebook.com/MyWebsiteAdvisor' target='_blank' title='Click Here to Follow us on Facebook'>Click Here</a> to Follow MyWebsiteAdvisor on Facebook!</li>";
		$string .= "<li><a href='http://twitter.com/MWebsiteAdvisor' target='_blank' title='Click Here to Follow us on Twitter'>Click Here</a> to Follow MyWebsiteAdvisor on Twitter!</li>";
		$string .= "<li><a href='http://mywebsiteadvisor.com/tools/premium-wordpress-plugins/' target='_blank' title='Click Here to Purchase one of our Premium WordPress Plugins'>Click Here</a> to Purchase Premium WordPress Plugins!</li>";
	
		return $string;
	}
	
	
	
	
	
	public function display_social_media(){
	
		$social = '<style>
	
		.fb_edge_widget_with_comment {
			position: absolute;
			top: 0px;
			right: 200px;
		}
		
		</style>
		
		<div  style="height:20px; vertical-align:top; width:45%; float:right; text-align:right; margin-top:5px; padding-right:16px; position:relative;">
		
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=253053091425708";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, "script", "facebook-jssdk"));</script>
			
			<div class="fb-like" data-href="http://www.facebook.com/MyWebsiteAdvisor" data-send="true" data-layout="button_count" data-width="450" data-show-faces="false"></div>
			
			
			<a href="https://twitter.com/MWebsiteAdvisor" class="twitter-follow-button" data-show-count="false"  >Follow @MWebsiteAdvisor</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		
		
		</div>';
		
		return $social;

	}	




	/**
	 * List all fonts from the fonts dir
	 *
	 * @return array
	 */
	private function get_font_list() {
		$plugin_dir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . str_replace(basename(__FILE__), null, plugin_basename(__FILE__));
		$fonts_dir =  $plugin_dir . DIRECTORY_SEPARATOR . "fonts";

		$fonts = array();
		try {
			$dir = new DirectoryIterator($fonts_dir);

			foreach($dir as $file) {
				if($file->isFile()) {
					$font = pathinfo($file->getFilename());

					if(strtolower($font['extension']) == 'ttf') {
						if(!$file->isReadable()) {
							$this->_messages['unreadable-font'] = sprintf('Some fonts are not readable, please try chmoding the contents of the folder <strong>%s</string> to writable and refresh this page.', $this->_plugin_dir . $this->_fonts_dir);
						}

						$fonts[$font['basename']] = str_replace('_', ' ', $font['filename']);
					}
				}
			}

			ksort($fonts);
		} catch(Exception $e) {}

		return $fonts;
	}




}

?>
