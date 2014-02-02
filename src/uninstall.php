<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Crafty Social Buttons
 * @author    Sarah Henderson <sarah@sarahhenderson.info>
 */

// If uninstall, not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings_option = "crafty-social-buttons";
$widget_option = "widget_crafty-social-buttons_widget";


if ( !is_multisite() ) 
{
	// delete all options
	delete_option( $settings_option );
	delete_option( $widget_option );
} 
else 
{
    global $wpdb;
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();

    foreach ( $blog_ids as $blog_id ) 
    {
        switch_to_blog( $blog_id );
		delete_option( $settings_option );
		delete_option( $widget_option );
    }
    switch_to_blog( $original_blog_id );
}