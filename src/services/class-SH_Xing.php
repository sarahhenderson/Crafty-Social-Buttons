<?php
/**
 * SH_Xing Class
 * @author 		Sarah Henderson
 * @date		2015-12-05
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Xing extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Xing";
		$this->imageUrl = $this->imagePath . "xing.png";
	}

	public function shareButtonUrl($url, $title) {

		$title = urlencode($title);
		return "https://www.xing.com/spi/shares/new?sc_p=xing-share&url=$url&title=$title";

	}

	public function linkButtonUrl($username) {

		if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else if (strpos($username, "company/") === 0) {
			$url = "http://xing.com/$username";
		} else {
			$url = "http://www.xing.com/profile/$username";
		}
		return $url;
	}

	public static function description() {
		return __('Hint','crafty-social-buttons') . ": www.xing.com/profile/<strong>user-id</strong>/" . __(" or enter the full url", 'crafty-social-buttons');
	}
}