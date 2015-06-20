<?php
/**
 * SH_WhatsApp Class
 * @author 		Sarah Henderson
 * @date		2015-06-13
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_WhatsApp extends SH_Social_Service {
	
	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "WhatsApp";
	}

    public static function canLink() {
        return false;
    }

    public function shareButtonUrl($url, $title) {
        $title = str_replace(' ', '+', $title);
		return "whatsapp://send?text=$title:+$url";
    }

	public function linkButtonUrl($username) {
		return '';
	}
}
