<?php
/**
 * SH_Crafty_Social_Buttons_Admin Class
 * @author    Sarah Henderson
 * @date    2013-10-12
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SH_Crafty_Social_Buttons_Admin {

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
		add_action( 'admin_init', array( $this, 'register_settings' ) );

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

		if ( ! is_admin() ) {
			wp_die( __( 'You cannot access this page' ) );
		}

		// make sure they have the rights to manage options
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$settings = $this->getSettings();

		include_once( plugin_dir_path( __FILE__ ) . 'views/admin.php' );
	}


	/**
	 * Checks which classes are available in the services folder and registers them
	 */
	function load_all_services() {

		include_once( 'services/class-SH_Social_Service.php' );
		$this->all_services = array();

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
				$this->all_services[] = $matches[1];
				include_once( 'services/' . $file );
			}
		}
	}

	/**
	 * Checks which folders are available in the button directory and registers them
	 */
	function load_all_image_sets() {
		$this->all_image_sets = array();

		$directory = plugin_dir_path( __FILE__ ) . 'buttons'; // included image sets
		foreach ( scandir( $directory ) as $folder ) {
			if ( '.' === $folder ) {
				continue;
			}
			if ( '..' === $folder ) {
				continue;
			}

			if ( ! preg_match( "/\./", $folder ) ) { // no dots allowed, only folders
				$this->all_image_sets[] = $folder;
			}
		}

        $upload_dir = wp_upload_dir();
		$directory = $upload_dir['basedir'] . '/crafty-social-buttons/buttons'; // custom image sets
		if (is_dir($directory)) {
			foreach ( scandir( $directory ) as $folder ) {
				if ( '.' === $folder ) {
					continue;
				}
				if ( '..' === $folder ) {
					continue;
				}

				if ( ! preg_match( "/\./", $folder ) ) { // no dots allowed, only folders
					$this->all_image_sets[] = $folder;
				}
			}
		}

	}

	/**
	 * Configure admin settings
	 */
	public function register_settings() {

		$section = 'cbs_basic_share_settings';
		$page    = $this->plugin_slug . '-share';

		add_settings_section( $section, __( 'Basic Options', $this->plugin_slug ),
			array( $this, 'displayShareBasicSettingsText' ), $page );

		add_settings_field( 'share_image_set', __( 'Image Set', $this->plugin_slug ),
			array( $this, 'renderImageSetSelect' ), $page, $section, array( 'share_image_set' ) );

		add_settings_field( 'share_image_size', __( 'Image Size', $this->plugin_slug ),
			array( $this, 'renderNumericTextbox' ), $page, $section,
			array( 'share_image_size', __( 'Size in pixels, between 24 and 64', $this->plugin_slug ) ) );

		add_settings_field( 'share_caption', __( 'Caption', $this->plugin_slug ),
			array( $this, 'renderTextbox' ), $page, $section,
			array( 'share_caption', __( 'Displays before the set of share buttons', $this->plugin_slug ) ) );

		add_settings_field( 'share_services', __( 'Show these services', $this->plugin_slug ),
			array( $this, 'render_service_select' ), $page, $section, array( 'share_services' ) );

		add_settings_field( 'position', __( 'Above or below content', $this->plugin_slug ),
			array( $this, 'renderPositionSelect' ), $page, $section, array( 'position' ) );

        $section = 'cbs_display_share_settings';
        add_settings_section( $section, __( 'Display Options', $this->plugin_slug ), null, $page );

		add_settings_field( 'show_on_posts', __( 'Show on Posts', $this->plugin_slug ),
			array( $this, 'renderCheckbox' ), $page, $section,
			array( 'show_on_posts', __( 'Shares a single post', $this->plugin_slug ) ) );

		add_settings_field( 'show_on_home', __( 'Show on Home Page', $this->plugin_slug ),
			array( $this, 'renderCheckbox' ), $page, $section,
            array( 'show_on_home', __( 'Shares each post on home page', $this->plugin_slug ) ) );

        add_settings_field( 'show_on_category', __( 'Show on Category Pages', $this->plugin_slug ),
            array( $this, 'renderCheckbox' ), $page, $section,
            array( 'show_on_category', __( 'Shares each post on category pages', $this->plugin_slug ) ) );

        add_settings_field( 'show_on_archive', __( 'Show on Archive Pages', $this->plugin_slug ),
            array( $this, 'renderCheckbox' ), $page, $section,
            array( 'show_on_archive', __( 'Shares each post on archive and tags pages', $this->plugin_slug ) ) );

        add_settings_field( 'show_on_pages', __( 'Show on Pages', $this->plugin_slug ),
            array( $this, 'renderCheckbox' ), $page, $section,
            array( 'show_on_pages', __( 'Shares the page', $this->plugin_slug ) ) );

        add_settings_field( 'show_on_static_home', __( 'Show on Static Front Page', $this->plugin_slug ),
            array( $this, 'renderCheckbox' ), $page, $section,
            array( 'show_on_static_home', __( 'Shares the home page if you have a static front page', $this->plugin_slug ) ) );

		$section = 'cbs_advanced_share_settings';
		add_settings_section( $section, __( 'Advanced Options', $this->plugin_slug ), null, $page );

        add_settings_field( 'open_in', __( 'Open in', $this->plugin_slug ),
            array( $this, 'renderRadio' ), $page, $section,
            array( 'open_in', '',
                array(
                    'new_window' => __( 'New Window', $this->plugin_slug ),
                    'same_window' => __( 'Same Window', $this->plugin_slug ),
                    'popup' => __( 'Popup', $this->plugin_slug ))
            ) );

		add_settings_field( 'show_count', __( 'Show share counts', $this->plugin_slug ),
			array( $this, 'renderCheckbox' ), $page, $section,
			array( 'show_count', __( 'Only displayed if service supports it.', $this->plugin_slug ) ) );

		add_settings_field( 'email_body', __( 'Email text', $this->plugin_slug ),
			array( $this, 'renderTextbox' ), $page, $section,
            array( 'email_body', __( 'Default Email text (user can override this)', $this->plugin_slug ) ) );


		add_settings_field( 'twitter_body', __( 'Tweet text', $this->plugin_slug ),
			array( $this, 'renderTextbox' ), $page, $section,
			array( 'twitter_body', __( 'Default Tweet text (user can override this)', $this->plugin_slug ) ) );

		add_settings_field( 'twitter_show_title', __( 'Title in Tweet text', $this->plugin_slug ),
			array( $this, 'renderCheckbox' ), $page, $section,
			array(
				'twitter_show_title',
				__( 'Include the post/page title in the default Tweet text', $this->plugin_slug )
			) );


		$section = 'cbs_link_button_settings';
		$page    = $this->plugin_slug . '-link';

        add_settings_section( $section, __( 'Display Options', $this->plugin_slug ),
			array( $this, 'displayLinkSettingsText' ), $page );

		add_settings_field( 'link_image_set', __( 'Image Set', $this->plugin_slug ),
			array( $this, 'renderImageSetSelect' ), $page, $section, array( 'link_image_set' ) );

		add_settings_field( 'link_image_size', __( 'Image Size', $this->plugin_slug ),
			array( $this, 'renderNumericTextbox' ), $page, $section,
			array( 'link_image_size', __( 'Size in pixels, between 24 and 64', $this->plugin_slug ) ) );

		add_settings_field( 'link_caption', __( 'Caption', $this->plugin_slug ),
			array( $this, 'renderTextbox' ), $page, $section,
			array( 'link_caption', __( 'Displays before the set of link buttons', $this->plugin_slug ) ) );

		add_settings_field( 'link_services', __( 'Show these services', $this->plugin_slug ),
			array( $this, 'render_service_select' ), $page, $section, array( 'link_services' ) );

        add_settings_field( 'new_window', __( 'Open in new window', $this->plugin_slug ),
            array( $this, 'renderCheckbox' ), $page, $section, array( 'new_window' ) );

		$section = 'cbs_link_service_settings';
		add_settings_section( $section, __( 'User IDs', $this->plugin_slug ), array(
				$this,
				'displayLinkServiceText'
			), $page );

		foreach ( $this->all_services as $service ) {
			// we want to add a custom description for some of the fields
			$caption     = $service;
			$description = "";
			$description = $this->call_service_method( $service, 'description' );

			add_settings_field(
				$service,
				$caption,
				array( $this, 'renderTextbox' ),
				$page,
				$section,
				array( $service, $description ) );
		}

		register_setting( $this->plugin_slug, $this->plugin_slug, array( $this, 'validate_settings' ) );
	}

	/**
	 * Display share basic settings section text
	 */
	public function add_contextual_help( $hook ) {
		$screen = get_current_screen();

		if ( $screen->id != 'settings_page_' . $this->plugin_slug ) {
			return;
		}

		$screen->add_help_tab( array(
			'id'      => 'csb-help-intro',
			'title'   => __( 'Welcome', $this->plugin_slug ),
			'content' => file_get_contents( plugin_dir_path( __FILE__ ) . '/help/intro-tab.php' )
		) );

		$screen->add_help_tab( array(
			'id'      => 'csb-share-help',
			'title'   => __( 'Share Buttons', $this->plugin_slug ),
			'content' => file_get_contents( plugin_dir_path( __FILE__ ) . '/help/share-tab.php' )
		) );

		$screen->add_help_tab( array(
			'id'      => 'csb-link-help',
			'title'   => __( 'Link Buttons', $this->plugin_slug ),
			'content' => file_get_contents( plugin_dir_path( __FILE__ ) . '/help/link-tab.php' )
		) );

		$screen->add_help_tab( array(
			'id'      => 'csb-widget',
			'title'   => __( 'Widget', $this->plugin_slug ),
			'content' => file_get_contents( plugin_dir_path( __FILE__ ) . '/help/widget-tab.php' )
		) );

		$screen->add_help_tab( array(
			'id'      => 'csb-shortcode',
			'title'   => __( 'Shortcodes', $this->plugin_slug ),
			'content' => file_get_contents( plugin_dir_path( __FILE__ ) . '/help/shortcode-tab.php' )
		) );

		$screen->add_help_tab( array(
			'id'      => 'csb-action-hooks',
			'title'   => __( 'Action Hooks', $this->plugin_slug ),
			'content' => file_get_contents( plugin_dir_path( __FILE__ ) . '/help/action-hook-tab.php' )
		) );

		$screen->add_help_tab( array(
			'id'      => 'csb-icons',
			'title'   => __( 'Adding Icons', $this->plugin_slug ),
			'content' => file_get_contents( plugin_dir_path( __FILE__ ) . '/help/adding-icons-tab.php' )
		) );

		$screen->add_help_tab( array(
			'id'      => 'csb-credits',
			'title'   => __( 'Credits', $this->plugin_slug ),
			'content' => file_get_contents( plugin_dir_path( __FILE__ ) . '/help/credits-tab.php' )
		) );

		$screen->add_help_tab( array(
			'id'      => 'csb-about',
			'title'   => __( 'About Me', $this->plugin_slug ),
			'content' => file_get_contents( plugin_dir_path( __FILE__ ) . '/help/about-me-tab.php' )
		) );
	}

	/**
	 * Display share basic settings section text
	 */
	public function displayShareBasicSettingsText() {
		echo '<p>';
		_e( 'Share Buttons will prompt the user to share the current post or page URL.', $this->plugin_slug );
		echo '</p>';

	}

	/**
	 * Display link settings section text
	 */
	public function displayLinkSettingsText() {
		echo '<p>';
		_e( 'Link Buttons will link to your user profile on each site.  Add them to the theme using the Crafty Social Buttons <strong>widget</strong>.', $this->plugin_slug );
		echo '</p><p>';
		_e( 'Enter your <strong>user id</strong> for each service you choose below to make the button link directly to your profile.', $this->plugin_slug );
		echo '</p>';
	}

	/**
	 * Display link settings section text
	 */
	public function displayLinkServiceText() {
		echo '<p>';
		_e( 'Enter just your <strong>user id</strong> for each service, not the full URL. The bit in bold that says <strong>user-id</strong> in the hint is the part you should enter.', $this->plugin_slug );
		echo '</p>';
	}

	/**
	 * Display a settings checkbox
	 */
	public function renderCheckbox( $args ) {
		$id          = $args[0];
		$name        = $this->plugin_slug . '[' . $args[0] . ']';
		$description = isset( $args[1] ) ? $args[1] : '';
		$settings    = $this->getSettings();
		$value       = $settings[ $id ];
		?>

		<input type="checkbox" id="<?php echo $id ?>" name="<?php echo $name ?>" <?php echo checked( 1, $value ) ?> value="0"/>
		<span class="description" for="<?php echo $id ?>">
                <?php echo $description ?>
            </span>

	<?php
	}

    /**
     * Display a set of radio buttons
     */
    public function renderRadio( $args ) {
        $id          = $args[0];
        $name        = $this->plugin_slug . '[' . $args[0] . ']';
        $description = isset( $args[1] ) ? $args[1] : '';
        $options     = isset( $args[2] ) ? $args[2] : array();
        $settings    = $this->getSettings();
        $value       = $settings[ $id ];
        ?>

        <?php foreach ($options as $key => $label) { ?>
        <input type="radio" id="<?php echo $id ?>" name="<?php echo $name ?>" <?php echo checked( $key, $value ); ?> value="<?php echo $key ?>"/> <?php echo $label; ?>
        <?php } ?>
        <span class="description" for="<?php echo $id ?>">
                <?php echo $description ?>
            </span>

    <?php
    }

	/**
	 * Display a settings textbox
	 */
	public function renderTextbox( $args ) {
		$id          = $args[0];
		$settings    = $this->getSettings();
		$value       = isset( $settings[ $id ] ) ? $settings[ $id ] : ' ';
		$name        = $this->plugin_slug . '[' . $args[0] . ']';
		$description = isset( $args[1] ) ? $args[1] : '';

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
	public function renderNumericTextbox( $args ) {
		$id          = $args[0];
		$settings    = $this->getSettings();
		$value       = isset( $settings[ $id ] ) ? $settings[ $id ] : ' ';
		$name        = $this->plugin_slug . '[' . $args[0] . ']';
		$description = isset( $args[1] ) ? $args[1] : '';
		$min         = isset( $args[2] ) ? $args[2] : 24;
		$max         = isset( $args[3] ) ? $args[3] : 64;

		?>

		<input type="number" id="<?php echo $id ?>" name="<?php echo $name ?>" value="<?php echo $value ?>" min="<?php echo $min ?>"
		       max="<?php echo $max ?>"/>
		<span class="description">
 	           <?php echo $description ?>
            </span>

	<?php
	}

	/**
	 * Display share settings section
	 */
	public function renderImageSetSelect( $args ) {
		$id       = $args[0];
		$name     = $this->plugin_slug . '[' . $args[0] . ']';
		$settings = $this->getSettings();
		$value    = $settings[ $id ];
		$base     = plugin_dir_url( __FILE__ ) . "buttons/";
		?>

		<select id="<?php echo $id ?>" class="csb-image-set" name="<?php echo $name ?>">

			<?php foreach ( $this->all_image_sets as $set ) { ?>
				<option value="<?php echo $set ?>" <?php echo selected( $set, $value );?>">
    		    	<?php echo$set?>
    	    	</option>
    		<?php } ?>

		</select>

	<?php
	}

	/**
	 * Display share settings section
	 */
	public function render_service_select( $args ) {
		$id          = $args[0];
		$name        = $this->plugin_slug . '[' . $args[0] . ']';
		$settings    = $this->getSettings();
		$image_set   = ( $id == 'link_services' ) ? $settings['link_image_set'] : $settings['share_image_set'];
		$image_size  = ( $id == 'link_services' ) ? $settings['link_image_size'] : $settings['share_image_size'];
		$shareOrLink = ( $id == 'link_services' ) ? 'Link' : 'Share';
		$value       = $settings[ $id ];
		?>

		<div class="csb-services">

			<div class="csb-include-list chosen">
				<div><span class="include-heading"><?php _e('Selected',$this->plugin_slug); ?></span> (<?php _e('these will be displayed',$this->plugin_slug);?></div>
				<ul id="csbsort2" class="connectedSortable data-base="<?php echo $image_set ?>"">
					<?php echo $this->get_selected_services_html( $value, $image_set, $image_size ); ?>
				</ul>
				<input type="hidden" name="<?php echo $name ?>" id="<?php echo $id ?>" class="csb-services"/>
			</div>

			<div class="csb-include-list available">
				<div><span class="include-heading"><?php _e( 'Available', $this->plugin_slug ) ?></span>
					(<?php _e( 'these will <strong>not</strong> be displayed', $this->plugin_slug ) ?>)
				</div>
				<ul id="csbsort1" class="connectedSortable">
					<?php echo $this->get_available_services_html( $value, $image_set, $image_size, $shareOrLink ); ?>
				</ul>
				</center>
			</div>
		</div>
	<?php
	}

	/**
	 * Display share settings section
	 */
	public function renderPositionSelect( $args ) {
		$id       = $args[0];
		$name     = $this->plugin_slug . '[' . $args[0] . ']';
		$settings = $this->getSettings();
		$value    = $settings[ $id ];
		?>

		<select id="<?php echo $id ?>" name="<?php echo $name ?>">
			<option
				value="above" <?php echo selected( 'above', $value ); ?> ><?php _e( 'Above', $this->plugin_slug ) ?></option>
			<option
				value="below" <?php echo selected( 'below', $value ); ?> ><?php _e( 'Below', $this->plugin_slug ) ?></option>
			<option
				value="both" <?php echo selected( 'both', $value ); ?> ><?php _e( 'Both', $this->plugin_slug ) ?></option>
		</select>

	<?php
	}

	/**
	 * Validate our saved settings
	 */
	public function validate_settings( $input ) {
		$settings = $this->getSettings();

		//mark it as configured forevermore
		$settings['configured'] = true;

		// find out which tab we are on, otherwise we'll mess up the checkbox values for the other tab
		// the other data will be fine, but any checkboxes will be unset
		$tab = $input['tab'];

		if ( 'share_options' == $tab ) {
			// first, all the checkboxes need to be set if present
			$settings['show_on_posts']      = isset( $input['show_on_posts'] );
			$settings['show_on_pages']      = isset( $input['show_on_pages'] );
			$settings['show_on_home']       = isset( $input['show_on_home'] );
			$settings['show_on_static_home']= isset( $input['show_on_static_home'] );
			$settings['show_on_category']   = isset( $input['show_on_category'] );
			$settings['show_on_archive']    = isset( $input['show_on_archive'] );
			$settings['show_count']         = isset( $input['show_count'] );
            $settings['twitter_show_title'] = isset( $input['twitter_show_title'] );

            // parse out our radio buttons, they are constrained so just take the values
            $settings['open_in']            = $input['open_in'];
            $settings['popup']              = $input['open_in'] == 'popup';

			// our select boxes have constrained UI, so just update them
			$settings['share_image_set'] = isset( $input['share_image_set'] ) ? $input['share_image_set'] : 'simple';
			$settings['share_services']  = isset( $input['share_services'] ) ? $input['share_services'] : '';
			$settings['position']        = isset( $input['position'] ) ? $input['position'] : 'below';

			// and finally, validate our text boxes
			$settings['share_caption'] = sanitize_text_field( $input['share_caption'] );
			$settings['email_body']    = sanitize_text_field( $input['email_body'] );
			$settings['twitter_body']  = sanitize_text_field( $input['twitter_body'] );

			// including numeric ones
			$settings['share_image_size'] = $this->sanitize_image_size( $input['share_image_size'] );


		} else if ( 'link_options' == $tab ) {

            // check if checkboxes are set
            $settings['new_window']     = isset( $input['new_window'] );

            // our select boxes have constrained UI, so just update them
			$settings['link_image_set'] = $input['link_image_set'];
			$settings['link_services']  = $input['link_services'];

			// and finally, validate our text boxes
			$settings['link_caption']   = sanitize_text_field( $input['link_caption'] );

			// including numeric ones
			$settings['link_image_size'] = $this->sanitize_image_size( $input['link_image_size'] );

			// and the textboxes for all our services
			foreach ( $this->all_services as $service ) {
				$settings[ $service ] = sanitize_text_field( stripslashes_deep( $input[ $service ] ) );
			}

		}

		return $settings;
	}

	function sanitize_image_size( $image_size_string ) {
		$size = sanitize_text_field( $image_size_string );
		if ( ! is_numeric( $size ) ) {
			return 48;
		}
		if ( $size < 24 ) {
			return 24;
		}
		if ( $size > 64 ) {
			return 64;
		}

		return $size;
	}

	/**
	 * Get list item HTML for selected services from our text list
	 */
	function get_selected_services_html( $selectedServicesString, $image_set, $image_size ) {

		$htmlListItems = '';
		if ( $selectedServicesString != '' ) {

			$selectedServices = explode( ',', $selectedServicesString ); // explode string to array
			foreach ( $selectedServices as $service ) {
				$htmlListItems .= $this->get_service_icon_html( $service, $image_set, $image_size );
			}
		}

		return $htmlListItems;
	}

	/**
	 * Get list item HTML for all services EXCEPT those already selected
	 */
	function get_available_services_html( $selectedServicesString, $image_set, $image_size, $shareOrLink = 'Share' ) {

		$htmlListItems    = '';
		$selectedServices = array();
		if ( $selectedServicesString != '' ) {
			$selectedServices = explode( ',', $selectedServicesString ); // explode string to array
		}

		foreach ( $this->all_services as $service ) {
			if ( ! $this->call_service_method( $service, 'can' . $shareOrLink ) ) {
				continue;
			}
			if ( in_array( $service, $selectedServices ) ) {
				continue;
			}

			$htmlListItems .= $this->get_service_icon_html( $service, $image_set, $image_size );

		}

		return $htmlListItems;
	}

	/**
	 * Get html for a single service icon for selection on the admin page
	 */
	function get_service_icon_html( $service, $image_set, $image_size ) {
		$filename = strtolower($service) . ".png";
		$base_url = plugin_dir_url( __FILE__ ) . "buttons/";
        $upload_dir = wp_upload_dir();
		$alt_url = $upload_dir['baseurl'] . '/' . $this->plugin_slug . "/buttons/";

		$url = $base_url . $image_set . "/" . $filename;
		$path = plugin_dir_path( __FILE__ ) . "buttons/" . $image_set . "/" . $filename;
		if (!file_exists($path)) {
			$url = $alt_url . $image_set . "/" . $filename;
		}
		return '<li id="' . $service
		       . '"><img src="' . strtolower( $url )
		       . '" data-filename="' . $filename
		       . '" data-image-set="' . strtolower( $image_set )
		       . '" data-alt-url="' . $alt_url
		       . '" data-url="' . $base_url
		       . '" alt="' . $service . '" width="' . $image_size . '" height="' . $image_size . '" /></li>';

	}


	/**
	 * Calls a static method on the given service and returns the result
	 */
	function call_service_method( $service, $method ) {
		return call_user_func( array( 'SH_' . $service, $method ) );
	}

	/**
	 * Loads all the settings from the database
	 */
	function getSettings() {
		$settings = get_option( $this->plugin_slug );
		$defaults = SH_Crafty_Social_Buttons_Plugin::get_default_settings();

		return wp_parse_args( $settings, $defaults );
	}

}

?>
