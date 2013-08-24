=== PTA Member Directory and Contact Form ===
Contributors: DBAR Productions 
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7U6S4U46CKYPJ
Tags: Staff,Members,Directory,Contact Form
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.0

Create and display a member/staff directory and contact form. Sortable list of staff by position/title. Spambot protected contact form. Many options.

== Description ==

This plugin lets you create a custom member (or staff) directory, along with an associated contact form, for your organization.  Create as many positions and members (staff) as you like.  Each person can have multiple positions, and each position can have multiple members (staff).

NEW in version 1.0, can also enable and add locations, allowing you to set up different directory listings and contact forms for multiple locations or offices. Members can belong to more than one location.  Locations can be set with either shortcode arguments or URL arguments from links.

The directory list is displayed by position, and positions can be sorted on the admin options page with a simple drag and drop interface.

The contact form can be set up to select an individual, a position, or both.  If a position is selected, all members who hold that position (and who have a valid email) will be emailed the message.  Vacant positions, and individuals without a valid email, will not be shown on the contact form.

There is built-in integration with the Contact Form DB plugin so any form submissions (that pass validation and spambot check) will be saved to the database via the CFDB plugin.

Detailed Help tabs for all admin screens for the plugin

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

**Is there any documentation or help on how to use this?**

A help tab has been added to the Member Directory admin section. Click on the Help tab in the upper right to show the help tab. There are sections covering each Member Directory admin screen, as well as how to set up shortcodes and custom links for both the directory and the contact form.

**How do I display the directory on a page?**

Place the shortcode [pta\_member\_directory] on the page where you want the directory.

**How do I use the contact form?**

If you want to use the contact form instead of displaying emails in the directory, make sure the "Use Contact Form?" option is checked. This will replace all email addresses in the directory with a "Send A Message" link.  Clicking on that link will automatically generate the contact form on the same page, with the recipient already selected.

However, you can also use the shortcode [pta\_member\_contact] to put the Contact Form on its own separate page.  This will allow you to use the contact form independently of the directory.  If you then select the page with your contact form on the options page, when you click on "Send A Message" in the directory, the link will take the user to the contact form page with the recipient field already selected.

**Is there any spam protection?  There is no captcha field?**

I'm not a fan of captcha as I often can\'t even read them myself, and it makes setup a bit more complicated since you need to obtain and enter a key for a captcha service.  Instead, I used the honeypot method of spam protection.  There is a hidden spambot field that normal visitors won't see, but spambots will fill in.  Any form submission that has that spambot field filled in will be rejected.

**How do I make a contact link for an individual or group on other pages of my site?**

Just create a link to your contact form page (the page with the [pta\_member\_contact] shortcode), and include an argument for the id of the individual or group you want to be pre-selected on the contact form.  For example, to link to an individual number, you set the id equal to the member directory ID of the member, which you can see in your list of all members.  If the ID is 101, then your like should look like:
http://yoursite.com/your_contact_form/?id=101
If you want to select a position to contact all members who hold that position, use the slug version of the position.  You can see the slug for each position from the list of Positions on the admin side.  For example, if you want the contact form to be pre-selected for the position of President (slug would be simply president), your link would look like:
http://yoursite.com/your_contact_form/?id=president

**How do I show a directory for a specific location?**

If you want a directory for a specific location on its own page, add the location argument to the directory shortcode, and use the slug version of the location you want to show. For example:
[pta\_member\_directory location="seattle"]
If you want a single directory page that you can use to show all locations, but also can show a specific location, use the regular directory shortcode without the location argument. If someone goes directly to that page, they will see the full directory for all locations. But, if you set up links to that page with location arguments in the URLs, you can show a specific location. You could, for example, set up navigation menu items for each location, all going to the same directory page, but with different location argument. For example, if you want to show a directory for Seattle, the link would be something like:
http://yoursite.com/your_directory_page/?location=seattle

== Screenshots ==

1. Add New Member - Admin
1. Options Page - Admin
1. Member Directory - Public Side
1. Contact Form - Public Side

== Changelog ==
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