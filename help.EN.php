<?php

/**
 *
 * 
 *  @module         MPForm
 *  @author         Frank Heyne, Dietrich Roland Pehlke, erpe
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


$MOD_MPFORM = (dirname(__FILE__))."/languages/". LANGUAGE .".php";
require_once ( !file_exists($MOD_MPFORM) ? (dirname(__FILE__))."/languages/EN.php" : $MOD_MPFORM );


//START HEADER HERE
require_once(LEPTON_PATH.'/modules/'.$mod_dir.'/functions.php');
$adm = module_header_footer($page_id, $mod_dir);
//END HEADER HERE

?>
<div class="helppage">
<p>The help and documentation for this module now consists of approximately 20 pages.
It is available online at the <a href="https://doc.lepton-cms.org/_extern/mp_form/EN/mpform.html" target="_blank">LEPTON documentation</a>.</p>
<p>If you find bugs, please <a href="http://forum.lepton-cms.org/" target="_blank">report them</a></p>
</div>
<br />
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td align="center">
				<input class="helppage" type="button" value="<?php echo $TEXT['BACK']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; margin-top: 5px;" />
			</td>
		</tr>
		</table>
<?php
$admin->print_footer();
?>