<?php
/*
Plugin Name: PTA Member Directory
Plugin URI: http://wordpress.org/plugins/pta-member-directory/
Description: Member/Staff directory listing and management system with contact form.  Uses custom post type and taxonomies.  
Creates a default template for viewing direcotry on the public side. 
Simple public view with options to show or hide contact info, or only show contact info for logged in users with admin specified role/capability,
or show a contact form instead of email address.
Drag and Drop defined "position" taxonomies on the custom settings page to change the order they are displayed for the public.
Custom post type of "member".  Default archive template for listing the directory is created with the plugin, so you can display it by simply using
yoursite.com/member
Or, put it on any page with the shortcode.  Separate shortcodes for directory and the contact form.  Contact form can be used stand alone and will
show a drop down list of all members to choose who to send the message to.
Author: Stephen Sherrard
Version: 1.3.9
Author URI: http://stephensherrardplugins.com
Text Domain: pta-member-directory
Domain Path: /languages
*/
// Save version # in database for future upgrades
if (!defined('PTA_MEMBER_DIRECTORY_VERSION_KEY'))
    define('PTA_MEMBER_DIRECTORY_VERSION_KEY', 'pta_member_directory_version');

if (!defined('PTA_MEMBER_DIRECTORY_VERSION_NUM'))
    define('PTA_MEMBER_DIRECTORY_VERSION_NUM', '1.3.9');

add_option(PTA_MEMBER_DIRECTORY_VERSION_KEY, PTA_MEMBER_DIRECTORY_VERSION_NUM);


include(dirname(__FILE__).'/includes/scripts.php');
include(dirname(__FILE__).'/includes/process-ajax.php');
include_once(dirname(__FILE__).'/includes/pta-display-directory.php');

add_action( 'init', 'pta_member_directory_init' );

/**
 * Initialization function.  Sets up the custom Post Type and Custom Taxonomy
 * Post type is registered as "member".  Taxonomy is registerd as "member_category"
 * @return Nothing This function just registers the post type and taxonomy
 */
function pta_member_directory_init() {

	load_plugin_textdomain( 'pta-member-directory', false, dirname(plugin_basename( __FILE__ )) . '/languages/' );

	// Set up labels for all our custom post type fields
    $labels = array(
		'name'                => _x( 'Members', 'Post Type General Name', 'pta-member-directory' ),
		'singular_name'       => _x( 'Member', 'Post Type Singular Name', 'pta-member-directory' ),
		'menu_name'           => __( 'Member Directory', 'pta-member-directory' ),
		'parent_item_colon'   => '',
		'all_items'           => __( 'All Members', 'pta-member-directory' ),
		'view_item'           => __( 'View Member', 'pta-member-directory' ),
		'add_new_item'        => __( 'Add New Member', 'pta-member-directory' ),
		'add_new'             => __( 'New Member', 'pta-member-directory' ),
		'edit_item'           => __( 'Edit Member', 'pta-member-directory' ),
		'update_item'         => __( 'Update Member', 'pta-member-directory' ),
		'search_items'        => __( 'Search members', 'pta-member-directory' ),
		'not_found'           => __( 'No members found', 'pta-member-directory' ),
		'not_found_in_trash'  => __( 'No members found in Trash', 'pta-member-directory' ),
	);
    // Various arguments/options for the member post type
	$args = array(
		'label'               => __( 'member', 'pta-member-directory' ),
		'description'         => __( 'PTA Board Members and Chairs', 'pta-member-directory' ),
		'labels'              => $labels,
		'supports'			  => array( 'title', 'thumbnail', 'excerpt', 'editor' ),
		'taxonomies'          => array( ),
		'hierarchical'        => false, // This simple post type doesn't use parent>child structures
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 30,
		'menu_icon'           => plugins_url( 'pta-member-directory/images/users_16.png' ),
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'member', $args );
	// Labels for the Taxonomy - We're calling them "positions" for the 'member_category' taxonomy
	$labels = array(
		'name'              => _x( 'Positions', 'taxonomy general name', 'pta-member-directory' ),
		'singular_name'     => _x( 'Position', 'taxonomy singular name', 'pta-member-directory' ),
		'search_items'      => __( 'Search Positions', 'pta-member-directory' ),
		'all_items'         => __( 'All Positions', 'pta-member-directory' ),
		'parent_item'       => __( 'Parent Category', 'pta-member-directory' ),
		'parent_item_colon' => __( 'Parent Category:', 'pta-member-directory' ),
		'edit_item'         => __( 'Edit Position', 'pta-member-directory' ), 
		'update_item'       => __( 'Update Position', 'pta-member-directory' ),
		'add_new_item'      => __( 'Add New Position', 'pta-member-directory' ),
		'new_item_name'     => __( 'New Position', 'pta-member-directory' ),
		'menu_name'         => __( 'Positions', 'pta-member-directory' ),
		'popular_items' 	=> NULL,
	);
	$args = array(
		'labels' 		=> $labels,
		'show_tagcloud' => false,
		'hierarchical' 	=> false, // single level only
	);
	register_taxonomy( 'member_category', 'member', $args );

	

	// If options haven't previously been setup, create the default member directory options
	$defaults = array(
				'link_name' => false,
				'format_phone' => true,
				'show_vacant_positions' => true,
				'show_group_link' => true,
				'use_contact_form' => true,
				'hide_from_public' => true,
				'capability' => "read",
				'contact_page_id' => 0,
				'reset_options' => false,
				'position_label' => __('Position','pta-member-directory'),
				'enable_location' => false,
				'location_label' => __('Location','pta-member-directory'),
				'contact_display' => 'both',
				'show_contact_names' => true,
				'show_first_names'	=> true,
				'add_blog_title'	=> true,
				'show_positions' => true,
				'show_locations' => false,
				'show_phone' => true,
				'show_photo' => true,
				'photo_size_x' => 100,
				'photo_size_y' => 100,
				'more_info' => true,
				'contact_message' => __('Thanks for your message! We\'ll get back to you as soon as we can.','pta-member-directory'),
				'force_table_borders' => false,
				'border_color' => '#000000',
				'border_size' => '1',
				'cell_padding' => '5',
				'enable_cfdb' => false,
				'form_title' => __('PTA Member Directory Contact Form','pta-member-directory'),
				'hide_donation_button' => false
				);
	$options = get_option( 'pta_directory_options', $defaults );
	// Make sure each option is set -- this helps if new options have been added during plugin upgrades
	foreach ($defaults as $key => $value) {
		if(!isset($options[$key])) {
			$options[$key] = $value;
		}
	}
	update_option( 'pta_directory_options', $options );

	if (true === $options['enable_location']) {
		// Labels for the Taxonomy - We're calling them "locations" for the 'member_location' taxonomy
		$labels = array(
			'name'              => _x( 'Locations', 'taxonomy general name', 'pta-member-directory' ),
			'singular_name'     => _x( 'Location', 'taxonomy singular name', 'pta-member-directory' ),
			'search_items'      => __( 'Search Locations', 'pta-member-directory' ),
			'all_items'         => __( 'All Locations', 'pta-member-directory' ),
			'parent_item'       => __( 'Parent Location', 'pta-member-directory' ),
			'parent_item_colon' => __( 'Parent Location:', 'pta-member-directory' ),
			'edit_item'         => __( 'Edit Location', 'pta-member-directory' ), 
			'update_item'       => __( 'Update Location', 'pta-member-directory' ),
			'add_new_item'      => __( 'Add New Location', 'pta-member-directory' ),
			'new_item_name'     => __( 'New Location', 'pta-member-directory' ),
			'menu_name'         => __( 'Locations', 'pta-member-directory' ),
			'popular_items' 	=> NULL,
		);
		$args = array(
			'labels' 		=> $labels,
			'show_tagcloud' => false,
			'hierarchical' 	=> false, // single level only
		);
		register_taxonomy( 'member_location', 'member', $args );
	}

	add_shortcode( 'pta_member_directory', 'pta_member_directory_shortcode' );
	add_shortcode( 'pta_member_contact', 'pta_member_contact_shortcode' );
	add_shortcode( 'pta_admin_contact', 'pta_admin_contact_shortcode');

	register_deactivation_hook( __FILE__, 'pta_member_directory_deactivate' );

	// Make sure the theme allows thumbnails for our custom post type
	if ( ! current_theme_supports( 'post-thumbnails', 'member' ) ) {
		add_theme_support( 'post-thumbnails' );
	} else {
		add_post_type_support( 'member', 'thumbnail' );
	}

}

