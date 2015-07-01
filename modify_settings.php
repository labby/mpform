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

// Include WB admin wrapper script
$admin_header = false;
require(LEPTON_PATH.'/modules/admin.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

$MOD_MPFORM = (dirname(__FILE__))."/languages/". LANGUAGE .".php";
require_once ( !file_exists($MOD_MPFORM) ? (dirname(__FILE__))."/languages/EN.php" : $MOD_MPFORM );

//	START HEADER HERE
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

$tpl->set_file('page', 'backend_modify_settings.htt');
$tpl->set_block('page', 'main_block', 'main');

// replace all placeholder {xxx} of the template file with values from language file
foreach($MOD_MPFORM['backend'] as $key => $value) {
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

$email_from_value = $setting['email_from'];
$email_fromname_value = $setting['email_fromname']; 

// replace static template placeholders with values from language file
$tpl->set_var(
	array(
		// variables from Website Baker framework
		'PAGE_ID'			=> (int) $page_id,
		'SECTION_ID'		=> (int) $section_id,
		'ADMIN_URL'			=> ADMIN_URL,
		'LEPTON_URL'			=> LEPTON_URL,
		'MOD_CLASS'			=> strtolower(basename(dirname(__FILE__))),
		'MODULE_URL'		=> LEPTON_URL . "/modules/mpform",

		// variables from global WB language files
		'TXT_SAVE'			=> $TEXT['SAVE'],
		'TXT_CANCEL'		=> $TEXT['CANCEL'],

		'is_following_true_checked'			=> (($settings['is_following']==true) ? 'checked="checked"' : ''),
		'is_following_false_checked'		=> (($settings['is_following']==true) ? '' : 'checked="checked"'),
		'txt_header'                        => $TEXT['HEADER'],
		'txt_field_loop'                    => $TEXT['FIELD'].' '.$TEXT['LOOP'],
		'txt_footer'                        => $TEXT['FOOTER'],
		'email_from' 		                => ((substr($settings['email_from'], 0, 5) != 'field') && ($settings['email_from'] != 'wbu') ? $settings['email_from'] : ''),
		'des_email_from_field'     	        => '',
		'email_fromname'	              	=> ((substr($settings['email_fromname'], 0, 5) != 'field') && ($settings['email_fromname'] != 'wbu') ? $settings['email_fromname'] : ''),
		'des_email_fromname_field'          => '',
		'des_email_fromname'                => '',
		'des_email_subject'         		=> '',
		'txt_email_subject'         		=> $TEXT['SUBJECT'],
		'des_success_email_to'  	   	    => '',
		'des_success_email_from'     	    => '',
		'des_success_email_fromname'  	    => '',
		'des_success_email_subject'     	=> '',
		'txt_success_email_subject'     	=> $TEXT['SUBJECT'],
		'txt_success_email_text'         	=> $TEXT['TEXT'],
		'txt_email_text' 	=> $TEXT['TEXT'],
		'NONE'				=> $TEXT['NONE'],
		'TXT_YES'			=> $TEXT['YES'],
		'TXT_NO'			=> $TEXT['NO'],
		// module settings
		'MOD_SAVE_URL'						=> LEPTON_URL. str_replace("\\","/",substr(dirname(__FILE__),strlen(LEPTON_PATH))).'/save_settings.php',
		'MOD_CANCEL_URL'					=> ADMIN_URL.'/pages/modify.php?page_id='.$page_id
	)
);

function give_me_address_list($curr_value, $java=true, $fname = '', $wbt){	// returns list of email fields from the form
	global $database, $section_id, $TEXT, $tpl;
	$tpl->set_block('main_block', $fname.'_block' , $fname);
	$rt = false;
	
	// add authenticated user:
	$s = "<option value=\"wbu\"";
	if($curr_value == 'wbu') {
		$s .= " selected='selected'";
		$rt = true;
	}
	if ($java) $s .= " onclick=\"javascript: document.getElementById('". $fname."_slave').style.display = 'none';\"";
	$s .= ">$wbt</option>";
	$tpl->set_var('options_'.$fname, $s);
	$tpl->parse($fname, $fname.'_block', true);
	$s = '';

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
			$tpl->set_var('options_'.$fname, $s);
      		$tpl->parse($fname, $fname.'_block', true);
		}
	} else {
		$tpl->set_var('options_'.$fname, $s);
		$tpl->parse($fname, $fname.'_block', true);
	}
	return $rt;
}

function give_me_name_list($curr_value, $java=true, $fname = '', $wbt){  // returns list of text fields from the form
	global $database, $section_id, $TEXT, $tpl;
	$tpl->set_block('main_block', $fname.'_block' , $fname);
	$rt = false;
	
	// add authenticated user:
	$s = "<option value=\"wbu\"";
	if($curr_value == 'wbu') {
		$s .= " selected='selected'";
		$rt = true;
	}
	if ($java) $s .= " onclick=\"javascript: document.getElementById('". $fname."_slave').style.display = 'none';\"";
	$s .= ">$wbt</option>";
	$tpl->set_var('options_'.$fname, $s);
	$tpl->parse($fname, $fname.'_block', true);
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
			$tpl->set_var('options_'.$fname, $s);
      		$tpl->parse($fname, $fname.'_block', true);
		}
	} else {
		$tpl->set_var('options_'.$fname, $s);
		$tpl->parse($fname, $fname.'_block', true);
	}
	return $rt;
}

function give_me_pages_list($page, $fname){	// returns list of possible success pages
	global $database, $admin, $tpl;
	$tpl->set_block('main_block', $fname.'_block' , 'schleife');
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
		$tpl->set_var('options_'.$fname, $s);
  		$tpl->parse('schleife', $fname.'_block', true);
	} 
}

// fill some fields with lists
$rt1 = give_me_address_list($email_from_value, true, 'email_from_f', $MOD_MPFORM['backend']['TXT_USER_ADDR']);
$tpl->set_var('display_email_from_field', (($rt1) ? 'none' : 'block'));
$rt2 = give_me_name_list($email_fromname_value, true, 'email_fromname_f', $MOD_MPFORM['backend']['TXT_USER_NAME']);
$tpl->set_var('display_email_fromname_field', (($rt2) ? 'none' : 'block'));
give_me_address_list($settings['success_email_to'], false, 'success_email_to', $MOD_MPFORM['backend']['TXT_USER_ADDR']);
give_me_pages_list($settings['success_page'],'success_page');

// Parse template objects output
$tpl->parse('main', 'main_block', false);
$tpl->pparse('output', 'page',false, false);
echo "&nbsp;";
$admin->print_footer();
?>