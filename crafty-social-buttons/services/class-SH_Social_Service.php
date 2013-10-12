<?php
/**
 * SH_Social_Service Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Social_Service {

	// construct the class
	public function __construct($newWindow, $imageSet, $settings) {
		$this->newWindow = $newWindow;
		$this->imagePath = plugins_url() . "/crafty-social-buttons/buttons/$imageSet/";
		$this->imageExtension = ".png";
		$this->settings = $settings;
		$this->service = "Default"; // must be set correctly in the subclass constructors
	}
	
	// generates the css class for the button link
	protected function cssClass() {
		return "crafty-social-button csb-" . trim(strtolower($this->service));		
	}
	
	public function shareButton($url, $title = '', $showCount = false) {
		return "";
	}
	
	public function linkButton($username) {
		return "";	
	}
	
	public function shareCount($url) {
		return "0";	
	}
	
	protected function buttonImage() {
		$imageUrl = $this->imagePath . trim(strtolower($this->service)) . $this->imageExtension;
		return '<img 
				   title="'.$this->service.'" 
					alt="'.$this->service. '" 
					src="' . $imageUrl .'" />';

	}
	
}
 
?>