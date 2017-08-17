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

$fields = array(
	'section_id' => $section_id
);

$database->prepare_and_execute(
	"DELETE FROM `".TABLE_PREFIX."mod_mpform_fields` WHERE `section_id` = :section_id",
	$fields
);

$database->prepare_and_execute(
	"DELETE FROM `".TABLE_PREFIX."mod_mpform_settings` WHERE `section_id` = :section_id",
	$fields
);

?>