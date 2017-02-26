<?php

/**
 * Markup the table layout of the events archive page.
 */ 
?>

<div class="aec aec-events aec-table-layout">
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
                		<select name="view" onchange="this.form.submit()" class="form-control">
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
    <?php endif; ?>
    
    <!-- Loop -->
    <?php while( $aec_query->have_posts() ) : $aec_query->the_post(); ?>
        <div class="row aec-table-layout-row">
           	<div class="col-md-2">
            	<p>
                	<span class="glyphicon glyphicon-calendar"></span>
					<?php echo aec_get_event_date( get_the_ID() ); ?>
                 </p>
           	</div>
                
        	<div class="col-md-2">
            	<?php if( has_post_thumbnail() ) : ?>
               		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'full' ); ?></a>
                <?php else : ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                    	<img src="<?php echo AEC_PLUGIN_URL; ?>public/images/placeholder-event.jpg" class="img-responsive" />
                    </a>
              	<?php endif; ?>
            </div>
                
            <div class="col-md-6">                	
				<h3 class="aec-no-margin"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo get_the_title(); ?></a></h3>
                
                <?php $parent_id = get_post_meta( get_the_ID(), 'parent', true ); ?>
           		<?php if( $has_recurring_link && $parent_id > 0 ) : ?>  
                	<small>
						<?php _e( 'Recurring Event', 'another-events-calendar' ); ?> 
                        <a href="<?php echo aec_recurring_events_page_link( $parent_id ); ?> ">(<?php _e( 'see all', 'another-events-calendar' ); ?>)</a>
                    </small>
           		<?php endif; ?>
                        
                <?php $venue_id = get_post_meta( get_the_ID(), 'venue_id', true ); ?>
                <?php if( $venue_id > 0 && is_string( get_post_status( $venue_id ) ) ) : ?>
					<p class="aec-margin-top aec-no-margin-bottom text-muted">
						<span class="glyphicon glyphicon-map-marker"></span>
						<a href="<?php echo aec_venue_page_link( $venue_id ); ?>"><?php echo get_the_title( $venue_id ); ?></a>
					</p>
				<?php endif; ?>
                
                <p class="aec-margin-top"><?php echo wp_kses_post( wp_trim_words( get_the_content(), 20 ) ); ?></p>
        	</div>
  
            <div class="col-md-2 text-right">
            	<?php 
					$cost = get_post_meta( get_the_ID(), 'cost', true );
					if( $cost > 0 ) printf( '<p class="lead">%s</p>', aec_currency_filter( aec_format_amount( $cost ) ) );
				?>
                
              	<p><a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm"><?php _e( 'Read more', 'another-events-calendar' ); ?></a></p>
            </div>
        </div>
    <?php endwhile; ?>
   
   	<?php if( $has_pagination ) : ?>
    	<!-- Footer -->
    	<div class="row aec-no-margin">
    		<?php the_aec_pagination( $aec_query->max_num_pages, "", $paged ); ?>
    	</div>
    <?php endif; ?>
</div>

<?php wp_reset_postdata(); ?>
<?php the_aec_socialshare_buttons(); ?>