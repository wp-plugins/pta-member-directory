<?php
/*
Copy and paste the code below into your theme's functions.php file
Modify, add, or delete cases for various string IDs you want to change (see output-filters.txt for list of IDs)

The below function has two case examples to get you started.  The first case is a simple text string that doesn't use any variables in the output.
The second case shows how to include the variable in the output, which, in the below example, is the location.

If you only want to change one string, then modify the appropriate example below depending on if the text output you want to modify
includes a variable or not.  Change the text between the single quotes on the line with case to match the ID of the text string you want to change.
Change the text between the double quotes on the next line to be what you want the output text to be.  

The location of the variable can be anywhere that you want.  Although it's not necessary to use the esc_html function, it's a best practice
for security to remove any malicious code that could have been passed in by an attacker.  If you want the variable to appear first in the
output, you would do something like:
$text = esc_html( $variable ) . " is the location";
The period between the variable and the text string is a concatentation operator, and is necessary, as is the semi-colon at the end of the line.
You can even put the variable in the middle of the text string by using concatenation, such as:
$text = "I love " . esc_html( $variable ) . " in the morning";

Each of your cases should end with the break; statement as shown in the examples.

If you aren't comfortable with PHP, or just want an easier way to modify any of the output text, check out the PTA Member Directory Customizer
at http://stephensherrardplugins.com
The PTA Member Directory Customizer gives you a simple, single page form, where you can see all the customizable text and what the defaults are,
and you can quickly enter any text you want to modify, along with simple placeholder text for where you want any variables to appear within your text string.
 */

function my_pta_output_modifier( $text, $id='', $variable='') {
	switch ($id) {
		case 'send_group_message':
			$text = "Contact this group.";
			break;

		case 'location_header':
			$text = "Showing directory for: " . esc_html( $variable );
			break;
		
		default:
			break;
	}
	return $text;
}

add_filter( 'pta_md_output', 'my_pta_output_modifier', 10, 3 );