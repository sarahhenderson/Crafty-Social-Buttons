<?php
/**
 * SH_SpecificFeeds Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_SpecificFeeds extends SH_Social_Service {
	
	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "SpecificFeeds";
		$this->imageUrl = $this->imagePath . "specificfeeds.png";
	}

	
	public function shareButton($url, $title = '', $showCount = false) {
		return '';
	}
	
	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://www.specificfeeds.com/follow ";
		}
		return $url;
	}
	
	public static function canShare() {
		return false;	
	}

	public static function description() {
		return 'Not necessary - will automatically <a href="http://www.specificfeeds.com/rss">let users subscribe to your blog via email</a>.  Optionally enter your custom url.';
	}
}

