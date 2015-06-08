<?php
/* 
 * CMS module: MPForm
 * For more information see info.php
 * 
 * This file handles the form in the frontend.
 * This file is (c) 2009 Website Baker Project <http://www.websitebaker.org/>
 * Improvements are copyright (c) 2009-2011 Frank Heyne
*/

// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include module.functions.php (introduced with WB 2.7)
@include_once(WB_PATH . '/framework/module.functions.php');

// include the module language file depending on the backend language of the current user
if (!include(get_module_language_file($mod_dir))) return;
	
// check if frontend.css file needs to be included into the <body></body> of view.php
if((!function_exists('register_frontend_modfiles') || !defined('MOD_FRONTEND_CSS_REGISTERED')) &&  
	file_exists(WB_PATH .'/modules/mpform/frontend.css')) {
	echo '<style type="text/css">';
	include_once(WB_PATH .'/modules/mpform/frontend.css');
	echo "\n</style>\n";
} 

require_once(WB_PATH.'/include/captcha/captcha.php');

// define some vars required later in private.php
global $success_email_text, $success_email_to, $felder;

// include private functions, if available
if (file_exists(WB_PATH .'/modules/mpform/private.php')) {
	include_once(WB_PATH .'/modules/mpform/private.php');
}

// Work-out if the form has been submitted or not
if ($_POST != array()) {    
	// some form has been submitted:
	include_once(WB_PATH .'/modules/mpform/evalform.php');
	eval_form($section_id);
} else {
	// the form has not been submitted:
	include_once(WB_PATH .'/modules/mpform/paintform.php');
	paint_form($section_id);
}
?>