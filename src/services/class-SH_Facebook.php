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

		return "https://www.facebook.com/sharer/sharer.php?u=$url";

	}

	public function linkButtonUrl($username) {

		if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "https://www.facebook.com/$username";
		}
		return $url;
	}

	public function fetchShareCount($url) {

		$response = wp_remote_get("https://graph.facebook.com?id=".urlencode($url));
		 if (is_wp_error($response)){
            // return zero if response is error
            return 0;
		 } else {
             $value = 0;

			  $json = json_decode($response['body'], true);
             if (!isset($json['share'])) return $value;

             $counts = $json['share'];

             $show = $this->settings['facebook_count'];

			 if (isset($counts['share_count'])) {
                 $value += intval($counts['share_count']);
			 }
             if (isset($counts['like_count']) && ($show == 'likes' || $show == 'comments')) {
                 $value += intval($counts['like_count']);
			 }
             if (isset($counts['comment_count']) && $show == 'comments') {
                 $value += intval($counts['comment_count']);
             }
             return $value;
		 }
	}

	public static function hasShareCount() {
		return true;
	}

	public static function description() {
		return __('Hint','crafty-social-buttons') . ": www.facebook.com/<strong>user-id</strong>/";
	}
}