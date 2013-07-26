<?php

function pta_directory_save_order () {
	$pta_categories = get_option( 'pta_member_categories' );

	$list = $pta_categories;
	$new_order = $_POST['list_items'];
	$new_list = array();

	// update order
	foreach ($new_order as $v) {
		if(isset($list[$v])) {
			$new_list[$v] = $list[$v];
		}
	}

	// save the new order
	update_option( 'pta_member_categories', $new_list );

	die();
}

add_action('wp_ajax_pta_directory_update_order', 'pta_directory_save_order');

/*EOF*/