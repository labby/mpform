<?php
/* 
 * CMS module: MPForm
 * For more information see info.php
 * 
 * This file prints the settings FOR A FIELD OF THE FORM in the backend.
 * This file is (c) 2009 Website Baker Project <http://www.websitebaker.org/>
 * Improvements are copyright (c) 2009-2011 Frank Heyne
*/

// manually include the config.php file (defines the required constants)
require('../../config.php');
require(WB_PATH.'/modules/admin.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include module.functions.php (introduced with WB 2.7)
@include_once(WB_PATH . '/framework/module.functions.php');

// include the module language file depending on the backend language of the current user
if (!@include(get_module_language_file($mod_dir))) return;

//START HEADER HERE
require_once(WB_PATH.'/modules/'.$mod_dir.'/functions.php');
require_once(WB_PATH.'/modules/'.$mod_dir.'/constants.php');
module_header_footer($page_id, $mod_dir);
//END HEADER HERE

// Get id
if (WB_VERSION >= "2.8.2") {
	$field_id = $admin->checkIDKEY('field_id', false, 'GET');
	if (!$field_id) {
		$admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL);
		exit();
	}
} else {
	if(!isset($_GET['field_id']) OR !is_numeric($_GET['field_id'])) {
		header("Location: ".ADMIN_URL."/pages/index.php");
		exit(0);
	} else {
		$field_id = $_GET['field_id'];
	}
}

// Get header and footer
$query_content = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_fields WHERE field_id = '$field_id'");
$form = $query_content->fetchRow();
$type = $form['type'];
if($type == '') {
	$type = 'none';
}

