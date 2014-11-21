<?php

function pta_display_directory($location='', $position='') {
	if ('' != $location) {
		$location = esc_html($location);
	}
	// Get our ordered category list, and allow other plugins to modify it.
	$categories = get_option( 'pta_member_categories' ); 
	if ('' != $position) {
		$position = strip_tags($position);
		if (in_array($position, $categories)) { // Make sure it's one of our category slugs
			// Make categories a one entry array with position
			$categories = array($position);
		} else {
			$position = '';
		}
	}
	$categories = apply_filters( 'pta_contact_form_categories', $categories );

	if(empty($categories)) {
		$return = '<p>'.apply_filters( 'pta_md_output', __('Sorry!  There is nothing to display yet.', 'pta-member-directory'), 'no_positions' ) .'</p>';
		return $return;
	}
	$options = get_option( 'pta_directory_options' ); // Display Options
	// First, check to see if option is set to hide the display from the public
	if (isset($options['hide_from_public']) && true === $options['hide_from_public']) {
		// If not logged in, then return with a message
		if (!is_user_logged_in()) {
			$return = '<p><strong>'.apply_filters( 'pta_md_output', __('Sorry!  You must be logged in to view the directory.', 'pta-member-directory'), 'not_logged_in' ).'</strong></p>';
			return $return;
		} elseif (!current_user_can( $options['capability'] )) {
			$return = '<p><strong>' . apply_filters( 'pta_md_output', __("Sorry!  You don't have the proper user level to view the directory.", 'pta-member-directory'), 'not_authorized') .'</strong></p>';
		}
	}
	if ( isset($_POST['contact_mode']) && 'submitted' == $_POST['contact_mode'] ) {
		$id = (int)($_POST['id']);
		$location = isset($_GET['location']) ? $_GET['location'] : '';
		$return = pta_directory_contact_form($id, $location);
		return $return;
	}
	if(isset($_GET['action']) && 'contact' == $_GET['action'] ) {
		$id = $_GET['id'];
		$location = isset($_GET['location']) ? $_GET['location'] : '';
		$return = pta_directory_contact_form($id, $location);
		return $return;
	}
	$members_shown = 0;
	// Set up table text for translation
	$column_position = $options['position_label']; // Get this from the options settings
	$column_name = apply_filters( 'pta_md_output', __('Name', 'pta-member-directory'), 'name_column_header' );
	$column_phone = apply_filters( 'pta_md_output', __('Phone', 'pta-member-directory'), 'phone_column_header' );
	$column_email = apply_filters( 'pta_md_output', __('Email', 'pta-member-directory'), 'email_column_header' );
	$vacant = apply_filters( 'pta_md_output', __('VACANT', 'pta-member-directory'), 'vacant_position' );
	$send_message = apply_filters( 'pta_md_output', __('Send A Message', 'pta-member-directory'), 'send_message' );
	$group_message = apply_filters( 'pta_md_output', __('Send Group A Message', 'pta-member-directory'), 'send_group_message' );
	$more_info = apply_filters( 'pta_md_output', __('more info...', 'pta-member-directory'), 'more_info' );

	// Allow other plugins to modify the starting column count if they are adding other columns
	// This is used to determine colspan for vacant positions
	$cols = (int)apply_filters( 'pta_directory_column_count', 2 );

	$return = '<div class="ptamd_directory_table">';

	// Allow other plugins to add content before the directory table
	$return .= apply_filters( 'pta_directory_before_table', '', $location );

	if($options['enable_location'] && '' != $location) {
		$args = array( 'hide_empty'=>false,  'slug' => $location  );
        $terms = get_terms( 'member_location', $args );
        if ($terms) {
            $term = array_shift($terms);
            $show_location = $term->name;
            $return .= '<h3 class="ptamd_location_header">'.apply_filters( 'pta_md_output', esc_html($options['location_label']).': '.esc_html($show_location), 'location_header', $show_location).'</h3>';
        }		
	}
	$show_positions = true;
	if('' != $position) {
		$args = array( 'hide_empty'=>false,  'slug' => $position  );
        $terms = get_terms( 'member_category', $args );
        if ($terms) {
            $term = array_shift($terms);
            $display_position = $term->name;
            $return .= '<h3 class="ptamd_position_header">'. apply_filters( 'pta_md_output', __('Directory Listing for ', 'pta-member-directory') . esc_html($column_position).': '.esc_html($display_position), 'position_header', $display_position ) . '</h3>';
            $show_positions = false;
            $cols--;
        }		
	}
	$return .= '
	<table class="pta_directory_table">
	        <thead>
	            <tr>';
	            if($show_positions) {
	            	$return .= '<th>'.esc_html($column_position).'</th>';
	            } 
	            $return .= apply_filters( 'pta_directory_table_headers_after_position', '', $show_positions );
	            $return .='   
	                <th>'.esc_html($column_name).'</th>';
                $return .= apply_filters( 'pta_directory_table_headers_after_name', '' );
                if($options['show_phone']) {
                	$return .= '<th>'.esc_html($column_phone).'</th>';
                	$cols++; // add one to our colspan
                }
              	$return .= '  
	                <th>'.esc_html($column_email).'</th>
	            </th>';
	            $return .= apply_filters( 'pta_directory_table_headers_after_email', '' );
	            if($options['enable_location'] && '' == $location) {
	            	// Show the location for each member if a specific location was not passed in
	            	$return .= '<th>'.esc_html($options['location_label']).'</th>';
	            	$cols++;
	            }
	            if($options['show_photo']) {
                	$return .= '<th>&nbsp;</th>';
                	$cols++; // add one to our colspan
                }
                $return .= apply_filters( 'pta_directory_table_headers_after_last', '' );
              	$return .= '
	        </thead>
	        <tbody>
	    ';
	    foreach ($categories as $slug) {
	        $args = array( 'hide_empty'=>false,  'slug' => $slug  );
	        $terms = get_terms( 'member_category', $args );
	        if ($terms) {
	            $term = array_shift($terms);
	            $category = $term->name;
	        } else {
	            $category = '&nbsp;';
	        }
	        $member_posts = array( 'post_type' => 'member', 'member_category' => $slug, 'meta_key' => '_pta_member_directory_lastname', 'orderby' => 'meta_value', 'order' => 'ASC', 'posts_per_page' => -1 );
	        if ('' != $location) {
	        	$member_posts['member_location'] = $location;
	        }
	        // Allow other plugins to modify the query
	        $member_posts = apply_filters( 'pta_directory_member_post_query', $member_posts );
	        $member_loop = new WP_Query( $member_posts );
	        $count = $member_loop->post_count;
	        // Keep track of how many members we have shown
	        $members_shown += $count;  
	        if ( 0 == $count) {
	        	if ('' != $location || !$options['show_vacant_positions']) {
	        		// Don't show VACANT positions for specific locations, since not every location will have every position
	        		// Perhaps update this in the future so positions can be linked to one or more locations (or all)
	        		continue;
	        	}
	            $return .= '<tr>';
	            if($show_positions) $return .= '<td><strong>'.esc_html($category).'</strong></td>';
	            $return .= apply_filters( 'pta_directory_table_content_after_vacant_position', '', $show_positions, $term );
	            $return .= '<td colspan="'.(int)$cols.'">'.esc_html($vacant).'</td></tr>';
	        } else {
	        	// Do we already have a contact page setup with the contact form shortcode?
	            // if so, get the link and add the id argument for the group
	            if ( isset($options['contact_page_id']) && 0 != $options['contact_page_id'] ) {
	            	$contact_url = add_query_arg( 'id', $slug, get_permalink($options['contact_page_id']) );
	            	if ($options['enable_location'] && '' != $location ) {
	            		$contact_url .= '&location='.$location;
	            	}
	            	if ('' != $position ) {
	            		$contact_url .= '&position='.$position;
	            	}
	            } else {
	            	$args = array ('action' => 'contact', 'id' => $slug);
	            	if ($options['enable_location'] && '' != $location) {
	            		$args['location'] = $location;
	            	}
	            	if ('' != $position) {
	            		$args['position'] = $position;
	            	}
	            	$contact_url = add_query_arg( $args );
	            }
	            // Add group message link if there is more than one person for the position && the option is set
	            $return .= '<tr>';
	            if (1 < $count && (isset($options['show_group_link']) && true === $options['show_group_link']) ) {
	            	$group_contact_link = '<a href="'.esc_url($contact_url).'">'.esc_html($group_message).'</a>';
	            } else {
	            	$group_contact_link = '';
	            }
	            if($show_positions) {
	            	$return .= '<td rowspan="'.(int)$count.'" style="vertical-align: middle;"><strong>'.esc_html($category).'</strong>';
	            	$return .= apply_filters( 'pta_directory_position_cell_before_group_contact_link', '', $term );
	                $return .= ' <br/>'. $group_contact_link;
		            $return .= '</td>';
	            } 
	            $return .= apply_filters( 'pta_directory_table_content_after_position', '', $show_positions, $count, $term );
	        }
	        $i=0;
	        while ( $member_loop->have_posts() ) : $member_loop->the_post();
	        	$id = get_the_ID();
	            $link = get_permalink( $id );
		        if (isset($options['use_contact_form']) && true === $options['use_contact_form']) {	            
		            // Do we already have a contact page setup with the contact form shortcode?
		            // if so, get the link and add the id argument
		            if ( isset($options['contact_page_id']) && 0 != $options['contact_page_id'] ) {
		            	// If only showing positions on contact form, then we need to set the contact id argument to the slug for the position to contact
		            	if ('positions' == $options['contact_display']) {
		            		$cid = $slug;
		            	} else {
		            		$cid = $id;
		            	}
		            	$contact_url = add_query_arg( 'id', $cid, get_permalink($options['contact_page_id']) );
		            	if ($options['enable_location'] && '' != $location ) {
		            		$contact_url .= '&location='.$location;
		            	}
		            } else {
		            	$args = array ('action' => 'contact', 'id' => $id);
		            	if ($options['enable_location'] && '' != $location) {
		            		$args['location'] = $location;
		            	}
		            	$contact_url = add_query_arg( $args );
		            }
		            $email = '<a href="'.esc_url($contact_url).'">'.$send_message.'</a>';
		        } else { // display the email with mailto link
		            $mail = esc_html( get_post_meta( get_the_ID(), '_pta_member_directory_email', true ) );
	            	$email = '<a href="mailto:'.esc_attr($mail).'">'.esc_html($mail).'</a>';
		        }
		        if (!is_email( get_post_meta( $id, '_pta_member_directory_email', true ) ) ) {
		        	$email = ''; // if they don't have an email, don't show anything
				}
		        $name = get_the_title(get_the_ID());
		        if (true == $options['link_name'] && '' != get_the_content() ) {
		        	$display_name = '<a href="'.esc_url($link ).'">'.esc_html($name).'</a>';
		        } else {
		        	$display_name = esc_html($name);
		        }
		        $phone = get_post_meta( get_the_ID(), '_pta_member_directory_phone', true );
	            if($i>0) {
	                $return .= '<tr>';
	            }
	            $return .= '
	                <td style="vertical-align: middle;">'. $display_name .'</td>';

                // Allow other plugins to add content cell after name
                $return .= apply_filters( 'pta_directory_table_content_after_name', '' );

	            if($options['show_phone']) {
	                $return .= '<td style="vertical-align: middle;">'. esc_html($phone) .'</td>';
	            }
	            $return .= '
	                <td style="vertical-align: middle;">'. $email .'</td>';

                // Allow other plugins to add content cell after name
                $return .= apply_filters( 'pta_directory_table_content_after_email', '' );

                if($options['enable_location'] && '' == $location) {
                	$return .= '<td style="vertical-align: middle;">';
                	$locations = get_the_terms( get_the_ID(), 'member_location');
			        if (is_array($locations)) {
			            foreach($locations as $key => $mlocation) {
			                $locations[$key] = $mlocation->name;
			            }
			        	$return .= esc_html(implode(' | ',$locations));
			        } else {
			        	$pta_member_directory_location = esc_html(get_post_meta( get_the_ID(), 'member_location', true ));
			        	$return .= $pta_member_directory_location;
			        }
			        $return .= '</td>';
                }
	            if( $options['show_photo'] ) {
					$return .= '<td>';
	            	if ( has_post_thumbnail($id)) {
	            		$size[] = $options['photo_size_x'];
	            		$size[] = $options['photo_size_y'];
	            		$image = get_the_post_thumbnail($id, $size);
	            		$return .= '<a href="'.esc_url($link).'">'. $image .'</a>';
	            	} else {
	            		$return .= '&nbsp;';
	            	}

	            	if ( true == $options['more_info'] && '' != get_the_content() ) {
	            	    $return .= '<br /><a href="'.esc_url($link).'">'.esc_html($more_info).'</a>';
	            	}
	            	
					$return .= '</td>';
	            }

	            // Allow other plugins to add content cell after name
	            $return .= apply_filters( 'pta_directory_table_content_after_last', '' );

	            $return .= '
	            </tr>';
	        	$i++;
	        endwhile; 
	        wp_reset_postdata();
	    } 
		 $return .='     
	    </tbody>
	</table>
	';
	$return .= apply_filters( 'pta_directory_after_table', '', $location );
	$return .= '</div>'; // close our table wrapper
	if(!$show_positions) {
		// If we aren't showing positions, add a group message link under the table
		$return .= $group_contact_link;
	}
	if($members_shown == 0) {
		$return = '<p>'. apply_filters( 'pta_md_output', __('Sorry!  There is nothing to display yet.', 'pta-member-directory'), 'no_members_shown') .'</p>';
	} 
	return $return;
}

