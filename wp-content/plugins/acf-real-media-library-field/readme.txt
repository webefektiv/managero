=== ACF Real Media Library Field ===
Contributors: mguenter, jondennis
Tags: media library folders, folders, media categories, media folders, media category, media folder, rml, media library, real media library
Stable tag: trunk
Requires at least: 4.0
Tested up to: 5.0
License: GPLv2

Add field type to select a media folder (Real Media Library extension).

== Description ==

This plugin is an extension for the [WP Real Media Library](https://codecanyon.net/item/wordpress-real-media-library-media-categories-folders/13155134) plugin that allows you to create folders in media library.
This plugin needs version >= 4.0 of the [Advanced Custom Fields](https://de.wordpress.org/plugins/advanced-custom-fields/) plugin.
This plugin needs version >= 2.7 of the WP Real Media Library plugin.

The following settings are available:

* Disable selection: Allows you to disable folder types for selection.
* Return format: RML Object or ID of the folder.

= Compatibility =

This ACF field type is compatible with:
* ACF 4
* ACF 5

Thanks to [Jon Dennis](http://www.theimageyard.co.uk/) who started with the development with this extension.

== Installation ==

1. Goto your wordpress backend
2. Navigate to Plugins > Add new
3. Search for "Advanced Custom Fields: Real Media Library Field"
4. "Install"

OR 

1. Copy the `acf-real_media_library` folder into your `wp-content/plugins` folder
2. Activate the Real Media Library plugin via the plugins admin page
3. Create a new field via ACF and select the Real Media Library type
4. Please refer to the description for more info regarding the field type settings

== Changelog ==

= 1.1.3 =
* Fixed bug with "Disable selection" (PHP warnings were generated)

= 1.1.2 =
* Fixed bug with CSS/JS resources
* Fixed bug with taxonomy field and RML field

= 1.1.1 =
* Improved the select boxes (better UX)

= 1.1.0 =
* Added option to select multiple folders
* Added compatibility for ACF 4

= 1.0.0 =
* Initial Release.