// protect from cross page reading
if ($form['page_id'] != $page_id) {  
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

$tpl->set_file('page', 'backend_modify_field.htt');
$tpl->set_block('page', 'main_block', 'main');

// list possible field types
$tpl->set_block('main_block', 'field_block', 'field_loop');
$fieldtypes = array (
	"heading"	=> $TEXT['HEADING'],
	"fieldset_start"	=> $LANG['backend']['fieldset_start'],
	"fieldset_end"	=> $LANG['backend']['fieldset_end'],
	"textfield"	=> $LANG['backend']["textfield"],
	"textarea"	=> $LANG['backend']["textarea"],
	"select"	=> $TEXT['SELECT_BOX'],
	"checkbox"	=> $TEXT['CHECKBOX_GROUP'],
	"radio"	=> $TEXT['RADIO_BUTTON_GROUP'],
	"email"	=> $TEXT['EMAIL_ADDRESS'],
	"email_recip"	=> $LANG['backend']['email_recip'],
	"email_subj"	=> $LANG['backend']['email_subj'],
	"date"	=> $LANG['backend']['date'],
	"filename"	=> $LANG['backend']['fileupload'],
	"integer_number"	=> $LANG['backend']['integer_number'],
	"decimal_number"	=> $LANG['backend']['decimal_number'],
	"html"	=> $LANG['backend']['HTML'] 
);
foreach ($fieldtypes as $k => $v) {
	$selected = ($k == $type) ? " selected=\"selected\">" : ">";
	$tpl->set_var('VAL_FIELDTYPE', '"'. $k . '"'. $selected . $v);
	$tpl->parse('field_loop', 'field_block', true);
}

// show additional fields depending on type
$tpl->set_block('main_block', 'type_options', 'typeoptions');
$fieldtypeoption = "";
// first round:
switch ($type) {
	case 'heading':
		$fieldtypeoption = "<tr>\n"
		."<td valign='top'>". $TEXT['TEMPLATE'] ."</td>\n"
		."<td><textarea name='template'  cols='50' rows='5' style='width: 98%; height: 200px;'>". $form['extra'] ."</textarea></td>\n"
		."</tr>\n";
		$form['required'] = 3;
		break;
	case 'textfield':
	case 'integer_number':
	case 'decimal_number':
	case 'email_subj':
		$fieldtypeoption = "<tr>\n"
		."<td>". $TEXT['LENGTH'] .":</td>\n"
		."<td><input type='text' name='length' value='". $form['extra'] ."' style='width: 98%;' maxlength='3' /></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td>". $LANG['backend']['TXT_DEFAULT'] .":</td>\n"
		."<td><input type='text' name='value' value='". $form['value'] ."' style='width: 98%;' /></td>\n"
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
		."<td><input type='text' name='width' value='$cols' style='width: 98%;' maxlength='3' /></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td>". $LANG['backend']['ROWS'] .":</td>\n"
		."<td><input type='text' name='rows' value='$rows' style='width: 98%;' maxlength='3' /></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td>". $LANG['backend']['TXT_DEFAULT'] .":</td>\n"
		."<td><textarea name='value' cols='50' rows='5' style='width: 98%; height: 100px;'>". $form['value'] ."</textarea></td>\n"
		."</tr>\n";
		break;
	case 'html':
		$fieldtypeoption = "<tr>\n"
		."<td>". $LANG['backend']['HTML'] .":</td>\n"
		."<td><textarea name='value' cols='80' rows='8' style='width: 98%; height: 200px;'>". $form['value'] ."</textarea></td>\n"
		."</tr>\n";
		$form['required'] = 3;
		break;
	case 'date':
		$fieldtypeoption = "<tr>\n"
		."<td>". $TEXT['LENGTH'] .":</td>\n"
		."<td><input type='text' name='length' value='". $form['extra'] ."' style='width: 98%;' maxlength='3' /></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td>". $LANG['backend']['TXT_DEFAULT'] .":</td>\n"
		."<td><input type='text' name='value' value='". $form['value'] ."' style='width: 98%;' /></td>\n"
		."</tr>\n";
		break;
	case 'email':
		$fieldtypeoption = "<tr>\n"
		."<td>". $TEXT['LENGTH'] .":</td>\n"
		."<td><input type='text' name='length' value='". $form['extra'] ."' style='width: 98%;' maxlength='3' /></td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td>". $LANG['backend']['TXT_DEFAULT'] .":</td>\n"
		."<td><input type='text' name='value' value='". $form['value'] ."' style='width: 98%;' /></td>\n"
		."</tr>\n";
		break;
	case 'filename':
		$fieldtypeoption = "<tr>\n"
		."<td>". $TEXT['LENGTH'] .":</td>\n"
		."<td><input type='text' name='length' value='". $form['extra'] ."' style='width: 98%;' maxlength='3' /></td>\n"
		."</tr>\n";
		break;
	case 'select':
	case 'checkbox':
	case 'radio':
		($type == 'radio') ? $kind = 'radio' : $kind = 'checkbox';
		$fieldtypeoption = "<tr>\n"
		//."<tr>\n"
		."<td valign='top'>". $LANG['backend']['TXT_LIST'] .":</td>\n"
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
				."<td><input type='text' name='value$option_count' value='$ovalue' style='width: 250px;' /> "
				."<input type='$kind' name='$isdef' value='$option_count' $cv /></td>\n"
				."</tr></table>\n";
		}
		for($i = 0; $i < 2; $i++) {
			$option_count = $option_count+1;
			($type == 'radio') ? $isdef = "isdefault" : $isdef = "isdefault$option_count";
			$fieldtypeoption .= '<table cellpadding="3" cellspacing="0" width="100%" border="0">'
				."<tr>\n<td width='70'>". $TEXT['OPTION'].' '.$option_count .":</td>\n"
				."<td><input type='text' name='value$option_count' value='' style='width: 250px;' /> "
				."<input type='$kind' name='$isdef' value='$option_count' /></td>\n"
				."</tr></table>\n";
			}
		$fieldtypeoption .= "<input type='hidden' name='list_count' value='$option_count' /></td>\n</tr>\n";
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
	$fieldtypeoption .= '<td><input type="text" name="size" value="'. trim($form['extra'][0]) .'" style="width: 98%;" maxlength="3" /></td>';
	$fieldtypeoption .= "\n</tr>\n<tr>\n";
	$fieldtypeoption .= "<td>". $TEXT['ALLOW_MULTIPLE_SELECTIONS'] .":</td>\n";
	$fieldtypeoption .= '<td><input type="radio" name="multiselect" id="multiselect_true" value="multiple"';
	if($form['extra'][1] == 'multiple') $fieldtypeoption .= ' checked="checked"';
	$fieldtypeoption .= " />";
	$fieldtypeoption .= '<a href="#" onclick="javascript: document.getElementById(\'multiselect_true\').checked = true;">';
	$fieldtypeoption .= $TEXT['YES'] ."</a>	&nbsp; ";
	$fieldtypeoption .= '<input type="radio" name="multiselect" id="multiselect_false" value=""';
	if($form['extra'][1] == '') $fieldtypeoption .= ' checked="checked"';
	$fieldtypeoption .= " />";
	$fieldtypeoption .= '<a href="#" onclick="javascript: document.getElementById(\'multiselect_false\').checked = true;">';
	$fieldtypeoption .= $TEXT['NO'] ."</a></td>\n</tr>\n";
}
if($type == 'checkbox' OR $type == 'radio') { 
	$fieldtypeoption .= "<tr>\n<td valign='top'>". $TEXT['SEPERATOR'] .":</td>\n";
	$fieldtypeoption .= '<td><input type="text" name="seperator" value="'. $form['extra'] .'" style="width: 98%;" />'."</td>\n</tr>\n";
}
if($type != 'heading' AND $type != 'fieldset_start' AND $type != 'fieldset_end' AND $type != 'none' AND $type != 'html') { 
	$fieldtypeoption .= "<tr>\n<td>". $LANG['backend']['entry'] .":</td>\n";
	$fieldtypeoption .= '<td><input type="radio" name="required" id="required_true" value="1"';
	if($form['required'] == 1 OR $type == 'email_recip') $fieldtypeoption .= ' checked="checked"';
	$fieldtypeoption .= " />";
	$fieldtypeoption .= '<a href="#" onclick="javascript: document.getElementById(\'required_true\').checked = true;">';
	$fieldtypeoption .= $LANG['backend']['compulsory_entry'] ."</a>	&nbsp; ";
	$fieldtypeoption .= '<input type="radio" name="required" id="required_false" value="0"';
	if($form['required'] == 0 AND $type != 'email_recip') $fieldtypeoption .= ' checked="checked"';
	$fieldtypeoption .= " />";
	$fieldtypeoption .= '<a href="#" onclick="javascript: document.getElementById(\'required_false\').checked = true;">';
	$fieldtypeoption .= $LANG['backend']['optional_entry'] ."</a>	&nbsp; ";
	$fieldtypeoption .= '<input type="radio" name="required" id="required_ro" value="2"';
	if($form['required'] == 2 AND $type != 'email_recip') $fieldtypeoption .= ' checked="checked"';
	$fieldtypeoption .= " />";
	$fieldtypeoption .= '<a href="#" onclick="javascript: document.getElementById(\'required_ro\').checked = true;">';
	$fieldtypeoption .= $LANG['backend']['ro_entry'] ."</a></td>\n</tr>\n";
	$fieldtypeoption .= "<tr>\n<td valign='top'>". $MENU['HELP'] .":</td>\n";
	$fieldtypeoption .= '<td><textarea name="help"  cols="50" rows="5" style="width: 98%; height: 100px;">'. $form['help'] ."</textarea></td>\n</tr>\n";
}

