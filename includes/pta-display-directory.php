<?php

function pta_display_directory() {
	$categories = get_option( 'pta_member_categories' );  // Ordered list of categories to display
	$options = get_option( 'pta_directory_options' ); // Display Options
	// First, check to see if option is set to hide the display from the public
	if (isset($options['hide_from_public']) && true === $options['hide_from_public']) {
		// If not logged in, then return with a message
		if (!is_user_logged_in()) {
			$return = '<p><strong>'.__('Sorry!  You must be logged in to view the directory.', 'pta-member-directory').'</strong></p>';
			return $return;
		} elseif (!current_user_can( $options['capability'] )) {
			$return = '<p><strong>' . __("Sorry!  You don't have the proper user level to view the directory.", 'pta-member-directory') .'</strong></p>';
		}
	}
	if ( isset($_POST['contact_mode']) && 'submitted' == $_POST['contact_mode'] ) {
		$id = (int)($_POST['id']);
		$return = pta_directory_contact_form($id);
		return $return;
	}
	if(isset($_REQUEST['action']) && 'contact' == $_REQUEST['action'] ) {
		$id = $_REQUEST['id'];
		$return = pta_directory_contact_form($id);
		return $return;
	}
	// Set up table text for translation
	$column_position = $options['position_label']; // Get this from the options settings
	$column_name = __('Name', 'pta-member-directory');
	$column_phone = __('Phone', 'pta-member-directory');
	$column_email = __('Email', 'pta-member-directory');
	$vacant = __('VACANT', 'pta-member-directory');
	$send_message = __('Send A Message', 'pta-member-directory');
	$group_message = __('Send group a message', 'pta-member-directory');
	$more_info = __('more info...', 'pta-member-directory');
	$cols = 2; // used to determine colspan for vacant positions
	$return = '
	<table class="pta_directory_table">
	        <thead>
	            <tr>
	                <th>'.$column_position.'</th>
	                <th>'.$column_name.'</th>';
                if($options['show_phone']) {
                	$return .= '<th>'.$column_phone.'</th>';
                	$cols++; // add one to our colspan
                }
              	$return .= '  
	                <th>'.$column_email.'</th>
	            </th>';
	            if($options['show_photo']) {
                	$return .= '<th>&nbsp;</th>';
                	$cols++; // add one to our colspan
                }
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
	        $mypost = array( 'post_type' => 'member', 'member_category' => $slug, 'meta_key' => '_pta_member_directory_lastname', 'orderby' => 'meta_value', 'order' => 'ASC' );
	        $loop = new WP_Query( $mypost );
	        $count = $loop->post_count;
	        if ( 0 == $count ) {
	            $return .= '<tr><td><strong>'.$category.'</strong></td>';
	            $return .= '<td colspan="'.$cols.'">'.$vacant.'</td></tr>';
	        } else {
	        	// Do we already have a contact page setup with the contact form shortcode?
	            // if so, get the link and add the id argument for the group
	            if ( isset($options['contact_page_id']) && 0 != $options['contact_page_id'] ) {
	            	$contact_url = get_permalink($options['contact_page_id']) .'?id='.$slug;
	            } else {
	            	$contact_url = add_query_arg( array ( 'action' => 'contact', 'id' => $slug ));
	            }
	            // Add group message link if there is more than one person for the position
	            $return .= '<tr><td rowspan="'.$count.'" style="vertical-align: middle;"><strong>'.$category.'</strong>';
	            if (1 == $count) {
	                $return .= '</td>';
	            } else {
	                $return .= ' <br><br><a href="'.$contact_url.'">'.$group_message.'</a></td>';
	            }
	        }
	        $i=0;
	        while ( $loop->have_posts() ) : $loop->the_post();
	        	$id = get_the_ID();
	            $link = get_permalink( $id );
		        if (isset($options['use_contact_form']) && true === $options['use_contact_form']) {	            
		            // Do we already have a contact page setup with the contact form shortcode?
		            // if so, get the link and add the id argument
		            if ( isset($options['contact_page_id']) && 0 != $options['contact_page_id'] ) {
		            	$contact_url = get_permalink($options['contact_page_id']) .'?id='.$id;
		            } else {
		            	$contact_url = add_query_arg( array ( 'action' => 'contact', 'id' => $id ));
		            }
		            $email = '<a href="'.$contact_url.'">'.$send_message.'</a>';
		        } else { // display the email with mailto link
		            $mail = esc_html( get_post_meta( get_the_ID(), '_pta_member_directory_email', true ) );
	            	$email = '<a href="mailto:'.$mail.'">'.$mail.'</a>';
		        }
		        if (!is_email( get_post_meta( $id, '_pta_member_directory_email', true ) ) ) {
		        	$email = ''; // if they don't have an email, don't show anything
				}
		        $name = get_the_title(get_the_ID());
		        $phone = esc_html( get_post_meta( get_the_ID(), '_pta_member_directory_phone', true ) );
	            if($i>0) {
	                $return .= '<tr>';
	            }
	            $return .= '
	                <td style="vertical-align: middle;">'. $name.'</td>';
	            if($options['show_phone']) {
	                $return .= '<td style="vertical-align: middle;">'. $phone .'</td>';
	            }
	            $return .= '
	                <td style="vertical-align: middle;">'. $email .'</td>';
	            if( $options['show_photo'] ) {
					$return .= '<td>';
	            	if ( has_post_thumbnail($id)) {
	            		$size[] = $options['photo_size_x'];
	            		$size[] = $options['photo_size_y'];
	            		$image = get_the_post_thumbnail($id, $size);
	            		$return .= '<a href="'.$link.'">'. $image .'</a>';
	            	} else {
	            		$return .= '&nbsp;';
	            	}

	            	$cc = get_the_content();
	            	if($cc != '') {
	            	     $return .= '<br><a href="'.$link.'">'.$more_info.'</a>';
	            	}
					$return .= '</td>';
	            }
	            $return .= '
	            </tr>';
	        	$i++;
	        endwhile; 
	    } 
		 $return .='     
	    </tbody>
	</table>
	';
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

