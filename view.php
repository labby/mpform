<?php

/**
 *
 * 
 *  @module         MPForm
 *  @author         Frank Heyne, Dietrich Roland Pehlke, erpe
 *  @license        http://www.gnu.org/licenses/gpl.htm
 *  @platform       see info.php of this addon
 *  @license terms  see info.php of this addon
 *  @version        see info.php of this module
 *  
 *
 */
 
 // include class.secure.php to protect this file and the whole CMS!
if (defined('LEPTON_PATH')) {	
	include(LEPTON_PATH.'/framework/class.secure.php'); 
} else {
	$oneback = "../";
	$root = $oneback;
	$level = 1;
	while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
		$root .= $oneback;
		$level += 1;
	}
	if (file_exists($root.'/framework/class.secure.php')) { 
		include($root.'/framework/class.secure.php'); 
	} else {
		trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
	}
}
// end include class.secure.php

$MOD_MPFORM = (dirname(__FILE__))."/languages/". LANGUAGE .".php";
require_once ( !file_exists($MOD_MPFORM) ? (dirname(__FILE__))."/languages/EN.php" : $MOD_MPFORM );

require_once LEPTON_PATH.'/modules/captcha_control/captcha/captcha.php';

// define some vars required later in custom.php
global $success_email_text, $success_email_to, $felder;

// include custom functions, if available
if (file_exists(LEPTON_PATH .'/modules/mpform/custom.php')) {
	include_once(LEPTON_PATH .'/modules/mpform/custom.php');
}

// Work-out if the form has been submitted or not
if ($_POST != array()) {    
	// some form has been submitted:
	require_once(LEPTON_PATH .'/modules/mpform/evalform.php');
	eval_form($section_id);
} else {
	// the form has not been submitted:
	require_once(LEPTON_PATH .'/modules/mpform/paintform.php');
	paint_form($section_id);
}
?>