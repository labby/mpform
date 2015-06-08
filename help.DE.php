<?php
/* 
 * CMS module: MPForm
 * For more information see info.php
 * 
 * English language Help file.
 * This file is (c) 2009 Website Baker Project <http://www.websitebaker.org/>
 * Improvements are copyright (c) 2009-2011 Frank Heyne
*/

// manually include the config.php file (defines the required constants)
require('../../config.php');

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die(header('Location: ../../index.php'));

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// Include admin wrapper script
require(WB_PATH.'/modules/admin.php');

// 2nd level...
require_once(WB_PATH.'/framework/class.admin.php');

// include core functions to edit the optional module CSS files (frontend.css, backend.css)
@include_once(WB_PATH .'/framework/module.functions.php');

// include the module language file depending on the backend language of the current user
if (!@include(get_module_language_file($mod_dir))) return;

//START HEADER HERE
require_once(WB_PATH.'/modules/'.$mod_dir.'/functions.php');
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