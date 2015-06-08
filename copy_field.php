<?php
/* 
 * CMS module: MPForm
 * For more information see info.php
 * 
 * This file copies a field of the form in the backend.
 * This file is (c) 2009 Website Baker Project <http://www.websitebaker.org/>
 * Improvements are copyright (c) 2009-2011 Frank Heyne
*/

require('../../config.php');

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Get id
if (WB_VERSION >= "2.8.2") {
	$oldfield_id = $admin->checkIDKEY('oldfield_id', false, 'GET');
	if (!$oldfield_id) {
		$admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL);
		exit();
	}
} else {
	if(!isset($_GET['oldfield_id']) OR !is_numeric($_GET['oldfield_id'])) {
		header("Location: ".ADMIN_URL."/pages/index.php");
		exit(0);
	} else {
		$oldfield_id = $_GET['oldfield_id'];
	}
}

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');
// Get new order
$order = new order(TABLE_PREFIX.'mod_mpform_fields', 'position', 'field_id', 'section_id');
$position = $order->get_new($section_id);

// Insert new row into database
$database->query("INSERT INTO ".TABLE_PREFIX."mod_mpform_fields (section_id,page_id,position,required) VALUES ('$section_id','$page_id','$position','0')");

// Get the id
$field_id = $database->get_one("SELECT LAST_INSERT_ID()");

// set temporary title
$database->query("UPDATE ".TABLE_PREFIX."mod_mpform_fields SET title = 'field$field_id' WHERE field_id = '$field_id'");  

// get values from existing field
$query_content = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_fields WHERE field_id = '$oldfield_id'");
$old = $query_content->fetchRow();

// copy settings from existing to new field
$database->query("UPDATE ".TABLE_PREFIX."mod_mpform_fields SET type='". $old['type']. "', required='". $old['required']. "', value='". $old['value']. "',"
				 ." extra='". $old['extra']. "', help='". $old['help']. "' WHERE field_id = '$field_id'");  

// Insert new column into results database
$ts = $database->query("SELECT `tbl_suffix` FROM `".TABLE_PREFIX."mod_mpform_settings` WHERE `section_id` = '$section_id'");
$setting = $ts->fetchRow();
$suffix = $setting['tbl_suffix'];
$results = TABLE_PREFIX . "mod_mpform_results_" . $suffix;
$s = "ALTER TABLE `$results` add `field" . $field_id . "` TEXT NOT NULL";
$database->query($s);

// Say that a new record has been added, then redirect to modify page
if (WB_VERSION >= "2.8.2") { $field_id = $admin->getIDKEY($field_id); }
if($database->is_error()) {
	$admin->print_error($database->get_error(), WB_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$field_id);
	} else {
	$admin->print_success($TEXT['SUCCESS'],     WB_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$field_id);
}

// Print admin footer
$admin->print_footer();

?>