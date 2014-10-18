<?php
/**
 * SH_Instagram Class
 * @author 		Sarah Henderson
 * @date			2013-12-26
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Instagram extends SH_Social_Service {
	
	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Instagram";
	}

	
	public function shareButton($url, $title = '', $showCount = false) {
		return '';
	}
	
	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://instagram.com/$username";
		}
		return $url;
	}
	
	public static function canShare() {
		return false;	
	}

	public static function description() {
		return __('Hint','crafty-social-buttons') . ": instagram.com/<strong>user-id</strong>";
	}

}