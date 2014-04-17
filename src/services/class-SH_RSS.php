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
	
	public function linkButton($username) {
		
		$url = empty($username) ? get_bloginfo('rss2_url') : $username;
		$html = '<a class="' . $this->cssClass() 
				. '" href="'. $url. '" ' 
				. '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}
	
	public static function canShare() {
		return false;	
	}

	public static function description() {
		return __('Hint: enter full url for feed service (including http://) or leave blank to use built-in WordPress RSS feed url','crafty-social-buttons');
	}

}

?>