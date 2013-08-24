<?php

function pta_members_help_tab() {
	$return = '<h4>' . __( 'PTA Member Directory &amp; Contact Form - Members Help' ,'pta-member-directory') . '</h4>';
	$return .= '<p>' . __( "The Member Directory lets you display a directory of organization members, or staff, sorted by positions (such as President, Secretary, etc.).", 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'This plugin creates a custom post type called "member". You enter the full member name (first and last) as the member post title, fill out their contact info, 
		and select which position (or positions) they hold (or enter a new position if it does not exist yet).', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'Email and phone are optional, but if you are using the contact form, there needs to be a valid email for a member before they will show up in the contact form recipient select box.', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'Email and/or phone can be hidden from the public (see options) or displayed in the directory. You can also hide the directory from the public and choose the level a user 
		must be in order to view the directory.', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'Since members are a custom post type, you can use the standard post editor to create a bio or "about" page (post) for each member, along with a featured image which you can also choose to 
		show in the directory listing.', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'Each member can have multiple positions in the directory, as well as multiple locations. Locations are optional, and can be disabled for the plugin, but <strong>if a member doe not have 
		at least one position, they will not show up in the directory.</strong>', 'pta-member-directory') . '</p>';
	return $return;
}

function pta_positions_help_tab() {
	$return = '<h4>' . __( 'PTA Member Directory &amp; Contact Form - Positions Help' ,'pta-member-directory') . '</h4>';
	$return .= '<p>' . __( "You can create new positions on the positions page, or from the add/edit member page. Each member can have more than one position.", 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'Members must be assigned at least one position to show up in the member directory.', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'The directory is listed by position, and you can arrange positions in the order that you want them displayed on the Sort Positions page.', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'You can change the directory heading for "Position" on the options page.', 'pta-member-directory') . '</p>';
	return $return;
}

function pta_locations_help_tab() {
	$return = '<h4>' . __( 'PTA Member Directory &amp; Contact Form - Locations Help' ,'pta-member-directory') . '</h4>';
	$return .= '<p>' . __( "You must first enable locations on the Options page, if you want to assign locations to members. 
		Once Locations are enabled, you will see a Locations page in the Members Directory menu (after you refresh the page).", 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'Members do not need to have a location assigned, but if you display a directory for a specific location, only members that 
		have that location assigned will show up.', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'Locations are specified via either shortcode arguments, or arguments passed in via a link URL. See the Shortcodes help and Custom Links help pages for details.', 'pta-member-directory') . '</p>';
	return $return;
}

function pta_options_help_tab() {
	$return = '<h4>' . __( 'PTA Member Directory &amp; Contact Form - Options Help' ,'pta-member-directory') . '</h4>';
	$return .= '<p>' . __( "Most of these options should be fairly obvious. If you're not sure, change the settings and
		view the results on the directory and/or contact page.", 'pta-member-directory') . '</p>';
	$return .= '<p>'. __("If you wish to hide emails from the public, then check the  
		Use Contact Form box. If you check the box, but do not create a separate page for the contact form, 
		then the contact form is generated dynamically on the same page as the directory.", 'pta-member-directory') . '</p>';
	$return .= '<p>'. __("If you want to have a separate contact form 
		page, put the contact form shortcode on the page where you want the contact form, and then select that page here in the select box 
		that says Contact Form Page. When that is set up properly, and you have Use Contact Form enabled, then when you click on a contact 
		link in the directory, you are taken to the separate contact page and the individual, or group, that was clicked on will be 
		pre-selected in the recipient field of the contact form. With the contact form shortcode on its own page, the contact form can be 
		used independently of the directory page, and will have a drop down list of recipients populated from the members and positions you created. 
		You can choose to show a list of individual members, or positions, or both.", 'pta-member-directory') . '</p>';
	$return .= '<p>'. __("If you want to save all contact form submissions to the database, you can do that with the Contact Form DB plugin. 
		Install that plugin and then check the Enable post to CFDB box, and set a title for your contact form (for display within CFDB only). 
		Note that you do NOT need to have Contact Form 7 installed to use the CFDB plugin, and it may actually cause conflicts with this plugin. 
		There is nothing else you need to do other than install CFDB and check the box here to enable posting to CFDB. Integration is built in.", 'pta-member-directory') . '</p>';
	$return .= '<p>'. __("Locations are optional, and you should leave the Enable Locations box unchecked if you do not wish to use them. 
		Once you enable locations, the next time you refresh any admin page, you will see a Locations page under the Member Directory where you 
		can define all your locations (offices, branch locations, etc.). Locations can be set as arguments in the shortcodes, or passed in as arguments 
		in a link. See the Shortcodes Help and Custom Links sections for more details on how to set those up.", 'pta-member-directory') . '</p>';
	$return .= '<p>'. __("The directory is displayed by positions. Use the Sort Positions page to arrange positions into the order that you want 
		them displayed. For the full directory, you can choose whether or not to show Vacant positions. If you create a directory for a specific 
		location, either via a shortcode argument, or through an argument in a link, any vacant positions for that location will always be skipped, 
		regardless of how you set the options. This is because positions aren't tied to a specific location, and we don't want to show Vacant 
		for a position that is actually filled, but just not at that location.", 'pta-member-directory') . '</p>';
	return $return;
}

function pta_shortcodes_help_tab() {
	$return = '<h4>' . __( 'PTA Member Directory &amp; Contact Form - Shortcodes Help' ,'pta-member-directory') . '</h4>';
	$return .= '<p>' . __( "To display the directory on a page, use the shortcode: [pta_member_directory] ", 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( "To display the contact form on a separate page (instead of being dynamically generated from the directory page), use the shortcode: [pta_member_contact] ", 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'If you have locations enabled, you can show a member directory for a specific location by passing in a location argument in the shortcode. 
		For example, if you have an office in Seattle, pass in the slug version of your Seattle location: [pta_member_directory location="seattle"]', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'The location argument also works with the contact form (whether dynamically generated or on a separate page with the contact form shortcode). 
		If you have locations enabled, and you have a directory set up for a specific location, when you click on a contact link, the location will be passed to the contact form 
		as well, and the contact form will only show other members from that location. However, you can also use the argument within the contact form shortcode 
		in case you want a separate contact form that only shows members from a specific location. The argument is the same: [pta_member_contact location="seattle"]', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'Make sure you use the <strong>slug</strong> for the location argument! Go to the Locations page and view the list of all locations to see the slug for each location.', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'When you set a location with a shortcode argument, the location will be shown at the top of the directory or contact form, using 
		whatever display name you set in the options for "location".', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'Locations can also be passed in as arguments in links instead of hard-coded into a shortcode. See the custom links section for help on creating those links.', 'pta-member-directory') . '</p>';
	return $return;
}

function pta_custom_links_help_tab() {
	$return = '<h4>' . __( 'PTA Member Directory &amp; Contact Form - Custom Links Help' ,'pta-member-directory') . '</h4>';
	$return .= '<p>' . __( 'Once you have your member directory set up and populated with members, and your directory and contact forms set up as desired with the shortcodes, you can use 
		custom links to those pages to pre-select an individual, group, or location.', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'If you want to generate a contact link for an individual, use the "id" argument, and set the id to the ID shown on the All Members admin page. 
		Note that these IDs are the custom post type IDs, and NOT the user ID for any site user accounts.', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'If you have your contact form shortcode set up on a page at "/contact" on your site, and you want to create a contact link for a member with ID 101, 
		the link would be: <br/>
		http://yoursite.com/contact/?id=101', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'If you are NOT using a separate contact form (dynamically generating the form from the directory page), you can still create a contact form link by 
		also passing in the "action" argument, where the action is "contact".  So, if your directory is on page "/directory", the link would be:<br/> 
		http://yoursite.com/directory/?action=contact&id=101', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'Note that you use a ? for the first argument, and any additional arguments are added using &', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'To make a contact link for all members that hold a specific position (message to group), you still use the id argument, but instead of a member id number, 
		you use the slug name for the position. So, if you want to contact all members who have the position of President, the link would look like: <br/>
		http://yoursite.com/contact/?id=president', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'Similarly, you can pass in a location with a link using the "location" argument, and the slug name of the location you want to show. 
		So, to create a link to the directory for only Seattle members, the link would be: <br/>
		http://yoursite.com/directory/?location=seattle', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'All three arguments can be combined in one link. So, if you want to contact all members who are President in the Seattle location, but you are not using the 
		separate contact form, the link would be: <br/>
		http://yoursite.com/directory/?action=contact&id=president&location=seattle', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'The order of the arguments does not matter.', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( 'If you are using the contact form on a separate page with the shortcode, you can still combine the id and location arguments, such as: <br/> 
		http://yoursite.com/contact/?id=president&location=seattle', 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( "If you need a link to a member's bio/about page, just copy the View link from the members list. You can also see the permalink under the member name when you create or edit a member.", 'pta-member-directory') . '</p>';
	
	return $return;
}

function pta_sort_positions_help_tab() {
	$return = '<h4>' . __( 'PTA Member Directory &amp; Contact Form - Sort Positions Help' ,'pta-member-directory') . '</h4>';
	$return .= '<p>' . __( "The member directory is listed by position. To change the order in which positions appear, simply go to the Sort Positions page 
		and drag and drop them in the order that you want them. The position order numbers will change the next time you refresh the page.", 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( "Within each position, members are sorted by Last Name. The program assumes that the last word in the member name is the last name of the member, 
		and uses that for alphabetical sorting.", 'pta-member-directory') . '</p>';
	$return .= '<p>' . __( '<strong>Note: Members will NOT show up in the directory if they do not have at least one position.</strong>', 'pta-member-directory') . '</p>';
	return $return;
}

/* EOF */