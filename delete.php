<?php
/* 
 * CMS module: MPForm
 * For more information see info.php
 * 
 * This file deletes section specific entries in the module tables in the backend.
 * It does not delete results and submissions!
 * This file is (c) 2009 Website Baker Project <http://www.websitebaker.org/>
 * Improvements are copyright (c) 2009-2011 Frank Heyne
*/

// Delete section
$database->query("DELETE FROM ".TABLE_PREFIX."mod_mpform_fields   WHERE section_id = '$section_id'");
$database->query("DELETE FROM ".TABLE_PREFIX."mod_mpform_settings WHERE section_id = '$section_id'");

?>