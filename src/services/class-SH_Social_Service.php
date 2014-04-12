<?php
/**
 * SH_Social_Service Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Social_Service {

	// construct the class
	public function __construct($type, $settings, $key) {
		$this->service = "Default"; // must be set correctly in the subclass constructors
		$this->settings = $settings;
		$this->key = $key;

		$imageSet = $settings[$type.'_image_set'];
		$this->imagePath = $this->getImageUrlPath($imageSet);

		$this->imageExtension = ".png";
		$this->imageSize = $settings[$type.'_image_size'];
		$this->newWindow = $settings['new_window'];
	}
	
	// generates the css class for the button link
	protected function cssClass() {
		return "crafty-social-button csb-" . trim(strtolower($this->service));		
	}

	protected function getImageUrlPath($imageSet) {
		$plugin_url = plugins_url() . "/crafty-social-buttons/buttons/$imageSet/";
		$plugin_file_path = plugin_dir_path(__FILE__) . "../buttons/$imageSet";

		if (is_dir($plugin_file_path)) {
			return $plugin_url;
		} else {
            $upload_dir = wp_upload_dir();
			$custom_url = $upload_dir['baseurl'] . "/crafty-social-buttons/buttons/$imageSet/";
			return $custom_url;
		}
	}
	
	public function shareButton($url, $title = '', $showCount = false) {
		return "";
	}
	
	public function linkButton($username) {
		return "";	
	}
	
	public function shareCount($url) {
		return "0";	
	}
	
	public static function canShare() {
		return true;	
	}

	public static function canLink() {
		return true;	
	}

	public static function description() {
		return "";	
	}

	protected function buttonImage() {
		$imageUrl = $this->imagePath . trim(strtolower($this->service)) . $this->imageExtension;
		return '<img title="'.$this->service.'" '
		.'alt="'.$this->service.'" '
		.'width="'.$this->imageSize.'" '
		.'height="'.$this->imageSize.'" '
		.'src="' . $imageUrl .'" />';
	}

	protected function shareCountHtml($display) {
		if ($display) {
			$slug = trim(strtolower($this->service));
			$key = $this->key;
			return '<span id="crafty-social-share-count-'.$slug.'-'.$key.'" class="crafty-social-share-count">0</span>';
		} else {
			return '';
		}

	}
	
}
 
?>