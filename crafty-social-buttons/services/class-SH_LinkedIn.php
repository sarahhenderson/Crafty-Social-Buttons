<?php
/**
 * SH_LinkedIn Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_LinkedIn extends SH_Social_Service {

	public function __construct($newWindow, $imageSet, $settings) {
		parent::__construct($newWindow, $imageSet, $settings);
		$this->service = "LinkedIn";
		$this->imageUrl = $this->imagePath . "linkedin.png";
	}

	public function shareButton($url, $title = '', $showCount = false) {
		
		$html = '<a class="' . $this->cssClass() . '" 
			 href="http://www.linkedin.com/shareArticle?mini=true&' 
			 . 'url='. $url
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
		
		$url = "http://linkedin.com/in/$username";
		$html = '<a class="' . $this->cssClass() . '" 
			 href="'. $url. '" ' . 
			 ($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}


	public function shareCount($url) {
   	
		 $response = wp_remote_get('http://www.linkedin.com/countserv/count/share?url=' . $url);
		 if (is_wp_error($response)){
        // return zero if response is error                             
        return "0";             
		 } else {
	
			$responseBody = str_replace('IN.Tags.Share.handleCount(', '', $response['body']);
    	   $responseBody = str_replace(');', '', $responseBody);

			 $json = json_decode($response['body'], true);
			 if (isset($json['count'])) {
				 return $json['count'];
			 } else {
				 return '0';
			 }
		 }
	}
}

?>