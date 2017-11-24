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

// Include admin wrapper script
$mod_dir = basename(dirname(__FILE__));
require(LEPTON_PATH.'/modules/admin.php');
require_once LEPTON_PATH.'/modules/'.$mod_dir.'/register_language.php';

if(!isset($_POST['marked_submission'])) {
	$admin->print_error($MOD_MPFORM['backend']['submission_selected'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);	
}
if(!isset($_POST['marked_submission']) OR !is_array($_POST['marked_submission'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
	exit(0);
} else {
	$submission_ids = $_POST['marked_submission'];
}

// first copy content of original table to xsik_table
$database->simple_query("DROP TABLE IF EXISTS `".TABLE_PREFIX."xsik_mod_mpform_submissions`");
$database->simple_query("CREATE TABLE `".TABLE_PREFIX."xsik_mod_mpform_submissions` LIKE `".TABLE_PREFIX."mod_mpform_submissions`");
// insert content from sik_table to original table
$database->simple_query("INSERT INTO `".TABLE_PREFIX."xsik_mod_mpform_submissions` SELECT * FROM `".TABLE_PREFIX."mod_mpform_submissions`");

// delete entries from original table
$database->simple_query("DELETE FROM `".TABLE_PREFIX."mod_mpform_submissions` WHERE `submission_id` IN(".implode(',',$submission_ids).") ");

// Check if there is a db error, otherwise say successful
if($database->is_error()) {
	$admin->print_error($database->get_error(), ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
	$admin->print_success($TEXT['SUCCESS'].'<br /><br />'.$MOD_MPFORM['backend']['delete_submissions'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();

?>