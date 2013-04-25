<?php



class Bulk_Watermark_Tools{

	public $opt;
	
	public $plugin_dir;
	
	
	public function __construct(){
		$this->plugin_dir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . str_replace(basename(__FILE__), null, plugin_basename(__FILE__));
	}







	public function apply_bulk_watermark($file_list){
	
		
	
		?>
		
	
		<h2>Applying Watermarks...</h2>
		
		<p>Your images are now having the watermarks applied, you can see them below.<br />
		If you find this plugin useful please support the author by recommending this to your friends!</p>	
		
		<br />
	

	
		<?php
		
	
		set_time_limit(0); 
		
		$start_time = $this->microtime_float();
	
		foreach($file_list as $file_path){
			
			$this->do_watermark($file_path);
			
			$file_url =  get_option('siteurl') ."/". preg_replace("#".ABSPATH."#", '', $file_path, 1);
				
			echo "$file_path Done!<br>";
			echo "<img src='$file_url"."?".filemtime($file_path)."' style='max-width:100%;'><br>";
	
			ob_flush();
			flush();
				
		}
		
		echo "Bulk Watermark Complete!<br>";
		
		$finish_time = $this->microtime_float();
		$exec_time = $finish_time - $start_time;
		$exec_time = number_format($exec_time, 2);
		echo "<p>Executition Time: $exec_time seconds.<br>";
		
		echo "<p>Memory Use: " . number_format(memory_get_usage()/1024/1024, 1) . " / " . ini_get('memory_limit') . "</p>";
		echo "<p>Peak Memory Use: " . number_format(memory_get_peak_usage()/1024/1024, 1) . " / " . ini_get('memory_limit') . "</p>";


		echo "<A HREF='javascript:history.back()'>Back</A>";
		die();
		
	}
	
	
	/**
	 * Apply watermark to certain image
	 *
	 * @param string $filepath
	 * @return boolean
	 */
	public function do_watermark($filepath) {
		// get image mime type
		$mime_type = wp_check_filetype($filepath);
		$mime_type = $mime_type['type'];
		
		// get watermark settings
		$options = $this->opt;

		// get image resource
		$image = $this->get_image_resource($filepath, $mime_type);

		// add watermark image to image
		if($options['watermark_settings']['watermark_type'] == "text-image"){
			
			$this->apply_watermark_image($image, $options);
			$this->apply_watermark_text($image, $options);
			
		}elseif($options['watermark_settings']['watermark_type'] == "text-only"){
		
			$this->apply_watermark_text($image, $options);
			
		}elseif($options['watermark_settings']['watermark_type'] == "image-only"){
		
			$this->apply_watermark_image($image, $options);
			
		}
		
		// save watermarked image
		return $this->save_image_file($image, $mime_type, $filepath);
	}
	



	public function do_watermark_preview(){

		$options = $this->opt;
	
		$filepath = $this->plugin_dir . "/example.jpg";
	
		$mime_type = wp_check_filetype($filepath);
		$mime_type = $mime_type['type'];

		// get image resource
		$image = $this->get_image_resource($filepath, $mime_type);
		
		// add watermark image to image
		if($options['watermark_settings']['watermark_type'] == "text-image"){
			
			$this->apply_watermark_image($image, $options);
			$this->apply_watermark_text($image, $options);
			
		}elseif($options['watermark_settings']['watermark_type'] == "text-only"){
		
			$this->apply_watermark_text($image, $options);
			
		}elseif($options['watermark_settings']['watermark_type'] == "image-only"){
		
			$this->apply_watermark_image($image, $options);
			
		}
		
		// Set the content-type
		header('Content-type: image/jpg');

		// Output the image using imagejpg()
		imagejpeg($image, null, 100);
		imagedestroy($image);
	}





