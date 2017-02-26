<?php

/**
 * Fired during plugin activation
 *
 * @link          http://yendif.com
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/includes
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**     
 * AEC_Activator Class
 *
 * @since    1.0.0
 */
class AEC_Activator {

	/**
	 * Called during the plugin activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {	 	
		
		// Insert general settings
		if( ! get_option( 'aec_general_settings' ) ) {
		
			$default_values = array(
				'bootstrap'            => array( 'css', 'javascript' ),
				'has_tags'             => 1,
				'has_venues'           => 1,
				'has_organizers'       => 1,
				'has_recurring_events' => 1,
				'show_comments'        => 1,
				'show_past_events'     => 0,
				'default_location'     => 'US',
				'show_credit_link'	   => 1
			);
			add_option( 'aec_general_settings', $default_values );
			
		}
		
		// Insert calendar settings
		if( ! get_option( 'aec_calendar_settings' ) ) {
			
			$default_values = array(
				'view_options'     	  => array( 'month', 'week', 'day' ),
				'default_view'     	  => 'month',
				'show_all_event_days' => 1
			);
			add_option( 'aec_calendar_settings', $default_values );
			
		}
			
		// Insert events settings
		if( ! get_option( 'aec_events_settings' ) ) {
			
			$default_values = array(
				'view_options'    => array( 'table', 'grid', 'blog' ),
				'default_view'    => 'table',
				'orderby'         => 'event_start_date',
				'order'           => 'asc',
				'no_of_cols'      => 3,
				'events_per_page' => 12
			);
			add_option( 'aec_events_settings', $default_values );
			
		}
		
		// Insert categories settings
		if( ! get_option( 'aec_categories_settings' ) ) {
			
			$default_values = array(
				'orderby'               => 'name',
				'order'                 => 'asc',
				'show_events_count'     => 1,
				'hide_empty_categories' => 0
		 	);
			add_option( 'aec_categories_settings', $default_values );
			
		}
			
		// Insert pages settings
		if( ! get_option( 'aec_page_settings' ) ) {
			
			$pages_1 = self::insert_pages();
			$pages_2 = self::insert_pages_1_5();
			$default_values = array_merge( $pages_1, $pages_2 );
			
			add_option( 'aec_page_settings', $default_values );
				
		} else {
		
			$page_settings = get_option( 'aec_page_settings' );
			
			if( ! array_key_exists( 'event_form', $page_settings ) ) {
				$new_pages  = self::insert_pages_1_5();
				$new_values = array_merge( $page_settings, $new_pages );
				update_option( 'aec_page_settings', $new_values );
			}
			
		}
		
		// Insert permalink settings
		if( ! get_option( 'aec_permalink_settings' ) ) {
			
			$default_values = array(
				'event_slug' => 'aec_events'
			);
			add_option( 'aec_permalink_settings', $default_values );
				
		}
			
		// Insert currency settings
		if( ! get_option( 'aec_currency_settings' ) ) {
			
			$default_values = array(
				'currency'            => 'USD',
				'position'            => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.'
			);
			add_option ('aec_currency_settings', $default_values);
				
		}
			
		// Insert map settings
		if( ! get_option( 'aec_map_settings' ) ) {
		
			$default_values = array(
				'enabled'    => 1,
				'api_key'    => '',
				'zoom_level' => 5
			);
			add_option ('aec_map_settings', $default_values);	
				
		}
		
		// Insert social share settings
		if( ! get_option( 'aec_socialshare_settings' ) ) {
		
			$default_values = array(
				'services' => array( 'facebook', 'twitter', 'gplus', 'linkedin', 'pinterest' ),
				'pages'    => array( 'categories', 'event_archives', 'event_detail' )
			);
			add_option ('aec_socialshare_settings', $default_values);	
				
		}
		
		// Add custom capabilities
		$roles = new AEC_Roles();
		$roles->add_caps();
		
		update_option( 'aec_version', AEC_PLUGIN_VERSION );

	}
	
	/**
	 * Insert pages required for the functional flow of the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function insert_pages() {
		
		$pages = array();
		
		// Insert calendar page
		$pages['calendar'] = wp_insert_post( array(
			'post_title'     => __( 'Calendar Page', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-calendar',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_calendar]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));	
		
		// Insert events page
		$pages['events'] = wp_insert_post( array(
			'post_title'     => __( 'Events Page', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-events',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_events]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));	
		
		// Insert categories page
		$pages['categories'] = wp_insert_post( array(
			'post_title'     => __( 'Categories Page', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-categories',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_categories]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));
				
		// Insert single category page
		$pages['category'] = wp_insert_post( array(
			'post_title'     => __( 'Single Category Page', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-category',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_category]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));
			
		// Insert single tag page
		$pages['tag'] = wp_insert_post( array(
			'post_title'     => __( 'Single Tag Page', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-tag',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_tag]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));		
			
		// Insert venue page
		$pages['venue'] = wp_insert_post( array(
			'post_title'     => __( 'Single Venue Page', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-venue',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_venue]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));		
			
		// Insert single organizer page
		$pages['organizer'] = wp_insert_post( array(
			'post_title'     => __( 'Single Organizer Page', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-organizer',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_organizer]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));
		
		// Insert search page
		$pages['search'] = wp_insert_post( array(
			'post_title'     => __( 'Search Results Page', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-search',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_search]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));	
				
		return $pages;
		
	}
	
	/**
	 * Insert pages for version 1.5.0+.
	 *
	 * @since    1.5.0
	 */
	public static function insert_pages_1_5() {
		
		$pages = array();
		
		// Insert event form page
		$pages['event_form'] = wp_insert_post( array(
			'post_title'     => __( 'Add New Event', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-event-form',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_event_form]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));
		
		// Insert manage evente page
		$pages['manage_events'] = wp_insert_post( array(
			'post_title'     => __( 'Manage Events', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-manage-events',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_manage_events]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));	
		
		// Insert venue form page
		$pages['venue_form'] = wp_insert_post( array(
			'post_title'     => __( 'Add New Venue', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-venue-form',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_venue_form]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));	
		
		// Insert manage venues page
		$pages['manage_venues'] = wp_insert_post( array(
			'post_title'     => __( 'Manage Venues', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-manage-venues',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_manage_venues]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));	
		
		// Insert organizer form page
		$pages['organizer_form'] = wp_insert_post( array(
			'post_title'     => __( 'Add New Organizer', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-organizer-form',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_organizer_form]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));	
		
		// Insert manage organizers page
		$pages['manage_organizers'] = wp_insert_post( array(
			'post_title'     => __( 'Manage Organizers', 'another-events-calendar' ),
			'post_type' 	 => 'page',
			'post_name'	 	 => 'aec-manage-organizers',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '[aec_manage_organizers]',
			'post_status'    => 'publish',
			'post_author'    => get_current_user_id()
		));	
				
		return $pages;
		
	}

}
