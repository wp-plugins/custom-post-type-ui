=== Custom Post Type UI ===
Contributors: williamsba1
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3084056
Tags: custom post types, CMS, post, types, cck, taxonomy, tax
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 0.4.1

Admin UI for creating custom post types and custom taxonomies in WordPress

== Description ==

This plugin provides an easy to use interface to create and administer custom post types in WordPress.  Plugin can also create custom taxonomies.  This plugin is created for WordPress 3.0.  

You can easily install WP 3.0 beta using the WordPress Beta Tester plugin located here: http://wordpress.org/extend/plugins/wordpress-beta-tester/

Below is a short example video showing Custom Post Type UI in action!
[vimeo http://vimeo.com/10187055]

== Screenshots ==

1. Create a custom post type
2. Create a custom taxonomy
3. Custom post type and taxonomies are automatically added to your admin menus
4. Easily view and edit existing custom post types
5. Custom taxonomies are automatically added to your content type screens

== Changelog ==

= 0.4.1 =
* Fixed bug with REWRITE and QUERY_VAR values not executing correctly
* Set REWRITE and QUERY_VAR values to True by default

= 0.4 =
* Default view now hides advanced options
* Get Code link to easily copy/paste code used to create custom post types and taxonomies
* Added support for 'author' and 'page-attributes' in CPT Supports field

= 0.3.1 =
* Fixed multiple warnings and errors

= 0.3 =
* added new menu/submenus for individual sections
* added support for 'title' and 'editor' in CPT Supports field
* added Singular Label for custom taxonomies (props sleary)

= 0.2.1 =
* Set default Query Var setting to False

= 0.2 =
* Added support for creating custom taxonomies
* Increased internationalization support
* Fixed siteurl bug

= 0.1.2 =
* Fixed a bug where default values were incorrect

= 0.1.1 =
* Fixed a bunch of warnings

= 0.1 =
* First beta release

== Upgrade Notice ==

= 0.4.1 =
* Fixed bug with REWRITE and QUERY_VAR values not executing correctly

= 0.4 =
* Default view now hides advanced options
* Get Code link to easily copy/paste code used to create custom post types and taxonomies
* Added support for 'author' and 'page-attributes' in CPT Supports field

= 0.3.1 =
* Fixed multiple warnings and errors

= 0.3 =
* added new menu/submenus for individual sections
* added support for 'title' and 'editor' in CPT Supports field
* added Singular Label for custom taxonomies (props sleary)

= 0.2.1 =
* Set default Query Var setting to False

= 0.2 =
* Fixed the siteurl bug
* Added support for creating custom taxonomies

= 0.1.2 =
* Fixed a bug where default values were incorrect

= 0.1.1 =
* Fixed a bunch of warnings

= 0.1 =
* First beta release

== Installation ==

1. Upload the Custom Post Type UI folder to the plugins directory in your WordPress installation
2. Activate the plugin
3. Navigate to Settings > Custom Post Type UI

That's it! Now you can easily start creating custom post types and taxonomies in WordPress

Upgrading

If you are upgrading from a version prior to v0.3 you will need to delete and recreate any custom taxonomies that you previously created.  You will NOT lose any content added to those custom taxonomies.

== Frequently Asked Questions ==

= Will this work in previous version of WordPress =

The register_post_type function was added in WordPress 2.9 so technically it should work in 2.9, but there is no admin menu UI so all post types are created and used behind the scenes.

= I'm getting an error: Undefined offset =

v0.3 reworked how custom taxonomies are stored.  You need to delete your current taxonomies and recreate them in the new version for this error to go away.

== Plugin Support ==
[Custom Post Type UI Support](http://webdevstudios.com/support/forum/custom-post-type-ui/ "WordPress Plugins and Support Services")