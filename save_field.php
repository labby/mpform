<?php

/**
 *
 * @category        page
 * @package         MPForm
 * @author          Frank Heyne (mod 4 wb at heysoft dot de), Dietrich Roland Pehlke (last)
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        LEPTON-CMS 2.0.0
 * @requirements    PHP 5.3 and higher
 * @version         1.1.8
 * @lastmodified    Jun 2015 
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

// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(LEPTON_PATH.'/modules/admin.php');

/*$admin_header = false;
require(LEPTON_PATH.'/modules/admin.php');
if ((WB_VERSION >= "2.8.2") && (!$admin->checkFTAN()))
{
	$admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
	exit();
}*/

(WB_VERSION >= "2.8.2") ? $fid = $admin->getIDKEY($field_id): $fid = $field_id;

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
	$title		= str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post_escaped('title'), ENT_QUOTES));
	$type 		= str_replace(array("[[", "]]"), '', $admin->get_post_escaped('type'));
	if (isset($_POST['required'])) {
		$required = $admin->get_post_escaped('required');
	} else {
		$required = '0';
	}
	$help 		= str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post_escaped('help'), ENT_QUOTES));
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
if ($admin->get_post('type') == 'textfield'
  or $admin->get_post('type') == 'email_subj'
  or $admin->get_post('type') == 'integer_number'
  or $admin->get_post('type') == 'decimal_number') {
	$length = int_not0($admin->get_post_escaped('length'));
	$value = str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post_escaped('value'), ENT_QUOTES));
	$database->query("UPDATE ".TABLE_PREFIX."mod_mpform_fields SET value = '$value', extra = '$length' WHERE field_id = '$field_id'");
} elseif ($admin->get_post('type') == 'textarea') {
	$value = str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post_escaped('value'), ENT_QUOTES));
	$width = int_not0($admin->get_post_escaped('width'));
	$rows  = int_not0($admin->get_post_escaped('rows'));
	$database->query("UPDATE ".TABLE_PREFIX."mod_mpform_fields SET value = '$value', extra = '$width,$rows' WHERE field_id = '$field_id'");
} elseif ($admin->get_post('type') == 'html') {
	$value = str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post_escaped('value'), ENT_QUOTES));
	$database->query("UPDATE ".TABLE_PREFIX."mod_mpform_fields SET value = '$value' WHERE field_id = '$field_id'");
} elseif ($admin->get_post('type') == 'heading') {
	$extra = str_replace(array("[[", "]]"), '', $admin->get_post_escaped('template'));
	if(trim($extra) == '') $extra = '<tr><td class="mpform_heading" colspan="3">{TITLE}{FIELD}</td></tr>';
	$database->query("UPDATE ".TABLE_PREFIX."mod_mpform_fields SET value = '', extra = '$extra' WHERE field_id = '$field_id'");
} elseif ($admin->get_post('type') == 'select') {
	$extra = int_not0($admin->get_post_escaped('size')).','.$admin->get_post_escaped('multiselect');
	$database->query("UPDATE ".TABLE_PREFIX."mod_mpform_fields SET value = '$value', extra = '$extra' WHERE field_id = '$field_id'");
} elseif ($admin->get_post('type') == 'checkbox') {
	$extra = str_replace(array("[[", "]]"), '', $admin->get_post_escaped('seperator'));
	if ($extra=="" and $isnewfield) $extra = "<br />";   // set default value
	$database->query("UPDATE ".TABLE_PREFIX."mod_mpform_fields SET value = '$value', extra = '$extra' WHERE field_id = '$field_id'");
} elseif ($admin->get_post('type') == 'date') {
	$length = int_not0($admin->get_post_escaped('length'));
	$value = str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post_escaped('value'), ENT_QUOTES));
	$database->query("UPDATE ".TABLE_PREFIX."mod_mpform_fields SET value = '$value', extra = '$length' WHERE field_id = '$field_id'");
} elseif ($admin->get_post('type') == 'radio') {
	$extra = str_replace(array("[[", "]]"), '', $admin->get_post_escaped('seperator'));
	if ($extra=="" and $isnewfield) $extra = "<br />";   // set default value
	$database->query("UPDATE ".TABLE_PREFIX."mod_mpform_fields SET value = '$value', extra = '$extra' WHERE field_id = '$field_id'");
}

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