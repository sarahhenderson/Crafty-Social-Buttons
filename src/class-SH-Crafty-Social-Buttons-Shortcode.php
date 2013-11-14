<?php
/**
 * SH_Crafty_Social_Buttons_Shortcode Class
 * @author	Sarah Henderson
 * @date	2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SH_Crafty_Social_Buttons_Shortcode {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 */
	protected $version = '1.0.1';

	/**
	 * Unique identifier for this plugin.
	 */
	protected $plugin_slug = 'crafty-social-buttons';

	/**
	 * Instance of this class.
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 */
	private function __construct() {
		
		// add share buttons to content 
		add_filter( 'the_content', array($this, 'add_share_buttons_to_content'));	
		
		// register shortcode [csbshare] and [csblink]
		add_shortcode( 'csbshare', array($this, 'get_share_button_html' ));	
		add_shortcode( 'csblink', array($this, 'get_link_button_html' ));	

		// register actions for people to include in their templates
		add_action( 'crafty-social-share-buttons', array($this, 'output_share_button_html' ));	
		add_action( 'crafty-social-link-buttons', array($this, 'output_link_button_html' ));	


	}

	 /**
	  * Checks which classes are available in the services folder and registers them
	  */
	 function loadAllServices() {
			$this->allServices = array();
			
			$directory = plugin_dir_path(__FILE__) . 'services';
			foreach (scandir($directory) as $file) {
				if ('.' === $file) continue;
				if ('..' === $file) continue;	
				if ("class-SH_Social_Service.php" === $file) { continue; }
				
				$matches = array();
				if (preg_match("/^class-SH_(.+)\.php$/", $file, $matches)) {
					$this->allServices[] = $matches[1];
				}
			}	
	 }
	 
	 /**
	  * Checks which folders are available in the button directory and registers them
	  */
	 function loadAllImageSets() {
			$this->allImageSets = array();
			
			$directory = plugin_dir_path(__FILE__) . 'buttons';
			foreach (scandir($directory) as $folder) {
				if ('.' === $folder) continue;
				if ('..' === $folder) continue;	
				
				if (!preg_match("/\./", $folder)) { // no dots allowed, only folders
					$this->allImageSets[] = $folder;
				}
			}	
	 }
	 		


	
	// get and show share buttons
	function add_share_buttons_to_content($content) {
		$settings = get_option($this->plugin_slug);
		
		// placement on pages/posts/categories/archives/homepage
		if (is_page() && $settings['show_on_pages'] || 
			 is_single() && $settings['show_on_posts'] || 
			 is_home() && $settings['show_on_home'] ) {
							
			$buttons = $this->get_buttons_html('share');
			
			switch ($settings['position']) {
			
				case 'above': // before the content
				return $buttons . $content;
				break;
				
				case 'below': // after the content
				return $content . $buttons;
				break;
				
				case 'both': // before and after the content
				return $buttons . $content . $buttons;
				break;
				
				default:
				return $content;
			}
	
		}
		return $content;
	}
	
	/**
	 * Generates the markup for the share buttons
	 */
	function get_share_button_html() {
		return $this->get_buttons_html('share');
	}
	
	/**
	 * Generates the markup for the link buttons
	 */
	function get_link_button_html() {
		return $this->get_buttons_html('link');
	}

	/**
	 * Outputs the markup for the share buttons
	 */
	function output_share_button_html() {
		echo $this->get_buttons_html('share');
	}
	
	/**
	 * Outputs the markup for the link buttons
	 */
	function output_link_button_html() {
		echo $this->get_buttons_html('link');
	}
	
	/**
	 * Generates the markup for the share buttons
	 * Type must be either 'share' or 'link'
	 */
	private function get_buttons_html($type = 'share') {
		global $post;

		if ('share' != $type && 'link' != $type) {
			$type = 'share';	
		}
		
		// get settings
		$settings = get_option($this->plugin_slug);
		
		$text = $settings[$type.'_caption'];
		$selectedServices = explode(',', $settings[$type.'_services']);
		
		// check if we should show the share count
		$showCount = $settings['show_count'];
		
		// use wordpress functions for page/post details
		$url = get_permalink($post->ID);	
		$title = get_the_title($post->ID);

		$buttonHtml = '<div class="crafty-social-buttons crafty-social-'.$type.'-buttons">';
		if ($text != '') {
				$buttonHtml .= '<span class="crafty-social-caption">' . $text . '</span>';
		}
		$buttonHtml .= '<ul class="crafty-social-buttons-list">';
		
		// generate markup for each button
		foreach ($selectedServices as $serviceName) {

			 $button = $this->get_individual_button_html($type, $serviceName, $url, $title, $showCount, $settings);						
			 if (!empty($button)) {
				 $buttonHtml .= '<li>' . $button . '</li>';
			 }
		}
	
		$buttonHtml .= '</ul></div>';		 
		return $buttonHtml;
	}
	
	/**
	 * Generates the markup for an individual share button
	 */
	function get_individual_button_html($type, $serviceName, $url, $title, $showCount, $settings) {
			
		include_once(plugin_dir_path(__FILE__) . "services/class-SH_Social_Service.php");
		$class = "SH_$serviceName";
		
		if (file_exists(plugin_dir_path(__FILE__) . "services/class-$class.php")) {
		
			$file = include_once(plugin_dir_path(__FILE__) . "services/class-$class.php");
			
			$service = new $class($settings['new_window'], $settings[$type.'_image_set'], $settings);
			
			$username = isset($settings[$serviceName]) ? $settings[$serviceName] : '';
			if ('share' == $type) {
				return $service->shareButton($url, $title, $showCount);
			} else {
				return $service->linkButton($username);	
			}
		} else {
			return "";	
		}
	}

	/**
	 * Generates the markup for an individual link button
	 */
	function get_individual_link_button_html($serviceName, $settings) {
			
		include_once(plugin_dir_path(__FILE__) ."services/class-SH_Social_Service.php");
		$class = "SH_$serviceName";
		
		if (file_exists(plugin_dir_path(__FILE__) . "services/class-$class.php")) {
		
			$file = include_once(plugin_dir_path(__FILE__) ."services/class-$class.php");
			$service = new $class($settings['new_window'], $settings['link_image_set'], $settings);
		
			return $service->linkButton();
		
		} else {
			return "";	
		}
	}
		 
	/**
	 * Return an instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


} // end Plugin class
?>
