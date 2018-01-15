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

// manually include the config.php file (defines the required constants)
require('../../config.php');
require(LEPTON_PATH.'/modules/admin.php');

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// convert page/section id to numbers (already checked by /modules/admin.php but kept for consistency)
$page_id = (isset($_GET['page_id'])) ? (int) $_GET['page_id'] : '';
$section_id = (isset($_GET['section_id'])) ? (int) $_GET['section_id'] : '';

$MOD_MPFORM = (dirname(__FILE__))."/languages/". LANGUAGE .".php";
require_once ( !file_exists($MOD_MPFORM) ? (dirname(__FILE__))."/languages/EN.php" : $MOD_MPFORM );

//START HEADER HERE
require_once(LEPTON_PATH.'/modules/'.$mod_dir.'/functions.php');
module_header_footer($page_id,$mod_dir);
//END HEADER HERE

// Get id
if (WB_VERSION >= "2.8.2") {
	$submission_id = $admin->checkIDKEY('submission_id', false, 'GET');
	if (!$submission_id) {
		$admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL);
		exit();
	}
} else {
	if(!isset($_GET['submission_id']) OR !is_numeric($_GET['submission_id'])) {
		header("Location: ".ADMIN_URL."/pages/index.php");
		exit(0);
	} else {
		$submission_id = $_GET['submission_id'];
	}
}

// Get submission details
$query_content = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_mpform_submissions` WHERE `submission_id` = '$submission_id'");
$submission = $query_content->fetchRow();

// Get the user details of whoever did this submission
$query_user = "SELECT username,display_name FROM `".TABLE_PREFIX."users` WHERE `user_id` = '".$submission['submitted_by']."'";
$get_user = $database->query($query_user);
if($get_user->numRows() != 0) {
	$user = $get_user->fetchRow();
} else {
	$user['display_name'] = 'Unknown';
	$user['username'] = 'unknown';
}

?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td><?php echo $TEXT['SUBMISSION_ID']; ?>:</td>
	<td><?php echo $submission['submission_id']; ?></td>
</tr>
<tr>
	<td><?php echo $TEXT['SUBMITTED']; ?>:</td>
	<td><?php echo date(TIME_FORMAT.', '.DATE_FORMAT, $submission['submitted_when']); ?></td>
</td>
<tr>
	<td><?php echo $TEXT['USER']; ?>:</td>
	<td><?php echo $user['display_name'].' ('.$user['username'].')'; ?></td>
</tr>
<tr>
	<td colspan="2">
		<hr />
	</td>
</tr>
<tr>
	<td colspan="2">
		<?php
			$lines = explode("\n",$submission['body']);
			foreach($lines as $k => $v) {
				$hr = explode('url]',$v);
//				print_r($hr);
				if (count($hr)>1) {
					$hr[0] = substr($hr[0],0,-1);
					$hr[1] = substr($hr[1],0,-2);
					$v = $hr[0]."[url]".$hr[1]."[/url]".$hr[2];
					echo str_replace(array('[url]','[/url]'), array('<a href="','" target="_blank">'.$hr[1].'</a>'), $v); 
				} else {
					echo $v;
				}
				echo "<br>";
			}
		?>
	</td>
</tr>
</table>
<br />
<center>
<input type="button" value="<?php echo $TEXT['CLOSE']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 110px; margin-top: 5px;" />
<input type="button" value="<?php echo $TEXT['DELETE']; ?>" onclick="javascript: confirm_link('<?php echo $TEXT['ARE_YOU_SURE']; ?>', '<?php echo LEPTON_URL; ?>/modules/mpform/delete_submission.php?page_id=<?php
		echo $page_id; ?>&section_id=<?php echo $section_id; ?>&submission_id=<?php
		echo $submission_id; ?>');" style="width: 110px; margin-top: 5px;" /></center>
<?php
// Print admin footer
$admin->print_footer();
?>