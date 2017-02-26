<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$theme_style = get_option('ecwmv_theme_style');
if(!empty($theme_style)){
	wp_enqueue_style("$theme_style-theme-style");
} else {
	wp_enqueue_style("blue-theme-style");
}

wp_enqueue_style('font-awesome-style');
while (have_posts()) : the_post();

	$event_id 	= get_the_ID();
	$start_date = get_post_meta($event_id,'ecwmv_event_start_date',true);
	$end_date 	= get_post_meta($event_id,'ecwmv_event_end_date',true);
	$start_time	= get_post_meta($event_id,'ecwmv_event_start_time',true);
	$end_time 	= get_post_meta($event_id,'ecwmv_event_end_time',true);
	$location 	= get_post_meta($event_id,'ecwmv_event_location',true);
	$event_cost	= get_post_meta($event_id,'ecwmv_event_cost',true);
	$org_name	= get_post_meta($event_id,'ecwmv_organizer_name',true);
	$org_phone	= get_post_meta($event_id,'ecwmv_organizer_phone',true);
	$org_email	= get_post_meta($event_id,'ecwmv_organizer_email',true);
	$org_website= get_post_meta($event_id,'ecwmv_organizer_website',true);
	$event_lat	= get_post_meta($event_id,'ecwmv_lat',true);
	$event_lng	= get_post_meta($event_id,'ecwmv_lng',true);
	$curr_symbol= get_option('ecwmv_curr_symbol');
	
	if(empty($event_cost)) {$event_cost = 'Free';}
	if(empty($curr_symbol)) {$curr_symbol = '$';} 
	
	
	$term_list = wp_get_post_terms($event_id, 'ecwmv-category');
	if(is_array($term_list) && !empty($term_list)){
		foreach ($term_list as $term){
			$term_name[] = $term->name;	
		}
		$categories = implode(',', $term_name);
	}
	
	$title = get_the_title(); 
	$display_dt	= $start_time.' @ '.$start_date;
	$info_content  = "<div class='info_content' style='height:65px;margin-bottom:10px'>";
	$info_content .= "<div style='float:left;margin-left:10px'>";
	$info_content .= "<a href='".get_permalink()."\'>"."<h5 style='margin:0 0 5px 0'>$title</h5>"."</a>";
	$info_content .= "<p style='margin:0'><i class='fa fa-calendar' style='margin-right:5px'></i>$display_dt</p>";
	$info_content .= "<p style='margin:0'><i class='fa fa-map-marker' style='margin-right:10px'></i>$location</p>";
	$info_content .= "</div>";
	$info_content .= "</div>";
		
	$marker_image = plugins_url( 'img/marker-icon.png', __FILE__ )
	
?>

	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js" ></script>
	<script>
		jQuery(document).ready(function(){ initMap(); })
		function initMap() {
			  var center = new google.maps.LatLng(<?php echo $event_lat; ?>, <?php echo $event_lng;?>);
			  var map = new google.maps.Map(document.getElementById('map-canvas'), {
			    maxZoom: 18,
			    zoom:14,
			    center: center,
			    mapTypeId: google.maps.MapTypeId.ROADMAP
			  });
			  var contentString = "<?php echo $info_content; ?>";
			  var infowindow = new google.maps.InfoWindow({
			    content: contentString
			  });
			  var marker = new google.maps.Marker({
			    position: center,
			    map: map,
			    title: '<?php echo $title; ?>',
			    icon: "<?php echo $marker_image;?>",
			  });
			  infowindow.open(map, marker);
			  marker.addListener('click', function() {
			    infowindow.open(map, marker);
			  });
			}
	</script>
	
	<div class="single-event-page">
		<h3><?php the_title(); ?></h3>
		<div class="single-event-top-box">
			<div class="single-event-summary">
				<p class="date summary-data">
					<i class="fa fa-calendar"></i>
					<label><?php echo esc_html('Start Date & Time:'); ?></label>
					<span><?php echo $start_time.' @ '.date('j M Y',strtotime($start_date));?></span>
				</p>
				<p class="date summary-data">
					<i class="fa fa-calendar"></i>
					<label><?php echo esc_html('End Date & Time:'); ?></label>
					<span><?php echo $end_time.' @ '.date('j M Y',strtotime($end_date));?></span>
				</p>
				<div class="clear"></div>
				<p class="cost summary-data">
					<i class="fa fa-money"></i>
					<label><?php echo esc_html('Price:');?></label>
					<span><?php echo $curr_symbol.' '.$event_cost; ?></span>
				</p>
				<?php if(!empty($categories)) {?>
				<p class="category summary-data">
					<i class="fa fa-money"></i>
					<label><?php echo esc_html('Category:')?></label>
					<span><?php echo $categories; ?></span>
				</p>
				<?php } ?>
				<div class="clear"></div>
				<p class="location summary-data">
					<i class="fa fa-map-marker"></i>
					<label><?php echo esc_html('Location:'); ?></label>
					<span><?php echo $location; ?></span>
				</p>
				
			</div>
			<div class="single-event-thumb">
				<?php echo get_the_post_thumbnail($event_id, array(500,500)); ?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="single-event-bottom-box">
			<div class="single-event-content">
				<?php the_content(); ?>
			</div>
			<div class="single-event-map-org">
				<div class="single-event-organizer">
					<?php if(!empty($org_name)) { ?><h4><?php echo esc_html($org_name); ?></h4><?php } ?>
					<?php if(!empty($org_phone)) { ?>
						<p><i class="fa fa-phone"></i> <?php echo esc_html('Phone:'); ?> <?php echo $org_phone; ?></p>
					<?php } ?>
					<?php if(!empty($org_email)) { ?>
						<p><i class="fa fa-envelope"></i> <?php echo esc_html('Email:'); ?> <?php echo $org_email; ?></p>
					<?php } ?>
					<?php if(!empty($org_website)) { ?>
						<p><i class="fa fa-globe"></i> <?php echo esc_html('Website:'); ?> <?php echo $org_website; ?></p>
					<?php } ?>
				</div>
				<div class="single-event-map">
					<div id="map-canvas"></div>
				</div>
			</div>
		</div>
		
	</div>
	

<?php 	
endwhile;

get_footer();