function pta_directory_get_the_ip() {
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    }
    else {
        return $_SERVER["REMOTE_ADDR"];
    }
}

function pta_directory_contact_form($id='', $location='', $position='', $hide_select = false) {
	// check if they selected a recipient from the drop down select box, and update the id for proper name/email
	$location = esc_html($location);
	$selected = false;
	$group = false;

	// Get our ordered category list, and allow other plugins to modify it.
	$categories = get_option( 'pta_member_categories' ); 
	if ('' != $position) {
		$position = strip_tags($position);
		if (in_array($position, $categories)) { // Make sure it's one of our category slugs
			// Make categories a one entry array with position
			$categories = array($position);
			$id = $position; // Set the selected group to the position
		} else {
			// If not a valid position, just reset it to not trigger a pre-selected group recipient that doesn't exist
			$position = '';
		}
	}
	$categories = apply_filters( 'pta_contact_form_categories', $categories );

	$options = get_option( 'pta_directory_options' ); // Display Options
	$cc_mail = array(); // reset our CC mail list
	if(isset($_POST['location']) && '' != $_POST['location']) {
		$location = sanitize_text_field( $_POST['location'] );
	}
	if (isset($_POST['recipient']) && '' != $_POST['recipient']) {
		if (is_numeric($_POST['recipient'])) { // if it's a number, they selected a single member to contact
			$id = (int)$_POST['recipient'];
		} else { // if it's not a number, they selected a position/role to send to
			$id = false; // make sure this is unset
			$group = sanitize_text_field( $_POST['recipient'] ); // set group to the position category slug
			if(isset($_POST['position'])) {
				$position = strip_tags($_POST['position']);
			}
		}		
	} elseif (isset($_GET['id']) && '' != $_GET['id'] && !$id) {
		// check if we got a member id or a group name(slug) passed in
		if (is_numeric($_GET['id'])) {
			$id = (int)$_GET['id'];
		} else { // name or other characters passed in
			if (in_array($_GET['id'], $categories)) { // Make sure it's one of our category slugs
				$group = sanitize_text_field( $_GET['id'] );
			} 
			$id = false; // unset this for later logic operations
		}		
	} 
	if ($id && !is_numeric($id)) {
		// When not using shortcode for contact form, $id will get passed in directly as function variable
		// So, need to first check if it's numeric, and if not, see if it matches a position
		if (in_array($id, $categories)) { // Make sure it's one of our category slugs
			$group = sanitize_text_field( $id );
		} 
		$id = false; // unset so can pass through the next check
	}
	if ('-1' == $id) {
		// -1 is our admin contact form id
		$email = esc_html( get_bloginfo( 'admin_email') );
		$label_send_message = '';
		$selected = true;
	} elseif ($id && $post=get_post($id)) { // Make sure there is an entry with the given id
		// grab the name and email of the pta directory member we want to contact
		$email = esc_html( get_post_meta( $id, '_pta_member_directory_email', true ) );
		$name = $post->post_title;
		$label_send_message = apply_filters( 'pta_md_output', __('Send a message to: ', 'pta-member-directory') . $name, 'send_message_label', $name );
		$label_recipient = apply_filters( 'pta_md_output', __('Or select a different recipient:', 'pta-member-directory'), 'select_different_recipient' );
		$selected = true; // recipient selected
	} else {
		if ($group) { // $group recipient selected, so get posts with that taxonomy
			$args = array( 'post_type' => 'member', 'member_category' => $group );
			if ($options['enable_location'] && '' != $location) {
				$args['member_location'] = $location;
			}
			$members = get_posts( $args );
			$count = 1;
			$email = '';
			foreach ($members as $member) {

				if ( 1 == $count ) { // set main email to the first email
					$email = esc_html( get_post_meta( $member->ID, '_pta_member_directory_email', true ) );
				} else {
					if (is_email( esc_html( $cc = get_post_meta( $member->ID, '_pta_member_directory_email', true ) ) )) {
						$cc_mail[] = 'Cc: ' . $cc;  // // add CC addresses to an array
					}
				}
				if(is_email( $email )) {
					$count++; // only increment our counter if the first email was a valid email, otherwise we want to get the next one
				}				
			}

			$args = array( 'hide_empty'=>true,  'slug' => $group  );
	        $terms = get_terms( 'member_category', $args );
	        if ($terms) {
	            $term = array_shift($terms);
	            $display_name = $term->name;
	        } else {
	        	$display_name = '';
	        }
			$label_recipient = apply_filters( 'pta_md_output', __('Or select a different recipient:', 'pta-member-directory'), 'select_different_recipient' );
			$label_send_message = apply_filters( 'pta_md_output', __('Send a message to: ', 'pta-member-directory') . $display_name, 'send_message_label', $display_name );
			if (is_email( $email )) {
				$selected = true; // only set to true if we have a valid email
			} else {
				// Nobody in the group has a valid email, so let's change the message shown above the form
				$label_recipient = apply_filters( 'pta_md_output', __('Please select a different recipient:', 'pta-member-directory'), 'please_select_different_recipient' );
				$label_send_message = apply_filters( 'pta_md_output', __('There are currently no contact emails for group: ', 'pta-member-directory') . $display_name, 'no_group_contact_emails', $display_name );
			}
			
		} else {
			// if no $id or $group given, set the message to ask them to select a recipient, set flag to false
			$email = '';
			$name = '';
			$label_recipient = apply_filters( 'pta_md_output', __('Please select a recipient:', 'pta-member-directory'), 'select_recipient_no_recipient_label' );
			$label_send_message = apply_filters( 'pta_md_output', __('Send a message: ', 'pta-member-directory'), 'send_message_no_recipient_label' );
			$selected = false; // no recipient selected yet
		}
	}	
    $subject = "";
    $label_name =   apply_filters( 'pta_md_output', __("Your Name", 'pta-member-directory'), 'contact_label_name' );
    $label_email = apply_filters( 'pta_md_output', __("Your E-mail Address", 'pta-member-directory'), 'contact_label_email' );
    $label_subject = apply_filters( 'pta_md_output', __("Subject", 'pta-member-directory'), 'contact_label_subject' );
    $label_message = apply_filters( 'pta_md_output', __("Your Message", 'pta-member-directory'), 'contact_label_message' );
    $label_submit = apply_filters( 'pta_md_output', __("Submit", 'pta-member-directory'), 'contact_label_submit' );
    $label_option = apply_filters( 'pta_md_output', __('Select a recipient', 'pta-member-directory'), 'contact_label_option' );
    // the error message when at least one of the required fields are empty:
    $error_empty = apply_filters( 'pta_md_output', __("Please fill in all the required fields.", 'pta-member-directory'), 'contact_error_empty' );
    // the error message when the e-mail address is not valid:
    $error_noemail = apply_filters( 'pta_md_output', __("Please enter a valid e-mail address.", 'pta-member-directory'), 'contact_error_noemail' );
    $error_email = apply_filters( 'pta_md_output', __("That recipient doesn't currently have a valid email address.  Please choose another recipient.", 'pta-member-directory'), 'contact_error_recipient_noemail' );
    $error_nonce = apply_filters( 'pta_md_output', __("Invalid Referrer!", 'pta-member-directory'), 'contact_error_nonce' );
    $error_bot = apply_filters( 'pta_md_output', __("Spambot!", 'pta-member-directory'), 'contact_error_bot' );
    $error_recipient = apply_filters( 'pta_md_output', __("No recipient selected.  Please select one.", 'pta-member-directory'), 'contact_error_recipient' );
    $error_spamcheck = apply_filters( 'pta_md_output', __("Spamcheck Failed!", 'pta-member-directory'), 'contact_error_spamcheck' );
    $error_toofast = apply_filters( 'pta_md_output', __("Multiple submissions! Please wait at least 20 seconds between submissions.", 'pta-member-directory'), 'contact_error_toofast' );
    $wp_mail_error = apply_filters( 'pta_md_output', __("Wordpress Mail Error! Check server mail settings.", 'pta-member-directory'), 'contact_error_wp_mail' );
    $result = '';
    $sent = false;
    $info = '';

    // if the <form> element is POSTed, run the following code
	if ( isset($_POST['contact_mode']) && 'submitted' == $_POST['contact_mode'] ) {
		$error = false;
		if ( !wp_verify_nonce($_POST['pta_directory_contact_form_nonce'],'pta_directory_contact_form') ) {
		   $error = true;
		   $result = $error_nonce;
		}
		// check the hidden spambot field
		if ( isset($_POST['contactbot']) && '' != $_POST['contactbot']) {
			$error = true;
		   	$result = $error_bot;
		}	
	    // set the "required fields" to check
	    $required_fields = array( "your_name", "email", "message", "subject" );
	 
	    // this part fetches everything that has been POSTed, sanitizes them and lets us use them as $form_data['subject']
	    foreach ( $_POST as $field => $value ) {
	        if ( get_magic_quotes_gpc() ) {
	            $value = stripslashes( $value );
	        }
	        $form_data[$field] = stripslashes(sanitize_text_field( $value ) ); // get rid of any bad stuff
	    }
	 
	    // if the required fields are empty, switch $error to TRUE and set the result text to the error message named 'error_empty'
	    foreach ( $required_fields as $required_field ) {
	        $value = trim( $form_data[$required_field] );
	        if ( empty( $value ) ) {
	            $error = true;
	            $result = $error_empty;
	        }
	    }

	    // If no recipient was passed in, and/or no recipient selected, set the error
	    if (!$selected) {
	    	$error = true;
    		$result = $error_recipient;
	    }
	 
	    // and if the e-mail is not valid, switch $error to TRUE and set the result text to the error message named 'error_noemail'
	    if ( $selected && !is_email( $form_data['email'] ) ) {
	        $error = true;
	        $result = $error_noemail;
	    }

    	// Add a check to make sure the email we got from member directory is a valid email address
    	if ( $selected && !is_email( $email ) ) {
	        $error = true;
	        $result = $error_email;
	    }

	    // Serialize and save the $_SERVER info for advanced spam checking via Akismet and others
	    $form_data['server_array'] = serialize($_SERVER);
	    $form_data['user_ip'] = pta_directory_get_the_ip();

	    // Allow other plugins to do a more thorough spam check on our data, only if no errors so far
	    $spam_check = false;
	    if (false === $error) {
	    	$spam_check = apply_filters( 'pta_member_contact_spam_check', $spam_check, $form_data );
	    	if (true == $spam_check) {
	    		$error = true;
	        	$result = $error_spamcheck;
	    	}
	    }

	    // check for duplicate/multiple submissions
		$last_submit = get_transient('pta_md_last_submit');
		$last_ip = get_transient('pta_md_last_ip');
		$time = current_time( 'timestamp');
		if(false === $error && false !== $last_submit && false !== $last_ip) {
			if($time - $last_submit < 20 && $last_ip == $form_data['user_ip']) {
				$error = true;
				$result = $error_toofast;
			}
		}

	    // but if $error is still FALSE, put together the POSTed variables and send the e-mail!
	    if ( $error == false ) {
	    	// Update the transient if no errors (successful submit)
	    	set_transient( 'pta_md_last_submit', $time, 360 );
	    	set_transient( 'pta_md_last_ip', $form_data['user_ip'], 360 );
	    	$email_subject = '';
	    	if(true === $options['add_blog_title']) {
	    		// get the website's name and puts it in front of the subject
	        	$email_subject .= "[" . get_bloginfo( 'name' ) . "] ";
	    	}
	        
	        $email_subject .= $form_data['subject'];
	        // get the message from the form and add the sender info
	        $email_message = __("Sender Name: ", 'pta-member-directory') . $form_data['your_name'] ."\r\n";
	        $email_message .= __("Sender email: ", 'pta-member-directory') . $form_data['email'] ."\r\n";
	        $email_message .= __("Sender IP: ", 'pta-member-directory') . $form_data['user_ip'] ."\r\n\r\n";
	        $email_message .= $form_data['message'];
	        // set the e-mail headers with the user's name, e-mail address and character encoding
	        $headers = array();
	        $headers[]  = "From: " . $form_data['your_name'] . " <" . $form_data['email'] . ">";
	        $headers[] 	= 'Reply-To: '. $form_data['email'];
	        $headers[] 	= "Content-Type: text/plain; charset=UTF-8";
	        $headers[] 	= "Content-Transfer-Encoding: 8bit";
	        if (!empty($cc_mail)) { // Add any cc addresses
	        	foreach ($cc_mail as $cc) {
	        		$headers[] = $cc;
	        	}
	        }
	        // send the e-mail
	        if ( $success =wp_mail( $email, $email_subject, $email_message, $headers ) ) {
	        	// and set the result text to the success message set in the options
		        $result = wp_kses_post(stripslashes($options['contact_message']));
		        // ...and switch the $sent variable to TRUE
		        $sent = true;

		        // If enabled, post message to CFDB plugin
				if (isset($options['enable_cfdb']) && true === $options['enable_cfdb']) {
				 	$uploaded_files = array();
				 	$title = $options['form_title'];
				 	// If recipient is an individual (numeric ID), let's get the name for better CFDB display
				 	if (is_numeric($form_data['recipient'])) {
				 		$form_data['recipient'] = esc_attr(get_the_title($form_data['recipient']));
				 	}
				    // Prepare data structure for call to hook
				    $data = (object) array(
				        'title' => $title,
				        'posted_data' => $form_data,
				        'uploaded_files' => $uploaded_files);
				 
				    // Call hook to submit data
				    do_action_ref_array('cfdb_submit', array(&$data));
				}
	        } else {
	        	// wp_mail returned false
	        	$sent = false;
	        	$result = $wp_mail_error;
	        }
	        
			// Action hook to allow other plugins to use submitted form data
	        do_action( 'pta_member_contact_message_sent', $form_data );
	    }
	}

	// if there's no $result text (meaning there's no error or success, meaning the user just opened the page and did nothing) there's no need to show the $info variable
	if ( $result != "" ) {
	    $info = '<div class="info">' . $result . '</div>';
	}
	// Let's build the form!
	// 
	// First, allow others to add output before the form
	$email_form = apply_filters( 'pta_member_before_contact_form', '', $id, $location );

	if($options['enable_location'] && '' != $location) {
		$args = array( 'hide_empty'=>false,  'slug' => $location  );
        $terms = get_terms( 'member_location', $args );
        if ($terms) {
            $term = array_shift($terms);
            $show_location = $term->name;
            $email_form .= '<h2>'.esc_html($options['location_label']).': '.esc_html($show_location).'</h2>';
        }
	}
	$email_form .= '<h3>'.esc_html($label_send_message).'</h3>
	<form class="pta-contact-form" method="post" action="' . get_permalink() . '">
		<input type="hidden" name="form_title" value="'.esc_attr($options["form_title"]).'"/>';
		// Allow other plugins to add fields before the recipient
		$email_form = apply_filters( 'pta_member_contact_form_before_recipient', $email_form, $id, $location );
		if ('-1' == $id) {
			$email_form .= '<input type="hidden" name="recipient" value="-1"/>';
		} elseif ( '' != $position && $selected ) {
			$email_form .= '<input type="hidden" name="recipient" value="'.esc_attr($group).'"/>';
			$email_form .= '<input type="hidden" name="position" value="'.esc_attr($group).'"/>';
		} elseif ( ($id || $group) && $selected && $hide_select) {
			$recipient = $id ? $id : $group;
			$email_form .= '<input type="hidden" name="recipient" value="'.esc_attr($recipient).'"/>';
		} else {
			$email_form .='
			<div>
				<label for="cf_recipient">'.esc_html($label_recipient).'</label>
				<select name="recipient" id="cf_recipient">
					<option value="">'.esc_html($label_option).'</option>';
				$members = get_posts(  apply_filters('pta_contact_form_members_args', array(
					'numberposts'		=> -1,
					'orderby'			=>	'meta_value',
					'order'				=>	'ASC',
					'meta_key'			=>	'_pta_member_directory_lastname',
					'post_type'			=>	'member',
					'post_status'		=>	'publish' )
				), $id, $location );
				// Allow other plugins to change the members for the recipient list
				$members = apply_filters( 'pta_member_contact_form_members', $members, $id, $location );

				if( ('positions' == $options['contact_display'] || 'both' == $options['contact_display']) && !empty($categories) ) {
					// Create list of positions/categories for multi-recipient mail
					if ('both' == $options['contact_display']) {
						$email_form .= '<optgroup label="'. esc_attr($options['position_label']).'">';
					}
					foreach ($categories as $category) {
						$args = array( 'hide_empty'=>true,  'slug' => $category  );
				        $terms = get_terms( 'member_category', $args );
				        if ($terms) {
				            $term = array_shift($terms);
				            $display_name = $term->name;
				        } else {
				        	continue;
				        }
				        // Get member names that hold position and make sure there is at least one valid email
				        $email_exists = false; // will set this to true if we find at least one member in this position with valid email
				        $display_names = ''; // Use this to create a list of names of people in each position
				        $name_count = 0;
				        foreach ($members as $member) {
				        	if(has_term( $category, 'member_category', $member )) {
				        		$member_email = get_post_meta( $member->ID, '_pta_member_directory_email', true );
				        		if (!is_email($member_email)) continue; // Don't list the member if they have no valid email

				        		// Only show members for the specific location, if enabled and set
				        		if($options['enable_location'] && '' != $location) {
				        			$locations = get_the_terms( $member->ID, 'member_location');
							        if (is_array($locations)) {
							            if(!has_term( $location, 'member_location', $member->ID )) continue;
							        } else {
							        	$member_location = esc_html(get_post_meta( $member->ID, 'member_location', true ));
							        	if($location != $member_location) continue;
							        }
				        		}

				        		$email_exists = true; // got an email
				        		if(0 == $name_count) {
				        			$display_names .= ' - '; // put a separator before the first name in the list
				        		} else {
				        			$display_names .= ' | '; // separate names with a pipe
				        		}
				        		// Check if we want full names or just first names
				        		if ( true === $options['show_first_names']) {
				        			$name_arr = explode(' ',trim($member->post_title));
				        			$name = str_replace(',', '', $name_arr[0]); // Get rid of any comma
				        			$display_names .= esc_attr($name); // put just the first name (word) in there
				        		} else {
				        			$display_names .= esc_attr($member->post_title);
				        		}			        		
				        		$name_count++;
				        	}
				        }
				        if(!$email_exists) continue; // skip this position if we didn't find at least one member with an email
						$email_form .= '
						<option value="'.$category.'" ';
						if(isset($group) && $category == $group) {
							$email_form .= 'selected="selected"';
						}
						$email_form .= '>'.$display_name;

						// Show names after position name if the option is set
						if ($options['show_contact_names']) {
							$email_form .= $display_names;
						}
				 		$email_form .= '</option>';
					}
					if ('both' == $options['contact_display']) {
						$email_form .= '</optgroup>';
					}
				}
				if( 'individuals' == $options['contact_display'] || 'both' == $options['contact_display'] ) {
					// Individual Members Contact options
					if ('both' == $options['contact_display']) {
						$email_form .= '<optgroup label="'. __('Individuals', 'pta-member-directory') .'">';
					}				
					foreach ($members as $member) {
						$positions = get_the_terms($member->ID, 'member_category');
						if ($positions) {
							foreach($positions as $key => $position) {
				                $positions[$key] = $position->name;
				            }
				        	$positions = esc_html(implode(' | ',$positions));
						}
						$member_email = get_post_meta( $member->ID, '_pta_member_directory_email', true );
						if (!is_email($member_email)) continue; // Don't list the member if they have no valid email
						// Only show members for the specific location, if enabled and set
		        		if($options['enable_location'] && '' != $location) {
		        			$locations = get_the_terms( $member->ID, 'member_location');
					        if (is_array($locations)) {
					            if(!has_term( $location, 'member_location', $member->ID )) continue;
					        } else {
					        	$member_location = esc_html(get_post_meta( $member->ID, 'member_location', true ));
					        	if($location != $member_location) continue;
					        }
		        		}
						$email_form .= '
						<option value="'.$member->ID.'" ';
						if(isset($id) && $member->ID == $id) {
							$email_form .= 'selected="selected"';
						}
						$email_form .= '>'.$member->post_title;
						if ($options['enable_location'] && $options['show_locations'] && '' == $location) {
		        			// Show location after name, if enabled
		        			$member_locations = wp_get_post_terms( $member->ID, 'member_location' );
		        			if ($member_locations) {
		        				$email_form .= ' (';
		        				$count = count($member_locations);
		        				$i = 0;
		        				foreach ($member_locations as $mlocation) {
		        					$email_form .= $mlocation->name;
		        					$i++;
		        					if ($i < $count) {
		        						$email_form .= ', ';
		        					}
		        				}
		        				$email_form .= ')';
		        			}
		        		}
					 	if ( $positions && $options['show_positions'] ) {
					 		$email_form .= ' - '.$positions;
				 		}
				 		$email_form .= '</option>';
					}
					if ('both' == $options['contact_display']) {
						$email_form .= '</optgroup>';
					}
				}
		$email_form .='
				</select>
			</div>';
		}
		
	// Allow other plugins to add fields after recipient
	$email_form = apply_filters( 'pta_member_contact_form_after_recipient', $email_form, $id, $location );
	$email_form .='
	    <div>
	        <label for="cf_name">' . esc_html($label_name) . ':</label>
	        <input type="text" name="your_name" id="cf_name" size="50" maxlength="50" value="' . 
	        	( isset($form_data['your_name']) ? esc_attr($form_data['your_name']) : "" ) . '" />
	    </div>
	    <div>
	        <label for="cf_email">' . esc_html($label_email) . ':</label>
	        <input type="text" name="email" id="cf_email" size="50" maxlength="50" value="' .
	        	 ( isset($form_data['email']) ? esc_attr($form_data['email']) : "" ) . '" />
	    </div>
	    <div>
	        <label for="cf_subject">' . esc_html($label_subject) . ':</label>
	        <input type="text" name="subject" id="cf_subject" size="50" maxlength="50" value="' . esc_attr($subject) . 
	        	( isset($form_data['subject']) ? esc_attr($form_data['subject']) : "" ) . '" />
	    </div>
	    <div>
	        <label for="cf_message">' . esc_html($label_message) . ':</label>
	        <textarea name="message" id="cf_message" cols="50" rows="15">' . 
	        	( isset($form_data['message']) ? esc_textarea($form_data['message']) : "" ) . '</textarea>
	    </div>
	    <div style="visibility:hidden"> 
			<input name="contactbot" type="text"size="20"  >
	    </div>';
    // Allow other plugins to add fields before submit button
    $email_form = apply_filters( 'pta_member_contact_form_before_submit', $email_form, $id, $location );
    $email_form .='
	    <div>
	    	'.wp_nonce_field("pta_directory_contact_form", "pta_directory_contact_form_nonce").'
	    	<input type="hidden" name="contact_mode" value="submitted" />
	        <input type="submit" value="' . esc_attr($label_submit) . '" name="send" id="cf_send" />
	        <input type="hidden" value="'.esc_attr($id).'" name="id" />
	        <input type="hidden" value="'.esc_attr($location).'" name="location" />
	    </div>
	</form>';
	// Allow other plugins to put content after the form
	$email_form = apply_filters( 'pta_member_contact_form_after_form', $email_form, $id, $location );
	if ( $sent == true ) {
	    return $info;
	} else {
	    return $info . $email_form;
	}
}

