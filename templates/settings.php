<div class="wrap">
<h2>WP GeoPosts</h2>
<form method="post" action="options.php"> 
<?php @settings_fields('wpgeoposts-group'); ?>
<?php @do_settings_fields('wpgeoposts-group'); ?>

<table class="form-table">  
  <tr valign="top">
  	<th scope="row">API Key</th>
  	<td><input type="text" name="apiKey" value="<?php echo get_option('apiKey'); ?>" /></td>
  </tr>
</table>
    
<?php @submit_button(); ?>
</form>
</div>