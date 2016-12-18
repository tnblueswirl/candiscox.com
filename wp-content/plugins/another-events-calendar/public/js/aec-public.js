(function( $ ) {
	'use strict';

	/*
	 *  This function will render a Google Map onto the selected jQuery element
	 *
	 *  @since	1.0.0
	 */
	function aec_map( $el ) {
		
		// var
		var $marker = $el.find('.marker');

		// vars
		var args = {
			zoom		: parseInt( aec.zoom ),
			center		: new google.maps.LatLng( 0, 0 ),
			mapTypeId	: google.maps.MapTypeId.ROADMAP
		};
		
		// create map	        	
		var map = new google.maps.Map( $el[0], args );
		
		// add a marker reference
		map.marker = '';		
		
		// Add marker
		var latitude = $('#aec-latitude').val();
		var geocoder = new google.maps.Geocoder();
		
		if( '' == latitude ) {
						
			var default_location = $el.data('default_location');
			geocoder.geocode( { 'address': default_location }, function( results, status ) {
      			if( status == google.maps.GeocoderStatus.OK ) {
					aec_update_latlng( results[0].geometry.location.lat(), results[0].geometry.location.lng() );
					$marker.data( 'latitude', results[0].geometry.location.lat() );
					$marker.data( 'longitude', results[0].geometry.location.lng() );
					aec_add_marker( $marker, map );		
					aec_center_map( map );					
      			}
    		});
			
		} else {
			
			aec_add_marker( $marker, map );		
			aec_center_map( map );
			
		};
		
		// Update marker position on address change
		$('.aec-map-field').on('blur', function() {
			var address = [];
			
			if( $('#aec-venue-address').val() ) address.push( $('#aec-venue-address').val() );
			if( $('#aec-venue-city').val() ) address.push( $('#aec-venue-city').val() );
			if( $('#aec-venue-country').val() ) address.push( $("#aec-venue-country option:selected").text() );
			if( $('#aec-venue-state').val() ) address.push( $('#aec-venue-state').val() );
			if( $('#aec-venue-pincode').val() ) address.push( $('#aec-venue-pincode').val() );
			
			address = address.join();

			geocoder.geocode( { 'address': address}, function( results, status ) {
      			if( status == google.maps.GeocoderStatus.OK) {
					map.marker.setPosition( results[0].geometry.location );
					aec_update_latlng( results[0].geometry.location.lat(), results[0].geometry.location.lng() );
					aec_center_map( map );					
      			}
    		});
			
		});
		
		// When modal window is open, this script resizes the map and resets the map center
		$( '#aec-map-modal' ).on( "shown.bs.modal", function() {
			google.maps.event.trigger( map, "resize" );
      		return aec_center_map( map );
		});
		
					
	}
	
	/*
	*  This function will add a marker to the selected Google Map
	*
	*  @since	1.0.0
	*/	
	function aec_add_marker( $marker, map ) {
	
		// var
		var latlng = new google.maps.LatLng( $marker.data('latitude'), $marker.data('longitude') );
	
		// create marker
		var marker = new google.maps.Marker({
			position	: latlng,
			map			: map,
			draggable   : false
		});
	
		map.marker = marker;
		
		// if marker contains HTML, add it to an infoWindow
		if( $marker.html() ) {
			// create info window
			var infowindow = new google.maps.InfoWindow({
				content		: $marker.html()
														
			});
	
			// show info window when marker is clicked
			google.maps.event.addListener(marker, 'click', function() {
	
				infowindow.open( map, marker );
	
			});
			
		}
	
	}		  
	
	/*
	 *  This function will center the map, showing all markers attached to this map
	 *	
	 *  @since	1.0.0
	 */	
	function aec_center_map( map ) {
	
		// vars
		var bounds = new google.maps.LatLngBounds();
	
		// loop through all markers and create bounds
		var latlng = new google.maps.LatLng( map.marker.position.lat(), map.marker.position.lng() );
		bounds.extend( latlng );
	
		map.setCenter( bounds.getCenter() );
	
	}
	
	/*
	*  Update latlng values in the event or venue forms.
	*
	*  @since	1.5.4
	*/	
	function aec_update_latlng( lat, lng ) {
		
		$( '#aec-latitude' ).val( lat );
		$( '#aec-longitude' ).val( lng );
		
	}
	
	/*
	 *  On Document Ready
	 *
	 *  @since	1.0.0
	 */	
	$(function() {
	
		// Add date picker in the search widget(s)
		$( ".aec-date-picker" ).datepicker({
			dateFormat: 'yy-mm-dd'
		});	
		
		// Add map in custom post type 'aec_venues'
		$( '.aec-map' ).each(function() {
			aec_map( $(this) );
		});
		
		// Add Mini-calendar
		$( 'body' ).on( 'click', '.aec-mini-calendar-nav', function( e ) {
			e.preventDefault();

			var data = {
				'action'	: 'aec_mini_calendar',
				'mo'        : $(this).data('month'),
				'yr'    	: $(this).data('year'),
				'widget_id' : $(this).data('id'),
			};	
			
			$( '.aec-spinner-container', '#' + data['widget_id'] ).html( '<div class="aec-spinner"></div>' );
			
			jQuery.post( aec.ajaxurl, data, function( response ) {
				$( '#' + data['widget_id'] ).replaceWith( response );
			});
		
		});
		
		// Show / Hide time selectors in the event submission form
		$( '#aec-all-day-event' ).on( 'change', function() {
											  
			if( $( "#aec-all-day-event" ).is( ":checked" ) ) { 
				$( '.aec-event-time-fields' ).hide(); 
			} else {
				$( '.aec-event-time-fields' ).show();
			}
			
		}).trigger( 'change' );	
		
		// Show / Hide recurring events in the event submission form
		$( '#aec-recurring-event' ).on( 'change', function () {
											  
			if ( $( "#aec-recurring-event" ).is( ":checked" ) ) { 
				$( '.aec-recurring-event-fields' ).show(); 
			} else {
				$( '.aec-recurring-event-fields' ).hide();
			}
			
		});
		
		// Show / Hide recurring settings based on the recurring type in the event submission form
		$( '#aec-recurring-frequency' ).on( 'change', function() {

			$( '.aec-recurring-settings' ).hide();
			
			var value = $( this ).val();
			switch( value ) {
				case 'daily' :
					$( '.aec-daily-recurrence' ).show();
					break;
				case 'weekly' :
					$( '.aec-weekly-recurrence' ).show();
					break;
				case 'monthly' :
					$( '.aec-monthly-recurrence' ).show();
					break;
			};
			
		}).trigger( 'change' );
		
		// Show / Hide Venue fields in the event submission form
		$( '#aec-venues' ).on( 'change', function() {											 
			
        	var value = $( this ).val();
			
        	 if( -1 == value ) {
            	$( '#aec-venue-fields' ).show();
			} else {
				$( '#aec-venue-fields' ).hide();
			}
			
    	}).trigger( 'change' );	
		
		// Show / Hide Organizer fields in the event submission form
		$( '#aec-add-new-organizer' ).on( 'click', function() {

			$( '#aec-organizer-fields' ).find( '.aec-organizer-fields .aec-organizer-group-id' ).html( '#' + $( '.aec-organizer-fields' ).length );
			var $clone = $( '#aec-organizer-fields' ).find( '.aec-organizer-fields' ).clone( false ); 
			$( '#aec-organizer-fields-container' ).append( $clone );
			
		});
		
		// Delete image attachment
		$( '#aec-img-delete' ).on( 'click', function( e ) {

			e.preventDefault();
			
			var $this = $( this );
			
			var data = {
				'action'        : 'aec_public_delete_attachment',
				'post_id'       : $this.data( 'post_id' ),
				'attachment_id' : $this.data( 'attachment_id' )
			};
			
			$.post( aec.ajaxurl, data, function( response ) {
				$( '#aec-img-preview' ).remove();
			});
			
		});
					
	});

})( jQuery );
