<?php

/**
 * Markup the organizer form.
 */
?>

<div class="aec aec-organizer-form">
	<form method="post" action="<?php echo aec_organizer_form_page_link(); ?>" class="form-vertical" enctype="multipart/form-data" data-toggle="validator" role="form">
    	<div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label for="aec-organizer-name" class="control-label"><?php _e( 'Organizer Name', 'another-events-calendar' ); ?></label>
                    <input type="text" name="title" class="form-control" id="aec-organizer-name" required value="<?php if( isset( $title ) ) echo esc_attr( $title ); ?>" />
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
                    <label for="aec-upload-image" class="col-sm-3 control-label"><?php _e( 'Upload Photo', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <input type="file" name="image" id="aec-upload-image" />
                        <?php if( isset( $image ) ) : ?>
                        	<div id="aec-img-preview" class="aec-margin-top">
                        		<img src="<?php echo $image; ?>" />
                            	<a href="#" id="aec-img-delete" data-post_id="<?php echo $organizer_id; ?>" data-attachment_id="<?php echo $attachment_id; ?>">
									<?php _e( 'Delete', 'another-events-calendar' ); ?>
                            	</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="aec-phone" class="col-sm-3 control-label"><?php _e( 'Phone', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="phone" id="aec-phone" class="form-control" value="<?php if( isset( $phone ) ) echo esc_attr( $phone ); ?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="aec-website" class="col-sm-3 control-label"><?php _e( 'Website', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="website" id="aec-website" class="form-control"value="<?php if( isset( $website ) ) echo esc_attr( $website ); ?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="aec-website" class="col-sm-3 control-label"><?php _e( 'Email', 'another-events-calendar' ); ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="email" id="aec-email" class="form-control" value="<?php if( isset( $email ) ) echo esc_attr( $email ); ?>" />
                    </div>
                </div>
    		</div>
        </div>
        
    	<?php wp_nonce_field( 'aec_public_save_organizer', 'aec_public_organizer_nonce' ); ?>
    	<input type="hidden" name="post_id" value="<?php echo $organizer_id; ?>" />
        
    	<div class="form-group">
    		<label class="col-sm-3 control-label"></label>
       		<div class="col-sm-9">            	
        		<button type="submit" class="btn btn-primary">
					<?php ( $organizer_id > 0 ) ? _e( 'Save Changes', 'another-events-calendar' ) : _e( 'Submit Organizer', 'another-events-calendar' ); ?>
                </button>
      		</div>
    	</div>
	</form>
</div>