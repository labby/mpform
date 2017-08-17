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

// Get id
if(!isset($_POST['field_id']) OR !is_numeric($_POST['field_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
	exit(0);
} else {
	$field_id = (int) $_POST['field_id'];
}

require_once(LEPTON_PATH.'/modules/mpform/constants.php');

// Include admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(LEPTON_PATH.'/modules/admin.php');

$fid = $field_id;

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

function int_not0($s) {
	$i = intval($s);
	return (($i==0)?'':$i);
}

// no need for the user to put a title in the end of a fieldset or html code:
if($admin->get_post('title') == '' AND $admin->get_post('type') == 'fieldset_end') $_POST['title'] = "end of fieldset";
if($admin->get_post('title') == '' AND $admin->get_post('type') == 'html') 		   $_POST['title'] = "HTML code";

// Validate all fields
if($admin->get_post('title') == '' OR $admin->get_post('type') == '') {
	$admin->print_error($MESSAGE['GENERIC']['FILL_IN_ALL'], LEPTON_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$fid);
} else {
	$title		= str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post('title'), ENT_QUOTES));
	$type 		= str_replace(array("[[", "]]"), '', $admin->get_post('type'));
	if (isset($_POST['required'])) {
		$required = $admin->get_post('required');
	} else {
		$required = '0';
	}
	$help 		= str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post('help'), ENT_QUOTES));
}

// is this a new field or an attack?
$broken = true;
$query_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_fields WHERE field_id = '$field_id'");
if($query_settings->numRows() > 0) {
	$fetch_settings = $query_settings->fetchRow();
	$isnewfield = $fetch_settings['title'] == "";
	$broken = $fetch_settings['page_id'] != $page_id;
}
if ($broken) {
	header("Location: ".ADMIN_URL."/pages/index.php");
	exit(0);	
}

// Update row
$database->query("UPDATE ".TABLE_PREFIX."mod_mpform_fields SET title = '$title', type = '$type', required = '$required', help = '$help' WHERE field_id = '$field_id'");
if($database->is_error()) {
	$admin->print_error($database->get_error());
}

// If field type has multiple options, get all values and implode them
$value = '';
$list_count = addslashes($admin->get_post('list_count'));
if(is_numeric($list_count)) {
	$values = array();
	for($i = 1; $i <= $list_count; $i++) {
		if (isset($_POST['isdefault']) and (is_numeric($_POST['isdefault']))) {
			$default = $_POST['isdefault'];
		} elseif (isset($_POST['isdefault'.$i]) and (is_numeric($_POST['isdefault'.$i]))) {
			$default = $_POST['isdefault'.$i];
		} else {
			$default = 0;
		}
		if($admin->get_post('value'.$i) != '') {
			($default == $i) ? $defcode = IS_DEFAULT : $defcode = '';
			$values[] = str_replace(array("[[", "]]"), '', str_replace(",", "&#44;", htmlspecialchars(addslashes($admin->get_post('value'.$i)), ENT_QUOTES))) . $defcode;
		}
	}
	$value = implode(',', $values);
}

// Get extra fields for field-type-specific settings
// Validate all fields and translate special chars
$fields = array();
$field_type = $admin->get_post('type');
switch( $field_type ) {
	
	case 'textfield':
 	case 'email_subj':
	case 'email':
	case 'integer_number':
	case 'decimal_number':
	case 'filename':
		$fields = array(
			'value'		=> str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post('value'), ENT_QUOTES)),
			'extra'	=> int_not0($admin->get_post('length'))
		);
		break;
	
	case 'textarea':
		$width = int_not0($admin->get_post('width'));
		$rows  = int_not0($admin->get_post('rows'));
		
		$fields = array(
			'value'	=> str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post('value'), ENT_QUOTES)),
			'extra'	=> $width.",".$rows
		);
		break;
		
	case 'html':
		$fields = array(
			'value'	=> str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post('value'), ENT_QUOTES))
		);
		break;
		
	case 'heading':
		$extra = str_replace(array("[[", "]]"), '', $admin->get_post('template'));
		if(trim($extra) == '') $extra = '<tr><td class="mpform_heading" colspan="3">{TITLE}{FIELD}</td></tr>';
		
		$fields = array(
			'value'	=> "",
			'extra'	=> $extra
		);
		break;
		
	case 'select':
		$fields = array(
			'value' => $value, 	// *
			'extra'	=> int_not0($admin->get_post('size')).','.$admin->get_post('multiselect')
		);
		break;
		
	case 'checkbox':
		$extra = str_replace(array("[[", "]]"), '', $admin->get_post('seperator'));
		if ($extra=="" and $isnewfield) $extra = "<br />";   // set default value
		
		$fields = array(
			'value' => $value, 	// *
			'extra'	=> $extra
		);
		break;
		
	case 'date':
		$fields = array(
			'value' => str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post('value'), ENT_QUOTES)),
			'extra'	=> int_not0($admin->get_post('length'))
		);
		break;
		
	case 'radio':
		$extra = str_replace(array("[[", "]]"), '', $admin->get_post('seperator'));
		if ($extra=="" and $isnewfield) $extra = "<br />";   // set default value
		
		$fields = array(
			'value'	=> $value, // *!
			'extra'	=> $extra
		);
		break;
		
	case 'fieldset_start':
		//$extra = str_replace(array("[[", "]]"), '', $admin->get_post('seperator'));
		//if ($extra=="" and $isnewfield) $extra = "<br />";   // set default value
		
		$fields = array(
			'value'	=> $value, // *!
			'extra'	=> "" // $extra
		);
		break;

	case 'fieldset_end':
		//$extra = str_replace(array("[[", "]]"), '', $admin->get_post('seperator'));
		//if ($extra=="" and $isnewfield) $extra = "<br />";   // set default value
		
		$fields = array(
			'value'	=> $value, // *!
			'extra'	=> "" // $extra
		);
		break;

		
	default:
		$admin->print_error( "[1] No field-type match!" );
		return 0;
}

$database->build_and_execute(
	"update",
	TABLE_PREFIX."mod_mpform_fields",
	$fields,
	"`field_id`= '".$field_id."'"
);  

// Check if there is a db error, otherwise say successful
if ($database->is_error()) {
	$admin->print_error($database->get_error(), LEPTON_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$fid);
} else {
	if (isset($_POST['copy'])) {
		$admin->print_success($TEXT['SUCCESS'], LEPTON_URL.'/modules/mpform/copy_field.php?page_id='.$page_id.'&section_id='.$section_id.'&oldfield_id='.$fid);
	} elseif (isset($_POST['add'])) {
		$admin->print_success($TEXT['SUCCESS'], LEPTON_URL.'/modules/mpform/add_field.php?page_id='.$page_id.'&section_id='.$section_id);
	} else {
		$admin->print_success($TEXT['SUCCESS'], LEPTON_URL.'/modules/mpform/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$fid);
	}
}

// Print admin footer
$admin->print_footer();

?>