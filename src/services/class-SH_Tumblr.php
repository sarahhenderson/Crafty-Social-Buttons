<?php
/**
 * SH_Tumblr Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Tumblr extends SH_Social_Service {

	public function __construct($newWindow, $imageSet, $settings) {
		parent::__construct($newWindow, $imageSet, $settings);
		$this->service = "Tumblr";
		$this->imageUrl = $this->imagePath . "tumblr.png";
	}

	public function shareButton($url, $title = '', $showCount = false) {
		
		// Tumblr insists there is no protocol on the url
		if (preg_match('[http://]', $url)) {
			$url = str_replace('http://', '', $url);			
		} else if (preg_match('[https://]', $url)) { // check if https:// is included
			$url = str_replace('https://', '', $url);			
		}

		$html = '<a class="' . $this->cssClass() . '" href="http://www.tumblr.com/share/link?' 
			 . 'url=' . $url 
			 . '&name=' . urlencode($title) . '" ' 
			 . ($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	
	public function linkButton($username) {
		if (!empty($username)) $username = $username.'.';
		$url = "http://".$username."tumblr.com/";
		$html = '<a class="' . $this->cssClass() . '" href="'. $url . '" ' . 
			($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	
	public static function description() {
		return "Hint: http://<strong>user-id</strong>.tumblr.com";	
	}
}

?>