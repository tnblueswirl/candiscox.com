<?php

/**
 * Markup the manage organizers page.
 */ 
?>

<div class="aec aec-organizers">   
 	
    <!-- Header -->
    <div class="row aec-no-margin">
        <form action="<?php echo aec_manage_organizers_page_link( true ); ?>" class="form-inline" method="get" role="search">
        	<?php if( ! get_option('permalink_structure') ) : ?>
        		<input type="hidden" name="page_id" value="<?php if( $page_settings['manage_organizers'] > 0 ) echo $page_settings['manage_organizers']; ?>">
        	<?php endif; ?>
            
            <div class="pull-left">
                <div class="form-group">
                	<?php $search_query = isset( $_REQUEST['aec'] ) ? esc_attr( $_REQUEST['aec'] ) : ''; ?>
                    <input type="text" name="aec" placeholder="<?php _e( 'Search by name...', 'another-events-calendar' ); ?>" value="<?php echo $search_query; ?>" />
                    <button type="submit" class="btn btn-primary"><?php _e( "Search", 'another-events-calendar' ); ?></button>
                	<a href="<?php echo aec_manage_organizers_page_link(); ?>" class="btn btn-default"><?php _e( "Reset", 'another-events-calendar' ); ?></a>
                </div>
            </div>
            
            <div class="pull-right">
                <div class="input-group">
                    <a href="<?php echo aec_organizer_form_page_link(); ?>" class="btn btn-primary"><?php _e( 'Add New Organizer', 'another-events-calendar' ); ?></a>
                </div>
            </div>
            
            <div class="clearfix"></div>
        </form>

    	<p class="text-muted aec-margin-top"><?php printf( __( '%d Organizer(s) Found', 'another-events-calendar' ), $aec_query->found_posts ); ?></p>
  	</div>
        
    <!-- Body -->
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th><?php _e( 'Name', 'another-events-calendar' ); ?></th>
                <th><?php _e( 'Email', 'another-events-calendar' ); ?></th>
                <th class="hidden-xs"><?php _e( 'Phone', 'another-events-calendar' ); ?></th>
                <th><?php _e( 'Status', 'another-events-calendar' ); ?></th>
                <th><?php _e( 'Actions', 'another-events-calendar' ); ?></th>
            </tr>
        </thead>
        <?php while( $aec_query->have_posts() ) : $aec_query->the_post(); ?>
            <tbody>
                <tr>
                    <td>
                        <h3 class="aec-no-margin"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo get_the_title(); ?></a></h3>
                    </td>
                    <td>
                        <?php
                            $email = get_post_meta( get_the_ID(), 'email', true );
                            if( $email ) echo $email;
                        ?>
                    </td>
                    <td class="hidden-xs">
                        <?php
                            $phone = get_post_meta( get_the_ID(), 'phone', true );
                            if( $phone ) echo $phone;
                        ?>
                    </td>
                    <td><?php echo get_post_status(); ?></td>
                    <td>
                        <a href="<?php echo aec_organizer_form_page_link( get_the_ID() ); ?>"><?php _e( 'edit', 'another-events-calendar' ); ?></a>
                        <span class="aec-divider"> / </span>
                        <a href="<?php echo get_delete_post_link( get_the_ID(), '', true ); ?>"><?php _e( 'delete', 'another-events-calendar' ); ?></a>
                    </td>
                </tr>
            </tbody>                
        <?php endwhile; ?>
    </table>
    
    <!-- Footer -->
    <div class="row aec-no-margin">
        <?php the_aec_pagination( $aec_query->max_num_pages, "", $paged ); ?>
    </div>
 
</div>