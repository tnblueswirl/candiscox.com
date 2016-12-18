<?php

/**
 * Venues.
 *
 * @link          http://yendif.com
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/public
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC_Public_Venues Class
 *
 * @since    1.0.0
 */
class  AEC_Public_Venues {

	/**
	 * Get things started.
	 *
	 * @since    1.0.0
	 */
	public function __construct( ) {
		
		add_shortcode( 'aec_venue', array( $this, 'shortcode_aec_venue' ) );

	}

	/**
	 * Process the shortcode [aec_venue];
	 *
	 * @since    1.0.0
	 */
	function shortcode_aec_venue( $atts ) {

		// Load dependencies
		wp_enqueue_style( AEC_PLUGIN_SLUG );
		
		wp_enqueue_script( AEC_PLUGIN_SLUG.'-google-map' );
		wp_enqueue_script( AEC_PLUGIN_SLUG.'-bootstrap' );
		wp_enqueue_script( AEC_PLUGIN_SLUG );
		 
		// Vars
		$general_settings = get_option( 'aec_general_settings' );
		$events_settings  = get_option( 'aec_events_settings' );
		$map_settings     = get_option( 'aec_map_settings' );
		
		$atts = shortcode_atts( 
   			array(
				'header'	  => 1,
				'view'		  => $events_settings['default_view'],
        		'venue'   	  => '',
				'past_events' => empty( $general_settings['show_past_events'] ) ? 0 : 1,
				'orderby'	  => $events_settings['orderby'],
				'order'		  => $events_settings['order'],
				'limit'       => $events_settings['events_per_page'],
				'pagination'  => 1,
    		), 
    		$atts
		);
		
		$venue_slug = get_query_var( 'aec_venue' ) ? get_query_var( 'aec_venue' ) : '';
		$error = 0;
		
		if( $venue_slug ) {
			$venue = get_page_by_path( $venue_slug, OBJECT, 'aec_venues' );
		} else {
			if( $atts['venue'] ) $venue = get_post( (int) $atts['venue'] );
		}
		
		if( empty( $venue ) ) $error = 1;
		
		if( $error ) return __( 'Sorry, no results matched your criteria.', 'another-events-calendar' );
		
		$has_map   = empty( $map_settings['enabled'] ) ? false : true;
		$latitude  = get_post_meta( $venue->ID, 'latitude', true );
		$longitude = get_post_meta( $venue->ID, 'longitude', true );
		
		$has_header     = $atts['header'];
		$has_pagination = $atts['pagination'];
		
		$view_options = isset( $events_settings['view_options'] ) ? $events_settings['view_options'] : array();
		$view_options[] = sanitize_text_field( $atts['view'] );
		$view_options = array_unique( $view_options );

		$view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : sanitize_text_field( $atts['view'] );
		
		$has_recurring_link = ! empty( $general_settings['has_recurring_events'] ) ? 1 : 0;
		$no_of_cols         = empty( $events_settings['no_of_cols'] ) ? 1 : $events_settings['no_of_cols'] ;
		$span               = round( 12 / $no_of_cols );
		$count              = 0;

		// Build query 
		$paged = aec_get_page_number();

		$args = array(
			'post_type'      => 'aec_events', 
			'posts_per_page' => empty( $atts['limit'] ) ? -1 : (int) $atts['limit'],
			'order'  		 => sanitize_text_field( $atts['order'] ),
			'paged'          => $paged,
			'post_status'	 => 'publish',
		);

		$meta_queries = array();
		
		$meta_queries[] = array( 
			array( 
				'key' => 'venue_id', 
				'value' => $venue->ID, 
				'compare' => '=' 
			) 
		);

		if( empty( $atts['past_events'] ) ) { 
			$meta_queries[] = array(
				'relation' => 'OR',
				array(
					'key'     => 'start_date_time',
					'value'	  => current_time('mysql'),
					'compare' => '>=',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'end_date_time',
					'value'	  => current_time('mysql'),
					'compare' => '>=',
					'type'    => 'DATETIME'
				)
			);
		} 
		
		$count_meta_queries = count( $meta_queries );
		if( $count_meta_queries ) {
			$args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $meta_queries ) : array( $meta_queries );
		}
		
		switch( trim( $atts['orderby'] ) ) {
			case 'date':
				$args['orderby'] = 'date';
				break;
			case 'title':
				$args['orderby'] = 'title';
				break;
			case 'event_start_date':
				$args['meta_key'] = 'start_date_time';
				$args['orderby']  = 'meta_value';
				break;
		}
		
		$aec_query = new WP_Query( $args );
		
		ob_start();
		if( $has_header ) include AEC_PLUGIN_DIR.'public/partials/venues/aec-public-venue-header-display.php';
		if( $aec_query->have_posts() ) {
			global $post;
			include AEC_PLUGIN_DIR."public/partials/events/aec-public-events-$view-display.php";
		} else {
			_e( 'No events found.', 'another-events-calendar' );
		}
		return ob_get_clean();
		
	}

}
