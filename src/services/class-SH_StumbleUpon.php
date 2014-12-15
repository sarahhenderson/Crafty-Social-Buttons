<?php
/**
 * SH_StumbleUpon Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_StumbleUpon extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "StumbleUpon";
		$this->imageUrl = $this->imagePath . "stumbleupon.png";
	}

	public function shareButtonUrl($url, $title) {
		$title = urlencode($title);

		return "http://www.stumbleupon.com/submit?url=$url&title=$title";
	}
	
	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://www.stumbleupon.com/stumbler/$username";
		}
		return $url;
	}
	
	public function fetchShareCount($url) {
		 $response = wp_remote_get('http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $url);
		 if (is_wp_error($response)){
            // return zero if response is error
            return 0;
		 } else {
			 $json = json_decode($response['body'], true);
			 if (isset($json['result']['views'])) {
				 return $json['result']['views'];
			 } else {
				 return 0;
			 }
		 }
	}

	public static function hasShareCount() {
		return true;
	}

	public static function description() {
		return __('Hint','crafty-social-buttons') . ": www.stumbleupon/stumbler/<strong>user-id</strong>";
	}
}