/**
 * Calls the init function to define the custom post type and taxonomy
 * Then does the necessary flush 
 * @return none 
 */
function pta_member_directory_rewrite_flush() {
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry, 
    // when you add a post of this CPT.
    pta_member_directory_init();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'pta_member_directory_rewrite_flush' ); // Calls the above function when plugin is activated


/**
 * Upon deactivation, check to see if we should delete the directory options so that they will be reset when reactivated
 */
function pta_member_directory_deactivate() {
	$options = get_option( 'pta_directory_options' );
	if(true === $options['reset_options']) {
		delete_option( 'pta_directory_options' );
	}
}


/********************************************************************************************************************************/
/********************************************************************************************************************************
								META BOX FUNCTIONS
 ********************************************************************************************************************************
 ********************************************************************************************************************************/

/**
 * Sets up the messages to be displayed when working with our custom post type
 * @param  array $messages The message content array we are filtering to add our custom messages
 * @return array           return the updated messages array
 */
function pta_member_directory_messages( $messages ) {
	global $post, $post_ID;
	$messages['member'] = array(
		0 => '', 
		1 => sprintf( __('Member updated. <a href="%s">View Member</a>', 'pta-member-directory'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.', 'pta-member-directory'),
		3 => __('Custom field deleted.', 'pta-member-directory'),
		4 => __('Member updated.', 'pta-member-directory'),
		5 => isset($_GET['revision']) ? sprintf( __('Member restored to revision from %s', 'pta-member-directory'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Member published. <a href="%s">View Member</a>', 'pta-member-directory'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Member saved.', 'pta-member-directory'),
		8 => sprintf( __('Member submitted. <a target="_blank" href="%s">Preview Member</a>', 'pta-member-directory'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Member scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Member</a>', 'pta-member-directory'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Member draft updated. <a target="_blank" href="%s">Preview Member</a>', 'pta-member-directory'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'pta_member_directory_messages' ); // The filter we hook into for the above function


/**
 * Set up the custom meta boxes for inputting our member info
 * Text input fields for phone, and email
 * Checkboxes for any existing positions (our custom taxonomies)
 * Plus another text field if you want to enter a new position
 * @param  object $post The Wordpress Post object
 * @return HTML       Outputs the Custom Meta box form
 */
function pta_member_directory_show_contact_meta_box($post) {
	$prefix = '_pta_member_directory_';  
	$custom_meta_fields = array(  
	    array(  
	        'label'	=> __('Contact Phone:', 'pta-member-directory'),  
	        'desc'  => __('contact phone #', 'pta-member-directory'),  
	        'id'    => $prefix.'phone',  
	        'type'  => 'text'  
	    ), 
	    array(  
	        'label'	=> __('Contact Email:', 'pta-member-directory'),  
	        'desc'  => __('contact email address', 'pta-member-directory'),  
	        'id'    => $prefix.'email',  
	        'type'  => 'text'  
	    ), 
	    array(  
		    'label' => __('Positions: ', 'pta-member-directory'), 
		    'desc'  => __('Select one or more positions, or use the box below to add a new position.', 'pta-member-directory'), 
		    'id'    => 'member_category',  
		    'type'  => 'tax_select'  
		)  
	);  
	$options = get_option( 'pta_directory_options' );
	if ( isset($options['enable_location']) && true === $options['enable_location']) {
		$custom_meta_fields[] = array(  
		    'label' => __('Locations: ', 'pta-member-directory'), 
		    'desc'  => __('Select one or more locations, or use the box below to add a new location.', 'pta-member-directory'), 
		    'id'    => 'member_location',  
		    'type'  => 'tax_select'  
		);
	}
	// Other plugins may add their own custom meta fields
	// Use the above format and append new fields to the $custom_meta_fields array
	$custom_meta_fields = apply_filters( 'pta_member_directory_meta_fields', $custom_meta_fields, $prefix );

	// Use nonce for verification  
	echo '<input type="hidden" name="pta_member_directory_post_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';  	      
	    // Begin the field table and loop  
	    echo '<table class="form-table">';  
	    foreach ($custom_meta_fields as $field) {  
	        // get value of this field if it exists for this post  
	        $meta = get_post_meta($post->ID, $field['id'], true);  
	        // begin a table row with  
	        echo '<tr> 
	                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th> 
	                <td>';  
	                switch($field['type']) {  
	                    case 'text':  
						    echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.esc_attr($meta).'" size="30" /> 
						        <br /><span class="description">'.$field['desc'].'</span>';  
						break;  
						case 'tax_select':    
						    $terms = get_terms($field['id'], 'get=all');
						    // Create an array of already saved terms for the current member  
						    $selected_terms = wp_get_object_terms($post->ID, $field['id']);
						    $selected = array() ;
						    foreach ($selected_terms as $object) {
						     	$selected[] = $object->slug;
						     } 
						    foreach ($terms as $term) {  
						    	echo '<input type="checkbox" value="'.$term->slug.'" name="'.$term->slug.'" id="'.$term->slug.'" ';
						        if (!empty($selected) && in_array($term->slug, $selected)) {
						        	echo 'checked="checked"'; 						                
						        }  
						        echo ' /><label for="'.$term->slug.'"> '.$term->name.'</label><br />';
						    }
						    echo '<p>'.$field['desc'].'</p>';
						    echo '<input type="text" name="new_'.$field['id'].'" id="new_'.$field['id'].'" value="" size="30" />';
						break;
						default:
							// If your plugin is using a field type other than 'text' or 'tax_select'
							// hook your input field here. Make sure to check for $field['type']
							// your hook should echo the input form field for your field type
							do_action( 'pta_member_directory_other_field_types', $field, $post );
							
	                } //end switch  
	        echo '</td></tr>';  
	    } // end foreach  
	    echo '</table>'; // end table   
}


/**
 * Creates the custom meta box to be displayed on the post editor screen.
 * This function just adds the meta box, and defines the above function for the display of the meta box
 * 
 */
function pta_member_directory_add_meta_boxes() {
	add_meta_box(
		'pta_member_directory-contact',			// Unique ID
		esc_html__( 'Member Info', 'pta-member-directory' ),		// Title
		'pta_member_directory_show_contact_meta_box',		// Callback function
		'member',					// Admin page (or post type)
		'normal',					// Context
		'high'					// Priority
	);

}

/**
 * This function gets called when we save our custom post.  It processes the form and saves the post and any taxonomies
 * @param  int $post_id The Wordpress post ID
 * @param  object $post    The Wordpress post Object
 * @return           returns post_id if any errors, otherwise just saves the post and taxonomies
 */
function pta_member_directory_save_post_meta ( $post_id, $post ) {
	$options = get_option( 'pta_directory_options' );
	$prefix = '_pta_member_directory_';
	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['pta_member_directory_post_nonce'] ) || !wp_verify_nonce( $_POST['pta_member_directory_post_nonce'], basename( __FILE__ ) ) )
		return $post_id;
	// check autosave  
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)  
        return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	// Set the last name meta filed for easy sorting
	if (isset( $_POST['post_title'])) {
		$name = sanitize_text_field( $_POST['post_title'] );
	} else {
		$name = $post->post_title;
	}

	// Try to remove any suffix (parts after comma), 
	// should also work if they enter last name first and then first name after a comma
	$no_suffix = substr($name, 0, strpos($name, ","));
	if ('' == $no_suffix) {
		$no_suffix = $name;
	}
	$parts = explode(" ", $no_suffix);
	$new_meta_value = array_pop($parts); // Grab the lastname
	$meta_key = '_pta_member_directory_lastname';
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If a new meta value was added and there was no previous value, add it. */
	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );
	/* If the new meta value does not match the old value, update it. */
	elseif ( $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );
	/* If there is no new meta value but an old value exists, delete it. */
	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );

	/* Get the posted phone field and sanitize it. */
	$phone = ( isset( $_POST[$prefix.'phone'] ) ? sanitize_text_field( $_POST[$prefix.'phone'] ) : '' );

	if(isset($options['format_phone']) && true === $options['format_phone'] ) {
		/* Get the phone number into a consistent format, remove any invalid characters */
		// This is set for US/Canada phone numbers.  Will need to edit to allow other formats
		// First, get rid of everything except digits
		$phone_numbers = preg_replace("/[^0-9]/", "", $phone);
		if(strlen($phone_numbers) == 7) { // if it's 7 digits, we'll just add the dashes
			$new_meta_value = preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone_numbers);
		} elseif(strlen($phone_numbers) == 10) { // if it's 10 digits, use (555) 555-5555 format
			$new_meta_value = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone_numbers);
		} else { // not 7 or 10 digits, just return the original entry minus any characters that shouldn't be there
			$new_meta_value = preg_replace("/[^0-9\-\.\+\*\(\)\ ]/", "", $phone);
		}
	} else {
		// If we don't want US formatted phone, let's just get rid of any characters that should not be in a phone #
		$new_meta_value = preg_replace("/[^0-9\-\.\+\*\(\)\ ]/", "", $phone);
	}
		
	/* Get the meta key. */
	$meta_key = '_pta_member_directory_phone';

	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If a new meta value was added and there was no previous value, add it. */
	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	/* If the new meta value does not match the old value, update it. */
	elseif ( $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );

	/* If there is no new meta value but an old value exists, delete it. */
	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );

	// Repeat for the email field ************************************
	// 
	/* Get the posted email and sanitize it with the WP sanitize_email function. */
	$new_meta_value = ( isset( $_POST[$prefix.'email'] ) ? sanitize_email( $_POST[$prefix.'email'] ) : '' );

	/* Get the meta key. */
	$meta_key = '_pta_member_directory_email';

	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If a new meta value was added and there was no previous value, add it. */
	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	/* If the new meta value does not match the old value, update it. */
	elseif ( $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );

	/* If there is no new meta value but an old value exists, delete it. */
	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );

	
	$categories = array();
	// Check if they entered a new term, if so, insert it into database, then fetch it to get the generated slug
	// and set our first categories array value to the slug
	if (isset($_POST['new_member_category']) && '' != $_POST['new_member_category']) {
		$position = sanitize_text_field( $_POST['new_member_category'] );
		$result = wp_insert_term( $position, 'member_category' );
		if($result) {
			$term_id = $result['term_id'];
			$term = get_term_by('id', $term_id, 'member_category');
			if($term) {
				$categories[] = $term->slug;
			}
		}
	}
	// Now Process the positions entered or cleared by checkboxes
	$args = array( 'hide_empty' => false, );
	$terms = get_terms( 'member_category', $args );
	foreach ($terms as $term) {
		if(isset($_POST[$term->slug])) {
			$categories[] = $term->slug;
		}
	} 
	wp_set_object_terms( $post_id, $categories, 'member_category' ); // Set the terms	

	if(isset($options['enable_location']) && true === $options['enable_location']) {
		$locations = array();
		// Check if they entered a new term, if so, insert it into database, then fetch it to get the generated slug
		// and set our first locations array value to the slug
		if (isset($_POST['new_member_location']) && '' != $_POST['new_member_location']) {
			$position = sanitize_text_field( $_POST['new_member_location'] );
			$result = wp_insert_term( $position, 'member_location' );
			if($result) {
				$term_id = $result['term_id'];
				$term = get_term_by('id', $term_id, 'member_location');
				if($term) {
					$locations[] = $term->slug;
				}
			}
		}
		// Now Process the locations entered or cleared by checkboxes
		$args = array( 'hide_empty' => false, );
		$terms = get_terms( 'member_location', $args );
		foreach ($terms as $term) {
			if(isset($_POST[$term->slug])) {
				$locations[] = $term->slug;
			}
		} 
		wp_set_object_terms( $post_id, $locations, 'member_location' ); // Set the terms
	}

	// Provide an easy hook for other plugins to save any custom meta data they added
	do_action( 'pta_directory_save_meta', $post_id, $post, $prefix );
}


