<?php
if ( ! defined( 'ABSPATH' ) ) exit;
add_action( 'add_meta_boxes', 'ecwmv_add_meta_box' );
function ecwmv_add_meta_box() {
	$add_custom_fields = array(	
								'ecwmv_event_date_time' => array('ecwmv_event_start_date','ecwmv_event_start_time','ecwmv_event_end_date','ecwmv_event_end_time'),
								'ecwmv_event_cost' => 'Event Cost',
								'ecwmv_event_location' 	=> array('ecwmv_event_location','ecwmv_city','ecwmv_state','ecwmv_country','ecwmv_lat','ecwmv_lng'),
								'ecwmv_organizer_name' 	=> 'Organizer Name',
								'ecwmv_organizer_phone' => 'Organizer Phone',
								'ecwmv_organizer_email' => 'Organizer Email',
								'ecwmv_organizer_website' => 'Organizer WebSite',
							  );
	$extra_fields = serialize($add_custom_fields);
	update_option('ecwmv_meta_fields', $extra_fields);
	add_meta_box(
			'ecwmv_meta_fields',
			__( 'Events Fields' ),
			ecwmv_create_field,
			'ecwmv-event',
			'normal',
			'default'
	);
}

function ecwmv_create_field( $post, $metabox  ) {
	wp_enqueue_script( 'jquery-timepicker-script' );
	wp_enqueue_script( 'bootstrap-datepicker-script' );
	wp_enqueue_script( 'datepair-script' );
	wp_enqueue_script( 'jquery-datepair-script' );
	wp_enqueue_style( 'jquery-timepicker-style' );
	wp_enqueue_style( 'bootstrap-datepicker-standalone-style' );
	wp_enqueue_style( 'ecwmv-style' );
	
	$meta_fields = unserialize(get_option('ecwmv_meta_fields',true));
	
	if(!empty($meta_fields)) {
		foreach ($meta_fields as $field_key => $field_name){
			wp_nonce_field( $field_key.'_nonce', $field_key.'_nonce' );
			
			if($field_key == 'ecwmv_event_date_time'){
			?>
				<div id="basicExample">
					<h4><?php echo esc_html('Event Start Date & Time*'); ?></h4>
				    <input type="text" class="date start required" name="ecwmv_event_start_date" data-date-format="yyyy/mm/dd" value="<?php echo get_post_meta($post->ID,'ecwmv_event_start_date',true); ?>"/>
				    <input type="text" class="time start required" name="ecwmv_event_start_time" value="<?php echo get_post_meta($post->ID,'ecwmv_event_start_time',true); ?>"/> 
				    <h4><?php echo esc_html('Event End Date & Time*'); ?></h4>
				    <input type="text" class="date end required" name="ecwmv_event_end_date" data-date-format="yyyy/mm/dd" value="<?php echo get_post_meta($post->ID,'ecwmv_event_end_date',true); ?>"/>
				    <input type="text" class="time end required" name="ecwmv_event_end_time" value="<?php echo get_post_meta($post->ID,'ecwmv_event_end_time',true); ?>"/>
				</div>
				
				<script>
				    jQuery(document).ready(function(){
				    	var basicExampleEl = document.getElementById('basicExample');
						var datepair = new Datepair(basicExampleEl);
					    jQuery('#basicExample .time').timepicker({
					        'showDuration': true,
					        'timeFormat': 'g:ia'
					    });
					    jQuery('#basicExample .date').datepicker({
					        'format': 'yyyy/m/d',
					        'autoclose': true
					    });
				    })
				</script>	
				
			<?php 	
			} elseif ($field_key == 'ecwmv_event_location'){
			?>
				<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>
				<?php include_once  plugin_dir_path(__FILE__).'js'.DIRECTORY_SEPARATOR.'location-map.php';?>
				<h4><label for="<?php echo $field_key; ?>"><?php echo esc_html('Event Location*'); ?></label></h4>
				<input style="width: 60%;" class="required" type="text" name="<?php echo $field_key; ?>" id="<?php echo $field_key; ?>" value="<?php echo get_post_meta($post->ID,$field_key,true); ?>">
				<div id="map-canvas" style="width: 400px;height:300px"></div>
				<div class="map-location-details">
					<p>
						<label for="ecwmv_city"><?php echo esc_html('City :'); ?></label>
						<input type="text" name="ecwmv_city" id="ecwmv_city" value="<?php echo get_post_meta($post->ID,'ecwmv_city',true); ?>">
					</p>
					<p>
						<label for="ecwmv_state"><?php echo esc_html('State :'); ?></label>
						<input type="text" name="ecwmv_state" id="ecwmv_state" value="<?php echo get_post_meta($post->ID,'ecwmv_state',true); ?>">
					</p>
					<p>
						<label for="ecwmv_country"><?php echo esc_html('Country :'); ?></label>
						<input type="text" name="ecwmv_country" id="ecwmv_country" value="<?php echo get_post_meta($post->ID,'ecwmv_country',true); ?>">
					</p>
					<p>
						<label for="ecwmv_lat"><?php echo esc_html('Latitude* :'); ?></label>
						<input class="required" type="text" name="ecwmv_lat" id="ecwmv_lat" value="<?php echo get_post_meta($post->ID,'ecwmv_lat',true); ?>">
					</p>
					<p>
						<label for="ecwmv_lng"><?php echo esc_html('Longitude* :'); ?></label>
						<input class="required" type="text" name="ecwmv_lng" id="ecwmv_lng" value="<?php echo get_post_meta($post->ID,'ecwmv_lng',true); ?>">
					</p>
					<p id="ecwmv-temp-place" style="display:none!important"></p>
				</div>	
				<div class="clear"></div>
			<?php 
			} else {
		?>
			<label class="optional-fields" for="<?php echo $field_key; ?>"><?php echo esc_html($field_name); ?></label>
			<input class="optional-input-fields" type="text" name="<?php echo $field_key; ?>" id="<?php echo $field_key; ?>" value="<?php echo get_post_meta($post->ID,$field_key,true); ?>">
			<div class="clear"></div>
		<?php
			}
		}
	}
	?>

	<script>
		jQuery(document).ready(function(){
			jQuery("#publish").click(function(){
				var status = validate_fields();
				console.log(status);
				if(status == 'no') return false;
				else return true;
			})
		})
		function validate_fields() { 
			var status = 'yes';
			jQuery(".req-error").remove();
			jQuery( ".required" ).each(function() {
				if( jQuery( this ).val() == '' ) { 
					jQuery( this ).after('<span class="req-error" style="color:red">This field is required</span>');
					status = 'no';
				}
			});
			if(status == 'no') alert("Please fill the required fields.");
			return status;
		}
	</script>
	<?php 
	
}

