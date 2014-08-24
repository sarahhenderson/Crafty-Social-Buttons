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
		$this->newWindow = ($type == "link" ? $settings['new_window'] : $settings['open_in'] == 'new_window');
        $this->popup = ($type == 'share' && $settings['popup']);
	}
	
	// generates the css class for the button link
	protected function cssClass() {
		$css = "crafty-social-button csb-" . trim(strtolower($this->service));
        if ($this->popup) {
            $css .= " popup";
        }
        return $css;
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

	public function shareButtonUrl($url, $title) {
		return "";
	}

	public function linkButtonUrl($username) {
		return "";
	}

	public function shareButton($url, $title = '', $showCount = false) {

		$service_url = esc_url($this-> shareButtonUrl($url, $title));

		$html = '<a class="' . $this->cssClass() . '" href="' . $service_url . '" '
		        . ($this->newWindow ? 'target="_blank"' : '') . '>';

		$html .= $this->buttonImage();

		if ($this->hasShareCount()) {
			$html .= $this->shareCountHtml($showCount);
		}

		$html .= '</a>';

		return $html;
	}


	public function linkButton($username) {

		$url = esc_url($this->linkButtonUrl($username));

		$html = '<a class="' . $this->cssClass()
		        . '" href="'. $url. '" ' .
		        ($this->newWindow ? 'target="_blank"' : '') . '>';

		$html .= $this->buttonImage();

		$html .= '</a>';

		return $html;
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

	public static function hasShareCount() {
		return false;
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
			return '<span class="crafty-social-share-count-'.$slug.'-'.$key.' crafty-social-share-count" style="display:none;">&nbsp;</span>';
		} else {
			return '';
		}

	}
	
}
 
?>