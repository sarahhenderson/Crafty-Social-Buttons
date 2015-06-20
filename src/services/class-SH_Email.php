<?php
/**
 * SH_Email Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Email extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Email";
		$this->imageUrl = $this->imagePath . "email.png";
		$this->message = isset($settings['email_body']) ?  $settings['email_body'] : '';
		$this->newWindow = false;
	}

	public function shareButtonUrl($url, $title) {
		$message = trim($this->message . ' ' . $url);
		$message = str_replace(" ", "%20", $message);
		$subject = str_replace(" ", "%20", $title);
		return "mailto:?Subject=$subject&Body=$message";
	}
	
	public function linkButtonUrl($username) {
		
		$url = "mailto:$username";
		return $url;
	}

	public static function description() {
		return __('Hint: Your email address','crafty-social-buttons');
	}
}