/* Meta box setup function. */
function pta_member_directory_post_meta_boxes_setup() {
	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'pta_member_directory_add_meta_boxes' );
	add_action( 'save_post', 'pta_member_directory_save_post_meta', 10, 2 );
}
/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'pta_member_directory_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'pta_member_directory_post_meta_boxes_setup' );

/**
 * Defines the colums and labels for our custom posts admin display
 * Hooks into wordpress manage_edit filter
 * @param  array $columns Column array we want to filter for our post type
 * @return array          returns our filtered column array
 */
function pta_member_columns( $columns ) {
	$columns['id'] = __('ID', 'pta-member-directory');
	$columns['title'] = __('Member Name', 'pta-member-directory');
	$columns['pta_member_directory_position'] = __('Position', 'pta-member-directory');
    $columns['_pta_member_directory_phone'] = __('Phone', 'pta-member-directory');
    $columns['_pta_member_directory_email'] = __('Email', 'pta-member-directory');
    unset( $columns['comments'] );
    unset( $columns['date'] );
    $options = get_option('pta_directory_options');
    if(isset($options['enable_location']) && true === $options['enable_location']) {
    	$columns['pta_member_directory_location'] = __('Location', 'pta-member-directory');
    }
    // Allow other plug-ins to modify our columns
    $columns = apply_filters( 'pta_member_directory_member_columns', $columns );
    
    return $columns;
}
add_filter( 'manage_edit-member_columns', 'pta_member_columns' );  // registers the above function for the wp filter

