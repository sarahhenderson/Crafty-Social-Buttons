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

	public function shareButton($url, $title = '', $showCount = false) {
		
		$html = '<a class="' . $this->cssClass() . '" href="http://www.facebook.com/sharer/sharer.php?' 
			 . 'u=' . $url. '" ' 
			 . ($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();

		$html .= $this->shareCountHtml($showCount);
	
		$html .= '</a>';
	
		return $html;
	}
	
	public function linkButton($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://www.facebook.com/$username";
		}
		$html = '<a class="' . $this->cssClass() . '" href="'. $url. '" ' . 
			 ($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	
	public function shareCount($url) {
		 $response = wp_remote_get('http://graph.facebook.com/' . $url);
		 if (is_wp_error($response)){
        // return zero if response is error                             
        return "0";             
		 } else {
			 $json = json_decode($response['body'], true);
			 if (isset($json['shares'])) {
				 return $json['shares'];
			 } elseif (isset($json['likes'])) {
				 return $json['likes'];
			 } else {
				 return '0';
			 }
		 }
	}

	public static function description() {
		return __('Hint','crafty-social-buttons') . ": www.facebook.com/<strong>user-id</strong>/";
	}
}
?>