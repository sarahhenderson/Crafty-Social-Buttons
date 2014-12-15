<?php
/**
 * SH_Facebook Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Facebook extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Facebook";
		$this->imageUrl = $this->imagePath . "facebook.png";
	}

	public function shareButtonUrl($url, $title) {

		return "http://www.facebook.com/sharer/sharer.php?u=$url";

	}

	public function linkButtonUrl($username) {

		if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://www.facebook.com/$username";
		}
		return $url;
	}

	public function fetchShareCount($url) {
		$response = wp_remote_get("http://api.facebook.com/method/links.getStats?urls=$url&format=json");
		 if (is_wp_error($response)){
            // return zero if response is error
            return 0;
		 } else {
			 $json = json_decode($response['body'], true);
			 if (isset($json[0]) && isset($json[0]['share_count'])) {
				 return $json[0]['share_count'];
			 }elseif (isset($json['shares'])) {
				 return $json['shares'];
			 } elseif (isset($json['likes'])) {
				 return $json['likes'];
			 } else {
				 return 0;
			 }
		 }
	}

	public static function hasShareCount() {
		return true;
	}

	public static function description() {
		return __('Hint','crafty-social-buttons') . ": www.facebook.com/<strong>user-id</strong>/";
	}
}