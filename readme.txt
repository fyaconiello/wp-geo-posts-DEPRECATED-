=== Plugin Name ===
Contributors: fyaconiello
Donate link: http://fyaconiello.github.com/wp-geo-posts/
Tags: location, geo, metabox, distance search,  distance, search,  
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: trunk
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple Wordpress plugin for adding geographic data to posts.

== Description ==

####Features

1. Adds `location`, `latitude`, and `longitude` meta + metaboxes to any content type.
2. Provides an easy to use interface for selecting which content types to apply the above meta values. *Note: this allows selection of built in types: page and post as well as any registered custom post types.*
3. Provides `WP_GeoQuery` an extended `WP_Query` class for doing distance based and geo-aware queries.

#####Coming Soon!

* Add support for `within radius` option to WP_GeoQuery
* HTML5 geolocation of visitors, with a fallback to `Change Location` (stored as COOKIE)
* Custom Markers by post type.
* Shortags for:
 * `Get Directions` link (utilizing Google Maps)
 * Static Map - show one or more posts on a static map
 * Dynamic Map - show one or more posts on a dynamic map
 * Option to show radius as overlay (for `within radius` calls)

== Installation ==

####Installation

1. Upload the entire `wp-geo-posts` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.

####Setup

1. Click the `Settings` link on the plugin management page **OR** click the `WP GeoPosts` link from the Settings flyout menu.
2. Generate a Google Maps API Key and enter it into the provided text input. *Note: this is optional and used for Google Maps API calls.*
3. Select all of the content types that you wish to attach georelated content from the leftmost bank of choices and move them to the rightmost column.
4. Submit the Form by clicking `Save Changes`.

== Frequently Asked Questions ==

= What are the post meta keys that your metabox adds? =

For every post type selected on the plugin settings page. That type's add/edit screens will have an additional metabox automatically added. Metadata that is added to each record:

 - **Location** via `wp_gp_location`
 - **Latitude** via `wp_gp_latitude`
 - **Longitude** via `wp_gp_longitude`

= How do I use WP_GeoQuery? (What is it for?) =

Make a geo-aware query against the posts table. `WP_GeoQuery` accepts all arguments that `WP_Query` takes. `latitude` and `longitude` are optional parameters. If passed, `distance` is calculated and returned with each result. In addition to the regular fields, each result returns `latitude`, `longitude`, and `location`.

```php
<?php
$query = new WP_GeoQuery(array(
  'latitude' => '37.5160', // User's Latitude (optional)
  'longitude' => '-77.5005', // User's Longitude (optional)
  'posts_per_page' => 25, // Any regular options available via WP_Query
));
foreach($query->posts as $post)
{
	echo " {$post->post_title}<br />\n";

	// returned only if latitude and longitude are passed into WP_GeoQuery
	echo " {$post->distance}<br />\n";

	// Always returned by WP_GeoQuery
	echo " {$post->location}<br />\n";
	echo " {$post->latitude}<br />\n";
	echo " {$post->longitude}<br />\n";
	echo "<br />\n";
}
?>
```

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 1.0 =
* Initial Release


== Upgrade Notice ==

= 1.0 =
Feature release