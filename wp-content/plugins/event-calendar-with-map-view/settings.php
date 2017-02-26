<?php 
 if ( ! defined( 'ABSPATH' ) ) exit;
 wp_enqueue_style('ecwmv-style');
?>
<?php 
	$nonce = sanitize_text_field($_REQUEST['setting_nonce']);
	
	if(isset($_POST['update']) && wp_verify_nonce( $nonce, 'setting_nonce' )){
		
		$view_displays = $_POST['view-display'];
		if (is_array($view_displays)) {
			foreach ($view_displays as $view_display) {
				$views[] = esc_attr($view_display);
			}
		}
	
		$theme_style 	= sanitize_text_field($_POST['theme-style']);
		$curr_symbol 	= sanitize_text_field($_POST['curr-symbol']);
		
		$map_zoom 		= sanitize_text_field(intval($_POST['map-zoom']));
		$map_center_lat	= sanitize_text_field(floatval($_POST['map-center-lat']));
		$map_center_lng	= sanitize_text_field(floatval($_POST['map-center-lng']));
		
		update_option('ecwmv_theme_style', $theme_style);
		update_option('ecwmv_curr_symbol', $curr_symbol);
		update_option('ecwmv_view_display', serialize($views));
		update_option('ecwmv_map_zoom', $map_zoom);
		update_option('ecwmv_map_center_lat', $map_center_lat);
		update_option('ecwmv_map_center_lng', $map_center_lng);
		echo '<div id="message" class="updated notice notice-success is-dismissible below-h2">
				<p>Events Settings updated successfully.</p>
			  </div> ';
	} 
	if(!empty($_REQUEST['reset'])){
		update_option('ecwmv_theme_style', 'blue');
		update_option('ecwmv_curr_symbol', '$');
		update_option('ecwmv_view_display', serialize(array('cal','list','grid','map')));
		update_option('ecwmv_map_zoom', '2');
		update_option('ecwmv_map_center_lat', '27.0000');
		update_option('ecwmv_map_center_lng', '17.0000');
		update_option('ecwmv_default_settings','1');
		echo '<div id="message" class="updated notice notice-success is-dismissible below-h2">
				<p>Events Settings updated successfully.</p>
			  </div> ';
	}
	
?>

<div class="wrap">
	<div class="events-sttings">
		<h1><?php echo esc_html('Events Settings'); ?></h1>
        <div class="event-setting-fields">
	        <form action="?post_type=ecwmv-event&page=ecwmv-settings" method="post" name="event-setting-form" id="event-setting-form">
	            <p>
	                <label for="theme-style"><?php echo esc_html('Theme Style:'); ?></label>
	                <span>
						<input type="radio" name="theme-style" <?php if(get_option('ecwmv_theme_style') == 'blue'){ echo 'checked="checked"'; } ?> value="blue"><b id="blue"></b>
					</span>
					<span>
						<input type="radio" name="theme-style" <?php if(get_option('ecwmv_theme_style') == 'red'){ echo 'checked="checked"'; } ?> value="red"><b id="red"></b>
					</span>
					<span>
						<input type="radio" name="theme-style" <?php if(get_option('ecwmv_theme_style') == 'green'){ echo 'checked="checked"'; } ?> value="green"><b id="green"></b>
					</span>
					<span>
						<input type="radio" name="theme-style" <?php if(get_option('ecwmv_theme_style') == 'pink'){ echo 'checked="checked"'; } ?> value="pink"><b id="pink"></b>
					</span>
					<span>
						<input type="radio" name="theme-style" <?php if(get_option('ecwmv_theme_style') == 'grey'){ echo 'checked="checked"'; } ?> value="grey"><b id="grey"></b>
					</span>
	            </p>
	            <p>
	                <label for="curr-symbol"><?php echo esc_html('Currency Symbol:')?></label>
	                <input name="curr-symbol" id="curr-symbol" type="text" value="<?php echo get_option('ecwmv_curr_symbol'); ?>" />
	            </p>
	            <?php 
	            	if(get_option('ecwmv_view_display')){
	            		$views_array = unserialize(get_option('ecwmv_view_display')); 
	            	}
	            ?>
	            <p>
	                <label for="views-display"><?php echo esc_html('Views Display:'); ?></label>
	                <span>
						<input type="checkbox" name="view-display[]" value="cal" <?php if(in_array('cal', $views_array)){ echo 'checked="checked"'; }?>><b>Calendar</b>
					</span>
					<span>
						<input type="checkbox" name="view-display[]" value="list" <?php if(in_array('list', $views_array)){ echo 'checked="checked"'; }?>><b>List</b>
					</span>
					<span>
						<input type="checkbox" name="view-display[]" value="grid" <?php if(in_array('grid', $views_array)){ echo 'checked="checked"'; }?>><b>Grid</b>
					</span>
					<span>
						<input type="checkbox" name="view-display[]" value="map" <?php if(in_array('map', $views_array)){ echo 'checked="checked"'; }?>><b>Map</b>
					</span>
	            </p>
	            <p>
	                <label for="map-zoom"><?php echo esc_html('Events Map Zoom:'); ?></label>
	                <input name="map-zoom" id="map-zoom" type="number" min="2" max="20" value="<?php echo get_option('ecwmv_map_zoom'); ?>"/>
	            </p>
	            <p>
	                <label for="map-center-lat"><?php echo esc_html('Events Map Center Latitude:'); ?></label>
	                <input name="map-center-lat" id="map-center-lat" type="number" step="any" value="<?php echo get_option('ecwmv_map_center_lat'); ?>" />
	            </p>
	            <p>
	                <label for="map-center-lng"><?php echo esc_html('Events Map center Longitude:'); ?></label>
	                <input name="map-center-lng" id="map-center-lng" type="number" step="any" value="<?php echo get_option('ecwmv_map_center_lng'); ?>" />
	            </p>
	            <input type="hidden" name="setting_nonce" value="<?php echo wp_create_nonce( 'setting_nonce' );?>" />
	            <p class="submit">
	            	<label>&nbsp;</label>
	            	<input type="submit" class="button button-primary" name="update" value="Submit" />
	            </p>
	        </form>
	        <p><a href="?post_type=ecwmv-event&page=ecwmv-settings&reset=1"><?php echo esc_html('Reset Default Settings'); ?></a></p>
        </div>
    </div>
</div>