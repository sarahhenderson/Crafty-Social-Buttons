<?php
/**
 * SH_Craftinoo Class
 * @author 		Sarah Henderson
 * @date		2015-12-05
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Craftinoo extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Craftinoo";
	}

	public function shareButton($url, $title = '', $showCount = false) {		
		return '';
	}
	
	public function linkButtonUrl($username) {
		if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://craftinoo.com/seller/$username";
		}

		return $url;
	}
	
	public static function canShare() {
	 	return false;	
	}
	
	public static function description() {
        return __('Hint','crafty-social-buttons'). __(": craftinoo.com/seller/<strong>user-id</strong>/ or enter the full url.",'crafty-social-buttons');
	}
}