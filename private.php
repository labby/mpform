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

// replace the sample content in the functions below with your own content, if required!

function private_function_before_new_form($section_id) {
	// example how to use:
	if ($section_id == 4526) {
		$_SESSION['field136'] = $_SESSION['EMAIL'];
		return true;
	}

	// another example how to use:
	if ($section_id == 4575) {
		$_SESSION['field4568'] = '';
		if (isset($_SERVER['HTTP_REFERER'])) {
			$_SESSION['field4568'] = $_SERVER['HTTP_REFERER'];
		} 
		return true;
	}

	return true;
}

function private_function_before_email($section_id) {
	// example how to use:
	if ($section_id == 4524) {
		global $felder;
		$felder .= ", field133 = '". $_SESSION['study_id'] ."'";
		return true;
	}

	return true;
}

function private_function_on_success($section_id) {
	global $database;
	
	// example for looping through a form
	// set the following values according your forms
	// see online help for tutorial!
	$prev_section =	9000;	// section_id of the part of the form before the loop 
	$prev_field = 	8000;	// id of the field deciding whether to skip the loop or not
	$prev_cond = 	"yes";	// value of the condition for entering the loop
	$loop_section = 9001;	// section_id of the looping part of the form 
	$loop_field = 	8000;	// id of the field deciding whether to leave the loop or not
	$loop_cond = 	"yes";	// value of the condition for staying in the loop
	$loop_page = 	7001;	// id of the page with the loop
	$next_page = 	7002;	// id of the page following after the loop
	
	if ($section_id == $prev_section) {
		($_SESSION['field'.$prev_field] == $prev_cond) ? $np = $loop_page : $np = $next_page;
		$query_menu = $database->query("SELECT link,target FROM ".TABLE_PREFIX."pages WHERE `page_id` = $np");
		if($query_menu->numRows() > 0) {
			$fetch_settings = $query_menu->fetchRow( MYSQL_ASSOC );
			$link = LEPTON_URL.PAGES_DIRECTORY.$fetch_settings['link'].PAGE_EXTENSION;
			echo "<script type='text/javascript'>location.href='".$link."';</script>";
		}
		return false;
	}
	if ($section_id == $loop_section) {
		if ($_SESSION['field'.$loop_field] == $loop_cond) {
			$np = $loop_page;
			$_SESSION['submission_id_'.$section_id] .= "_";
			foreach ($_SESSION as $k => $v) {
				if (substr($k, 0, 5) == "field") unset ($_SESSION[$k]);
			}
		} else {
			$np = $next_page;
			$_SESSION['submission_id_'.$section_id] = substr($_SESSION['submission_id_'.$section_id], 0, 8);  // restore original submission_id
		}
		$query_menu = $database->query("SELECT link,target FROM ".TABLE_PREFIX."pages WHERE `page_id` = $np");
		if($query_menu->numRows() > 0) {
			$fetch_settings = $query_menu->fetchRow( MYSQL_ASSOC );
			$link = LEPTON_URL.PAGES_DIRECTORY.$fetch_settings['link'].PAGE_EXTENSION;
			echo "<script type='text/javascript'>location.href='".$link."';</script>";
		}
		return false;
	}

	return true;
}

?>