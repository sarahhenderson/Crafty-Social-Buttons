<?php
/**
 * SH_Digg Class
 * @author 		Sarah Henderson
 * @date			2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Digg extends SH_Social_Service {

	public function __construct($type, $settings, $key) {
		parent::__construct($type, $settings, $key);
		$this->service = "Digg";
	}

	public function shareButton($url, $title = '', $showCount = false) {
		
		$html = '<a class="' . $this->cssClass() . '" href="http://www.digg.com/submit?' 
			. 'url='. $url. '" ' 
			. ($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();

		$html .= '</a>';
	
		return $html;
	}
	
	public function linkButton($username) {
		
		if (strpos($username, 'http://') === 0) {
			$url = $username;
		} else {
			$url = "http://www.digg.com/$username";
		}
		$html = '<a class="' . $this->cssClass() . '" href="'. $url. '" ' . 
			($this->newWindow ? 'target="_blank"' : '') . '>';
	
		$html .= $this->buttonImage();	
		
		$html .= '</a>';
	
		return $html;
	}

	public static function description() {
		return "Hint: www.digg.com/<strong>user-id</strong>";	
	}
}
?>