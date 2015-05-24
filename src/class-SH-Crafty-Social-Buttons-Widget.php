<?php
/**
 * SH_Crafty_Social_Buttons_Widget Class
 * @author 	Sarah Henderson
 * @date	2013-07-07
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
// widget class
class SH_Crafty_Social_Buttons_Widget extends WP_Widget {

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
	public function __construct() {
		parent::__construct(
			$this->plugin_slug.'_widget', // Base ID
			__('Crafty Social Buttons', $this->plugin_slug), // Name
			array( 'description' => __( 'Add Crafty Social Link Buttons or Share Buttons to your site', $this->plugin_slug ), ) 
		);

	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		$buttonType = isset($instance['buttonType']) ? $instance['buttonType'] : 'csblink';
		$before_widget = isset($instance['$before_widget']) ? $instance['$before_widget'] : '<div class="widget widget_crafty_social_buttons">';
		$after_widget = isset($instance['$after_widget']) ? $instance['$after_widget'] : '</div>';

		echo $before_widget;
		$shortcode = "[$buttonType]";
	
		echo do_shortcode($shortcode, $this->plugin_slug . "_widget" );
	
		echo $after_widget;
	
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param    array    new_instance    The previous instance of values before the update.
	 * @param    array    old_instance    The new instance of values to be generated via the update.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		$instance['buttonType'] = strip_tags( $new_instance['buttonType'] );
		return $instance;
	
	}


	/**
	 * Generates the administration form for the widget.
	 *
	 * @param    array    instance    The array of keys and values for the widget.
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		
		extract($instance);

		$buttonType = isset($instance['buttonType']) ? $instance['buttonType'] : 'csblink';

		include( plugin_dir_path( __FILE__ ) . '/views/widget.php' );

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