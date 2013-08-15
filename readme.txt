=== PTA Member Directory and Contact Form ===
Contributors: DBAR Productions 
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7U6S4U46CKYPJ
Tags: Staff,Members,Directory,Contact Form
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 0.6

Create and display a member/staff directory and contact form. Sortable list of staff by position/title. Spambot protected contact form. Many options.

== Description ==

This plugin lets you create a custom member (or staff) directory, along with an associated contact form, for your organization.  Create as many positions and members (staff) as you like.  Each person can have multiple positions, and each position can have multiple members (staff).

The directory list is displayed by position, and positions can be sorted on the admin options page with a simple drag and drop interface.

The contact form can be set up to select an individual, a position, or both.  If a position is selected, all members who hold that position (and who have a valid email) will be emailed the message.  Vacant positions, and individuals without a valid email, will not be shown on the contact form.

There is built-in integration with the Contact Form DB plugin so any form submissions (that pass validation and spambot check) will be saved to the database via the CFDB plugin.

The directory has a variety of options for customization:

*   Choose the heading to display for "Position" in the directory (e.g., you can choose to show "Title" instead of "Position")
*   Contact form contact select drop down can be configured to display individuals, positions, or both.  If you choose "both", there are nice headers to separate positions and individuals.    
*   You can choose to show first names after positions on the contact form select box, which will show the first name for each person that holds that position
*   You can also choose to show the positions a person holds after their name when showing individuals on the contact form.
*   Use the shortcode to put the directory on any page and dynamically generate a contact form.  Or, use a separate shortcode for the contact form so it can also be used independently of the directory (the directory will then use that page for the contact form)
*   Choose to show or hide phone numbers in the directory
*   Choose to show emails in directory OR a link to the contact form (which then passes the position or individual to the contact form, so the recipient selection is already made)
*   Choose to hide the directory from the public, so that only logged in users can see it.  You can also choose the minimum user level to view the directory (subscriber or contributor)
*   Type in your own message to display on screen after the contact form has been submitted
*   Choose to diplay images in the directory, and pick the size of the images.  If you display images, then the images will link to the individual member/post page.  Members are a custom post type, and the full editor for them is enabled, so you can create a bio page for each member.  The Featured Image is the photo that will be displayed in the directory, when photos are enabled.
*	Enable or disable Contact Form DB integration, and choose the form title to post to CFDB.

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.
== Frequently Asked Questions ==

**How do I display the directory on a page?**

Place the shortcode [pta\_member\_directory] on the page where you want the directory.

**How do I use the contact form?**

If you want to use the contact form instead of displaying emails in the directory, make sure the "Use Contact Form?" option is checked. This will replace all email addresses in the directory with a "Send A Message" link.  Clicking on that link will automatically generate the contact form on the same page, with the recipient already selected.

However, you can also use the shortcode [pta\_member\_contact] to put the Contact Form on its own separate page.  This will allow you to use the contact form independently of the directory.  If you then select the page with your contact form on the options page, when you click on "Send A Message" in the directory, the link will take the user to the contact form page with the recipient field already selected.

**Is there any spam protection?  There is no captcha field?**

I'm not a fan of captcha as I often can\'t even read them myself, and it makes setup a bit more complicated since you need to obtain and enter a key for a captcha service.  Instead, I used the honeypot method of spam protection.  There is a hidden spambot field that normal visitors won't see, but spambots will fill in.  Any form submission that has that spambot field filled in will be rejected.

== Screenshots ==

1. Add New Member - Admin
1. Options Page - Admin
1. Member Directory - Public Side
1. Contact Form - Public Side

== Changelog ==
**version 0.6**

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

**Todo List:**

*   Add a Location field that can also be passed in with a shortcode argument so you can have separate directories & contact forms for different locations or offices  
    
*   Set up the plugin as a Class to make it easier to extend and give it a clean namespace
*   Set up hooks and filters for extensibility