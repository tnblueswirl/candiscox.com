<?php

/**
 * Markup the event recurrence details metabox used in the "aec_recurringevents" custom post type form.
 */ 
?>

<table class="aec-input widefat">
	<tbody>
    	<tr>
        	<td class="label">
				<label><?php _e( 'Repeat type', 'another-events-calendar' ); ?></label>
          	</td>
            <td>
            	<select id="aec-recurring-frequency" name="repeat_type">
                	<option value="no_repeat"<?php selected( 'no_repeat', $repeat_type ); ?>><?php _e( 'No repeat', 'another-events-calendar' );?></option>
                    <option value="daily"<?php selected( 'daily', $repeat_type ); ?>><?php _e( 'Daily', 'another-events-calendar' ); ?></option>
					<option value="weekly"<?php selected( 'weekly', $repeat_type ); ?>><?php _e( 'Weekly', 'another-events-calendar' ); ?></option>
                    <option value="monthly"<?php selected( 'monthly', $repeat_type ); ?>><?php _e( 'Monthly', 'another-events-calendar' ); ?></option>
                </select>
                
               	<!-- daily recurrence -->
                <div class="aec-recurring-settings aec-daily-recurrence aec-hide">
                	<p>
                    	<?php _e( 'Repeat every', 'another-events-calendar' ); ?>&nbsp; 
                        <input type="text" style="width: 200px;" name="repeat_days" value="<?php echo esc_attr( $repeat_days ); ?>" />&nbsp; 
						<?php _e( 'days', 'another-events-calendar' ); ?>
                    </p>
               	</div>
                    
                <!-- weekly recurrence -->
                <div class="aec-recurring-settings aec-weekly-recurrence aec-hide">
                	<p>
                    	<?php _e( 'Repeat every', 'another-events-calendar' ); ?>&nbsp;
                        <input type="text" style="width: 200px;" name="repeat_weeks" value="<?php echo esc_attr( $repeat_weeks ); ?>" />&nbsp; 
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
							
							foreach( $days as $index => $day ) {
								$selected = in_array( $index, $repeat_week_days ) ? 'checked="checked"' : '';
								printf( '<label class="aec-margin-right"><input type="checkbox" value="%d" name="repeat_week_days[]" %s/>%s</label>', $index, $selected, $day );
							}
						?>
                    </p>
              	</div>
                        
               	<!-- monthly recurrence -->
                <div class="aec-recurring-settings aec-monthly-recurrence aec-hide">
                	<p>
                    	<?php _e( 'Repeat Every', 'another-events-calendar' ); ?>&nbsp;
                        <input type="text" style="width: 200px;" name="repeat_months" value="<?php echo esc_attr( $repeat_months ); ?>" />&nbsp;
						<?php _e( 'months', 'another-events-calendar' ); ?>
                   	</p>
                    
                    <p>
                    	<?php _e( 'On Dates', 'another-events-calendar' ); ?>&nbsp;
                        <input type="text" name="repeat_month_days" placeholder="e.g. 1,10,25" style="width: 200px;" value="<?php echo esc_attr( $repeat_month_days ); ?>" /> 
                    </p>
              	</div>
            </td>
         </tr>
         
         <tr>
            <td class="label">
                <label><?php _e( 'Repeat until', 'another-events-calendar' )?></label>
            </td>
            <td>
            	<p>
                 	<input type="radio" name="repeat_until" value="times" <?php checked( 'times', $repeat_until ); ?>/>
                 	<input type="text" style="width: 200px;" name="repeat_end_times" value="<?php echo esc_attr( $repeat_end_times ); ?>" />&nbsp;
				 	<?php _e( 'Occurrences', 'another-events-calendar' ); ?>
                 </p>
                 
                 <p>
                 	<input type="radio" name="repeat_until" value="date" <?php checked( 'date', $repeat_until ); ?> />
                 	<input type="text" style="width: 200px;" name="repeat_end_date" class="aec-date" value="<?php echo esc_attr( $repeat_end_date ); ?>" />&nbsp;
                 	 <?php _e( 'Date', 'another-events-calendar' ); ?>
                  </p>
            </td>
        </tr>
 	</tbody>
</table>