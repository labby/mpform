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

$database->execute_query("DELETE FROM ".TABLE_PREFIX."search WHERE name = 'module' AND value = 'mpform'");
$database->execute_query("DELETE FROM ".TABLE_PREFIX."search WHERE extra = 'mpform'");

$database->execute_query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_mpform_fields`");
$database->execute_query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_mpform_settings`");
$database->execute_query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_mpform_submissions`");

$table_name = TABLE_PREFIX . "mod_mpform_results_%";
$t = $database->query("SHOW TABLES LIKE '".$table_name."'");
if ($t->numRows() > 0 ) {
	while ($tn = $t->fetchRow()) {
		$database->execute_query("DROP TABLE IF EXISTS `".$tn[0]."`");
	}
}

?>