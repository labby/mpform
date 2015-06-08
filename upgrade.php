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

require(LEPTON_PATH.'/modules/mpform/info.php');

echo "<BR><B>Updating database for module: $module_name</B><BR>";

// adding fields new in version 0.4.0:
//get settings table to see what needs to be created
$settingstable=$database->query("SELECT * FROM `".TABLE_PREFIX."mod_mpform_settings");
$settings = $settingstable->fetchRow();


// If not already there, add new fields to the existing settings table
echo'<span class="good"><b>Adding new fields to the settings table</b></span><br />';

if (!isset($settings['success_text'])){
	$qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_settings` ADD `success_text` TEXT NOT NULL AFTER `success_page`";
	$database->query($qs);
	if($database->is_error()) {
		echo mysql_error().'<br />';
	} else {
		echo "Added new field `success_text` successfully<br />";
	}
}

if (!isset($settings['submissions_text'])){
	$qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_settings` ADD `submissions_text` TEXT NOT NULL AFTER `success_text`";
	$database->query($qs);
	if($database->is_error()) {
		echo mysql_error().'<br />';
	} else {
		echo "Added new field `submissions_text` successfully<br />";
	}
}

if (!isset($settings['email_text'])){
	$qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_settings` ADD `email_text` TEXT NOT NULL AFTER `email_subject`";
	$database->query($qs);
	if($database->is_error()) {
		echo mysql_error().'<br />';
	} else {
		echo "Added new field `email_text` successfully<br />";
	}
}

if (!isset($settings['enum_start'])){
	$qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_settings` ADD `enum_start` VARCHAR( 1 ) NOT NULL";
	$database->query($qs);
	if($database->is_error()) {
		echo mysql_error().'<br />';
	} else {
		echo "Added new field `enum_start` successfully<br />";
	}
}
	
// removing fields never ever used:
if (isset($settings['radio_html'])){
	$qs = "ALTER TABLE `".TABLE_PREFIX."mod_mpform_settings` DROP `radio_html`, DROP `date_html`,
		DROP `check_html_header`, DROP `check_html_loop`, DROP `check_html_footer`, 
		DROP `select_html_header`, DROP `select_html_loop`, DROP `select_html_footer`";
	$database->query($qs);
	if($database->is_error()) {
		echo mysql_error().'<br />';
	} else {
		echo "Removed unnecessary fields successfully<br />";
	}
}

// Remove bug in Search Query body (below version 0.1.3)
$query_body_code = " [TP]pages.page_id = [TP]mod_mpform_settings.page_id AND [TP]mod_mpform_settings.header LIKE \'%[STRING]%\' AND [TP]pages.searching = \'1\'
OR [TP]pages.page_id = [TP]mod_mpform_settings.page_id AND [TP]mod_mpform_settings.footer LIKE \'%[STRING]%\' AND [TP]pages.searching = \'1\'
OR [TP]pages.page_id = [TP]mod_mpform_fields.page_id AND [TP]mod_mpform_fields.title LIKE \'%[STRING]%\' AND [TP]pages.searching = \'1\'";

$qs = "UPDATE ".TABLE_PREFIX."search SET value = '$query_body_code' WHERE name = 'query_body' and extra = 'mpform' LIMIT 1";
$database->query($qs);
if($database->is_error()) {
	echo mysql_error().'<br />';
} else {
	echo "Search function updated successfully<br />";
}

echo "<BR><B>Module $module_name updated to version: $module_version</B><BR>";
sleep (5);
?>
