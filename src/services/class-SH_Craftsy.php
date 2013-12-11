<?php
/**
 * SH_Craftsy Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Craftsy extends SH_Social_Service {

	public function __construct($newWindow, $imageSet, $settings) {
		parent::__construct($newWindow, $imageSet, $settings);
		$this->service = "Craftsy";
	}

	public function shareButton($url, $title = '', $showCount = false) {
		
		return '';
	}
	
	public function linkButton($username) {
		if (strpos($username, "instructors/") !== false) {
			$url = "http://craftsy.com/$username";
		} else if (strpos($username, "pattern-store") !== false) {
			$url = "http://craftsy.com/user/$username";
		} else if (is_numeric($username)) {
			$url = "http://craftsy.com/user/$username";
		} else {
			$url = "http://craftsy.com/instructors/$username";
		}

		$html = '<a class="' . $this->cssClass() . '" 
			href="'. $url. '" ' . 
			($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	
	public function shareCount($url) {
		 return '0'; // Craftsy has no share count feature
	}
}
?>