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

// Include admin wrapper script
$admin_header = false;
require(LEPTON_PATH.'/modules/admin.php');

global $MOD_MPFORM; 

$mod_dir = basename(dirname(__FILE__));
require_once LEPTON_PATH.'/modules/'.$mod_dir.'/info.php';
require_once LEPTON_PATH.'/modules/'.$mod_dir.'/register_language.php';

//	START HEADER HERE
require_once(LEPTON_PATH.'/modules/'.$mod_dir.'/functions.php');
module_header_footer($page_id, $mod_dir);

//	Get the "settings" from the database table
$settings = array();
$database->execute_query(
	"SELECT * FROM `". TABLE_PREFIX ."mod_mpform_settings` WHERE `section_id` = ".$section_id,
	true,
	$settings,
	false
);

// Protect from cross page reading
if ($settings['page_id'] != $page_id) {  
	header("Location: ".ADMIN_URL."/pages/index.php");
	exit(0);
}

// Get all pages for the "pages" select for the "following" (success) pages 
LEPTON_tools::register( "page_tree" );
$all_pages = array();
page_tree( 0, $all_pages, array('page_id','page_title','menu_title') );

// die( LEPTON_tools::display( $all_pages ));

$email_from_value = $settings['email_from'];
$email_fromname_value = $settings['email_fromname']; 

/**
 *	Any e-mail fields in the current form?
 *
 */
$all_email_fields = array();
$database->execute_query(
	"SELECT `field_id`,`title` FROM `".TABLE_PREFIX."mod_mpform_fields` WHERE `section_id` = ".$section_id." AND (`type` = 'email') ORDER BY `position` ASC",
	true,
	$all_email_fields,
	true
);

//die( LEPTON_tools::display( $all_email_fields ));

/**
 *	Same for the textfields for "names"
 */
$all_text_fields = array();
$database->execute_query(
	"SELECT `field_id`,`title` FROM `".TABLE_PREFIX."mod_mpform_fields` WHERE `section_id` = ".$section_id." AND (`type` = 'textfield') ORDER BY `position` ASC",
	true,
	$all_text_fields,
	true
);
	 
// Collecting the values for the template
$form_values = array(
	'settings'			=> $settings,
	'MOD_MPFORM'		=> $MOD_MPFORM['backend'],
	'MOD_MPFORM_ADV'	=> $MOD_MPFORM['backend_adv'],
	
	'all_pages'			=> $all_pages,
	'all_email_fields'	=> $all_email_fields,
	'all_text_fields'	=> $all_text_fields,
	
	// variables from framework
	'PAGE_ID'		=> (int) $page_id,
	'SECTION_ID'	=> (int) $section_id,
	'ADMIN_URL'		=> ADMIN_URL,
	'LEPTON_URL'	=> LEPTON_URL,
	'MOD_CLASS'		=> strtolower(basename(dirname(__FILE__))),
	'MODULE_URL'	=> LEPTON_URL . "/modules/mpform",

	// variables from global language files
	'TXT_SAVE'			=> $TEXT['SAVE'],
	'TXT_CANCEL'		=> $TEXT['CANCEL'],
	'is_following_true_checked'		=> (($settings['is_following']==true) ? 'checked="checked"' : ''),
	'is_following_false_checked'	=> (($settings['is_following']==true) ? '' : 'checked="checked"'),
	'txt_header'					=> $TEXT['HEADER'],
	'txt_field_loop'				=> $TEXT['FIELD'].' '.$TEXT['LOOP'],
	'txt_footer'					=> $TEXT['FOOTER'],
	'email_from'					=> ((substr($settings['email_from'], 0, 5) != 'field') && ($settings['email_from'] != 'wbu') ? $settings['email_from'] : ''),
	'des_email_from_field'			=> '',
	'email_fromname'				=> ((substr($settings['email_fromname'], 0, 5) != 'field') && ($settings['email_fromname'] != 'wbu') ? $settings['email_fromname'] : ''),
	'des_email_fromname_field'		=> '',
	'des_email_fromname'			=> '',
	'des_email_subject'				=> '',
	'txt_email_subject'				=> $TEXT['SUBJECT'],
	'des_success_email_to'			=> '',
	'des_success_email_from'		=> '',
	'des_success_email_fromname'	=> '',
	'des_success_email_subject'		=> '',
	'txt_success_email_subject'		=> $TEXT['SUBJECT'],
	'txt_success_email_text'		=> $TEXT['TEXT'],
	'txt_email_text' 	=> $TEXT['TEXT'],
	'NONEN'				=> $TEXT['NONE'],
	'TXT_YES'			=> $TEXT['YES'],
	'TXT_NO'			=> $TEXT['NO'],
	// module settings
	'MOD_SAVE_URL'		=> LEPTON_URL. str_replace("\\","/",substr(dirname(__FILE__),strlen(LEPTON_PATH))).'/save_settings.php',
	'MOD_CANCEL_URL'	=> ADMIN_URL.'/pages/modify.php?page_id='.$page_id
);

