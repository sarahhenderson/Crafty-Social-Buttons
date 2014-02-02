=== Crafty Social Buttons ===
Contributors: shen045
Donate link: http://sarahhenderson.info/donate
Author URI: http://sarahhenderson.info
Plugin URL: http://github.io/sarahhenderson/crafty-social-buttons
Tags: social, social buttions, social icons, ravelry, etsy, craftsy, widget, shortcode
Requires at least: 3.5
Tested up to: 3.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds social sharing and link buttons, including Ravelry, Etsy, Craftsy and Pinterest.

== Description ==

This plugin adds a set of social buttons to your website, and includes craft-related social sites like Ravelry, Etsy, Craftsy and Pinterest as well as other major social networks.   You can choose from nine different button styles to match your theme.  You can have the share buttons automatically added to the top or bottom of each post, and can position the link buttons using either a widget, a shortcode or a template action hook.

You can use the share button and link button functions either separately, or together:

**Share Button mode** allows you to put a set of buttons at the top or bottom of each post.  Each button will share that post with that particular service.  I.e., clicking the facebook icon will open a window for the user to share your post on their Facebook page.  Not all services support sharing, for instance, you can't share via Etsy or Craftsy.

**Link Button mode** lets you configure the buttons to link directly you your profiles on the social networks.  I.e. clicking the facebook icon will take the user to your facebook page, clicking the Etsy icon will take them to your Etsy shop.   These are designed to be included in either your header, sidebars or footer, as they are not specific to a post.  These can be easily included by adding the Social Link Button widget to the area where you want the buttons to appear.

Supported social services include:

*	Digg
*   Craftsy (link only)
*	Email (share only)
*	Etsy (link only)
*   Facebook
*   Flickr (link only)
*	Google+
*   Instagram (link only)
*	LinkedIn
*	Pinterest
*	Ravelry
*	Reddit
*	Stumble Upon
*	Tumblr
*	Twitter

You can also choose exactly which services you want separately for each mode, so you can can have *Share Buttons* for Ravelry, Pinterest, Facebook, Twitter and Email, but have *Link Buttons* for Google+, Craftsy, Pinterest and your Etsy shop.

For more information, see the plugin website at http://sarahhenderson.github.io/Crafty-Social-Buttons/

== Installation ==

The easiest way to install is through the WordPress control panel.  On the plugins page, search for *Crafty Social Buttons* and click Install.  Once installed, Activate the plugin.  

You can also download the plugin and then follow these steps:

1. Upload the whole `crafty-social-buttons` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

Once you have activated, you will see a message bar confirming that it has been installed, with a convenient link to the settings page where you can configure the options for how and where the buttons will appear.
You will also find a new Widget called Crafty Social Link Buttons, which can be added to any widget area in your theme.

== Frequently Asked Questions ==

= Can I add a different set of icons? =

Yes!  There are instructions in the help section of the Plugin (pull down the help menu in the top right of the WordPress admin, or at the plugin homepage.

= Can I add a different social/craft network ? =

I'm happy to take requests for new services to add.

= Can I have more control over where to put the icons? =

Yes! The buttons can also be included in any post or page by using one of these shortcodes:

*	[csbshare] for the *Share buttons*
*	[csblink] for the *Link buttons*

If your theme doesn't have a widget area where you want the *Link Buttons* to be placed, you can include them by editing your theme template.  Just include one of these links in your template to generate the buttons:

*  `<?php do_action('crafty-social-link-buttons'); ?>` 
*  `<?php do_action('crafty-social-share-buttons'); ?>` 

Since the *Share buttons* are page/post specific, the shortcode is usualy best, and since the *Link buttons* are site/site owner specific, the widget or action hook will normally be a better choice.

== Screenshots ==

1. *Share buttons* displayed below the content of a post (twenty thirteen theme, somacro icon set).
2. *Link buttons* displayed in the footer using a widget (twenty thirteen theme, simple icon set).
3. Settings page for configuring the *Link buttons*
4. Settings page for configuring the *Share buttons*
5. *Share buttons* displayed with post count bubbles next to them (twenty thirteen theme, arbenting icon set).

== Changelog ==

= 1.0.8 =
* Enabled support for WordPress MultiSite

= 1.0.6 =
* Fixed bug with headers not sent

= 1.0.5 =
* Added Instagram and Flickr link buttons

= 1.0.4 =
* Added more link options for Craftsy - link to individual user profile, pattern store, or instructor page

= 1.0.3 =
* LinkedIn now able to link to either individual or company profile
* Improved styling of link button hints

= 1.0.2 =
* Fixed issue with preview icons not appearing in admin on some hosts
* Updated readme

= 1.0.1 =
* Fixed issue with preview icons not appearing in admin on some hosts
* Updated readme

= 1.0.0 =
* First version!

== Upgrade Notice ==

= 1.0.8 =
* Enables WordPress MultiSite

= 1.0.6 =
* Fixed bug with headers not sent

= 1.0.5 =
* Instagram and Flickr added

= 1.0.4 =
* More link options for Craftsy

= 1.0.3 =
* LinkedIn now able to link to either individual or company profile

= 1.0.2 =
* Fixes preview icon not appearing on some hosts

= 1.0 =
* First version!

== Future Editions ==

These are the things currently in the pipeline:

* Add support for YouTube (for crafters who post video tutorials)
* Adding an option to choose image sizes for the icons
* Changing the post count feature to request data only after the page is loaded so it doesn't slow things down so much.
* Option to diplays in archives and category listings (?)
* (Anything else?  You tell me!)