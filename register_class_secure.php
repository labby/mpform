<?php

/**
 *  @module         MPForm
 *  @author         Frank Heyne, Dietrich Roland Pehlke, erpe
 *  @license        http://www.gnu.org/licenses/gpl.htm
 *  @platform       see info.php of this addon
 *  @license terms  see info.php of this addon
 *  @version        see info.php of this module
 *
 */
 

 
$files_to_register = array(
	'add.php',
	'add_field.php',
	'copy_field.php',
	'delete_field.php',
	'delete_submission.php',
	'delete_submissions.php',	
	'help.DE.php',
	'help.EN.php',
	'modify_field.php',
	'modify_settings.php',
	'modify_adv_settings.php',
	'modify_submissions.php',	
	'move_to.php',
	'move_down.php',
	'move_up.php',
	'save.php',
	'save_settings.php',
	'save_adv_settings.php',
	'save_field.php',
	'view_submission.php'
);

LEPTON_secure::getInstance()->accessFiles( $files_to_register );
?>