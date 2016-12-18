<?php

/**
 * Markup the manage events page.
 */ 
?>

<div class="aec aec-events">

	<!-- Header -->
	<div class="row aec-no-margin">
        <form action="<?php echo aec_manage_events_page_link( true ); ?>" class="form-inline" method="get" role="search">
        	<?php if( ! get_option('permalink_structure') ) : ?>
        		<input type="hidden" name="page_id" value="<?php if( $page_settings['manage_events'] > 0 ) echo $page_settings['manage_events']; ?>">
        	<?php endif; ?>
                
            <div class="pull-left">
                <div class="form-group">
                	<?php $search_query = isset( $_REQUEST['aec'] ) ? esc_attr( $_REQUEST['aec'] ) : ''; ?>
                    <input type="text" name="aec" placeholder="<?php _e( 'Search by title, desc...', 'another-events-calendar' ); ?>" value="<?php echo $search_query; ?>" />
                    <button type="submit" class="btn btn-primary"><?php _e( "Search", 'another-events-calendar' ); ?></button>
                	<a href="<?php echo aec_manage_events_page_link(); ?>" class="btn btn-default"><?php _e( "Reset", 'another-events-calendar' ); ?></a>
              	</div>
            </div>
                
            <div class="pull-right">
                <div class="input-group">
                    <a href="<?php echo aec_event_form_page_link(); ?>" class="btn btn-primary"><?php _e( 'Add New Event', 'another-events-calendar' ); ?></a>
                </div>
            </div>
            
            <div class="clearfix"></div>
        </form>
        
        <p class="text-muted aec-margin-top"><?php printf( __( '%d Event(s) Found', 'another-events-calendar' ), $aec_query->found_posts ); ?></p>
	</div>

	<!-- Body -->
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th><?php _e( 'Title', 'another-events-calendar' ); ?></th>
                <th class="hidden-xs" style="width:150px"><?php _e( 'Start date', 'another-events-calendar' ); ?></th>
                <th class="hidden-xs" style="width:150px;"><?php _e( 'End date', 'another-events-calendar' ); ?></th>
                <th><?php _e( 'Status', 'another-events-calendar' ); ?></th>
                <th><?php _e( 'Actions', 'another-events-calendar' ); ?></th>
            </tr>
        </thead>
        <?php while( $aec_query->have_posts() ) : $aec_query->the_post(); ?>
            <tbody>
                <tr>
                    <td>
                        <h3 class="aec-no-margin"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo get_the_title(); ?></a></h3>
                        <?php 
                            $meta = array();
                            
                            // Categories
                            $categories = get_the_terms( get_the_ID(), 'aec_categories' );
                            $meta_categories = array(); 
                            if( $categories ) {
                                foreach( $categories as $category ) {
                                    $meta_categories[] = sprintf( '<a class="text-primary" href="%s">%s</a>', aec_category_page_link( $category ), $category->name );
                                }
                                if( count( $meta_categories ) ) $meta[] = '<span class="glyphicon glyphicon-folder-open"></span> '.implode( ', ', $meta_categories );
                            }
                            
                            // Venue
                            $venue_id = get_post_meta( get_the_ID(), 'venue_id', true );
                            if( $venue_id > 0 && is_string( get_post_status( $venue_id ) ) ) {
                                $meta[] =  sprintf( '<span class="glyphicon glyphicon-map-marker"></span> <a href="%s">%s</a>', aec_venue_page_link( $venue_id ), get_the_title( $venue_id ) );
                            }
                            
                            // ...
                            if( count( $meta ) ) echo '<small class="aec-margin-top">'.implode( ' / ', $meta ).'</small>';                                   
                        ?>
                    </td>
                    <td class="hidden-xs">
                        <?php 
                            $start_date_time = get_post_meta( get_the_ID(), 'start_date_time', true );  
                            echo date_i18n( get_option('date_format').' '.get_option('time_format'), strtotime( $start_date_time ) );
                        ?>
                    </td>
                    <td class="hidden-xs">
                        <?php 
                            $end_date_time = get_post_meta( get_the_ID(), 'end_date_time', true );  
                            if( ! empty( $end_date_time ) ) echo date_i18n( get_option('date_format').' '.get_option('time_format'), strtotime( $end_date_time ) );
                        ?>
                    </td>
                     <td><?php echo get_post_status(); ?></td>
                     <td>
                        <a href="<?php echo aec_event_form_page_link( get_the_ID() ); ?>"><?php _e( 'edit', 'another-events-calendar' ); ?></a>
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