function pta_directory_contact_form($id='') {
	// check if they selected a recipient from the drop down select box, and update the id for proper name/email
	$selected = false;
	$group = false;
	$categories = get_option( 'pta_member_categories' ); // use this more than once, so put it up top
	$options = get_option( 'pta_directory_options' ); // Display Options
	$cc_mail = array(); // reset our CC mail list
	if (isset($_POST['recipient']) && '' != $_POST['recipient']) {
		if (is_numeric($_POST['recipient'])) { // if it's a number, they selected a single member to contact
			$id = (int)$_POST['recipient'];
		} else { // if it's not a number, they selected a position/role to send to
			$id = false; // make sure this is unset
			$group = sanitize_text_field( $_POST['recipient'] ); // set group to the position category slug
		}		
	} elseif (isset($_REQUEST['id']) && '' != $_REQUEST['id'] && !$id) {
		// check if we got a member id or a group name(slug) passed in
		if (is_numeric($_REQUEST['id'])) {
			$id = (int)$_REQUEST['id'];
		} else { // name or other characters passed in
			if (in_array($_REQUEST['id'], $categories)) { // Make sure it's one of our category slugs
				$group = sanitize_text_field( $_REQUEST['id'] );
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
	if ($id && $post=get_post((int)$id)) { // Make sure there is an entry with the given id
		// grab the name and email of the pta directory member we want to contact
		$email = esc_html( get_post_meta( $id, '_pta_member_directory_email', true ) );
		$name = $post->post_title;
		$label_send_message = __('Send a message to: ', 'pta-member-directory') . $name;
		$label_recipient = __('Or, select a different recipient:', 'pta-member-directory');
		$selected = true; // recipient selected
	} else {
		if ($group) { // $group recipient selected, so get posts with that taxonomy
			$args = array( 'post_type' => 'member', 'member_category' => $group );
			$members = get_posts( $args );
			$count = 1;
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
			$label_recipient = __('Or select a different recipient:', 'pta-member-directory');
			$label_send_message = __('Send a message to: ', 'pta-member-directory') . $display_name;
			if (is_email( $email )) {
				$selected = true; // only set to true if we have a valid email
			} else {
				// Nobody in the group has a valid email, so let's change the message shown above the form
				$label_recipient = __('Please select a different recipient:', 'pta-member-directory');
				$label_send_message = __('There are currently no contact emails for group: ', 'pta-member-directory') . $display_name;
			}
			
		} else {
			// if no $id or $group given, set the message to ask them to select a recipient, set flag to false
			$email = '';
			$name = '';
			$label_recipient = __('Please select a recipient:', 'pta-member-directory');
			$label_send_message = __('Send a message: ', 'pta-member-directory');
			$selected = false; // no recipient selected yet
		}
	}	
    $subject = "";
    $label_name = __("Your Name", 'pta-member-directory');
    $label_email = __("Your E-mail Address", 'pta-member-directory');
    $label_subject = __("Subject", 'pta-member-directory');
    $label_message = __("Your Message", 'pta-member-directory');
    $label_submit = __("Submit", 'pta-member-directory');
    $label_option = __('Select a recipient', 'pta-member-directory');
    // the error message when at least one of the required fields are empty:
    $error_empty = __("Please fill in all the required fields.", 'pta-member-directory');
    // the error message when the e-mail address is not valid:
    $error_noemail = __("Please enter a valid e-mail address.", 'pta-member-directory');
    $error_email = __("That recipient doesn't currently have a valid email address.  Please choose another recipient.", 'pta-member-directory');
    $error_nonce = __("Invalid Referrer!", 'pta-member-directory');
    $error_bot = __("Spambot!", 'pta-member-directory');
    $error_recipient = __("No recipient selected.  Please select one.", 'pta-member-directory');
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
	        $form_data[$field] = sanitize_text_field( strip_tags( $value ) ); // get rid of any bad stuff
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
	    	if (isset($email) && !is_email( $email)) {
	    		$result =$error_email;
	    	} else {
	    		$result = $error_recipient;
	    	}
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

	    // but if $error is still FALSE, put together the POSTed variables and send the e-mail!
	    if ( $error == false ) {
	        // get the website's name and puts it in front of the subject
	        $email_subject = "[" . get_bloginfo( 'name' ) . "] " . $form_data['subject'];
	        // get the message from the form and add the IP address of the user below it
	        $email_message = $form_data['message'] . "\n\nIP: " . pta_directory_get_the_ip();
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
	        // send the e-mail with the shortcode attribute named 'email' and the POSTed data
	        wp_mail( $email, $email_subject, $email_message, $headers );
	        // and set the result text to the success message set in the options
	        $result = wp_kses_post(stripslashes($options['contact_message']));
	        // ...and switch the $sent variable to TRUE
	        $sent = true;

	        // If enabled, post message to CFDB plugin
	        ;
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
	        
	    }
	}

	// if there's no $result text (meaning there's no error or success, meaning the user just opened the page and did nothing) there's no need to show the $info variable
	if ( $result != "" ) {
	    $info = '<div class="info">' . $result . '</div>';
	}
	// anyways, let's build the form!
	
	$email_form = '<h3>'.$label_send_message.'</h3>
	<form class="contact-form" method="post" action="' . get_permalink() . '">
		<input type="hidden" name="form_title" value="'.$options["form_title"].'"/>
		<div>
			<label for="cf_recipient">'.$label_recipient.'</label>
			<select name="recipient" id="cf_recipient">
				<option value="">'.$label_option.'</option>';
			$members = get_posts(  array(
				'numberposts'		=> -1,
				'orderby'			=>	'meta_value',
				'order'				=>	'ASC',
				'meta_key'			=>	'_pta_member_directory_lastname',
				'post_type'			=>	'member',
				'post_status'		=>	'publish' )
			);
			if( 'positions' == $options['contact_display'] || 'both' == $options['contact_display'] ) {
				// Create list of positions/categories for multi-recipient mail
				if ('both' == $options['contact_display']) {
					$email_form .= '<optgroup label="'. $options['position_label'].'">';
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
			        $first_names = ''; // Use this to create a list of first names of people in each position
			        $name_count = 0;
			        foreach ($members as $member) {
			        	if(has_term( $category, 'member_category', $member )) {
			        		$member_email = get_post_meta( $member->ID, '_pta_member_directory_email', true );
			        		if (!is_email($member_email)) continue; // Don't list the member if they have no valid email
			        		$email_exists = true; // got an email
			        		if(0 == $name_count) {
			        			$first_names .= ' - '; // put a separator before the first name in the list
			        		} else {
			        			$first_names .= ' | '; // separate names with a pipe
			        		}
			        		$name_arr = explode(' ',trim($member->post_title));
			        		$first_names .= esc_attr($name_arr[0]); // put just the first name in there
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
					// Show first names after position name if the option is set
					if ($options['show_contact_names']) {
						$email_form .= $first_names;
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
					$email_form .= '
					<option value="'.$member->ID.'" ';
					if(isset($id) && $member->ID == $id) {
						$email_form .= 'selected="selected"';
					}
					$email_form .= '>'.$member->post_title;
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
		</div>
	    <div>
	        <label for="cf_name">' . $label_name . ':</label>
	        <input type="text" name="your_name" id="cf_name" size="50" maxlength="50" value="' . 
	        	( isset($form_data['your_name']) ? $form_data['your_name'] : "" ) . '" />
	    </div>
	    <div>
	        <label for="cf_email">' . $label_email . ':</label>
	        <input type="text" name="email" id="cf_email" size="50" maxlength="50" value="' .
	        	 ( isset($form_data['email']) ? $form_data['email'] : "" ) . '" />
	    </div>
	    <div>
	        <label for="cf_subject">' . $label_subject . ':</label>
	        <input type="text" name="subject" id="cf_subject" size="50" maxlength="50" value="' . $subject . 
	        	( isset($form_data['subject']) ? $form_data['subject'] : "" ) . '" />
	    </div>
	    <div>
	        <label for="cf_message">' . $label_message . ':</label>
	        <textarea name="message" id="cf_message" cols="50" rows="15">' . 
	        	( isset($form_data['message']) ? $form_data['message'] : "" ) . '</textarea>
	    </div>
	    <div style="visibility:hidden"> 
			<input name="contactbot" type="text"size="20"  >
	    </div>
	    <div>
	    	'.wp_nonce_field("pta_directory_contact_form", "pta_directory_contact_form_nonce").'
	    	<input type="hidden" name="contact_mode" value="submitted" />
	        <input type="submit" value="' . $label_submit . '" name="send" id="cf_send" />
	        <input type="hidden" value="'.$id.'" name="id" />
	    </div>
	</form>';

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
}

function pta_member_directory_shortcode() {
	return pta_display_directory();
}

function pta_member_contact_shortcode() {
	return pta_directory_contact_form();
}

    /*EOF*/