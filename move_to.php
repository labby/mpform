<?php
/* 
 * CMS module: MPForm
 * For more information see info.php
 * 
 * This backend file changes the ordering of the fields in the form.
 * This file is (c) 2009 Website Baker Project <http://www.websitebaker.org/>
 * Improvements are copyright (c) 2009-2011 Frank Heyne
*/

require('../../config.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Load the other required class files if they are not already loaded
require_once(WB_PATH."/framework/class.database.php");

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');
require(WB_PATH.'/modules/'.$mod_dir.'/functions.php');

// Get id
if (WB_VERSION >= "2.8.2") {
	$field_id = $admin->checkIDKEY('field_id', false, 'GET');
	if (!$field_id) {
		$admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL);
		exit();
	}
} else {
	if(!isset($_GET['field_id']) OR !is_numeric($_GET['field_id'])) {
		header("Location: ".ADMIN_URL."/pages/index.php");
		exit(0);
	} else {
		$field_id = $_GET['field_id'];
	}
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
	$order = new orderx(TABLE_PREFIX.'mod_mpform_fields', 'position', 'field_id', 'section_id');
	
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