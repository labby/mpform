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

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// Include admin wrapper script
require(LEPTON_PATH.'/modules/admin.php');

// Include the ordering class

require(LEPTON_PATH.'/modules/'.$mod_dir.'/functions.php');

// Get id
if(!isset($_GET['field_id']) OR !is_numeric($_GET['field_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
	exit(0);
} else {
	$field_id = $_GET['field_id'];
}


if (isset($_GET['page_id']) AND is_numeric($_GET['page_id'])
AND isset($_GET['section_id']) AND is_numeric($_GET['section_id'])) {

 	$page_id = $_GET['page_id'];
	$section_id = $_GET['section_id'];
	$position = '';
	
	if (isset($_GET['position'])) {
		$position = $_GET['position'];
		if(!is_numeric($position)) return;
	}
	
	if ($position=='') {
		$where = strtolower(((isset($_GET['where'])) ? $_GET['where'] : ''));
	} else {	
		$where = '';
	}
	$update_when_modified = true; // Tells script to update when this page was last updated
	$print_footer=false;
	switch ($where) {
		case 'up':
		case 'down':
			$print_footer=true;
			break;
		default:
			break;
	}
	
	// Create new order object
	$order = new mpform_order(TABLE_PREFIX.'mod_mpform_fields', 'position', 'field_id', 'section_id');
	
	// Reorder It!
	switch ($where) {
		case 'up':
			$rt=$order->move_up($field_id);
			break;
		case 'down':
			$rt=$order->move_down($field_id);
			break;
		default:
			$rt=$order->move_to($section_id,$field_id,$position);
	}
	if ($print_footer) {	
		if($rt) {
			$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
		} else {
			$admin->print_error($TEXT['ERROR'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
		}
		// Print admin footer
		$admin->print_footer();
	} else {
		if(!$rt) {
			echo "<p>".$TEXT['ERROR']."</p>";
		}
	}
}
?>