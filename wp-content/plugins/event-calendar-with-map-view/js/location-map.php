<script>
function initialize() {
	  <?php $post_id = sanitize_text_field($_REQUEST['post']);  ?>
	  <?php 
	  	$ecwmv_lat = get_post_meta($post_id,'ecwmv_lat',true);
	  	$ecwmv_lng = get_post_meta($post_id,'ecwmv_lng',true);
	  	$ecwmv_event_location = get_post_meta($post_id,'ecwmv_event_location',true);
	  	if(empty($ecwmv_lat)){$ecwmv_lat = '23.0300';}
	  	if(empty($ecwmv_lng)){$ecwmv_lng = '72.5800';}
	  	
	   ?>
	  map = new google.maps.Map(document.getElementById('map-canvas'),{
		  center: {lat: <?php echo $ecwmv_lat ?>, lng: <?php echo $ecwmv_lng; ?>},
		    zoom: 13});
	  var infowindow = new google.maps.InfoWindow();
	  var marker = new google.maps.Marker({
	    map: map,
	    position: {lat: <?php echo $ecwmv_lat ?>, lng: <?php echo $ecwmv_lng; ?>},
	    anchorPoint: new google.maps.Point(0, -29)
	  });

	  <?php if(!empty($ecwmv_event_location)){  ?>
		  infowindow.setContent('<?php echo $ecwmv_event_location; ?>');
		  infowindow.open(map, marker);  
	  <?php } ?>
	  
	
	  // Create the autocomplete object and associate it with the UI input control.
	  // Restrict the search to the default country, and to place type "cities".
	  autocomplete = new google.maps.places.Autocomplete(document.getElementById('ecwmv_event_location'));
	  places = new google.maps.places.PlacesService(map);
	
	  google.maps.event.addListener(autocomplete, 'place_changed', function(){
		  var place = autocomplete.getPlace();
	

		    // If the place has a geometry, then present it on a map.
		    if (place.geometry.viewport) {
		      map.fitBounds(place.geometry.viewport);
		    } else {
		      map.setCenter(place.geometry.location);
		      map.setZoom(17);  
		    }
		    marker.setIcon(/** @type {google.maps.Icon} */({
		      url: place.icon,
		      size: new google.maps.Size(71, 71),
		      origin: new google.maps.Point(0, 0),
		      anchor: new google.maps.Point(17, 34),
		      scaledSize: new google.maps.Size(35, 35)
		    }));
		    marker.setPosition(place.geometry.location);
		    marker.setVisible(true);
		    
		    
		    jQuery( 'p#ecwmv-temp-place' ).html( place.adr_address);
	     	
	     	var city = jQuery("#ecwmv-temp-place span.locality").text();
	     	var state = jQuery("#ecwmv-temp-place span.region").text();
	     	var country = jQuery("#ecwmv-temp-place span.country-name").text();
		    var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();
            jQuery('#ecwmv_city').val(city);
            jQuery('#ecwmv_state').val(state);
            jQuery('#ecwmv_country').val(country);
            jQuery('#ecwmv_lat').val(latitude);
            jQuery('#ecwmv_lng').val(longitude);
         
                    
		    var address = '';
		    if (place.address_components) {
		      address = [
		        (place.address_components[0] && place.address_components[0].short_name || ''),
		        (place.address_components[1] && place.address_components[1].short_name || ''),
		        (place.address_components[2] && place.address_components[2].short_name || '')
		      ].join(' ');
		    }
			
		    infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
		    infowindow.open(map, marker);
	  });
 }
google.maps.event.addDomListener(window, "load", initialize);
</script>