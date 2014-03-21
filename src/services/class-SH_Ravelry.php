<?php
/**
 * SH_Ravelry Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Ravelry extends SH_Social_Service {
	
	public function __construct($newWindow, $imageSet, $settings) {
		parent::__construct($newWindow, $imageSet, $settings);
		$this->service = "Ravelry";
		$this->imageUrl = $this->imagePath . "ravelry.png";
	}

	
	public function shareButton($url, $title = '', $showCount = false) {

		$html = '<a class="' . $this->cssClass() . '" href="http://www.ravelry.com/bookmarklets/queue?'
			. 'url=' . $url 
			. '&title=' . urlencode($title) . '" ' 
			. ($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();
	
		$html .= '</a>';
	
		return $html;
	}
	
	public function linkButton($username) {
		
		if (strpos($username, 'http://') === 0) {
			$url = $username;
		} else {
			$url = "http://www.ravelry.com/people/$username";
		}
		$html = '<a class="' . $this->cssClass() . '" href="'. $url. '" ' . 
			($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	

	public static function description() {
		return "Hint: www.ravelry.com/people/<strong>user-id</strong>";	
	}
}

?>