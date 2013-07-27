<?php
/*
Plugin Name: PTA Member Directory
Plugin URI: http://www.dbar-productions.com
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
Version: 0.4
Author URI: http://dbar-productions.com
*/

/**
 * @todo For public release version Create a "location" or "office" option.
 *       Then can add members to the office/location, and query the results to filter by location
 *       if want to show members from just one office.
 * @todo Set taxonomy 'member_category' to heirarchial so can assign positions to parent groups
 *       This would require making a separate order table/option for parents, as well as other sort
 *       tables to order the children in each parent category.  This would probably require a nested
 *       array for the category sort order, and extra logic and display options in the public directory.
 *       When adding a new member, only show child positions to select.  Edit parents and parent>child
 *       relationship on the edit positions page.
 * @todo Create a page on admin where you can view positions and type in names of people for each position,
 *       as opposed to creating/editing people and picking their positions.  Perhaps an option to allow
 *       selecting existing site users for positions, and then automatically filling in their info.  This
 *       could later be enhanced by an jQuery search/select box to quickly select from large list of people
 */

include(dirname(__FILE__).'/includes/scripts.php');
include(dirname(__FILE__).'/includes/process-ajax.php');
require_once(dirname(__FILE__).'/includes/pta-display-directory.php');

add_action( 'init', 'pta_member_directory_init' );

/**
 * Initialization function.  Sets up the custom Post Type and Custom Taxonomy
 * Post type is registered as "member".  Taxonomy is registerd as "member_category"
 * @return Nothing This function just registers the post type and taxonomy
 */
function pta_member_directory_init() {
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
				'format_phone' => true,
				'use_contact_form' => true,
				'hide_from_public' => true,
				'capability' => "read",
				'contact_page_id' => 0,
				'reset_options' => false,
				'position_label' => 'Position',
				'contact_display' => 'both',
				'show_contact_names' => true,
				'show_positions' => true,
				'show_phone' => true,
				'show_photo' => true,
				'photo_size_x' => 100,
				'photo_size_y' => 100,
				'contact_message' => "Thanks for your message! We'll get back to you as soon as we can.",
				'enable_cfdb' => false,
				'form_title' => 'PTA Member Directory Contact Form'
				);
	$options = get_option( 'pta_directory_options', $defaults );
	// Make sure each option is set -- this helps if new options have been added during plugin upgrades
	foreach ($defaults as $key => $value) {
		if(!isset($options[$key])) {
			$options[$key] = $value;
		}
	}
	update_option( 'pta_directory_options', $options );


	add_shortcode( 'pta_member_directory', 'pta_member_directory_shortcode' );
	add_shortcode( 'pta_member_contact', 'pta_member_contact_shortcode' );

	register_deactivation_hook( __FILE__, 'pta_member_directory_deactivate' );

	load_plugin_textdomain( 'pta-member-directory', false, '/languages/' );

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
	        'label'=> __('Contact Phone:', 'pta-member-directory'),  
	        'desc'  => __('contact phone #', 'pta-member-directory'),  
	        'id'    => $prefix.'phone',  
	        'type'  => 'text'  
	    ), 
	    array(  
	        'label'=> __('Contact Email:', 'pta-member-directory'),  
	        'desc'  => __('contact email address', 'pta-member-directory'),  
	        'id'    => $prefix.'email',  
	        'type'  => 'text'  
	    ), 
	    array(  
		    'label' => __('Positions: ', 'pta-member-directory'), 
		    'id'    => 'member_category',  
		    'type'  => 'tax_select'  
		)   
	);  
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
						    _e('<p>Select one or more positions, or use the box below to add a new position.</p>', 'pta-member-directory');
						    echo '<input type="text" name="new_position" id="new_position" value="" size="30" />';
						break;
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
	$name = ( isset( $_POST['post_title'] ) ? sanitize_text_field( $_POST['post_title'] ) : '' );
	$parts = explode(" ", $name);
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
	if (isset($_POST['new_position']) && '' != $_POST['new_position']) {
		$position = sanitize_text_field( $_POST['new_position'] );
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
	$columns['id'] = 'ID';
	$columns['title'] = 'Member Name';
	$columns['pta_member_directory_position'] = 'Position';
    $columns['_pta_member_directory_phone'] = 'Phone';
    $columns['_pta_member_directory_email'] = 'Email';
    unset( $columns['comments'] );
    unset( $columns['date'] );
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
function title_column_orderby( $vars ) {
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
add_filter( 'request', 'title_column_orderby' ); // hook for ordering column by title

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
		$title = 'Enter Member Name';
		return $title;
	}
    return $title;
}
add_filter( 'enter_title_here', 'pta_title_text_input' ); // Hook for the above function

/**
 * Sets up our default template for showing the directory on the public side of the site
 * Allows themes to create their own template for our post type, but if it doesn't exist in the theme,
 * then this function sets the path for our member templates.
 * @param  [string] $template_path current path to the theme template files
 * @return [string] $template_path updated template path
 * @todo Not being used right now, but could make a default template for single-member.php to format their pages
 */

