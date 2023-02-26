[DEPRECATED] - Please do not use this repo as-is

Notes: 

- Google puts rate limiting on the host IP, this solution may not work well on shared hosts
- The google geocoding api has changed

wp-geo-posts
============

A simple Wordpress plugin for adding geographic data to posts.

#### Features

1. Adds `location`, `latitude`, and `longitude` meta + metaboxes to any content type.
2. Provides an easy to use interface for selecting which content types to apply the above meta values. *Note: this allows selection of built in types: page and post as well as any registered custom post types.*
3. Provides `WP_GeoQuery` an extended `WP_Query` class for doing distance based and geo-aware queries.
4. Has support for `within radius` option to WP_GeoQuery

##### Coming Soon!

* `Get Directions` link (utilizing Google Maps)
* Custom Markers by post type.
* Shortags for:
 * Static Map - show one or more posts on a static map
 * Dynamic Map
  * Option to show radius as overlay
  * Show one or more posts

#### Installation

1. Upload the entire `wp-geo-posts` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.

#### Setup

1. Click the `Settings` link on the plugin management page **OR** click the `WP GeoPosts` link from the Settings flyout menu.
2. Generate a Google Maps API Key and enter it into the provided text input. *Note: this is optional and used for Google Maps API calls.*
3. Select all of the content types that you wish to attach georelated content from the leftmost bank of choices and move them to the rightmost column.
4. Submit the Form by clicking `Save Changes`.

#### Usage

##### Metaboxes

For every post type selected on the plugin settings page. That type's add/edit screens will have an additional metabox automatically added. Metadata that is added to each record:

 - **Location** via `wp_gp_location`
 - **Latitude** via `wp_gp_latitude`
 - **Longitude** via `wp_gp_longitude`

Latitude and Longitude are readonly attributes of the metabox. Their values are automatically generated on  save via a call to Google's geoencoding api. 

##### WP_GeoQuery Usage

Make a geo-aware query against the posts table. `WP_GeoQuery` accepts all arguments that `WP_Query` takes. `latitude` and `longitude` are optional parameters. If passed, `distance` is calculated and returned with each result. In addition to the regular fields, each result returns `latitude`, `longitude`, and `location`.

```php
<?php
$query = new WP_GeoQuery(array(
  'latitude' => '37.5160', // User's Latitude (optional)
  'longitude' => '-77.5005', // User's Longitude (optional)
  'radius' => 25, // Radius to select for in miles (optional)
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
