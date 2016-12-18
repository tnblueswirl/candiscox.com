<?php

/**
 *  Markup the blog layout of the events archive page.
 */ 
?>

<div class="aec aec-events aec-blog-layout">
	<?php if( $has_header ) : ?>
		<!-- Header -->
    	<div class="row aec-no-margin">
    		<div class="pull-left text-muted">
        		<?php printf( __( ' %d Event(s) Found', 'another-events-calendar' ), $aec_query->found_posts ); ?>
        	</div>
        
        	<?php if( count( $view_options ) > 1 ) : ?>
        		<div class="pull-right">
        			<form action="" method="GET">
               			<?php 
							foreach( $_GET as $key => $content ) {
								if( 'view' != $key ) {
									printf( '<input type="hidden" name="%s" value="%s" />', $key, $content );
								}
							}
						?>  
                		<select name="view" onchange="this.form.submit()">
                			<?php
                    			foreach( $view_options as $view_option ) {
                    				printf( '<option value="%s"%s>%s</option>', $view_option, selected( $view_option, $view ), $view_option );
                    			}
                 			?>
                 		</select>
    				</form>
        		</div>
        	<?php endif; ?>
        
        	<div class="clearfix"></div>
    	</div>
    
    	<div class="aec-spacer"></div>
    <?php endif; ?>
    
    <!-- Loop -->
    <div class="row aec-no-margin">
    	<?php while( $aec_query->have_posts() ) : $aec_query->the_post(); ?>
        	<h2 class="aec-no-margin">
        		<a href="<?php the_permalink(); ?>" class="pull-left" title="<?php the_title_attribute(); ?>"><?php echo get_the_title(); ?></a>
                <?php 
					$cost = get_post_meta( get_the_ID(), 'cost', true );
					if( $cost > 0 ) printf( '<div class="lead aec-no-margin pull-right">%s</div>', aec_currency_filter( aec_format_amount( $cost ) ) );
				?>
                <div class="clearfix"></div>
            </h2>
            
            <?php $parent_id = get_post_meta( get_the_ID(), 'parent', true ); ?>
            <?php if( $has_recurring_link && $parent_id > 0 ) : ?>  
               	<small>
					<?php _e( 'Recurring Event', 'another-events-calendar' ); ?> 
                    <a href="<?php echo aec_recurring_events_page_link( $parent_id ); ?> ">(<?php _e( 'see all', 'another-events-calendar' ); ?>)</a>
                </small>
           	<?php endif; ?>
            
            <div class="thumbnail aec-margin-top aec-no-padding aec-no-border">
            	<?php if( has_post_thumbnail() ) : ?>
               		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'full' ); ?></a>
                <?php endif; ?>
                  
                <div class="caption">
                    <!-- Event Date -->
                    <p class="aec-margin-top aec-no-margin-bottom">
                       	<span class="glyphicon glyphicon-calendar"></span>
               			<?php echo aec_get_event_date( get_the_ID() ); ?>
                    </p>
                        
                    <!-- Event Venue -->
					<?php $venue_id = get_post_meta( get_the_ID(), 'venue_id', true ); ?>
                <?php if( $venue_id > 0 && is_string( get_post_status( $venue_id ) ) ) : ?>
						<p class="aec-margin-top aec-no-margin-bottom text-muted">
							<span class="glyphicon glyphicon-map-marker"></span>
							<a href="<?php echo aec_venue_page_link( $venue_id ); ?>"><?php echo get_the_title( $venue_id ); ?></a>
						</p>
					<?php endif; ?>
                        
                    <!-- Description -->
                    <p class="aec-margin-top"><?php echo wp_kses_post( wp_trim_words( get_the_content(), 20 ) ); ?></p>

					<!-- More -->
                    <p><a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm"><?php _e( 'Read more', 'another-events-calendar' ); ?></a></p>
                </div>
       		</div>
            
            <hr />
    	<?php endwhile; ?>
    </div>
   
   	<?php if( $has_pagination ) : ?>
    	<!-- Footer -->
     	<div class="row aec-no-margin">
    		<?php the_aec_pagination( $aec_query->max_num_pages, "", $paged ); ?>  
    	</div>
    <?php endif; ?>
</div>

<?php wp_reset_postdata(); ?>
<?php the_aec_socialshare_buttons(); ?>