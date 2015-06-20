<?php
/**
 * SH_Crafty_Social_Buttons_Admin Class
 * @author    Sarah Henderson
 * @date    2013-10-12
 */

if (!defined('ABSPATH')) {
   exit;
} // Exit if accessed directly

class SH_Crafty_Social_Buttons_Admin
{

   /**
    * Unique identifier for this plugin.
    */
   protected $plugin_slug = 'crafty-social-buttons';

   /**
    * Instance of this class.
    */
   protected static $instance = null;

   /**
    * Field renderer
    */
   protected $renderer = null;

   /**
    * Initialize the plugin by setting localization, filters, and administration functions.
    */
   private function __construct()
   {

      // register settings
      add_action('admin_init', array($this, 'register_settings'));

      $this->renderer = SH_Crafty_Social_Buttons_Admin_Fields::get_instance();

   }

   /**
    * Render the settings page for this plugin.
    */
   public function display_plugin_admin_page()
   {

      if (!is_admin()) {
         wp_die(__('You cannot access this page'));
      }

      // make sure they have the rights to manage options
      if (!current_user_can('manage_options')) {
         wp_die(__('You do not have sufficient permissions to access this page.'));
      }
      $settings = $this->getSettings();

      include_once(plugin_dir_path(__FILE__) . 'views/admin.php');
   }



   /**
    * Configure admin settings
    */
   public function register_settings()
   {

      $this->register_share_settings();

      $this->register_link_settings();

      $this->register_share_count_settings();

      $this->register_advanced_settings();

      register_setting($this->plugin_slug, $this->plugin_slug, array($this, 'validate_settings'));
   }


   /**
    * Validate our saved settings
    */
   public function validate_settings($input)
   {
      $settings = $this->getSettings();

      //mark it as configured forevermore
      $settings['configured'] = true;

      // find out which tab we are on, otherwise we'll mess up the checkbox values for the other tab
      // the other data will be fine, but any checkboxes will be unset
      $tab = $input['tab'];

      if ('share_options' == $tab) {
         // first, all the checkboxes need to be set if present
         $settings['show_on_posts'] = isset($input['show_on_posts']);
         $settings['show_on_pages'] = isset($input['show_on_pages']);
         $settings['show_on_home'] = isset($input['show_on_home']);
         $settings['show_on_static_home'] = isset($input['show_on_static_home']);
         $settings['show_on_category'] = isset($input['show_on_category']);
         $settings['show_on_archive'] = isset($input['show_on_archive']);
         $settings['share_float_buttons'] = isset($input['share_float_buttons']);
         $settings['twitter_show_title'] = isset($input['twitter_show_title']);

         // parse out our radio buttons, they are constrained so just take the values
         $settings['open_in'] = $input['open_in'];
         $settings['popup'] = $input['open_in'] == 'popup';
         $settings['share_hover_effect'] = $input['share_hover_effect'];

         // our select boxes have constrained UI, so just update them
         $settings['share_image_set'] = isset($input['share_image_set']) ? $input['share_image_set'] : 'simple';
         $settings['share_services'] = isset($input['share_services']) ? $input['share_services'] : '';
         $settings['position'] = isset($input['position']) ? $input['position'] : 'below';
         $settings['share_alignment'] = isset($input['share_alignment']) ? $input['share_alignment'] : 'left';
         $settings['share_float_alignment'] = isset($input['share_float_alignment']) ? $input['share_float_alignment'] : 'left';
         $settings['share_float_height'] = isset($input['share_float_height']) ? $input['share_float_height'] : '20';
         $settings['share_caption_position'] = isset($input['share_caption_position']) ? $input['share_caption_position'] : 'inline-block';

         // and finally, validate our text boxes
         $settings['share_caption'] = sanitize_text_field($input['share_caption']);
         $settings['email_body'] = sanitize_text_field($input['email_body']);
         $settings['twitter_body'] = sanitize_text_field($input['twitter_body']);

         // including numeric ones
         $settings['share_image_size'] = $this->sanitize_image_size($input['share_image_size']);


      } else if ('link_options' == $tab) {

         // check if checkboxes are set
         $settings['new_window'] = isset($input['new_window']);

         // our select boxes have constrained UI, so just update them
         $settings['link_image_set'] = $input['link_image_set'];
         $settings['link_services'] = $input['link_services'];
         $settings['link_alignment'] = isset($input['link_alignment']) ? $input['link_alignment'] : 'left';
         $settings['link_caption_position'] = isset($input['link_caption_position']) ? $input['link_caption_position'] : 'inline-block';
         $settings['link_hover_effect'] = $input['link_hover_effect'];

         // and finally, validate our text boxes
         $settings['link_caption'] = sanitize_text_field($input['link_caption']);

         // including numeric ones
         $settings['link_image_size'] = $this->sanitize_image_size($input['link_image_size']);

         // and the textboxes for all our services
         foreach ($this->renderer->all_services as $service) {
            $settings[$service] = sanitize_text_field(stripslashes_deep($input[$service]));
         }

      } else if ('share_count_options' == $tab) {

         // first, all the checkboxes need to be set if present
         $settings['show_count'] = isset($input['show_count']);
         $settings['cache_share_counts'] = isset($input['cache_share_counts']);

         // including numeric ones
         $settings['cache_expiry_minutes'] = $this->sanitize_cache_expiry( $input['cache_expiry_minutes'] );

      } else if ('advanced_options' == $tab) {

          // first, all the checkboxes need to be set if present
          $settings['post_types_are_filtered'] = isset($input['post_types_are_filtered']);
          $settings['share_nofollow'] = isset($input['share_nofollow']);
          $settings['link_nofollow'] = isset($input['link_nofollow']);

          // our checkboxes have constrained UI, so just update them
          $settings['post_types_for_display'] = $input['post_types_for_display'];

          // and finally, sanitize our text boxes
          $settings['share_css_classes'] = sanitize_text_field($input['share_css_classes']);
          $settings['link_css_classes'] = sanitize_text_field($input['link_css_classes']);


      }

    return $settings;
   }

