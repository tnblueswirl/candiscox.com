<?php

add_filter('thegem_icon_userpack_enabled', function() {
	return true;
});

function thegem_child_scripts() {
	wp_register_style('icons-userpack', get_stylesheet_directory_uri() . '/css/icons-userpack.css');
}
add_action( 'wp_enqueue_scripts', 'thegem_child_scripts' );

function thegem_child_admin_scripts() {
	wp_register_style('icons-userpack', get_stylesheet_directory_uri() . '/css/icons-userpack.css');
}
add_action( 'admin_enqueue_scripts', 'thegem_child_admin_scripts' );

function thegem_userpack_icons_info_link($link, $pack) {
	if($pack == 'userpack') {
		return esc_url(get_stylesheet_directory_uri().'/fonts/icons-list-userpack.html');
	}
	return $link;
}

function thegem_child_init() {
	add_filter('thegem_user_icons_info_link', 'thegem_userpack_icons_info_link', 10, 2);
}
add_action( 'after_setup_theme', 'thegem_child_init' );