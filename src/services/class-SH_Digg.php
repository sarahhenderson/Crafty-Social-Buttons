<?php
/**
 * SH_Digg Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Digg extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Digg";
	}

	public function shareButtonUrl($url, $title) {
		
		return "http://www.digg.com/submit?url=$url";
	}
	
	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://www.digg.com/$username";
		}
		return $url;
	}

	public static function description() {
		return __('Hint','crafty-social-buttons') . ": www.digg.com/<strong>user-id</strong>";
	}
}