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

require(LEPTON_PATH.'/modules/admin.php');

global $MOD_MPFORM, $parser, $loader; 

$mod_dir = basename(dirname(__FILE__));
require_once LEPTON_PATH.'/modules/'.$mod_dir.'/info.php';
require_once LEPTON_PATH.'/modules/'.$mod_dir.'/register_language.php';

//START HEADER HERE
require_once(LEPTON_PATH.'/modules/'.$mod_dir.'/functions.php');
require_once(LEPTON_PATH.'/modules/'.$mod_dir.'/constants.php');
//END HEADER HERE

$oTWIG = lib_twig_box::getInstance();
$oTWIG->registerModule( $mod_dir );

/** 
 *	Make sure that page and section id are numeric.
 */
$page_id = (isset($page_id)) ? intval($page_id) : 0;
$section_id = (isset($section_id)) ? intval($section_id ): 0;

// Query submissions table
$order_submissions=array();
$all_submissions = array();
$database->execute_query(
	"SELECT * FROM `".TABLE_PREFIX."mod_mpform_submissions` WHERE `section_id` = '".$section_id."' ORDER BY `submitted_when` DESC",
	true,
	$all_submissions
);

if(count($all_submissions) > 0) {
	// List submissions
	foreach($all_submissions as &$submission) {	
		$order_submissions[]= array(
				'SUBMISSION_ID'	=> $submission['submission_id'],
				'field_submission_id'	=> $submission['submission_id'],
				'field_submission_when'	=> date(TIME_FORMAT.', '.DATE_FORMAT, $submission['submitted_when'])
		);
	}
}

$form_values = array(
	// variables from framework
	'MOD_MPFORM'	=> $MOD_MPFORM,
	'PAGE_ID'		=> (int) $page_id,
	'SECTION_ID'	=> (int) $section_id,
	'LEPTON_URL'	=> LEPTON_URL,
	'ADMIN_URL'		=> ADMIN_URL,
	'TXT_SAVE'		=> $TEXT['SAVE'],
	'TXT_CANCEL'	=> $TEXT['CANCEL'],
	'TXT_TITLE'		=> $TEXT['TITLE'],
	'TXT_PLEASE_SELECT'	=> $TEXT['PLEASE_SELECT'],
	'MODULE_URL'    => LEPTON_URL.'/modules/'.$mod_dir,

	// variables from global language files
	'TXT_SAVE'		=> $TEXT['SAVE'],
	'TXT_CANCEL'	=> $TEXT['CANCEL'],
	'TXT_HELP'		=> $MENU['HELP'],
	'TEXT_HEADING_F'=> $TEXT['MODIFY'].'/'.$TEXT['DELETE'].' '.$TEXT['FIELD'] ,
	'TEXT_HEADING_S'=> $TEXT['SUBMISSIONS'], 
	'TEXT_DELETE'	=> $TEXT['DELETE'],
	'TEXT_ARE_YOU_SURE' => str_replace(' ', '%20', $MOD_MPFORM['backend']['Are_you_sure']), // #1
	'TEXT_SUBMISSION_ID'	=> $TEXT['SUBMISSION_ID'],
	'TEXT_SUBMITTED'=> $TEXT['SUBMITTED'],
	"order_submissions"	=> $order_submissions,
	"leptoken"		=> get_leptoken(),	
	'MARKALL'	=>  $MESSAGE['ADDON_GROUPS_MARKALL'],
		
	// module settings
	'MODULE_DIR'    => $mod_dir,
	'TXT_TYPE'		=> $MOD_MPFORM['backend']['TXT_TYP'],
	'TXT_COPY_FIELD'=> $MOD_MPFORM['backend']['TXT_COPY_FIELD'],
	'TXT_ADD_FIELD'	=> $MOD_MPFORM['backend']['TXT_ADD_FIELD']
);

echo $oTWIG->render(
	'@mpform/backend_delete_submissions.lte',
	$form_values
);
// Print admin footer
$admin->print_footer();

?>