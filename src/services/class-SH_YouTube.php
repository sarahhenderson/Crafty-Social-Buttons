<?php
/**
 * SH_YouTube Class
 * @author 		Sarah Henderson
 * @date			2013-12-26
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_YouTube extends SH_Social_Service {
	
	public function __construct($newWindow, $imageSet, $settings) {
		parent::__construct($newWindow, $imageSet, $settings);
		$this->service = "YouTube";
	}

	
	public function shareButton($url, $title = '', $showCount = false) {
		return '';
	}
	
	public function linkButton($username) {
		
		if (strpos($username, 'http://') === 0) {
			$url = $username;
		} else {
			$url = "http://youtube.com/user/$username";
		}
		$html = '<a class="' . $this->cssClass() 
				. '" href="'. $url. '" ' 
				. ($this->newWindow ? 'target="_blank"' : '') 
				. '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	
	public static function canShare() {
		return false;	
	}

	public static function description() {
		return "Hint: youtube.com/user/<strong>user-id</strong>";	
	}

}

?>