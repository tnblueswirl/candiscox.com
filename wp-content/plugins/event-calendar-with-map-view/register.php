<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$labels = array(
		'name'               => _x( 'Events', 'post type general name' ),
		'singular_name'      => _x( 'Event', 'post type singular name' ),
		'menu_name'          => _x( 'My Events', 'admin menu' ),
		'name_admin_bar'     => _x( 'Event', 'add new on admin bar' ),
		'add_new'            => _x( 'Add New', 'event' ),
		'add_new_item'       => __( 'Add New Event' ),
		'new_item'           => __( 'New Event' ),
		'edit_item'          => __( 'Edit Event' ),
		'view_item'          => __( 'View Event' ),
		'all_items'          => __( 'All Events' ),
		'search_items'       => __( 'Search Events' ),
		'parent_item_colon'  => __( 'Parent Events:' ),
		'not_found'          => __( 'No Events found.' ),
		'not_found_in_trash' => __( 'No Events found in Trash.' )
);

$args = array(
		'labels'             => $labels,
		'description'        => __( 'Description.' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'ecwmv-event' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'menu_icon'			 => 'dashicons-calendar-alt',
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt','custom-fields' )
);

register_post_type( 'ecwmv-event', $args );

$labels = array(
		'name'                       => _x( 'Events Categories', 'Taxonomy General Name'),
		'singular_name'              => _x( 'Events Category', 'Taxonomy Singular Name'),
		'menu_name'                  => __( 'Event Category'),
		'all_items'                  => __( 'All Items'),
		'parent_item'                => __( 'Parent Item'),
		'parent_item_colon'          => __( 'Parent Item:'),
		'new_item_name'              => __( 'New Item Name'),
		'add_new_item'               => __( 'Add New Item'),
		'edit_item'                  => __( 'Edit Item'),
		'update_item'                => __( 'Update Item'),
		'view_item'                  => __( 'View Item'),
		'separate_items_with_commas' => __( 'Separate items with commas'),
		'add_or_remove_items'        => __( 'Add or remove items'),
		'choose_from_most_used'      => __( 'Choose from the most used'),
		'popular_items'              => __( 'Popular Items'),
		'search_items'               => __( 'Search Items'),
		'not_found'                  => __( 'Not Found'),
		'items_list'                 => __( 'Items list'),
		'items_list_navigation'      => __( 'Items list navigation'),
);
$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
);
register_taxonomy( 'ecwmv-category', array( 'ecwmv-event' ), $args );

wp_register_script( 'jquery-timepicker-script', plugins_url( '/js/jquery.timepicker.js', __FILE__ ) );
wp_register_script( 'bootstrap-datepicker-script', plugins_url( '/js/bootstrap-datepicker.js', __FILE__ ) );
wp_register_script( 'datepair-script', plugins_url( '/js/datepair.js', __FILE__ ) );
wp_register_script( 'jquery-datepair-script', plugins_url( '/js/jquery.datepair.js', __FILE__ ) );
wp_register_script( 'fullcalendar-script', plugins_url( '/js/fullcalendar.js', __FILE__ ) );
wp_register_script( 'moment-min-script', plugins_url( '/js/moment.min.js', __FILE__ ) );

wp_register_style( 'jquery-timepicker-style', plugins_url('/css/jquery.timepicker.css', __FILE__) );
wp_register_style( 'bootstrap-datepicker-standalone-style', plugins_url('/css/bootstrap-datepicker.standalone.css', __FILE__) );
wp_register_style( 'ecwmv-style', plugins_url('/css/ecwmv.css', __FILE__) );
wp_register_style( 'fullcalendar-style', plugins_url('/css/fullcalendar.css', __FILE__) );
wp_register_style( 'font-awesome-style', 'http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css' );


//wp_register_style( 'fullcalendar-print-style', plugins_url('/css/fullcalendar.print.css', __FILE__) );


wp_register_style( 'blue-theme-style', plugins_url("/css/theme-style/blue.css", __FILE__) );
wp_register_style( 'red-theme-style', plugins_url("/css/theme-style/red.css", __FILE__) );
wp_register_style( 'green-theme-style', plugins_url("/css/theme-style/green.css", __FILE__) );
wp_register_style( 'pink-theme-style', plugins_url("/css/theme-style/pink.css", __FILE__) );
wp_register_style( 'grey-theme-style', plugins_url("/css/theme-style/grey.css", __FILE__) );	


function ecwmv_custom_post_type_template($single_template) {
	global $post;
	if ($post->post_type == 'ecwmv-event') {
		$single_template = plugin_dir_path(__FILE__).DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'single-ecwmv-event.php';
	}
	return $single_template;
}
add_filter( 'single_template', 'ecwmv_custom_post_type_template' );

if(!get_option('ecwmv_default_settings')) {
	update_option('ecwmv_theme_style', 'blue');
	update_option('ecwmv_curr_symbol', '$');
	update_option('ecwmv_view_display', serialize(array('cal','list','grid','map')));
	update_option('ecwmv_map_zoom', '2');
	update_option('ecwmv_map_center_lat', '27.0000');
	update_option('ecwmv_map_center_lng', '17.0000');
	update_option('ecwmv_default_settings','1');
}