/**
 * Display the post info for our custom post type.  Fills our columns in with the custom post meta info we setup
 * @param  string $column  the column field name
 * @param  int $post_id the current post id
 * @return HTML          retrieves and echos out our custom meta data for the column
 */
function populate_pta_columns( $column, $post_id ) {
	$options = get_option('pta_directory_options');
    if ( '_pta_member_directory_phone' == $column ) {
        $pta_member_directory_phone = esc_html( get_post_meta( get_the_ID(), '_pta_member_directory_phone', true ) );
        echo $pta_member_directory_phone;
    }
    elseif ( '_pta_member_directory_email' == $column ) {
        $pta_member_directory_email = esc_html( get_post_meta( get_the_ID(), '_pta_member_directory_email', true ) );
        echo '<a href="mailto:'.$pta_member_directory_email.'">'.$pta_member_directory_email.'</a>';
    }
    elseif ( 'pta_member_directory_position' == $column ) {
    	$positions = get_the_terms($post_id, 'member_category');
        if (is_array($positions)) {
            foreach($positions as $key => $position) {
                $positions[$key] = $position->name;
            }
        	echo esc_html(implode(' | ',$positions));
        } else {
        	$pta_member_directory_position = esc_html(get_post_meta( get_the_ID(), 'member_category', true ));
        	echo $pta_member_directory_position;
        }
    }
    elseif ( isset($options['enable_location']) && true === $options['enable_location'] && 'pta_member_directory_location' == $column ) {
    	$locations = get_the_terms($post_id, 'member_location');
        if (is_array($locations)) {
            foreach($locations as $key => $location) {
                $locations[$key] = $location->name;
            }
        	echo esc_html(implode(' | ',$locations));
        } else {
        	$pta_member_directory_location = esc_html(get_post_meta( get_the_ID(), 'member_location', true ));
        	echo $pta_member_directory_location;
        }
    }
    elseif ('id' == $column ) {
    	echo get_the_ID();
    }
}
add_action( 'manage_member_posts_custom_column', 'populate_pta_columns', 10, 2 ); // hook our above function to the wordpress action hook

/**
 * Extends the filter capabilities of the posts lists to include filtering with our custom taxonomy values
 * @return HTML Updates the filter drop down list if we are showing the 'member' post type
 */
function pta_filter_list() {
    $screen = get_current_screen();
    global $wp_query;
    if ( $screen->post_type == 'member' ) {
        wp_dropdown_categories( array(
            'show_option_all' => __('Show All Positions', 'pta-member-directory'),
            'taxonomy' => 'member_category',
            'name' => 'member_category',
            'orderby' => 'name',
            'selected' => ( isset( $wp_query->query['member_category'] ) ? $wp_query->query['member_category'] : '' ),
            'hierarchical' => true,
            'depth' => 2,
            'show_count' => false,
            'hide_empty' => true,
        ) );
        $options = get_option('pta_directory_options');
        if (isset($options['enable_location']) && true === $options['enable_location']) {
        	wp_dropdown_categories( array(
	            'show_option_all' => __('Show All Locations', 'pta-member-directory'),
	            'taxonomy' => 'member_location',
	            'name' => 'member_location',
	            'orderby' => 'name',
	            'selected' => ( isset( $wp_query->query['member_location'] ) ? $wp_query->query['member_location'] : '' ),
	            'hierarchical' => true,
	            'depth' => 2,
	            'show_count' => false,
	            'hide_empty' => true,
	        ) );
        }
    }
}
add_action( 'restrict_manage_posts', 'pta_filter_list' ); // hook the above function into wordpress

/**
 * Does the actual filtering of our post list
 * @param  WP Query $query the current wordpress post query
 * @return         alters the query if it includes our custom taxonomy
 */
function pta_perform_filtering( $query ) {
    $qv = &$query->query_vars;
    if (!array_key_exists( 'member_category', $qv )) {
    	return;
    }
    if ( ( $qv['member_category'] ) && is_numeric( $qv['member_category'] ) ) {
        $term = get_term_by( 'id', $qv['member_category'], 'member_category' );
        $qv['member_category'] = $term->slug;
    }
    if (!array_key_exists( 'member_location', $qv )) {
    	return;
    }
    if ( ( $qv['member_location'] ) && is_numeric( $qv['member_location'] ) ) {
        $term = get_term_by( 'id', $qv['member_location'], 'member_location' );
        $qv['member_location'] = $term->slug;
    }
}
add_filter( 'parse_query','pta_perform_filtering' ); // hooks the above function to the parse query filter

/**
 * Custom ordering of Member Name (title field)
 * Since our member name is a single string, we set a hidden meta value for lastname,
 * which we got when saving the post by splitting off the last word of the name field
 * So, now we can use that to sort the "names" by last name
 * @param  [type] $vars [description]
 * @return [type]       [description]
 */
function pta_title_column_orderby( $vars ) {
	if (!is_admin()) {
		return $vars;
	}
	$screen = get_current_screen();
	if ( $screen->post_type != 'member' ) {
		return $vars;
	}
    if ( isset( $vars['orderby'] ) && 'title' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => '_pta_member_directory_lastname',
            'orderby' => 'meta_value'
        ) );
    } 
    return $vars;
}
add_filter( 'request', 'pta_title_column_orderby' ); // hook for ordering column by title

/**
 * Changes the "Enter title" default text for our member posts to "Enter Member Name"
 * @param  string $title The title text input
 * @return string $title The renamed (if needed) title text input
 */
function pta_title_text_input( $title ){
	if (!is_admin()) {
		return $vars;
	}
	$screen = get_current_screen();
	if ( $screen->post_type == 'member' ) {
		$title = __('Enter Member Name', 'pta-member-directory');
		return $title;
	}
    return $title;
}
add_filter( 'enter_title_here', 'pta_title_text_input' ); // Hook for the above function

