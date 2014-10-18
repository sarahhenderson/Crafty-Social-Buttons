<?php
/**
 * SH_Flickr Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Flickr extends SH_Social_Service {
	
	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Flickr";
	}

	
	public function shareButton($url, $title = '', $showCount = false) {
		return '';
	}
	
	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://flickr.com/photos/$username";
		}
		return $url;
	}
	
	public static function canShare() {
		return false;	
	}

	public static function description() {
		return __('Hint','crafty-social-buttons') . ": flickr.com/photos/<strong>user-id</strong> "
        ."(".__('numbers and letters','crafty-social-buttons') .")";
	}
}