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
 
 // include class.secure.php to protect this file and the whole CMS!
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
// end include class.secure.php
 
$module_directory	= 'mpform';
$module_name		= 'MPForm';
$module_function	= 'page';
$module_version		= '2.0.0';
$module_platform	= '4.x';
$module_author		= 'Frank Heyne, Dietrich Roland Pehlke (last)';
$module_license		= '<a href="http://www.gnu.org/licenses/gpl.htm">GNU General Public License</a>';
$module_guid		= '3FFE634D-D6BD-4C42-B449-D7A1DE3BA74A';
$module_description = 'This module allows you to create customised online forms. <a href="https://doc.lepton-cms.org/_extern/mp_form/EN/mpform.html" target="_blank">See online docs</a>';
$module_home		= '<a href="http://lepton-cms.com">LEPAdoR</a>';

/**
 * Documentation:  https://doc.lepton-cms.org/_extern/mp_form/EN/mpform.html
 */
?>