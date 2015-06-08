<?php
/* 
 * CMS module: MPForm
 * For more information see info.php
 * 
 * This file adds a field to the form in the backend.
 * This file is (c) 2009 Website Baker Project <http://www.websitebaker.org/>
 * Improvements are copyright (c) 2009-2011 Frank Heyne
*/

require('../../config.php');

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');
// Get new order
$order = new order(TABLE_PREFIX.'mod_mpform_fields', 'position', 'field_id', 'section_id');
$position = $order->get_new($section_id);

// Insert new row into database
$database->query("INSERT INTO ".TABLE_PREFIX."mod_mpform_fields (section_id,page_id,position,required) VALUES ('$section_id','$page_id','$position','0')");

// Get the id
$field_id = $database->get_one("SELECT LAST_INSERT_ID()");

// Check whether results table exists, create it if not
$ts = $database->query("SELECT `tbl_suffix` FROM `".TABLE_PREFIX."mod_mpform_settings` WHERE `section_id` = '$section_id'");
$setting = $ts->fetchRow();
$suffix = $setting['tbl_suffix'];
$results = TABLE_PREFIX . "mod_mpform_results_" . $suffix;
$t = $database->query("SHOW TABLES LIKE '".$results."'");
if ($t->numRows() < 1 ) {
	$s = "CREATE TABLE `$results` ( `session_id` VARCHAR(20) NOT NULL,"
		. ' `started_when` INT NOT NULL DEFAULT \'0\' ,'     	// time when first form was sent to browser
		. ' `submitted_when` INT NOT NULL DEFAULT \'0\' ,'   	// time when last form was sent back to server
		. ' `referer` VARCHAR( 255 ) NOT NULL, '				// referer page
		. ' PRIMARY KEY ( `session_id` ) '
		. ' )';
	$database->query($s);
}

// Insert new column into database
$s = "ALTER TABLE `$results` add `field" . $field_id . "` TEXT NOT NULL";
$database->query($s);

(WB_VERSION >= "2.8.2") ? $fid = $admin->getIDKEY($field_id) : $fid = $field_id; 
// Say that a new record has been added, then redirect to modify page
if($database->is_error()) {
	$admin->print_error($database->get_error(), WB_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$fid);
	} else {
	$admin->print_success($TEXT['SUCCESS'], WB_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$fid);
}

// Print admin footer
$admin->print_footer();

?>