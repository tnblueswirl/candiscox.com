<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link          http://yendif.com
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/admin
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC_Admin Class
 *
 * @since    1.0.0
 */
class AEC_Admin {

	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {  

		wp_enqueue_style( 'jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );		
		wp_enqueue_style( AEC_PLUGIN_SLUG, AEC_PLUGIN_URL.'admin/css/aec-admin.css', array(), AEC_PLUGIN_VERSION, 'all' );
		
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$map_settings = get_option( 'aec_map_settings' );
		
		$api_key = ! empty( $map_settings['api_key'] ) ? '&key='.$map_settings['api_key'] : '';
		wp_enqueue_script( AEC_PLUGIN_SLUG.'-google-map', 'https://maps.googleapis.com/maps/api/js?v=3.exp'.$api_key );	
			
		wp_enqueue_script( AEC_PLUGIN_SLUG, AEC_PLUGIN_URL.'admin/js/aec-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), AEC_PLUGIN_VERSION, false );
		wp_localize_script( AEC_PLUGIN_SLUG, 'aec', array(
				'zoom_level' => ! empty( $map_settings['zoom_level'] ) ? (int) $map_settings['zoom_level'] : 5
			)
		);
		
	}
	
	 /**
	 * Set menu display order.
	 *
	 * @since    1.0.0
	 */
 	public function set_menu_order() {
	
		global $submenu;
		
		if( array_key_exists( 'edit.php?post_type=aec_events', $submenu ) ) {
			$before = $after = array();
			
			$settings_slug  = 'aec_settings';
			$recurring_slug = 'aec_recurring_events';
			
			foreach( $submenu['edit.php?post_type=aec_events'] as $item ) {
				if( strpos( $item[2], $recurring_slug ) !== false || $item[2] == $settings_slug ) {
					$after[]  = $item;
				} else {
					$before[] = $item;
				}
			}
			
			$submenu['edit.php?post_type=aec_events'] = array_values( array_merge( $before, $after ) );
		}
		
	}	 
	
	/**
	 * Display Admin Notices.
	 *
	 * @since    1.6.0
	 * @access   public
	 */
	public function admin_notices() {
		
		global $typenow;
		$post_type = array( 'aec_events', 'aec_venues', 'aec_organizers', 'aec_recurring_events' );
		
		if( in_array( $typenow, $post_type ) ) {
			?>
			<div class="updated notice notice-info">
            	<p>
                	<?php _e( 'Another Events Calendar', 'another-events-calendar' ); ?>:
                    <a href="https://yendif.com/wordpress/another-events-calendar/getting-started.html" target="_blank">
						<?php _e( 'Documentation', 'another-events-calendar' ); ?>
                    </a> | 
                   	<a href="mailto:admin@yendifplayer.com">
						<?php _e( 'Support E-Mail', 'another-events-calendar' ); ?>( admin@yendifplayer.com )
                    </a> | 
                    <a href="https://yendif.com/forum/another-events-calendar-for-wordpress.html" target="_blank">
						<?php _e( 'Ask in our Forum', 'another-events-calendar' ); ?>
                    </a>
              	</p>
			</div>
			<?php 
		}
		
	}	
	
	/**
	 * Change admin footer text.
	 *
	 * @since    1.6.0
	 * @access   public
	 *
	 * @param    string    $footer_text    WordPress admin footer text.
	 * @return   string    $footer_text    Updated footer text.
	 */
	public function admin_footer_text( $footer_text ) {  
		
		global $typenow;
		$post_type = array( 'aec_events', 'aec_venues', 'aec_organizers', 'aec_recurring_events' );
		
		if( in_array( $typenow, $post_type ) ) { 

			$footer_text = sprintf(
				esc_html__( 'Rate %1$sAnother Events Calendar%2$s %3$s', 'another-events-calendar' ),
				'<strong>',
				'</strong>',
				'<a href="https://wordpress.org/support/plugin/another-events-calendar/reviews/" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>' 
			);
			
		}
		
		return $footer_text;
		
	}
	
}