$tpl->set_var('VAL_TYPE_OPTIONS', $fieldtypeoption);
$tpl->parse('typeoptions', 'type_options', true);

$tpl->set_var(
	array(
		// variables from Website Baker framework
		'PAGE_ID'		=> (int) $page_id,
		'SECTION_ID'	=> (int) $section_id,
		'FIELD_ID'		=> (int) $field_id,
		'WB_URL'		=> WB_URL,
		'ADMIN_URL'		=> ADMIN_URL,
		'TXT_SAVE'		=> $TEXT['SAVE'],
		'TXT_CANCEL'	=> $TEXT['CANCEL'],
		'TXT_TITLE'		=> $TEXT['TITLE'],
		'TXT_PLEASE_SELECT'	=> $TEXT['PLEASE_SELECT'],
		'MODULE_URL'    => WB_URL.'/modules/'.$mod_dir,
		//'FTAN'			=> (WB_VERSION >= "2.8.2") ? $admin->getFTAN() : '',
		'FTAN'			=> '',
		
		// module settings
		'MODULE_DIR'    => $mod_dir,
		'TXT_TYPE'		=> $LANG['backend']['TXT_TYP'],
		'TXT_COPY_FIELD'=> $LANG['backend']['TXT_COPY_FIELD'],
		'TXT_ADD_FIELD'	=> $LANG['backend']['TXT_ADD_FIELD'],
		'TXT_MODIFY_FIELD'	=> sprintf($LANG['backend']['TXT_MODIFY_FIELD'], $field_id),
		'VAL_TITLE'		=> $form['title']
	)
);

// Parse template objects output
$tpl->parse('main', 'main_block', false);
$tpl->pparse('output', 'page',false, false);

// Print admin footer
$admin->print_footer();

?>