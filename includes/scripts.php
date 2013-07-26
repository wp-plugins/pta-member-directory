<?php

function pta_member_directory_load_scripts () {
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'update-order', plugin_dir_url(__FILE__).'/js/update-order.js' );
	wp_enqueue_style( 'pta-categories', plugin_dir_url(__FILE__).'/css/admin.css' );
}