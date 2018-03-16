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

global $MOD_MPFORM, $parser, $loader; 

$mod_dir = basename(dirname(__FILE__));
require_once LEPTON_PATH.'/modules/'.$mod_dir.'/info.php';
require_once LEPTON_PATH.'/modules/'.$mod_dir.'/register_language.php';


/** 
 *	Make sure that page and section id are numeric.
 */
$page_id = (isset($page_id)) ? intval($page_id) : 0;
$section_id = (isset($section_id)) ? intval($section_id ): 0;

/**
 *	Delete all form fields with no title
 */
$fields = array(
	'page_id' => $page_id,
	'section_id' => $section_id,
	'title' => ""
);

$database->prepare_and_execute(
	"DELETE FROM `".TABLE_PREFIX."mod_mpform_fields`  WHERE `page_id` = :page_id and `section_id` = :section_id and `title` = :title;",
	$fields
);

$imgurl= LEPTON_URL.'/templates/'.DEFAULT_THEME.'/images/';
$form_values = array(
		// variables from framework
		'PAGE_ID'		=> (int) $page_id,
		'SECTION_ID'	=> (int) $section_id,
		'IMG_URL'		=> $imgurl,
		'LEPTON_URL'	=> LEPTON_URL,
		'LANGUAGE'		=> ((file_exists(LEPTON_PATH .'/modules/'.$mod_dir.'/help.' . LANGUAGE .'.php')) ? LANGUAGE : 'EN'),
		'MODULE_URL'    => LEPTON_URL.'/modules/'.$mod_dir,
		
		// variables from global language files
		'TXT_SAVE'		=> $TEXT['SAVE'],
		'TXT_CANCEL'	=> $TEXT['CANCEL'],
		'TXT_HELP'		=> $MENU['HELP'],
		'TEXT_HEADING_F'=> $TEXT['MODIFY'].'/'.$TEXT['DELETE'].' '.$TEXT['FIELD'] ,
		'TEXT_HEADING_S'=> $TEXT['SUBMISSIONS'], 
		'TEXT_DELETE'	=> $TEXT['DELETE'],
		'TEXT_ARE_YOU_SURE' => str_replace(' ', '%20', $MOD_MPFORM['backend']['Are_you_sure']), // #1
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
		'TEXT_TYPE'		=> $MOD_MPFORM['backend']['TXT_TYP'],
		'TXT_ADV_SETTINGS'	=> $MOD_MPFORM['backend_adv']['adv_settings'],
		'TXT_FIELDS'	=> $MOD_MPFORM['backend']['TXT_ADD_FIELD'],
		'TXT_SETTINGS'	=> $MOD_MPFORM['backend']['TXT_SETTINGS'],
		'EDIT_CSS'		=> $MOD_MPFORM['backend']['TXT_EDIT_CSS']
);

// Include the ordering class
require_once LEPTON_PATH.'/framework/class.order.php';
// Create new order object an reorder
$order = new order(TABLE_PREFIX.'mod_mpform_fields', 'position', 'field_id', 'section_id');
$order->clean($section_id);
require_once LEPTON_PATH.'/modules/'.$mod_dir.'/functions.php';

// Loop through existing fields
$all_fields = array();
$database->execute_query(
	"SELECT * FROM `".TABLE_PREFIX."mod_mpform_fields` WHERE `section_id` = '".$section_id."' ORDER BY position ASC",
	true,
	$all_fields
);

$order_fields = array();
$num_fields = count($all_fields);

if( $num_fields > 0) {
	$pos = 0;

	foreach($all_fields as &$field) {
		$pos++;		

		// alternate row color
		if( $pos % 2 == 1 ) {
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
				$rt = $MOD_MPFORM['backend']['fieldset_start'];
				break;
			case 'fieldset_end':
				$rt = $MOD_MPFORM['backend']['fieldset_end'];
				break;
			case 'integer_number':
				$rt = $MOD_MPFORM['backend']['integer_number'];
				break;
			case 'decimal_number':
				$rt = $MOD_MPFORM['backend']['decimal_number'];
				break;
			case 'email_recip':
				$rt = $MOD_MPFORM['backend']['email_recip'];
				break;
			case 'email_subj':
				$rt = $MOD_MPFORM['backend']['email_subj'];
				break;
			case 'html':
				$rt = $MOD_MPFORM['backend']['HTML'];
				break;
			default:
				 $rt = '';
		}
		$multiselect_field='';
		if ($field['type'] == 'select') {
			$field['extra'] = explode(',',$field['extra']);
			$multiselect_txt = $TEXT['MULTISELECT'] .': '.(($field['extra'][1] == 'multiple') ? $TEXT['YES'] : $TEXT['NO']);
			$multiselect_img = LEPTON_URL.'/modules/'.$mod_dir.'/images/'. (($field['extra'][1] == 'multiple') ? "mehrfach.gif" : "einfach.gif");
			$multiselect_field = "<img src='$multiselect_img' border='0' alt='$multiselect_txt' title='$multiselect_txt' />";
		}
		
		if ($field['required'] == 1) {
			$entry = $MOD_MPFORM['backend']['compulsory_entry'];
			$entrytype = "<img src='" .LEPTON_URL. "/modules/$mod_dir/images/compulsory.gif' border='0' alt='$entry' title='$entry' />";
		} elseif ($field['required'] == 0) {
			$entry = $MOD_MPFORM['backend']['optional_entry'];
			$entrytype = "<img src='" .LEPTON_URL. "/modules/$mod_dir/images/optional.gif' border='0' alt='$entry' title='$entry' />";
		} elseif ($field['required'] == 2) {
			$entry = $MOD_MPFORM['backend']['ro_entry'];
			$entrytype = "<img src='" .LEPTON_URL. "/modules/$mod_dir/images/readonly.gif' border='0' alt='$entry' title='$entry' />";
		} else {
			$entrytype = '&nbsp;';
		} 
		
		// set vars for this field
		
		$order_fields[] = array(
				'FIELD_ID'			=> $field['field_id'],
				'MUVE_UP_STYLE'		=> (($pos != 1) ? '' : 'style="display:none"'),
				'MUVE_DOWN_STYLE'	=> (($pos != $num_fields) ? '' : 'style="display:none"'),
				'ROW_CLASS'			=> $row,
				'ROW_COLOR'			=> $rowcolor,
				'field_field_title'	=> $field['title'],
				'field_title'		=> $field['title'],
				'type_field'		=> $rt,
				'entrytype'			=> $entrytype,		
				'multiselect_field'	=> $multiselect_field
		);
	}
}

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
	$row = 'row_a';
	foreach($all_submissions as &$submission) {

		// Alternate row color
		if($row == 'row_a') {
			$row = 'row_b';
			$rowcolor = '#ECF3F7';
		} else {
			$row = 'row_a';
			$rowcolor = '#DBEBF2';
		}

		
		$order_submissions[]= array(
				'SUBMISSION_ID'	=> $submission['submission_id'],
				'ROW_CLASS'		=> $row,
				'ROW_COLOR'		=> $rowcolor,
				'field_submission_id'	=> $submission['submission_id'],
				'field_submission_when'	=> date(TIME_FORMAT.', '.DATE_FORMAT, $submission['submitted_when'])
		);
	}
}

$form_values["order_fields"]	= $order_fields;
$form_values["order_submissions"]	= $order_submissions;
$form_values["leptoken"]	= LEPTON_tools::get_leptoken();


$oTWIG = lib_twig_box::getInstance();
$oTWIG->registerModule( $mod_dir );

echo $oTWIG->render(
	'@mpform/backend_modify.lte',
	$form_values
);

insert_drag_drop(5);
?>