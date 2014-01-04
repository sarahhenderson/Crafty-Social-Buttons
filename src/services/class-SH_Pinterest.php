<?php
/**
 * SH_Pinterest Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Pinterest extends SH_Social_Service {

	public function __construct($newWindow, $imageSet, $settings) {
		parent::__construct($newWindow, $imageSet, $settings);
		$this->service = "Pinterest";
		$this->imageUrl = $this->imagePath . "pinterest.png";
	}

	public function shareButton($url, $title = '', $showCount = false) {
		
		$html = "<a class=\"" . $this->cssClass() . "\" href='javascript:void((function()%7Bvar%20e=document.createElement(&apos;script&apos;);e.setAttribute(&apos;type&apos;,&apos;text/javascript&apos;);e.setAttribute(&apos;charset&apos;,&apos;UTF-8&apos;);e.setAttribute(&apos;src&apos;,&apos;http://assets.pinterest.com/js/pinmarklet.js?r=&apos;+Math.random()*99999999);document.body.appendChild(e)%7D)());'>";

		$html .= $this->buttonImage();	
		
		if ($showCount) {
			$html .= '<span class="crafty-social-share-count">' . $this->shareCount($url) . '</span>';	
		}

		$html .= '</a>';
	
		return $html;
	}
	
	public function linkButton($username) {
		
		$url = "http://pinterest.com/$username";
		$html = '<a class="' . $this->cssClass() . '" href="'. $url. '" ' . 
			 ($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	
	public function shareCount($url) {
		 $response = wp_remote_get('http://api.pinterest.com/v1/urls/count.json?callback=&url=' . $url);
		 if (is_wp_error($response)){
        // return zero if response is error                             
        return "0";             
		 } else {
			 $responseBody = str_replace('(', '', $response['body']); // strip random extra parens in json
			 $responseBody = str_replace(')', '', $responseBody);
			 $json = json_decode($responseBody, true);
			 if (isset($json['count'])) {
				 return $json['count'];
			 } else {
				 return '0';
			 }
		 }
	}
	public static function description() {
		return "Hint: www.pinterest.com/<strong>user-id</strong>";	
	}
}

?>