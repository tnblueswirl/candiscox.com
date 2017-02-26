<?php

function filter_where($where = '') {
	global $wpdb;
	$where .= "AND $wpdb->postmeta.meta_key = 'ecwmv_event_start_date' AND $wpdb->postmeta.meta_value > '" .date('Y-m-d') . "'";
	return $where;
}


class ecwmv_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
				'ecwmv_widget',
				__('ECWMV Events List'),
				array( 'description' => __( 'Events List as per category.' ), )
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$event_no 	=  $instance['events-to-show'];
		$event_cat 	=  $instance['events-category'];
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		wp_enqueue_style('ecwmv-style');
		
		$theme_style = get_option('ecwmv_theme_style');
		if(!empty($theme_style)){
			wp_enqueue_style("$theme_style-theme-style");
		} else {
			wp_enqueue_style("blue-theme-style");
		}
		wp_enqueue_style('font-awesome-style');
		

		add_filter('posts_where', 'filter_where');
		// This is where you run the code and display the output
		$args = array(
				'post_type' 		=> 'ecwmv-event',
				'posts_per_page' 	=> $event_no,
				'post_status'		=> 'publish',
				'ecwmv-category' 	=> $event_cat,
				'order'             => 'ASC',
				'orderby' 			=> 'meta_value',
				'meta_key' 			=> 'ecwmv_event_start_date',
				'meta_type'			=>'DATE'
		);
		$event_loop = new WP_Query($args);

		if($event_loop->have_posts()){
			while ($event_loop->have_posts()): $event_loop->the_post();
			
			$title 		= get_the_title();
			$permalink  = get_permalink();
			$start_date = get_post_meta(get_the_ID(),'ecwmv_event_start_date',true);
			$start_time	= get_post_meta(get_the_ID(),'ecwmv_event_start_time',true);
			if ( strlen(get_the_excerpt()) > 65 ) { $excerpt = substr(get_the_excerpt(), 0, 65)."..."; } else { $excerpt = get_the_excerpt(); }
		
			$html .= '<div class="widget-list">';
			$html .= 	'<h3><a href="'.$permalink.'">'.$title.'</a></h3>';
			$html .= 	'<p><i class="fa fa-calendar"></i> '.$start_time.' @ '.date('j M Y',strtotime($start_date)).'</p>';
			$html .=	'<p>'.$excerpt.'</p>';
			$html .= '</div>';
			$html .= '<div class="clear"></div>';
				
			endwhile;
		}
		remove_filter('posts_where', 'filter_where');
		echo $html;
		echo $args['after_widget'];
	}

	// Widget Backend
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title 		= $instance[ 'title' ];
			$event_num 	= $instance['events-to-show'];
			$event_cat 	= $instance['events-category'];
		}
		else {
			$title = __( 'Events List' );
			$event_num = '5';
			$event_cat = '';
		}
		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label><br> 
			<input class="ecwmv-wid" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'events-to-show' ); ?>"><?php _e( 'Events to Show:' ); ?></label><br> 
			<input class="ecwmv-wid" id="<?php echo $this->get_field_id( 'events-to-show' ); ?>" name="<?php echo $this->get_field_name( 'events-to-show' ); ?>" type="number" value="<?php echo esc_attr( $event_num ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'events-category' ); ?>"><?php _e( 'Events Category:' ); ?></label> <br>
			
			<?php 
				$terms = get_terms('ecwmv-category', $args);
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
					echo '<select class="ecwmv-wid" id="'.$this->get_field_id( 'events-category' ).'" name="'.$this->get_field_name( 'events-category' ).'">';
					echo '<option value="">All</option>';
					foreach ( $terms as $term ) {
						if($event_cat == $term->slug){
							echo '<option selected value="'.$term->slug.'">' . $term->name . '</li>';
						} else {
							echo '<option value="'.$term->slug.'">' . $term->name . '</li>';
						}
					}
					echo '</select>';
				}
			?>			
		</p>
	<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['events-to-show'] = ( ! empty( $new_instance['events-to-show'] ) ) ? strip_tags( $new_instance['events-to-show'] ) : '5';
		$instance['events-category'] = ( ! empty( $new_instance['events-category'] ) ) ? strip_tags( $new_instance['events-category'] ) : '';
		return $instance;
	}
}

// Register and load the widget
function ecwmv_load_widget() {
	register_widget( 'ecwmv_widget' );
}
add_action( 'widgets_init', 'ecwmv_load_widget' );