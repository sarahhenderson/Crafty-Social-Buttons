<?php
/**
 * SH_Dawanda Class
 * @author 		Sarah Henderson
 * @date		2016-03-02
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Dawanda extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Dawanda";
	}

	public function shareButton($url, $title = '', $showCount = false) {		
		return '';
	}
	
	public function linkButtonUrl($username) {
		if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://$username.dawanda.com";
		}

		return $url;
	}
	
	public static function canShare() {
	 	return false;	
	}
	
	public static function description() {
        return __('Hint','crafty-social-buttons'). __(": <strong>user-id</strong>.dawanda.com or enter the full url.",'crafty-social-buttons');
	}
}