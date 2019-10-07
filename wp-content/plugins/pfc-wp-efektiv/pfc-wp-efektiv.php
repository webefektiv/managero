<?php
/*
Plugin Name: PFC-WP-Efektiv
Plugin URI: http://webefektiv.ro
Description: A brief description of the Plugin.
Version: 1.0
Author: Office 3
Author URI: http://webefektiv.ro
License: MIT
*/


// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}


// if admin area
if ( is_admin() ) {

	// include dependencies
	require_once plugin_dir_path( __FILE__ ) . 'admin/menu-page.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/settings-page.php';

}



// register settings
function wp_pfc_register_settings(){

	register_setting(
		'pfc_wp_options',
		'pfc_wp_options',
		'pfc_wp_callback_validate_options'
	);



	add_settings_section(
		'pfc_wp_section_list',
		'List post Options',
		'pfc_wp_callback_section_list',
		'pfc_wp'
	);

	add_settings_section(
		'myplugin_section_admin',
		'Customize Admin Area',
		'myplugin_callback_section_admin',
		'pfc_wp'
	);


}

add_action('admin_init','wp_pfc_register_settings');


// validate plugin settings
function pfc_wp_callback_validate_options($input){
	return $input;
}



