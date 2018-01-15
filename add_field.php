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
require(LEPTON_PATH.'/modules/admin.php');

// Include the ordering class
require(LEPTON_PATH.'/framework/class.order.php');
// Get new order
$order = new order(TABLE_PREFIX.'mod_mpform_fields', 'position', 'field_id', 'section_id');
$position = $order->get_new($section_id);

// Insert new row into database
$fields = array(
	'section_id'	=> $section_id,
	'page_id'		=> $page_id,
	'position'		=> $position,
	'required'		=> '0',
	'value'			=> '',
	'extra'			=> ''
);
$database->build_and_execute(
	'insert',
	TABLE_PREFIX."mod_mpform_fields",
	$fields
);

// Get the id
$field_id = $database->get_one("SELECT LAST_INSERT_ID()");

// Check whether results table exists, create it if not
$ts = $database->query("SELECT `tbl_suffix` FROM `".TABLE_PREFIX."mod_mpform_settings` WHERE `section_id` = '$section_id'");
$setting = $ts->fetchRow();
$suffix = $setting['tbl_suffix'];
$results = TABLE_PREFIX . "mod_mpform_results_" . $suffix;
$t = $database->query("SHOW TABLES LIKE '".$results."'");
if ($t->numRows() < 1 ) {
	$s = "CREATE TABLE `".$results."` ( `session_id` VARCHAR(20) NOT NULL,"
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

// Say that a new record has been added, then redirect to modify page
if($database->is_error()) {
	$admin->print_error($database->get_error(), LEPTON_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$field_id);
	} else {
	$admin->print_success($TEXT['SUCCESS'], LEPTON_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$field_id);
}

// Print admin footer
$admin->print_footer();

?>