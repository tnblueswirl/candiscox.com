<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function get_ecwmv_events( $atts ) {
	$atts = shortcode_atts( array(
			'view' => '',
			'cat_slug' => '',
	), $atts );
	$html = '';
	wp_enqueue_style('ecwmv-style');
	
	$theme_style = get_option('ecwmv_theme_style');
	if(!empty($theme_style)){
		wp_enqueue_style("$theme_style-theme-style");
	} else {
		wp_enqueue_style("blue-theme-style");
	}
	
	
	$view = sanitize_text_field($_REQUEST['view']);
	if(empty($view)){ $view = $atts['view'];} 
	$cat_slug = $atts['cat_slug'];
	
	if(!empty($cat_slug)){
		$term = term_exists($cat_slug, 'ecwmv-category');
		if ($term == 0 && $term == null) {
			$html .= '<p style="color:red;font-size:14px">"'.$cat_slug.'" category dose not exists.</p>';
			$cat_slug = '';
		}
	}
	
	$current_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$current_link = explode("?",$current_link);
	
	
	if(get_option('ecwmv_view_display')){
		$views_array = unserialize(get_option('ecwmv_view_display'));
	}
	
	$html .= '<div class="ecwmv-top-links">';
	if(in_array('cal', $views_array)){$html .= '<a href="'.$current_link[0].'?view=calendar">Calendar</a>';}
	if(in_array('list', $views_array)){$html .= '<a href="'.$current_link[0].'?view=list">List</a>';}
	if(in_array('grid', $views_array)){$html .= '<a href="'.$current_link[0].'?view=grid">Grid</a>';}
	if(in_array('map', $views_array)){$html .= '<a href="'.$current_link[0].'?view=map">Map</a>';}
	$html .= '</div>'; 
	
	$args = array( 
					'post_type' 		=> 'ecwmv-event',  
					'posts_per_page' 	=> -1,
					'post_status'		=> 'publish',
					'ecwmv-category' 	=> $cat_slug,
					'order'             => 'ASC',
					'orderby' => 'meta_value',
					'meta_key' => 'ecwmv_event_start_date',
					'meta_type'=>'DATE'
				);
	
	if($view == 'list'){
		wp_enqueue_style('font-awesome-style');
		$html .= '<div class="ecwmv-events-lists">';
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$args['posts_per_page'] = 9;
		$args['paged'] =  $paged;
		add_filter('posts_where', 'filter_where');
		$event_loop = new WP_Query($args);
		if($event_loop->have_posts()){
			while ($event_loop->have_posts()): $event_loop->the_post();
		
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'medium' );
			$url = $thumb['0'];
			if(empty($url)){
				$url = plugins_url( 'img/no_image.jpg', __FILE__ );
			}
			$title 		= get_the_title();
			$permalink  = get_permalink();
			$location 	= get_post_meta(get_the_ID(),'ecwmv_event_location',true);
			$start_date = get_post_meta(get_the_ID(),'ecwmv_event_start_date',true);
			$start_time	= get_post_meta(get_the_ID(),'ecwmv_event_start_time',true);
			$org_phone	= get_post_meta(get_the_ID(),'ecwmv_organizer_phone',true);
			$org_email	= get_post_meta(get_the_ID(),'ecwmv_organizer_email',true);
			if ( strlen(get_the_excerpt()) > 120 ) { $excerpt = substr(get_the_excerpt(), 0, 120)."..."; } else { $excerpt = get_the_excerpt(); }
				
			$html .= '<div class="single-list">';
			$html .= 	'<div class="single-list-left">';
			$html .= 		'<a href="'.$permalink.'"><img class="single-thumb" src="'.$url.'"></a>';
			$html .= 	'</div>';
			
			$html .= 	'<div class="single-list-right">';
			$html .= 		'<h3><a href="'.$permalink.'">'.$title.'</a></h3>';
			$html .=		'<p>'.$excerpt.'</p>';
			$html .= 		'<p><i class="fa fa-map-marker"></i> '.$location.'</p>';
			$html .= 		'<p><i class="fa fa-calendar"></i> '.$start_time.' @ '.date('j M Y',strtotime($start_date)).'</p>';
			if(!empty($org_phone)){
				$html .= '<p><i class="fa fa-phone"></i> '.$org_phone.'</p>';
			}
			if(!empty($org_email)){
				$html .= '<p><i class="fa fa-envelope"></i> '.$org_email.'</p>';
			}
			$html .= 	'</div>';
			$html .= '</div>';
			$html .= '<div class="clear"></div>';
			
			endwhile;
		}
		$big = 999999999; // need an unlikely integer
		$html .= '<div class="pagination-links">';
		$html .=  paginate_links( array(
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'current' => max( 1, get_query_var('paged') ),
				'total' => $event_loop->max_num_pages
		) );
		$html .= '</div>';
		$html .= '</div>';
		remove_filter('posts_where', 'filter_where');
		
	} elseif ($view == 'grid') {
		wp_enqueue_style('font-awesome-style');
		$i = 1;
		$html .= '<div class="ecwmv-events-grids">';
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$args['posts_per_page'] = 9;
		$args['paged'] =  $paged;
		add_filter('posts_where', 'filter_where');
		$event_loop = new WP_Query($args);
		if($event_loop->have_posts()){
			while ($event_loop->have_posts()): $event_loop->the_post();
		
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'medium' );
			$url = $thumb['0'];
			if(empty($url)){
				$url = plugins_url( 'img/no_image.jpg', __FILE__ );
			}
			$title 		= get_the_title();
			$permalink  = get_permalink(); 
			$location 	= get_post_meta(get_the_ID(),'ecwmv_event_location',true);
			$start_date = get_post_meta(get_the_ID(),'ecwmv_event_start_date',true);
			$start_time	= get_post_meta(get_the_ID(),'ecwmv_event_start_time',true);
			$org_phone	= get_post_meta(get_the_ID(),'ecwmv_organizer_phone',true);
			$org_email	= get_post_meta(get_the_ID(),'ecwmv_organizer_email',true);
			
			$html .= '<div class="single-grid">';
			
			$html .= '<a href="'.$permalink.'"><img class="grid-thumb" src="'.$url.'"></a>';
			$html .= '<h3><a href="'.$permalink.'">'.$title.'</a></h3>';
			$html .= '<p><i class="fa fa-map-marker"></i> '.$location.'</p>';
			$html .= '<p><i class="fa fa-calendar"></i> '.$start_time.' @ '.date('j M Y',strtotime($start_date)).'</p>';
			if(!empty($org_phone)){
				$html .= '<p><i class="fa fa-phone"></i> '.$org_phone.'</p>';
			}
			if(!empty($org_email)){
				$html .= '<p><i class="fa fa-envelope"></i> '.$org_email.'</p>';
			}
			$html .= '</div>';
			$html .= '<div class="row-clear-'.$i.'"></div>';
			$i++;
			if($i > 3){ $i = 1;}
			endwhile;
		}
		
		$html .= '</div>';
		$html .= '<div class="clear"></div>';
		$big = 999999999; // need an unlikely integer
		$html .= '<div class="pagination-links">';
		$html .=  paginate_links( array(
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'current' => max( 1, get_query_var('paged') ),
				'total' => $event_loop->max_num_pages
		) );
		$html .= '</div>';
		remove_filter('posts_where', 'filter_where');
		
	} elseif ($view == 'map') {
		wp_enqueue_style('font-awesome-style');
		$map_zoom = get_option('ecwmv_map_zoom');
		if(empty($map_zoom)){$map_zoom = '2';}
		$map_center_lat = get_option('ecwmv_map_center_lat');
		if(empty($map_center_lat)){$map_center_lat = '27.0000';}
		$map_center_lng = get_option('ecwmv_map_center_lng');
		if(empty($map_center_lng)){$map_center_lng = '17.0000';}
		
		$evnts_string = '';
		$markers = '';
		$info_content = '';
		$event_loop = new WP_Query($args);
		if($event_loop->have_posts()){
			while ($event_loop->have_posts()): $event_loop->the_post();
				
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'thumbnail' );
			$url = $thumb['0'];
				
			if ( strlen(get_the_title()) > 45 ) { $title = substr(get_the_title(), 0, 45)."..."; } else { $title = get_the_title(); }
			$permalink = get_permalink();
				
			$desc = '';
			$desc .= "<div class='event-cal-thumb'><img src='$url'></div>";
			$desc .= "<div class='event-cal-small-desc'><p><b>$title</b></p>$excerpt</div>";
		
			$title = addslashes($title);
			$location 	= get_post_meta(get_the_ID(),'ecwmv_event_location',true);
			$lat 		= get_post_meta(get_the_ID(),'ecwmv_lat',true);
			$lng		= get_post_meta(get_the_ID(),'ecwmv_lng',true);
			$start_date = get_post_meta(get_the_ID(),'ecwmv_event_start_date',true);
			$start_time	= get_post_meta(get_the_ID(),'ecwmv_event_start_time',true);
			$display_dt	= $start_time.' @ '.$start_date;
			
			$markers .=  "['$title','$lat','$lng'],";
			
			$info_content  = "<div class='info_content' style='height:65px;margin-bottom:10px'>";
			$info_content .= "<div style='float:left;margin-left:10px'>";
			$info_content .= "<a href='".get_permalink()."\'>"."<h5 style='margin:0 0 5px 0'>$title</h5>"."</a>";
			$info_content .= "<p style='margin:0'><i class='fa fa-calendar' style='margin-right:5px'></i>$display_dt</p>";
			$info_content .= "<p style='margin:0'><i class='fa fa-map-marker' style='margin-right:10px'></i>$location</p>";
			$info_content .= "</div>";
			$info_content .= "</div>";
			
			$window_content .=  "[\"$info_content\"],";
			endwhile;
		}
		
		$marker_image = plugins_url( 'img/marker-icon.png', __FILE__ );
		$html .= '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js" ></script>';
		$html .= '
				<script>
					jQuery(document).ready(function(){ initial(); })
					function initial(){
						var center = new google.maps.LatLng('.$map_center_lat.','.$map_center_lng.');
					    var map = new google.maps.Map(document.getElementById("map_canvas"), {
					      maxZoom: 18,
					      zoom:'.$map_zoom.',
					      center: center,
					      mapTypeId: google.maps.MapTypeId.ROADMAP
					    });
				
					    var markers = ['.$markers.'];
						// Info Window Content
						var infoWindowContent = ['.$window_content.'];
					                   
					    // Display multiple markers on a map
					    var infoWindow = new google.maps.InfoWindow(), marker, i;
					
						var markersArray = [];
					    for( i = 0; i < markers.length; i++ ) {
					        
					        var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
					        
					        marker = new google.maps.Marker({
					            position: position,
					            map: map,
					            icon: "'.$marker_image.'",
					            title: markers[i][0],
					         
					        });
					        marker.infowindow = infoWindowContent[i][0] ;
					        google.maps.event.addListener(marker, "click", (function(marker, i) {
					            return function() {
					                infoWindow.setContent(infoWindowContent[i][0]);
					                infoWindow.open(map, marker);
					            }
					        })(marker, i));
					        markersArray.push(marker);
					    } 
					 }
				</script>
				';
		$html .= '<div style="width:100%;min-height:500px" id="map_canvas" class="mapping"></div>';
	} else {
	
		$evnts_string = '';
		$event_loop = new WP_Query($args);
		if($event_loop->have_posts()){
			while ($event_loop->have_posts()): $event_loop->the_post();
			
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'thumbnail' );
			$url = $thumb['0'];
			
			if ( strlen(get_the_title()) > 24 ) { $title = substr(get_the_title(), 0, 24)."..."; } else { $title = get_the_title(); }
			if ( strlen(get_the_excerpt()) > 75 ) { $excerpt = substr(get_the_excerpt(), 0, 75)."..."; } else { $excerpt = get_the_excerpt(); }
			$permalink = get_permalink();
			
			$start_date = get_post_meta(get_the_ID(),'ecwmv_event_start_date',true);
			$start_time	= get_post_meta(get_the_ID(),'ecwmv_event_start_time',true);
			$end_date 	= get_post_meta($event_id,'ecwmv_event_end_date',true);
			
			
			$display_dt	=  $start_time;
			$date_time_html = "<p style='margin:0'><i class='fa fa-clock-o' style='margin-right:5px'></i>$display_dt</p>";
			
			$desc = '';
			$desc .= "<div class='event-cal-thumb'><img src='$url'></div>";
			$desc .= "<div class='event-cal-small-desc'><p><b>$title</b></p>$date_time_html $excerpt</div>";
				
			$title = addslashes($title);
			$evnts_string .= 	'{ 
									title:"'.$title.'",
									start:"'.$start_date.'",
									end:"'.$end_date.'",
									desc:"'.$desc.'",
									url:"'.get_permalink().'",	
								},'; 
			endwhile;
		}
		$current_date = date("Y-m-d");
		
		$html .= '<script>';
		$html .= "jQuery(document).ready(function($) {
					$('#calendar').fullCalendar({
						header: {
							left: 'prev',
							center: 'title,month,basicWeek,basicDay',
							right: 'next'
				
						},
						defaultDate: '$current_date',
						editable: false,
						eventLimit: 3, 
	    				eventLimitText: 'More',
						events: [
								  ".$evnts_string."
								]
					});
					
				});";
		$html .= '</script>';
		$html .= '<div id="calendar"></div>';
	}
	
	return $html;
}
add_shortcode( 'ecwmv_events', 'get_ecwmv_events' );

function mytheme_enqueue_scripts() {
	
	wp_enqueue_script( 'moment-min-script' );
	wp_enqueue_script( 'fullcalendar-script' );
	wp_enqueue_style( 'fullcalendar-style' );

}
add_action( 'wp_head', 'mytheme_enqueue_scripts' , 1);
