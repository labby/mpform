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

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// Include admin wrapper script
require(LEPTON_PATH.'/modules/admin.php');

// 2nd level...
require_once(LEPTON_PATH.'/framework/class.admin.php');

// include core functions to edit the optional module CSS files (frontend.css, backend.css)
@include_once(LEPTON_PATH .'/framework/module.functions.php');

// include the module language file depending on the backend language of the current user
if (!@include(get_module_language_file($mod_dir))) return;

//START HEADER HERE
require_once(LEPTON_PATH.'/modules/'.$mod_dir.'/functions.php');
$adm = module_header_footer($page_id, $mod_dir);
//END HEADER HERE

?>
<div class="helppage">
<p>Die Dokumentation zu diesem Modul umfasst mittlerweile etwa 20 Seiten. Sie ist online auf der <a href="http://wbdemo.heysoft.de/pages/de/mpform.php" target="help">Homepage des Moduls</a> verf&uuml;gbar.</p>
<p>Falls Sie Bugs finden, melden Sie sie bitte an <em>mod 4 wb at heysoft dot de</em></p>
</div>
<br />
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td align="center">
				<input type="button" value="<?php echo $TEXT['BACK']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; margin-top: 5px;" />
			</td>
		</tr>
		</table>
<?php
$admin->print_footer();
?>