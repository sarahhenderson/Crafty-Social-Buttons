<?php
/**
 * SH_LinkedIn Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_LinkedIn extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "LinkedIn";
		$this->imageUrl = $this->imagePath . "linkedin.png";
	}

	public function shareButtonUrl($url, $title) {
		$title = urlencode($title);
		return "http://www.linkedin.com/shareArticle?mini=true&url=$url&title=$title";
	}

	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
            $url = $username;
        } else if (strpos($username, "company/") === 0) {
			  $url = "http://linkedin.com/$username";
        } else {
			  $url = "http://linkedin.com/in/$username";
        }
		return $url;
  	}

	public function fetchShareCount($url) {
   	
		 $response = wp_remote_get('http://www.linkedin.com/countserv/count/share?format=json&url=' . $url);
		 if (is_wp_error($response)){
            // return zero if response is error
            return 0;
		 } else {
			 $json = json_decode($response['body'], true);
			 if (isset($json['count'])) {
				 return $json['count'];
			 } else {
				 return '0';
			 }
		 }
	}

	public static function hasShareCount() {
		return true;
	}


	public static function description() {
		return __('Hint','crafty-social-buttons') . ": www.linkedin.com/in/<strong>user-id</strong> or www.linkedin.com/<strong>company/company-id</strong>";
	}
}