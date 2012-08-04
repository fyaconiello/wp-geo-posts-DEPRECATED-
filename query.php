<?php
if(!class_exists('WP_GeoQuery'))
{
	/**
	 * Extends WP_Query to do geographic searches
	 */
	class WP_GeoQuery extends WP_Query
	{
		private $_search_latitude = NULL;
		private $_search_longitude = NULL;
		
		/**
		 * Constructor - adds necessary filters to extend Query hooks
		 */
		public function __construct($args = array())
		{
			// Extract Latitude
			if(!empty($args['latitude']))
			{
				$this->_search_latitude = $args['latitude'];
			}
			// Extract Longitude
			if(!empty($args['longitude']))
			{
				$this->_search_longitude = $args['longitude'];
			}
			// unset lat/long
			unset($args['latitude'], $args['longitude']);
			
			add_filter('posts_fields', array(&$this, 'posts_fields'), 10, 2);
			add_filter('posts_join', array(&$this, 'posts_join'), 10, 2);
			add_filter('posts_where', array(&$this, 'posts_where'), 10, 2);
			add_filter('posts_orderby', array(&$this, 'posts_orderby'), 10, 2);

			parent::query($args);
		} // END public function __construct($args = array())

		/**
		 * Selects the distance from a haversine formula
		 */		
		public function posts_fields($fields)
		{
			global $wpdb;
      
			if(!empty($this->_search_latitude) && !empty($this->_search_longitude))
			{
				$fields .= sprintf(", ( 3959 * acos( 
						cos( radians(%s) ) * 
						cos( radians( latitude.meta_value ) ) * 
						cos( radians( longitude.meta_value ) - radians(%s) ) + 
						sin( radians(%s) ) * 
						sin( radians( latitude.meta_value ) ) 
					) ) AS distance ", $this->_search_latitude, $this->_search_longitude, $this->_search_latitude);	
			}
			
			$fields .= ", latitude.meta_value AS latitude ";
			$fields .= ", longitude.meta_value AS longitude ";
			$fields .= ", location.meta_value AS location ";
			
			return $fields;
		} // END public function posts_join($join, $query)

		/**
		 * Makes joins as necessary in order to select lat/long metadata
		 */		
		public function posts_join($join, $query)
		{
			global $wpdb;
      
			$join .= " INNER JOIN {$wpdb->postmeta} AS latitude ON {$wpdb->posts}.ID = latitude.post_id ";
			$join .= " INNER JOIN {$wpdb->postmeta} AS longitude ON {$wpdb->posts}.ID = longitude.post_id ";
			$join .= " INNER JOIN {$wpdb->postmeta} AS location ON {$wpdb->posts}.ID = location.post_id ";
			
			return $join;
		} // END public function posts_join($join, $query)
		
		/**
		 * Adds where clauses to compliment joins
		 */		
		public function posts_where($where)
		{
			$where .= ' AND latitude.meta_key="wp_gp_latitude" ';
			$where .= ' AND longitude.meta_key="wp_gp_longitude" ';
			$where .= ' AND location.meta_key="wp_gp_location" ';
			
			return $where;
		} // END public function posts_where($where)
		
		/**
		 * Adds where clauses to compliment joins
		 */	
		public function posts_orderby($orderby)
		{
			if(!empty($this->_search_latitude) && !empty($this->_search_longitude))
			{
				$orderby = " distance ASC, " . $orderby;
			}
			
			return $orderby;
		} // END public function posts_orderby($orderby)
	}
}