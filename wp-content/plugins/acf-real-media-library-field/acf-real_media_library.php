<?php
/**
Plugin Name: Advanced Custom Fields: Real Media Library Field
Plugin URI: http://matthias-web.de
Description: Add a new ACF field type that allows the user to select an Real Media Library folder
Author: Matthias Günter
Version: 1.1.3
Author URI: http://matthias-web.de
Licence: GPLv2
*/

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_plugin_real_media_library') ) :

class acf_plugin_real_media_library {
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	17/02/2016
	*  @since	1.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// vars
		$this->settings = array(
			'version'	=> '1.1.2',
			'url'		=> plugin_dir_url( __FILE__ ),
			'path'		=> plugin_dir_path( __FILE__ )
		);
		
		
		// set text domain
		// https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
		//load_plugin_textdomain( 'acf-real_media_library', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' ); 
		
		
		// include field
		add_action('acf/include_field_types', 	array($this, 'include_field_types')); // v5
		add_action('acf/register_fields', 		array($this, 'include_field_types')); // v4
		
	}
	
	
	/*
	*  include_field_types
	*
	*  This function will include the field type class
	*
	*  @type	function
	*  @date	17/02/2016
	*  @since	1.0.0
	*
	*  @param	$version (int) major ACF version. Defaults to false
	*  @return	n/a
	*/
	
	function include_field_types( $version = false ) {
		
		// support empty $version
		if( !$version ) $version = 4;
		
		// include
		if ($version === 5 || $version === 4)
			include_once('fields/acf-real_media_library-v' . $version . '.php');
	}
	
}


// initialize
new acf_plugin_real_media_library();


// class_exists check
endif;
	
?>