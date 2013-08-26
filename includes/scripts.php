<?php

function pta_member_directory_load_scripts () {
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'update-order', plugin_dir_url(__FILE__).'/js/update-order.js' );
	wp_enqueue_style( 'pta-categories', plugin_dir_url(__FILE__).'/css/admin.css' );
}

function pta_member_directory_options_load_scripts () {
	if( wp_style_is( 'wp-color-picker', 'registered' ) ) {
		wp_enqueue_style( 'wp-color-picker' );
    	wp_enqueue_script( 'pta-color-picker', plugin_dir_url(__FILE__).'/js/pta-color-picker.js', array( 'wp-color-picker' ), false, true );
	}
	
}