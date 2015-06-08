<?php

/**
 *
 * @category        page
 * @package         MPForm
 * @author          Frank Heyne (mod 4 wb at heysoft dot de), Dietrich Roland Pehlke (last)
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        LEPTON-CMS 2.0.0
 * @requirements    PHP 5.3 and higher
 * @version         1.1.8
 * @lastmodified    Jun 2015 
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

// Get id
if(!isset($_GET['field_id']) OR !is_numeric($_GET['field_id'])) {
	header("Location: index.php");
	exit(0);
} else {
	$field_id = $_GET['field_id'];
}
// Include WB admin wrapper script
require(LEPTON_PATH.'/modules/admin.php');

// Include the ordering class
require(LEPTON_PATH.'/framework/class.order.php');

// Create new order object an reorder
$order = new order(TABLE_PREFIX.'mod_mpform_fields', 'position', 'field_id', 'section_id');
if($order->move_up($field_id)) {
	$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
	$admin->print_error($TEXT['ERROR'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();
?>