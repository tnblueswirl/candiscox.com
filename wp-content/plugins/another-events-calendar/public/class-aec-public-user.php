<?php

/**
 * User Functions.
 *
 * @link          https://yendif.com/
 * @since         1.5.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/public
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC_Public_User Class
 *
 * @since    1.5.0
 */
class  AEC_Public_User {

	/**
	 * Get things started.
	 *
	 * @since    1.5.0
	 */
	public function __construct( ) {
		
		add_shortcode( 'aec_manage_events', array( $this, 'shortcode_aec_manage_events' ) );
		add_shortcode( 'aec_event_form', array( $this, 'shortcode_aec_event_form' ) );
		
		add_shortcode( 'aec_manage_venues', array( $this, 'shortcode_aec_manage_venues' ) );
		add_shortcode( 'aec_venue_form', array( $this, 'shortcode_aec_venue_form' ) ); 
		
		add_shortcode( 'aec_manage_organizers', array( $this, 'shortcode_aec_manage_organizers' ) );
		add_shortcode( 'aec_organizer_form', array( $this, 'shortcode_aec_organizer_form' ) );   

	}
	
	/**
	 * Manage form submissions.
	 *
	 * @since    1.5.0
	 */
	public function manage_actions() {	

		if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
		
			$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
			
			if( $post_id > 0 ) {			
				if( ! aec_current_user_can('edit_post', $post_id) ) return;
			}
			
			// Handle Event Form Submission
			if( isset( $_POST['aec_public_event_nonce'] ) && wp_verify_nonce( $_POST['aec_public_event_nonce'], 'aec_public_save_event' ) ) {
				$this->save_event_s( $post_id  );	
			}
			
			// Handle Venue Form Submission
			if( isset( $_POST['aec_public_venue_nonce'] ) && wp_verify_nonce( $_POST['aec_public_venue_nonce'], 'aec_public_save_venue' ) ) {
				$this->save_venue( $post_id  );
			}
			
			// Handle Organizer Form Submission
			if( isset( $_POST['aec_public_organizer_nonce'] ) && wp_verify_nonce( $_POST['aec_public_organizer_nonce'], 'aec_public_save_organizer' ) ) {
				$this->save_organizer( $post_id  );	
			}		
		
		}
	
	}
	
	/**
	 * Process the shortcode [aec_manage_events].
	 *
	 * @since    1.5.0
	 */
	public function shortcode_aec_manage_events() {     
		
		if( ! is_user_logged_in() ) {		
			return aec_login_form();			
		}	
		
		// Load dependencies
		wp_enqueue_style( AEC_PLUGIN_SLUG ); 
		
		// Vars
		$general_settings = get_option( 'aec_general_settings' );
		$events_settings  = get_option( 'aec_events_settings' );
		$page_settings    = get_option( 'aec_page_settings' ); 
		
		// Build query 
		$paged = aec_get_page_number();		
		
		$args = array(
			'post_type'      => 'aec_events',
			'posts_per_page' => empty( $events_settings['events_per_page'] ) ? -1 : $events_settings['events_per_page'],
			'paged'          => $paged,
			'post_status'	 => 'publish',
			'author' 		 => get_current_user_id(),
			's'				 => isset( $_REQUEST['aec'] ) ? sanitize_text_field( $_REQUEST['aec'] ) : '',
		);
		
		$aec_query = new WP_Query( $args );
		
		ob_start();
		include AEC_PLUGIN_DIR."public/partials/user/aec-public-manage-events-display.php";
		return ob_get_clean();
		
	}
	
	/**
	 * Process the shortcode [aec_event_form].
	 *
	 * @since    1.5.0
	 */
	public function shortcode_aec_event_form() {   
	 	
		if( ! is_user_logged_in() ) {		
			return aec_login_form();			
		}		
		
		$event_id = get_query_var( 'aec_id' ) ? (int) get_query_var( 'aec_id' ) : 0;
		$has_permission = true;
		
		if( $event_id > 0 ) {
			if( ! aec_current_user_can( 'edit_post', $event_id ) ) $has_permission = false;
		}
		
		if( ! $has_permission ) {
			return __( 'You do not have sufficient permissions to access this page.', 'another-events-calendar' );
		}
		
		// Load dependencies		
		wp_enqueue_script( AEC_PLUGIN_SLUG ); 
		wp_enqueue_script( AEC_PLUGIN_SLUG.'-bootstrap-validator' ); 
		wp_enqueue_script( AEC_PLUGIN_SLUG.'-google-map' );
		
		wp_enqueue_style( AEC_PLUGIN_SLUG );
		
		// Vars
		$general_settings = get_option( 'aec_general_settings' );
		$map_settings     = get_option( 'aec_map_settings' );
		
		$countries        = aec_get_countries();
		$default_location = $general_settings['default_location'];
		
		if( $event_id > 0 ) {
			$post = get_post( $event_id );
			
			$title = $post->post_title;
			$description = $post->post_content;
			
			$categories = wp_get_object_terms( $event_id, 'aec_categories', array( 'fields' => 'ids' ) );
			
			$attachment_id = get_post_meta( $event_id, '_thumbnail_id', true );
			if( $attachment_id ) $image = wp_get_attachment_url( $attachment_id );
			
			$cost = get_post_meta( $event_id, 'cost', true );
			
			$all_day_event = get_post_meta( $event_id, 'all_day_event', true );
			
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
			
			$repeat_type       = get_post_meta( $event_id, 'repeat_type', true );
			$repeat_days       = get_post_meta( $event_id, 'repeat_days', true );
			$repeat_weeks      = get_post_meta( $event_id, 'repeat_weeks', true );
			$repeat_week_days  = (array) get_post_meta( $event_id, 'repeat_week_days', true );
			$repeat_week_days  = array_filter( $repeat_week_days );
			$repeat_months     = get_post_meta( $event_id, 'repeat_months', true );
			$repeat_month_days = get_post_meta( $event_id, 'repeat_month_days', true );
			$repeat_until      = get_post_meta( $event_id, 'repeat_until', true );
			$repeat_end_times  = get_post_meta( $event_id, 'repeat_end_times', true );
			$repeat_end_date   = get_post_meta( $event_id, 'repeat_end_date', true );
		
			$venue_id = get_post_meta( $event_id, 'venue_id', true );
			
			$organizers = get_post_meta( $event_id, 'organizers', true );
			$organizers = (array) $organizers;			
		}
		
		// ...	
		ob_start();
		include AEC_PLUGIN_DIR."public/partials/user/aec-public-edit-event-display.php";
		return ob_get_clean();
		
	}
	
	/**
	 * Create Event.
	 *
	 * @since    1.5.0
	 *
	 * @param    int    $post_id    Event ID.   
	 */	
	public function save_event_s( $post_id ) { 
	
		$meta = array();	
		$meta['title']       = $_POST['title'];
		$meta['description'] = $_POST['description'];
		
		$is_recurring_event  = isset( $_POST['recurring_event'] ) ? 1 : 0;
			
		$args = array( 
			'ID'			=> $post_id,
			'post_type'     => $is_recurring_event ? 'aec_recurring_events' : 'aec_events',
			'post_title'    => $meta['title'],
			'post_content'	=> $meta['description'],
			'post_status'   => 'publish',
		);
		
		$event_id = wp_insert_post( $args );
		
		$meta['categories'] = isset( $_POST['categories'] ) ? array_map( 'esc_attr', $_POST['categories'] ) : array();
		wp_set_object_terms( $event_id, $meta['categories'], 'aec_categories', false );
		
		// Upload image
		$meta['thumbnail_id'] = 0;
		
		if( $_FILES['image']['error'] === UPLOAD_ERR_OK ) {
		
			// require the needed files
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
				
			// then loop over the files that were sent and store them using media_handle_upload();
			if( $_FILES ) {
			
				foreach( $_FILES as $file => $array ) {
					$mime = $_FILES[ $file ]['type'];					
					if( ! in_array( $mime, array( 'image/jpeg', 'image/jpg', 'image/png' ) ) ) return;
					
					if( $_FILES[ $file ]['error'] !== UPLOAD_ERR_OK ) return;
					
					$meta['thumbnail_id'] = media_handle_upload( $file, $event_id );
					
				}  
				
				if( $meta['thumbnail_id'] > 0 ) {
					update_post_meta( $event_id, '_thumbnail_id', $meta['thumbnail_id'] );
				} 
				
			}		
			
		}
		
		// ...		
		$meta['cost'] = aec_sanitize_amount( $_POST['cost'] );
		update_post_meta( $event_id, 'cost', $meta['cost'] );
		
		$meta['all_day_event'] = isset( $_POST['all_day_event'] ) ? 1 : 0; 
		update_post_meta( $event_id, 'all_day_event', $meta['all_day_event'] );
		
		$meta['start_date_time'] = sprintf( '%s %02d:%02d:00', sanitize_text_field( $_POST['start_date'] ), $_POST['start_hour'], $_POST['start_min'] );
		update_post_meta( $event_id, 'start_date_time', $meta['start_date_time'] );
		
		if( ! empty( $_POST['end_date'] ) ) {
			$meta['end_date_time'] = sprintf( '%s %02d:%02d:00', sanitize_text_field( $_POST['end_date'] ), $_POST['end_hour'], $_POST['end_min'] );
		} else { 
			$meta['end_date_time'] = '0000-00-00 00:00:00';
		}			
		update_post_meta( $event_id, 'end_date_time', $meta['end_date_time'] );
				
		// Add Venue
		$meta['venue_id'] = (int) $_POST['venue_id'];
		
		if( -1 == $meta['venue_id'] && ! empty( $_POST['venue_name'] ) ) {
			
			// Insert new venue
			$args = array(
				'post_type'   => 'aec_venues',
				'post_title'  => sanitize_text_field( $_POST['venue_name'] ),
				'post_status' => 'publish'
			);
								
			$meta['venue_id'] = wp_insert_post( $args );					
						
			$address = ! empty( $_POST['venue_address'] ) ? sanitize_text_field( $_POST['venue_address'] ) : '';
			update_post_meta( $meta['venue_id'], 'address', $address );
						
			$city = ! empty( $_POST['venue_city'] ) ? sanitize_text_field( $_POST['venue_city'] ) : '';
			update_post_meta( $meta['venue_id'], 'city', $city );
							
			$state = ! empty( $_POST['venue_state'] ) ? sanitize_text_field( $_POST['venue_state'] ) : '';
			update_post_meta( $meta['venue_id'], 'state', $state );
				
			$country = ! empty( $_POST['venue_country'] ) ? sanitize_text_field( $_POST['venue_country'] ) : '';
			update_post_meta( $meta['venue_id'], 'country', $country );

			$pincode = ! empty( $_POST['venue_pincode'] ) ? sanitize_text_field( $_POST['venue_pincode'] ) : '';
			update_post_meta( $meta['venue_id'], 'pincode', $pincode );
							
			$phone = ! empty( $_POST['venue_phone'] ) ? sanitize_text_field( $_POST['venue_phone'] ) : '';
			update_post_meta( $meta['venue_id'], 'phone', $phone );
						
			$website = ! empty( $_POST['venue_website'] ) ? esc_url_raw( $_POST['venue_website'] ) : '';
			update_post_meta( $meta['venue_id'], 'website', $website );
						
			$hide_map = ! empty( $_POST['venue_hide_map'] ) ? 1 : 0;
			update_post_meta( $meta['venue_id'], 'hide_map', $hide_map );
				
			$latitude = isset( $_POST['venue_latitude'] ) ? sanitize_text_field( $_POST['venue_latitude'] ) : '';
			update_post_meta( $meta['venue_id'], 'latitude', $latitude );  
		
			$longitude = isset( $_POST['venue_longitude'] ) ? sanitize_text_field( $_POST['venue_longitude'] ) : '';
			update_post_meta( $meta['venue_id'], 'longitude', $longitude ); 
				
		}
			
		if( ! empty( $meta['venue_id'] ) ) {
			update_post_meta( $event_id, 'venue_id', $meta['venue_id'] );
		}
		
		// Add Organizers
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
			update_post_meta( $event_id, 'organizers', $meta['organizer_ids'] );
		}
		
		// Insert Recurrence event(s).
		if( $is_recurring_event ) { 
			$meta['repeat_type'] = ! empty( $_POST['repeat_type'] ) ? sanitize_text_field( $_POST['repeat_type'] ) : '';
			update_post_meta( $event_id, 'repeat_type', $meta['repeat_type'] );
			
			$meta['repeat_days'] = ! empty( $_POST['repeat_days'] ) ? sanitize_text_field( $_POST['repeat_days'] ) : '';
			update_post_meta( $event_id, 'repeat_days', $meta['repeat_days'] );
			
			$meta['repeat_weeks'] = ! empty( $_POST['repeat_weeks'] ) ? sanitize_text_field( $_POST['repeat_weeks'] ) : '';
			update_post_meta( $event_id, 'repeat_weeks', $meta['repeat_weeks'] );
			
			$meta['repeat_week_days'] = ! empty( $_POST['repeat_week_days'] ) ? array_map( 'esc_attr', $_POST['repeat_week_days'] ) : '';
            update_post_meta( $event_id, 'repeat_week_days', $meta['repeat_week_days'] );
					
			$meta['repeat_months'] = ! empty( $_POST['repeat_months'] ) ? sanitize_text_field( $_POST['repeat_months'] ) : '';
			update_post_meta( $event_id, 'repeat_months', $meta['repeat_months'] );
			
			$meta['repeat_month_days'] = ! empty( $_POST['repeat_month_days'] ) ? sanitize_text_field( $_POST['repeat_month_days'] ) : '';
			update_post_meta( $event_id, 'repeat_month_days', $meta['repeat_month_days'] );
				
			if( ! empty( $meta['repeat_month_days'] ) ) {
				$meta['repeat_month_days'] = trim( $meta['repeat_month_days'] );
				$meta['repeat_month_days'] = explode( ',', $meta['repeat_month_days'] );
				$meta['repeat_month_days'] = array_map( 'trim', $meta['repeat_month_days'] );
			}
			
			$meta['repeat_until'] = ! empty( $_POST['repeat_until'] ) ? sanitize_text_field( $_POST['repeat_until'] ) : '';
			update_post_meta( $event_id, 'repeat_until', $meta['repeat_until'] );
			
			$meta['repeat_end_times'] = ! empty( $_POST['repeat_end_times'] ) ? sanitize_text_field( $_POST['repeat_end_times'] ) : '';
			update_post_meta( $event_id, 'repeat_end_times', $meta['repeat_end_times'] );		
			
			$meta['repeat_end_date'] = ! empty( $_POST['repeat_end_date'] ) ? sanitize_text_field( $_POST['repeat_end_date'] ) : '';
			update_post_meta( $event_id, 'repeat_end_date', $meta['repeat_end_date'] );
			
			$this->create_recurring_events( $event_id, $meta );
		}
		
		// redirect
    	wp_redirect( aec_manage_events_page_link() );
   		exit();
	
	}
	
	/**
 	 * Create Recurrence event(s).
 	 *
 	 * @since    1.5.0
	 *
	 * @param    int      $main_event_id    Main Event ID.
	 * @param    array    $meta             Main Event Post Meta.
 	 */
	public function create_recurring_events( $main_event_id, $meta ) {
		
		$start_date = new DateTime( $meta['start_date_time'] );
		$end_date = new DateTime( $meta['end_date_time'] );
		
		$interval = $start_date->diff( $end_date );
		$meta['interval'] = $interval->format('P%YY%mM%dDT%HH%iM%sS');
	
		switch( $meta['repeat_type'] ) {
			case 'no_repeat':
				$this->add_event( $main_event_id, $meta, $start_date );
				break;
			case 'daily':
				if( empty( $meta['repeat_days'] ) ) return;			
				$this->process_daily_events( $main_event_id, $meta );
				break;
			case 'weekly' :
				if( empty( $meta['repeat_weeks'] ) || empty( $meta['repeat_week_days'] ) ) return;
				$this->process_weekly_events( $main_event_id, $meta );	
				break;
			case 'monthly':
				if( empty( $meta['repeat_months'] ) || empty( $meta['repeat_month_days'] ) ) return;
				$start_date->modify( 'first day of this month' );
				$this->process_monthly_events( $main_event_id, $meta );
				break;
		} 
		 		
	}
	
	/**
 	 * Process daily events.
 	 *
 	 * @since    1.5.0
	 *
	 * @param    int      $main_event_id    Main Event ID.
	 * @param    array    $meta             Main Event Post Meta.
 	 */
	function process_daily_events( $main_event_id, $meta ) {
		
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
			$this->add_event( $main_event_id, $meta, $date );
		}
		
	}
	
	/**
 	 * Process weekly events.
 	 *
 	 * @since    1.5.0
	 *
	 * @param    int      $main_event_id    Main Event ID.
	 * @param    array    $meta             Main Event Post Meta.
	 * @param    object   $start_date       Event Start Date.
	 * @param    int      $occurences       Number of Occurences.
 	 */
	public function process_weekly_events( $main_event_id, $meta, $start_date = '', $occurences = 0 ) {
		
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
					$this->add_event( $main_event_id, $meta, $date );
					
					if( ++$occurences >= $meta['repeat_end_times'] ) break;
				}
			}
			
			if( $occurences < $meta['repeat_end_times'] )	{
				$this->process_weekly_events( $main_event_id, $meta, $start_date->modify( '+'.( $meta['repeat_weeks'] * 7 ).' days' ), $occurences );
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
					if( $_date->format('U') > $initial_start_date->format('U') &&  $_date->format('U') <= $end->format('U') ) $this->add_event( $main_event_id, $meta, $_date );
				}
				
			}
			
		}
		
	}	
	
	/**
 	 * Process monthly events.
 	 *
 	 * @since    1.5.0
	 *
	 * @param    int       $main_event_id    Main Event ID.
	 * @param    array     $meta             Main Event Post Meta.
	 * @param    object    $start_date       Event Start Date.
	 * @param    int       $occurences       Number of Occurences.
 	 */
	function process_monthly_events( $main_event_id, $meta, $start_date = '', $occurences = 0 ) {
		
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
					$this->add_event( $main_event_id, $meta, $date );
					
					if( ++$occurences >= $meta['repeat_end_times'] ) break;
				}

			}
			
			if( $occurences < $meta['repeat_end_times'] ) {
				$this->process_monthly_events( $main_event_id, $meta, $start_date->modify( '+'.$meta['repeat_months'].' month' ), $occurences );
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
					if( $_date->format('U') > $initial_start_date->format('U') &&  $_date->format('U') <= $end->format('U') ) $this->add_event( $main_event_id, $meta, $_date );
				}
				
			}
			
		}
		
	}
	
	/**
 	 * Add Event.
 	 *
 	 * @since    1.5.0
	 *
	 * @param    int       $main_event_id    Main Event ID.
	 * @param    array     $meta             Main Event Post Meta.
	 * @param    object    $start_date       Event Start Date.
 	 */
	public function add_event( $main_event_id, $meta, $start_date ) {	
			
		$args = array(
			'post_type'    => 'aec_events',
			'post_title'   => $meta['title'],
			'post_status'  => 'publish',
			'post_content' => $meta['description'],
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

		wp_set_object_terms( $post_id, $meta['categories'], 'aec_categories', true );

		if( $meta['thumbnail_id'] > 0 ) {
			update_post_meta( $post_id, '_thumbnail_id', $meta['thumbnail_id'] );
		}
	   	
		update_post_meta( $post_id, 'parent', $main_event_id );		
		
	}
	
	/**
	 * Process the shortcode [aec_manage_venues].
	 *
	 * @since    1.5.0
	 */
	public function shortcode_aec_manage_venues() {     
		
		if( ! is_user_logged_in() ) {		
			return aec_login_form();			
		}	
		
		// Load dependencies
		wp_enqueue_style( AEC_PLUGIN_SLUG ); 
		
		// Vars
		$general_settings = get_option( 'aec_general_settings' );
		$events_settings  = get_option( 'aec_events_settings' ); 
		$page_settings    = get_option( 'aec_page_settings' );
		
		// Build query 
		$paged = aec_get_page_number();		
		
		$args = array(
			'post_type'      => 'aec_venues',
			'posts_per_page' => empty( $events_settings['events_per_page'] ) ? -1 : $events_settings['events_per_page'],
			'paged'          => $paged,
			'post_status'	 => 'publish',
			'author' 		 => get_current_user_id(),
			's'				 => isset( $_REQUEST['aec'] ) ? sanitize_text_field( $_REQUEST['aec'] ) : '',
		);
		
		$aec_query = new WP_Query( $args );
		
		// ...
		ob_start();
		include AEC_PLUGIN_DIR."public/partials/user/aec-public-manage-venues-display.php";
		return ob_get_clean();
		
	}
	
	/**
	 * Process the shortcode [aec_venue_form].
	 *
	 * @since    1.5.0
	 */
	public function shortcode_aec_venue_form() {   
	 	
		if( ! is_user_logged_in() ) {		
			return aec_login_form();			
		}		
		
		$venue_id = get_query_var( 'aec_id' ) ? (int) get_query_var( 'aec_id' ) : 0;
		$has_permission = true;
		
		if( $venue_id > 0 ) {
			if( ! aec_current_user_can( 'edit_post', $venue_id ) ) $has_permission = false;
		}
		
		if( ! $has_permission ) {
			return __( 'You do not have sufficient permissions to access this page.', 'another-events-calendar' );
		}
		
		// Load dependencies		
		wp_enqueue_script( AEC_PLUGIN_SLUG ); 
		wp_enqueue_script( AEC_PLUGIN_SLUG.'-bootstrap-validator' ); 
		wp_enqueue_script( AEC_PLUGIN_SLUG.'-google-map' );
		
		wp_enqueue_style( AEC_PLUGIN_SLUG );
		
		// Vars
		$general_settings = get_option( 'aec_general_settings' );
		$map_settings     = get_option( 'aec_map_settings' );
		
		$countries        = aec_get_countries();
		$default_location = $general_settings['default_location'];
		
		if( $venue_id > 0 ) {
			$post = get_post( $venue_id );
			
			$title       = $post->post_title;
			$description = $post->post_content;
			
			$address     = get_post_meta( $venue_id, 'address', true );
			$city        = get_post_meta( $venue_id, 'city', true );
			$state       = get_post_meta( $venue_id, 'state', true );
			$country     = get_post_meta( $venue_id, 'country', true );		
			$pincode     = get_post_meta( $venue_id, 'pincode', true );
			$phone       = get_post_meta( $venue_id, 'phone', true );
			$website     = get_post_meta( $venue_id, 'website', true );
			$hide_map    = get_post_meta( $venue_id, 'hide_map', true );
			$latitude    = get_post_meta( $venue_id, 'latitude', true );
			$longitude   = get_post_meta( $venue_id, 'longitude', true );		
		}
		
		// ...	
		ob_start();
		include AEC_PLUGIN_DIR."public/partials/user/aec-public-edit-venue-display.php";
		return ob_get_clean();
		
	}
	
	/**
	 * Create Venue.
	 *
	 * @since    1.5.0
	 *
	 * @param    int    $post_id    Event ID.   
	 */	
	public function save_venue( $post_id ) { 
			
		$args = array( 
			'ID'			=> $post_id,
			'post_type'     => 'aec_venues',
			'post_title'    => $_POST['title'],
			'post_content'	=> $_POST['description'],
			'post_status'   => 'publish',
		);
		
		$venue_id = wp_insert_post( $args );
		
		$address = ! empty( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : '';
		update_post_meta( $venue_id, 'address', $address );
					
		$city = ! empty( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';
		update_post_meta( $venue_id, 'city', $city );
						
		$state = ! empty( $_POST['state'] ) ? sanitize_text_field( $_POST['state'] ) : '';
		update_post_meta( $venue_id, 'state', $state );
			
		$country = ! empty( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
		update_post_meta( $venue_id, 'country', $country );

		$pincode = ! empty( $_POST['pincode'] ) ? sanitize_text_field( $_POST['pincode'] ) : '';
		update_post_meta( $venue_id, 'pincode', $pincode );
						
		$phone = ! empty( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
		update_post_meta( $venue_id, 'phone', $phone );
					
		$website = ! empty( $_POST['website'] ) ? esc_url_raw( $_POST['website'] ) : '';
		update_post_meta( $venue_id, 'website', $website );
					
		$hide_map = ! empty( $_POST['hide_map'] ) ? 1 : 0;
		update_post_meta( $venue_id, 'hide_map', $hide_map );
			
		$latitude = isset( $_POST['latitude'] ) ? sanitize_text_field( $_POST['latitude'] ) : '';
		update_post_meta( $venue_id, 'latitude', $latitude );  
	
		$longitude = isset( $_POST['longitude'] ) ? sanitize_text_field( $_POST['longitude'] ) : '';
		update_post_meta( $venue_id, 'longitude', $longitude ); 
		
		// redirect
    	wp_redirect( aec_manage_venues_page_link() );
   		exit();
		
	}
	
	/**
	 * Process the shortcode [aec_manage_organizers].
	 *
	 * @since    1.5.0
	 */
	public function shortcode_aec_manage_organizers() {     
		
		if( ! is_user_logged_in() ) {		
			return aec_login_form();			
		}	
		
		// Load dependencies
		wp_enqueue_style( AEC_PLUGIN_SLUG ); 
		
		// Vars
		$general_settings = get_option( 'aec_general_settings' );
		$events_settings  = get_option( 'aec_events_settings' );
		$page_settings    = get_option( 'aec_page_settings' ); 
		
		// Build query 
		$paged = aec_get_page_number();		
		
		$args = array(
			'post_type'      => 'aec_organizers',
			'posts_per_page' => empty( $events_settings['events_per_page'] ) ? -1 : $events_settings['events_per_page'],
			'paged'          => $paged,
			'post_status'	 => 'publish',
			'author' 		 => get_current_user_id(),
			's'				 => isset( $_REQUEST['aec'] ) ? sanitize_text_field( $_REQUEST['aec'] ) : '',
		);
		
		$aec_query = new WP_Query( $args );
		
		// ...
		ob_start();
		include AEC_PLUGIN_DIR."public/partials/user/aec-public-manage-organizers-display.php";
		return ob_get_clean();
		
	}
	
	/**
	 * Process the shortcode [aec_organizer_form].
	 *
	 * @since    1.5.0
	 */
	public function shortcode_aec_organizer_form() {   
	 	
		if( ! is_user_logged_in() ) {		
			return aec_login_form();			
		}		
		
		$organizer_id = get_query_var( 'aec_id' ) ? get_query_var( 'aec_id' ) : 0;
		$has_permission = true;
		
		if( $organizer_id > 0 ) {
			if( ! current_user_can( 'edit_post', $organizer_id ) ) $has_permission = false;
		}
		
		if( ! $has_permission ) {
			return __( 'You do not have sufficient permissions to access this page.', 'another-events-calendar' );
		}
		
		// Load dependencies		
		wp_enqueue_script( AEC_PLUGIN_SLUG ); 
		wp_enqueue_script( AEC_PLUGIN_SLUG.'-bootstrap-validator' ); 
		
		wp_enqueue_style( AEC_PLUGIN_SLUG );
		
		// Vars
		if( $organizer_id > 0 ) {
			$post = get_post( $organizer_id );
			
			$title       = $post->post_title;
			$description = $post->post_content;
			
			$attachment_id = get_post_meta( $organizer_id, '_thumbnail_id', true );
			if( $attachment_id ) $image = wp_get_attachment_url( $attachment_id );
			
			$phone   = get_post_meta( $organizer_id, 'phone', true );
			$website = get_post_meta( $organizer_id, 'website', true );
			$email   = get_post_meta( $organizer_id, 'email', true );			
		}
		
		// ...	
		ob_start();
		include AEC_PLUGIN_DIR."public/partials/user/aec-public-edit-organizer-display.php";
		return ob_get_clean();
		
	}
	
	/**
	 * Create Organizer.
	 *
	 * @since    1.5.0
	 *
	 * @param    int    $post_id    Event ID.   
	 */	
	public function save_organizer( $post_id ) { 
	
		$args = array( 
			'ID'			=> $post_id,
			'post_type'     => 'aec_organizers',
			'post_title'    => $_POST['title'],
			'post_content'	=> $_POST['description'],
			'post_status'   => 'publish',
		);
		
		$organizer_id = wp_insert_post( $args );
		
		$phone = ! empty( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
		update_post_meta( $organizer_id, 'phone', $phone );
					
		$website = ! empty( $_POST['website'] ) ? esc_url_raw( $_POST['website'] ) : '';
		update_post_meta( $organizer_id, 'website', $website );
		
		$email = ! empty( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
		update_post_meta( $organizer_id, 'email', $email );
		
		// Upload image
		if( $_FILES['image']['error'] === UPLOAD_ERR_OK ) {
		
			// require the needed files
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
				
			// then loop over the files that were sent and store them using media_handle_upload();
			if( $_FILES ) {
			
				foreach( $_FILES as $file => $array ) {
					$mime = $_FILES[ $file ]['type'];					
					if( ! in_array( $mime, array( 'image/jpeg', 'image/jpg', 'image/png' ) ) ) return;
					
					if( $_FILES[ $file ]['error'] !== UPLOAD_ERR_OK ) return;
					
					$thumbnail_id = media_handle_upload( $file, $organizer_id );
					
				}  
				
				if( $thumbnail_id > 0 ) {
					update_post_meta( $organizer_id, '_thumbnail_id', $thumbnail_id );
				} 
				
			}		
			
		}
		
		// redirect
    	wp_redirect( aec_manage_organizers_page_link() );
   		exit();
	
	}
	
	/**
	 * Delete an attachment.
	 *
	 * @since    1.5.0
	 * @access   public
	 */
	public function ajax_callback_delete_attachment() {
	
		if( isset( $_POST['attachment_id'] ) ) {
		
			$attachment_id = (int) $_POST['attachment_id'];
			$post_id       = (int) $_POST['post_id'];
			
			wp_delete_attachment( $attachment_id, true );
			delete_post_meta( $post_id, '_thumbnail_id' );
			
		}
		
		wp_die();
	
	}

}
