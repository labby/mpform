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

// Get id
if(!isset($_GET['field_id']) OR !is_numeric($_GET['field_id'])) {
	header("Location: index.php");
	exit(0);
} else {
	$field_id = $_GET['field_id'];
}

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');

// Create new order object an reorder
$order = new order(TABLE_PREFIX.'mod_mpform_fields', 'position', 'field_id', 'section_id');
if($order->move_down($field_id)) {
	$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
	$admin->print_error($TEXT['ERROR'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();
?>