	private function apply_watermark_text($image, array $opt) {
		
		$text  =  $opt['text_watermark_settings']['watermark_text'];
		$text_size = $opt['text_watermark_settings']['watermark_text_width'] / 100;
		$text_color  =  $opt['text_watermark_settings']['watermark_text_color'];
		$text_transparency  =  $opt['text_watermark_settings']['watermark_text_transparency'];
		
		$v_pos = .5;
		$h_pos = .5;
		
		//get size of image watermark will be applied to.
		$img_width = imagesx($image);
		$img_height = imagesy($image);
		
		//fix font path
		$opt    = $this->get_full_font_path($opt);
		
		//calculate font size as well as the size of the text
		$font_size = $this->calculate_font_size($opt, $img_width);
		$text_size = $this->calculate_text_box_size($opt, $font_size);

		//calculate where to position the text
		$txt_dest_x = ($img_width * $h_pos) - ($text_size['width']/2);
		$txt_dest_y = ($img_height * $v_pos ) + ($text_size['height']/2);
		
		// allocate text color
		$text_transparency =  (int) (($text_transparency/100) * 127);
		$text_color  = $this->image_transparent_color_allocate_hex($image, $text_color, $text_transparency);
		//$text_color = ImageColorAllocateAlpha($image, 255, 255, 255, 96);
		
		// Add the text to image
		imagettftext($image, $font_size, 0, $txt_dest_x, $txt_dest_y, $text_color, $opt['text_watermark_settings']['watermark_font'], html_entity_decode($text));

		return $image;
	}

	
	/**
	 * Add watermark image to image
	 *
	 * @param resource $image
	 * @param array $opt
	 * @return resource
	 */
	private function apply_watermark_image($image, array $opt) {
		// get size and url of watermark
		$image_size  =  $opt['image_watermark_settings']['watermark_image_width'] / 100;
		$url  =  $opt['image_watermark_settings']['watermark_image_url'];
		//$quality = $opt['watermark_image']['quality'];
		$v_pos = .9;
		$h_pos = .9;
		
		$watermark = imagecreatefrompng("$url"); 
		$watermark_width = imagesx($watermark);
		$watermark_height = imagesy($watermark);
				
		$img_width = imagesx($image);
		$img_height = imagesy($image);

		$image_ratio = (($img_width * $image_size) / $watermark_width);
			
		$w =($watermark_width * $image_ratio);
		$h = ($watermark_height * $image_ratio);

		
		$img_dest_y = ($img_height * $v_pos) - ($h);
		$img_dest_x = ($img_width * $h_pos) - ($w);
	
		
		imagecopyresized($image, $watermark, $img_dest_x, $img_dest_y, 0, 0, $w, $h, $watermark_width, $watermark_height);
		
			
		return $image;
	}
	



	/**
	 * Get fullpath of font
	 *
	 * @param array $opt
	 * @return unknown
	 */
	private function get_full_font_path(array $opt) {
		$opt['text_watermark_settings']['watermark_font'] = $this->plugin_dir . "/fonts/" . $opt['text_watermark_settings']['watermark_font'];

		return $opt;
	}



	/**
	 * Allocate a color for an image from HEX code
	 *
	 * @param resource $image
	 * @param string $hexstr
	 * @return int
	 */
	private function image_transparent_color_allocate_hex($image, $hexstr, $transparency) {
		return imagecolorallocatealpha($image,
			hexdec(substr($hexstr,0,2)),
			hexdec(substr($hexstr,2,2)),
			hexdec(substr($hexstr,4,2)),
			$transparency
		);
	}
	


	/**
	 * Calculate text bounting box size
	 *
	 * @param array $opt
	 * @param int $font_size
	 * @return array $size
	 */
	private function calculate_text_box_size(array $opt, $font_size){
	
		$bbox = imagettfbbox(
			$font_size,
			0,
			$opt['text_watermark_settings']['watermark_font'],
			html_entity_decode($opt['text_watermark_settings']['watermark_text'])
		);

		//calculate height and width of text
		$size['width'] = $bbox[4] - $bbox[0];
		$size['height'] = $bbox[1] - $bbox[7];

		return $size;
	
	}
	
	
	
	

	/**
	 * Calculate font size
	 *
	 * @param array $opt
	 * @param int $width
	 * @return int $font_size
	 */
	private function calculate_font_size(array $opt, $width) {

		$font_size = 72;
		$size = $this->calculate_text_box_size($opt, $font_size);

		//calculate font size needed to fill the desired wwatermark text width, based on size of original image
		$font_size_ratio = (($opt['text_watermark_settings']['watermark_text_width'] / 100) * $width)  / $size['width'];
		
		$font_size = $font_size * $font_size_ratio;
			

		
		return $font_size;
	}




	
	/**
	 * Get array with image size
	 *
	 * @param resource $image
	 * @return array
	 */
	private function get_image_size($image) {
		return array(
			'x' => imagesx($image),
			'y' => imagesy($image)
		);
	}
	

	
	/**
	 * Get image resource accordingly to mimetype
	 *
	 * @param string $filepath
	 * @param string $mime_type
	 * @return resource
	 */
	private function get_image_resource($filepath, $mime_type) {
		switch ( $mime_type ) {
			case 'image/jpeg':
				return imagecreatefromjpeg($filepath);
			case 'image/png':
				return imagecreatefrompng($filepath);
			case 'image/gif':
				return imagecreatefromgif($filepath);
			default:
				return false;
		}
	}
	
