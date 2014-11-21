=== PTA Member Directory and Contact Form ===
Contributors: DBAR Productions 
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7U6S4U46CKYPJ
Tags: Staff,Members,Directory,Contact Form
Requires at least: 3.3
Tested up to: 4.0.1
Stable tag: 1.6.2

Create and display a member/staff directory and contact form. Sortable list of staff by position/title. Spambot protected contact form. Many options.

== Description ==

**PLEASE DO NOT USE THE SUPPORT FORUM FOR FEATURE REQUESTS!!**
You may submit and vote for new features here:
https://stephensherrardplugins.com/support/forum/feature-requests/

This plugin lets you create a custom member (or staff) directory, along with an associated contact form, for your organization.  Create as many positions and members (staff) as you like.  Each person can have multiple positions, and each position can have multiple members (staff).

You can also enable and add locations, allowing you to set up different directory listings and contact forms for multiple locations or offices. Members can belong to more than one location.  Locations can be set with either shortcode arguments or URL arguments from links.

The directory list is displayed by position, and positions can be sorted on the admin options page with a simple drag and drop interface.

The contact form can be set up to select an individual, a position, or both.  If a position is selected, all members who hold that position (and who have a valid email) will be emailed the message.  Vacant positions, and individuals without a valid email, will not be shown on the contact form.

There is built-in integration with the Contact Form DB plugin so any form submissions (that pass validation and spambot check) will be saved to the database via the CFDB plugin.

Detailed Help tabs for all admin screens for the plugin

Version 1.4 adds a new role "PTA Manager" which has all the same capabilities as the Wordpress "Editor" role, but also adds capabilities to manage this and other PTA plugins settings. This allows you to give control of the settings of this plugin to somebody else in your organization without giving them full admin level access.

The directory has a variety of options for customization:

*   Choose the heading to display for "Position" in the directory (e.g., you can choose to show "Title" instead of "Position")
*	Choose the heading to display for "Location" in the directory and contact form (e.g., "Office", "City", "Branch", etc.)
*   Contact form contact select drop down can be configured to display individuals, positions, or both.  If you choose "both", there are nice headers to separate positions and individuals.    
*   You can choose to show full names, only first names, or nothing, after positions on the contact form select box, which will show for each person that holds that position
*   You can also choose to show the positions a person holds after their name when showing individuals on the contact form.
*	You can also choose to show locations after each person's name on the contact form
*   Use the shortcode to put the directory on any page and dynamically generate a contact form.  Or, use a separate shortcode for the contact form so it can also be used independently of the directory (the directory will then use that page for the contact form)
*   Choose to show or hide phone numbers in the directory
*   Choose to show emails in directory OR a link to the contact form (which then passes the position or individual to the contact form, so the recipient selection is already made)
*   Choose to hide the directory from the public, so that only logged in users can see it.  You can also choose the minimum user level to view the directory (subscriber or contributor)
*   Type in your own message to display on screen after the contact form has been submitted (HTML formatting allowed)
*   Choose to diplay images in the directory, and pick the size of the images.  If you display images, then the images will link to the individual member/post page.  Members are a custom post type, and the full editor for them is enabled, so you can create a bio page for each member.  The Featured Image is the photo that will be displayed in the directory, when photos are enabled.
*	Enable or disable Contact Form DB integration, and choose the form title to post to CFDB.

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.
== Frequently Asked Questions ==

**Can this plugin do (insert a feature request here)?**
**Can you add (insert a feature request here)?**

PLEASE DO NOT USE THE SUPPORT FORUM FOR FEATURE REQUESTS!!

This plugin has a lot of options and features, but I have been getting overwhelmed with feature requests recently. This plugin already does MUCH more than I originally intended, and more than we needed for our own school PTA web site. I have created some extensions that I thought would be helpful to the largest number of people, which you can find at:
https://stephensherrardplugins.com

PLEASE USE THE FEATURE REQUEST FORUM TO REQUEST NEW FEATURES!!
https://stephensherrardplugins.com/support/forum/feature-requests/

**Is there any documentation or help on how to use this?**

A help tab has been added to the Member Directory admin section. Click on the Help tab in the upper right to show the help tab. There are sections covering each Member Directory admin screen, as well as how to set up shortcodes and custom links for both the directory and the contact form.

**I'm getting a Wordpress Mail Error when submitting the contact form, or emails are not getting delivered**