function give_me_address_list($curr_value, $java=true, $fname = '', $wbt){	// returns list of email fields from the form
	global $database, $section_id, $TEXT, $tpl;
	$rt = false;
	
	// add authenticated user:
	$s = "<option value=\"wbu\"";
	if($curr_value == 'wbu') {
		$s .= " selected='selected'";
		$rt = true;
	}
	if ($java) $s .= " onclick=\"javascript: document.getElementById('". $fname."_slave').style.display = 'none';\"";
	$s .= ">$wbt</option>";
	$s .= '';

	$query_email_fields = $database->query("SELECT `field_id`,`title` FROM `".TABLE_PREFIX."mod_mpform_fields` WHERE `section_id` = '$section_id' AND (`type` = 'email') ORDER BY `position` ASC");
	if($query_email_fields->numRows() > 0) {
		while($field = $query_email_fields->fetchRow()) {
			$s = "<option value=\"field".$field['field_id']."\"";
			if($curr_value == 'field'.$field['field_id']) {
				$s .= " selected='selected'";
				$rt = true;
			}
			if ($java) $s .= " onclick=\"javascript: document.getElementById('". $fname."_slave').style.display = 'none';\"";
			$s .= ">".$TEXT['FIELD'].': '.$field['title']. "</option>";
		}
	}
	return $rt;
}

function give_me_name_list($curr_value, $java=true, $fname = '', $wbt){  // returns list of text fields from the form
	global $database, $section_id, $TEXT, $tpl;
	// $tpl->set_block('main_block', $fname.'_block' , $fname);
	$rt = false;
	
	// add authenticated user:
	$s = "<option value=\"wbu\"";
	if($curr_value == 'wbu') {
		$s .= " selected='selected'";
		$rt = true;
	}
	if ($java) $s .= " onclick=\"javascript: document.getElementById('". $fname."_slave').style.display = 'none';\"";
	$s .= ">$wbt</option>";
	$s = '';
	
	$query_email_fields = $database->query("SELECT `field_id`,`title` FROM `".TABLE_PREFIX."mod_mpform_fields` WHERE `section_id` = '$section_id' AND (`type` = 'textfield') ORDER BY `position` ASC");
	if($query_email_fields->numRows() > 0) {
		while($field = $query_email_fields->fetchRow()) {
			$s = "<option value=\"field".$field['field_id']."\"";
			if($curr_value == 'field'.$field['field_id']) {
				$s .= " selected='selected'";
				$rt = true;
			}
			if ($java) $s .= " onclick=\"javascript: document.getElementById('". $fname."_slave').style.display = 'none';\"";
			$s .= ">".$TEXT['FIELD'].': '.$field['title']. "</option>";
		}
	}
	return $rt;
}

function give_me_pages_list($page, $fname){	// returns list of possible success pages
	global $database, $admin, $tpl;
	// $tpl->set_block('main_block', $fname.'_block' , 'schleife');
	$s = '';
	// Get exisiting pages and show the pagenames
	$query = $database->query("SELECT * FROM `".TABLE_PREFIX."pages` WHERE `visibility` <> 'deleted'");
	while($mail_page = $query->fetchRow()) {
		if(!$admin->page_is_visible($mail_page)) continue;
		$mail_pagename = $mail_page['menu_title'];
		$success_page = $mail_page['page_id'];
		//$rt .= $success_page.':'.$success_page.':';
		if($page == $success_page) {
			$selected = ' selected="selected"';
		} else {
			$selected = '';
		}
		$s = '<option value="'.$success_page.'"'.$selected.'>'.$mail_pagename.'</option>';
		// $tpl->set_var('options_'.$fname, $s);
  		// $tpl->parse('schleife', $fname.'_block', true);
	} 
}

// fill some fields with lists
$rt1 = give_me_address_list($email_from_value, true, 'email_from_f', $MOD_MPFORM['backend']['TXT_USER_ADDR']);
// die( LEPTON_tools::display( $rt1 ));
// $tpl->set_var('display_email_from_field', (($rt1) ? 'none' : 'block'));
$form_values['display_email_from_field'] = ($settings['email_from'] === 'wbu') ? 'none' :'block';//  (($rt1) ? 'none' : 'block');

$rt2 = give_me_name_list($email_fromname_value, true, 'email_fromname_f', $MOD_MPFORM['backend']['TXT_USER_NAME']);
// $tpl->set_var('display_email_fromname_field', (($rt2) ? 'none' : 'block'));
$form_values['display_email_fromname_field'] = (($rt2) ? 'none' : 'block');

give_me_address_list($settings['success_email_to'], false, 'success_email_to', $MOD_MPFORM['backend']['TXT_USER_ADDR']);
give_me_pages_list($settings['success_page'],'success_page');

//	Get the templatge-engine
$oTWIG = lib_twig_box::getInstance();
$oTWIG->registerModule( $mod_dir );

echo $oTWIG->render(
	'@mpform/backend_modify_settings.lte',
	$form_values
);
// Print admin footer
$admin->print_footer();
?>