function pta_directory_options_form( $options=array() ) {
	$capabilities = array(
			__('Subscriber', 'pta-member-directory') => 'read',
			__('Contributor', 'pta-member-directory') => 'edit_posts',
		);
	// Set up translation ready text for the form
	$form_title = __('Member Directory Options', 'pta-member-directory');
	$contact_section_title = __('Contact Form Options', 'pta-member-directory');
	$other_options_title = __('Other Options', 'pta-member-directory');
	$format_phone_label = __('Automatically format 7 or 10 digit phone numbers in US format?', 'pta-member-directory');
	$format_phone_desc = __(" YES <em>(if checked, any new 7 or 10 digit inputs will be formatted as (555) 555-5555.<br />Existing entries won't be changed unless you update them.)</em>", 'pta-member-directory');
	$show_phone_label = __('Show phone numbers in directory?', 'pta-member-directory');
	$show_phone_desc = __(" YES <em>(if checked, member contact phone numbers will be shown if they are set for that member.)</em>", 'pta-member-directory');
	$show_photo_label = __('Show photos in directory?', 'pta-member-directory');
	$show_photo_desc = __(" YES <em>(if checked, member photos will be shown if one exists for that member.  They will be linked to that members post page.)</em>", 'pta-member-directory');
	$photo_size_label = __('Directory Photo Size?', 'pta-member-directory');
	$photo_size_desc = __(" Size of member photos to display in directory listing.", 'pta-member-directory');
	$contact_form_label = __('Use Contact Form?', 'pta-member-directory');
	$contact_form_desc = __(' YES <em>(if checked, email will be hidden and replaced with a contact form link)</em>', 'pta-member-directory');
	$public_label = __('Hide From Public?', 'pta-member-directory');
	$public_desc = __(' YES <em>(if checked, member directory will only be displayed to logged in members)</em>', 'pta-member-directory');
	$user_level_label = __('Minimum user level to view:', 'pta-member-directory');
	$user_level_desc = __('(only applies if Hide From Public is checked)', 'pta-member-directory');
	$form_link_label = __('Contact form page: ', 'pta-member-directory');
	$form_link_desc = __('Only use this if you have set up a page with the [pta_member_contact] contact form shortcode, or want to use a different contact form.<br />Otherwise, leave this set to None to generate the contact form dynamically from the directory page.', 'pta-member-directory');
	$reset_options_label = __('Reset Options: ', 'pta-member-directory');
	$reset_options_desc = __(' Resets ALL of the above options to default values upon deactivation and reactivation of the plugin.', 'pta-member-directory');
	$position_label = __('Display name for Position: ', 'pta-member-directory');
	$position_label_desc = __(' This is the label used for the "Position" table column header in the public directory.', 'pta-member-directory');
	$enable_location_label = __('Enable Locations: ', 'pta-member-directory');
	$enable_location_desc = __('  YES <em>(Check this to enable locations/offices.  Locations will be displayed in the main member directory, or add a location to the shortcode to show a directory for a specific location/office.)</em>', 'pta-member-directory');
	$location_label = __('Display name for location: ', 'pta-member-directory');
	$location_label_desc = __(' If locations are enabled, this is the label used for the "Location" table column header in the public directory.', 'pta-member-directory');
	$contact_select_label = __('Contact form dropdown shows: ', 'pta-member-directory');
	$contact_select_desc = __(' Select if you want to show positions, individuals, or both on the contact form recipient drop down select box.', 'pta-member-directory');
	$show_contact_names_label = __('Show names after positions? ', 'pta-member-directory');
	$show_contact_names_desc = __(' YES <em>(if checked, and showing positions (or both) on contact form, the names of members who hold that position will be listed after the position name.)</em>', 'pta-member-directory');
	$show_first_names_label = __('Show only first names after positions? ', 'pta-member-directory');
	$show_first_names_desc = __(' YES <em>(if checked, and showing names after positions (see above), only the first names (i.e., first word in the member name) will be shown. Useful if several people hold a position and the form is getting too wide.)</em>', 'pta-member-directory');
	$show_positions_label = __('Show positions after names? ', 'pta-member-directory');
	$show_positions_desc = __(' YES <em>(if checked, and showing individuals (or both) on contact form, the positions a member holds will be listed after their name.)</em>', 'pta-member-directory');
	$show_group_link_label = __('Show Group Message links? ', 'pta-member-directory');
	$show_group_link_desc = __(' YES <em>(if checked, and if there is more than one member for a position, there will be a "Send Group A Message" link under the Position name to send everyone in that group a message.)</em>', 'pta-member-directory');
	$show_vacant_positions_label = __('Show Vacant Positions? ', 'pta-member-directory');
	$show_vacant_positions_desc = __(" YES <em>(if checked, any positions you have created that don't have assigned members will show in the directory list with VACANT listed in the name column.)</em>", 'pta-member-directory');
	$show_locations_label = __('Show locations after names? ', 'pta-member-directory');
	$show_locations_desc = __(' YES <em>(if checked, and locations are enabled, the locations of a member will be shown in parenthesis after their name for individual contacts (not position groups).)</em>', 'pta-member-directory');
	$contact_message_label = __('Contact form message: ', 'pta-member-directory');
	$contact_message_desc = __(' The message to display on the screen after a message has been successfully sent.  HTML allowed.', 'pta-member-directory');
	$enable_cfdb_label = __('Enable post to CFDB: ', 'pta-member-directory');
	$enable_cfdb_desc = __('  YES <em>(If the Contact Form DB plugin is installed, check this to save contact form submissions to the database via CFDB.)</em>', 'pta-member-directory');
	$add_blog_title_label = __('Add Blog Title to email Subject?: ', 'pta-member-directory');
	$add_blog_title_desc = __('  YES <em>(adds your blog title to the beginning of the email subject to let you know the email came from your contact form)</em>.', 'pta-member-directory');
	$form_title_label = __('Contact Form DB form title: ', 'pta-member-directory');
	$form_title_desc = __('Sets the form title that results will be stored under with the Contact Form DB plugin. (hidden form field)', 'pta-member-directory');
	$force_table_borders_label = __('Force Table Borders? ', 'pta-member-directory');
	$force_table_borders_desc = __(" YES <em>Adds some extra inline styling to the directory page to show tables borders, plus a bit of padding, for themes that don't use borders on tables by default (and for those who don't want to edit CSS).</em>", 'pta-member-directory');
	$border_properties_label = __('Table Properties: ', 'pta-member-directory');
	$border_color_label = __('Border Color', 'pta-member-directory');
	$border_size_label = __('Border Size (pixels)', 'pta-member-directory');
	$cell_padding_label = __('Cell padding (pixels)', 'pta-member-directory');
	$border_properties_desc = __('These table properties are only used if Force Table Borders is enabled.', 'pta-member-directory');
	$donation_text = __('Please help support continued development of this plugin. Any donation amount is greatly appreciated!', 'pta-member-directory');
	$hide_donation_button_label = __('Hide Donation Button: ', 'pta-member-directory');
	$hide_donation_button_desc = __(' Check this if you already donated, or just don\'t want to see the donation button any more.', 'pta-member-directory');
	$link_name_label = __('Link name to member page?', 'pta-member-directory');
	$link_name_desc = __(" YES <em>(if checked, and if the member has post content, a link will be created for the member name which will take the viewer to the member's bio/info page.)</em>", 'pta-member-directory');
	$more_info_label = __('Show "more info..." link?', 'pta-member-directory');
	$more_info_desc = __(' YES <em>(if checked, if the member has post content, and you have enabled photos, a "more info..." link will appear in the photo column that links to the member bio/info page.)</em>', 'pta-member-directory');
	
	$return = '
		<form name="pta_directory_options" id="pta_directory_options" method="post" action="">';

		$return .= '
		<h3>'.$form_title.'</h3>
			<table class="form-table">
				<tr>
					<th scope="row">'.$position_label.'</th>
					<td>
						<input type="text" maxlength="50" size="50" name="position_label" value="';
						if (isset($options['position_label'])) {
							$return .= esc_attr($options['position_label']);
						}
						$return .= '" /><br />
						<em>'.$position_label_desc.'</em>
					</td>
				</tr>
				<tr>
					<th scope="row">'.$link_name_label.'</th>
					<td>
						<input name="link_name" type="checkbox" value="true" ';
							if (isset($options['link_name']) && true === $options['link_name']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$link_name_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$show_vacant_positions_label.'</th>
					<td>
						<input name="show_vacant_positions" type="checkbox" value="true" ';
							if (isset($options['show_vacant_positions']) && true === $options['show_vacant_positions']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$show_vacant_positions_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$enable_location_label.'</th>
					<td>
						<input name="enable_location" type="checkbox" value="true" ';
							if (isset($options['enable_location']) && true === $options['enable_location']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$enable_location_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$location_label.'</th>
					<td>
						<input type="text" maxlength="50" size="50" name="location_label" value="';
						if (isset($options['location_label'])) {
							$return .= esc_attr($options['location_label']);
						}
						$return .= '" /><br />
						<em>'.$location_label_desc.'</em>
					</td>
				</tr>
				<tr>
					<th scope="row">'.$show_group_link_label.'</th>
					<td>
						<input name="show_group_link" type="checkbox" value="true" ';
							if (isset($options['show_group_link']) && true === $options['show_group_link']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$show_group_link_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$show_phone_label.'</th>
					<td>
						<input name="show_phone" type="checkbox" value="true" ';
							if (isset($options['show_phone']) && true === $options['show_phone']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$show_phone_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$format_phone_label.'</th>
					<td>
						<input name="format_phone" type="checkbox" value="true" ';
							if (isset($options['format_phone']) && true === $options['format_phone']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$format_phone_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$show_photo_label.'</th>
					<td>
						<input name="show_photo" type="checkbox" value="true" ';
							if (isset($options['show_photo']) && true === $options['show_photo']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$show_photo_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$photo_size_label.'</th>
					<td>
						X: <input type="text" maxlength="4" size="4" name="photo_size_x" value="';
						if (isset($options['photo_size_x'])) {
							$return .= esc_attr($options['photo_size_x']);
						}
						$return .= '" />
						Y: <input type="text" maxlength="4" size="4" name="photo_size_y" value="';
						if (isset($options['photo_size_y'])) {
							$return .= esc_attr($options['photo_size_y']);
						}
						$return .= '" />
						<em>'.$photo_size_desc.'</em>
					</td>
				</tr>
				<tr>
					<th scope="row">'.$more_info_label.'</th>
					<td>
						<input name="more_info" type="checkbox" value="true" ';
							if (isset($options['more_info']) && true === $options['more_info']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$more_info_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$public_label.'</th>
					<td>
						<input name="hide_from_public" type="checkbox" value="true" ';
							if (isset($options['hide_from_public']) && true === $options['hide_from_public']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$public_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$user_level_label.'</th>
					<td>
						<select name="capability">';
							foreach ($capabilities as $key => $value) {
								$return .= '<option value="' . $value .'" ';
								if(isset($options['capability']) && $value == $options['capability']) {
									$return .= 'selected="selected"';
								}
								$return .= '>'.$key.'</option>';
							}
						$return .= '
						</select>
						<em>'.$user_level_desc.'</em>
					</td>
				</tr>
				<tr>
					<th scope="row">'.$force_table_borders_label.'</th>
					<td>
						<input name="force_table_borders" type="checkbox" value="true" ';
							if (isset($options['force_table_borders']) && true === $options['force_table_borders']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$force_table_borders_desc.'
					</td>
				</tr>
				<tr>
					<th>'.$border_properties_label.'</th>
					<td>'.$border_color_label.':&nbsp;&nbsp;
						<input type="text" maxlength="7" size="7" class="pta_border_color" name="border_color" value="';
						if (isset($options['border_color'])) {
							$return .= esc_attr($options['border_color']);
						}
						$return .= '" /><br />'.$border_size_label.':&nbsp;
						<input type="text" maxlength="3" size="3" name="border_size" value="';
						if (isset($options['border_size'])) {
							$return .= (int)($options['border_size']);
						}
						$return .= '" />&nbsp;&nbsp;'.$cell_padding_label.':&nbsp;
						<input type="text" maxlength="3" size="3" name="cell_padding" value="';
						if (isset($options['cell_padding'])) {
							$return .= (int)($options['cell_padding']);
						}
						$return .= '" /><br/>
						<em>'.$border_properties_desc.'</em>
					</td>
				</tr>
			</table>';

			$return .='
			<h3>'.$contact_section_title.'</h3>
			<table class="form-table">
				<tr>
					<th scope="row">'.$contact_select_label.'</th>
					<td>
						<select name="contact_display">';
						$choices = array(__('positions', 'pta-member-directory'), __('individuals', 'pta-member-directory'), __('both', 'pta-member-directory'));
						foreach ($choices as $choice) {
							$return .= '<option value="'.$choice.'" ';
							if ( isset($options['contact_display']) && $choice == $options['contact_display'] ) {
								$return .= 'selected="selected"';
							}
							$return .= '">'.$choice.'</option>';
						}
						$return .= '</select><em>'.$contact_select_desc.'</em>
					</td>
				</tr>
				<tr>
					<th scope="row">'.$show_contact_names_label.'</th>
					<td>
						<input name="show_contact_names" type="checkbox" value="true" ';
							if (isset($options['show_contact_names']) && true === $options['show_contact_names']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$show_contact_names_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$show_first_names_label.'</th>
					<td>
						<input name="show_first_names" type="checkbox" value="true" ';
							if (isset($options['show_first_names']) && true === $options['show_first_names']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$show_first_names_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$show_positions_label.'</th>
					<td>
						<input name="show_positions" type="checkbox" value="true" ';
							if (isset($options['show_positions']) && true === $options['show_positions']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$show_positions_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$show_locations_label.'</th>
					<td>
						<input name="show_locations" type="checkbox" value="true" ';
							if (isset($options['show_locations']) && true === $options['show_locations']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$show_locations_desc.'
					</td>
				</tr>				
				<tr>
					<th scope="row">'.$contact_form_label.'</th>
					<td>
						<input name="use_contact_form" type="checkbox" value="true" ';
							if (isset($options['use_contact_form']) && true === $options['use_contact_form']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$contact_form_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$form_link_label.'</th>
					<td>';
						$args = array(
				    		"show_option_none" => __('None', 'pta-member-directory'),
				    		"selected" => (int)$options['contact_page_id'],
				    		"name" => "contact_page_id",
				    		"echo" => 0,
						);
						$return .= wp_dropdown_pages($args);
						$return .= '<br />
						<em>'.$form_link_desc.'</em>
					</td>
				</tr>
				<tr>
					<th scope="row">'.$add_blog_title_label.'</th>
					<td>
						<input name="add_blog_title" type="checkbox" value="true" ';
							if (isset($options['add_blog_title']) && true === $options['add_blog_title']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$add_blog_title_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$enable_cfdb_label.'</th>
					<td>
						<input name="enable_cfdb" type="checkbox" value="true" ';
							if (isset($options['enable_cfdb']) && true === $options['enable_cfdb']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$enable_cfdb_desc.'
					</td>
				</tr>
				<tr>
					<th>'.$form_title_label.'</th>
					<td>
						<input type="text" maxlength="100" size="100" name="form_title" value="';
						if (isset($options['form_title'])) {
							$return .= esc_attr($options['form_title']);
						}
						$return .= '" /><br />
						<em>'.$form_title_desc.'</em>
					</td>
				</tr>
				<tr>
					<th scope="row">'.$contact_message_label.'</th>
					<td>
						<textarea cols="100" rows="5" name="contact_message">';
						if (isset($options['contact_message'])) {
							$return .= esc_html(stripslashes($options['contact_message']));
						}
						$return .= '</textarea><br />
						<em>'.$contact_message_desc.'</em>
					</td>
				</tr>
			</table>';

			$return .= '
			<h3>'.$other_options_title.'</h3>
			<table class="form-table">
				<tr>
					<th scope="row">'.$reset_options_label.'</th>
					<td>
						<input name="reset_options" type="checkbox" value="true" ';
							if (isset($options['reset_options']) && true === $options['reset_options']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$reset_options_desc.'
					</td>
				</tr>
				<tr>
					<th scope="row">'.$hide_donation_button_label.'</th>
					<td>
						<input name="hide_donation_button" type="checkbox" value="true" ';
							if (isset($options['hide_donation_button']) && true === $options['hide_donation_button']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$hide_donation_button_desc.'
					</td>
				</tr>
			</table>';

			$return .= '
			<p class="submit">'
            	.wp_nonce_field($action = "pta_directory_options", $name = "pta_directory_options_nonce").'
            	<input type="hidden" name="pta_directory_options_mode" value="submitted" />
            	<input type="submit" name="update" class="button-primary" value="'.__("SUBMIT", "pta-member-directory").'" />
            	<input type="submit" name="cancel" class="button-secondary" value="'.__("CANCEL", "pta-member-directory").'" />
            </p>
		</form>
		';
	if (false === $options['hide_donation_button']) {
		$return .= '
			<h5>'.$donation_text.'</h5>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="7U6S4U46CKYPJ">
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		';
	}
		
	return $return;
}


/**
 * Displays the settings page for our member post type
 * @return HTML Calls and Processes the options page form
 */
function pta_directory_settings_page() {
	$options = get_option( 'pta_directory_options' );
	$messages = '';
	$text_fields = array('capability', 'position_label', 'location_label', 'contact_display', 'form_title');
	$numeric_fields = array('photo_size_x', 'photo_size_y', 'contact_page_id', 'border_size', 'cell_padding');
	// Check if the options form was submitted
	if ($submitted = isset($_POST['pta_directory_options_mode']) && 'submitted' == $_POST['pta_directory_options_mode']) {
		if(!wp_verify_nonce($_POST['pta_directory_options_nonce'], 'pta_directory_options')) {
			$messages = '<div id="message" class="error">' . __( 'Invalid Referrer!', 'pta-member-directory' ) . '</div>';
		} elseif ( isset($_POST['cancel']) ) {
			$messages = '<div id="message" class="error">' . __('Update Cancelled', 'pta-member-directory') . '</div>';
		} elseif (isset($_POST['update']) ) { // update the options
			foreach ($options as $key => $value) {
				if (in_array($key, $text_fields)) {
					$options[$key] = sanitize_text_field($_POST[$key]);
				} elseif ( in_array($key, $numeric_fields)) {
					$options[$key] = (int)(strip_tags($_POST[$key])); // these need to be numbers
				} elseif ( 'contact_message' == $key ){
					$options[$key] = wp_kses_post( $_POST[$key] );
				} elseif ('border_color' == $key) {
					// Make sure it's a valid hex color code
					if ( preg_match('/^#[a-f0-9]{6}$/i', $_POST[$key]) ) {
			    		$options[$key] = $_POST[$key];
			    	} else {
			    		$options[$key] = '#000000';
			    	}
				} else {
					if(isset($_POST[$key])) {
						$options[$key] = true;
					} else {
						$options[$key] = false;
					}
				}
			}
			// Allow other plugins to modify our standard options
			//apply_filters( 'pta_directory_before_saving_options', $options );
			if(update_option( 'pta_directory_options', $options )) {
				$messages = '<div id="message" class="updated">' . __('Options Updated!', 'pta-member-directory') . '</div>';
				$not_changed = false;
			} else {
				$messages = '<div id="message" class="error">' . __('Error! Options not changed.', 'pta-member-directory') . '</div>';
				$not_changed = true;
			}
			// Allow other plugins to update any options they added to our options page, and pass them the messages & main options
			// They can get their posted options from the global $_POST
			//$messages = apply_filters( 'pta_directory_after_save_options', $messages, $not_changed );
		}
	}
	ob_start();  ?>
	<div class="wrap">
	<div id="icon-users" class="icon32"><br/></div>
	<h2><?php _e('PTA Member Directory - Options', 'pta-member-directory'); ?></h2>
	<p><strong><?php _e('Click on the Help tab in the upper right for detailed help.', 'pta-member-directory'); ?> <br />
	</strong></p>
		<?php 
		echo $messages; // show any messages
		echo pta_directory_options_form($options); // Show the display options form
	?>
	</div>
	<?php
	echo ob_get_clean();
}



/**
 * Display a simple table list of all positions and allows them to be sorted by drag and drop
 * This is how the display order is set for the public directory
 * The order is stored in wordpress options table as a simple array with the category slugs in the correct order
 * Uses the jQuery sortable library
 * @return HTML/jQuery
 */
function pta_directory_sort_page() {
	pta_save_member_categories(); // Found a hook, but let's just make sure we are up to date anyway by calling this function again
	$pta_categories = get_option( 'pta_member_categories' );
	ob_start();  ?>
	<div class="wrap">
	<div id="icon-users" class="icon32"><br/></div>
	<h2><?php _e('PTA Member Directory - Sort Position Display Order', 'pta-member-directory'); ?></h2>
		<?php 
		if (!$pta_categories) { ?>
		<h2><?php _e('No Positions to display.<br/>Positions can be added from the Positions submenu, or when adding a new member.','pta-member-directory'); ?></h2>
		<?php
			return;
		} ?>
		<hr />
		<h4><?php _e('Drag and Drop the Positions below to change the display order on the public page.<br />(Order numbers will change after you refresh the page)', 'pta-member-directory'); ?></h4>
		<table class="wp-list-table widefat fixed posts pta-categories">
			<thead>
				<tr>
					<th><?php _e('Position', 'pta-member-directory'); ?></th>
					<th><?php _e('Order', 'pta-member-directory'); ?></th>
				</tr>
			</thead>
			<tbody>
	<?php 
		$count = 1;
		foreach ( $pta_categories as $key => $slug ) {
			$args = array( 'hide_empty'=>false,  'slug' => $slug );
			$terms = get_terms( 'member_category', $args );
			if ($terms) {
				$term = array_shift($terms);
				echo '<tr id="list_items_'.$key.'" class="list_item">
						<td>'.$term->name .'</td>
						<td>'.$count.'</td>
					</tr>';
			}
			$count++;			 	
		 } 

	?>
			</tbody>
			<tfoot>
				<tr>
					<th><?php _e('Position', 'pta-member-directory'); ?></th>
					<th><?php _e('Order', 'pta-member-directory'); ?></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?php
	echo ob_get_clean();
}


/* Add the "settings" submenu item to our custom post type admin menu */
function pta_member_plugin_menu() {
	global $pta_directory_page;
	global $pta_directory_sort_page;	
	$pta_directory_page = add_submenu_page( 'edit.php?post_type=member', 'settings',  __('Options','pta-member-directory'), 'manage_options', 'pta_member_settings', 'pta_directory_settings_page');
	$pta_directory_sort_page = add_submenu_page( 'edit.php?post_type=member', 'sort',  __('Sort Positions','pta-member-directory'), 'manage_options', 'pta_member_sort', 'pta_directory_sort_page');
	add_action('admin_print_styles-' . $pta_directory_sort_page, 'pta_member_directory_load_scripts');
	add_action('admin_print_styles-' . $pta_directory_page, 'pta_member_directory_options_load_scripts');
}
add_action('admin_menu', 'pta_member_plugin_menu'); // Calls the function above to add the submenu when we are in the admin menu

function pta_directory_custom_help() {
	$screen = get_current_screen();
	if('member' != $screen->post_type)
		return;

	include_once(dirname(__FILE__).'/includes/pta-directory-help-tabs.php');

	$members_help = pta_members_help_tab();
	$positions_help = pta_positions_help_tab();
	$locations_help = pta_locations_help_tab();
	$options_help = pta_options_help_tab();
	$shortcodes_help = pta_shortcodes_help_tab();
	$custom_links_help = pta_custom_links_help_tab();
	$sorting_help = pta_sort_positions_help_tab();
	$styling_help = pta_styling_help_tab();
	$modify_output_help = pta_modify_output_help_tab();

	$screen->add_help_tab( array(
	    'id'      => 'pta-members',
	    'title'   => __('Members Help', 'pta-member-directory'),
	    'content' => $members_help,
	));
	$screen->add_help_tab( array(
	    'id'      => 'pta-positions',
	    'title'   => __('Positions Help', 'pta-member-directory'),
	    'content' => $positions_help,
	));
	$screen->add_help_tab( array(
	    'id'      => 'pta-locations',
	    'title'   => __('Locations Help', 'pta-member-directory'),
	    'content' => $locations_help,
	));
	$screen->add_help_tab( array(
	    'id'      => 'pta-options',
	    'title'   => __('Options Help', 'pta-member-directory'),
	    'content' => $options_help,
	));
	$screen->add_help_tab( array(
	    'id'      => 'pta-shortcodes',
	    'title'   => __('Shortcodes Help', 'pta-member-directory'),
	    'content' => $shortcodes_help,
	));
	$screen->add_help_tab( array(
	    'id'      => 'pta-links',
	    'title'   => __('Custom links', 'pta-member-directory'),
	    'content' => $custom_links_help,
	));
	$screen->add_help_tab( array(
		    'id'      => 'pta-sort',
		    'title'   => __('Sorting Positions', 'pta-member-directory'),
		    'content' => $sorting_help,
	));	
	$screen->add_help_tab( array(
		    'id'      => 'pta-styling',
		    'title'   => __('Styling/Appearance', 'pta-member-directory'),
		    'content' => $styling_help,
	));	
	$screen->add_help_tab( array(
		    'id'      => 'pta-modify',
		    'title'   => __('Modifying Output Text', 'pta-member-directory'),
		    'content' => $modify_output_help,
	));	
}
add_action('admin_head', 'pta_directory_custom_help');

/**
 * Saves and updates our Wordpress Option that stores the list of positions in the order we want them displayed
 * This is called with the 3 different action hooks below this function, as well as first thing in our setting page function above,
 * just to make sure our list is always up to date
 */
function pta_save_member_categories() {
	$categories = get_option( 'pta_member_categories' );
	$args = array( 'hide_empty' => false, );
	$term_objects = get_terms( 'member_category', $args );
	if(!is_array($term_objects) || empty($term_objects)) {
		delete_option( 'pta_member_categories' );
		return;
	}

	$terms = array();
	// Get all the category slugs into an array
	foreach ($term_objects as $object) {
		$terms[] = $object->slug;
	}
	// First, let's check all the terms against the stored categories, and append them to the array if they don't exist
	foreach ( $terms as $term ) {
		if(!$categories || !in_array($term, $categories)) {
			$categories[] = $term;
		}
	}
	// Next, build a new category list out of only the categories that still exist as terms (in case some terms were deleted)
	$new_categories = array();
	foreach ( $categories as $category ) {
		if(in_array($category, $terms)) {
			$new_categories[] = $category;
		}
	} 
	update_option( 'pta_member_categories', $new_categories );
	
}


add_action('edited_member_category', 'pta_save_member_categories', 10, 1); // Update our categories list option if anything is edited
add_action('create_member_category', 'pta_save_member_categories', 10, 1); // Update our categories list option if anything is created
add_action('delete_member_category', 'pta_save_member_categories', 10, 1); // Update our categories list option if anything is deleted

$pta_md_plugin_file = 'pta-member-directory/pta-member-directory.php';
add_filter( "plugin_action_links_{$pta_md_plugin_file}", 'pta_md_plugin_action_links', 10, 2 );
function pta_md_plugin_action_links( $links, $file ) {
	$extensions_link = '<a href="http://stephensherrardplugins.com">' . __( 'Extensions', 'pta-member-directory' ) . '</a>';
	array_unshift( $links, $extensions_link );
	$settings_link = '<a href="' . admin_url( 'edit.php?post_type=member&page=pta_member_settings' ) . '">' . __( 'Settings', 'pta-member-directory' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
/* EOF */