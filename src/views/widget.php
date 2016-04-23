<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'crafty-social-buttons'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<p>
	<label for="<?php echo $this->get_field_id('buttonType'); ?>">Type:</label>

	<select class="widefat" 
    	    id="<?php echo $this->get_field_id('buttonType'); ?>" 
            name="<?php echo $this->get_field_name('buttonType'); ?>">
    	<option <?php echo selected('csblink', $buttonType); ?> value="csblink">Link Buttons</option>
    	<option <?php echo selected('csbshare', $buttonType); ?> value="csbshare">Share Buttons</option>
	</select>
</p>

<p class="description">
	To configure which services are displayed by this widget, please visit the 
    <a href="options-general.php?page=crafty-social-buttons">Crafty Social Buttons options page</a>.
 </p>
