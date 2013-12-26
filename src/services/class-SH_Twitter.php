<?php
/**
 * SH_Twitter Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Twitter extends SH_Social_Service {
	
	public function __construct($newWindow, $imageSet, $settings) {
		parent::__construct($newWindow, $imageSet, $settings);
		$this->service = "Twitter";
		$this->text = isset($settings['twitter_body']) ? '' : $settings['twitter_body']; 
	}

	
	public function shareButton($url, $title = '', $showCount = false) {

		$html = '<a class="' . $this->cssClass() . '" 
			href="http://twitter.com/share?'
			. 'url=' . $url 
			. '&text=' . urlencode(trim($this->text . ' ' . $title)) . '" ' 
			. ($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();
	
		if ($showCount) {
			$html .= '<span class="crafty-social-share-count">' . $this->shareCount($url) . '</span>';	
		}
	
		$html .= '</a>';
	
		return $html;
	}
	
	public function linkButton($username) {
		
		$url = "http://twitter.com/$username";
		$html = '<a class="' . $this->cssClass() . '" 
					   href="'. $url. '" ' . 
						($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	
	public function shareCount($url) {
		
		 $response = wp_remote_get('http://urls.api.twitter.com/1/urls/count.json?url=' . $url);
		 if (is_wp_error($response)){
        // return zero if response is error                             
        return "0";             
		 } else {
			 $json = json_decode($response['body'], true);
			 if (isset($json['count'])) {
				 return $json['count'];
			 } else {
				 return '0';
			 }
		 }
	}
	
	public static function description() {
		return "Hint: @<strong>user-id</strong>";	
	}
}

?>

