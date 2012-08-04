<div class="wrap">
<h2>WP GeoPosts</h2>
<form method="post" action="options.php"> 
<?php @settings_fields('wpgeoposts-group'); ?>
<?php @do_settings_fields('wpgeoposts-group'); ?>

<table class="form-table">  
  <tr valign="top">
  	<th scope="row"><label for="api_key">Google Maps API Key</label></th>
  	<td>
			<input type="text" name="api_key" id="api_key" value="<?php echo get_option('api_key'); ?>" />
			<p><em>Go to <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key">https://developers.google.com/maps/documentation/javascript/tutorial#api_key</a> to obtain an API key.</em></p>
		</td>
  </tr>
	<tr valign="top">
		<th scope="row" colspan="2">Apply geo fields to which post types?</th>
	</tr>
	<tr valign="top">
  	<th scope="row"><label for="post_type_bank">Post Types</label></th>
  	<td>
			<table>
				<tr>
					<td>
						<select multiple size="10" id="post_type_bank" name="post_type_bank" style="width:150px;">
						<?php foreach($post_type_bank as $post_type) : ?>
							<option value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option>
						<?php endforeach; ?>
						</select>
					</td>
					<td align="center" valign="middle">
						<input type="button" value="->" id="select_post_type">
						<br />
						<input type="button" value="<-" id="deselect_post_type">
					</td>
					<td>
						<select multiple size="10" name="post_type_bank_selected" id="post_type_bank_selected" style="width:150px;">
						<?php foreach($geo_post_types as $post_type) : ?>
							<option value="<?php echo $post_type; ?>" selected="selected"><?php echo $post_type; ?></option>
						<?php endforeach; ?>							
						</select>
					</td>
				</tr>
			</table>
			<input type="hidden" name="geo_post_types" id="geo_post_types" value='<?php echo get_option('geo_post_types'); ?>' />
			<p><em>Move elements from one dropdown to the other.</em></p>
		</td>
  </tr>
</table>
    
<?php @submit_button(); ?>
</form>
</div>
<script>
jQuery(document).ready(
	function()
	{
		jQuery('#select_post_type').click(	
			function() 
			{
				jQuery('#post_type_bank option:selected').each(
					function()
					{
						var option = jQuery(this).clone();
						jQuery('#post_type_bank_selected').append(option);
						jQuery(this).remove();
						rebuild();
					}
				);
			}
		);

		jQuery('#deselect_post_type').click(	
			function() 
			{
				jQuery('#post_type_bank_selected option:selected').each(
					function()
					{
						var option = jQuery(this).clone();
						jQuery('#post_type_bank').append(option);
						jQuery(this).remove();
						rebuild();
					}
				);
			}
		);
	}
);

function rebuild()
{
	var selected_options = [];
	jQuery('#post_type_bank_selected option').each(
		function()
		{
			selected_options.push(jQuery(this).val());
		}
	);
	jQuery('#geo_post_types').val(JSON.stringify(selected_options));
}
</script>