This is a server issue and has nothing to do with the contact form or this plugin. Version 1.2 added the Wordpress Mail Error message to let you know that the built-in Wordpress mailer (wp\_mail) returned an error. This means that your server is not compatible with the built-in Wordpress mail function. You can use one of the many SMTP mail plugins, that change the Wordpress Mail function to use SMTP for sending emails. Since this plugin uses Wordpress Mail, any of those plugins that modify Wordpress Mail to use SMTP should work with this plugin to fix your mail errors. Note that some of those SMTP plugins will change the reply-to address to equal the from address instead of the address of the person filling out the contact form. I have created my own PTA SMTP Mailer plugin which is a modified version of Easy WP SMTP that does not change the reply-to address, and also has improved settings and built-in help tabs. You can download the PTA SMTP Mailer plugin for free at:
https://stephensherrardplugins.com

**How do I display the directory on a page?**

Place the shortcode [pta\_member\_directory] on the page where you want the directory. You can use shortcode arguments to specify a location or position. You can download a free extension to automatically generate the shortcodes for the directory and contact form, along with properly formatted arguments for location or position, from my plugins site at:
https://stephensherrardplugins.com

**How do I use the contact form?**

If you want to use the contact form instead of displaying emails in the directory, make sure the "Use Contact Form?" option is checked. This will replace all email addresses in the directory with a "Send A Message" link.  Clicking on that link will automatically generate the contact form on the same page, with the recipient already selected.

However, you can also use the shortcode [pta\_member\_contact] to put the Contact Form on its own separate page.  This will allow you to use the contact form independently of the directory.  If you then select the page with your contact form on the options page, when you click on "Send A Message" in the directory, the link will take the user to the contact form page with the recipient field already selected.

There is also a new shortcode to create a simple admin contact form without the recipient select box. Just use the shortcode [pta\_admin\_contact] . All messages sent from that form will go to the site\'s admin email. You can also force a simple admin contact form by passing in -1 as the id in a contact form link, such as: http://yoursite.com/your_contact_form/?id=-1

Additional shortcode arguments for the contact form have been added in version 1.5 to allow you to specify an ID for a specific member as well as to hide the recipient select box, so that you can hard-code contact forms for specific members. For example, if you enter content (such as bio) for a member and set the directory to link to member posts, you could embed a contact form shortcode for each member on their own post so visitors can contact them directly from the bio pages without having to select a recipient.

Simplify the shortcode generation by downloading the free extension, PTA Shortcodes, from my plugin site at:
https://stephensherrardplugins.com

**Can I add more fields to the contact form? Or use a different contact form?**

At this time there are no built-in options for adding additional fields to the contact form. However, there is a Gravity Forms extension available which will allow you to create your own contact form with the very powerful Gravity Forms. The extension will prepopulate a recipient select box in your Gravity Forms from existing members and positions, pre-select one of those recipients based on links/arguments passed to it, and will alter the notification email to address to make sure the message gets sent to the correct recipient. The Gravity Forms extension is available at: https://stephensherrardplugins.com

**Is there any spam protection?  There is no captcha field?**

I'm not a fan of captcha as I often can't even read them myself, and it makes setup a bit more complicated since you need to obtain and enter a key for a captcha service.  Instead, I used the honeypot method of spam protection.  There is a hidden spambot field that normal visitors won't see, but spambots will fill in.  Any form submission that has that spambot field filled in will be rejected.

**How can I change the text that appears in the member directory or contact form?**

As of version 1.3.6 a filter hook has been added for almost all text that is output to the public side of the member directory and contact form. There is some information in the help tab in the admin section on how to use this, and there are now 2 files in the main directory of this plugin, output-filters.php and output-filters.txt, that provide all the information and sample code needed to modify any of the text strings.  If you are not comfortable with PHP or adding code to your theme\'s functions.php file, there is a very simple Customizer add-on extension plug-in available at https://stephensherrardplugins.com

**How do I make a contact link for an individual or group on other pages of my site?**

Just create a link to your contact form page (the page with the [pta\_member\_contact] shortcode), and include an argument for the id of the individual or group you want to be pre-selected on the contact form.  For example, to link to an individual number, you set the id equal to the member directory ID of the member, which you can see in your list of all members.  If the ID is 101, then your link should look like:
http://yoursite.com/your_contact_form/?id=101
If you want to select a position to contact all members who hold that position, use the slug version of the position.  You can see the slug for each position from the list of Positions on the admin side.  For example, if you want the contact form to be pre-selected for the position of President (slug would be simply president), your link would look like:
http://yoursite.com/your_contact_form/?id=president

**How do I show a directory for a specific location?**

If you want a directory for a specific location on its own page, add the location argument to the directory shortcode, and use the slug version of the location you want to show. For example:
[pta\_member\_directory location="seattle"]
If you want a single directory page that you can use to show all locations, but also can show a specific location, use the regular directory shortcode without the location argument. If someone goes directly to that page, they will see the full directory for all locations. But, if you set up links to that page with location arguments in the URLs, you can show a specific location. You could, for example, set up navigation menu items for each location, all going to the same directory page, but with different location argument. For example, if you want to show a directory for Seattle, the link would be something like:
http://yoursite.com/your_directory_page/?location=seattle

