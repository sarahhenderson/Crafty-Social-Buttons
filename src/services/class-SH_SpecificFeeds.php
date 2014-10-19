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

	public function getLinkButtonTitle() {
		return "Subscribe via email using Specific Feeds";
	}

	public static function description() {
		return 'Optional - defaults to <em>subscribe by email</em> page.  Or enter your custom url. <a href="http://www.specificfeeds.com/rss">More info</a>.';
	}
}