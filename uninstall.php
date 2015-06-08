<?php
/* CMS module: MPForm
 * For more information see info.php
 * 
 * This file provides the deinstallation function of the module.
 * This file is (c) 2009 Website Baker Project <http://www.websitebaker.org/>
 * Improvements are copyright (c) 2009-2011 Frank Heyne
*/

// Must include code to stop this file from being accessed directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

$database->query("DELETE FROM ".TABLE_PREFIX."search WHERE name = 'module' AND value = 'mpform'");
$database->query("DELETE FROM ".TABLE_PREFIX."search WHERE extra = 'mpform'");

$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_mpform_fields`");
$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_mpform_settings`");
$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_mpform_submissions`");

$results = TABLE_PREFIX . "mod_mpform_results_%";
$t = $database->query("SHOW TABLES LIKE '".$results."'");
if ($t->numRows() > 0 ) {
	while ($tn = $t->fetchRow()) {
		$database->query("DROP TABLE IF EXISTS `".$tn[0]."`");
	}
}

?>