**Can I add custom fields to the directory?**

Hooks and filters are in place to allow the creation of custom fields. This requires a significant amount of programming and knowledge of how to create inputs and save data from custom meta boxes on the admin post editor page for the custom post type.  I have created an add-on Custom Fields plugin extension for this that allows you to create any number of custom fields, with 4 different field types (text, link, file, textarea), and 3 output locations for these fields (after name, after email, and after the last column).  You can have more than one custom field in each location as well, so you are only limited by how many fields you want to try to fit into the directory table.  This plugin extension is available at my new site: 
https://stephensherrardplugins.com

Version 1.5.1 also now supports the Descriptions extension, which will grab any content you put into the description box for each Position, and either display it as a new column in the directory, or put a tooltip icon with pop-up description dialog next to position names that have description content.  Our school PTA wanted to list job description and responsibilities for each position to help people decide what to volunteer for in the new school year, so I put created this extension, which can be found at:
https://stephensherrardplugins.com

== Screenshots ==

1. Add New Member - Admin
1. Options Page part1 - Admin
1. Options Page part2 - Admin
1. Member Directory - Public Side
1. Contact Form - Public Side

== Changelog ==
**Version 1.6.2**

*	Due to continuing conflicts for the limited number of custom post type menu positions in the normal location (has to be an integer), I have changed the menu position yet again to a large, and hopefully not used by any other custom post type, menu position number. Wordpress needs to fix this issue in the core code so menus items don't disappear when two of them have the same menu position! In the meantime, the Member Directory admin menu will move much further down the list.
*	Added a div wrapper around the member directory table with class: ptamd_directory_table and added classes to the h3 headers for position or location that show up when you specifiy a position or location in the shortcode. Those h3 classes are: ptamd_position_header and ptamd_location_header . This allows you to target elements of the directory table with your own CSS to modify the appearance.
*	If you're using a responsive theme that doesn't handle tables well on small screens, you can create your own CSS styles to restyle the Member Directory table using the updated CSS classes and the new div container. If that doesn't work for you, I have created a simple and lightweight plugin that simply loads the jQuery Stacktable plugin from John Polacek and applies that to ALL tables on the public side of your site (it automatically stacks table columns on smaller screens). You can download that free from my plugins site.

**Version 1.6.1**

*	Minor change to admin menu position to avoid conflict with another plugin that specified the same position.

**Version 1.6**

*	Adds a transient time and IP check to contact form processing to prevent multiple submissions of the contact form due to the user hitting refresh and re-submitting the form, or from theme & other plugin issues tha may cause the contact form to be processed more than once.
*	Tested for compatilbility with Wordpress version 4.0

**Version 1.5.3**

*	Minor change to the way contact links for individuals are generated to work better with certain permalink structures.

**Version 1.5.2**

*	Adds some additional logic checking to the member directory contact links. If the contact form is set to show only positions, but you click on an individual member "send message" link, we need to change the id argument to be the position instead of the individual id, otherwise the contact form won't work properly since it's expecting position name slugs instead of numerical member ids in this case.
*	Adds a check to make sure PTA Manager role is added and Admin has manage_pta capability if plugin was upgraded without the activation function being triggered. If the role and capabilitiy wasn't added, the options and sort positions pages for the plugin may not appear unless you deactivate and reactivate the plugin.
*	Supports the Descriptions extension available at https://stephensherrardplugins.com

**Version 1.5.1**

*	Minor filter hooks change for upcoming extensions compatibility

**Version 1.5**

