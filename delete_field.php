<?php
/* 
 * CMS module: MPForm
 * For more information see info.php
 * 
 * This file deletes a field in a section in the backend.
 * This file is (c) 2009 Website Baker Project <http://www.websitebaker.org/>
 * Improvements are copyright (c) 2009-2011 Frank Heyne
*/

require('../../config.php');

// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');

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

// Delete row
$database->query("DELETE FROM ".TABLE_PREFIX."mod_mpform_fields WHERE field_id = '$field_id' and page_id = '$page_id'");

// Check if there is a db error, otherwise say successful
if($database->is_error()) {
	$admin->print_error($database->get_error(), ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
	$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();

?>