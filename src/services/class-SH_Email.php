<?php
/**
 * SH_Email Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Email extends SH_Social_Service {

	public function __construct($newWindow, $imageSet, $settings) {
		parent::__construct($newWindow, $imageSet, $settings);
		$this->service = "Email";
		$this->imageUrl = $this->imagePath . "email.png";
		$this->message = isset($settings['email_body']) ?  $settings['email_body'] : '';
	}

	public function shareButton($url, $title = '', $showCount = false) {
		
		$html = '<a class="' . $this->cssClass() . '" 
			href="mailto:?' 
			. 'Subject=' . $title  
			. '&Body=' . trim($this->message . ' ' . $url)  . '">';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	
	public function linkButton($username) {
		
		$url = "mailto:$username";
		$html = '<a class="' . $this->cssClass() . '" 
				 href="'. $url. '" >';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	
	public static function description() {
		return "Hint: Your email address";	
	}
}
?>