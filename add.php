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

$MOD_MPFORM = (dirname(__FILE__))."/languages/". LANGUAGE .".php";
require_once ( !file_exists($MOD_MPFORM) ? (dirname(__FILE__))."/languages/EN.php" : $MOD_MPFORM );

// update db schema
$query_content = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_settings");
$setting = $query_content->fetchRow();

// set default values
$header = '<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\">';
$field_loop = '<tr class="{CLASSES}"><td class=\"mpform_title\">{TITLE}{REQUIRED}:</td><td class="mpform_help">{HELP}</td><td class="mpform_field">{FIELD}{ERRORTEXT}</td></tr>';
$footer = '<tr><td></td><td></td>
<td><input type=\"submit\" name=\"submit\" class=\"mpform_submit\" value=\"'.$MOD_MPFORM['backend']['TXT_SUBMIT'].'\" /></td></tr>
</table>';
$heading_html = "<h3>{HEADING}</h3>";
$short_html = "<b>{TITLE}:</b> {DATA}<br />";
$long_html = "<b>{TITLE}:</b><br />{DATA}<br /><br />";
$email_html = "<b>{TITLE}:</b> <a href=\"mailto:{DATA}\">{DATA}</a><br />";
$uploadfile_html = "<b>{TITLE}:</b> <a href=\"{DATA}\">{DATA}</a><br />";
$date_format = $MOD_MPFORM['backend_adv']['date_format'];
$is_following = 0;
$upload_files_folder = MEDIA_DIRECTORY. "/".$mod_dir;
$email_to = $admin->get_email();
$email_from = '';
$email_fromname = '';
$email_subject = $MOD_MPFORM['backend']['EMAIL_SUBJECT'];
$success_page = 'none';
$success_text = '<div class="mpform_results">Thank you for submitting your data. We received the following data:<br />{DATA}
<br />Referer page: {REFERER}<br />Your IP address: {IP}</div>';
$submissions_text = '{DATA}
Referer page: {REFERER}
IP address: {IP}
Date: {DATE}';
$email_text = 'The following data was submitted:<br />{DATA}
<br />Referer page: {REFERER}<br />IP address: {IP}';
$success_email_to = '';
$success_email_from = $admin->get_email();
$success_email_fromname = '';
$success_email_text = 'Thank you for submitting your data. We received the following data:<br />{DATA}
<br />Referer page: {REFERER}<br />Your IP address: {IP}';
$success_email_subject = $MOD_MPFORM['backend']['EMAIL_SUC_SUBJ'];
$max_submissions = 50;
$stored_submissions = 1000;
$max_file_size_kb = 1024;
$attach_file = 0;
$upload_file_mask = '0604';
$upload_dir_mask = '0705';
$upload_only_exts = "jpg,gif,png,tif,bmp,pdf";
if(extension_loaded('gd') AND function_exists('imageCreateFromJpeg')) { /* Make's sure GD library is installed */
	$use_captcha = true;
} else {
	$use_captcha = false;
}
$SQL  = "INSERT INTO ". TABLE_PREFIX. "mod_mpform_settings (";
$SQL .= "                page_id        , ";
$SQL .= "                section_id     , ";
$SQL .= "                header         , ";
$SQL .= "                field_loop     , ";
$SQL .= "                footer         , ";
$SQL .= "                email_to       , ";
$SQL .= "                email_from     , ";
$SQL .= "                email_fromname , ";
$SQL .= "                email_subject  , ";
$SQL .= "                email_text		, ";
$SQL .= "                success_page   , ";
$SQL .= "                success_text   , ";
$SQL .= "                submissions_text, ";
$SQL .= "                success_email_to, ";
$SQL .= "                success_email_from   , ";
$SQL .= "                success_email_fromname   , ";
$SQL .= "                success_email_text   , ";
$SQL .= "                success_email_subject, ";
$SQL .= "                max_submissions, ";
$SQL .= "                stored_submissions, ";
$SQL .= "                heading_html   , ";
$SQL .= "                short_html   , ";
$SQL .= "                long_html   , ";
$SQL .= "                email_html   , ";
$SQL .= "                uploadfile_html   , ";
$SQL .= "                date_format   , ";
$SQL .= "                max_file_size_kb   , ";
$SQL .= "                attach_file   , ";
$SQL .= "                upload_file_mask,";
$SQL .= "                upload_dir_mask,";
$SQL .= "                use_captcha   , ";
$SQL .= "                upload_files_folder   , ";
$SQL .= "                upload_only_exts,";
$SQL .= "                is_following,";
$SQL .= "                tbl_suffix";
$SQL .= "        ) ";
$SQL .= "        VALUES ";
$SQL .= "        ( ";
$SQL .= "                '$page_id'              , ";
$SQL .= "                '$section_id'           , ";
$SQL .= "                '$header'               , ";
$SQL .= "                '$field_loop'           , ";
$SQL .= "                '$footer'               , ";
$SQL .= "                '$email_to'             , ";
$SQL .= "                '$email_from'           , ";
$SQL .= "                '$email_fromname'       , ";
$SQL .= "                '$email_subject'        , ";
$SQL .= "                '$email_text', ";
$SQL .= "                '$success_page'         , ";
$SQL .= "                '$success_text'         , ";
$SQL .= "                '$submissions_text', ";
$SQL .= "                '$success_email_to', ";
$SQL .= "                '$success_email_from'   , ";
$SQL .= "                '$success_email_fromname', ";
$SQL .= "                '$success_email_text'   , ";
$SQL .= "                '$success_email_subject', ";
$SQL .= "                '$max_submissions'      , ";
$SQL .= "                '$stored_submissions'   , ";
$SQL .= "                '$heading_html'   , ";
$SQL .= "                '$short_html'   , ";
$SQL .= "                '$long_html'   , ";
$SQL .= "                '$email_html'   , ";
$SQL .= "                '$uploadfile_html'   , ";
$SQL .= "                '$date_format'   , ";
$SQL .= "                '$max_file_size_kb'   , ";
$SQL .= "                '$attach_file'   , ";
$SQL .= "                '$upload_file_mask',";
$SQL .= "                '$upload_dir_mask',";
$SQL .= "                '$use_captcha' , ";
$SQL .= "                '$upload_files_folder'   , ";
$SQL .= "                '$upload_only_exts' , ";
$SQL .= "                '$is_following',";
$SQL .= "                '$section_id'";
$SQL .= "        )";
$database->query($SQL);

?>
