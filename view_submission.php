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

require(LEPTON_PATH.'/modules/admin.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

require_once LEPTON_PATH.'/modules/'.$mod_dir.'/register_language.php';

// convert page/section id to numbers (already checked by /modules/admin.php but kept for consistency)
$page_id = (isset($_GET['page_id'])) ? (int) $_GET['page_id'] : '';
$section_id = (isset($_GET['section_id'])) ? (int) $_GET['section_id'] : '';

//START HEADER HERE
require_once(LEPTON_PATH.'/modules/'.$mod_dir.'/functions.php');
module_header_footer($page_id,$mod_dir);
//END HEADER HERE

// Get id
if(!isset($_GET['submission_id']) OR !is_numeric($_GET['submission_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
	die();
} else {
	$submission_id = intval($_GET['submission_id']);
}

// Get submission details
$submission = array();
$database->execute_query(
	"SELECT * FROM `".TABLE_PREFIX."mod_mpform_submissions` WHERE `submission_id` = ".$submission_id,
	true,
	$submission,
	false
);

if(0 === count($submission))
{
	die(header("Location: ".ADMIN_URL."/pages/index.php"));
}

$submission['submitted_when_formatted'] = date(TIME_FORMAT.', '.DATE_FORMAT, $submission['submitted_when']);;

//	in the org code the submission body was parsed:
$submission_text = "";
$lines = explode("\n",$submission['body']);
foreach($lines as $k => $v) {
	$hr = explode('url]',$v);

	if (count($hr)>1) {
		$hr[0] = substr($hr[0],0,-1);
		$hr[1] = substr($hr[1],0,-2);
		$v = $hr[0]."[url]".$hr[1]."[/url]".$hr[2];
		$submission_text .= str_replace(array('[url]','[/url]'), array('<a href="','" target="_blank">'.$hr[1].'</a>'), $v); 
	} else {
		$submission_text .= $v;
	}
	$submission_text .= "<br>";
}

$submission['submission_text'] = $submission_text;

// Get the user details of whoever did this submission
$user = array();
$database->execute_query(
	"SELECT `username`, `display_name` FROM `".TABLE_PREFIX."users` WHERE `user_id` = '".$submission['submitted_by']."'",
	true,
	$user,
	false
);

if( 0 === count($user) )
{
	$user['display_name'] = 'Unknown';
	$user['username'] = 'unknown';
}

$page_values = array(
	'TEXT'			=> $TEXT,
	'section_id'	=> $section_id,
	'page_id'		=> $page_id,
	'user'			=> $user,
	'MOD_MPFORM'	=> $MOD_MPFORM,
	'submission'	=> $submission,
	'submission_id'	=> $submission_id,
	'leptoken'		=> LEPTON_tools::get_leptoken()
);

$oTWIG = lib_twig_box::getInstance();
$oTWIG->registerModule("mpform");

echo $oTWIG->render(
	"@mpform/backend_view_submissions.lte",
	$page_values
);

// Print admin footer
$admin->print_footer();
?>