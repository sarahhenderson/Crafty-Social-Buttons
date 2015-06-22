<?php
/**
 * SH_Crafty_Social_Buttons_Shortcode Class
 * @author   Sarah Henderson
 * @date   2013-07-07
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

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
		add_filter( 'the_content', array( $this, 'add_share_buttons_to_content' ) );

		// register shortcode [csbshare] and [csblink] and [csbnone]
		add_shortcode( 'csblink', array( $this, 'get_link_button_html' ) );
		add_shortcode( 'csbshare', array( $this, 'get_share_button_html' ) );
		add_shortcode( 'csbnone', array( $this, 'shortcode_csbnone' ) );

		// register actions for people to include in their templates
		add_action( 'crafty-social-share-buttons', array( $this, 'output_share_button_html' ) );
		add_action( 'crafty-social-link-buttons', array( $this, 'output_link_button_html' ) );
		add_action( 'crafty-social-share-page-buttons', array( $this, 'output_share_button_html_for_page' ) );

	}

	/**
	 * Checks which classes are available in the services folder and registers them
	 */
	function loadAllServices() {
		$this->allServices = array();

		$directory = plugin_dir_path( __FILE__ ) . 'services';
		foreach ( scandir( $directory ) as $file ) {
			if ( '.' === $file ) {
				continue;
			}
			if ( '..' === $file ) {
				continue;
			}
			if ( "class-SH_Social_Service.php" === $file ) {
				continue;
			}

			$matches = array();
			if ( preg_match( "/^class-SH_(.+)\.php$/", $file, $matches ) ) {
				$this->allServices[] = $matches[1];
			}
		}
	}

	/**
	 * Checks which folders are available in the button directory and registers them
	 */
	function loadAllImageSets() {
		$this->allImageSets = array();

		$directory = plugin_dir_path( __FILE__ ) . 'buttons';
		foreach ( scandir( $directory ) as $folder ) {
			if ( '.' === $folder ) {
				continue;
			}
			if ( '..' === $folder ) {
				continue;
			}

			if ( ! preg_match( "/\./", $folder ) ) { // no dots allowed, only folders
				$this->allImageSets[] = $folder;
			}
		}
	}


	// get and show share buttons
	function add_share_buttons_to_content( $content ) {
		$settings = $this->getSettings();

		// placement on pages/posts/categories/archives/homepage
		if ( $this->should_show_buttons( $content, $settings ) ) {

			$buttons = $this->get_buttons_html( 'share' );

			switch ( $settings['position'] ) {

				case 'above': // before the content
					return $buttons . $content;
					break;

				case 'below': // after the content
					return $content . $buttons;
					break;

				case 'floating': // floating to left or right of content
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

	function should_show_buttons( $content, $settings ) {
        global $post;

		if ( has_shortcode( $content, 'csbnone' ) ) {
			return false;
		}

		if ( is_page() && ! is_front_page() && $settings['show_on_pages'] ) {
			return true;
		}
		if ( is_home() && $settings['show_on_home'] ) {
			return true;
		}
		if ( is_category() && $settings['show_on_category'] ) {
			return true;
		}
		if ( is_archive() && ! is_category() && $settings['show_on_archive'] ) {
			return true;
		}
		if ( is_front_page() && $settings['show_on_static_home'] ) {
			return true;
		}
        if ( is_single() && $settings['show_on_posts'] ) {
            if ($settings['post_types_are_filtered']) {

                $post_type = get_post_type($post->ID);
                $post_types_for_display = $settings['post_types_for_display'];

                // if nothing has been selected to display, do not show
                if (!is_array($post_types_for_display)) return false;

                return in_array($post_type, $post_types_for_display);

            } else {
                return true;
            }
        }

		return false;
	}

	/**
	 * Generates the markup for the link buttons
	 */
	function get_link_button_html() {
		return $this->get_buttons_html( 'link' );
	}

	/**
	 * Outputs the markup for the link buttons
	 */
	function output_link_button_html() {
		echo $this->get_buttons_html( 'link' );
	}

	/**
	 * Generates the markup for the share buttons
	 */
	function get_share_button_html() {
		return $this->get_buttons_html( 'share' );
	}

	function get_share_button_html_for_page() {
		return $this->get_buttons_html( 'share', true );
	}


	/**
	 * Outputs the markup for the share buttons
	 */
	function output_share_button_html() {
		echo $this->get_buttons_html( 'share' );
	}

	function output_share_button_html_for_page() {
		echo $this->get_buttons_html( 'share', true );
	}

	/**
	 * Generates the markup for the link buttons
	 */
	function shortcode_csbnone() {
		return; // nothing to output.  The other shortcodes will be suppressed if this shortcode is present.
	}

	/**
	 * Generates the markup for the share buttons
	 * Type must be either 'share' or 'link'
	 */
	private function get_buttons_html( $type = 'share', $sharePageUrl = false ) {
		global $post, $wp;

		if ( 'share' != $type && 'link' != $type ) {
			$type = 'share';
		}

		// get settings
		$settings = $this->getSettings();

		$text             = $settings[ $type . '_caption' ];
		$selectedServices = explode( ',', $settings[ $type . '_services' ] );
		$sizeKey          = substr( strval( $settings[ $type . '_image_size' ] ), 0, 1 );
		$alignment        = $settings[ $type . '_alignment' ];
		$caption_position = $settings[ $type . '_caption_position' ];
		$showCount        = $settings['show_count'];
		$float            = false;

		// use wordpress functions for page/post details
		if ( $sharePageUrl || ! $post ) {
			$postId = "page";
			$url    = home_url( $wp->request );
			$title  = wp_title( ' ', false, 'right' );

		} else {
			$postId = $post->ID;
			$url    = get_permalink( $post->ID );
			$title  = get_the_title( $post->ID );
		}

		if ( $showCount && $type == 'share' ) { // add url and title to JS for our scripts to access
			$servicesWithShareCount = $this->get_services_with_share_count( $selectedServices );
			$data                   = array(
				'url'         => $url,
				'callbackUrl' => wp_nonce_url( admin_url( 'admin-ajax.php' ) . '?action=share_count' ),
				'title'       => $title,
				'services'    => $servicesWithShareCount,
				'key'         => $postId
			);
			wp_localize_script( $this->plugin_slug . '-script', 'crafty_social_buttons_data_' . $postId, $data );
		}

		$css_classes   = array();
		$css_classes[] = "crafty-social-buttons";
		$css_classes[] = "crafty-social-$type-buttons";
		$css_classes[] = "crafty-social-buttons-size-$sizeKey";
		$css_classes[] = "crafty-social-buttons-align-$alignment";
		$css_classes[] = "crafty-social-buttons-caption-$caption_position";

        if ($type == 'share') {
            $float = $settings[$type . '_float_buttons'];
            $float_alignment = $settings[$type . '_float_alignment'];
            $float_height = $settings[$type . '_float_height'];

            if ($float && (is_single() || is_page())) {
                $css_classes[] = "crafty-social-buttons-floating";
                $css_classes[] = "crafty-social-buttons-floating-align-$float_alignment";
                $css_classes[] = "crafty-social-buttons-floating-top-$float_height";
            }
        }

        if ($settings[$type."_css_classes"])
            $css_classes[] = $settings[$type."_css_classes"];

        $css_class_string = join( " ", $css_classes );

		$buttonHtml = '<div class="' . $css_class_string . '">';
		if ( $text != '' ) {
			$buttonHtml .= '<span class="crafty-social-caption">' . $text . '</span>';
		}
		$buttonHtml .= '<ul class="crafty-social-buttons-list">';

		// generate markup for each button
		foreach ( $selectedServices as $serviceName ) {

			$button = $this->get_individual_button_html( $type, $serviceName, $url, $title, $showCount, $settings, $postId );
			if ( ! empty( $button ) ) {
				$buttonHtml .= '<li>' . $button . '</li>';
			}
		}

		$buttonHtml .= '</ul></div>';

		return $buttonHtml;
	}

	/* Returns a list containing only those selected services that support getting share counts */
	function get_services_with_share_count( $selectedServices ) {
		$services = array();

		foreach ( $selectedServices as $serviceName ) {
			$this->ensure_class_included( $serviceName );
			$class = "SH_$serviceName";

			if ( class_exists( $class ) ) {
				//$hasCount = $class::hasShareCount(); // PHP 5.3+
				$hasCount = call_user_func( "$class::hasShareCount" ); // PHP 5.2
				if ( $hasCount ) {
					$services[] = $serviceName;
				}
			}
		}

		return $services;
	}

	/* Generates the markup for an individual share button */
	function get_individual_button_html( $type, $serviceName, $url, $title, $showCount, $settings, $key ) {

		$this->ensure_class_included( $serviceName );
		$class = "SH_$serviceName";

		if ( class_exists( $class ) ) {
			$service = new $class( $type, $settings, $key );

			$username = isset( $settings[ $serviceName ] ) ? $settings[ $serviceName ] : '';
			if ( 'share' == $type ) {
				return $service->shareButton( $url, $title, $showCount );
			} else {
				return $service->linkButton( $username );
			}
		} else {
			return "";
		}
	}

	function ensure_class_included( $serviceName ) {

		if ( ! class_exists( 'SH_Social_Service' ) ) {
			include_once( plugin_dir_path( __FILE__ ) . "services/class-SH_Social_Service.php" );
		}

		$class = "SH_$serviceName";
		if ( ! class_exists( $class ) ) {
			$file_path = plugin_dir_path( __FILE__ ) . "services/class-$class.php";
			if ( file_exists( $file_path ) ) {
				$file = include_once( $file_path );
			}
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


}