<?php
/* 
 * CMS module: MPForm
 * For more information see info.php
 * 
 * This file prints the main form of the module in the backend.
 * This file is (c) 2009 Website Baker Project <http://www.websitebaker.org/>
 * Improvements are copyright (c) 2009-2011 Frank Heyne
*/

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die(header('Location: ../../index.php'));

// obtain module directory
$mod_dir = basename(dirname(__FILE__));
require(WB_PATH.'/modules/'.$mod_dir.'/info.php');

// include module.functions.php (introduced with WB 2.7)
@include_once(WB_PATH . '/framework/module.functions.php');

// include the module language file depending on the backend language of the current user
if (!include(get_module_language_file($mod_dir))) return;

//print_r (get_defined_constants()); #########

//Delete all form fields with no title
$database->query("DELETE FROM `".TABLE_PREFIX."mod_mpform_fields`  WHERE `page_id` = '$page_id' and `section_id` = '$section_id' and `title` = '';");

// include template parser class and set template
require_once(WB_PATH . '/include/phplib/template.inc');
$tpl = new Template(dirname(__FILE__) . '/htt/');
// define how to handle unknown variables (default:='remove', during development use 'keep' or 'comment')
$tpl->set_unknowns('keep');

// define debug mode (default:=0 (disabled), 1:=variable assignments, 2:=calls to get variable, 4:=show internals)
$tpl->debug = 0;

$tpl->set_file('page', 'backend_modify.htt');
$tpl->set_block('page', 'main_block', 'main');

// ensure that page and section id are numeric
$page_id = (isset($page_id)) ? (int) $page_id : '';
$section_id = (isset($section_id)) ? (int) $section_id : '';

if (substr(VERSION, 0, 3) == "2.7") {
	$imgurl = ADMIN_URL . '/images/';
} else {
	$imgurl = THEME_URL . '/images/';
}

$tpl->set_var(
	array(
		// variables from Website Baker framework
		'PAGE_ID'		=> (int) $page_id,
		'SECTION_ID'	=> (int) $section_id,
		'IMG_URL'		=> $imgurl,
		'WB_URL'		=> WB_URL,
		'LANGUAGE'		=> ((file_exists(WB_PATH .'/modules/'.$mod_dir.'/help.' . LANGUAGE .'.php')) ? LANGUAGE : 'EN'),
		'MODULE_URL'    => WB_URL.'/modules/'.$mod_dir,
		//'FTAN'		=> (WB_VERSION >= "2.8.2") ? $admin->getFTAN() : '',
		'FTAN'			=> '',
		
		// variables from global WB language files
		'TXT_SAVE'		=> $TEXT['SAVE'],
		'TXT_CANCEL'	=> $TEXT['CANCEL'],
		'TXT_HELP'		=> $MENU['HELP'],
		'TEXT_HEADING_F'=> $TEXT['MODIFY'].'/'.$TEXT['DELETE'].' '.$TEXT['FIELD'] ,
		'TEXT_HEADING_S'=> $TEXT['SUBMISSIONS'], 
		'TEXT_DELETE'	=> $TEXT['DELETE'],
		'TEXT_ARE_YOU_SURE' => str_replace(' ', '%20', $TEXT['ARE_YOU_SURE']),
		'TEXT_FIELD'	=> $TEXT['FIELD'],
		'TEXT_MOVE_UP'	=> $TEXT['MOVE_UP'],
		'TEXT_MOVE_DOWN'=> $TEXT['MOVE_DOWN'],
		'TEXT_SUBMISSION_ID'	=> $TEXT['SUBMISSION_ID'],
		'TEXT_SUBMITTED'=> $TEXT['SUBMITTED'],
		'TEXT_OPEN'		=> $TEXT['OPEN'],

		// module settings
		'TXT_HEADING'	=> $module_name,
		'MODULE_DIR'    => $mod_dir,
  		'MOD_CANCEL_URL'=> ADMIN_URL,
		'TEXT_TYPE'		=> $LANG['backend']['TXT_TYP'],
		'TXT_ADV_SETTINGS'	=> $LANG['backend_adv']['adv_settings'],
		'TXT_FIELDS'	=> $LANG['backend']['TXT_ADD_FIELD'],
		'TXT_SETTINGS'	=> $LANG['backend']['TXT_SETTINGS'],
		'EDIT_CSS'		=> $LANG['backend']['TXT_EDIT_CSS']
	)
);

// Include the ordering class
require_once(WB_PATH.'/framework/class.order.php');
// Create new order object an reorder
$order = new order(TABLE_PREFIX.'mod_mpform_fields', 'position', 'field_id', 'section_id');
$order->clean($section_id);
require_once(WB_PATH.'/modules/'.$mod_dir.'/functions.php');
$tpl->set_block('main_block', 'field_block' , 'field_loop');

