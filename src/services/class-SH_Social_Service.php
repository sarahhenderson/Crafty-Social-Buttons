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

		$url = esc_url($this-> shareButtonUrl($url, $title));
		$buttonTitle = $this->getShareButtonTitle();
		return $this->generateButtonHtml($url, $buttonTitle, $showCount);
	}


	public function linkButton($username) {

		$url = esc_url($this->linkButtonUrl($username));
		$buttonTitle = $this->getLinkButtonTitle();
		return $this->generateButtonHtml($url, $buttonTitle, false);
	}

	private function generateButtonHtml($url, $title = '', $showCount = false) {

		$html = '<a href="' . $url . '"'
		        . ' class="' . $this->cssClass() .'"'
		        . ' title="' . $title . '" '
		        . ($this->newWindow ? 'target="_blank"' : '') . '>';

		$html .= $this->buttonImage($title);

		if ($this->hasShareCount()) {
			$html .= $this->shareCountHtml($showCount);
		}

		$html .= '</a>';

		return $html;
	}

	public function shareCount($url) {
		return 0;
	}

	public function getShareButtonTitle() {
		return __("Share via ", 'crafty-social-buttons') . $this->service;
	}
	public function getLinkButtonTitle() {
		return $this->service;
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

	protected function buttonImage($title = '') {
		$imageUrl = $this->imagePath . trim(strtolower($this->service)) . $this->imageExtension;
		return '<img '
		.' class="crafty-social-button-image"'
		.' alt="'.$title.'"'
		.' width="'.$this->imageSize.'"'
		.' height="'.$this->imageSize.'"'
		.' src="' . $imageUrl .'" />';
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