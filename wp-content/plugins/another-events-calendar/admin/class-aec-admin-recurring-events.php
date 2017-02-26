<?php

/**
 * Recurring Events.
 *
 * @link          https://yendif.com
 * @since         1.3.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/admin
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC_Admin_Recurring_Events Class
 *
 * @since    1.0.0
 */
class AEC_Admin_Recurring_Events {
		
	 /**
	 * Register the Custom Post Type for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function register_post_type() { 	

		$general_settings = get_option( 'aec_general_settings' );
		
		if( ! empty( $general_settings['has_recurring_events'] ) ) {
		
			$labels = array(
            	'name'               => _x( 'Recurring Events', 'post type general name', 'another-events-calendar' ),
            	'singular_name'      => _x( 'Recurring Event', 'post type singular name', 'another-events-calendar' ),
            	'menu_name'          => __( 'Recurring Events', 'another-events-calendar' ),
            	'name_admin_bar'     => __( 'Recurring Event', 'another-events-calendar' ),
				'all_items'          => __( 'Recurring Events', 'another-events-calendar' ),
				'add_new_item'       => __( 'Add New Recurring Event', 'another-events-calendar' ),
            	'add_new'            => __( 'Add New', 'another-events-calendar' ),
            	'new_item'           => __( 'New Recurring Event', 'another-events-calendar' ),
            	'edit_item'          => __( 'Edit Recurring Event', 'another-events-calendar' ),
				'update_item'        => __( 'Update Recurring Event', 'another-events-calendar' ),
            	'view_item'          => __( 'View Recurring Event', 'another-events-calendar' ),
            	'search_items'       => __( 'Search Events', 'another-events-calendar' ),
            	'not_found'          => __( 'No events found.', 'another-events-calendar' ),
            	'not_found_in_trash' => __( 'No events found in Trash.', 'another-events-calendar' )
        	);
			
			$args = array( 
        		'labels'              => $labels,
				'supports'            => array( 'title', 'editor', 'author', 'thumbnail' ),
            	'has_archive'         => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=aec_events',
        		'publicly_queryable'  => true,
				'exclude_from_search' => true,
				'capability_type'     => 'aec_event',
				'map_meta_cap'        => true
    		); 
		
			register_post_type( 'aec_recurring_events', $args ); 
		
		}  
	
	} 
	
	/**
 	 * Register the meta boxes.
 	 *
 	 * @since    1.3.0
 	 */
	public function register_meta_boxes() {
	
    	$general_settings = get_option( 'aec_general_settings' );
		
		add_meta_box( 'aec-event-details', __( 'Time & Date', 'another-events-calendar' ), array( $this, 'display_meta_box_event_details' ), 'aec_recurring_events', 'normal', 'high' );
		
		add_meta_box( 'aec-recurring-settings', __( 'Recurring Settings', 'another-events-calendar' ), array( $this,'display_meta_box_recurring_settings' ), 'aec_recurring_events', 'normal','high' );	
		
		add_meta_box( 'aec-cost-details', __( 'Event Cost', 'another-events-calendar' ), array( $this, 'display_meta_box_cost_details' ), 'aec_recurring_events', 'normal', 'high' );

		if( aec_current_user_can( 'edit_aec_venue' ) && ! empty( $general_settings['has_venues'] ) ) {
			add_meta_box( 'aec-venue-details', __( 'Select Venue', 'another-events-calendar' ), array( $this, 'display_meta_box_venue_details' ), 'aec_recurring_events', 'normal', 'high' ); 
		}
		
		if( aec_current_user_can( 'edit_aec_organizer' ) && ! empty( $general_settings['has_organizers'] ) ) {
		    add_meta_box( 'aec-organizer-details', __( 'Select Organizers', 'another-events-calendar' ), array( $this, 'display_meta_box_organizer_details' ), 'aec_recurring_events', 'normal', 'high' );  
		}	
				   
	}
	