add_action( 'wp_enqueue_scripts', 'pta_directory_add_my_stylesheet' );

function pta_directory_add_my_stylesheet() {
	wp_register_style( 'pta_directory-style', plugins_url('/css/pta-contact-form.css', __FILE__) );
    wp_enqueue_style( 'pta_directory-style' );
    $options = get_option( 'pta_directory_options' );
    if ( true === $options['force_table_borders'] ) {
    	$color = esc_attr($options['border_color']);
    	if ( !preg_match('/^#[a-f0-9]{6}$/i', $color) ) {
    		$color = "#000000";
    	}
    	$size = esc_attr($options['border_size']);
    	$padding = esc_attr($options['cell_padding']);
    	// Force borders and some padding for themes that don't have borders by default and user doesn't want to edit CSS
	    $custom_css = "
	            .pta_directory_table table
				{
				border-collapse:collapse;
				}
				.pta_directory_table table, .pta_directory_table th, .pta_directory_table td
				{
				border: {$size}px solid {$color};
				}
				.pta_directory_table th, .pta_directory_table td
				{
				padding: {$padding}px;
				}";
	    wp_add_inline_style( 'pta_directory-style', $custom_css );
    }
}

function pta_member_directory_shortcode($atts) {
	extract( shortcode_atts( array(
			'location' => '',
			'position'	=> '',
		), $atts ) );
	if ('' != $location) {
		$location = sanitize_title($atts['location']);
	} elseif (isset($_GET['location']) && '' != $_GET['location']) {
		$location = sanitize_title($_GET['location']);
	} else {
		$location = '';
	}
	if ('' != $position) {
		$position = sanitize_title($atts['position']);
	} elseif (isset($_GET['position']) && '' != $_GET['position']) {
		$position = sanitize_title($_GET['position']);
	} else {
		$position = '';
	}
	return pta_display_directory($location, $position);
}

function pta_member_contact_shortcode($atts) {
	extract( shortcode_atts( array(
			'location' => '',
			'position'	=> '',
			'id' => '',
			'hide_select' => false,
		), $atts ) );
	if ('' != $location) {
		$location = sanitize_title($atts['location']);
	} elseif (isset($_GET['location']) && '' != $_GET['location']) {
		$location = sanitize_title($_GET['location']);
	} else {
		$location = '';
	}
	if ('' != $position) {
		$position = sanitize_title($atts['position']);
	} elseif (isset($_GET['position']) && '' != $_GET['position']) {
		$position = sanitize_title($_GET['position']);
	} else {
		$position = '';
	}
	if ('' != $id) {
		$id = sanitize_title($atts['id']);
	} elseif (isset($_GET['id']) && '' != $_GET['id']) {
		$id = sanitize_title($_GET['id']);
	} else {
		$id = '';
	}
	if('true' !== $hide_select) {
		$hide_select = false;
	}
	return pta_directory_contact_form($id, $location, $position, $hide_select);
}

function pta_admin_contact_shortcode() {
	// Set id to -1 to flag for simple admin contact form
	$id = '-1'; 
	return pta_directory_contact_form($id);
}

    /*EOF*/