//function pta_include_template_function( $template_path ) {
//    if ( get_post_type() == 'member' ) {
//        if ( is_single() ) {
            // just return if it's a single, so we can show individual member page for now
            // in future, can set up a custom template to show individual members
//            return $template_path;
            /*if ( $theme_file = locate_template( array ( 'single-member.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-member.php';
            } */
//        } else {
        	// checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
 //       	if ( $theme_file = locate_template( array ( 'archive-member.php' ) ) ) {
 //               $template_path = $theme_file;
 //           } else {
 //               $template_path = plugin_dir_path( __FILE__ ) . '/archive-member.php';
 //           }
 //       }
 //   }
 //   return $template_path;
//}
//add_filter( 'template_include', 'pta_include_template_function', 1 ); // hook the above function to template_include action

function pta_directory_options_form( $options=array() ) {
	$capabilities = array(
			'Subscriber' => 'read',
			'Contributor' => 'edit_posts',
		);
	// Set up translation ready text for the form
	$form_title = __('Member Directory Options', 'pta-member-directory');
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
	$reset_options_desc = __(' Resets the above options to default values upon deactivation and reactivation of the plugin.', 'pta-member-directory');
	$position_label = __('Display name for Position: ', 'pta-member-directory');
	$position_label_desc = __(' This is the label used for the "Position" table column header in the public directory.', 'pta-member-directory');
	$contact_select_label = __('Contact form dropdown shows: ', 'pta-member-directory');
	$contact_select_desc = __(' Select if you want to show positions, individuals, or both on the contact form recipient drop down select box.', 'pta-member-directory');
	$show_contact_names_label = __('Show first names after positions? ', 'pta-member-directory');
	$show_contact_names_desc = __(' YES <em>(if checked, and showing positions (or both) on contact form, the first names of members who hold that position will be listed after the position name.)</em>', 'pta-member-directory');
	$show_positions_label = __('Show positions after names? ', 'pta-member-directory');
	$show_positions_desc = __(' YES <em>(if checked, and showing individuals (or both) on contact form, the positions a member holds will be listed after their name.)</em>', 'pta-member-directory');
	$contact_message_label = __('Contact form message: ', 'pta-member-directory');
	$contact_message_desc = __(' The message to display on the screen after a message has been successfully sent.  HTML allowed.', 'pta-member-directory');
	$enable_cfdb_label = __('Enable post to CFDB: ', 'pta-member-directory');
	$enable_cfdb_desc = __('  YES <em>(If the Contact Form DB plugin is installed, check this to save contact form submissions to the database via CFDB.)</em>', 'pta-member-directory');
	$form_title_label = __('Contact Form DB form title: ', 'pta-member-directory');
	$form_title_desc = __('Sets the form title that results will be stored under with the Contact Form DB plugin. (hidden form field)', 'pta-member-directory');
	$return = '
		<h3>'.$form_title.'</h3>
		<form name="pta_directory_options" id="pta_directory_options" method="post" action="">
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
					<th scope="row">'.$contact_select_label.'</th>
					<td>
						<select name="contact_display">';
						$choices = array('positions', 'individuals', 'both');
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
						<label><input name="show_contact_names" type="checkbox" value="true" ';
							if (isset($options['show_contact_names']) && true === $options['show_contact_names']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$show_contact_names_desc.'</label>
					</td>
				</tr>
				<tr>
					<th scope="row">'.$show_positions_label.'</th>
					<td>
						<label><input name="show_positions" type="checkbox" value="true" ';
							if (isset($options['show_positions']) && true === $options['show_positions']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$show_positions_desc.'</label>
					</td>
				</tr>
				<tr>
					<th scope="row">'.$show_phone_label.'</th>
					<td>
						<label><input name="show_phone" type="checkbox" value="true" ';
							if (isset($options['show_phone']) && true === $options['show_phone']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$show_phone_desc.'</label>
					</td>
				</tr>
				<tr>
					<th scope="row">'.$format_phone_label.'</th>
					<td>
						<label><input name="format_phone" type="checkbox" value="true" ';
							if (isset($options['format_phone']) && true === $options['format_phone']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$format_phone_desc.'</label>
					</td>
				</tr>
				<tr>
					<th scope="row">'.$show_photo_label.'</th>
					<td>
						<label><input name="show_photo" type="checkbox" value="true" ';
							if (isset($options['show_photo']) && true === $options['show_photo']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$show_photo_desc.'</label>
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
					<th scope="row">'.$contact_form_label.'</th>
					<td>
						<label><input name="use_contact_form" type="checkbox" value="true" ';
							if (isset($options['use_contact_form']) && true === $options['use_contact_form']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$contact_form_desc.'</label>
					</td>
				</tr>
				<tr>
					<th scope="row">'.$public_label.'</th>
					<td>
						<label><input name="hide_from_public" type="checkbox" value="true" ';
							if (isset($options['hide_from_public']) && true === $options['hide_from_public']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$public_desc.'</label>
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
					<th scope="row">'.$form_link_label.'</th>
					<td>';
						$args = array(
				    		"show_option_none" => "None",
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
					<th scope="row">'.$enable_cfdb_label.'</th>
					<td>
						<label><input name="enable_cfdb" type="checkbox" value="true" ';
							if (isset($options['enable_cfdb']) && true === $options['enable_cfdb']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$enable_cfdb_desc.'</label>
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
				<tr>
					<th scope="row">'.$reset_options_label.'</th>
					<td>
						<label><input name="reset_options" type="checkbox" value="true" ';
							if (isset($options['reset_options']) && true === $options['reset_options']) { 
								$return .= 'checked'; 
							}
							$return .= ' />'.$reset_options_desc.'</label>
					</td>
				</tr>
			</table>
			<p class="submit">'
            	.wp_nonce_field($action = "pta_directory_options", $name = "pta_directory_options_nonce").'
            	<input type="hidden" name="pta_directory_options_mode" value="submitted" />
            	<input type="submit" name="update" class="button-primary" value="SUBMIT" />
            	<input type="submit" name="cancel" class="button-secondary" value="CANCEL" />
            </p>
		</form>
	';
	return $return;
}

/**
 * Displays the settings page for our member post type
 * @return HTML Calls and Processes the options page form
 */
function pta_directory_settings_page() {
	$options = get_option( 'pta_directory_options' );
	$messages = '';
	// Check if the options form was submitted
	if ($submitted = isset($_POST['pta_directory_options_mode']) && 'submitted' == $_POST['pta_directory_options_mode']) {
		if(!wp_verify_nonce($_POST['pta_directory_options_nonce'], 'pta_directory_options')) {
			$messages = __( '<div id="message" class="error">Invalid Referrer!</div>', 'pta-member-directory' );
		} elseif (isset($_POST['cancel']) && 'CANCEL' == $_POST['cancel']) {
			$messages = __('<div id="message" class="error">Update Cancelled</div>', 'pta-member-directory');
		} elseif (isset($_POST['update']) && 'SUBMIT' == $_POST['update'] ) { // update the options
			foreach ($options as $key => $value) {
				if ('capability' == $key || 'position_label' == $key || 'contact_display' == $key || 'form_title' == $key) {
					$options[$key] = strip_tags($_POST[$key]);
				} elseif ( 'photo_size_x' == $key || 'photo_size_y' == $key || 'contact_page_id' == $key ) {
					$options[$key] = (int)(strip_tags($_POST[$key])); // these need to be numbers
				} elseif ( 'contact_message' == $key ){
					$options[$key] = wp_kses_post( $_POST[$key] );
				} else {
					if(isset($_POST[$key])) {
						$options[$key] = true;
					} else {
						$options[$key] = false;
					}
				}
			}
			if(update_option( 'pta_directory_options', $options )) {
				$messages = __('<div id="message" class="updated">Options Updated!</div>', 'pta-member-directory');
			} else {
				$messages = __('<div id="message" class="error">Error! Options not changed.</div>', 'pta-member-directory');
			}
		}
	}
	ob_start();  ?>
	<div class="wrap">
	<div id="icon-users" class="icon32"><br/></div>
	<h2><?php _e('PTA Member Directory - Options', 'pta-member-directory'); ?></h2>
	<p><?php _e('To display the member directory on a page, use the shortcode [pta_member_directory] <br />
	If you want to use the contact form on its own separate page, use the shortcode [pta_member_contact]', 'pta-member-directory'); ?></p>
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
		if (!$pta_categories) {
			_e('<h2>No Positions to display</h2>
				<p>Positions can be added from the Positions submenu, or when adding a new member.</p>', 'pta-member-directory');
			return;
		} ?>
		<hr />
		<h4><?php _e('Drag and Drop the Positions below to change the display order on the public page.<br />
							(Order numbers will change after you refresh the page)', 'pta-member-directory'); ?></h4>
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
	$pta_directory_page = add_submenu_page( 'edit.php?post_type=member', 'settings',  'Options', 'manage_options', 'pta_member_settings', 'pta_directory_settings_page');
	//add_action('admin_print_styles-' . $pta_directory_page, 'pta_member_directory_load_scripts');
	$pta_directory_sort_page = add_submenu_page( 'edit.php?post_type=member', 'sort',  'Sort Positions', 'manage_options', 'pta_member_sort', 'pta_directory_sort_page');
	add_action('admin_print_styles-' . $pta_directory_sort_page, 'pta_member_directory_load_scripts');
}
add_action('admin_menu', 'pta_member_plugin_menu'); // Calls the function above to add the submenu when we are in the admin menu

/**
 * Saves and updates our Wordpress Option that stores the list of positions in the order we want them displayed
 * This is called with the 3 different action hooks below this function, as well as first thing in our setting page function above,
 * just to make sure our list is always up to date
 */
function pta_save_member_categories() {
	$categories = get_option( 'pta_member_categories' );
	$args = array( 'hide_empty' => false, );
	if (!$term_objects = get_terms( 'member_category', $args )) {
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
add_action('delete_term', 'pta_save_member_categories', 10, 1); // Update our categories list option if anything is deleted


/* EOF */