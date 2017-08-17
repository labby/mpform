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

// include WB admin wrapper script to check permissions
$update_when_modified = true;
require(LEPTON_PATH . '/modules/admin.php');

// protect from cross page writing
$query_content = $database->query("SELECT * FROM ".TABLE_PREFIX."sections WHERE section_id = '$section_id'");
$res = $query_content->fetchRow();
if ($res['page_id'] != $page_id) {  
	header("Location: ".ADMIN_URL."/pages/index.php");
	exit(0);
}

// obtain module directory
$curr_dir = dirname(__FILE__);

// convert page/section id to numbers (already checked by /modules/admin.php but kept for consistency)
$page_id = (isset($_POST['page_id'])) ? (int) $_POST['page_id'] : '';
$section_id = (isset($_POST['section_id'])) ? (int) $_POST['section_id'] : '';

$update_keys=array('heading_html', 'short_html', 'long_html', 'email_html', 'uploadfile_html', 'enum_start',
				   'use_captcha', 'date_format', 'max_submissions', 'stored_submissions', 'upload_files_folder',
				   'upload_dir_mask', 'upload_file_mask', 'attach_file', 'max_file_size_kb', 'upload_only_exts');
foreach($update_keys as $key) {
	${$key} = (isset($_POST[$key])) ? $admin->get_post_escaped($key) : '';
}

// Sanitize data, cleaning if necessary:
if(!is_numeric($max_file_size_kb))	$max_file_size_kb = 1024;

$upload_only_exts = preg_replace("/[^0-9a-zA-Z,]/", "", $upload_only_exts);  // only allow valid chars 

if(!is_numeric($max_submissions)) $max_submissions = 50;
if(!is_numeric($stored_submissions)) $stored_submissions = 1000;
// Make sure max submissions is not greater than stored submissions if stored_submissions <> 0
if($max_submissions > $stored_submissions) $max_submissions = $stored_submissions;

//	Add folder for the files

if (!$upload_files_folder) $upload_files_folder = MEDIA_DIRECTORY . '/';
if ((substr($upload_files_folder,-1,1) === "\\") or (substr($upload_files_folder,-1,1) === "/")) {
	$upload_files_folder = substr($upload_files_folder,0,-1);
}	
$upload_files_folder = preg_replace("/[^\\0-9a-zA-Z_\-\.\/]/", "", $upload_files_folder);  // only allow valid chars 

$upload_dir_mask = preg_replace("/[^0-7]/", "", $upload_dir_mask);  // only allow valid chars
if(!is_numeric($upload_dir_mask) || $upload_dir_mask==0) $upload_dir_mask = '0705';

$upload_file_mask = preg_replace("/[^0-7]/", "", $upload_file_mask);  // only allow valid chars
if(!is_numeric($upload_file_mask) || $upload_file_mask==0) $upload_file_mask = '0604';

require_once( LEPTON_PATH."/framework/functions/function.make_dir.php");
require_once( LEPTON_PATH."/framework/functions/function.change_mode.php");

// set permissions for upload directory
change_mode(LEPTON_PATH.MEDIA_DIRECTORY);  // reset to full permission
chmod(LEPTON_PATH.MEDIA_DIRECTORY, intval('0775', 8));

if ($upload_files_folder != MEDIA_DIRECTORY ) {
	if (!file_exists(LEPTON_PATH.$upload_files_folder) && !is_dir(LEPTON_PATH.$upload_files_folder)) {
		make_dir(LEPTON_PATH.$upload_files_folder);
		copy( LEPTON_PATH.MEDIA_DIRECTORY.'/index.php', LEPTON_PATH.$upload_files_folder.'/index.php'); // no directory listings allowed
	}
	if (is_dir(LEPTON_PATH.$upload_files_folder)) {
		change_mode(LEPTON_PATH.$upload_files_folder); // reset to full permission
		chmod(LEPTON_PATH.$upload_files_folder, intval($upload_dir_mask, 8));
	}
}

// now loop over update values and create the SQL query string (this way we do not forget values)
$sql_key_values = '';
foreach($update_keys as $key) {
	$sql_key_values .= (($sql_key_values) ? ', ' : '' ) . "`$key` = '" . ${$key} . "'";
}

// write page settings to the module table
$table = TABLE_PREFIX . 'mod_mpform_settings';
$sql = "UPDATE `$table` SET $sql_key_values
	WHERE `section_id` = '$section_id'";

$database->query($sql);

// check if there is a db error, otherwise say successful
if ($database->is_error()) {
	$admin->print_error($database->get_error(), ADMIN_URL . '/pages/modify.php?page_id=' . $page_id);
} else {
	$admin->print_success($TEXT['SUCCESS'], ADMIN_URL . '/pages/modify.php?page_id=' . $page_id);
}

// print admin footer
$admin->print_footer();

?>