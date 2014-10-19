<?php
/**
 * SH_RSS Class
 * @author 		Sarah Henderson
 * @date			2013-12-26
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_RSS extends SH_Social_Service {
	
	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "RSS";
	}

	
	public function shareButton($url, $title = '', $showCount = false) {
		return '';
	}
	
	public function linkButtonUrl($username) {
		
		$url = empty($username) ? get_bloginfo('rss2_url') : $username;
		return $url;
	}
	
	public static function canShare() {
		return false;	
	}

	public function getLinkButtonTitle() {
		return "Subscribe via RSS";
	}

	public static function description() {
		return __('Hint: enter full url for feed service (including http://) or leave blank to use built-in WordPress RSS feed url','crafty-social-buttons');
	}

}