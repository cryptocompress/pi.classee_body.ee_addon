<?php

/*
=====================================================
 ExpressionEngine - by pMachine
-----------------------------------------------------
 http://www.pmachine.com/
-----------------------------------------------------
 Copyright (c) 2003,2004,2005 pMachine, Inc.
=====================================================
 THIS IS COPYRIGHTED SOFTWARE
 PLEASE READ THE LICENSE AGREEMENT
 http://www.pmachine.com/expressionengine/license.html
=====================================================
 File: pi.classee_body.php
-----------------------------------------------------
 Purpose: Applies dynamic classes to your BODY tag.
=====================================================
*/



$plugin_info = array(
	'pi_name'			=> 'ClassEE Body',
	'pi_version'		=> '1.0.1',
	'pi_author'			=> 'Derek Hogue',
	'pi_author_url'		=> 'http://amphibian.info',
	'pi_description'	=> 'Applies dynamic classes to your BODY tag.',
	'pi_usage'			=> Classee_body::usage()
);

class Classee_body
{

	function Classee_body()
	{
		global $TMPL, $IN, $SESS, $PREFS;
		
		$this->return_data = '';
		
		$r = '';		
		$attr = $TMPL->fetch_param('attr');
		$open = ( $attr == 'false' ) ? '' : ' class="';
		$close = ( $attr == 'false' ) ? '' : '"';
		
		$segments = count($IN->SEGS);
		$cat_trigger = $PREFS->ini('reserved_category_word');			
				
		if($segments > 0) {
			
			// class per URI segment
			for($i = 1; $i <= $segments; $i++) {
				$seg = $IN->fetch_uri_segment($i);
				// Ignore the category indicator
				if($seg != $cat_trigger) {
					// prepend numeric segs
					$pre = ''; if(is_numeric($seg)) { $pre = 'n'; }
					$r .= $pre . $seg . ' ';
				}
			}
			
			// Check for pagination
			if(ereg('P{1}[0-9]+', $IN->URI) != FALSE) {
				$r .= 'paged ';
			}
			
			// Check for category
			if(strpos($IN->URI, "/$cat_trigger/") !== FALSE || ereg('C{1}[0-9]+', $IN->URI) != FALSE) {
				$r .= 'category ';
			}
			
			// Check for monthly archive
			if ( $segments >= 2) {
				$m = $IN->fetch_uri_segment($segments);
				$y = $IN->fetch_uri_segment($segments-1);
				if(ereg('^[0-9]{4}$', $y) != FALSE && ereg('^[0-9]{2}$', $m) != FALSE) {
					$r .= 'monthly ';
				}
			}			
			
		} else {
			
			// No segs, so we're on the home page
			$r .= 'home ';		
		
		}
		
		// class for member group
		$g = $SESS->userdata['group_id'];
		
		switch($g) {
			case 1:
				$r .= 'superadmin';
				break;
			case 2:
				$r .= 'banned';
				break;
			case 3:
				$r .= 'guest';
				break;
			case 4:
				$r .= 'pending';
				break;
			case 5:
				$r .= 'member';
				break;				
			case ($g > 5):
				$r .= 'groupid_' . $g;
				break;
		}
				
		$this->return_data = $open . $r . $close;
	
	} 
    
    
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.
//  Make sure and use output buffering

function usage()
{
ob_start(); 
?>
This plugin will apply several dynamic classes to your <body> tag.  Use it like so in your template:

<body{exp:classee_body}>

That's it.  You'll now get a classed-up <body> tag using URI segments, the current member group, and type of archive page (category, paged, or monthly).

For example, if the current URI was:

http://mydomain.com/magazine/articles/c/politics/P20/ 

Your <body> tag would look like this:

<body class="magazine articles politics category paged P20 superadmin">

(In this case, you'd be logged-in as a SuperAdmin, and your category keyword would be "c".)

Member groups 1 through 5 will be classed using their group names (superadmin, banned, guest, pending, member), whereas custom member groups will be classed "groupid_N" (N being the member group ID).

Numeric URI segments (for example, when calling an entry via its entry_id) will be prepended with the letter "n", i.e.

http://mydomain.com/magazine/articles/246

Would yield:

<body class="magazine articles n246 groupid_7">

If there are no URI segments to be found, your <body> will get the class of "home".

If you'd like to retreive only the class names, but not the class="" attribute itelf, simply add attr="false" as a parameter:

{exp:classee_body attr="false"}

<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
// END
}
// END CLASS
?>