=== Crafty Social Buttons ===
Contributors: shen045
Donate link: http://sarahhenderson.info/donate
Author URI: http://sarahhenderson.info
Plugin URL: http://github.io/sarahhenderson/crafty-social-buttons
Tags: social, social buttons, social icons, sharing, social sharing, facebook, google, pinterest, flickr, ravelry, etsy, craftsy, whatsapp, youtube, widget, shortcode
Requires at least: 3.5
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds social sharing and link buttons, including Ravelry, Etsy, Craftsy and Pinterest.

== Description ==

This plugin adds a set of social buttons to your website, and includes craft-related social sites like Ravelry, Etsy, Craftsy and Pinterest as well as other major social networks, including WhatsApp.  Support for sharing to WhatsApp is also provided on mobile devices.   You can choose from nine different button styles to match your theme.  You can have the share buttons automatically added to the top or bottom of each post, and can position the link buttons using either a widget, a shortcode or a template action hook.

You can use the share button and link button functions either separately, or together:

**Share Button mode** allows you to put a set of buttons at the top or bottom of each post.  Each button will share that post with that particular service.  I.e., clicking the facebook icon will open a window for the user to share your post on their Facebook page.  Not all services support sharing, for instance, you can't share via Etsy or Craftsy.

**Link Button mode** lets you configure the buttons to link directly you your profiles on the social networks.  I.e. clicking the facebook icon will take the user to your facebook page, clicking the Etsy icon will take them to your Etsy shop.   These are designed to be included in either your header, sidebars or footer, as they are not specific to a post.  These can be easily included by adding the Social Link Button widget to the area where you want the buttons to appear.

Supported social services include:

*   Craftsy (link only)
*	Digg
*	Ebay (link only)
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
*	Specific Feeds (link only)
*	Stumble Upon
*	Tumblr
*	Twitter
*   Vimeo
*   YouTube
*   WhatApp (share only, only on mobile)

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

