<?php
/**
 * SH_Tumblr Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Tumblr extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Tumblr";
		$this->imageUrl = $this->imagePath . "tumblr.png";
	}

	public function shareButtonUrl($url, $title) {
		
		// Tumblr insists there is no protocol on the url
		if (preg_match('[http://]', $url)) {
			$url = str_replace('http://', '', $url);			
		} else if (preg_match('[https://]', $url)) { // check if https:// is included
			$url = str_replace('https://', '', $url);			
		}

		$title = urlencode($title);
		return "http://www.tumblr.com/share/link?url=$url&name=$title";
	}
	
	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://".$username.".tumblr.com/";
		}
		return $url;
	}
	
	public static function description() {
		return __('Hint','crafty-social-buttons') . ": http://<strong>user-id</strong>.tumblr.com";
	}
}
