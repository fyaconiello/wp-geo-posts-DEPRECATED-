<table>  
  <tr valign="top">
		<th class="metabox_label_column"><label for="wp_gp_location"><?php echo _e("Location", 'wp_gp_location_label'); ?></label></th>
		<td colspan="3"><input type="text" id="wp_gp_location" name="wp_gp_location" value="<?php echo get_post_meta($post->ID,'wp_gp_location',true); ?>" width="300px;" /></td>
	<tr>
	<tr>
		<th class="metabox_label_column"><label for="wp_gp_latitude"><?php echo _e("Latitude", 'wp_gp_latitude_label'); ?></label></th>
		<td><input readonly="readonly" type="text" id="wp_gp_latitude" name="wp_gp_latitude" value="<?php echo get_post_meta($post->ID,'wp_gp_latitude',true); ?>" /></td>
		<th class="metabox_label_column"><label for="wp_gp_longitude"><?php echo _e("Longitude", 'wp_gp_longitude_label'); ?></label></th>
		<td><input readonly="readonly" type="text" id="wp_gp_longitude" name="wp_gp_longitude" value="<?php echo get_post_meta($post->ID,'wp_gp_longitude',true); ?>" /></td>
	</tr>
</table>	
<style>
#wp_gp_location 				{ width:100%; }
.metabox_label_column 	{ width:125px; text-align:left; }
.metabox_label_column label	{ padding-left:15px; }
</style>