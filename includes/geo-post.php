<?php
if(!class_exists('WP_GeoPost'))
{
	class WP_GeoPost
	{
		/**
		 * Construct the Geo Post single object
		 */
		public function __construct()
		{
			// register actions
			add_action('init', array($this, 'init'));
			add_action('admin_init', array($this, 'admin_init'));
	    } // END public function __construct()

		/**
		 * hook into WP's init action hook
		 */
		public function init()
		{
		    // on geo post save callback
			add_action('save_post', array($this, 'save_post'));
		} // END public static function activate

		/**
		 * hook into WP's admin_init action hook
		 */
		public function admin_init()
		{
			// Add metaboxes
			add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
		} // END public static function activate

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
						array($this, 'add_inner_meta_boxes'),
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
			include(sprintf("%s/../templates/geo_metabox.php", dirname(__FILE__)));			
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
    } // END class WP_GeoPost
} // if(!class_exists('WP_GeoPost'))