<?php

/**
 * Markup the event form.
 */ 
?>

<div class="aec aec-event-form">
	<form method="post" action="<?php echo aec_event_form_page_link(); ?>" class="form-vertical" enctype="multipart/form-data" data-toggle="validator" role="form">

		<!-- Event Title -->
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label for="aec-event-title" class="control-label"><?php _e( 'Event Title', 'another-events-calendar' ); ?></label>
                    <input type="text" name="title" class="form-control" id="aec-event-title" required value="<?php if( isset( $title ) ) echo esc_attr( $title ); ?>"/>
                </div>
        
                <!-- Event Description -->
                <div class="form-group">
                    <label><?php _e( 'Event Description', 'another-events-calendar' ); ?></label>
                    <?php 
						wp_editor( 
							isset( $description ) ? $description : '', 
							'description', 
							array(
								'media_buttons' => false,
								'quicktags'     => false,
								'editor_height' => 200
							)
						); 
					?>
                </div>
        	</div>
        </div>
           
        <!-- Event Details -->
        <div class="panel panel-default form-horizontal"> 
        	<div class="panel-heading"><?php _e( 'Event Details', 'another-events-calendar' ); ?></div>
            
            <div class="panel-body">
            
            	<div class="form-group">
                	<label class="col-sm-3 control-label"><?php _e( 'Select Category', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                    	<div class="aec-multi-checkbox">
                        	<?php
                            	$terms = get_terms( 'aec_categories', array( 'hide_empty' => false ) );
                                foreach( $terms as $term ) { 
									$checked = isset( $categories ) && in_array( $term->term_id, $categories ) ? 'checked="checked"' : '';
									printf( '<label><input type="checkbox" name="categories[]" value="%s" %s/>%s</label>', $term->name, $checked, $term->name );
								}								
							?>
                         </div>
                   	</div>
            	</div>
                
                <div class="form-group">
                	<label for="aec-upload-image" class="col-sm-3 control-label"><?php _e( 'Upload Image', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                   		<input type="file" name="image" id="aec-upload-image" />
                        <?php if( isset( $image ) ) : ?>
                        	<div id="aec-img-preview" class="aec-margin-top">
                        		<img src="<?php echo $image; ?>" />
                            	<a href="#" id="aec-img-delete" data-post_id="<?php echo $event_id; ?>" data-attachment_id="<?php echo $attachment_id; ?>">
									<?php _e( 'Delete', 'another-events-calendar' ); ?>
                            	</a>
                            </div>
                        <?php endif; ?>
                    </div>
              	</div>
                
               	<div class="form-group">
               		<label class="col-sm-3 control-label"><?php _e( 'Event Cost', 'another-events-calendar' ); ?>&nbsp;[<?php echo aec_get_currency(); ?>]</label>
                   	<div class="col-sm-9">
                    	<input type="text" name="cost" value="<?php if( isset( $cost ) ) echo esc_attr( $cost ); ?>" class="form-control" />
                    </div>
               </div>
          	
            </div>
     	</div>
                
       	<!-- Event Date & Time -->
       	<div class="panel panel-default form-horizontal">
       		<div class="panel-heading"><?php _e('Event Date & Time', 'another-events-calendar'); ?></div>
           		
            <div class="panel-body">
            
            	<div class="form-group">
                	<label for="aec-all-day-event" class="col-sm-3 control-label"><?php _e( 'All Day Event', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                    	<input type="checkbox" name="all_day_event" id="aec-all-day-event" value="1" <?php if( isset( $all_day_event ) ) checked( $all_day_event, 1 ); ?>/>
                    </div>
               	</div>
                
                <div class="form-group">
               		<label class="col-sm-3 control-label"><?php _e( 'Start Date & Time', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-5">
                        <input type="text" name="start_date" class="aec-date-picker form-control" value="<?php if( isset( $start_date ) ) echo esc_attr( $start_date ); ?>" />
                    </div>
                    <div class="col-sm-2">
                        <select name="start_hour" class="aec-event-time-fields form-control" style="display: none;">
                        <?php
							$start_hour = isset( $start_hour ) ? (int) $start_hour : 0;
                            for( $i = 0; $i <= 23; $i++ ) {
                                printf( '<option value="%d"%s>%02d</option>', $i, selected( $start_hour, $i ), $i );
                            }					
                        ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select name="start_min" class="aec-event-time-fields form-control" style="display: none;">
                        <?php
							$start_min = isset( $start_min ) ? (int) $start_min : 0;
                            for( $i = 0; $i <= 59; $i++ ) {
                                printf( '<option value="%d"%s>%02d</option>', $i, selected( $start_min, $i ), $i );
                            }					
                        ?>
                        </select>
                    </div>
               	</div>
                
                <div class="form-group">
                	<label class="col-sm-3 control-label"><?php _e( 'End Date & Time', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-5">
                    	<input type="text" name="end_date" class="aec-date-picker form-control" value="<?php if( isset( $end_date ) ) echo esc_attr( $end_date ); ?>" />
                   	</div>
                    <div class="col-sm-2">
                    	<select name="end_hour" class="aec-event-time-fields form-control" style="display: none;">
                        <?php
							$end_hour = isset( $end_hour ) ? (int) $end_hour : 0;
                        	for( $i = 0; $i <= 23; $i++ ) {
                            	printf( '<option value="%d"%s>%02d</option>', $i, selected( $end_hour, $i ), $i );
                           	}				
                        ?>
                        </select>
                   	</div>
                    <div class="col-sm-2">
                    	<select name="end_min" class="aec-event-time-fields form-control" style="display: none;">
                        <?php
							$end_min = isset( $end_min ) ? (int) $end_min : 0;
                        	for( $i = 0; $i <= 59; $i++ ) {
                            	printf( '<option value="%d"%s>%02d</option>', $i, selected( $end_min, $i ), $i );
                            }					
                      	?>
                        </select>
                 	</div> 
               	</div>
          	
            	<?php if( ! $event_id ) : ?>
                	<div class="form-group">
                        <label for="aec-recurring-event" class="col-sm-3 control-label"><?php _e( 'This is a recurring event', 'another-events-calendar' )?></label>
                        <div class="col-sm-9">
                            <input type="checkbox" name="recurring_event" id="aec-recurring-event" value="1" />
                        </div>
                    </div>
                    
                    <div class="form-group aec-recurring-event-fields" style="display: none;">
                        <label for="aec-recurrence" class="col-sm-3 control-label"><?php _e( 'Repeat type', 'another-events-calendar' )?></label>
                        <div class="col-sm-9 aec-recurring-events">
                        	<p>
                            	<?php $repeat_type = isset( $repeat_type ) ? $repeat_type : 'no_repeat'; ?>
                                <select id="aec-recurring-frequency" name="repeat_type" class="form-control">
                                    <option value="no_repeat"<?php selected( 'no_repeat', $repeat_type ); ?>><?php _e( 'No repeat', 'another-events-calendar' );?></option>
                                    <option value="daily"<?php selected( 'daily', $repeat_type ); ?>><?php _e( 'Daily', 'another-events-calendar' ); ?></option>
                                    <option value="weekly"<?php selected( 'weekly', $repeat_type ); ?>><?php _e( 'Weekly', 'another-events-calendar' ); ?></option>
                                    <option value="monthly"<?php selected( 'monthly', $repeat_type ); ?>><?php _e( 'Monthly', 'another-events-calendar' ); ?></option>
                                </select>
                            </p>
                            
                            <!-- Daily recurrence -->
                           	<div class="aec-recurring-settings aec-daily-recurrence" style="display: none;">
                            	<p>
                                	<?php _e( 'Repeat every', 'another-events-calendar' ); ?>&nbsp; 
                                    <input type="text" style="width: 200px;" name="repeat_days" value="<?php if( isset( $repeat_days ) ) echo esc_attr( $repeat_days ); ?>" />&nbsp; 
                                    <?php _e( 'days', 'another-events-calendar' ); ?>
                                </p>
                          	</div>
                            
                            <!-- Weekly recurrence -->
                            <div class="aec-recurring-settings aec-weekly-recurrence" style="display: none;">
                                <p>
                                    <?php _e( 'Repeat every', 'another-events-calendar' ); ?>&nbsp;
                                    <input type="text" style="width: 200px;" name="repeat_weeks" value="<?php if( isset( $repeat_weeks ) )  echo esc_attr( $repeat_weeks ); ?>" />&nbsp; 
                                    <?php _e( 'weeks', 'another-events-calendar' ); ?>
                                </p>
                                
                                <p>                          
                                    <?php _e( 'On Days', 'another-events-calendar' ); ?>&nbsp;
                                        
                                    <?php 
                                        $days = array(
                                            __( 'SUN', 'another-events-calendar' ),
                                            __( 'MON', 'another-events-calendar' ),
                                            __( 'TUE', 'another-events-calendar' ),
                                            __( 'WED', 'another-events-calendar' ),
                                            __( 'THU', 'another-events-calendar' ),
                                            __( 'FRI', 'another-events-calendar' ),
                                            __( 'SAT', 'another-events-calendar' )
                                        );
                                        
										$repeat_week_days = isset( $repeat_week_days ) ? $repeat_week_days : array();
                                        foreach( $days as $index => $day ) {
                                            $selected = in_array( $index, $repeat_week_days ) ? 'checked="checked"' : '';
                                            printf( '<label class="aec-margin-right"><input type="checkbox" value="%d" name="repeat_week_days[]" %s/>%s</label>', $index, $selected, $day );
                                        }
                                    ?>
                                </p>
                            </div>
                            
                            <!-- Monthly recurrence -->
                            <div class="aec-recurring-settings aec-monthly-recurrence" style="display: none;">
                                <p>
                                    <?php _e( 'Repeat Every', 'another-events-calendar' ); ?>&nbsp;
                                    <input type="text" style="width: 200px;" name="repeat_months" value="<?php if( isset( $repeat_months ) ) echo esc_attr( $repeat_months ); ?>" />&nbsp;
                                    <?php _e( 'months', 'another-events-calendar' ); ?>
                                </p>
                                
                                <p>
                                    <?php _e( 'On Dates', 'another-events-calendar' ); ?>&nbsp;
                                    <input type="text" name="repeat_month_days" placeholder="e.g. 1,10,25" style="width: 200px;" value="<?php if( isset( $repeat_month_days ) )  echo esc_attr( $repeat_month_days ); ?>" /> 
                                </p>
                            </div>                                
                        </div>
                 	</div>
                    
                    <div class="aec-recurring-event-fields form-group" style="display: none;">
                        <label class="col-sm-3 control-label"><?php _e( 'Repeat until', 'another-events-calendar' ); ?></label>
                        <div class="col-sm-9">
                        	<?php $repeat_until = isset( $repeat_until ) ? $repeat_until : 'times'; ?>
                            
                            <p>
                                <input type="radio" name="repeat_until" value="times" <?php checked( 'times', $repeat_until ); ?>/>
                                <input type="text" name="repeat_end_times" style="width: 200px;" value="<?php if( isset( $repeat_end_times ) ) echo esc_attr( $repeat_end_times ); ?>" />&nbsp;
                                <?php _e( 'Occurrences', 'another-events-calendar' ); ?>
                             </p>
                             
                             <p>
                                <input type="radio" name="repeat_until" value="date" <?php checked( 'date', $repeat_until ); ?>/>
                                <input type="text" style="width: 200px;" name="repeat_end_date" class="aec-date-picker" value="<?php if( isset( $repeat_end_date ) ) echo esc_attr( $repeat_end_date ); ?>" />&nbsp;
                                 <?php _e( 'Date', 'another-events-calendar' ); ?>
                              </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
      	</div>
                
        <!-- Event Venue -->
        <div class="panel panel-default form-horizontal">
        	<div class="panel-heading"><?php _e( 'Venue Details', 'another-events-calendar' ); ?></div>
            
            <div class="panel-body">
            
                <div class="form-group">
                    <label for="aec-venues" class="col-sm-3 control-label"><?php _e( 'Select Venue', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <select name="venue_id" id="aec-venues" class="form-control">
                            <option value="-1"><?php _e( 'Create New Venue', 'another-events-calendar' ); ?></option>
                            <?php
                                $venues   = get_posts( array( 'post_type' => 'aec_venues' ) );
								$venue_id = isset( $venue_id ) ? $venue_id : 0;
                                foreach( $venues as $venue ) {	
                                    printf('<option value="%s"%s>%s</option>', $venue->ID, selected( $venue->ID, $venue_id ), $venue->post_title );
                                }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div id="aec-venue-fields">
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php _e( 'Venue Name', 'another-events-calendar' ); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="venue_name" id="aec-venue-name" class="aec-map-field form-control" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php _e( 'Address', 'another-events-calendar' ); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="venue_address" id="aec-venue-address" class="aec-map-field form-control" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php _e( 'City', 'another-events-calendar' ); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="venue_city" id="aec-venue-city" class="aec-map-field form-control" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php _e( 'State', 'another-events-calendar' ); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="venue_state" id="aec-venue-state" class="aec-map-field form-control" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="aec-country" class="col-sm-3 control-label"><?php _e( 'Country', 'another-events-calendar' ); ?></label>
                        <div class="col-sm-9">
                            <select name="venue_country" id="aec-venue-country" class="aec-map-field form-control">
                                <option value="">-- <?php _e( 'Select Country', 'another-events-calendar' ); ?> --</option>
                                <?php
                                    $countries = aec_get_countries();
                                    foreach( $countries as $key => $label ) {
                                        printf( '<option value="%s">%s</option>', $key, $label );
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php _e( 'Postal Code', 'another-events-calendar' ); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="venue_pincode" id="aec-venue-pincode" class="aec-map-field form-control" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php _e( 'Phone:', 'another-events-calendar' ); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="venue_phone" id="aec-venue-phone" class="form-control" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php _e( 'Website', 'another-events-calendar' ); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="venue_website" id="aec-venue-website" class="form-control" />
                        </div>
                    </div>
                    
                    <?php if( ! empty( $map_settings['enabled'] ) ) : ?>
                    <div class="form-group">
         				<label class="col-sm-3 control-label"></label>
            			<div class="col-sm-9">
                            <div class="embed-responsive embed-responsive-16by9">
                                <div class="aec-map embed-responsive-item" data-default_location="<?php echo $default_location; ?>">
                                    <div class="marker"></div>
                                </div>
                            </div>
                            <input type="hidden" name="venue_latitude" id="aec-latitude" />
                            <input type="hidden" name="venue_longitude" id="aec-longitude" />
          				</div>
   					</div>
                    
                    <div class="form-group">
                        <label for="aec-hide-map" class="col-sm-3 control-label"><?php _e( 'Hide Google Map', 'another-events-calendar' ); ?></label>
                        <div class="col-sm-9">
                           <input type="checkbox" name="venue_hide_map" id="aec-hide-map" value="1" />
                        </div>
                    </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
                
        <!-- Event Organizer -->
        <div class="panel panel-default form-horizontal">
            <div class="panel-heading"><?php _e( 'Organizer Details', 'another-events-calendar' ); ?></div>
            
            <div class="panel-body">
            
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php _e( 'Select Organizers', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <div class="aec-multi-checkbox">
                            <?php
                                $organizers_list = get_posts( array( 'post_type' => 'aec_organizers' ) );
								$organizers      = isset( $organizers ) ? $organizers : array();
                                foreach( $organizers_list as $organizer ) {
                                    $checked = in_array( $organizer->ID, $organizers ) ? ' checked' : ''; 
                                    printf( '<label><input type="checkbox" name="organizers[]" value="%s"%s/>%s</label>', $organizer->ID, $checked, $organizer->post_title );
                                }
                             ?>
                        </div>
                    </div>
                </div>
                
                <div id="aec-organizer-fields-container"></div>
                
                <div class="aec-add-new-organizer-block text-right">
                    <a id="aec-add-new-organizer" class="btn btn-default"><?php _e( 'Add another Organizer', 'another-events-calendar' ); ?></a>
                </div>
                
                <div id="aec-organizer-fields" style="display: none;">
               		<div class="aec-organizer-fields">
                    	
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                               <h3 class="aec-organizer-group-id"></h3>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e( 'Organizer Name', 'another-events-calendar' ); ?></label>
                            <div class="col-sm-9">
                               <input type="text" name="organizer_name[]" class="form-control" />
                            </div>
                        </div>
                        
                        <div class="form-group">
                           <label class="col-sm-3 control-label"><?php _e( 'Phone', 'another-events-calendar' ); ?></label>
                            <div class="col-sm-9">
                              <input type="text" name="organizer_phone[]" class="form-control" />
                            </div>
                        </div>
                        
                        <div class="form-group">
                           <label class="col-sm-3 control-label"><?php _e( 'Email', 'another-events-calendar' ); ?></label>
                            <div class="col-sm-9">
                               <input type="text" name="organizer_email[]" class="form-control" />
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e( 'Website', 'another-events-calendar' ); ?></label>
                            <div class="col-sm-9">
                               <input type="text" name="organizer_website[]" class="form-control" />
                            </div>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
        
        <?php wp_nonce_field( 'aec_public_save_event', 'aec_public_event_nonce' ); ?>
        <input type="hidden" name="post_id" value="<?php echo $event_id; ?>" />
        
        <div class="form-group">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-9">
                <button type="submit" name="submit" class="btn btn-primary">
					<?php ( $event_id > 0 ) ? _e( 'Save Changes', 'another-events-calendar' ) : _e( 'Submit Event', 'another-events-calendar' ); ?>
                </button>
            </div>
        </div>
  	</form>
</div>