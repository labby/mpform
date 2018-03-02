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

// delete search table entries
$database->execute_query("DELETE FROM ".TABLE_PREFIX."search WHERE name = 'module' AND value = 'mpform'");
$database->execute_query("DELETE FROM ".TABLE_PREFIX."search WHERE extra = 'mpform'");

// drop tables
LEPTON_handle::drop_table("mod_mpform_fields");
LEPTON_handle::drop_table("mod_mpform_settings");
LEPTON_handle::drop_table("mod_mpform_submissions");

//drop result tables
$table_name = TABLE_PREFIX . "mod_mpform_results_%";
$result_tables = array();
$database->execute_query(
	"SHOW TABLES LIKE ".$table_name." ",
	true,
	$result_tables,
	true
);	
if (count($result_tables) > 0 ) {
	foreach ($result_tables as $to_delete) {
		LEPTON_handle::drop_table($to_delete);
	}
}

?>