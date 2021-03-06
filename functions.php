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

function module_header_footer($page_id, $mod_dir) {
	global $admin, $database, $HEADING, $TEXT, $MESSAGE, $section_id;
	
	require_once(LEPTON_PATH.'/modules/admin.php');
	
	require(LEPTON_PATH.'/modules/'.$mod_dir.'/info.php');
	
	// Get page details
	$results_array=$admin->get_page_details($page_id);
	
	// Get display name of person who last modified the page
	$user=$admin->get_user_details( $results_array['modified_by'] );
	
	// Convert the unix ts for modified_when to human a readable form
	if($results_array['modified_when'] != 0) {
		$modified_ts = date(TIME_FORMAT.', '.DATE_FORMAT, $results_array['modified_when']);
	} else {
		$modified_ts = 'Unknown';
	}

	$header_values = array(
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
		
	);
	$header_values['DISPLAY_MODIFIED'] = '';

	$header_values['TEXT_MANAGE_SECTIONS'] = $HEADING['MANAGE_SECTIONS'];
	$header_values['DISPLAY_MANAGE_SECTIONS'] = "";

	$header_values['TEXT_CURRENT_PAGE'] = $TEXT['CURRENT_PAGE'];
	$header_values['TEXT_CHANGE_SETTINGS'] = $TEXT['CHANGE_SETTINGS'];
	$header_values['LAST_MODIFIED'] = $MESSAGE['PAGES_LAST_MODIFIED'];
	$header_values['HEADING_MODIFY_PAGE'] = $HEADING['MODIFY_PAGE'];
	
	$oTwig = lib_twig_box::getInstance();
	$oTwig->registerModule( $mod_dir );

	echo $oTwig->render(
		"@mpform/backend_header.lte",
		$header_values
	);
	
	return true;
	
}  // end of: function module_header_footer


function insert_drag_drop($button_up_cell) {
echo "<div>
	<script type=\"text/javascript\">
	<!--
	var JsAdmin = { LEPTON_URL : '". LEPTON_URL."', THEME_URL : '". THEME_URL."', structure_type : 'ul', buttonCell : ". $button_up_cell." };
	//-->
	</script>
</div>";
} // end of: function insert_drag_drop
?>