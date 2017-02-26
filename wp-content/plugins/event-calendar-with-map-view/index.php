<?php
/*
 Plugin Name: Event Calendar With Map View (ECWMV) 
 Plugin URI: https://harshphpdeveloper.wordpress.com/
 Description: Very User Friendly Event Calendar plugin with different style theme , views and events on google map.   
 Author: Harsh Pandya
 Version: 0.6
 Author URI: https://harshphpdeveloper.wordpress.com/
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action('admin_menu', 'ecwmv_events_menu_setup');
add_action( 'init', 'register_ecwmv_posts', 0 );
add_action( 'init', 'add_ecwmv_shortcodes', 0 );


function ecwmv_events_menu_setup(){
	add_submenu_page( 'edit.php?post_type=ecwmv-event', 'Settings', 'Settings', 'manage_options', 'ecwmv-settings', 'ecwmv_settings' );
}

function ecwmv_settings(){
	include_once  plugin_dir_path(__FILE__).DIRECTORY_SEPARATOR.'settings.php';
}

function register_ecwmv_posts(){
	include_once  plugin_dir_path(__FILE__).DIRECTORY_SEPARATOR.'register.php';	
	include_once  plugin_dir_path(__FILE__).DIRECTORY_SEPARATOR.'register_fields.php';
	include_once  plugin_dir_path(__FILE__).DIRECTORY_SEPARATOR.'register_widget.php';
}

function add_ecwmv_shortcodes(){
	include_once  plugin_dir_path(__FILE__).DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'ecwmv_events.php';
}

