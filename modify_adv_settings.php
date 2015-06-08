<?php
/* CMS module: MPForm
 * For more information see info.php
 * 
 * This file prints the advanced settings form of the module in the backend.
 * This file is (c) 2009 Website Baker Project <http://www.websitebaker.org/>
 * Improvements are copyright (c) 2009-2011 Frank Heyne
*/

// unset page/section IDs defined via GET before including the admin file (we expect POST here)
unset($_GET['page_id']);
unset($_GET['section_id']);

// manually include the config.php file (defines the required constants)
require('../../config.php');

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// include core functions of WB 2.7 to edit the optional module CSS files (frontend.css, backend.css)
@include_once(WB_PATH .'/framework/module.functions.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include the module language file depending on the backend language of the current user
if (!@include(get_module_language_file($mod_dir))) return;  

//START HEADER HERE
require_once(WB_PATH.'/modules/'.$mod_dir.'/functions.php');
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
require_once(WB_PATH . '/include/phplib/template.inc');
$tpl = new Template(dirname(__FILE__) . '/htt/');

// define how to handle unknown variables (default:='remove', during development use 'keep' or 'comment')
$tpl->set_unknowns('keep');

// define debug mode (default:=0 (disabled), 1:=variable assignments, 2:=calls to get variable, 4:=show internals)
$tpl->debug = 0;

$tpl->set_file('page', 'backend_modify_adv_settings.htt');
$tpl->set_block('page', 'main_block', 'main');

// replace all placeholder {xxx} of the template file with values from language file
foreach($LANG['backend_adv'] as $key => $value) {
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
		'MODULE_URL'		=> WB_URL . "/modules/mpform",
		//'FTAN'				=> (WB_VERSION >= "2.8.2") ? $admin->getFTAN() : '',
		'FTAN'			=> '',

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
		'MEDIA_DIRECTORY'           => WB_PATH . MEDIA_DIRECTORY,
		'des_attach_file'           => '',
		'attach_file_true_checked'	=> (($settings['attach_file']==true) ? 'checked="checked"' : ''),
		'attach_file_false_checked'	=>(($settings['attach_file']==true) ? '' : 'checked="checked"'),
		'des_max_file_size_kb'     	=> '',
		'TXT_ENABLED'		=> $TEXT['ENABLED'],
		'TXT_DISABLED'		=> $TEXT['DISABLED'],
		'TXT_SAVE'			=> $TEXT['SAVE'],
		'TXT_CANCEL'		=> $TEXT['CANCEL'],

		// module settings
		'MOD_SAVE_URL'				=> WB_URL. str_replace("\\","/",substr(dirname(__FILE__),strlen(WB_PATH))).'/save_adv_settings.php',
		'MOD_CANCEL_URL'			=> ADMIN_URL.'/pages/modify.php?page_id='.$page_id
	)
);

// Parse template objects output
$tpl->parse('main', 'main_block', false);
$tpl->pparse('output', 'page',false, false);

$admin->print_footer();
?>