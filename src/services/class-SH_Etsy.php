<?php
/**
 * SH_Etsy Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Etsy extends SH_Social_Service {
	
	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Etsy";
		$this->imageUrl = $this->imagePath . "etsy.png";
	}

	
	public function shareButton($url, $title = '', $showCount = false) {
		return '';
	}
	
	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://etsy.com/shop/$username";
		}
		return $url;
	}
	
	public static function canShare() {
		return false;	
	}

	public static function description() {
		return __('Hint','crafty-social-buttons') . ": www.etsy.com/shop/<strong>user-id</strong>/";
	}
}