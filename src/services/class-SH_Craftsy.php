<?php
/**
 * SH_Craftsy Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Craftsy extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Craftsy";
	}

	public function shareButton($url, $title = '', $showCount = false) {		
		return '';
	}
	
	public function linkButtonUrl($username) {
		if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else if (strpos($username, "instructors/") !== false) {
			$url = "http://craftsy.com/$username";
		} else if (strpos($username, "pattern-store") !== false) {
			$url = "http://craftsy.com/user/$username";
		} else if (is_numeric($username)) {
			$url = "http://craftsy.com/user/$username";
		} else {
			$url = "http://craftsy.com/instructors/$username";
		}

		return $url;
	}
	
	public static function canShare() {
	 	return false;	
	}
	
	public static function description() {
        return __('Hint','crafty-social-buttons').": www.craftsy.com/user/<strong>user-id</strong>/ ("
        . __('numbers','crafty-social-buttons') .')'
        . __('To link to pattern store or instructor page, enter the full url.','crafty-social-buttons');
	}
}