<?php
/**
 * SH_Crafty_Social_Buttons_Plugin Class
 * @author   Sarah Henderson
 * @date   2013-10-12
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class SH_Crafty_Social_Buttons_Plugin
{

   /**
    * Plugin version, used for cache-busting of style and script file references.
    */
   protected $version = '1.5.2';

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
   private function __construct()
   {
      if (is_admin() && !class_exists('SH_Crafty_Social_buttons_Admin')) {
         require_once(plugin_dir_path(__FILE__) . 'class-SH-Crafty-Social-Buttons-Admin.php');
         $this->admin_class = SH_Crafty_Social_Buttons_Admin::get_instance();
      }

      // Load plugin text domain
      add_action('init', array($this, 'load_plugin_textdomain'));

      // Add the options page and menu item.
      add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

      // add hooks for showing messages and initialising settings
      add_action('admin_notices', array($this, 'show_admin_messages'));

      // Load admin style sheet and JavaScript.
      add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'), 10, 1);
      add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'), 11, 1);

      // Load public-facing style sheet and JavaScript.
      add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
      add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

      // Set up ajax callbacks for client-side loading of share counts
      add_action('wp_ajax_share_count', array($this, 'get_share_count'));
      add_action('wp_ajax_nopriv_share_count', array($this, 'get_share_count'));

   }

   /**
    * Return an instance of this class.
    */
   public static function get_instance()
   {
      // If the single instance hasn't been set, set it now.
      if (null == self::$instance) {
         self::$instance = new self;
      }
      return self::$instance;
   }

   /**
    * Fired when the plugin is activated.
    * @param    boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
    */
   public static function activate($network_wide)
   {
      if (function_exists('is_multisite') && is_multisite() && $network_wide) {
         global $wpdb;
         $old_blog = get_current_blog_id();

         $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
         foreach ($blogids as $blog_id) {
            switch_to_blog($blog_id);
            self::activate_single_site();
         }
         switch_to_blog($old_blog);
      } else { // activate non-multi-site or single site
         self::activate_single_site();
      }
   }

   private static function activate_single_site()
   {
      $settings = get_option("crafty-social-buttons");
      if (!$settings || count($settings) <= 1) {
         $settings = self::get_default_settings();
      }
      update_option("crafty-social-buttons", $settings);
   }

   /**
    * Fired when the plugin is deactivated.
    * @param    boolean $network_wide True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
    */
   public static function deactivate($network_wide)
   {
      // TODO: Define deactivation functionality here
   }

   /**
    * Load the plugin text domain for translation.
    */
   public function load_plugin_textdomain()
   {

      $domain = $this->plugin_slug;
      $locale = apply_filters('plugin_locale', get_locale(), $domain);

      load_textdomain($domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo');
      load_plugin_textdomain($domain, FALSE, dirname(plugin_basename(__FILE__)) . '/lang/');
   }

   /**
    * Register and enqueue admin-specific style sheet.
    * @return    null    Return early if no settings page is registered.
    */
   public function enqueue_admin_styles($current_screen)
   {

      if (!isset($this->plugin_screen_hook_suffix)) {
         return;
      }

      if ($current_screen == $this->plugin_screen_hook_suffix) {
         wp_enqueue_style($this->plugin_slug . '-admin-styles',
            plugins_url('css/admin.min.css', __FILE__), array(), $this->version);

      }

   }


   /**
    * Register and enqueue admin-specific JavaScript.
    * @return    null    Return early if no settings page is registered.
    */
   public function enqueue_admin_scripts($current_screen)
   {

      if (!isset($this->plugin_screen_hook_suffix)) {
         return;
      }

      if ($current_screen == $this->plugin_screen_hook_suffix) {

         wp_enqueue_script('jquery');
         wp_enqueue_script('jquery-ui-core');
         wp_enqueue_script('jquery-ui-sortable');
         wp_enqueue_script($this->plugin_slug . '-admin-scripts',
            plugins_url('js/admin.min.js', __FILE__), array('jquery', 'jquery-ui-core'), $this->version);

      }
   }

   /**
    * Register and enqueue public-facing style sheet.
    */
   public function enqueue_styles()
   {

      wp_enqueue_style($this->plugin_slug . '-styles',
         plugins_url('css/public.min.css', __FILE__), array(), $this->version);
   }

   /**
    * Register and enqueues public-facing JavaScript files.
    */
   public function enqueue_scripts()
   {
      $settings = $this->getSettings();

      // only add javascript if post counts are to be shown or popups are enabled
      if ($settings['show_count'] || $settings['popup']) {
         wp_enqueue_script($this->plugin_slug . '-script',
            plugins_url('js/public.min.js', __FILE__), array('jquery'), $this->version, true);
      }

       // only add whatsApp sharing if that button is enabled
       if (strpos($settings['share_services'], 'WhatsApp') !== false) {
           wp_enqueue_script($this->plugin_slug . '-whatsapp-script',
               plugins_url('js/whatsapp-share.min.js', __FILE__), array('jquery'), $this->version, true);
       }
   }

   /**
    * Register the administration menu for this plugin into the WordPress Dashboard menu.
    */
   public function add_plugin_admin_menu()
   {

      if (!is_admin()) {
         return;
      }

      $this->plugin_screen_hook_suffix = add_options_page(
         __('Crafty Social Buttons', $this->plugin_slug),
         __('Crafty Social Buttons', $this->plugin_slug),
         'read',
         $this->plugin_slug,
         array($this->admin_class, 'display_plugin_admin_page')
      );

      // Adds help tab when admin page loads
      add_action('load-' . $this->plugin_screen_hook_suffix, array($this->admin_class, 'add_contextual_help'));
   }


   /**
    * Show admin notifications
    */
   function show_admin_messages()
   {

      $settings = $this->getSettings();

      // check if any buttons have been configured
      if (!$settings['configured']) {

         // output a warning that buttons need configuring and provide a link to settings
         echo '<div class="updated fade"><p>Thanks for installing <strong>Crafty Social Buttons!</strong> ' .
            ' Your posts now have the default share buttons. <a href="admin.php?page=' . $this->plugin_slug .
            '"><strong>Configure them!</strong></a></p></div>';
      }
   }

   /**
    * Ajax callback method for those services that don't support directly getting share counts on client
    */
   function get_share_count()
   {
      $settings = $this->getSettings();
      $result = new stdClass();
      try {

         $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';

         if (!wp_verify_nonce($nonce)) {
            $result->error = true;
            $result->message = __('You have taken too long. Please go back and retry.', $this->plugin_slug);
            wp_die(json_encode($result));
         }
         // get service
         $service = isset($_GET['service']) ? $_GET['service'] : '';
         if (empty($service) || strpos($settings['share_services'], $service) === false) {
            $result->error = true;
            $result->message = __('Service not specified.', $this->plugin_slug);
            wp_die(json_encode($result));
         }

         // get key
         $key = isset($_GET['key']) ? $_GET['key'] : '';
         if (empty($key)) {
            $result->error = true;
            $result->message = __('Key not specified.', $this->plugin_slug);
            wp_die(json_encode($result));
         }

         // get url
         if ($key == "page") {
            $url = isset($_GET['url']) ? $_GET['url'] : '';
            if (empty($url)) {
               $result->error = true;
               $result->message = __('Url not specified.', $this->plugin_slug);
               wp_die(json_encode($result));
            }
         } else {
            $url = get_permalink($key);
         }

         include_once(plugin_dir_path(__FILE__) . "services/class-SH_Social_Service.php");
         $class = "SH_$service";

         if (file_exists(plugin_dir_path(__FILE__) . "services/class-$class.php")) {
            $file = include_once(plugin_dir_path(__FILE__) . "services/class-$class.php");

            $service = new $class('share', $settings, '');
            $count = intval($service->shareCount($url, $key));
            $result->count = $count;
            wp_die(json_encode($result));
         }
         exit;
      } catch (Exception $ex) {
         $result->error = true;
         $result->exception = $ex;
         $result->message = $ex . getMessage();
         wp_die(json_encode($result));
      }
   }

   /**
    * Loads all the settings from the database
    */
   function getSettings()
   {
      $settings = get_option($this->plugin_slug);
      $defaults = SH_Crafty_Social_Buttons_Plugin::get_default_settings();

      return wp_parse_args($settings, $defaults);
   }


   /**
    * Get default settings (as an array)
    */
   public static function get_default_settings()
   {
      $settings = array(
         'version' => '1',
         'configured' => false,

         'share_image_set' => 'simple',
         'share_image_size' => 48,
         'share_caption' => 'Share this:',
         'share_services' => 'Facebook,Google,Twitter',
         'show_on_posts' => true,
         'show_on_pages' => false,
         'show_on_home' => false,
         'show_on_static_home' => false,
         'show_on_category' => false,
         'show_on_archive' => false,
         'position' => 'below',
         'share_caption_position' => 'inline-block',
         'share_alignment' => 'left',
         'share_float_buttons' => false,
         'share_float_alignment' => 'right',
         'share_float_height' => '30',
         'share_nofollow' => false,
         'share_hover_effect' => 'hover-none',
         'open_in' => 'new_window',
         'popup' => false,
         'email_body' => 'I thought you might like this: ',
         'twitter_body' => '',
         'twitter_show_title' => true,

         'link_image_set' => 'simple',
         'link_image_size' => 48,
         'link_caption' => 'Find me on:',
         'link_caption_position' => 'inline-block',
         'link_alignment' => 'left',
         'link_services' => 'Facebook,Google,Twitter,Ravelry,Etsy,SpecificFeeds',
         'link_nofollow' => false,
         'link_hover_effect' => 'hover-none',
         'new_window' => true,

         'show_count' => false,
         'cache_share_counts' => false,
         'cache_expiry_minutes' => 5,

         'share_css_classes' => '',
         'link_css_classes' => '',
         'post_types_are_filtered' => false,
         'post_types_for_display' => array()

      );
      return $settings;
   }

}