add_action( 'save_post', 'ecwmv_save_fields' );
function ecwmv_save_fields( $post_id ) {
	$meta_fields = unserialize(get_option('ecwmv_meta_fields',true));
	
	if(!empty($meta_fields)) {
		foreach ($meta_fields as $field_key => $field_name){
		
			if($field_key == 'ecwmv_event_date_time') {
				$all_date_time_fields = $meta_fields["$field_key"];//array('ecwmv_event_start_date','ecwmv_event_start_time','ecwmv_event_end_date','ecwmv_event_end_time');
				foreach ($all_date_time_fields as $date_fields){
					if ( ! current_user_can( 'edit_post', $post_id ) ) return;
					if ( isset( $_POST["$date_fields"] ) )
						update_post_meta( $post_id, $date_fields, sanitize_text_field( $_POST["$date_fields"] ) );
				}
			} elseif ($field_key == 'ecwmv_event_location'){
				$all_location_fields = $meta_fields["$field_key"];//array('ecwmv_event_start_date','ecwmv_event_start_time','ecwmv_event_end_date','ecwmv_event_end_time');
				foreach ($all_location_fields as $locate_fields){
					if ( ! current_user_can( 'edit_post', $post_id ) ) return;
					if ( isset( $_POST["$locate_fields"] ) )
						update_post_meta( $post_id, $locate_fields, sanitize_text_field( $_POST["$locate_fields"] ) );
				}
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) ) return;
				if ( isset( $_POST["$field_key"] ) )
					update_post_meta( $post_id, $field_key, sanitize_text_field( $_POST["$field_key"] ) );
			}
		}
		
	}
}