	/**
 	 * Display the event details meta box.
 	 *
 	 * @since    1.3.0
	 *
	 * @param    object    $post    Post Object.
 	 */
    public function display_meta_box_event_details( $post ) {
	
		$all_day_event = get_post_meta( $post->ID, 'all_day_event', true );
		
		$start_date_time = get_post_meta( $post->ID, 'start_date_time', true );
		if( ! empty( $start_date_time ) ) {
			$start_date = date( 'Y-m-d', strtotime( $start_date_time ) );
			$start_hour = date( 'G', strtotime( $start_date_time ) );
			$start_min  = date( 'i', strtotime( $start_date_time ) );
		} else {
			$start_date = $start_hour = $start_min = ''; 
		}		
		
		$end_date_time = get_post_meta( $post->ID, 'end_date_time', true );
		if( ! empty( $end_date_time ) && '0000-00-00 00:00:00' != $end_date_time ) {
			$end_date = date( 'Y-m-d', strtotime( $end_date_time ) ); 
			$end_hour = date( 'G', strtotime( $end_date_time ) );
			$end_min  = date( 'i', strtotime( $end_date_time ) );
		} else {
			$end_date = $end_hour = $end_min = ''; 
		}		
		
		wp_nonce_field( 'aec_save_recurring_events', 'aec_recurring_events_nonce' );
		
		include AEC_PLUGIN_DIR.'admin/partials/events/aec-admin-events-display.php';
			   				
	}
	
	/**
 	 * Display the Recurring settings meta box.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post    Post Object.
 	 */
	public function display_meta_box_recurring_settings( $post ) {
		
		$repeat_type       = get_post_meta( $post->ID, 'repeat_type', true );
		$repeat_days       = get_post_meta( $post->ID, 'repeat_days', true );
		$repeat_weeks      = get_post_meta( $post->ID, 'repeat_weeks', true );
		$repeat_week_days  = (array) get_post_meta( $post->ID, 'repeat_week_days', true );
		$repeat_week_days  = array_filter( $repeat_week_days );
		$repeat_months     = get_post_meta( $post->ID, 'repeat_months', true );
		$repeat_month_days = get_post_meta( $post->ID, 'repeat_month_days', true );
		$repeat_until      = get_post_meta( $post->ID, 'repeat_until', true );
		$repeat_end_times  = get_post_meta( $post->ID, 'repeat_end_times', true );
		$repeat_end_date   = get_post_meta( $post->ID, 'repeat_end_date', true );
		
		wp_nonce_field( 'save_aec_recurring_settings', 'aec_recurring_settings_nonce' );
		
		include AEC_PLUGIN_DIR.'admin/partials/recurring-events/aec-admin-recurring-settings-display.php';
  
 	}
	
	/**
 	 * Display the event cost details meta box.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post    Post Object.
 	 */
    public function display_meta_box_cost_details( $post ) {
	
		$cost = get_post_meta( $post->ID, 'cost', true );
		
		wp_nonce_field( 'aec_save_recurring_event_cost', 'aec_recurring_event_cost_nonce' );
		
		include AEC_PLUGIN_DIR.'admin/partials/events/aec-admin-event-cost-display.php';
			   				
	}
	
	/**
 	 * Display the venue details meta box.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post    Post Object.
 	 */
	public function display_meta_box_venue_details( $post ) {
		
		$general_settings = get_option( 'aec_general_settings' );
		$map_settings     = get_option( 'aec_map_settings' );
		
		$venues = get_posts(
			array(
				'post_type'      => 'aec_venues',
				'posts_per_page' => '-1'
			)
		);
		
		$venue_id = get_post_meta( $post->ID, 'venue_id', true );

		$countries        = aec_get_countries();
		$default_location = $general_settings['default_location'];
		
	    wp_nonce_field( 'aec_save_recurring_venue', 'aec_recurring_venue_nonce' );

		include AEC_PLUGIN_DIR.'admin/partials/events/aec-admin-venues-display.php';
	
	}
		
	/**
 	 * Display the organizer details meta box.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post    Post Object.
 	 */
	public function display_meta_box_organizer_details( $post ) {

		$organizers_list = get_posts(
			array(
				'post_type'   => 'aec_organizers',
				'posts_per_page' => '-1'
			)
	    );
		
		$organizers = get_post_meta( $post->ID, 'organizers', true );
		$organizers = (array) $organizers;
		
		wp_nonce_field( 'aec_save_recurring_organizers', 'aec_recurring_organizers_nonce' );
		
		include AEC_PLUGIN_DIR.'admin/partials/events/aec-admin-organizers-display.php';
		
   }

