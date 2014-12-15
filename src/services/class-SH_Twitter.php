<?php
/**
 * SH_Twitter Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Twitter extends SH_Social_Service {
	
	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Twitter";
		$this->text = isset($settings['twitter_body']) ? $settings['twitter_body'] : '';
		$this->show_title = isset($settings['twitter_show_title']) && $settings['twitter_show_title'];
	}

	public function shareButtonUrl($url, $title) {
		if ($this->show_title) {
			$this->text .= ' '.$title;
		}
		$text = htmlspecialchars(urlencode(html_entity_decode(trim($this->text), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8');

		return "http://twitter.com/share?url=$url&text=$text";
	}

	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://twitter.com/$username";
		}
		return $url;
	}
	
	public function fetchShareCount($url) {
		
		 $response = wp_remote_get('http://urls.api.twitter.com/1/urls/count.json?url=' . $url);
		 if (is_wp_error($response)){
	        // return zero if response is error
            return 0;
		 } else {
			 $json = json_decode($response['body'], true);
			 if (isset($json['count'])) {
				 return $json['count'];
			 } else {
				 return 0;
			 }
		 }
	}

	public static function hasShareCount() {
		return true;
	}


	public static function description() {
		return __('Hint','crafty-social-buttons') . ": @<strong>user-id</strong>";
	}
}
