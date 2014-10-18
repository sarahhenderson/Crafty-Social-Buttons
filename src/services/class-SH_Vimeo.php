<?php
/**
 * SH_Vimeo Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Vimeo extends SH_Social_Service {
	
	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Vimeo";
		$this->imageUrl = $this->imagePath . "vimeo.png";
	}

	
	public function shareButton($url, $title = '', $showCount = false) {
		return '';
	}
	
	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
	        $url = "http://vimeo.com/$username";
		}
		return $url;
	}
	
	public static function canShare() {
		return false;	
	}

	public static function description() {
		return __('Hint','crafty-social-buttons') . ": vimeo.com/<strong>user-id</strong>/";
	}
}
