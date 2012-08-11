<?php
/*
Plugin Name: WP GeoPosts
Plugin URI: https://github.com/fyaconiello/wp-geo-posts
Description: A simple wordpress plugin for adding geodata to posts
Version: 1.0
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


if(!class_exists('WP_Geo_Posts'))
{
	class WP_Geo_Posts
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// register actions
			add_action('init', array(&$this, 'init'));
			add_action('admin_init', array(&$this, 'admin_init'));
			add_action('admin_menu', array(&$this, 'add_menu'));

			# Save the post
			#add_action('post_updated', array(&$this, 'save_post'));
			#add_action('save_post', array(&$this, 'save_post'));
			#add_action('publish_post', array(&$this, 'save_post'));
			#add_action('edit_page_form', array(&$this, 'save_post'));
		} // END public function __construct

		/**
		 * hook into WP's add_meta_boxes action hook
		 */
		public function add_meta_boxes()
		{
			$geo_post_types = get_option('geo_post_types');
			if(!empty($geo_post_types))
			{
				// Get all of the selected post types
				$geo_post_types = json_decode($geo_post_types);
				foreach($geo_post_types as $post_type)
				{
					// Add this metabox to every selected post
					add_meta_box( 
						'id_wp_geo_posts_section',
						'Geographic Information',
						array(&$this, 'add_inner_meta_boxes'),
						$post_type
		    	);					
				} // END foreach($geo_post_types as $post_type)
			} // END if(!empty($geo_post_types))
		} // END public function add_meta_boxes()
		
		/**
		 * called off of the add meta box
		 */		
		public function add_inner_meta_boxes($post)
		{
			// Render the settings template
			include(sprintf("%s/templates/geo_metabox.php", dirname(__FILE__)));			
		} // END public function add_inner_meta_boxes($post)

		/**
		 * Save the Latitude and Longitude of for this location
		 */
		public function save_post($post_id)
		{
			global $wpdb;

		  // verify if this is an auto save routine. 
		  // If it is our form has not been submitted, so we dont want to do anything
		  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		  {
				return;
		  }

		  // Check permissions
		  if('page' == $_POST['post_type']) 
		  {
		    if(!current_user_can('edit_page', $post_id))
		    {
					return;
		    }
		  }
		  else
		  {
		    if(!current_user_can('edit_post', $post_id))
		    {
					return;
		    }
		  }
  
		  // OK, we're authenticated: we need to find and save the data
			$geo_post_types = get_option('geo_post_types');
			if(!empty($geo_post_types))
			{
				// Get all of the selected post types
				$geo_post_types = json_decode($geo_post_types);
			  if(in_array($_POST['post_type'], $geo_post_types))
			  {
					if(empty($_POST['wp_gp_location']))
					{
						update_post_meta($post_id, 'wp_gp_location', '');
						update_post_meta($post_id, 'wp_gp_latitude', '');
						update_post_meta($post_id, 'wp_gp_longitude', '');
					}
					// If a location was posted that is different from the previously saved location
					elseif(!empty($_POST['wp_gp_location']) && $_POST['wp_gp_location'] != get_post_meta($post_id, 'wp_gp_location', true))
					{
						$location = $_POST['wp_gp_location'];
						// Save the Location
						update_post_meta($post_id, 'wp_gp_location', $location);
						
						// Try to geolocate
						$obj = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($location) . '&sensor=true'));
						
						// If successful
						if($obj->status == 'OK')
						{
							try
							{
								if(empty($latitude))
								{
									$latitude 	= $obj->results[0]->geometry->location->lat;
									update_post_meta($post_id, 'wp_gp_latitude', (string)$latitude);
								}
				
								if(empty($longitude))
								{
									$longitude 	= $obj->results[0]->geometry->location->lng;
									update_post_meta($post_id, 'wp_gp_longitude', (string)$longitude);
								}
							}
							catch(Exception $e) {/*die($e->getMessage());*/}
						} // END if($obj->status == 'OK')
					} // END if(!empty($_POST['wp_gp_location']) && $_POST['wp_gp_location'] != get_post_meta($post_id, 'wp_gp_location', true))
				} // END if(in_array($_POST['post_type'], $geo_post_types))
		  } // END if(!empty($geo_post_types))
		} // END public function save_post($post_id)

		/**
		 * hook into WP's init action hook
		 */
		public function init()
		{
			add_action('save_post', array(&$this, 'save_post'));
			// Import WP_GeoQuery class
			require_once(sprintf("%s/query.php", dirname(__FILE__)));
		} // END public static function activate

		/**
		 * hook into WP's admin_init action hook
		 */
		public function admin_init()
		{
			// Set up the settings for this plugin
			$this->init_settings();
			
			// Add metaboxes
			add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
		} // END public static function activate
		
		/**
		 * add a menu
		 */		
		public function add_menu()
		{
			add_options_page('WP GeoPosts Settings', 'WP GeoPosts', 'manage_options', 'wp_geo_posts', array(&$this, 'plugin_settings_page'));
		} // END public function add_menu()
		
		/**
		 * Initialize some custom settings
		 */		
		public function init_settings()
		{
			// register the settings for this plugin
			register_setting('wpgeoposts-group', 'api_key');
			register_setting('wpgeoposts-group', 'geo_post_types');
		} // END public function init_custom_settings()
		
		/**
		 * Menu Callback
		 */		
		public function plugin_settings_page()
		{
			if(!current_user_can('manage_options'))
			{
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}
			
			// Get the defaul value for the select box
			$args = array(
				'public'   => true,
				'_builtin' => true
			);
			$post_type_bank	= get_post_types($args, 'names', 'and');
			if(($key = array_search('attachment', $post_type_bank)) !== false)
			{
				unset($post_type_bank[$key]);
			}
			
			$geo_post_types = get_option('geo_post_types');
			if(!empty($geo_post_types))
			{
				$geo_post_types = json_decode($geo_post_types);
				foreach($geo_post_types as $post_type)
				{
					if(($key = array_search($post_type, $post_type_bank)) !== false)
					{
						unset($post_type_bank[$key]);
					}
				}				
			}
			
			// Render the settings template
			include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
		} // END public function plugin_settings_page()
		
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
	} // END class WP_Geo_Posts
} // END if(!class_exists('WP_Geo_Posts'))

if(class_exists('WP_Geo_Posts'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('WP_Geo_Posts', 'activate'));
	register_deactivation_hook(__FILE__, array('WP_Geo_Posts', 'deactivate'));
	
	// instantiate the plugin class
	$wp_geo_posts_plugin = new WP_Geo_Posts();
	
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