   function sanitize_image_size($image_size_string)
   {
      $size = sanitize_text_field($image_size_string);
      if (!is_numeric($size)) {
         return 48;
      }
      if ($size < 24) {
         return 24;
      }
      if ($size > 64) {
         return 64;
      }

      return $size;
   }

   function sanitize_cache_expiry($cache_expiry_string)
   {
      $minutes = sanitize_text_field($cache_expiry_string);
      if (!is_numeric($minutes)) {
         return 30;
      }
      if ($minutes < 1) {
         return 1;
      }
      if ($minutes > 180) {
         return 180;
      }

      return $minutes;
   }

   /**
    * Calls a static method on the given service and returns the result
    */
   function call_service_method($service, $method)
   {
      return call_user_func(array('SH_' . $service, $method));
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

   /** Registers the settings on the Share Options page
    */
   private function register_share_settings()
   {
      $section = 'cbs_basic_share_settings';
      $page = $this->plugin_slug . '-share';

      add_settings_section($section, __('Basic Options', $this->plugin_slug),
         array($this, 'displayShareBasicSettingsText'), $page);

      add_settings_field('share_image_set', __('Image Set', $this->plugin_slug),
         array($this->renderer, 'renderImageSetSelect'), $page, $section, array('share_image_set'));

      add_settings_field('share_image_size', __('Image Size', $this->plugin_slug),
         array($this->renderer, 'renderNumericTextbox'), $page, $section,
         array('share_image_size', __('Size in pixels, between 24 and 64', $this->plugin_slug)));

      add_settings_field('share_caption', __('Caption', $this->plugin_slug),
         array($this->renderer, 'renderTextbox'), $page, $section,
         array('share_caption', __('Displays before the set of share buttons', $this->plugin_slug)));

      add_settings_field('share_caption_position', __('Caption Position', $this->plugin_slug),
         array($this->renderer, 'renderCaptionPositionSelect'), $page, $section, array('share_caption_position'));

      add_settings_field('share_services', __('Show these services', $this->plugin_slug),
         array($this->renderer, 'render_service_select'), $page, $section, array('share_services'));

      add_settings_field('position', __('Position', $this->plugin_slug),
         array($this->renderer, 'renderPositionSelect'), $page, $section, array('position'));

      add_settings_field('share_alignment', __('Button Alignment', $this->plugin_slug),
         array($this->renderer, 'renderAlignmentSelect'), $page, $section, array('share_alignment'));

      add_settings_field('share_hover_effect', __('Hover Effect', $this->plugin_slug),
           array($this->renderer, 'renderRadio'), $page, $section,
           array('share_hover_effect', '',
               array(
                   'hover-none' => __('No effect', $this->plugin_slug),
                   'hover-dim' => __('Dim on hover', $this->plugin_slug),
                   'hover-brighten' => __('Brighten on hover', $this->plugin_slug))
           ));

	  $section = 'cbs_display_share_float_settings';
	  add_settings_section($section, __('Floating Button Options', $this->plugin_slug), null, $page);

	  add_settings_field('share_float_buttons', __('Float Share Buttons', $this->plugin_slug),
		   array($this->renderer, 'renderCheckbox'), $page, $section,
		   array('share_float_buttons', __('Make share buttons float on single post pages', $this->plugin_slug)));

      add_settings_field('share_float_alignment', __('Float Alignment', $this->plugin_slug),
	    array($this->renderer, 'renderFloatAlignmentSelect'), $page, $section, array('share_float_alignment'));

	  add_settings_field('share_float_height', __('Float Height', $this->plugin_slug),
		   array($this->renderer, 'renderFloatHeightSelect'), $page, $section, array('share_float_height'));

      $section = 'cbs_display_share_settings';
      add_settings_section($section, __('Display Options', $this->plugin_slug), null, $page);

      add_settings_field('show_on_posts', __('Show on Posts', $this->plugin_slug),
         array($this->renderer, 'renderCheckbox'), $page, $section,
         array('show_on_posts', __('Shares a single post', $this->plugin_slug)));

      add_settings_field('show_on_home', __('Show on Home Page', $this->plugin_slug),
         array($this->renderer, 'renderCheckbox'), $page, $section,
         array('show_on_home', __('Shares each post on home page', $this->plugin_slug)));

      add_settings_field('show_on_category', __('Show on Category Pages', $this->plugin_slug),
         array($this->renderer, 'renderCheckbox'), $page, $section,
         array('show_on_category', __('Shares each post on category pages', $this->plugin_slug)));

      add_settings_field('show_on_archive', __('Show on Archive Pages', $this->plugin_slug),
         array($this->renderer, 'renderCheckbox'), $page, $section,
         array('show_on_archive', __('Shares each post on archive and tags pages', $this->plugin_slug)));

      add_settings_field('show_on_pages', __('Show on Pages', $this->plugin_slug),
         array($this->renderer, 'renderCheckbox'), $page, $section,
         array('show_on_pages', __('Shares the page', $this->plugin_slug)));

      add_settings_field('show_on_static_home', __('Show on Static Front Page', $this->plugin_slug),
         array($this->renderer, 'renderCheckbox'), $page, $section,
         array('show_on_static_home', __('Shares the home page if you have a static front page', $this->plugin_slug)));

      $section = 'cbs_advanced_share_settings';
      add_settings_section($section, __('Advanced Options', $this->plugin_slug), null, $page);

      add_settings_field('open_in', __('Open in', $this->plugin_slug),
         array($this->renderer, 'renderRadio'), $page, $section,
         array('open_in', '',
            array(
               'new_window' => __('New Window', $this->plugin_slug),
               'same_window' => __('Same Window', $this->plugin_slug),
               'popup' => __('Popup', $this->plugin_slug))
         ));

      add_settings_field('email_body', __('Email text', $this->plugin_slug),
         array($this->renderer, 'renderTextbox'), $page, $section,
         array('email_body', __('Default Email text (user can override this)', $this->plugin_slug)));


      add_settings_field('twitter_body', __('Tweet text', $this->plugin_slug),
         array($this->renderer, 'renderTextbox'), $page, $section,
         array('twitter_body', __('Default Tweet text (user can override this)', $this->plugin_slug)));

      add_settings_field('twitter_show_title', __('Title in Tweet text', $this->plugin_slug),
         array($this->renderer, 'renderCheckbox'), $page, $section,
         array(
            'twitter_show_title',
            __('Include the post/page title in the default Tweet text', $this->plugin_slug)
         ));
   }

   private function register_link_settings()
   {
      $section = 'cbs_link_button_settings';
      $page = $this->plugin_slug . '-link';

      add_settings_section($section, __('Display Options', $this->plugin_slug),
         array($this, 'displayLinkSettingsText'), $page);

      add_settings_field('link_image_set', __('Image Set', $this->plugin_slug),
         array($this->renderer, 'renderImageSetSelect'), $page, $section, array('link_image_set'));

      add_settings_field('link_image_size', __('Image Size', $this->plugin_slug),
         array($this->renderer, 'renderNumericTextbox'), $page, $section,
         array('link_image_size', __('Size in pixels, between 24 and 64', $this->plugin_slug)));

      add_settings_field('link_caption', __('Caption', $this->plugin_slug),
         array($this->renderer, 'renderTextbox'), $page, $section,
         array('link_caption', __('Displays before the set of link buttons', $this->plugin_slug)));

      add_settings_field('link_caption_position', __('Caption Position', $this->plugin_slug),
         array($this->renderer, 'renderCaptionPositionSelect'), $page, $section, array('link_caption_position'));

      add_settings_field('link_services', __('Show these services', $this->plugin_slug),
         array($this->renderer, 'render_service_select'), $page, $section, array('link_services'));

      add_settings_field('link_alignment', __('Alignment', $this->plugin_slug),
         array($this->renderer, 'renderAlignmentSelect'), $page, $section, array('link_alignment'));

      add_settings_field('new_window', __('Open in new window', $this->plugin_slug),
         array($this->renderer, 'renderCheckbox'), $page, $section, array('new_window'));


      add_settings_field('link_hover_effect', __('Hover Effect', $this->plugin_slug),
           array($this->renderer, 'renderRadio'), $page, $section,
           array('link_hover_effect', '',
               array(
                   'hover-none' => __('No effect', $this->plugin_slug),
                   'hover-dim' => __('Dim on hover', $this->plugin_slug),
                   'hover-brighten' => __('Brighten on hover', $this->plugin_slug))
           ));


      $section = 'cbs_link_service_settings';
      add_settings_section($section, __('User IDs', $this->plugin_slug), array(
         $this,
         'displayLinkServiceText'
      ), $page);

      foreach ($this->renderer->all_services as $service) {
         // we want to add a custom description for some of the fields
         $caption = $service;
         $description = "";

          $canLink = $this->call_service_method($service, 'canLink');
         $description = $this->call_service_method($service, 'description');

          if ($canLink) {
             add_settings_field(
                $service,
                $caption,
                array($this->renderer, 'renderTextbox'),
                $page,
                $section,
                array($service, $description));
         }
      }


   }

   /** Registers the settings on the Share Options page
    */
   private function register_share_count_settings()
   {
      $section = 'cbs_share_count_settings';
      $page = $this->plugin_slug . '-share-counts';

      add_settings_section($section, __('Display Options', $this->plugin_slug),  null, $page);

      add_settings_field('show_count', __('Show share counts', $this->plugin_slug),
         array($this->renderer, 'renderCheckbox'), $page, $section,
         array('show_count', __('Counts are displayed only for services that provide the count data.', $this->plugin_slug)));

      $section = 'cbs_share_count_caching_settings';

      add_settings_section($section, __('Caching Options', $this->plugin_slug), array($this, 'displayCacheSettingsText'), $page);

      add_settings_field('cache_share_counts', __('Cache share counts', $this->plugin_slug),
         array($this->renderer, 'renderCheckbox'), $page, $section,
         array('cache_share_counts', __('Return cached share count values.', $this->plugin_slug)));


      add_settings_field('cache_expiry_minutes', __('Cache expiry', $this->plugin_slug),
         array($this->renderer, 'renderNumericTextbox'), $page, $section,
         array('cache_expiry_minutes', __('Number of minutes to remember share counts (between 1 and 180)', $this->plugin_slug), 1, 180));

   }

    /** Registers the settings on the Share Options page
     */
    private function register_advanced_settings()
    {
        $section = 'cbs_advanced_settings';
        $page = $this->plugin_slug . '-advanced';

        add_settings_section($section, __('', $this->plugin_slug),
            array($this, 'displayAdvancedSettingsText'), $page);

        $section = 'cbs_advanced_settings_css';
        add_settings_section($section, __('Extra CSS Classes', $this->plugin_slug),
            array($this, 'displayExtraCssSettingsText'), $page);

        add_settings_field('share_css_classes', __('Share Button CSS Classes', $this->plugin_slug),
            array($this->renderer, 'renderTextbox'), $page, $section,
            array('share_css_classes', __('Add css classes, separated by spaces.  These will be added to the block of Share buttons', $this->plugin_slug)));

        add_settings_field('link_css_classes', __('Link Button CSS Classes', $this->plugin_slug),
            array($this->renderer, 'renderTextbox'), $page, $section,
            array('link_css_classes', __('Add css classes, separated by spaces.  These will be added to the block of Link buttons', $this->plugin_slug)));

        $section = 'cbs_advanced_settings_nofollow';
        add_settings_section($section, __('Nofollow attributes', $this->plugin_slug),
            array($this, 'displayNoFollowSettingsText'), $page);


        add_settings_field('share_nofollow', __('Share Buttons', $this->plugin_slug),
            array($this->renderer, 'renderCheckbox'), $page, $section,
            array('share_nofollow', __('Add <code>rel="nofollow"</code> to share buttons', $this->plugin_slug)));

        add_settings_field('link_nofollow', __('Link Buttons', $this->plugin_slug),
            array($this->renderer, 'renderCheckbox'), $page, $section,
            array('link_nofollow', __('Add <code>rel="nofollow"</code> to link buttons', $this->plugin_slug)));

        $section = 'cbs_advanced_settings_post_types';
        add_settings_section($section, __('Post Types', $this->plugin_slug),
            array($this, 'displayPostTypeSettingsText'), $page);


        add_settings_field('post_types_are_filtered', __('Post Type Filtering', $this->plugin_slug),
            array($this->renderer, 'renderCheckbox'), $page, $section,
            array('post_types_are_filtered', __('Enable post type filtering', $this->plugin_slug)));

        add_settings_field('post_types_for_display', __('Selected Post Types', $this->plugin_slug),
            array($this->renderer, 'renderPostTypeList'), $page, $section,
            array('post_types_for_display', __('If filtering is enabled, Share buttons will only be shown on the post types you select', $this->plugin_slug)));


    }

   /**
    * Display share basic settings section text
    */
   public function displayShareBasicSettingsText()
   {
      echo '<p>';
      _e('Share Buttons will prompt the user to share the current post or page URL.', $this->plugin_slug);
      echo '</p>';

   }

   /**
    * Display link settings section text
    */
   public function displayLinkSettingsText()
   {
      echo '<p>';
      _e('Link Buttons will link to your user profile on each site.  Add them to the theme using the Crafty Social Buttons <strong>widget</strong>.', $this->plugin_slug);
      echo '</p><p>';
      _e('Enter your <strong>user id</strong> for each service you choose below to make the button link directly to your profile.', $this->plugin_slug);
      echo '</p>';
   }

   /**
    * Display link settings section text
    */
   public function displayLinkServiceText()
   {
      echo '<p>';
      _e('<strong>Option 1</strong>: Enter just your <strong>user id</strong> for each service, and the link url will be generated for you.
			The bit in bold that says <strong>user-id</strong> in the hint is the part you should enter.', $this->plugin_slug);
      echo '</p>';
      echo '<p>';
      _e('<strong>Option 2</strong>: Enter the url you want to link to, starting with http or https.
            This will make the button link directly to that url.', $this->plugin_slug);
      echo '</p>';
   }

   /**
    * Display link settings section text
    */
   public function displayCacheSettingsText()
   {
      echo '<p>';
      _e('Normally the latest share counts will be fetched every single time a post is displayed.   Turning on caching will remember and redisplay the last
      count instead of requesting it each time.', $this->plugin_slug);
      echo '</p>';
      echo '<p>';
      _e('This will reduce the number of requests to the count service, but means the counts will appear not to increase during the cache interval. You can cache the post counts for between 1 minute and 180 minutes (3 hours).', $this->plugin_slug);
      echo '</p>';
   }

    public function displayAdvancedSettingsText()
    {
        echo '<p>';
        _e('Most users will not need to change any settings on this page.', $this->plugin_slug);
        echo '</p>';
    }

    public function displayExtraCssSettingsText()
    {
        echo '<p>';
        _e('You may need to add extra styles to work with other libraries that define their own classes.', $this->plugin_slug);
        echo '</p>';
    }

    public function displayNoFollowSettingsText()
    {
        echo '<p>';
        _e('These settings let you add <code>rel="nofollow"</code> attributes to the share and link button hyperlinks.', $this->plugin_slug);
        echo '</p>';
    }

    public function displayPostTypeSettingsText()
    {
        echo '<p>';
        _e('By default, share buttons will be added to all post types.  If you want the buttons to appear only on certain posts types, you can configure them here.', $this->plugin_slug);
        echo '</p>';
        echo '<p>';
        _e('This setting will be most useful if you are using custom post types that aren&apos;t suitable for sharing, or if you are using attachment pages and don&apos;t want them shared.', $this->plugin_slug);
        echo '</p>';
    }

   /**
    * Display share basic settings section text
    * @param $hook
    */
   public function add_contextual_help($hook)
   {
      $screen = get_current_screen();

      if ($screen->id != 'settings_page_' . $this->plugin_slug) {
         return;
      }

      $screen->add_help_tab(array(
         'id' => 'csb-help-intro',
         'title' => __('Welcome', $this->plugin_slug),
         'content' => file_get_contents(plugin_dir_path(__FILE__) . '/help/intro-tab.php')
      ));

      $screen->add_help_tab(array(
         'id' => 'csb-share-help',
         'title' => __('Share Buttons', $this->plugin_slug),
         'content' => file_get_contents(plugin_dir_path(__FILE__) . '/help/share-tab.php')
      ));

      $screen->add_help_tab(array(
         'id' => 'csb-link-help',
         'title' => __('Link Buttons', $this->plugin_slug),
         'content' => file_get_contents(plugin_dir_path(__FILE__) . '/help/link-tab.php')
      ));

      $screen->add_help_tab(array(
         'id' => 'csb-share-count-help',
         'title' => __('Share Count Options', $this->plugin_slug),
         'content' => file_get_contents(plugin_dir_path(__FILE__) . '/help/share-count-tab.php')
      ));


      $screen->add_help_tab(array(
         'id' => 'csb-advanced-help',
         'title' => __('Advanced Options', $this->plugin_slug),
         'content' => file_get_contents(plugin_dir_path(__FILE__) . '/help/advanced-tab.php')
      ));

      $screen->add_help_tab(array(
         'id' => 'csb-widget',
         'title' => __('Widget', $this->plugin_slug),
         'content' => file_get_contents(plugin_dir_path(__FILE__) . '/help/widget-tab.php')
      ));

      $screen->add_help_tab(array(
         'id' => 'csb-shortcode',
         'title' => __('Shortcodes', $this->plugin_slug),
         'content' => file_get_contents(plugin_dir_path(__FILE__) . '/help/shortcode-tab.php')
      ));

      $screen->add_help_tab(array(
         'id' => 'csb-action-hooks',
         'title' => __('Action Hooks', $this->plugin_slug),
         'content' => file_get_contents(plugin_dir_path(__FILE__) . '/help/action-hook-tab.php')
      ));

      $screen->add_help_tab(array(
         'id' => 'csb-icons',
         'title' => __('Adding Icons', $this->plugin_slug),
         'content' => file_get_contents(plugin_dir_path(__FILE__) . '/help/adding-icons-tab.php')
      ));

      $screen->add_help_tab(array(
         'id' => 'csb-credits',
         'title' => __('Credits', $this->plugin_slug),
         'content' => file_get_contents(plugin_dir_path(__FILE__) . '/help/credits-tab.php')
      ));

      $screen->add_help_tab(array(
         'id' => 'csb-about',
         'title' => __('About Me', $this->plugin_slug),
         'content' => file_get_contents(plugin_dir_path(__FILE__) . '/help/about-me-tab.php')
      ));
   }



}