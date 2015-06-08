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