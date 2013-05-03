<?php
if(!class_exists('WP_GeoPostsSettings'))
{
	class WP_GeoPostsSettings
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// register actions
            add_action('admin_init', array(&$this, 'admin_init'));
        	add_action('admin_menu', array(&$this, 'add_menu'));
		} // END public function __construct
		
        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {
        	// register the plugin's settings
			register_setting('wpgeoposts-group', 'api_key');
			register_setting('wpgeoposts-group', 'geo_post_types');
        } // END public static function activate
        
        /**
         * add a menu
         */		
        public function add_menu()
        {
            // Add a page to manage this plugin's settings
        	add_options_page(
        	    'WP GeoPosts Settings', 
        	    'WP GeoPosts', 
        	    'manage_options', 
        	    'wp_geo_posts', 
        	    array(&$this, 'plugin_settings_page')
        	);
        } // END public function add_menu()
    
        /**
         * Menu Callback
         */		
        public function plugin_settings_page()
        {
        	if(!current_user_can('manage_options'))
        	{
        		wp_die(__('You do not have sufficient permissions to access this page.'));
        	}
	
			// Get the default value for the select box
			$args = apply_filters( 'WP_GeoPostsSettings/plugin_settings_page/get_post_types', array(
				'public'   => true,
				//'_builtin' => true
			));
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
        	include(sprintf("%s/../templates/settings.php", dirname(__FILE__)));
        } // END public function plugin_settings_page()
    } // END class WP_GeoPostsSettings
} // END if(!class_exists('WP_GeoPostsSettings'))
