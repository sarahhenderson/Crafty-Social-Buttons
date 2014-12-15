<?php
/**
 * SH_Ravelry Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Ravelry extends SH_Social_Service {
	
	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Ravelry";
		$this->imageUrl = $this->imagePath . "ravelry.png";
	}

	
	public function shareButtonUrl($url, $title) {
		$title = urlencode($title);

		return "http://www.ravelry.com/bookmarklets/queue?url=$url&title=$title";
	}
	
	public function linkButtonUrl($username) {

        if (strpos($username, 'http://') === 0 || strpos($username, 'https://') === 0) {
			$url = $username;
		} else {
			$url = "http://www.ravelry.com/people/$username";
		}
		return $url;
	}

	public static function description() {
		return __('Hint','crafty-social-buttons') . ": www.ravelry.com/people/<strong>user-id</strong>";
	}
}