Yes!  There are instructions in the help section of the Plugin (pull down the help menu in the top right of the WordPress admin, or at the plugin webpage (http://sarahhenderson.github.io/Crafty-Social-Buttons/#adding-icons).

= Can I add a different social/craft network ? =

I'm happy to take requests for new services to add.

= Can I have more control over where to put the icons? =

Yes! The buttons can also be included in any post or page by using one of these shortcodes:

*  [csblink] for the *Link buttons*
*  [csbshare] for the *Share buttons*
*  [csbnone] will hide the *Share buttons* on a post/page where they would otherwise normally appear

If your theme doesn't have a widget area where you want the buttons to be placed, you can include them by editing your theme template.  Just include one of these links in your template to generate the buttons:

*  `<?php do_action('crafty-social-link-buttons'); ?>`
*  `<?php do_action('crafty-social-share-buttons'); ?>`  (inside loop)
*  `<?php do_action('crafty-social-share-page-buttons'); ?>` (outside loop)

Since the *Share buttons* are page/post specific, the shortcode is usually best, and since the *Link buttons* are site/site owner specific, the widget or action hook will normally be a better choice.

== Screenshots ==

1. *Share buttons* displayed below the content of a post (twenty thirteen theme, somacro icon set).
2. *Link buttons* displayed in the footer using a widget (twenty thirteen theme, simple icon set).
3. Settings page for configuring the *Link buttons*
4. Settings page for configuring the *Share buttons*
5. *Share buttons* displayed with post count bubbles next to them (twenty thirteen theme, arbenting icon set).

== Changelog ==

= 1.5.2 =
* Fixed bug where share count script was not getting loaded properly

= 1.5.1 =
* Fixed bug in plugin initialisation affecting PHP version older than 5.4.

= 1.5.0 =
* Added WhatsApp share button (displays on mobile devices only)
* Can now add a simple hover effect (dim or brighten) to the buttons
* Can now add rel="nofollow" to links
* Can now add custom css classes to the button group (useful if you want to use something like animate.css)
* Can now choose which post types the sharing buttons get added to (including custom post types)
* Fixed bug where spaces were being stripped from email subject

= 1.4.2 =
* Fixed bug generating a notice next to link buttons

= 1.4.1 =
* Added specific CSS styles to be compatible with twenty fifteen theme
* Ensure widget CSS class is output in the widget wrapper
* Popups now work even if share counts are disabled
* Experimental - added ability to float share buttons on single pages to right or left of content

= 1.4.0 =
* Added `crafty-social-button-image` class to button images for easier styling
* Added option to cache share counts
* Added [csbnone] shortcode to suppress share buttons on any post or page
* Optimised images (smaller file sizes, faster loading)
* Urlencoded post titles in links to enable validator compliance
* Fixed bug in LinkedIn generated link button url

= 1.3.8 =
* Added link buttons for Ebay, Vimeo and SpecificFeeds
* Stopped email links opening a blank window
* Added titles to share and link buttons

= 1.3.7 =
* Fixed Facebook share counts not appearing by switching to different Facebook API

= 1.3.6 =
* Fix site url incorrectly showing up in email To: field

= 1.3.5 =
* Fix broken Pinterest share link

= 1.3.4 =
* Hide the zeros that Facebook returns as a string

= 1.3.3 =
* Fixed issue affecting PHP 5.2 installations

= 1.3.2 =
* Changed caption 'post counts' to 'share counts' in settings page.
* Changed format of ajax share count request url to avoid issues with apache security plugins
* Share counts are hidden until data is available, prevents row of zeros displaying
* Escaped entities in urls to ensure HTML validator compliance
* Added options for alignment and caption position

= 1.3.1 =
* Fixed bug with post counts not showing both above and below posts
* Added separate option for opening link buttons in a new window
* Fixed issue with php short tags in old versions of PHP

= 1.3.0 =
* Added more detailed options for where share buttons should appear
* Added action hooks to put share buttons on category, tag and archive pages
* Added option to have share pages load in a popup
* Fixed bug with format of company LinkedIn url (thanks to https://github.com/harancamatti)

= 1.2.2 =
* Changed font size of share buttons
* CSS uses class instead of id to allow multiple sets of buttons per page
* Allow https:// urls to override link button usernames

= 1.2.1 =
* Fixed bug causing fatal errors in some versions of PHP

= 1.2.0 =
* Share counts are now loaded via Ajax (so don't slow down the page loading)
* Custom icons can be placed under wp-content (so aren't deleted when the plugin is updated)
* Fixed issues with share counts not being retrieved because of API changes
* Improved layout of share count bubbles
* Admin section is translation-ready except for help section

= 1.1.1 =
* Including page/post title in default tweet text is optional

= 1.1.0 =
* Allows you to choose the size of share and link buttons
* CSS uses ID instead of class to better protect against theme interference

= 1.0.12 =
* Fixed bug where twitter prompt text wasn't appearing
* Fixed ragged edge on somacro YouTube icon

= 1.0.11 =
* Allows arbitrary URLs for link buttons

= 1.0.10 =
* Added RSS link button option
* Fixed problem with special characters in titles appearing wrongly in the Twitter share

= 1.0.9 =
* Added YouTube link button option

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

= 1.5.1 =
* Added WhatsApp sharing button, a hover effect, rel="nofollow" option, ability to choose which post types have share buttons, and ability to add custom CSS classes to button blocks.

= 1.3.8 =
* Add link buttons for Ebay, Vimeo and SpecificFeeds (allows visitors to subscribe by email)

= 1.3.7 =
* Fixes Facebook share counts not appearing

= 1.3.2 =
* Choose button alignment, and share counts hidden until loaded

= 1.3.1 =
* Fixed bug with post counts both above and below posts

= 1.3.0 =
* Better control of where share buttons appear, and option to have share links in a popup

= 1.2.2 =
* Share count font sizes better match icon size

= 1.2.0 =
* Share counts load faster and look better

= 1.1.1 =
* Including page/post title in default tweet text is optional

= 1.1.0 =
* Allows you to choose the size of share and link buttons

= 1.0.10 =
* Added RSS link button option
* Fixed problem with special characters in titles appearing wrongly in the Twitter share

= 1.0.9 =
* Added YouTube link button option

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

* Adding the ability to share/link to VK, XING, Flipboard, Goodreads and 500px
* Additional options for layout and style of the buttons
* Shortcode options for more manual control of the buttons
* (Anything else?  You tell me!)