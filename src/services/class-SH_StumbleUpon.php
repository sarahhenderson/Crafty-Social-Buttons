<?php
/**
 * SH_StumbleUpon Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_StumbleUpon extends SH_Social_Service {

	public function __construct($newWindow, $imageSet, $settings) {
		parent::__construct($newWindow, $imageSet, $settings);
		$this->service = "StumbleUpon";
		$this->imageUrl = $this->imagePath . "stumbleupon.png";
	}

	public function shareButton($url, $title = '', $showCount = false) {
		
		$html = '<a class="' . $this->cssClass() . '" href="http://www.stumbleupon.com/submit?'  
			 . 'url=' . $url 
			 . '&title=' . urlencode($title) . '" ' 
			 . ($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();	
		
		if ($showCount) {
			$html .= '<span class="crafty-social-share-count">' . $this->shareCount($url) . '</span>';	
		}

		$html .= '</a>';
	
		return $html;
	}
	
	public function linkButton($username) {
		
		if (strpos($username, 'http://') === 0) {
			$url = $username;
		} else {
			$url = "http://www.stumbleupon.com/stumbler/$username";
		}
		$html = '<a class="' . $this->cssClass() . '" href="'. $url . '" ' . 
			 ($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	
	public function shareCount($url) {
		 $response = wp_remote_get('http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $url);
		 if (is_wp_error($response)){
        // return zero if response is error                             
        return "0";             
		 } else {
			 $json = json_decode($response['body'], true);
			 if (isset($json['result']['views'])) {
				 return $json['result']['views'];
			 } else {
				 return '0';
			 }
		 }
	}
	public static function description() {
		return "Hint: www.stumbleupon/stumbler/<strong>user-id</strong>";	
	}
}
?>