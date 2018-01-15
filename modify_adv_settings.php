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

// Include admin wrapper script
require(LEPTON_PATH.'/modules/admin.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

$MOD_MPFORM = (dirname(__FILE__))."/languages/". LANGUAGE .".php";
require_once ( !file_exists($MOD_MPFORM) ? (dirname(__FILE__))."/languages/EN.php" : $MOD_MPFORM );

//START HEADER HERE
require_once(LEPTON_PATH.'/modules/'.$mod_dir.'/functions.php');
module_header_footer($page_id, $mod_dir);
//END HEADER HERE

// Get header and footer
$query_content = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_mpform_settings` WHERE `section_id` = '$section_id'");
$setting = $query_content->fetchRow();

// protect from cross page reading
if ($setting['page_id'] != $page_id) {  
	header("Location: ".ADMIN_URL."/pages/index.php");
	exit(0);
}

// include template parser class and set template
require_once(LEPTON_PATH . '/include/phplib/template.inc');
$tpl = new Template(dirname(__FILE__) . '/htt/');

// define how to handle unknown variables (default:='remove', during development use 'keep' or 'comment')
$tpl->set_unknowns('keep');

// define debug mode (default:=0 (disabled), 1:=variable assignments, 2:=calls to get variable, 4:=show internals)
$tpl->debug = 0;

$tpl->set_file('page', 'backend_modify_adv_settings.htt');
$tpl->set_block('page', 'main_block', 'main');

// replace all placeholder {xxx} of the template file with values from language file
foreach($MOD_MPFORM['backend_adv'] as $key => $value) {
	$tpl->set_var($key, $value);
}

// obtain display option from the database table
$table = TABLE_PREFIX . 'mod_mpform_settings';
$sql = "SELECT * FROM `$table` WHERE `section_id` = '$section_id'";
$sql_result = $database->query($sql);
$settings = $sql_result->fetchRow();

// replace all placeholder {xxx} of the template file with values from the db
foreach($settings as $key => $value) {
	$tpl->set_var($key, $value);
}

// replace static template placeholders with values from language file
$tpl->set_var(
	array(
		// variables from Website Baker framework
		'PAGE_ID'			=> (int) $page_id,
		'SECTION_ID'		=> (int) $section_id,
		'MOD_CLASS'			=> strtolower(basename(dirname(__FILE__))),
		'MODULE_URL'		=> LEPTON_URL . "/modules/mpform",
		// variables from global WB language files
		'des_use_captcha'           => '',
		'txt_use_captcha'           => $TEXT['CAPTCHA_VERIFICATION'],
		'use_captcha_true_checked'	=> (($settings['use_captcha']==true) ? 'checked="checked"' : ''),
		'use_captcha_false_checked'	=> (($settings['use_captcha']==true) ? '' : 'checked="checked"'),
		'des_max_submissions'       => '',
		'txt_max_submissions'       => $TEXT['MAX_SUBMISSIONS_PER_HOUR'],
		'des_stored_submissions'    => '',
		'txt_stored_submissions'    => $TEXT['SUBMISSIONS_STORED_IN_DATABASE'],
		'des_upload_files_folder'   => '',
		'MEDIA_DIRECTORY'           => LEPTON_PATH . MEDIA_DIRECTORY,
		'des_attach_file'           => '',
		'attach_file_true_checked'	=> (($settings['attach_file']==true) ? 'checked="checked"' : ''),
		'attach_file_false_checked'	=>(($settings['attach_file']==true) ? '' : 'checked="checked"'),
		'des_max_file_size_kb'     	=> '',
		'TXT_ENABLED'		=> $TEXT['ENABLED'],
		'TXT_DISABLED'		=> $TEXT['DISABLED'],
		'TXT_SAVE'			=> $TEXT['SAVE'],
		'TXT_CANCEL'		=> $TEXT['CANCEL'],

		// module settings
		'MOD_SAVE_URL'				=> LEPTON_URL. str_replace("\\","/",substr(dirname(__FILE__),strlen(LEPTON_PATH))).'/save_adv_settings.php',
		'MOD_CANCEL_URL'			=> ADMIN_URL.'/pages/modify.php?page_id='.$page_id
	)
);

// Parse template objects output
$tpl->parse('main', 'main_block', false);
$tpl->pparse('output', 'page',false, false);

echo "&nbsp;";
$admin->print_footer();
?>