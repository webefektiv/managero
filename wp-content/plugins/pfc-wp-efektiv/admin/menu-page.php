<?php

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}


function pfc_wp_add_toplevel_menu() {

	/*
		add_menu_page(
			string   $page_title,
			string   $menu_title,
			string   $capability,
			string   $menu_slug,
			callable $function = '',
			string   $icon_url = '',
			int      $position = null
		)
	*/

	add_menu_page(
		'PFC Settings',
		'PFC WP',
		'manage_options',
		'pfcwp',
		'pfc_wp_display_settings_page',
		'dashicons-admin-generic',
		null
	);

}
add_action( 'admin_menu', 'pfc_wp_add_toplevel_menu' );


function pfc_wp_add_sublevel_menu() {

	/*

	add_submenu_page(
		string   $parent_slug,
		string   $page_title,
		string   $menu_title,
		string   $capability,
		string   $menu_slug,
		callable $function = ''
	);

	*/

	add_submenu_page(
		'options-general.php',
		'PFC WP List',
		'PF WP',
		'manage_options',
		'pfcwplist',
		'pfc_display_list_page'
	);

}
add_action( 'admin_menu', 'pfc_wp_add_sublevel_menu' );