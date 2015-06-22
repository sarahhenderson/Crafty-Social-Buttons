<?php
/**
 * Crafty Social Buttons
 *
 * Adds social sharing buttons and links to your site.  Includes icons for Ravelry, Etsy,
 * Craftsy and Pinterest along with the major social networks.
 *
 * @package   SH-Crafty-Social-Buttons
 * @author    Sarah Henderson <sarah@sarahhenderson.info>
 * @license   GPL-2.0+
 * @link      http://sarahhenderson.info
 * @copyright 2013 Sarah Henderson
 *
 * @wordpress-plugin
 * Plugin Name: Crafty Social Buttons
 * Plugin URI:  http://github.com/sarahhenderson/crafty-social-buttons
 * Description: Adds social sharing buttons and links to your site, including Ravelry, Etsy, Craftsy and Pinterest
 * Version:     1.5.2
 * Author:      Sarah Henderson
 * Author URI:  http://sarahhenderson.info
 * Text Domain: crafty-social-button
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// include our three main classes
require_once( plugin_dir_path( __FILE__ ) . 'class-SH-Crafty-Social-Buttons-Admin-Fields.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-SH-Crafty-Social-Buttons-Plugin.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-SH-Crafty-Social-Buttons-Shortcode.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-SH-Crafty-Social-Buttons-Widget.php' );

// Register hooks that are fired when the plugin is activated and deactivated respectively.
register_activation_hook( __FILE__, array( 'SH_Crafty_Social_Buttons_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'SH_Crafty_Social_Buttons_Plugin', 'deactivate' ) );

// instantiate our main plugin (which will include admin if necessary) and the shortcodes
SH_Crafty_Social_Buttons_Shortcode::get_instance();
SH_Crafty_Social_Buttons_Plugin::get_instance();

// and hook in our Widget
add_action( 'widgets_init', create_function( '', 'register_widget("SH_Crafty_Social_Buttons_Widget");' ) );