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


$admin = new LEPTON_admin('Pages', 'pages_modify');

?>
<div class="ui grey segment">
	<h2 class="ui header">MP Form Hilfe</h2>
	<div class="helppage">
		<p>Die Dokumentation zu diesem Modul umfasst mittlerweile etwa 20 Seiten. Sie ist online auf der <a href="https://doc.lepton-cms.org/_extern/mp_form/DE/mpform.html" target="_blank">LEPTON Dokumentation</a> verf&uuml;gbar.</p>
		<p>Falls Sie Bugs finden, <a href="http://forum.lepton-cms.org/" target="_blank">melden Sie sie bitte</a></p>
	</div>
	<br />
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td align="center">
				<input class="helppage" type="button" value="<?php echo $TEXT['BACK']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; margin-top: 5px;" />
			</td>
		</tr>
	</table>
</div>
<?php
$admin->print_footer();
?>