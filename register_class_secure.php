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
global $lepton_filemanager;
if (!is_object($lepton_filemanager)) require_once( "../../framework/class.lepton.filemanager.php" );

$base = "/modules/mpform/";
 
$files_to_register = array(
	$base.'add.php',
	$base.'add_field.php',
	$base.'copy_field.php',
	$base.'delete_field.php',
	$base.'delete_submission.php',
	$base.'help.DE.php',
	$base.'help.EN.php',
	$base.'modify_field.php',
	$base.'modify_settings.php',
	$base.'modify_adv_settings.php',
	$base.'move_to.php',
	$base.'move_down.php',
	$base.'move_up.php',
	$base.'save.php',
	$base.'save_settings.php',
	$base.'save_adv_settings.php',
	$base.'save_field.php'
);

$lepton_filemanager->register( $files_to_register );

?>