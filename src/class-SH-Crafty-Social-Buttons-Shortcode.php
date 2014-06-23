<?php
/**
 * SH_Crafty_Social_Buttons_Shortcode Class
 * @author	Sarah Henderson
 * @date	2013-07-07
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SH_Crafty_Social_Buttons_Shortcode {

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
        add_shortcode( 'csblink',       array($this, 'get_link_button_html' ));
		add_shortcode( 'csbshare',      array($this, 'get_share_button_html' ));

		// register actions for people to include in their templates
		add_action( 'crafty-social-share-buttons', array($this, 'output_share_button_html' ));	
		add_action( 'crafty-social-link-buttons', array($this, 'output_link_button_html' ));
        add_action( 'crafty-social-share-page-buttons',  array($this, 'output_share_button_html_for_page' ));

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
		$settings = $this->getSettings();

		// placement on pages/posts/categories/archives/homepage
		if (is_page() && !is_front_page() && $settings['show_on_pages'] ||
			 is_single() && $settings['show_on_posts'] || 
			 is_home() && $settings['show_on_home'] ||
             is_category() && $settings['show_on_category'] ||
             is_archive() && !is_category() && $settings['show_on_archive'] ||
             is_front_page() && $settings['show_on_static_home']) {

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
     * Generates the markup for the link buttons
     */
    function get_link_button_html() {
        return $this->get_buttons_html('link');
    }

    /**
     * Outputs the markup for the link buttons
     */
    function output_link_button_html() {
        echo $this->get_buttons_html('link');
    }

	/**
	 * Generates the markup for the share buttons
	 */
	function get_share_button_html() { return $this->get_buttons_html('share'); }
    function get_share_button_html_for_page() { return $this->get_buttons_html('share', true); }


	/**
	 * Outputs the markup for the share buttons
	 */
	function output_share_button_html() { echo $this->get_buttons_html('share'); }
    function output_share_button_html_for_page() { echo $this->get_buttons_html('share', true); }


    /**
	 * Generates the markup for the share buttons
	 * Type must be either 'share' or 'link'
	 */
	private function get_buttons_html($type = 'share', $sharePageUrl = false) {
		global $post, $wp;

		if ('share' != $type && 'link' != $type) {
			$type = 'share';	
		}
		
		// get settings
		$settings = $this->getSettings();
		
		$text = $settings[$type.'_caption'];
		$selectedServices = explode(',', $settings[$type.'_services']);
        $sizeKey = substr(strval($settings[$type.'_image_size']),0,1);
		
		// check if we should show the share count
		$showCount = $settings['show_count'];
		
		// use wordpress functions for page/post details

        if ($sharePageUrl) {
            $postId = "page";
            $url = home_url( $wp->request );
            $title = wp_title(' ', false, 'right');

        } else {
            $postId = $post->ID;
            $url = get_permalink($post->ID);
            $title = get_the_title($post->ID);
        }

		if ($showCount && $type == 'share') { // add url and title to JS for our scripts to access
			$data = array( 'url' => $url,
						   'callbackUrl' => wp_nonce_url(admin_url( 'admin-ajax.php' ) . '?action=share_count'),
			               'title' => $title,
			               'services' => $selectedServices,
						   'key' => $post->ID);
			wp_localize_script( $this->plugin_slug . '-scripts', 'crafty_social_buttons_data_'.$postId, $data );
		}

		$buttonHtml = '<div class="crafty-social-buttons crafty-social-'.$type.'-buttons crafty-social-buttons-size-'.$sizeKey.'">';
		if ($text != '') {
				$buttonHtml .= '<span class="crafty-social-caption">' . $text . '</span>';
		}
		$buttonHtml .= '<ul class="crafty-social-buttons-list">';
		
		// generate markup for each button
		foreach ($selectedServices as $serviceName) {

			 $button = $this->get_individual_button_html($type, $serviceName, $url, $title, $showCount, $settings, $post->ID);
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
	function get_individual_button_html($type, $serviceName, $url, $title, $showCount, $settings, $key) {
			
		include_once(plugin_dir_path(__FILE__) . "services/class-SH_Social_Service.php");
		$class = "SH_$serviceName";
		
		if (file_exists(plugin_dir_path(__FILE__) . "services/class-$class.php")) {
		
			$file = include_once(plugin_dir_path(__FILE__) . "services/class-$class.php");
			
			$service = new $class($type, $settings, $key);
			
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
	function get_individual_link_button_html($serviceName, $settings, $key = '') {
			
		include_once(plugin_dir_path(__FILE__) ."services/class-SH_Social_Service.php");
		$class = "SH_$serviceName";
		
		if (file_exists(plugin_dir_path(__FILE__) . "services/class-$class.php")) {
		
			$file = include_once(plugin_dir_path(__FILE__) ."services/class-$class.php");
			$service = new $class($settings['new_window'], $settings['link_image_set'], $settings, $key);
		
			return $service->linkButton();
		
		} else {
			return "";	
		}
	}

    /**
     * Loads all the settings from the database
     */
    function getSettings() {
        $settings = get_option( $this->plugin_slug );
        $defaults = SH_Crafty_Social_Buttons_Plugin::get_default_settings();
        return wp_parse_args( $settings, $defaults );
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
