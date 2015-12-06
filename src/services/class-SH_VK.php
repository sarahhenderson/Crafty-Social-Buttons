<?php
/**
 * SH_VK Class
 * @author 		Sarah Henderson
 * @date		2015-11-05
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_VK extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "VK";
		$this->imageUrl = $this->imagePath . "vk.png";
	}

	public function shareButtonUrl($url, $title) {

		return "http://vk.com/share.php?url=$url&title=$title";

	}

	public function linkButtonUrl($username) {

		if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://vk.com/$username";
		}
		return $url;
	}


	public static function description() {
		return __('Hint','crafty-social-buttons') . ": vk.com/<strong>user-id</strong>/" . __(" or enter the full url", 'crafty-social-buttons');
	}
}