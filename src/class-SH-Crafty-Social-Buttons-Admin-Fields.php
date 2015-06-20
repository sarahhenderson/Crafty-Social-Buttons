<?php
/**
 * SH_Crafty_Social_Buttons_Admin_Fields Class
 * @author    Sarah Henderson
 * @date    2014-12-15
 */

if (!defined('ABSPATH')) {
   exit;
} // Exit if accessed directly

class SH_Crafty_Social_Buttons_Admin_Fields
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
    * Set of available services.
    */
   public $all_services = null;

   /**
    * Set of available image sets.
    */
   protected $all_image_sets = null;

   /**
    * Initialize the plugin by setting localization, filters, and administration functions.
    */
   private function __construct()
   {
      $this->load_all_services();
      $this->load_all_image_sets();
   }


   /**
    * Checks which classes are available in the services folder and registers them
    */
   function load_all_services()
   {

      include_once('services/class-SH_Social_Service.php');
      $this->all_services = array();

      $directory = plugin_dir_path(__FILE__) . 'services';
      foreach (scandir($directory) as $file) {
         if ('.' === $file) {
            continue;
         }
         if ('..' === $file) {
            continue;
         }
         if ("class-SH_Social_Service.php" === $file) {
            continue;
         }

         $matches = array();
         if (preg_match("/^class-SH_(.+)\.php$/", $file, $matches)) {
            $this->all_services[] = $matches[1];
            include_once('services/' . $file);
         }
      }
   }

   /**
    * Checks which folders are available in the button directory and registers them
    */
   function load_all_image_sets()
   {
      $this->all_image_sets = array();

      $directory = plugin_dir_path(__FILE__) . 'buttons'; // included image sets
      foreach (scandir($directory) as $folder) {
         if ('.' === $folder) {
            continue;
         }
         if ('..' === $folder) {
            continue;
         }

         if (!preg_match("/\./", $folder)) { // no dots allowed, only folders
            $this->all_image_sets[] = $folder;
         }
      }

      $upload_dir = wp_upload_dir();
      $directory = $upload_dir['basedir'] . '/crafty-social-buttons/buttons'; // custom image sets
      if (is_dir($directory)) {
         foreach (scandir($directory) as $folder) {
            if ('.' === $folder) {
               continue;
            }
            if ('..' === $folder) {
               continue;
            }

            if (!preg_match("/\./", $folder)) { // no dots allowed, only folders
               $this->all_image_sets[] = $folder;
            }
         }
      }

   }


   /**
    * Display a settings checkbox
    */
   public function renderCheckbox($args)
   {
      $id = $args[0];
      $name = $this->plugin_slug . '[' . $args[0] . ']';
      $description = isset($args[1]) ? $args[1] : '';
      $settings = $this->getSettings();
      $value = $settings[$id];
      ?>

      <input type="checkbox" id="<?php echo $id ?>" name="<?php echo $name ?>" <?php echo checked(1, $value) ?>
             value="0"/>
      <span class="description" for="<?php echo $id ?>">
                <?php echo $description ?>
            </span>

   <?php
   }

   /**
    * Display a set of radio buttons
    */
   public function renderRadio($args)
   {
      $id = $args[0];
      $name = $this->plugin_slug . '[' . $args[0] . ']';
      $description = isset($args[1]) ? $args[1] : '';
      $options = isset($args[2]) ? $args[2] : array();
      $settings = $this->getSettings();
      $value = $settings[$id];
      ?>

      <?php foreach ($options as $key => $label) { ?>
        <label>
       <input type="radio" id="<?php echo $id ?>" name="<?php echo $name ?>" <?php echo checked($key, $value); ?>
             value="<?php echo $key ?>"/> <?php echo $label; ?> &nbsp; </label>
   <?php } ?>
      <span class="description" for="<?php echo $id ?>">
                <?php echo $description ?>
            </span>

   <?php
   }

   /**
    * Display a settings textbox
    */
   public function renderTextbox($args)
   {
      $id = $args[0];
      $settings = $this->getSettings();
      $value = isset($settings[$id]) ? $settings[$id] : ' ';
      $name = $this->plugin_slug . '[' . $args[0] . ']';
      $description = isset($args[1]) ? $args[1] : '';

      ?>

      <input type="text" id="<?php echo $id ?>" name="<?php echo $name ?>" value="<?php echo $value ?>"/>
      <span class="description">
 	           <?php echo $description ?>
            </span>

   <?php
   }

   /**
    * Display a settings textbox
    */
   public function renderNumericTextbox($args)
   {
      $id = $args[0];
      $settings = $this->getSettings();
      $value = isset($settings[$id]) ? $settings[$id] : ' ';
      $name = $this->plugin_slug . '[' . $args[0] . ']';
      $description = isset($args[1]) ? $args[1] : '';
      $min = isset($args[2]) ? $args[2] : 24;
      $max = isset($args[3]) ? $args[3] : 64;

      ?>

      <input type="number" id="<?php echo $id ?>" name="<?php echo $name ?>" value="<?php echo $value ?>"
             min="<?php echo $min ?>"
             max="<?php echo $max ?>"/>
      <span class="description">
 	           <?php echo $description ?>
            </span>

   <?php
   }

   /**
    * Display share settings section
    */
   public function renderImageSetSelect($args)
   {
      $id = $args[0];
      $name = $this->plugin_slug . '[' . $args[0] . ']';
      $settings = $this->getSettings();
      $value = $settings[$id];
      $base = plugin_dir_url(__FILE__) . "buttons/";
      ?>

      <select id="<?php echo $id ?>" class="csb-image-set" name="<?php echo $name ?>">

         <?php foreach ($this->all_image_sets as $set) { ?>
				<option value="<?php echo $set ?>" <?php echo selected($set, $value); ?>">
    		    	<?php echo $set ?>
    	    	</option>
    		<?php } ?>

      </select>

   <?php
   }

   /**
    * Display share settings section
    */
   public function render_service_select($args)
   {
      $id = $args[0];
      $name = $this->plugin_slug . '[' . $args[0] . ']';
      $settings = $this->getSettings();
      $image_set = ($id == 'link_services') ? $settings['link_image_set'] : $settings['share_image_set'];
      $image_size = ($id == 'link_services') ? $settings['link_image_size'] : $settings['share_image_size'];
      $shareOrLink = ($id == 'link_services') ? 'Link' : 'Share';
      $value = $settings[$id];
      ?>

      <div class="csb-services">

         <div class="csb-include-list chosen">
            <div><span class="include-heading"><?php _e('Selected', $this->plugin_slug); ?></span>
               (<?php _e('these will be displayed', $this->plugin_slug); ?></div>
            <ul id="csbsort2" class="connectedSortable data-base="<?php echo $image_set ?>"">
            <?php echo $this->get_selected_services_html($value, $image_set, $image_size); ?>
            </ul>
            <input type="hidden" name="<?php echo $name ?>" id="<?php echo $id ?>" class="csb-services"/>
         </div>

         <div class="csb-include-list available">
            <div><span class="include-heading"><?php _e('Available', $this->plugin_slug) ?></span>
               (<?php _e('these will <strong>not</strong> be displayed', $this->plugin_slug) ?>)
            </div>
            <ul id="csbsort1" class="connectedSortable">
               <?php echo $this->get_available_services_html($value, $image_set, $image_size, $shareOrLink); ?>
            </ul>
            </center>
         </div>
      </div>
   <?php
   }

   /** Display selection for position */
   public function renderPositionSelect($args)
   {
      $id = $args[0];
      $name = $this->plugin_slug . '[' . $args[0] . ']';
      $settings = $this->getSettings();
      $value = $settings[$id];
      ?>

      <select id="<?php echo $id ?>" name="<?php echo $name ?>">
         <option
            value="above" <?php echo selected('above', $value); ?> ><?php _e('Above Content', $this->plugin_slug) ?></option>
         <option
            value="below" <?php echo selected('below', $value); ?> ><?php _e('Below Content', $this->plugin_slug) ?></option>
	      <option
            value="both" <?php echo selected('both', $value); ?> ><?php _e('Both above and below content', $this->plugin_slug) ?></option>
      </select>
   <?php
   }

	/** Display selection for position */
	public function renderFloatHeightSelect($args)
	{
		$id = $args[0];
		$name = $this->plugin_slug . '[' . $args[0] . ']';
		$settings = $this->getSettings();
		$value = $settings[$id];
		?>

		<select id="<?php echo $id ?>" name="<?php echo $name ?>">
			<option
				value="10" <?php echo selected('10', $value); ?> ><?php _e('10% down from top', $this->plugin_slug) ?></option>
			<option
				value="20" <?php echo selected('20', $value); ?> ><?php _e('20% down from top', $this->plugin_slug) ?></option>
			<option
				value="30" <?php echo selected('30', $value); ?> ><?php _e('30% down from top', $this->plugin_slug) ?></option>
			<option
				value="40" <?php echo selected('40', $value); ?> ><?php _e('40% down from top', $this->plugin_slug) ?></option>
			<option
				value="50" <?php echo selected('50', $value); ?> ><?php _e('50% down from top', $this->plugin_slug) ?></option>
		</select>

	<?php
	}

   /** Display selection for caption position */
   public function renderCaptionPositionSelect($args)
   {
      $id = $args[0];
      $name = $this->plugin_slug . '[' . $args[0] . ']';
      $settings = $this->getSettings();
      $value = $settings[$id];
      ?>

      <select id="<?php echo $id ?>" name="<?php echo $name ?>">
         <option
            value="inline-block" <?php echo selected('inline-block', $value); ?> ><?php _e('On same line as icons', $this->plugin_slug) ?></option>
         <option
            value="block" <?php echo selected('block', $value); ?> ><?php _e('On separate line above icons &nbsp; ', $this->plugin_slug) ?></option>
      </select>

   <?php
   }

   /** Display selection for alignment */
   public function renderAlignmentSelect($args)
   {
      $id = $args[0];
      $name = $this->plugin_slug . '[' . $args[0] . ']';
      $settings = $this->getSettings();
      $value = $settings[$id];
      ?>

      <select id="<?php echo $id ?>" name="<?php echo $name ?>">
         <option
            value="left" <?php echo selected('left', $value); ?> ><?php _e('Left', $this->plugin_slug) ?></option>
         <option
            value="center" <?php echo selected('center', $value); ?> ><?php _e('Center', $this->plugin_slug) ?></option>
         <option
            value="right" <?php echo selected('right', $value); ?> ><?php _e('Right', $this->plugin_slug) ?></option>
      </select>


   <?php
   }

    /** Display selection for caption position */
    public function renderPostTypeList($args)
    {
        $id = $args[0];
        $name = $this->plugin_slug . '[' . $args[0] . ']';
        $settings = $this->getSettings();
        $description = $args[1];
        $post_types = get_post_types( array('public' => true), 'names' );
        $values = $settings[$id];
        if (!is_array($values))
            $values = array();

        foreach ( $post_types  as $post_type ) {
            if ($post_type == 'page') continue;
            ?>
            <p>
                <input type="checkbox"  id="<?php echo $id ?>" name="<?php echo $name ?>[]" value="<?php echo $post_type; ?>" <?php checked(in_array($post_type, $values)); ?>>
                <?php echo $post_type; ?>
            </p>
        <?php } ?>
        <p><?php echo $description; ?></p>
    <?php
    }


   /** Display selection for alignment */
   public function renderFloatAlignmentSelect($args)
   {
      $id = $args[0];
      $name = $this->plugin_slug . '[' . $args[0] . ']';
      $settings = $this->getSettings();
      $value = $settings[$id];
      ?>

      <select id="<?php echo $id ?>" name="<?php echo $name ?>">
         <option
            value="left" <?php echo selected('left', $value); ?> ><?php _e('Left', $this->plugin_slug) ?></option>
         <option
            value="right" <?php echo selected('right', $value); ?> ><?php _e('Right', $this->plugin_slug) ?></option>
      </select>

   <?php
   }


   /**
    * Get list item HTML for selected services from our text list
    */
   function get_selected_services_html($selectedServicesString, $image_set, $image_size)
   {

      $htmlListItems = '';
      if ($selectedServicesString != '') {

         $selectedServices = explode(',', $selectedServicesString); // explode string to array
         foreach ($selectedServices as $service) {
            $htmlListItems .= $this->get_service_icon_html($service, $image_set, $image_size);
         }
      }

      return $htmlListItems;
   }

   /**
    * Get list item HTML for all services EXCEPT those already selected
    */
   function get_available_services_html($selectedServicesString, $image_set, $image_size, $shareOrLink = 'Share')
   {

      $htmlListItems = '';
      $selectedServices = array();
      if ($selectedServicesString != '') {
         $selectedServices = explode(',', $selectedServicesString); // explode string to array
      }

      foreach ($this->all_services as $service) {
         if (!$this->call_service_method($service, 'can' . $shareOrLink)) {
            continue;
         }
         if (in_array($service, $selectedServices)) {
            continue;
         }

         $htmlListItems .= $this->get_service_icon_html($service, $image_set, $image_size);

      }

      return $htmlListItems;
   }

   /**
    * Get html for a single service icon for selection on the admin page
    */
   function get_service_icon_html($service, $image_set, $image_size)
   {
      $filename = strtolower($service) . ".png";
      $base_url = plugin_dir_url(__FILE__) . "buttons/";
      $upload_dir = wp_upload_dir();
      $alt_url = $upload_dir['baseurl'] . '/' . $this->plugin_slug . "/buttons/";

      $url = $base_url . $image_set . "/" . $filename;
      $path = plugin_dir_path(__FILE__) . "buttons/" . $image_set . "/" . $filename;
      if (!file_exists($path)) {
         $url = $alt_url . $image_set . "/" . $filename;
      }
      return '<li id="' . $service
      . '"><img src="' . strtolower($url)
      . '" data-filename="' . $filename
      . '" data-image-set="' . strtolower($image_set)
      . '" data-alt-url="' . $alt_url
      . '" data-url="' . $base_url
      . '" alt="' . $service . '" width="' . $image_size . '" height="' . $image_size . '" /></li>';

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

}