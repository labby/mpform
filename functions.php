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

function module_header_footer($page_id, $mod_dir) {
	global $admin, $database, $HEADING, $TEXT, $MESSAGE, $section_id;
	require_once(LEPTON_PATH.'/modules/admin.php');
	
	require(LEPTON_PATH.'/modules/'.$mod_dir.'/info.php');
	//START HEADER HERE
	
	// Get page details
	$results_array=$admin->get_page_details($page_id);
	
	// Get display name of person who last modified the page
	$user=$admin->get_user_details($results_array['modified_by']);
	
	// Convert the unix ts for modified_when to human a readable form
	if($results_array['modified_when'] != 0) {
		$modified_ts = date(TIME_FORMAT.', '.DATE_FORMAT, $results_array['modified_when']);
	} else {
		$modified_ts = 'Unknown';
	}
	// Include page info script
	$template = new Template(LEPTON_PATH.'/modules/'.$mod_dir.'/htt/');
	$template->set_file('page', 'modify.htt');
	$template->set_block('page', 'main_block', 'main');
	$template->set_var(array(
		'PAGE_ID' => $results_array['page_id'],
		'SECTION_ID' => $section_id,
		'PAGE_TITLE' => ($results_array['page_title']),
		'MODULE_TITLE' => $module_name,
		'MODULE_VERSION' => $module_version,
		'MODIFIED_BY' => $user['display_name'],
		'MODIFIED_BY_USERNAME' => $user['username'],
		'MODIFIED_WHEN' => $modified_ts,
		'ADMIN_URL' => ADMIN_URL,
		'MOD_CLASS'	=> $mod_dir
		)
	);
	if($modified_ts == 'Unknown') {
		$template->set_var('DISPLAY_MODIFIED', 'hide');
	} else {
		$template->set_var('DISPLAY_MODIFIED', '');
	}

	// Work-out if we should show the "manage sections" link
	$query_sections = $database->query("SELECT section_id FROM ".TABLE_PREFIX."sections WHERE page_id = '$page_id' AND module = 'menu_link'");
	if($query_sections->numRows() > 0) {
		$template->set_var('DISPLAY_MANAGE_SECTIONS', 'none');
	} elseif(MANAGE_SECTIONS == 'enabled') {
		$template->set_var('TEXT_MANAGE_SECTIONS', $HEADING['MANAGE_SECTIONS']);
	} else {
		$template->set_var('DISPLAY_MANAGE_SECTIONS', 'none');
	}
	
	// Insert language TEXT
	$template->set_var(array(
		'TEXT_CURRENT_PAGE' => $TEXT['CURRENT_PAGE'],
		'TEXT_CHANGE_SETTINGS' => $TEXT['CHANGE_SETTINGS'],
		'LAST_MODIFIED' => $MESSAGE['PAGES']['LAST_MODIFIED'],
		'HEADING_MODIFY_PAGE' => $HEADING['MODIFY_PAGE']
		)
	);
	
	// Parse and print header template
	$template->parse('main', 'main_block', false);
	$template->pparse('output', 'page');
	
	return $admin;
	//END HEADER HERE
}  // end of: function module_header_footer

require_once (LEPTON_PATH.'/framework/class.order.php');
class orderx extends order {

// Clean ordering (should be called if a row in the middle has been deleted)
	function move_to($cf_value,$field_id,$position) {
		global $database;

		// Get current index
		$order = $this->get_position($field_id);
		if ($order === false) return false;
		if ($order == $position) return true;

		if($order < $position)
			$sql = "UPDATE `".$this->table ."` SET `".$this->order_field."` = `".$this->order_field."` - 1 ".
	 					" WHERE `".$this->common_field."` = '".$cf_value."' ".
	 					" AND `".$this->order_field."` > '".$order ."' AND `".$this->order_field."` <= '".$position."'";
		else
			$sql = "UPDATE `".$this->table ."` SET `".$this->order_field."` = `".$this->order_field."` + 1 ".
	 					" WHERE `".$this->common_field."` = '".$cf_value."' ".
	 					" AND `".$this->order_field."` >= '".$position ."' AND `".$this->order_field."` < '".$order."'";
	 					
		$database->query($sql);
		if($database->is_error()) {
			echo $sql."<br>".$database->get_error();
			return false;
		}
		$sql = "UPDATE `".$this->table ."` SET `".$this->order_field."` = '".$position."'".
				 	" WHERE `".$this->id_field."` = '".$field_id. "'";

		$database->query($sql);
		if($database->is_error()) {
			echo $sql."<br>".$database->get_error();
			return false;
		}
		return true;
	}
	
	function get_position($field_id) {
		global $database;

		// Get current index
		$query_order = "SELECT `".$this->order_field."` FROM `".$this->table."` WHERE `".$this->id_field."` = '$field_id'";
		$get_order = $database->query($query_order);
		if($database->is_error()) {
			echo $query_order."<br>".$database->get_error();
			return false;
		}		
		$fetch_order = $get_order->fetchRow();
		$order = $fetch_order[$this->order_field];
		
		return $order;
	}
}  // end of: class orderx extends order

function insert_drag_drop($button_up_cell) {
echo "<script type=\"text/javascript\">
<!--
var JsAdmin = { LEPTON_URL : '". LEPTON_URL."', THEME_URL : '". THEME_URL."', structure_type : 'ul', buttonCell : ". $button_up_cell." };
//-->
</script>
<script type='text/javascript' src='". LEPTON_URL."/include/yui/yahoo/yahoo-min.js'></script>
<script type='text/javascript' src='". LEPTON_URL."/include/yui/event/event-min.js'></script>
<script type='text/javascript' src='". LEPTON_URL."/include/yui/dom/dom-min.js'></script>
<script type='text/javascript' src='". LEPTON_URL."/include/yui/connection/connection-min.js'></script>
<script type='text/javascript' src='". LEPTON_URL."/include/yui/dragdrop/dragdrop-min.js'></script>
<script type='text/javascript' src='". LEPTON_URL."/modules/jsadmin/js/jsadmin.js'></script>
<script type='text/javascript' src='". LEPTON_URL."/modules/mpform/js/dragdrop.js'></script>
";
} // end of: function insert_drag_drop

?>