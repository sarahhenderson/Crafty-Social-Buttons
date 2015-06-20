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
	public function __construct($type, $settings, $key) {
		$this->cache_key = "_csb_cache";
		$this->service = "Default"; // must be set correctly in the subclass constructors
		$this->settings = $settings;
		$this->key = $key;

		$imageSet = $settings[$type.'_image_set'];
		$this->imagePath = $this->getImageUrlPath($imageSet);

		$this->imageExtension = ".png";
		$this->imageSize = $settings[$type.'_image_size'];
		$this->newWindow = ($type == "link" ? $settings['new_window'] : $settings['open_in'] == 'new_window');
		$this->nofollow = $settings[$type .'_nofollow'];
		$this->hover = $settings[$type .'_hover_effect'];
        $this->popup = ($type == 'share' && $settings['popup']);
	}
	
	// generates the css class for the button link
	protected function cssClass() {
		$css = "crafty-social-button csb-" . trim(strtolower($this->service));
        $css .= " " . $this->hover;
        if ($this->popup) {
            $css .= " popup";
        }

        return $css;
	}

	protected function getImageUrlPath($imageSet) {
		$plugin_url = plugins_url() . "/crafty-social-buttons/buttons/$imageSet/";
		$plugin_file_path = plugin_dir_path(__FILE__) . "../buttons/$imageSet";

		if (is_dir($plugin_file_path)) {
			return $plugin_url;
		} else {
            $upload_dir = wp_upload_dir();
			$custom_url = $upload_dir['baseurl'] . "/crafty-social-buttons/buttons/$imageSet/";
			return $custom_url;
		}
	}

	public function shareButtonUrl($url, $title) {
		return "";
	}

	public function linkButtonUrl($username) {
		return "";
	}

	public function shareButton($url, $title = '', $showCount = false) {

		$url = esc_url($this-> shareButtonUrl($url, $title), array("http", "https", "mailto", "whatsapp"));
		$buttonTitle = $this->getShareButtonTitle();
		return $this->generateButtonHtml($url, $buttonTitle, $showCount);
	}


	public function linkButton($username) {

		$url = esc_url($this->linkButtonUrl($username));
		$buttonTitle = $this->getLinkButtonTitle();
		return $this->generateButtonHtml($url, $buttonTitle, false);
	}

	private function generateButtonHtml($url, $title = '', $showCount = false) {

		$html = '<a href="' . $url . '"'
		        . ' class="' . $this->cssClass() .'"'
		        . ' title="' . $title . '" '
		        . ($this->newWindow ? 'target="_blank"' : '')
		        . ($this->nofollow ? 'rel="nofollow"' : '')
                . '>';

		$html .= $this->buttonImage($title);

		if ($this->hasShareCount()) {
			$html .= $this->shareCountHtml($showCount);
		}

		$html .= '</a>';

		return $html;
	}

	public function shareCount($url, $post_id) {
		if (!$this->hasShareCount())
			return 0;

		if (!is_numeric($post_id)) // url based counts are not cached
			return $this->fetchShareCount($url);

		if (!$this->settings['cache_share_counts']) // caching is not enabled
			return $this->fetchShareCount($url);

		$cached_share_count = $this->getCachedShareCount($post_id);
		if ($cached_share_count != null)
			return $cached_share_count['count'];

		// If we got here, either there was no existing cached value, or the cache had expired
		$count = $this->fetchAndCacheShareCount($post_id, $url);
		return $count;
	}

	protected function fetchAndCacheShareCount($post_id, $url) {
		$share_count = $this->fetchShareCount($url);
		$cache = get_post_meta($post_id, $this->cache_key, true);
		if (!$cache) {
			$cache = array();
		}
		$cache[$this->service] =  array(
			'count' => $share_count,
			'timestamp' => time()
		);

		update_post_meta($post_id, $this->cache_key, $cache);
		return $share_count;
	}

	protected function getCachedShareCount($post_id) {
		$cache = get_post_meta($post_id, $this->cache_key, true);
		if (!$cache) return null;		// exit if there is no cache

		if (!isset($cache[$this->service])) return null; // exit if no cached value for this service
		$service_cache = $cache[$this->service];
		$expiry_time = $service_cache['timestamp'];
		if (!is_numeric($expiry_time)) return null; // exit if there is something wrong with our expiry time

		$minutes_since_caching = (time() - $expiry_time) / 60;
		$cache_expiry_minutes = intval($this->settings['cache_expiry_minutes']);
		if ($minutes_since_caching > $cache_expiry_minutes)
			return null; // exit if cache has expired

		// if we made it here, we have a valid, non-expired cached count
		return $cache[$this->service];
	}

	public function getShareButtonTitle() {
		return __("Share via ", 'crafty-social-buttons') . $this->service;
	}
	public function getLinkButtonTitle() {
		return $this->service;
	}
	public static function canShare() {
		return true;	
	}

	public static function canLink() {
		return true;	
	}

	public static function hasShareCount() {
		return false;
	}

	public static function description() {
		return "";	
	}

	protected function buttonImage($title = '') {
		$imageUrl = $this->imagePath . trim(strtolower($this->service)) . $this->imageExtension;
		return '<img '
		.' class="crafty-social-button-image"'
		.' alt="'.$title.'"'
		.' width="'.$this->imageSize.'"'
		.' height="'.$this->imageSize.'"'
		.' src="' . $imageUrl .'" />';
	}

	protected function shareCountHtml($display) {
		if ($display) {
			$slug = trim(strtolower($this->service));
			$key = $this->key;
			return '<span class="crafty-social-share-count-'.$slug.'-'.$key.' crafty-social-share-count" style="display:none;">&nbsp;</span>';
		} else {
			return '';
		}

	}
	
}