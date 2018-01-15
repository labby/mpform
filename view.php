<?php

/**
 *
 * 
 *  @module      	MPForm
 *  @author         Frank Heyne, Dietrich Roland Pehlke (last)
 *  @license        http://www.gnu.org/licenses/gpl.htm
 *  @platform       see info.php of this addon
 *  @license terms  see info.php of this addon
 *  @version        see info.php of this module
 *  
 *
 */

// Must include code to stop this file being access directly
if(defined('LEPTON_PATH') == false) { exit("Cannot access this file directly"); }

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

$MOD_MPFORM = (dirname(__FILE__))."/languages/". LANGUAGE .".php";
require_once ( !file_exists($MOD_MPFORM) ? (dirname(__FILE__))."/languages/EN.php" : $MOD_MPFORM );
//	load the correct language-file
require_once (LEPTON_PATH."/modules/mpform/register_language.php");

$captcha_filename_lepton_1 = LEPTON_PATH."/include/captcha/captcha.php";
require_once((file_exists($captcha_filename_lepton_1))
	? $captcha_filename_lepton_1
	: LEPTON_PATH.'/modules/captcha_control/captcha/captcha.php'
);

// define some vars required later in private.php
global $success_email_text, $success_email_to, $felder;

// include private functions, if available
if (file_exists(LEPTON_PATH .'/modules/mpform/private.php')) {
	include_once(LEPTON_PATH .'/modules/mpform/private.php');
}

// Work-out if the form has been submitted or not
if ($_POST != array()) {    
	// some form has been submitted:
	include_once(LEPTON_PATH .'/modules/mpform/evalform.php');
	eval_form($section_id);
} else {
	// the form has not been submitted:
	include_once(LEPTON_PATH .'/modules/mpform/paintform.php');
	paint_form($section_id);
}
?>