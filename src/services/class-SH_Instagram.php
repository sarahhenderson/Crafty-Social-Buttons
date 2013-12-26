<?php
/**
 * SH_Instagram Class
 * @author 		Sarah Henderson
 * @date			2013-12-26
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Instagram extends SH_Social_Service {
	
	public function __construct($newWindow, $imageSet, $settings) {
		parent::__construct($newWindow, $imageSet, $settings);
		$this->service = "Instagram";
	}

	
	public function shareButton($url, $title = '', $showCount = false) {

		$html = '<a class="' . $this->cssClass() . '" 
			href="http://instagram.com/share?'
			. 'url=' . $url . '" ' 
			. ($this->newWindow ? 'target="_blank"' : '') 
			. '>';
	
		$html .= $this->buttonImage();
	
		if ($showCount) {
			$html .= '<span class="crafty-social-share-count">' . $this->shareCount($url) . '</span>';	
		}
	
		$html .= '</a>';
	
		return $html;
	}
	
	public function linkButton($username) {
		
		$url = "http://instagram.com/$username";
		$html = '<a class="' . $this->cssClass() 
				. '"href="'. $url. '" ' 
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
		return "Hint: instagram.com/<strong>user-id</strong>";	
	}

}

?>

