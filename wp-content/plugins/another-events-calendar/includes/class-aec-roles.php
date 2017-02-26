<?php

/**
 * Custom Capabilities.
 *
 * @link          https://yendif.com
 * @since         1.6.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/includes
 */
 
// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC_Roles Class
 *
 * @since    1.6.0
 * @access   public
 */
class AEC_Roles {

	/**
	 * Add new capabilities.
	 *
	 * @since    1.6.0
	 * @access   public
	 */
	public function add_caps() {
	
		global $wp_roles;

		if( class_exists( 'WP_Roles' ) ) {
			if( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if( is_object( $wp_roles ) ) {

			// Add the "administrator" capabilities
			$capabilities = $this->get_core_caps();
			foreach( $capabilities as $cap_group ) {
				foreach( $cap_group as $cap ) {
					$wp_roles->add_cap( 'administrator', $cap );
				}
			}
			$wp_roles->add_cap( 'administrator', 'manage_aec_options' );
			
			// Add the "editor" capabilities
			$wp_roles->add_cap( 'editor', 'edit_aec_events' );			
			$wp_roles->add_cap( 'editor', 'edit_others_aec_events' );			
			$wp_roles->add_cap( 'editor', 'publish_aec_events' );			
			$wp_roles->add_cap( 'editor', 'read_private_aec_events' );	
			$wp_roles->add_cap( 'editor', 'delete_aec_events' );			
			$wp_roles->add_cap( 'editor', 'delete_private_aec_events' );
			$wp_roles->add_cap( 'editor', 'delete_published_aec_events' );
			$wp_roles->add_cap( 'editor', 'delete_others_aec_events' );
			$wp_roles->add_cap( 'editor', 'edit_private_aec_events' );
			$wp_roles->add_cap( 'editor', 'edit_published_aec_events' );
			
			$wp_roles->add_cap( 'editor', 'edit_aec_organizers' );			
			$wp_roles->add_cap( 'editor', 'edit_others_aec_organizers' );			
			$wp_roles->add_cap( 'editor', 'publish_aec_organizers' );			
			$wp_roles->add_cap( 'editor', 'read_private_aec_organizers' );	
			$wp_roles->add_cap( 'editor', 'delete_aec_organizers' );			
			$wp_roles->add_cap( 'editor', 'delete_private_aec_organizers' );
			$wp_roles->add_cap( 'editor', 'delete_published_aec_organizers' );
			$wp_roles->add_cap( 'editor', 'delete_others_aec_organizers' );
			$wp_roles->add_cap( 'editor', 'edit_private_aec_organizers' );
			$wp_roles->add_cap( 'editor', 'edit_published_aec_organizers' );
			
			$wp_roles->add_cap( 'editor', 'edit_aec_venues' );			
			$wp_roles->add_cap( 'editor', 'edit_others_aec_venues' );			
			$wp_roles->add_cap( 'editor', 'publish_aec_venues' );			
			$wp_roles->add_cap( 'editor', 'read_private_aec_venues' );	
			$wp_roles->add_cap( 'editor', 'delete_aec_venues' );			
			$wp_roles->add_cap( 'editor', 'delete_private_aec_venues' );
			$wp_roles->add_cap( 'editor', 'delete_published_aec_venues' );
			$wp_roles->add_cap( 'editor', 'delete_others_aec_venues' );
			$wp_roles->add_cap( 'editor', 'edit_private_aec_venues' );
			$wp_roles->add_cap( 'editor', 'edit_published_aec_venues' );
			
			// Add the "author" capabilities
			$wp_roles->add_cap( 'author', 'edit_aec_events' );		
			$wp_roles->add_cap( 'author', 'publish_aec_events' );
			$wp_roles->add_cap( 'author', 'delete_aec_events' );
			$wp_roles->add_cap( 'author', 'delete_published_aec_events' );
			$wp_roles->add_cap( 'author', 'edit_published_aec_events' );
			
			$wp_roles->add_cap( 'author', 'edit_aec_organizers' );						
			$wp_roles->add_cap( 'author', 'publish_aec_organizers' );
			$wp_roles->add_cap( 'author', 'delete_aec_organizers' );
			$wp_roles->add_cap( 'author', 'delete_published_aec_organizers' );
			$wp_roles->add_cap( 'author', 'edit_published_aec_organizers' );
			
			$wp_roles->add_cap( 'author', 'edit_aec_venues' );						
			$wp_roles->add_cap( 'author', 'publish_aec_venues' );
			$wp_roles->add_cap( 'author', 'delete_aec_venues' );
			$wp_roles->add_cap( 'author', 'delete_published_aec_venues' );
			$wp_roles->add_cap( 'author', 'edit_published_aec_venues' );
			
			// Add the "contributor" capabilities
			$wp_roles->add_cap( 'contributor', 'edit_aec_events' );						
			$wp_roles->add_cap( 'contributor', 'publish_aec_events' );
			$wp_roles->add_cap( 'contributor', 'delete_aec_events' );
			$wp_roles->add_cap( 'contributor', 'delete_published_aec_events' );
			$wp_roles->add_cap( 'contributor', 'edit_published_aec_events' );

			$wp_roles->add_cap( 'contributor', 'edit_aec_organizers' );						
			$wp_roles->add_cap( 'contributor', 'publish_aec_organizers' );
			$wp_roles->add_cap( 'contributor', 'delete_aec_organizers' );
			$wp_roles->add_cap( 'contributor', 'delete_published_aec_organizers' );
			$wp_roles->add_cap( 'contributor', 'edit_published_aec_organizers' );

			$wp_roles->add_cap( 'contributor', 'edit_aec_venues' );						
			$wp_roles->add_cap( 'contributor', 'publish_aec_venues' );
			$wp_roles->add_cap( 'contributor', 'delete_aec_venues' );
			$wp_roles->add_cap( 'contributor', 'delete_published_aec_venues' );
			$wp_roles->add_cap( 'contributor', 'edit_published_aec_venues' );
			
			// Add the "subscriber" capabilities
			$wp_roles->add_cap( 'subscriber', 'edit_aec_events' );						
			$wp_roles->add_cap( 'subscriber', 'publish_aec_events' );
			$wp_roles->add_cap( 'subscriber', 'delete_aec_events' );
			$wp_roles->add_cap( 'subscriber', 'delete_published_aec_events' );
			$wp_roles->add_cap( 'subscriber', 'edit_published_aec_events' );

			$wp_roles->add_cap( 'subscriber', 'edit_aec_organizers' );						
			$wp_roles->add_cap( 'subscriber', 'publish_aec_organizers' );
			$wp_roles->add_cap( 'subscriber', 'delete_aec_organizers' );
			$wp_roles->add_cap( 'subscriber', 'delete_published_aec_organizers' );
			$wp_roles->add_cap( 'subscriber', 'edit_published_aec_organizers' );

			$wp_roles->add_cap( 'subscriber', 'edit_aec_venues' );	
			$wp_roles->add_cap( 'subscriber', 'publish_aec_venues' );
			$wp_roles->add_cap( 'subscriber', 'delete_aec_venues' );
			$wp_roles->add_cap( 'subscriber', 'delete_published_aec_venues' );
			$wp_roles->add_cap( 'subscriber', 'edit_published_aec_venues' );
			
		}
		
	}

	/**
	 * Gets the core post type capabilities.
	 *
	 * @since    1.6.0
	 * @access   public
	 *
	 * @return   array    $capabilities    Core post type capabilities.
	 */
	public function get_core_caps() {
	
		$capabilities = array();

		$capability_types = array( 'event', 'venue', 'organizer' );
		
		foreach( $capability_types as $capability_type ) {
		
			$capabilities[ $capability_type ] = array(
				"edit_aec_{$capability_type}",
				"read_aec_{$capability_type}",
				"delete_aec_{$capability_type}",
				"edit_aec_{$capability_type}s",
				"edit_others_aec_{$capability_type}s",
				"publish_aec_{$capability_type}s",
				"read_private_aec_{$capability_type}s",
				"delete_aec_{$capability_type}s",
				"delete_private_aec_{$capability_type}s",
				"delete_published_aec_{$capability_type}s",
				"delete_others_aec_{$capability_type}s",
				"edit_private_aec_{$capability_type}s",
				"edit_published_aec_{$capability_type}s",
			);
		}

		return $capabilities;
		
	}
	
	/**
	 * Filter a user's capabilities depending on specific context and/or privilege.
	 *
	 * @since    1.6.0
	 * @access   public
	 *
	 * @param    array     $caps       Returns the user's actual capabilities.
	 * @param    string    $cap        Capability name.
	 * @param    int       $user_id    The user ID.
	 * @param    array     $args       Adds the context to the cap. Typically the object ID.
	 * @return   array                 Actual capabilities for meta capability.
	 */
	public function meta_caps( $caps, $cap, $user_id, $args ) {
		
		$capabilities = array( 
			'edit_aec_event', 
			'delete_aec_event', 
			'read_aec_event', 
			'edit_aec_organizer', 
			'delete_aec_organizer', 
			'read_aec_organizer', 
			'edit_aec_venue', 
			'delete_aec_venue', 
			'read_aec_venue'
		);
		
		// If editing, deleting, or reading an AEC item, get the post and post type object.
		if( in_array( $cap, $capabilities ) ) {
			$post = get_post( $args[0] );
			$post_type = get_post_type_object( $post->post_type );

			// Set an empty array for the caps.
			$caps = array();
		}
		
		// If editing an event, assign the required capability.
		if( 'edit_aec_event' == $cap ) {
			if( $user_id == $post->post_author )
				$caps[] = $post_type->cap->edit_aec_events;
			else
				$caps[] = $post_type->cap->edit_others_aec_events;
		}
	
		// If deleting an event, assign the required capability.
		if( 'delete_aec_event' == $cap ) {
			if( $user_id == $post->post_author )
				$caps[] = $post_type->cap->delete_aec_events;
			else
				$caps[] = $post_type->cap->delete_others_aec_events;
		}
	
		// If reading a private event, assign the required capability.
		if( 'read_aec_event' == $cap ) {
			if( 'private' != $post->post_status )
				$caps[] = 'read';
			elseif ( $user_id == $post->post_author )
				$caps[] = 'read';
			else
				$caps[] = $post_type->cap->read_private_aec_events;
		}
		
		// If editing a venue, assign the required capability.
		if( 'edit_aec_venue' == $cap ) {
			if( $user_id == $post->post_author )
				$caps[] = $post_type->cap->edit_aec_venues;
			else
				$caps[] = $post_type->cap->edit_others_aec_venues;
		}
	
		// If deleting a venue, assign the required capability.
		if( 'delete_aec_venue' == $cap ) {
			if( $user_id == $post->post_author )
				$caps[] = $post_type->cap->delete_aec_venues;
			else
				$caps[] = $post_type->cap->delete_others_aec_venues;
		}
	
		// If reading a private venue, assign the required capability.
		if( 'read_aec_venue' == $cap ) {
			if( 'private' != $post->post_status )
				$caps[] = 'read';
			elseif ( $user_id == $post->post_author )
				$caps[] = 'read';
			else
				$caps[] = $post_type->cap->read_private_aec_venues;
		}
		
		// If editing an organizer, assign the required capability.
		if( 'edit_aec_organizer' == $cap ) {
			if( $user_id == $post->post_author )
				$caps[] = $post_type->cap->edit_aec_organizers;
			else
				$caps[] = $post_type->cap->edit_others_aec_organizers;
		}
	
		// If deleting an organizer, assign the required capability.
		if( 'delete_aec_organizer' == $cap ) {
			if( $user_id == $post->post_author )
				$caps[] = $post_type->cap->delete_aec_organizers;
			else
				$caps[] = $post_type->cap->delete_others_aec_organizers;
		}
	
		// If reading a private organizer, assign the required capability.
		if( 'read_aec_organizer' == $cap ) {
			if( 'private' != $post->post_status )
				$caps[] = 'read';
			elseif ( $user_id == $post->post_author )
				$caps[] = 'read';
			else
				$caps[] = $post_type->cap->read_private_aec_organizers;
		}
		
		// Return the capabilities required by the user.
		return $caps;

	}
	
	/**
	 * Remove core post type capabilities (called on uninstall).
	 *
	 * @since    1.6.0
	 * @access   public
	 */
	 public function remove_caps() {
	
		global $wp_roles;
	
		if( class_exists( 'WP_Roles' ) ) {
			if( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}
	
		if( is_object( $wp_roles ) ) {
		
			// Remove the "administrator" Capabilities
			$capabilities = $this->get_core_caps();
	
			foreach( $capabilities as $cap_group ) {
				foreach( $cap_group as $cap ) {
					$wp_roles->remove_cap( 'administrator', $cap );
				}
			}
			$wp_roles->remove_cap( 'administrator', 'manage_aec_options' );
			
			// Remove the "editor" capabilities
			$wp_roles->remove_cap( 'editor', 'edit_aec_events' );			
			$wp_roles->remove_cap( 'editor', 'edit_others_aec_events' );			
			$wp_roles->remove_cap( 'editor', 'publish_aec_events' );			
			$wp_roles->remove_cap( 'editor', 'read_private_aec_events' );
			$wp_roles->remove_cap( 'editor', 'delete_aec_events' );			
			$wp_roles->remove_cap( 'editor', 'delete_private_aec_events' );
			$wp_roles->remove_cap( 'editor', 'delete_published_aec_events' );
			$wp_roles->remove_cap( 'editor', 'delete_others_aec_events' );
			$wp_roles->remove_cap( 'editor', 'edit_private_aec_events' );
			$wp_roles->remove_cap( 'editor', 'edit_published_aec_events' );
			
			$wp_roles->remove_cap( 'editor', 'edit_aec_organizers' );			
			$wp_roles->remove_cap( 'editor', 'edit_others_aec_organizers' );			
			$wp_roles->remove_cap( 'editor', 'publish_aec_organizers' );			
			$wp_roles->remove_cap( 'editor', 'read_private_aec_organizers' );
			$wp_roles->remove_cap( 'editor', 'delete_aec_organizers' );			
			$wp_roles->remove_cap( 'editor', 'delete_private_aec_organizers' );
			$wp_roles->remove_cap( 'editor', 'delete_published_aec_organizers' );
			$wp_roles->remove_cap( 'editor', 'delete_others_aec_organizers' );
			$wp_roles->remove_cap( 'editor', 'edit_private_aec_organizers' );
			$wp_roles->remove_cap( 'editor', 'edit_published_aec_organizers' );
			
			$wp_roles->remove_cap( 'editor', 'edit_aec_venues' );			
			$wp_roles->remove_cap( 'editor', 'edit_others_aec_venues' );			
			$wp_roles->remove_cap( 'editor', 'publish_aec_venues' );			
			$wp_roles->remove_cap( 'editor', 'read_private_aec_venues' );
			$wp_roles->remove_cap( 'editor', 'delete_aec_venues' );			
			$wp_roles->remove_cap( 'editor', 'delete_private_aec_venues' );
			$wp_roles->remove_cap( 'editor', 'delete_published_aec_venues' );
			$wp_roles->remove_cap( 'editor', 'delete_others_aec_venues' );
			$wp_roles->remove_cap( 'editor', 'edit_private_aec_venues' );
			$wp_roles->remove_cap( 'editor', 'edit_published_aec_venues' );
			
			// Remove the "author" capabilities
			$wp_roles->remove_cap( 'author', 'edit_aec_events' );						
			$wp_roles->remove_cap( 'author', 'publish_aec_events' );
			$wp_roles->remove_cap( 'author', 'delete_aec_events' );
			$wp_roles->remove_cap( 'author', 'delete_published_aec_events' );
			$wp_roles->remove_cap( 'author', 'edit_published_aec_events' );
			
			$wp_roles->remove_cap( 'author', 'edit_aec_organizers' );						
			$wp_roles->remove_cap( 'author', 'publish_aec_organizers' );
			$wp_roles->remove_cap( 'author', 'delete_aec_organizers' );
			$wp_roles->remove_cap( 'author', 'delete_published_aec_organizers' );
			$wp_roles->remove_cap( 'author', 'edit_published_aec_organizers' );
			
			$wp_roles->remove_cap( 'author', 'edit_aec_venues' );						
			$wp_roles->remove_cap( 'author', 'publish_aec_venues' );
			$wp_roles->remove_cap( 'author', 'delete_aec_venues' );
			$wp_roles->remove_cap( 'author', 'delete_published_aec_venues' );
			$wp_roles->remove_cap( 'author', 'edit_published_aec_venues' );
			
			// Remove the "contributor" capabilities
			$wp_roles->remove_cap( 'contributor', 'edit_aec_events' );						
			$wp_roles->remove_cap( 'contributor', 'publish_aec_events' );
			$wp_roles->remove_cap( 'contributor', 'delete_aec_events' );
			$wp_roles->remove_cap( 'contributor', 'delete_published_aec_events' );
			$wp_roles->remove_cap( 'contributor', 'edit_published_aec_events' );
			
			$wp_roles->remove_cap( 'contributor', 'edit_aec_organizers' );						
			$wp_roles->remove_cap( 'contributor', 'publish_aec_organizers' );
			$wp_roles->remove_cap( 'contributor', 'delete_aec_organizers' );
			$wp_roles->remove_cap( 'contributor', 'delete_published_aec_organizers' );
			$wp_roles->remove_cap( 'contributor', 'edit_published_aec_organizers' );
			
			$wp_roles->remove_cap( 'contributor', 'edit_aec_venues' );						
			$wp_roles->remove_cap( 'contributor', 'publish_aec_venues' );
			$wp_roles->remove_cap( 'contributor', 'delete_aec_venues' );
			$wp_roles->remove_cap( 'contributor', 'delete_published_aec_venues' );
			$wp_roles->remove_cap( 'contributor', 'edit_published_aec_venues' );
			
			// Remove the "subscriber" capabilities
			$wp_roles->remove_cap( 'subscriber', 'edit_aec_events' );						
			$wp_roles->remove_cap( 'subscriber', 'publish_aec_events' );
			$wp_roles->remove_cap( 'subscriber', 'delete_aec_events' );
			$wp_roles->remove_cap( 'subscriber', 'delete_published_aec_events' );
			$wp_roles->remove_cap( 'subscriber', 'edit_published_aec_events' );
			
			$wp_roles->remove_cap( 'subscriber', 'edit_aec_organizers' );						
			$wp_roles->remove_cap( 'subscriber', 'publish_aec_organizers' );
			$wp_roles->remove_cap( 'subscriber', 'delete_aec_organizers' );
			$wp_roles->remove_cap( 'subscriber', 'delete_published_aec_organizers' );
			$wp_roles->remove_cap( 'subscriber', 'edit_published_aec_organizers' );
			
			$wp_roles->remove_cap( 'subscriber', 'edit_aec_venues' );						
			$wp_roles->remove_cap( 'subscriber', 'publish_aec_venues' );
			$wp_roles->remove_cap( 'subscriber', 'delete_aec_venues' );
			$wp_roles->remove_cap( 'subscriber', 'delete_published_aec_venues' );
			$wp_roles->remove_cap( 'subscriber', 'edit_published_aec_venues' );
	
		}
		
	}
	
}