	/**
	 * Save image from image resource
	 *
	 * @param resource $image
	 * @param string $mime_type
	 * @param string $filepath
	 * @return boolean
	 */
	private function save_image_file($image, $mime_type, $filepath) {
		switch ( $mime_type ) {
			case 'image/jpeg':
				return imagejpeg($image, $filepath, 90);
			case 'image/png':
				return imagepng($image, $filepath);
			case 'image/gif':
				return imagegif($image, $filepath);
			default:
				return false;
		}
	}




	public function microtime_float(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	
	
	
	
	
	
	public function list_directories($dir){
		
		$dir_list_output = array();
		
		$upload_dir   = wp_upload_dir();
		$base_dir = $upload_dir['basedir'];
		$base_url = $upload_dir['baseurl'];
			
		//$pattern = "#".$_SERVER['DOCUMENT_ROOT']."#";
		//$dir_list_output[] = preg_replace($pattern, '', $base_dir, 1);
		$dir_list_output[] = $base_dir;
				
		//$flags = FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS;		
		$iterator = new RecursiveDirectoryIterator($base_dir);
		
		foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as  $file) {
			$file_info = pathinfo($file->getFilename());
			if ( !$file->isFile() && is_numeric($file->getFilename()) ) { //create list of directories
			
				//$dirPath = preg_replace($pattern, '', $file->getPathname(), 1);
				$dirPath = $file->getPathname();
				
				$dir_list_output[] =  $dirPath;
				
			}
		}			
		
		sort($dir_list_output);
			
		return $dir_list_output;	
	}

	
	public function list_files($dir){
		$file_list_output = array();
	
		
		$upload_dir   = wp_upload_dir();
		$base_dir = $upload_dir['basedir'];
		$base_url = $upload_dir['baseurl'];
			
		$allowed_types= array('jpg', 'jpeg', 'gif', 'png');
		
		//$flags = FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS;
		$iterator = new RecursiveDirectoryIterator($dir);
		
		foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as  $file) {
			$file_info = pathinfo($file->getFilename());
			
			if(isset($file_info['extension'])){
				$file_ext = strtolower($file_info['extension']);
				
				if($file->isFile() && in_array($file_ext, $allowed_types)){ //create list of files
				
					
					$imgPath = $file->getPath()."/".$file->getFilename();
					//$imgUrl = $base_url . "/" . $file->getFilename();
					$imgUrl =  get_option('siteurl') ."/". preg_replace("#".ABSPATH."#", '', $imgPath, 1);
					
					$file_list_output[] = "<p><input name='bulk_file_list[]' class='bulk_watermark_file_select' value='$imgPath' type='checkbox' > <a class='watermark_preview' href='$imgUrl"."?".filemtime($imgPath)."' target='_blank' title='".$file->getFilename()."'>" . $file->getFilename() . "</a></p>";
					
				}
			}
		}
				
		sort($file_list_output);
	
		return $file_list_output;
	}
	
	
	
	function get_relative_path($from, $to){
		$from     = explode('/', $from);
		$to       = explode('/', $to);
		$relPath  = $to;
	
		foreach($from as $depth => $dir) {
			// find first non-matching dir
			if($dir === $to[$depth]) {
				// ignore this directory
				array_shift($relPath);
			} else {
				// get number of remaining dirs to $from
				$remaining = count($from) - $depth;
				if($remaining > 1) {
					// add traversals up to first matching dir
					$padLength = (count($relPath) + $remaining - 1) * -1;
					$relPath = array_pad($relPath, $padLength, '..');
					break;
				} else {
					$relPath[0] = './' . $relPath[0];
				}
			}
		}
		return implode('/', $relPath);
	}



}

?>