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

// Get id

if(!isset($_GET['field_id']) OR !is_numeric($_GET['field_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
	exit(0);
} else {
	$field_id = $_GET['field_id'];
}

// Get header and footer
$form = array();
$database->execute_query(
	"SELECT * FROM `".TABLE_PREFIX."mod_mpform_fields` WHERE `field_id` = '".$field_id."'",
	true,
	$form,
	false
);

$type = ($form['type'] != "")
	? $form['type']
	: 'none'
	;

// protect from cross page reading
if ($form['page_id'] != $page_id) {  
	header("Location: ".ADMIN_URL."/pages/index.php");
	exit(0);
}

$field_typtes = array(
	"heading"	=> $TEXT['HEADING'],
	"fieldset_start"	=> $MOD_MPFORM['backend']['fieldset_start'],
	"fieldset_end"	=> $MOD_MPFORM['backend']['fieldset_end'],
	"textfield"	=> $MOD_MPFORM['backend']["textfield"],
	"textarea"	=> $MOD_MPFORM['backend']["textarea"],
	"select"	=> $TEXT['SELECT_BOX'],
	"checkbox"	=> $TEXT['CHECKBOX_GROUP'],
	"radio"	=> $TEXT['RADIO_BUTTON_GROUP'],
	"email"	=> $TEXT['EMAIL_ADDRESS'],
	"email_recip"	=> $MOD_MPFORM['backend']['email_recip'],
	"email_subj"	=> $MOD_MPFORM['backend']['email_subj'],
	"date"	=> $MOD_MPFORM['backend']['date'],
	"filename"	=> $MOD_MPFORM['backend']['fileupload'],
	"integer_number"	=> $MOD_MPFORM['backend']['integer_number'],
	"decimal_number"	=> $MOD_MPFORM['backend']['decimal_number'],
	"html"	=> $MOD_MPFORM['backend']['HTML'] 
);

// show additional fields depending on type
$fieldtypeoption = "";
// first round:
switch ($type) {
	case 'heading':
		$fieldtypeoption = "<tr>\n"
		."<td valign='top'>". $TEXT['TEMPLATE'] ."</td>\n"
		."<td><textarea class='mp_options' name='template'  cols='50' rows='5' style='width: 98%; height: 200px;'>". $form['extra'] ."</textarea></td>\n"
		."</tr>\n";
		$form['required'] = 3;
		break;
	case 'textfield':
	case 'integer_number':
	case 'decimal_number':
	case 'email_subj':
		$fieldtypeoption = "<tr>\n"
		."<td>". $TEXT['LENGTH'] .":</td>\n"
		."<td><input class='mp_options' type='text' name='length' value='". $form['extra'] ."' style='width: 98%;' maxlength='3' /></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td>". $MOD_MPFORM['backend']['TXT_DEFAULT'] .":</td>\n"
		."<td><input class='mp_options' type='text' name='value' value='". $form['value'] ."' style='width: 98%;' /></td>\n"
		."</tr>\n";
		break;
	case 'textarea':
		$cr = explode(',', $form['extra']);
		if (isset($cr[0]) and is_numeric($cr[0])) {
			$cols = $cr[0];
		} else {
			$cols = 25;
		}
		if (isset($cr[1]) and is_numeric($cr[1])) {
			$rows = $cr[1];
		} else {
			$rows = 5;
		}
		$fieldtypeoption = "<tr>\n"
		."<td>". $TEXT['WIDTH'] .":</td>\n"
		."<td><input class='mp_options' type='text' name='width' value='$cols' style='width: 98%;' maxlength='3' /></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td>". $MOD_MPFORM['backend']['ROWS'] .":</td>\n"
		."<td><input class='mp_options' type='text' name='rows' value='$rows' style='width: 98%;' maxlength='3' /></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td>". $MOD_MPFORM['backend']['TXT_DEFAULT'] .":</td>\n"
		."<td><textarea class='mp_options' name='value' cols='50' rows='5' style='width: 98%; height: 100px;'>". $form['value'] ."</textarea></td>\n"
		."</tr>\n";
		break;
	case 'html':
		$fieldtypeoption = "<tr>\n"
		."<td>". $MOD_MPFORM['backend']['HTML'] .":</td>\n"
		."<td><textarea class='mp_options' name='value' cols='80' rows='8' style='width: 98%; height: 200px;'>". $form['value'] ."</textarea></td>\n"
		."</tr>\n";
		$form['required'] = 3;
		break;
	case 'date':
		$fieldtypeoption = "<tr>\n"
		."<td>". $TEXT['LENGTH'] .":</td>\n"
		."<td><input class='mp_options' type='text' name='length' value='". $form['extra'] ."' style='width: 98%;' maxlength='3' /></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td>". $MOD_MPFORM['backend']['TXT_DEFAULT'] .":</td>\n"
		."<td><input class='mp_options' type='text' name='value' value='". $form['value'] ."' style='width: 98%;' /></td>\n"
		."</tr>\n";
		break;
	case 'email':
		$fieldtypeoption = "<tr>\n"
		."<td>". $TEXT['LENGTH'] .":</td>\n"
		."<td><input class='mp_options' type='text' name='length' value='". $form['extra'] ."' style='width: 98%;' maxlength='3' /></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td>". $MOD_MPFORM['backend']['TXT_DEFAULT'] .":</td>\n"
		."<td><input class='mp_options' type='text' name='value' value='". $form['value'] ."' style='width: 98%;' /></td>\n"
		."</tr>\n";
		break;
	case 'filename':
		$fieldtypeoption = "<tr>\n"
		."<td>". $TEXT['LENGTH'] .":</td>\n"
		."<td><input class='mp_options' type='text' name='length' value='". $form['extra'] ."' style='width: 98%;' maxlength='3' /></td>\n"
		."</tr>\n";
		break;
	case 'select':
 	case 'email_recip':	
	case 'checkbox':
	case 'radio':
		($type == 'radio') ? $kind = 'radio' : $kind = 'checkbox';
		$fieldtypeoption = "<tr>\n"
		//."<tr>\n"
		."<td valign='top'>". $MOD_MPFORM['backend']['TXT_LIST'] .":</td>\n"
		."<td>";
			
		$option_count = 0;
		$list = explode(',', $form['value']);
		foreach($list AS $option_value) {
			$def = strpos($option_value, IS_DEFAULT);
			if ($def > 0) {
				$ovalue = substr($option_value, 0, $def);
				$cv = " checked='checked'";
			} else {
				$ovalue = $option_value;
				$cv = "";
			}
			$option_count = $option_count+1;
			($type == 'radio') ? $isdef = "isdefault" : $isdef = "isdefault$option_count";
			$fieldtypeoption .= '<table cellpadding="3" cellspacing="0" width="100%" border="0">'
				."<tr>\n<td width='70'>". $TEXT['OPTION'].' '.$option_count .":</td>\n"
				."<td><input class='mp_options' type='text' name='value$option_count' value='$ovalue' style='width: 250px;' /> "
				."<input class='mp_options' type='$kind' name='$isdef' value='$option_count' $cv /></td>\n"
				."</tr></table>\n";
		}
		for($i = 0; $i < 2; $i++) {
			$option_count = $option_count+1;
			($type == 'radio') ? $isdef = "isdefault" : $isdef = "isdefault$option_count";
			$fieldtypeoption .= '<table cellpadding="3" cellspacing="0" width="100%" border="0">'
				."<tr>\n<td width='70'>". $TEXT['OPTION'].' '.$option_count .":</td>\n"
				."<td><input class='mp_options' type='text' name='value$option_count' value='' style='width: 250px;' /> "
				."<input class='mp_options' type='$kind' name='$isdef' value='$option_count' /></td>\n"
				."</tr></table>\n";
			}
		$fieldtypeoption .= "<input class='mp_options' type='hidden' name='list_count' value='$option_count' /></td>\n</tr>\n";
		break;
	case 'fieldsetstart':
	case 'fieldsetend':
	case 'none':
		$form['required'] = 3;

}  // switch ($type)

// second round:
if($type == 'select') {
	$form['extra'] = explode(',',$form['extra']);
	$fieldtypeoption .= "<tr>\n<td>". $TEXT['SIZE'] .":</td>\n";
	$fieldtypeoption .= '<td><input class="mp_options" type="text" name="size" value="'. trim($form['extra'][0]) .'" style="width: 98%;" maxlength="3" /></td>';
	$fieldtypeoption .= "\n</tr>\n<tr>\n";
	$fieldtypeoption .= "<td>". $TEXT['ALLOW_MULTIPLE_SELECTIONS'] .":</td>\n";
	$fieldtypeoption .= '<td><input class="mp_options" type="radio" name="multiselect" id="multiselect_true" value="multiple"';
	if($form['extra'][1] == 'multiple') $fieldtypeoption .= ' checked="checked"';
	$fieldtypeoption .= " />";
	$fieldtypeoption .= '<a href="#" onclick="javascript: document.getElementById(\'multiselect_true\').checked = true;">';
	$fieldtypeoption .= $TEXT['YES'] ."</a>	&nbsp; ";
	$fieldtypeoption .= '<input class="mp_options" type="radio" name="multiselect" id="multiselect_false" value=""';
	if($form['extra'][1] == '') $fieldtypeoption .= ' checked="checked"';
	$fieldtypeoption .= " />";
	$fieldtypeoption .= '<a href="#" onclick="javascript: document.getElementById(\'multiselect_false\').checked = true;">';
	$fieldtypeoption .= $TEXT['NO'] ."</a></td>\n</tr>\n";
}
if($type == 'checkbox' OR $type == 'radio') { 
	$fieldtypeoption .= "<tr>\n<td valign='top'>". $TEXT['SEPERATOR'] .":</td>\n";
	$fieldtypeoption .= '<td><input class="mp_options" type="text" name="seperator" value="'. $form['extra'] .'" style="width: 98%;" />'."</td>\n</tr>\n";
}
if($type != 'heading' AND $type != 'fieldset_start' AND $type != 'fieldset_end' AND $type != 'none' AND $type != 'html') { 
	$fieldtypeoption .= "<tr>\n<td>". $MOD_MPFORM['backend']['entry'] .":</td>\n";
	$fieldtypeoption .= '<td><input class="mp_options" type="radio" name="required" id="required_true" value="1"';
	if($form['required'] == 1 OR $type == 'email_recip') $fieldtypeoption .= ' checked="checked"';
	$fieldtypeoption .= " />";
	$fieldtypeoption .= '<a href="#" onclick="javascript: document.getElementById(\'required_true\').checked = true;">';
	$fieldtypeoption .= $MOD_MPFORM['backend']['compulsory_entry'] ."</a>	&nbsp; ";
	$fieldtypeoption .= '<input class="mp_options" type="radio" name="required" id="required_false" value="0"';
	if($form['required'] == 0 AND $type != 'email_recip') $fieldtypeoption .= ' checked="checked"';
	$fieldtypeoption .= " />";
	$fieldtypeoption .= '<a href="#" onclick="javascript: document.getElementById(\'required_false\').checked = true;">';
	$fieldtypeoption .= $MOD_MPFORM['backend']['optional_entry'] ."</a>	&nbsp; ";
	$fieldtypeoption .= '<input class="mp_options" type="radio" name="required" id="required_ro" value="2"';
	if($form['required'] == 2 AND $type != 'email_recip') $fieldtypeoption .= ' checked="checked"';
	$fieldtypeoption .= " />";
	$fieldtypeoption .= '<a href="#" onclick="javascript: document.getElementById(\'required_ro\').checked = true;">';
	$fieldtypeoption .= $MOD_MPFORM['backend']['ro_entry'] ."</a></td>\n</tr>\n";
	$fieldtypeoption .= "<tr><td>&nbsp;</td></tr><tr class='helper'>\n<td valign='top'>". $MENU['HELP'] .":</td>\n";
	$fieldtypeoption .= '<td><textarea class="mp_options" name="help"  cols="50" rows="5" style="width: 98%; height: 100px;">'. $form['help'] ."</textarea></td>\n</tr>\n";
}


$form_values = array(
	// variables from framework
	'MOD_MPFORM'	=> $MOD_MPFORM,
	'PAGE_ID'		=> (int) $page_id,
	'SECTION_ID'	=> (int) $section_id,
	'FIELD_ID'		=> (int) $field_id,
	'LEPTON_URL'	=> LEPTON_URL,
	'ADMIN_URL'		=> ADMIN_URL,
	'TXT_SAVE'		=> $TEXT['SAVE'],
	'TXT_CANCEL'	=> $TEXT['CANCEL'],
	'TXT_TITLE'		=> $TEXT['TITLE'],
	'TXT_PLEASE_SELECT'	=> $TEXT['PLEASE_SELECT'],
	'MODULE_URL'    => LEPTON_URL.'/modules/'.$mod_dir,
		
	// module settings
	'MODULE_DIR'    => $mod_dir,
	'TXT_TYPE'		=> $MOD_MPFORM['backend']['TXT_TYP'],
	'TXT_COPY_FIELD'=> $MOD_MPFORM['backend']['TXT_COPY_FIELD'],
	'TXT_ADD_FIELD'	=> $MOD_MPFORM['backend']['TXT_ADD_FIELD'],
	'TXT_MODIFY_FIELD'	=> sprintf($MOD_MPFORM['backend']['TXT_MODIFY_FIELD'], $field_id),
	'VAL_TITLE'		=> $form['title'],
	
	'VAL_TYPE_OPTIONS'	=> $fieldtypeoption,
	
	'form'			=> $form,
	'field_typtes'	=> $field_typtes
);

echo $oTWIG->render(
	'@mpform/backend_modify_field.lte',
	$form_values
);
// Print admin footer
$admin->print_footer();

?>