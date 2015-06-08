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
It is available online at the <a href="http://wbdemo.heysoft.de/pages/en/mpform.php" target="help">Home page of the module</a>.</p>
<p>If you find bugs, please report them to <em>mod 4 wb at heysoft dot de</em></p>
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