// Loop through existing fields
$query_fields = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_mpform_fields` WHERE section_id = '$section_id' ORDER BY position ASC");
if($query_fields->numRows() > 0) {
	$num_fields = $query_fields->numRows();
	$pos = 0;
	$row = 'row_a';
	while($field = $query_fields->fetchRow()) {
		$pos++;		

		// alternate row color
		if($row == 'row_a') {
			$row = 'row_b';
			$rowcolor = '#ECF3F7';
		} else {
			$row = 'row_a';
			$rowcolor = '#DBEBF2';
		}

		switch ($field['type']){
			case 'textfield':
				$rt = $TEXT['SHORT_TEXT'];
				break;
			case 'textarea':
				$rt = $TEXT['LONG_TEXT'];
				break;
			case 'heading':
				$rt = $TEXT['HEADING'];
				break;
			case 'select':
				$rt = $TEXT['SELECT_BOX'];
				break;
			case 'checkbox':
				$rt = $TEXT['CHECKBOX_GROUP'];
				break;
			case 'radio':
				$rt = $TEXT['RADIO_BUTTON_GROUP'];
				break;
			case 'filename':
				$rt = $TEXT['UPLOAD_FILES'];
				break;
			case 'date';
				$rt =  $TEXT['DATE'];
				break;
			case 'email':
				$rt = $TEXT['EMAIL_ADDRESS'];
				break;
			case 'fieldset_start':
				$rt = $LANG['backend']['fieldset_start'];
				break;
			case 'fieldset_end':
				$rt = $LANG['backend']['fieldset_end'];
				break;
			case 'integer_number':
				$rt = $LANG['backend']['integer_number'];
				break;
			case 'decimal_number':
				$rt = $LANG['backend']['decimal_number'];
				break;
			case 'email_recip':
				$rt = $LANG['backend']['email_recip'];
				break;
			case 'email_subj':
				$rt = $LANG['backend']['email_subj'];
				break;
			case 'html':
				$rt = $LANG['backend']['HTML'];
				break;
			default:
				 $rt = '';
		}
		$multiselect_field='';
		if ($field['type'] == 'select') {
			$field['extra'] = explode(',',$field['extra']);
			$multiselect_txt = $TEXT['MULTISELECT'] .': '.(($field['extra'][1] == 'multiple') ? $TEXT['YES'] : $TEXT['NO']);
			$multiselect_img = WB_URL.'/modules/'.$mod_dir.'/images/'. (($field['extra'][1] == 'multiple') ? "mehrfach.gif" : "einfach.gif");
			$multiselect_field = "<img src='$multiselect_img' border='0' alt='$multiselect_txt' title='$multiselect_txt' />";
		}
		
		if ($field['required'] == 1) {
			$entry = $LANG['backend']['compulsory_entry'];
			$entrytype = "<img src='" .WB_URL. "/modules/$mod_dir/images/compulsory.gif' border='0' alt='$entry' title='$entry' />";
		} elseif ($field['required'] == 0) {
			$entry = $LANG['backend']['optional_entry'];
			$entrytype = "<img src='" .WB_URL. "/modules/$mod_dir/images/optional.gif' border='0' alt='$entry' title='$entry' />";
		} elseif ($field['required'] == 2) {
			$entry = $LANG['backend']['ro_entry'];
			$entrytype = "<img src='" .WB_URL. "/modules/$mod_dir/images/readonly.gif' border='0' alt='$entry' title='$entry' />";
		} else {
			$entrytype = '&nbsp;';
		} 
		
		// set vars for this field
		$tpl->set_var(
			array(
				'FIELD_ID'			=> (WB_VERSION >= "2.8.2") ? $admin->getIDKEY($field['field_id']) : $field['field_id'],
				'MUVE_UP_STYLE'		=> (($pos != 1) ? '' : 'style="display:none"'),
				'MUVE_DOWN_STYLE'	=> (($pos != $num_fields) ? '' : 'style="display:none"'),
				'ROW_CLASS'			=> $row,
				'ROW_COLOR'			=> $rowcolor,
				'field_field_title'	=> $field['title'],
				'field_title'		=> $field['title'],
				'type_field'		=> $rt,
				'entrytype'			=> $entrytype,		
				'multiselect_field'	=> $multiselect_field,
			)
		);
		$tpl->parse('field_loop', 'field_block', true);
	}
} else {
	$tpl->set_var('field_loop',$TEXT['NONE_FOUND']);
}

$tpl->set_block('main_block', 'submission_block' , 'submission_loop');

// Query submissions table
$query_submissions = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_mpform_submissions` WHERE section_id = '$section_id' ORDER BY submitted_when ASC");
if($query_submissions->numRows() > 0) {
	// List submissions
	$row = 'row_a';
	while($submission = $query_submissions->fetchRow()) {

		// Alternate row color
		if($row == 'row_a') {
			$row = 'row_b';
			$rowcolor = '#ECF3F7';
		} else {
			$row = 'row_a';
			$rowcolor = '#DBEBF2';
		}

		$tpl->set_var(
			array(
				'SUBMISSION_ID'	=> (WB_VERSION >= "2.8.2") ? $admin->getIDKEY($submission['submission_id']) : $submission['submission_id'],
				'ROW_CLASS'		=> $row,
				'ROW_COLOR'		=> $rowcolor,
				'field_submission_id'	=> $submission['submission_id'],
				'field_submission_when'	=> date(TIME_FORMAT.', '.DATE_FORMAT, $submission['submitted_when']),
			)
		);

		$tpl->parse('submission_loop', 'submission_block', true);
	}
} else {
	$tpl->set_var('submission_loop','<tr><td>'.$TEXT['NONE_FOUND'].'</td></tr>');
}
// Parse template objects output
$tpl->parse('main', 'main_block', false);
$tpl->pparse('output', 'page', false, false);

insert_drag_drop(5);
?>