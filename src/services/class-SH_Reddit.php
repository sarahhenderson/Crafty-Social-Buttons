<?php
/**
 * SH_Reddit Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Reddit extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Reddit";
		$this->imageUrl = $this->imagePath . "reddit.png";
	}

	public function shareButtonUrl($url, $title) {
		$title = urlencode($title);

		return "http://reddit.com/submit?url=$url&title=$title";
	}
	
	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://www.reddit.com/user/$username";
		}
		return $url;
	}
	
	public function fetchShareCount($url) {
		 $response = wp_remote_get('http://www.reddit.com/api/info.json?url=' . $url);
		 if (is_wp_error($response)){
            // return zero if response is error
            return 0;
		 } else {
			 $json = json_decode($response['body'], true);
			 if (isset($json['data']['children']['0']['data']['score'])) {
				 return $json['data']['children']['0']['data']['score'];
			 } else {
				 return 0;
			 }
		 }
	}

	public static function hasShareCount() {
		return true;
	}


	public static function description() {
		return __('Hint','crafty-social-buttons') . ": www.reddit.com/user/<strong>user-id</strong>";
	}
}