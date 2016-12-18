<?php

/**
 * Markup the venue form.
 */ 
?>

<div class="aec aec-venue-form">
	<form method="post" action="<?php echo aec_venue_form_page_link(); ?>" class="form-vertical" data-toggle="validator" role="form">

		<div class="panel panel-default">
        	<div class="panel-body"> 
                <div class="form-group">
                    <label for="aec-event-name" class="control-label"><?php _e( 'Venue Title', 'another-events-calendar' ); ?></label>
                    <input type="text" name="title" class="form-control" id="aec-venue-name" required value="<?php if( isset( $title ) ) echo esc_attr( $title ); ?>"/>
                </div>
                
                <div class="form-group">
                    <label class="control-label"><?php _e( 'Description', 'another-events-calendar' ); ?></label>
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
        
        <div class="panel panel-default form-horizontal">
        	<div class="panel-body"> 
                <div class="form-group">
                    <label for="aec-venue-address" class="col-sm-3 control-label"><?php _e( 'Address', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="address" id="aec-venue-address" class="aec-map-field form-control" value="<?php if( isset( $address ) ) echo $address; ?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="aec-venue-city" class="col-sm-3 control-label"><?php _e( 'City', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="city" id="aec-venue-city" class="aec-map-field form-control" value="<?php if( isset( $city ) ) echo $city; ?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="aec-venue-state" class="col-sm-3 control-label"><?php _e( 'State', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="state" id="aec-venue-state" class="aec-map-field form-control" value="<?php if( isset( $state ) ) echo $state; ?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="aec-venue-country" class="col-sm-3 control-label"><?php _e( 'Country', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <select name="country" id="aec-venue-country" class="aec-map-field form-control">
                            <option value="">-- <?php _e( 'Select Country', 'another-events-calendar' ); ?> --</option>
                            <?php
                                $countries = aec_get_countries();
                                $country   = isset( $country ) ? $country : '';
                                foreach( $countries as $key => $label ) {
                                    printf( '<option value="%s"%s>%s</option>', $key, selected( $country, $key, false ), $label );
                                }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="aec-venue-pincode" class="col-sm-3 control-label"><?php _e( 'Postal Code', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="pincode" id="aec-venue-pincode" class="aec-map-field form-control" value="<?php if( isset( $pincode ) ) echo $pincode; ?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="aec-venue-phone" class="col-sm-3 control-label"><?php _e( 'Phone', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="phone" id="aec-venue-phone" class="form-control" value="<?php if( isset( $phone ) )  echo $phone; ?>" />
                    </div>
                    
                </div>
                <div class="form-group">
                    <label for="aec-venue-website" class="col-sm-3 control-label"><?php _e( 'Website', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="website" id="aec-venue-website" class="form-control" value="<?php if( isset( $website ) )  echo $website; ?>"/>
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
                            <input type="hidden" name="latitude" id="aec-latitude" value="<?php if( isset( $latitude ) ) echo $latitude; ?>" />
                            <input type="hidden" name="longitude" id="aec-longitude" value="<?php if( isset( $longitude ) )  echo $longitude; ?>" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="aec-hide-map" class="col-sm-3 control-label"></label>
                        <div class="col-sm-9">
                            <?php $hide_map = isset( $hide_map ) ? $hide_map : 0; ?>
                            <input type="checkbox" name="hide_map" id="aec-hide-map" value="1" <?php checked( 1, $hide_map ); ?>/><?php _e( 'Show / Hide Map', 'another-events-calendar' ); ?>
                        </div>
                    </div>
                <?php endif; ?>
        	</div>
        </div>
    
    	<?php wp_nonce_field( 'aec_public_save_venue', 'aec_public_venue_nonce' ); ?>
    	<input type="hidden" name="post_id" value="<?php echo $venue_id; ?>" />
        
        <div class="form-group">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-9">
                <button type="submit" class="btn btn-primary">
                	<?php ( $venue_id > 0 ) ? _e( 'Save Changes', 'another-events-calendar' ) : _e( 'Submit Venue', 'another-events-calendar' ); ?>
                </button>
            </div>
        </div>

	</form>
</div>