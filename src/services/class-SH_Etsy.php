<?php
/**
 * SH_Twitter Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Etsy extends SH_Social_Service {
	
	public function __construct($newWindow, $imageSet, $settings) {
		parent::__construct($newWindow, $imageSet, $settings);
		$this->service = "Etsy";
		$this->imageUrl = $this->imagePath . "etsy.png";
	}

	
	public function shareButton($url, $title = '', $showCount = false) {
		return '';
	}
	
	public function linkButton($username) {
		
		$url = "http://etsy.com/shop/$username";
		$html = '<a class="' . $this->cssClass() . '" 
		            href="'. $url. '" ' . 
						($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	
	public static function canShare() {
		return false;	
	}

	public static function description() {
		return "Hint: www.etsy.com/shop/<strong>user-id</strong>/";	
	}
}

?>