	/**
 	 * Save the meta box data.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    int       $post_id    Post ID.
	 * @param    object    $post       Post Object.
	 * @return   int       $post_id    Post ID.
 	 */
	public function save_meta_boxes( $post_id, $post ) {
	 
	 	// vars
	 	$meta = array();
		
	 	// Bail if we're doing an auto save.
    	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		
		// Check if the post type = "aec_events".
		if ( 'aec_recurring_events' != $post->post_type ) {
			return $post_id;
		}
	
		// Check if the current user has permission to edit this post.
		if ( ! aec_current_user_can( 'edit_aec_event', $post_id ) ) { 
			return $post_id;
		}	 
	 	
		// Save event details
		if ( isset( $_POST['aec_recurring_events_nonce'] ) && wp_verify_nonce( $_POST['aec_recurring_events_nonce'], 'aec_save_recurring_events' ) ) {
			
			$meta['all_day_event'] = isset( $_POST['all_day_event'] ) ? 1 : 0; 
			update_post_meta( $post_id, 'all_day_event', $meta['all_day_event'] );
		
			$meta['start_date_time'] = sprintf( '%s %02d:%02d:00', sanitize_text_field( $_POST['start_date'] ), $_POST['start_hour'], $_POST['start_min'] );
			update_post_meta( $post_id, 'start_date_time', $meta['start_date_time'] );
			
			if( ! empty( $_POST['end_date'] ) ) {
				$meta['end_date_time'] = sprintf( '%s %02d:%02d:00', sanitize_text_field( $_POST['end_date'] ), $_POST['end_hour'], $_POST['end_min'] );
			} else { 
				$meta['end_date_time'] = '0000-00-00 00:00:00';
			}			
			update_post_meta( $post_id, 'end_date_time', $meta['end_date_time'] );
			
		}
		
		// Save event cost
		if ( isset( $_POST['aec_recurring_event_cost_nonce'] ) && wp_verify_nonce( $_POST['aec_recurring_event_cost_nonce'], 'aec_save_recurring_event_cost' ) ) {
			
			$meta['cost'] = aec_sanitize_amount( $_POST['cost'] );
			update_post_meta( $post_id, 'cost', $meta['cost'] );
			
		}

		// Save venue details
		if ( isset( $_POST['aec_recurring_venue_nonce'] ) && wp_verify_nonce( $_POST['aec_recurring_venue_nonce'], 'aec_save_recurring_venue' ) ) {
			
			$meta['venue_id'] = (int) $_POST['venue_id'];
			
			if( -1 == $meta['venue_id'] && ! empty( $_POST['venue_name'] ) ) {
			
				// Insert new venue
				$args = array(
					'post_type'   => 'aec_venues',
					'post_title'  => sanitize_text_field( $_POST['venue_name'] ),
					'post_status' => 'publish'
				);
								
				$meta['venue_id'] = wp_insert_post( $args );					
						
				$address = ! empty( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : '';
				update_post_meta( $meta['venue_id'], 'address', $address );
						
				$city = ! empty( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';
				update_post_meta( $meta['venue_id'], 'city', $city );
							
				$state = ! empty( $_POST['state'] ) ? sanitize_text_field( $_POST['state'] ) : '';
				update_post_meta( $meta['venue_id'], 'state', $state );
				
				$country = ! empty( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
				update_post_meta( $meta['venue_id'], 'country', $country );

				$pincode = ! empty( $_POST['pincode'] ) ? sanitize_text_field( $_POST['pincode'] ) : '';
				update_post_meta( $meta['venue_id'], 'pincode', $pincode );
							
				$phone = ! empty( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
				update_post_meta( $meta['venue_id'], 'phone', $phone );
						
				$website = ! empty( $_POST['website'] ) ? esc_url_raw( $_POST['website'] ) : '';
				update_post_meta( $meta['venue_id'], 'website', $website );
						
				$hide_map = ! empty( $_POST['hide_map'] ) ? 1 : 0;
				update_post_meta( $meta['venue_id'], 'hide_map', $hide_map );
				
				$latitude = isset( $_POST['latitude'] ) ? sanitize_text_field( $_POST['latitude'] ) : '';
				update_post_meta( $meta['venue_id'], 'latitude', $latitude );  
		
				$longitude = isset( $_POST['longitude'] ) ? sanitize_text_field( $_POST['longitude'] ) : '';
				update_post_meta( $meta['venue_id'], 'longitude', $longitude ); 
				
			}
			
			if( ! empty( $meta['venue_id'] ) ) {
				update_post_meta( $post_id, 'venue_id', $meta['venue_id'] );
			}		
			
		}
				
		// Save organizer details
		if ( isset( $_POST['aec_recurring_organizers_nonce'] ) && wp_verify_nonce( $_POST['aec_recurring_organizers_nonce'], 'aec_save_recurring_organizers' ) ) {
		
			$meta['organizer_ids'] = array();
			
			if( ! empty( $_POST['organizers'] ) ) {
				$meta['organizer_ids'] = array_map( 'trim', $_POST['organizers'] );
			}
	
			// Insert new organizers	
			if( ! empty( $_POST['organizer_name'] ) ) {	
				
				$organizers = $_POST['organizer_name'];
				$organizers = array_map( 'trim', $organizers );	
				$organizers = array_filter( $organizers );
					
				foreach( $organizers as $key => $organizer ) {
					$args = array(
						'post_type'   => 'aec_organizers',
						'post_title'  => sanitize_text_field( $organizer ),
						'post_status' => 'publish'
					);
										
					$organizer_id = wp_insert_post( $args );
					
					$phone = ! empty( $_POST['organizer_phone'][ $key ] ) ? sanitize_text_field( $_POST['organizer_phone'][ $key ] ) : '';
					update_post_meta( $organizer_id, 'phone', $phone );
	
					$email = ! empty( $_POST['organizer_email'][ $key ] ) ? sanitize_email( $_POST['organizer_email'][ $key ] ) : '';
					update_post_meta( $organizer_id, 'email', $email );
						
					$website = ! empty( $_POST['organizer_website'][ $key ] ) ? esc_url_raw( $_POST['organizer_website'][ $key ] ) : '';
					update_post_meta( $organizer_id, 'website', $website );
					
					$meta['organizer_ids'][] = (string) $organizer_id;
				}
				
			}

			if( ! empty( $meta['organizer_ids'] ) ) {
				update_post_meta( $post_id, 'organizers', $meta['organizer_ids'] );
			}
			
		}		
		
		// Save recurring settings 
		if( isset( $_POST['aec_recurring_settings_nonce'] ) && wp_verify_nonce( $_POST['aec_recurring_settings_nonce'], 'save_aec_recurring_settings' ) ) {
			
			$meta['repeat_type'] = ! empty( $_POST['repeat_type'] ) ? sanitize_text_field( $_POST['repeat_type'] ) : '';
			update_post_meta( $post_id, 'repeat_type', $meta['repeat_type'] );
			
			$meta['repeat_days'] = ! empty( $_POST['repeat_days'] ) ? sanitize_text_field( $_POST['repeat_days'] ) : '';
			update_post_meta( $post_id, 'repeat_days', $meta['repeat_days'] );
			
			$meta['repeat_weeks'] = ! empty( $_POST['repeat_weeks'] ) ? sanitize_text_field( $_POST['repeat_weeks'] ) : '';
			update_post_meta( $post_id, 'repeat_weeks', $meta['repeat_weeks'] );
			
			$meta['repeat_week_days'] = ! empty( $_POST['repeat_week_days'] ) ? array_map( 'esc_attr', $_POST['repeat_week_days'] ) : '';
            update_post_meta( $post_id, 'repeat_week_days', $meta['repeat_week_days'] );
					
			$meta['repeat_months'] = ! empty( $_POST['repeat_months'] ) ? sanitize_text_field( $_POST['repeat_months'] ) : '';
			update_post_meta( $post_id, 'repeat_months', $meta['repeat_months'] );
			
			$meta['repeat_month_days'] = ! empty( $_POST['repeat_month_days'] ) ? sanitize_text_field( $_POST['repeat_month_days'] ) : '';
			update_post_meta( $post_id, 'repeat_month_days', $meta['repeat_month_days'] );
				
			if( ! empty( $meta['repeat_month_days'] ) ) {
				$meta['repeat_month_days'] = trim( $meta['repeat_month_days'] );
				$meta['repeat_month_days'] = explode( ',', $meta['repeat_month_days'] );
				$meta['repeat_month_days'] = array_map( 'trim', $meta['repeat_month_days'] );
			}
			
			$meta['repeat_until'] = ! empty( $_POST['repeat_until'] ) ? sanitize_text_field( $_POST['repeat_until'] ) : '';
			update_post_meta( $post_id, 'repeat_until', $meta['repeat_until'] );
			
			$meta['repeat_end_times'] = ! empty( $_POST['repeat_end_times'] ) ? sanitize_text_field( $_POST['repeat_end_times'] ) : '';
			update_post_meta( $post_id, 'repeat_end_times', $meta['repeat_end_times'] );		
			
			$meta['repeat_end_date'] = ! empty( $_POST['repeat_end_date'] ) ? sanitize_text_field( $_POST['repeat_end_date'] ) : '';
			update_post_meta( $post_id, 'repeat_end_date', $meta['repeat_end_date'] );
			
			// Recurring Events
    		if ( $post->post_status == 'publish' ) {
				
				// Delete existing events of this recurring event
				$args = array( 
					'post_type'   => 'aec_events',
					'post_status' => 'any',
					'numberposts' => -1,
					'fields'      => 'ids',
					'meta_query'  => array(
						array(
							'key'     	=> 'parent',
							'value'     => $post_id,
							'compare' 	=> '=',
						)
					)
				);
			
				$items = get_posts( $args );
				
				if( count( $items ) > 0 ) {
					foreach( $items as $item ) {
						wp_delete_post( $item, true );
					}
				}
			
				// Create Recurrence event(s)
				$start_date = new DateTime( $meta['start_date_time'] );
				$end_date = new DateTime( $meta['end_date_time'] );
				
				$interval = $start_date->diff( $end_date );
				$meta['interval'] = $interval->format('P%YY%mM%dDT%HH%iM%sS');
		
				switch( $meta['repeat_type'] ) {
					case 'no_repeat':
						$this->add_event( $post, $meta, $start_date );
						break;
					case 'daily':
						if( empty( $meta['repeat_days'] ) ) return;			
						$this->process_daily_events( $post, $meta );
						break;
					case 'weekly' :
						if( empty( $meta['repeat_weeks'] ) || empty( $meta['repeat_week_days'] ) ) return;
						$this->process_weekly_events( $post, $meta );	
						break;
					case 'monthly':
						if( empty( $meta['repeat_months'] ) || empty( $meta['repeat_month_days'] ) ) return;
						$start_date->modify( 'first day of this month' );
						$this->process_monthly_events( $post, $meta );
						break;
				} 
				
			}
				
		}	
		
	}
	
	/**
 	 * Process daily events.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post    Post Object.
	 * @param    array     $meta    Post Meta.
 	 */
	function process_daily_events( $post, $meta ) {
		
		$start_date = new DateTime( $meta['start_date_time'] );
		$interval   = new DateInterval( 'P'.$meta['repeat_days'].'D' );
		
		if( 'times' == $meta['repeat_until'] ) {
			$end = (int) $meta['repeat_end_times'] - 1;			
		} else {
			$end = new DateTime( $meta['repeat_end_date'] );
			$end = $end->modify( '+1 day' );
		}
		
		if( ! $end ) return;
		
		$period = new DatePeriod( $start_date, $interval, $end );	
		foreach( $period as $date ) {
			$this->add_event( $post, $meta, $date );
		}
	}
	
	/**
 	 * Process weekly events.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post          Post Object.
	 * @param    array     $meta          Post Meta.
	 * @param    object    $start_date    Event Start Date.
	 * @param    int       $occurences    Number of Occurences.
 	 */
	function process_weekly_events( $post, $meta, $start_date = '', $occurences = 0 ) {
		
		if( '' == $start_date ) {
			$start_date = new DateTime( $meta['start_date_time'] );
			if( $start_date->format( 'w' ) !== 0 ) $start_date->modify( '-'.$start_date->format( 'w' ).' days' );
		}
		
		$initial_start_date = new DateTime( $meta['start_date_time'] );
		
		if( 'times' == $meta['repeat_until'] ) {
			
			$interval = new DateInterval('P1D');		
			$period   = new DatePeriod( $start_date, $interval, 6 );
			
			foreach( $period as $date ) {				
				if( ! in_array( $date->format('w'), $meta['repeat_week_days'] ) ) continue;
				
				if( $date->format('U') > $initial_start_date->format('U') ) {
					$this->add_event( $post, $meta, $date );
					
					if( ++$occurences >= $meta['repeat_end_times'] ) break;
				}
			}
			
			if( $occurences < $meta['repeat_end_times'] )	{
				$this->process_weekly_events( $post, $meta, $start_date->modify( '+'.( $meta['repeat_weeks'] * 7 ).' days' ), $occurences );
			}
					
		} else {
		
			$interval = new DateInterval( 'P'.( $meta['repeat_weeks'] * 7 ).'D' );	
			$end      = new DateTime( $meta['repeat_end_date'] );
			$end      = $end->modify( '+1 day' );
			$period   = new DatePeriod( $start_date, $interval, $end );

			foreach( $period as $date ) {
				
				$_interval = new DateInterval( 'P1D' );			
				$_period   = new DatePeriod( $date, $_interval, 6 );
				
				foreach( $_period as $_date ) {
					if( ! in_array( $_date->format('w'), $meta['repeat_week_days'] ) ) continue;
					if( $_date->format('U') > $initial_start_date->format('U') &&  $_date->format('U') <= $end->format('U') ) $this->add_event( $post, $meta, $_date );
				}
				
			}
			
		}
	}
	
	/**
 	 * Process monthly events.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post          Post Object.
	 * @param    array     $meta          Post Meta.
	 * @param    object    $start_date    Event Start Date.
	 * @param    int       $occurences    Number of Occurences.
 	 */
	function process_monthly_events( $post, $meta, $start_date = '', $occurences = 0 ) {
		
		if( '' == $start_date ) {
			$start_date = new DateTime( $meta['start_date_time'] );
			$start_date->modify( 'first day of this month' );
		}
		
		$initial_start_date = new DateTime( $meta['start_date_time'] );
		
		if( 'times' == $meta['repeat_until'] ) {
			
			$interval = new DateInterval( 'P1D' );		
			$end      = $start_date->format( 't' ) - 1;	
			$period   = new DatePeriod( $start_date, $interval, $end );
			
			foreach( $period as $date ) {				
				if( ! in_array($date->format('j'), (array) $meta['repeat_month_days'] ) ) continue;
				
				if( $date->format('U') > $initial_start_date->format('U') ) {
					$this->add_event( $post, $meta, $date );
					
					if( ++$occurences >= $meta['repeat_end_times'] ) break;
				}
			}
			
			if( $occurences < $meta['repeat_end_times'] ) {
				$this->process_monthly_events( $post, $meta, $start_date->modify( '+'.$meta['repeat_months'].' month' ), $occurences );
			}
					
		} else {
		
			$interval = new DateInterval( 'P'.$meta['repeat_months'].'M' );		
			$end      = new DateTime( $meta['repeat_end_date'] );
			$end      = $end->modify( '+1 day' );
			$period   = new DatePeriod( $start_date, $interval, $end );
			
			foreach( $period as $date ) {	
						
				$_interval = new DateInterval('P1D');
				$_end      = $date->format('t') - 1;	
				$_period   = new DatePeriod( $date, $_interval, $_end );
				
				foreach( $_period as $_date ) {
					if( ! in_array( $_date->format( 'j' ), (array) $meta['repeat_month_days'] ) ) continue;
					if( $_date->format('U') > $initial_start_date->format('U') &&  $_date->format('U') <= $end->format('U') ) $this->add_event( $post, $meta, $_date );
				}
				
			}
			
		}
		
	}
	
	/**
 	 * Add Event.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post          Post Object.
	 * @param    array     $meta          Post Meta.
	 * @param    object    $start_date    Event Start Date.
 	 */
	public function add_event( $post, $meta, $start_date ) {	

		$args = array(
			'post_type'    => 'aec_events',
			'post_title'   => $post->post_title,
			'post_status'  => 'publish',
			'post_content' => $post->post_content,
		);
				
		$post_id = wp_insert_post( $args );
		
		update_post_meta( $post_id, 'all_day_event', $meta['all_day_event'] );
		
		$start_date_time = $start_date->format( 'Y-m-d H:i:s' );
		update_post_meta( $post_id, 'start_date_time', $start_date_time );
	
		$end_date_time = aec_add_date( $start_date_time, $meta['interval'] );
		update_post_meta( $post_id, 'end_date_time', $end_date_time );
				
		update_post_meta( $post_id, 'cost', $meta['cost'] );
		update_post_meta( $post_id, 'venue_id', $meta['venue_id'] );
		update_post_meta( $post_id, 'organizers', $meta['organizer_ids'] );
    	
		$terms = get_the_terms( $post->ID , 'aec_categories' );
		if( ! empty( $terms ) ) {
			foreach( $terms as $term ) {
				wp_set_object_terms( $post_id, $term->term_id , 'aec_categories', true );
			}
		}
		
		$terms = get_the_terms( $post->ID , 'aec_tags' );
		if( ! empty( $terms ) ) {
			foreach( $terms as $term ) {
				wp_set_object_terms( $post_id, $term->term_id , 'aec_tags', true );
			}
		}

		$thumbnail_id = get_post_thumbnail_id( $post->ID );
		if( ! is_wp_error( $post_id ) ) update_post_meta( $post_id, '_thumbnail_id', $thumbnail_id );
	   	
		update_post_meta( $post_id, 'parent', $post->ID );		
		
	}
	
	/**
	 * Adds the categories filter.
	 *
	 * @since    1.0.0
	 *
	 */
	public function restrict_manage_posts() {
	
		global $typenow;
		
		$post_type = 'aec_recurring_events'; 
		$taxonomy  = 'aec_categories'; 
		
		if( $typenow == $post_type ) {
		
			$selected      = isset( $_GET[ $taxonomy ] ) ? (int) $_GET[ $taxonomy ] : '';
			$info_taxonomy = get_taxonomy( $taxonomy );
			
			wp_dropdown_categories( array(
				'show_option_none' => __( "Show All {$info_taxonomy->label}" ),
				'taxonomy'         => $taxonomy,
				'name'             => $taxonomy,
				'orderby'          => 'name',
				'selected'         => $selected,
				'hierarchical'     => true,
				'hide_empty'       => false,
			));
			
		}
		
	}
	
	/**
	 * Convert term id into slug.
	 *
	 * @since    1.0.0
	 */
	public function parse_query( $query ) {
	
		global $pagenow;
		
		$post_type = 'aec_recurring_events'; 
		$taxonomy  = 'aec_categories';		
		$q_vars    = &$query->query_vars;
		
		if( $pagenow == 'edit.php' && isset( $q_vars['post_type'] ) && $q_vars['post_type'] == $post_type && isset( $q_vars[ $taxonomy ] ) && is_numeric( $q_vars[ $taxonomy ] ) ) {
			if( $q_vars[ $taxonomy ] > 0 ) {
				$term = get_term_by( 'id', $q_vars[ $taxonomy ], $taxonomy );
				$q_vars[ $taxonomy ] = $term->slug;
			} else {			
				unset( $q_vars[ $taxonomy ] );
			}
		}
		
	}
	
	/**
	 * Display column values.
	 *
	 * @since    1.0.0
	 *
     * @param    int       $post_id    Post ID.
	 * @param    string    $columns    Column name.  
	 */
	public function manage_posts_custom_column( $columns, $post_id ) {

		switch( $columns ) {
			case 'start_date':
				$start_date_time = get_post_meta( $post_id, 'start_date_time', true );
				$start_date = date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime( $start_date_time ) );
				echo ! empty( $start_date ) ? $start_date : '--';
				break;
			case 'end_date':
				$end_date_time = get_post_meta( $post_id, 'end_date_time', true );
				echo ( '0000-00-00 00:00:00' != $end_date_time ) ? date_i18n( get_option('date_format').' '.get_option('time_format'), strtotime( $end_date_time ) ) : "--";
				break;
		}
		
	}
	
	/**
	 * Add custom columns.
	 *
	 * @since    1.0.0
	 *
	 * @param    array    $columns    Array of column names.
	 * @return   array                Updated column names.
	 */
	function manage_posts_columns( $columns ) {
    
		$n_columns = array(); 
		
		unset( $columns['taxonomy-aec_categories'] );
		
	 	foreach( $columns as $key => $value ) {
	  		if( 'author' == $key ) {
				$n_columns['taxonomy-aec_categories'] = __( 'Categories', 'another-events-calendar' );
	   			$n_columns['start_date']              = __( 'Start Date','another-events-calendar' );
				$n_columns['end_date']                = __( 'End Date', 'another-events-calendar' );
	    	} 
			
			$n_columns[ $key ] = $value; 
		} 
		
		return $n_columns;
			
	}
	
}