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

$update_keys=array('header', 'field_loop', 'footer', 'field_loop', 'email_to', 'email_from', 'email_fromname', 
					'email_subject', 'success_page', 'success_text', 'submissions_text', 'email_text', 'success_email_to',
					'success_email_from', 'success_email_fromname',	'success_email_text','success_email_subject', 
					'is_following', 'tbl_suffix');
foreach($update_keys as $key) {
	${$key} = (isset($_POST[$key])) ? $admin->get_post_escaped($key) : '';
}
$upd_extra = array('email_from_field','email_fromname_field');
foreach($upd_extra as $key) {
	${$key} = (isset($_POST[$key])) ? $admin->get_post_escaped($key) : '';
}

// Sanitize data, cleaning if necessary:
$tbl_suffix = preg_replace("/\W/", "", $tbl_suffix);  // only allow alphanumerical chars as table suffix

// check multiple email recipients
$temp_email_to = "";
$emails = preg_split('/[\r\n]/', $email_to);
foreach ($emails as $recip) {
	$teil = explode("<", $recip);
	$ok = false;
	if (count($teil) == 1) { // no name part found
		$ok = $admin->filter_var(trim($teil[0]));
	} elseif (count($teil) == 2) { // with name part
		$s = explode(">", $teil[1]);  	// string with (list of) email address(es)
		$se = explode(",", $s[0]);		// array  with (list of) email address(es)
		foreach ($se as $sh) {
			$ok = $admin->filter_var(trim($sh)); // check each address
			if (!$ok) break;  // break as soon as an invalid address is found
		}
	}
	if ($ok) $temp_email_to .= "$recip\n"; // only take valid lines
}
$email_to = $temp_email_to;

if (!$admin->filter_var($email_from)) $email_from = '';
if (!$admin->filter_var($success_email_from)) $success_email_from = '';

$email_fromname = htmlspecialchars($email_fromname, ENT_QUOTES);
$email_subject = htmlspecialchars($email_subject, ENT_QUOTES);
$success_email_fromname = htmlspecialchars($success_email_fromname, ENT_QUOTES);
$success_email_subject = htmlspecialchars($success_email_subject, ENT_QUOTES);
$success_email_text = htmlspecialchars($success_email_text, ENT_QUOTES);
// end of data cleaning

if($email_from_field != '') $email_from = $email_from_field;  // use a field of the form as sender's address
if($email_fromname_field != '') $email_fromname = $email_fromname_field; //  use a field of the form as sender's name

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