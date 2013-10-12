<?php
/**
 * SH_Crafty_Social_Buttons_Plugin Class
 * @author 	Sarah Henderson
 * @date	2013-10-12
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SH_Crafty_Social_Buttons_Plugin {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 */
	protected $version = '1.0.0';

	/**
	 * Unique identifier for this plugin.
	 */
	protected $plugin_slug = 'crafty-social-buttons';

	/**
	 * Instance of this class.
	 */
	protected static $instance = null;
	
	/**
	 * Instanced of other classes we need
	 */
	protected $admin_class = null;
	 
	/**
	 * Slug of the plugin screen.
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 */
	private function __construct() {

		if (is_admin() && !class_exists('SH_Crafty_Social_buttons_Admin')) {
			require_once( plugin_dir_path( __FILE__ ) . 'class-SH-Crafty-Social-Buttons-Admin.php' );
			$this->admin_class = SH_Crafty_Social_Buttons_Admin::get_instance();
		}

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		
		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// add hooks for showing messages and initialising settings
		add_action('admin_notices', array($this, 'show_admin_messages'));  

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 11, 1 );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );						
		
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

	/**
	 * Fired when the plugin is activated.
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		
		 $settings = get_option("crafty-social-buttons");
		 if ( false === $settings ) {
			  $settings = self::get_default_settings();
		 }
		 update_option("crafty-social-buttons", $settings );			
	}

	/**
	 * Fired when the plugin is deactivated.
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles($current_screen) {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		if ( $current_screen ==  $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', 
				plugins_url( 'css/admin.min.css', __FILE__ ), array(), $this->version );
			
		}

	}

	 		
	/**
	 * Register and enqueue admin-specific JavaScript.
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts($current_screen) {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
		
		if ( $current_screen == $this->plugin_screen_hook_suffix ) {

			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script( $this->plugin_slug . '-admin-scripts', 
				plugins_url( 'js/admin.min.js', __FILE__ ), array( 'jquery', 'jquery-ui-core' ), $this->version );
			
		}
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_slug . '-styles', 
			plugins_url( 'css/public.min.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-scripts', 
			plugins_url( 'js/public.min.js', __FILE__ ), array( 'jquery' ), $this->version );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	public function add_plugin_admin_menu() {

		if (!is_admin()) { return; }
		
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Crafty Social Buttons', $this->plugin_slug ),
			__( 'Crafty Social Buttons', $this->plugin_slug ),
			'read',
			$this->plugin_slug,
			array( $this->admin_class, 'display_plugin_admin_page' )
		);

		// Adds help tab when admin page loads
    	add_action('load-'.$this->plugin_screen_hook_suffix, array($this->admin_class, 'add_contextual_help'));
	}	
	
	/**
	 * Show admin notifications
	 */
	function show_admin_messages() {
		// query the db for current settings
		$settings = get_option($this->plugin_slug);
		
		// check if any buttons have been selected
		if (!$settings['configured']) {
		
			// output a warning that buttons need configuring and provide a link to settings
			echo '<div class="updated fade"><p>Thanks for installing <strong>Crafty Social Buttons!</strong>. '.
				 ' Your buttons need <a href="admin.php?page=' . $this->plugin_slug . 
				 '"><strong>configuration</strong></a> before they will appear.</p></div>';
		}
	}
	
			 
	/** 
	 * Get default settings (as an array)
	 */
	public static function get_default_settings() {
		$settings = array(
			'version'				=> '1',
			'configured'			=> false,
			
			'share_image_set' 	=> 'simple',
			'share_caption'		=> 'Share this:',
			'share_services'		=> 'Facebook,Google,Twitter,Ravelry',
			'show_on_posts'		=> false,
			'show_on_pages'		=> false,
			'show_on_home'			=> false,
			'position'				=> 'below',
			'show_count'			=> false,
			'new_window'			=> true,
			'email_body'			=> 'I thought you might like this: ',
			'twitter_body'			=> 'I like this: ',
						
			'link_image_set'	 	=> 'simple',
			'link_caption'			=> 'Find me on:',
			'link_services'		=> 'Facebook,Google,Twitter,Ravelry,Etsy',
		);
		return $settings;
	}

	
}
?>
