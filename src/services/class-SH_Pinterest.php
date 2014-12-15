<?php
/**
 * SH_Pinterest Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Pinterest extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Pinterest";
		$this->imageUrl = $this->imagePath . "pinterest.png";
	}

	public function shareButton($url, $title = '', $showCount = false) {

		$linkTitle = $this->getShareButtonTitle();

		$html = "<a title=\"" . $linkTitle . "\" class=\"" . $this->cssClass() . "\" href='javascript:void((function()%7Bvar%20e=document.createElement(&apos;script&apos;);e.setAttribute(&apos;type&apos;,&apos;text/javascript&apos;);e.setAttribute(&apos;charset&apos;,&apos;UTF-8&apos;);e.setAttribute(&apos;src&apos;,&apos;http://assets.pinterest.com/js/pinmarklet.js?r=&apos;+Math.random()*99999999);document.body.appendChild(e)%7D)());'>";

		$html .= $this->buttonImage($linkTitle);

		$html .= $this->shareCountHtml($showCount);

		$html .= '</a>';

		return $html;
	}
	
	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://pinterest.com/$username";
		}
		return $url;
	}
	
	public function fetchShareCount($url) {
		 $response = wp_remote_get('http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=' . $url);
		 if (is_wp_error($response)){
            // return zero if response is error
            return 0;
		 } else {
			 $responseBody = str_replace('receiveCount(', '', $response['body']); // strip callback info from jsonp
			 $responseBody = str_replace(')', '', $responseBody);
			 $json = json_decode($responseBody, true);
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
		return __('Hint','crafty-social-buttons') . ": www.pinterest.com/<strong>user-id</strong>";
	}
}