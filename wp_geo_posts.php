<?php
/*
Plugin Name: WP GeoPosts
Plugin URI: http://fyaconiello.github.com/wp-geo-posts/
Description: A simple wordpress plugin for adding geodata to posts
Version: 2.0
Author: Francis Yaconiello
Author URI: http://www.yaconiello.com
License: GPL2
*/
/*
Copyright 2012  Francis Yaconiello  (email : francis@yaconiello.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if(!class_exists('WP_GeoPosts'))
{
	class WP_GeoPosts
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
        	// Initialize Settings
            require_once(sprintf("%s/includes/settings.php", dirname(__FILE__)));
            $WP_GeoPostsSettings = new WP_GeoPostsSettings();
            
		    // Add Meta Boxes & Save callback to select post-types
		    require_once(sprintf("%s/includes/geo-post.php", dirname(__FILE__)));
		    $WP_GeoPost = new WP_GeoPost();
		    
		    // Import WP_GeoQuery class
			require_once(sprintf("%s/includes/geo-query.php", dirname(__FILE__)));
		} // END public function __construct
		
		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// Do nothing
		} // END public static function activate
		
		/**
		 * Deactivate the plugin
		 */		
		public static function deactivate()
		{
			// Do nothing
		} // END public static function deactivate
	} // END class WP_GeoPosts
} // END if(!class_exists('WP_GeoPosts'))

if(class_exists('WP_GeoPosts'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('WP_GeoPosts', 'activate'));
	register_deactivation_hook(__FILE__, array('WP_GeoPosts', 'deactivate'));
	
	// instantiate the plugin class
	$wp_geo_posts_plugin = new WP_GeoPosts();
	
	// Add a link to the settings page onto the plugin page
	if(isset($wp_geo_posts_plugin))
	{
		// Add the settings link to the plugins page
		function plugin_settings_link($links)
		{ 
		  $settings_link = '<a href="options-general.php?page=wp_geo_posts">Settings</a>'; 
		  array_unshift($links, $settings_link); 
		  return $links; 
		}

		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", 'plugin_settings_link');
	}		
}
