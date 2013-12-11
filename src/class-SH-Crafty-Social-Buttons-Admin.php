<?php
/**
 * SH_Crafty_Social_Buttons_Admin Class
 * @author 	Sarah Henderson
 * @date	2013-10-12
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SH_Crafty_Social_Buttons_Admin {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 */
	protected $version = '1.0.2';

	/**
	 * Unique identifier for this plugin.
	 */
	protected $plugin_slug = 'crafty-social-buttons';

	/**
	 * Instance of this class.
	 */
	protected static $instance = null;

	/**
	 * Set of available services.
	 */
	protected $all_services = null;

	/**
	 * Set of available image sets.
	 */
	protected $all_image_sets = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 */
	private function __construct() {		

		// register settings
		add_action( 'admin_init', array($this, 'register_settings' ));

		$this->load_all_services();
		$this->load_all_image_sets();
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
	 * Render the settings page for this plugin.
	 */
	public function display_plugin_admin_page() {

		if (!is_admin()) {
			wp_die(__('You cannot access this page'));	
		}
		
		// make sure they have the rights to manage options
		if (!current_user_can( 'manage_options' ) )  {
			wp_die( __('You do not have sufficient permissions to access this page.'));
		}
		 $settings = $this->getSettings();
		
		include_once( plugin_dir_path(__FILE__) . 'views/admin.php' );
	}

	 
	 /**
	  * Checks which classes are available in the services folder and registers them
	  */
	 function load_all_services() {
			
			$this->all_services = array();
			
			$directory = plugin_dir_path(__FILE__) . 'services';
			foreach (scandir($directory) as $file) {
				if ('.' === $file) continue;
				if ('..' === $file) continue;	
				if ("class-SH_Social_Service.php" === $file) { continue; }
				
				$matches = array();
				if (preg_match("/^class-SH_(.+)\.php$/", $file, $matches)) {
					$this->all_services[] = $matches[1];
				}
			}	
	 }
	 
	 /**
	  * Checks which folders are available in the button directory and registers them
	  */
	 function load_all_image_sets() {
			$this->all_image_sets = array();
			
			$directory = plugin_dir_path(__FILE__) . 'buttons';
			foreach (scandir($directory) as $folder) {
				if ('.' === $folder) continue;
				if ('..' === $folder) continue;	
				
				if (!preg_match("/\./", $folder)) { // no dots allowed, only folders
					$this->all_image_sets[] = $folder;
				}
			}	
	 }
	
	/**
	 * Show admin notifications
	 */
	function show_admin_messages() {

		$settings = $this->getSettings();
		
		// check if any buttons have been configured
		if (!$settings['configured']) {
		
			// output a warning that buttons need configuring and provide a link to settings
			echo '<div class="updated fade"><p>Thanks for installing <strong>Crafty Social Buttons!</strong>. '.
				 ' Your buttons need <a href="admin.php?page=' . $this->plugin_slug . 
				 '"><strong>configuration</strong></a> before they will appear.</p></div>';
		}
	}

	/**
	 * Configure admin settings
	 */
	public function register_settings() {
		
		$section = 'cbs_basic_share_settings'; 
		$page = $this->plugin_slug.'-share';
		
		add_settings_section( $section, 'Display Options', 
			array($this, 'displayShareBasicSettingsText'),  $page);  
		
		add_settings_field( 'share_image_set', 'Image Set',  
			array($this, 'renderImageSetSelect'), $page,  $section, array('share_image_set')  );  
		
		add_settings_field( 'share_caption', 'Caption',  
			array($this, 'renderTextbox'), $page,  $section, 
			array('share_caption', 'Displays before the set of share buttons')  );  

		add_settings_field( 'share_services', 'Show these services',  
			array($this, 'render_service_select'), $page,  $section, array('share_services')  );  


		add_settings_field( 'position', 'Above or below content',  
			array($this, 'renderPositionSelect'), $page,  $section, array('position')  );  

		add_settings_field( 'show_on_posts', 'Show on Posts',  
			array($this, 'renderCheckbox'), $page,  $section, 
			array('show_on_posts', 'Shows on post single individual pages') );  
		
		add_settings_field( 'show_on_pages', 'Show on Pages',  
			array($this, 'renderCheckbox'), $page,  $section, array('show_on_pages')  );  
			
		add_settings_field( 'show_on_home', 'Show on Home Page',  
			array($this, 'renderCheckbox'), $page,  $section, array('show_on_home')  );  
		
		
		
		
		
		$section = 'cbs_advanced_share_settings';
		add_settings_section( $section, 'Advanced Options', null,  $page);  
		
		add_settings_field( 'new_window', 'Open in new window',  
			array($this, 'renderCheckbox'), $page,  $section, array('new_window')  );  
		
		add_settings_field( 'show_count', 'Show post counts',  
		array($this, 'renderCheckbox'), $page,  $section,
			array('show_count', 'Only done if service supports it.  Calling out to the 
			services to obtain the counts can slow down the loading of the page significantly')  );  
		
		add_settings_field( 'email_body', 'Email text',  
			array($this, 'renderTextbox'), $page,  $section, array('email_body')  );  
		
		add_settings_field( 'twitter_body', 'Twitter text',  
		array($this, 'renderTextbox'), $page,  $section, array('twitter_body') );  
		
		
	
	
		$section = 'cbs_link_button_settings';
		$page = $this->plugin_slug.'-link';
		add_settings_section( $section, 'Display Options', array($this, 'displayLinkSettingsText'),  $page);  
	 
		add_settings_field( 'link_image_set', 'Image Set',  
			array($this, 'renderImageSetSelect'), $page,  $section, array('link_image_set')  );  
		
		add_settings_field( 'link_caption', 'Caption',  
			array($this, 'renderTextbox'), $page,  $section, 
			array('link_caption', 'Displays before the set of link buttons')  );  
		
		add_settings_field( 'link_services', 'Show these services',  
			array($this, 'render_service_select'), $page,  $section, array('link_services')  );  		
		
		
		$section = 'cbs_link_service_settings';
		add_settings_section( $section, 'User IDs', array($this, 'displayLinkServiceText'),  $page);  
	 
	 	foreach($this->all_services as $service) {
			// we want to add a custom description for some of the fields
			$caption = $service;
			switch ($service) {
				case "Craftsy": $description = 'Hint: www.craftsy.com/user/<strong>user-id</strong>/ (numbers). For more options see Help > Link Buttons (link top right of screen)'; break;
				case "Digg": $description = "Hint: www.digg.com/<strong>user-id</strong>"; break;
				case "Email": $description = "Hint: Your email address"; break;
				case "Etsy": $description = 'Hint: www.etsy.com/shop/<strong>user-id</strong>/'; break;
				case "Facebook": $description = 'Hint: www.facebook.com/<strong>user-id</strong>/'; break;
				case "Google": 
					$description = "Hint: plus.google.com/u/0/<strong>user-id</strong> (it's a long number)"; 
					$caption = "Google Plus"; 
					break;
				case "LinkedIn": $description = "Hint: www.linkedin.com/in/<strong>user-id</strong> or www.linkedin.com/<strong>company/company-id</strong>"; break;
				case "Pinterest": $description = "Hint: www.pinterest.com/<strong>user-id</strong>"; break;
				case "Ravelry": $description = "Hint: www.ravelry.com/people/<strong>user-id</strong>"; break;
				case "Reddit": $description = "Hint: www.reddit.com/user/<strong>user-id</strong>"; break;
				case "StumbleUpon": $description = "Hint: www.stumbleupon/stumbler/<strong>user-id</strong>"; break;
				case "Tumblr": $description = "Hint: http://<strong>user-id</strong>.tumblr.com"; break;
				case "Twitter": $description = "Hint: @<strong>user-id</strong>"; break;
				default: $description = "";
			}
			add_settings_field( 
				$service, 
				$caption,  
				array($this, 'renderTextbox'), 
				$page,  
				$section, 
				array($service, $description) );  
		}
		
		register_setting( $this->plugin_slug, $this->plugin_slug, array($this, 'validate_settings' ));
	}
	
	/**
	 * Display share basic settings section text
	 */
	public function add_contextual_help($hook) {
		$screen = get_current_screen();
		
   	 	if ( $screen->id != 'settings_page_' . $this->plugin_slug ) {
        	return;
		}
	
		$screen->add_help_tab( array(
        'id'	=> 'csb-help-intro',
        'title'	=> __('Welcome'),
        'content'	=> file_get_contents( plugin_dir_path(__FILE__). '/help/intro-tab.php' ) ) );

		$screen->add_help_tab( array(
        'id'	=> 'csb-share-help',
        'title'	=> __('Share Buttons'),
        'content'	=> file_get_contents( plugin_dir_path(__FILE__). '/help/share-tab.php' ) ) );
		  
		$screen->add_help_tab( array(
        'id'	=> 'csb-link-help',
        'title'	=> __('Link Buttons'),
        'content'	=> file_get_contents( plugin_dir_path(__FILE__). '/help/link-tab.php' ) ) );
				
		 $screen->add_help_tab( array(
        'id'	=> 'csb-widget',
        'title'	=> __('Widget'),
        'content'	=> file_get_contents( plugin_dir_path(__FILE__). '/help/widget-tab.php' ) ) );

		 $screen->add_help_tab( array(
        'id'	=> 'csb-shortcode',
        'title'	=> __('Shortcodes'),
        'content'	=> file_get_contents( plugin_dir_path(__FILE__). '/help/shortcode-tab.php' ) ) );

		 $screen->add_help_tab( array(
        'id'	=> 'csb-action-hooks',
        'title'	=> __('Action Hooks'),
        'content'	=> file_get_contents( plugin_dir_path(__FILE__). '/help/action-hook-tab.php' ) ) );

		 $screen->add_help_tab( array(
        'id'	=> 'csb-icons',
        'title'	=> __('Adding Icons'),
        'content'	=> file_get_contents( plugin_dir_path(__FILE__). '/help/adding-icons-tab.php' ) ) );

		 $screen->add_help_tab( array(
        'id'	=> 'csb-credits',
        'title'	=> __('Credits'),
        'content'	=> file_get_contents( plugin_dir_path(__FILE__). '/help/credits-tab.php' ) ) );

		 $screen->add_help_tab( array(
        'id'	=> 'csb-about',
        'title'	=> __('About Me'),
        'content'	=> file_get_contents( plugin_dir_path(__FILE__). '/help/about-me-tab.php' ) ) );
	 }
	
	/**
	 * Display share basic settings section text
	 */
	public function displayShareBasicSettingsText() { ?>
		<p>Share Buttons will prompt the user to share the current post or page URL.</p>
		<?php 
	}
	
	/**
	 * Display link settings section text
	 */
	public function displayLinkSettingsText() {?>
		<p>Link Buttons will link to your user profile on each site.  
        Enter your <strong>user id</strong> for each service you choose below to make the button link directly to your profile.</p>
		<?php
 	}
	
	/**
	 * Display link settings section text
	 */
	public function displayLinkServiceText() {?>
		<p>Enter just your <strong>user id</strong> for each service, not the full URL. The bit in bold that says <strong>user-id</strong> in the hint is the part you should enter.</p>
		<?php
 	}
	
	/**
	 * Display a settings checkbox
	 */
	public function renderCheckbox($args) {
		$id = $args[0];
		$name = $this->plugin_slug . '[' . $args[0] . ']';
		$description = isset($args[1]) ? $args[1] : '';
		$settings = $this->getSettings();
		$value = $settings[$id];
		?>

            <input type="checkbox" id="<?=$id?>" name="<?=$name?>" <?php echo checked(1, $value) ?> value="0" />
            <span class="description" for="<?=$id?>">
                <?=$description?>
            </span>

        <?php 
	}
	
	/**
	 * Display a settings textbox
	 */
	public function renderTextbox($args) {
		$id = $args[0];
		$settings = $this->getSettings();
		$value = isset($settings[$id]) ? $settings[$id] : ' ';
		$name = $this->plugin_slug . '[' . $args[0] . ']';
		$description = isset($args[1]) ? $args[1] : '';
		
		?>

            <input type="text" id="<?=$id?>" name="<?=$name?>" value="<?=$value?>" />
            <span class="description">
 	           <?=$description?>
            </span>
            
		<?php 
	}
	
	/**
	 * Display share settings section
	 */
	public function renderImageSetSelect($args) {
		$id = $args[0];
		$name = $this->plugin_slug . '[' . $args[0] . ']';
		$settings = $this->getSettings();
		$value = $settings[$id];
		$base = plugin_dir_url(__FILE__) . "buttons/";
		$path = $base . $value . "/";
		?>
        
		<select id="<?=$id?>" class="csb-image-set" name="<?=$name?>">
        
    		<?php foreach ($this->all_image_sets as $set) { ?>
    		<option value="<?=$set?>" <?php echo selected($set, $value);?>">
    			<?=$set?>
    		</option>
    	
		<?php } ?>

        </select>

<?php 
}
	
	/**
	 * Display share settings section
	 */
	public function render_service_select($args) {
		$id = $args[0];
		$name = $this->plugin_slug . '[' . $args[0] . ']';
		$settings = $this->getSettings();
		$image_set = ($id == 'link_services') ? $settings['link_image_set'] : $settings['share_image_set'];
		$value = $settings[$id];
		?>
        
            <div class="csb-services">

                <div class="csb-include-list chosen">
                    <div><span class="include-heading">Selected</span> (these will be displayed)</div>
                    <ul id="csbsort2" class="connectedSortable data-base="<?=$image_set?>"">
                        <?php echo $this->get_selected_services_html($value, $image_set); ?>
                    </ul>
                    <input type="hidden" name="<?=$name?>" id="<?=$id?>" class="csb-services" />
                </div>

                <div class="csb-include-list available">
                    <div><span class="include-heading">Available</span> (these will <strong>not</strong> be displayed)</div>
                    <ul id="csbsort1" class="connectedSortable">
                        <?php echo $this->get_available_services_html($value, $image_set); ?>
                    </ul>
                    </center>
                </div>
            </div>
            <?php
	}
	
	/**
	 * Display share settings section
	 */
	public function renderPositionSelect($args) {
		$id = $args[0];
		$name = $this->plugin_slug . '[' . $args[0] . ']';
		$settings = $this->getSettings();
		$value = $settings[$id];
		?>
        
            <select id="<?=$id?>" name="<?=$name?>">
                <option value="above" <?php echo selected('above',$value);?> >Above</option>
                <option value="below" <?php echo selected('below',$value);?> >Below</option>
                <option value="both" <?php echo selected('both',$value);?> >Both</option>
            </select>
        
        <?php 
	}
	
	/**
	 * Validate our saved settings
	 */
	public function validate_settings($input) {
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
			$settings['show_count'] = isset($input['show_count']);
			$settings['new_window'] = isset($input['new_window']);
			
			// our select boxes have constrained UI, so just update them
			$settings['share_image_set'] = isset($input['share_image_set']) ? $input['share_image_set'] : 'simple';
			$settings['share_services'] = isset($input['share_services']) ? $input['share_services'] : '';
			$settings['position'] = isset($input['position']) ? $input['position'] : 'below';
			
			// and finally, validate our text
			$settings['share_caption'] = sanitize_text_field ($input['share_caption']);
			$settings['email_body'] = sanitize_text_field ($input['email_body']);
			$settings['twitter_body'] = sanitize_text_field ($input['twitter_body']);
			
	
		} else if ('link_options' == $tab) {

			// our select boxes have constrained UI, so just update them
			$settings['link_image_set'] = $input['link_image_set'];
			$settings['link_services'] = $input['link_services'];
			
			// and finally, validate our text boxes
			$settings['link_caption'] = sanitize_text_field ($input['link_caption']);
			
			// and the textboxes for all our services
			foreach($this->all_services as $service) {
				$settings[$service] = sanitize_text_field (stripslashes_deep($input[$service]));
			}

		}
		return $settings; 
	}
		
	/**
	 * Get list item HTML for selected services from our text list
	 */
	function get_selected_services_html($selectedServicesString, $image_set) {

		$htmlListItems = '';	
		if ($selectedServicesString != '') {
		
			$selectedServices = explode(',', $selectedServicesString); // explode string to array
			foreach ($selectedServices as $service) {
				$url = plugin_dir_url(__FILE__) . "buttons/" . $image_set . "/" . $service . ".png";
				$htmlListItems .= $this->get_service_icon_html($url, $service, $image_set);
			}
		}
		return $htmlListItems;
	}
	
	/**
	 * Get list item HTML for all services EXCEPT those already selected
	 */
	function get_available_services_html($selectedServicesString, $image_set) {
	
		$htmlListItems = '';	
		$selectedServices = array();
		if ($selectedServicesString != '') {	
			$selectedServices = explode(',', $selectedServicesString); // explode string to array
		}
		
		foreach ($this->all_services as $service) {
			if (!in_array($service, $selectedServices)) {		
				$url = plugin_dir_url(__FILE__) . "buttons/" . $image_set . "/" . $service . ".png";
				$htmlListItems .= $this->get_service_icon_html($url, $service, $image_set);
			}
		}
		
		return $htmlListItems;
	}
	
	
	/**
	 * Get html for a single service icon for selection on the admin page
	 */
	function get_service_icon_html($url, $service, $image_set) {
		return '<li id="' . $service
				.'"><img src="' . strtolower($url) 
				. '" data-image-set="' . strtolower($image_set)
				. '" alt="' . $service . '" width="48" height="48" /></li>';

	}
	
	/**
	 * Loads all the settings from the database
	 */	
	function getSettings() {
		$settings = get_option($this->plugin_slug);
		return $settings;
	}	

}
?>
