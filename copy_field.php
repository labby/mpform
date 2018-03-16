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
require(LEPTON_PATH.'/modules/admin.php');

if(!isset($_GET['oldfield_id']) OR !is_numeric($_GET['oldfield_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
	exit(0);
} else {
	$oldfield_id = intval($_GET['oldfield_id']);
}

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
	'required'		=> 0
);

$database->build_and_execute(
	"insert",
	TABLE_PREFIX."mod_mpform_fields",
	$fields
);

// Get the id
$field_id = $database->get_one("SELECT LAST_INSERT_ID()");

// set temporary title
$fields = array(
	'title' => "field".$field_id
);

$database->build_and_execute(
	"update",
	TABLE_PREFIX."mod_mpform_fields",
	$fields,
	"`field_id` = '".$field_id."'"
);  

// get values from existing field
$old = array();
$query_content = $database->execute_query(
	"SELECT `type`,`required`,`value`,`extra`,`help` FROM `".TABLE_PREFIX."mod_mpform_fields` WHERE `field_id` = '".$oldfield_id."'",
	true,
	$old,
	false
);

// copy settings from existing to new field
$database->build_and_execute(
	"update",
	TABLE_PREFIX."mod_mpform_fields",
	$old,
	"`field_id`= '".$field_id."'"
);  

// Insert new column into results database
$ts = $database->query("SELECT `tbl_suffix` FROM `".TABLE_PREFIX."mod_mpform_settings` WHERE `section_id` = '$section_id'");
$setting = $ts->fetchRow();
$suffix = $setting['tbl_suffix'];
$results = TABLE_PREFIX . "mod_mpform_results_" . $suffix;
$s = "ALTER TABLE `$results` add `field" . $field_id . "` TEXT NOT NULL";
$database->query($s);

// Say that a new record has been added, then redirect to modify page
if($database->is_error()) {
	$admin->print_error($database->get_error(), LEPTON_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$field_id);
	} else {
	$admin->print_success($TEXT['SUCCESS'],     LEPTON_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$field_id);
}

// Print admin footer
$admin->print_footer();

?>