*	Allow id argument for contact form shortcode, so you can create a contact form for a specific member on their own page or post (can be used in each member's post content area)
*	Added a "hide_select" argument to the contact form shortcode that will not show the recipient select box if set to "true" AND if an id argument is set in the shortcode or passed in via a link.
*	Additional hooks added for extensions

**Version 1.4.0**

*	Add PTA Manager Role - same as Editor role but with added capabilities to manage PTA plugins
*	Additional hooks added for extensions

**Version 1.3.9**

*	Readme.txt cleanup. No functional changes after 1.3.8

**Version 1.3.8**

*	Added wp_reset_postdata() to end of directory display loop to fix extra post display issues with some themes.

**Version 1.3.7**

*	Minor Action/Filter hook updates for extensions

**Version 1.3.7**

*	Minor Action/Filter hook updates for extensions ( extension available at http://stephensherrardplugins.com )
*	Added Dutch translation by Remco Spil

**Version 1.3.6**

*	Added option to show/hide "more info..." link in photo column for members with post content
*	Added option to link member name to member post page if member has post content
*	Added filter hooks for all text that is output to the public side for the Directory and Contact Form, to make it easy to modify displayed text without using a translation plugin or editing the plugin files.  See FAQ or Help Tabs for details.  Customizer add-on to make modification of text super simple is available as an extension!
*	Help tab update
*	Include translation file that was missing from last update

**Version 1.3.5**

*	Fixes for translation
*	Fix to not display warning on contact form if no positions have been created yet and Debug is on
*	Includes French translation by Dan bp-fr.net

**Version 1.3.4**

*	Bug fix for compatibility with Wordpress versions older than 3.5 (still need at least 3.3, though). Only affects Wordpress versions before 3.5 (you really should upgrade!).

**Version 1.3.3**

*	Added shortcode argument for position for both directory and contact form shortcodes. Allows you to show a directory list for only the specified position, or a contact form that only sends email to the position as a group recipient
*	Small fix for compatibility with Wordpress versions older than 3.5 (still need at least 3.3, though).
*	Additional hooks and filters for plugin extensions

**Version 1.3.2**

*	Added on option to turn on or off adding the blog title to the subject line for the emails sent by the contact form.
*	Added/improved hooks and filters for extension by other plugins
*	Relaxed shortcode argument handling - can enter either the Title or slug for location arguments in shortcodes
*	Added sender name, sender email, and sender IP info to contact form message body. Useful if you are using another SMTP mail plugin which might overwrite our header info, thus setting the from and reply-to address to your admin mail instead of the sender's name and email
*	Fixed directory display issue where it was limiting the number of members shown for each position to the max number of posts you had set per page on the Wordpress Reading settings page.

**Version 1.3.1**

*	Important fix for the new lastname logic. Found situations where the lastname meta field could get wiped out, and the member would not show at all in the directory. This patch fixes that, but you will need to edit each affected member and hit update (don't actually have to change anything) to force it to update the member post meta info.

**Version 1.3**

*	Added a new option to show/hide the Send Group A Message text link on the directory.
*	Added some additional logic to try to figure out the last name from the member name (post title) in the cases where people use a suffix after a comma, or even if you enter the last name first and then a comma and first name. It will now ignore any characters after the first comma it finds, and then pick the last word in the string from anything before that comma (assuming words are separated by spaces). See the Help dropdown from any Member Directory Admin page for more.
*	Organized the options page better now that we have quite a few options

**Version 1.2**

*	Added a new shortcode to generate a simple admin contact form. Use [pta\_admin\_contact] to generate a simple contact form without the recipient checkbox. This contact form will send an email to the site admin email address.
*	Added error checking to wp_mail function for sending the contact form email. If you get a Wordpress Mail Error message, you will know that there is an issue with your server mail settings. If there is no error, but emails are still not being delivered (check junk/spam folders) you may want to install a plugin like Easy WP SMTP to allow Wordpress to send mail via SMTP instead of the PHP mail function.
*	If there are no positions or members in the directory, output a message on the directory page instead of the table headers with an empty table
*	Other minor code improvements

**Version 1.1**

*	New option to force table borders (plus a little padding) in the directory for themes that don't show borders by default (and for users who don't want to edit the theme CSS styles themselves). Border color and size, plus cell padding, can also be set.
*	Changed contact form CSS class from "contact-form" to "pta-contact-form" to avoid any conflicts with other contact forms
*	Minor error checking fix on contact form
*	Strip any slashes from sanitized form fields before sending emails
*	Added filter and action hooks for extensibility

**Version 1.0.2**

*	Fixed wrong include path for help tabs

**Version 1.0.1**

*	Help tabs file didn't get uploaded with 1.0.

**Version 1.0**

*	Added Locations to allow showing directories or contact form for a specific location, office, branch, etc. 
*	Locations can be set with arguments in the shortcodes to set up fixed directory/contact pages for each location, or locations can be passed in as an argument in a link.
*	Added an option to enable/disable showing of Vacant positions
*	Added detailed help tabs to admin side pages

**Version 0.7**

*	Fixed syntax on load_plugin_textdomain for proper translation setup

**Version 0.6**

*	Added option to show full name or only first names after positions on contact form
*	Some extra escaping on form POST data

**Version 0.5**
*	Minor housekeeping and readme.txt cleanup - no functional changes

**Version 0.4**

*	Added integration with Contact Form DB plugin
*	Updated the option for specifying the contact page to be a simple drop down selector of existing pages
*	Added a bit of CSS to fix directory table layout on some themes
*	Modified default options checking on activation to account for new options added in upgrades
*	Added code to make sure themes allow thumbnails and featured images for the member custom post type

**Version 0.3**

First public release