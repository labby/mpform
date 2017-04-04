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

if (!function_exists('upload_one_file')) {
	function upload_one_file($fileid, $upload_files_folder, $filename, $only_exts, $chmod, $maxbytes) {
		// include strings for this function
		$mod_dir = basename(dirname(__FILE__));
		
		$MOD_MPFORM = (dirname(__FILE__))."/languages/". LANGUAGE .".php";
		require_once ( !file_exists($MOD_MPFORM) ? (dirname(__FILE__))."/languages/EN.php" : $MOD_MPFORM );
	
		// stop if file too large
		if ($_FILES[$fileid]['size'] > $maxbytes) {
			$s = sprintf($MOD_MPFORM['frontend']['err_too_large'], $_FILES[$fileid]['size'], $maxbytes);
			return $s;
		}
		
		// stop after upload error
		if ($_FILES[$fileid]['error'] == 1) {
			$s = sprintf($MOD_MPFORM['frontend']['err_too_large2'], $maxbytes);
			return $s;
		} elseif ($_FILES[$fileid]['error'] == 2) {
			$s = sprintf($MOD_MPFORM['frontend']['err_too_large2'], $maxbytes);
			return $s;
		} elseif ($_FILES[$fileid]['error'] == 3) {
			$s = $MOD_MPFORM['frontend']['err_partial_upload'];
			return $s;
		} elseif ($_FILES[$fileid]['error'] == 4) {
			$s = $MOD_MPFORM['frontend']['err_no_upload'];
			return $s;
		}

		/**
		 *	Test the file-extension (MIME type)
		 */
		$allowed_types = explode(",",$only_exts);
		
		$temp_array= explode( ".", $filename);
		$temp_extension = array_pop( $temp_array);
		if(!in_array($temp_extension, $allowed_types)) {
			return "[1] File type not allowed here!";
		} 
		
		require_once(LEPTON_PATH.'/modules/lib_lepton/upload/class.upload.php');
		$upload = new upload($_FILES[ $fileid ]);
		
		if ($upload->uploaded) {
		
			$upload->Process( $upload_files_folder );
			
			 if ($upload->processed) {
			 	// ok
			 } else {
			 	// ERROR
			 	return "[2] ".$upload->error;
			 }
		}
		return false;  // upload did not fail  
	}
}

if (!class_exists('wbx')) {
	class wbx // extends wb
	{
		function __construct() {
		
		}
	
		// Validate send email
		function mailx($fromaddress, $toaddress, $subject, $message, $fromname='', $file_attached='') {
			/* 
				INTEGRATED OPEN SOURCE PHPMAILER CLASS FOR SMTP SUPPORT AND MORE
				SOME SERVICE PROVIDERS DO NOT SUPPORT SENDING MAIL VIA PHP AS IT DOES NOT PROVIDE SMTP AUTHENTICATION
				NEW WBMAILER CLASS IS ABLE TO SEND OUT MESSAGES USING SMTP WHICH RESOLVE THESE ISSUE (C. Sommer)
	
				NOTE:
				To use SMTP for sending out mails, you have to specify the SMTP host of your domain
				via the Settings panel in the backend of Website Baker
			*/ 
	
			$fromaddress = preg_replace('/[\r\n]/', '', $fromaddress);
			$subject = preg_replace('/[\r\n]/', '', $subject);
			$htmlmessage = preg_replace('/[\r\n]/', "<br />\n", $message);
			$plaintext = preg_replace(",<br />,", "\r\n", $message);
			$plaintext = preg_replace(",</h.>,", "\r\n", $plaintext);
			$plaintext = htmlspecialchars_decode(preg_replace(",</?\w+>,", " ", $plaintext), ENT_NOQUOTES);
	
			// create PHPMailer object and define default settings
			require_once LEPTON_PATH."/modules/lib_phpmailer/library.php";
			
			//	ALDUS: PHPMailer >= 6 comes up with his own namespace
			//	so we've have to look for the current version first here
			$module_version = '';
			require_once LEPTON_PATH."/modules/lib_phpmailer/info.php";
			$myMail = intval($module_version) < 6 
				? new PHPMailer()
				: new PHPMailer\PHPMailer\PHPMailer()
				;
	
			// set user defined from address
			if ($fromaddress!='') {
				if($fromname!='') $myMail->FromName = $fromname;         // FROM-NAME
				$myMail->From = $fromaddress;                            // FROM:
				$myMail->AddReplyTo($fromaddress);                       // REPLY TO:
			}
			
			// define recipient(s)
			$emails = explode(",", $toaddress);
			foreach ($emails as $recip) {
				if (trim($recip) != '')
				$myMail->AddAddress(trim($recip));                      // TO:
			}
			
			// define information to send out
			$myMail->Subject = $subject;                                // SUBJECT
			$myMail->Body = $htmlmessage;                               // CONTENT (HTML)
			$myMail->AltBody = $plaintext;                    			// CONTENT (PLAINTEXT)
			$myMail->CharSet="UTF-8";									// force text to be utf-8
			if (is_array($file_attached)) {
				foreach($file_attached as $k => $v) {
					$myMail->AddAttachment($k, $v);                  // ATTACHMENT (FILE)
				}
			}

			// check if there are any send mail errors, otherwise say successful
			if (!$myMail->Send()) {
				$_SESSION['mpform_wbx_error'] = $myMail->ErrorInfo;
				return false;
			} else {
				return true;
			}
		}
	}
	global $wbx;
	$wbx = new wbx;
}

////////////////// Main function ///////////////////////

if (!function_exists('eval_form')) {
function eval_form($section_id) {
	global $database, $MESSAGE, $admin, $TEXT, $wbx, $MOD_MPFORM;

	 $ip = (preg_match("/^\d+\.\d+\.\d+\.\d+$/", $_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'unknown';  // IP address of sender

	// obtain the settings of the output filter module
	if (file_exists(LEPTON_PATH.'/modules/output_filter/filter-routines.php')) {
		include_once(LEPTON_PATH.'/modules/output_filter/filter-routines.php');
		if (function_exists('get_output_filter_settings')) {
			$filter_settings = get_output_filter_settings();
		} else {
			$filter_settings['email_filter'] = 0;
		}
	} else {
		// no output filter used, define default settings
		$filter_settings['email_filter'] = 0;
	}

	$files_to_attach = array();
	$upload_filename = '';
	
	// Check that submission ID matches
	if (!isset($_SESSION['submission_id_'.$section_id])
		OR !isset($_POST['submission_id'])
		OR $_SESSION['submission_id_'.$section_id] != $_POST['submission_id']) {
			include_once(LEPTON_PATH .'/modules/mpform/paintform.php');
			paint_form($section_id);
			return;
	}
	
	if(ENABLED_ASP && ( // form faked? Check the honeypot-fields.
		(!isset($_POST['submitted_when'.$section_id]) OR !isset($_SESSION['submitted_when'.$section_id])) OR 
		($_POST['submitted_when'.$section_id] != $_SESSION['submitted_when'.$section_id]) OR
		(!isset($_POST['email']) OR $_POST['email']) OR
		(!isset($_POST['homepage']) OR $_POST['homepage']) OR
		(!isset($_POST['comment']) OR $_POST['comment']) OR
		(!isset($_POST['url']) OR $_POST['url'])
	)) {
		exit(header("Location: ".LEPTON_URL.PAGES_DIRECTORY.""));
	}

	// Get form settings
	$query_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_settings WHERE section_id = '$section_id'");
	if($query_settings->numRows() > 0) {
		$fetch_settings = $query_settings->fetchRow();

		$email_from = $fetch_settings['email_from'];
		if(substr($email_from, 0, 5) == 'field') {
			// Set the email from field to what the user entered in the specified field
			$email_from = htmlspecialchars(addslashes($_POST[$email_from]));  
		}
		if ($email_from == 'wbu') {
			$email_from = $admin->get_email();
		}

		$email_fromname = $fetch_settings['email_fromname'];
		if(substr($email_fromname, 0, 5) == 'field') {
			// Set the email from field to what the user entered in the specified field
			$email_fromname = htmlspecialchars($admin->get_post_escaped($email_fromname), ENT_QUOTES);
		}
		if ($email_fromname == 'wbu') {
			$email_fromname = $admin->get_display_name();
		}

		$success_email_to = $fetch_settings['success_email_to'];
		if(substr($success_email_to, 0, 5) == 'field') {
			// Set the success_email to field to what the user entered in the specified field
			$success_email_to = htmlspecialchars(addslashes($_POST[$success_email_to]));
		}
		if ($success_email_to == 'wbu') {
			$success_email_to = $admin->get_email();
		}
	
		$email_subject = $fetch_settings['email_subject'];
		$email_text = $fetch_settings['email_text'];
		$success_page = $fetch_settings['success_page'];
		$success_text = $fetch_settings['success_text'];
		$submissions_text = $fetch_settings['submissions_text'];
		$success_email_from = $fetch_settings['success_email_from'];
		$success_email_fromname = $fetch_settings['success_email_fromname'];
		$success_email_text = $fetch_settings['success_email_text'];
		$success_email_subject = $fetch_settings['success_email_subject'];		
		$max_submissions = $fetch_settings['max_submissions'];
		$stored_submissions = $fetch_settings['stored_submissions'];
		$use_captcha = $fetch_settings['use_captcha'];
		$upload_files_folder = $fetch_settings['upload_files_folder'];
		$attach_file = $fetch_settings['attach_file'];
		$upload_only_exts = $fetch_settings['upload_only_exts'];
		$upload_file_mask = $fetch_settings['upload_file_mask'];
		$max_file_size = $fetch_settings['max_file_size_kb'] * 1024;
		$_POST['MAX_FILE_SIZE'] = $max_file_size; // stupid enough, PEAR checks this POST variable for maximum size!
		$suffix = $fetch_settings['tbl_suffix'];
		$email_to = $fetch_settings['email_to'];
		
		// settings for html output of form input:
		$heading_html = $fetch_settings['heading_html'];
		$short_html = $fetch_settings['short_html'];
		$long_html = $fetch_settings['long_html'];
		$email_html = $fetch_settings['email_html'];
		$uploadfile_html = $fetch_settings['uploadfile_html'];
	} else {
		exit($TEXT['UNDER_CONSTRUCTION']);
	}

	// get authenticated user data
	if(isset($admin) AND $admin->is_authenticated() AND $admin->get_user_id() > 0) {
		$submitted_by = $admin->get_user_id();
		$wb_user = $admin->get_display_name();
		$wb_email = $admin->get_email();
	} else {
		$submitted_by = 0;
		$wb_user = '';
		$wb_email = '';
	}

	//$email_body = '';
	$fer = array();
	$err_txt = array();
	$html_data_user = '';
	$html_data_site = '';
	
	$format = DEFAULT_DATE_FORMAT. " " .DEFAULT_TIME_FORMAT;
	$jetzt = date($format);

	// Captcha
	if($use_captcha) {
		if(isset($_POST['captcha']) AND $_POST['captcha'] != ''){
			// Check for a mismatch
			if(!isset($_SESSION['captcha'.$section_id]) OR $_POST['captcha'] != $_SESSION['captcha'.$section_id]) {
				$err_txt['captcha'.$section_id] = $MOD_MPFORM['frontend']['INCORRECT_CAPTCHA'];
				$fer[] = 'captcha'.$section_id;
			}
		} else {
			$err_txt['captcha'.$section_id] = $MOD_MPFORM['frontend']['INCORRECT_CAPTCHA'];
			$fer[] = 'captcha'.$section_id;
		}
	}
	if(isset($_SESSION['captcha'.$section_id])) { unset($_SESSION['captcha'.$section_id]); }
	
	// Create blank "required" array
	$felder = "";	// for results table
	$mailto = "";

	// Get list of fields
	$query_fields = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_fields WHERE section_id = '$section_id' ORDER BY position ASC");
	if($query_fields->numRows() > 0) {
		while($field = $query_fields->fetchRow()) {
			// Loop through fields and add to message body
			$field_id = $field['field_id'];
			if($field['type'] != '') {
				if ((!empty($_POST['field'.$field_id]))
				or  ($admin->get_post('field'.$field_id) == "0")) { // added Apr 2009
					$post_field = $_POST['field'.$field_id];
			
					// copy user entered data to $_SESSION in case form must be reviewed (for instance because of missing required values)
					if (is_array($post_field)) {
						$_SESSION['field'.$field_id] = str_replace(array("[[", "]]"), array("&#91;&#91;", "&#93;&#93;"), $post_field);
					} else {
						// make sure user does see what he entered:
						$_SESSION['field'.$field_id] = str_replace(array("[[", "]]"), array("&#91;&#91;", "&#93;&#93;"), htmlspecialchars(stripslashes($post_field), ENT_QUOTES));
					}

					// no injections, please
					if (!is_array($post_field)) {
						$field_value = str_replace(array("[[", "]]"), array("&#91;&#91;", "&#93;&#93;"), htmlspecialchars($admin->get_post_escaped('field'.$field_id), ENT_QUOTES));
					}
					
					// if the output filter is active, we need to revert (dot) to . and (at) to @ (using current filter settings)
					// otherwise the entered mail will not be accepted and the recipient would see (dot), (at) etc.
					if ($filter_settings['email_filter']) {
						$field_value = $post_field;
						$field_value = str_replace($filter_settings['at_replacement'], '@', $field_value);
						$field_value = str_replace($filter_settings['dot_replacement'], '.', $field_value);
						$post_field = $field_value;
					}
					if($field['type'] == 'email' AND $admin->validate_email($post_field) == false) {
						$err_txt[$field_id] = $MESSAGE['USERS']['INVALID_EMAIL'];
						$fer[] = $field_id;
					}

					// check invalid user input
					if($field['type'] == 'integer_number') {
						$v = $post_field;
						if (!preg_match("/^[0-9]+$/", $v)) {  // only allow valid chars
							$err_txt[$field_id] = $MOD_MPFORM['frontend']['integer_error'];
							$fer[]=$field_id;
						}
					}
					if ($field['type'] == 'decimal_number') {
						$v = $post_field;
						if (!preg_match("/^(\+|\-)?[0-9]+(\,|\.)?[0-9]*$/", $v)) {  // only allow valid chars
							$err_txt[$field_id] = $MOD_MPFORM['frontend']['decimal_error'];
							$fer[]=$field_id;
						}
					}

					if ($field['type'] == 'heading') {
						//$email_body .= $field_value."\n";
						$html_data_user .= str_replace('{HEADING}', $field['title'], $heading_html);
						$html_data_site .= str_replace('{HEADING}', $field['title'], $heading_html);
					} elseif ($field['type'] == 'email_recip') {
						// the browser will convert umlauts, we need to undo this for compare:
						$recip = htmlentities  ($post_field[0], ENT_NOQUOTES, 'UTF-8');
						if ($recip == $MOD_MPFORM['frontend']['select']) {
							$err_txt[$field_id] = $MOD_MPFORM['frontend']['select_recip'];
							$fer[]=$field_id;
						}
						$recip = htmlspecialchars($post_field[0], ENT_QUOTES);
						//$email_body .= $field['title'].': '.$recip."\n";
						$html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $recip), $short_html);
						$html_data_site .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $recip), $short_html);
						if ($mailto == "") { 
							$mailto = $recip;
						}
						
					} elseif ($field['type'] == 'email_subj') {
						$email_subject .= " ". $field_value;
						$success_email_subject .= " ". $field_value;
						$html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $field_value), $short_html);
						$html_data_site .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $field_value), $short_html);

					} elseif (!is_array($post_field)) {
						//$email_body .= $field['title'].': '.$field_value."\n\n";   // besser noch strip_tags ??
						if ($field['type'] == 'email') {
							$html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $field_value), $email_html);
							$html_data_site .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $field_value), $email_html);
						} elseif ($field['type'] == 'textarea') {
							//$zeilen = str_replace("\n", "<br />", $field_value);  // v 1.0
							$zeilen = str_replace("\r\n", "<br />", $field_value);  // Test doppelte LF
							$html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $zeilen), $long_html);
							$html_data_site .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $zeilen), $long_html);
						} else {
							$html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $field_value), $short_html);
							$html_data_site .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $field_value), $short_html);
						}
						//$s1[$field['title']] = $field_value; // save as label_name => value,,,
						if (strlen($felder) > 0) {
							$felder .= ", ";
						}
						$felder .= "field" . $field_id . " = '" . $database->mysql_escape(htmlspecialchars($post_field)) . "'";
					} else {
						//$email_body .= $field['title'].": \n";   
						//$s1[$field['title']]='';
						if (strlen($felder) > 0) {
							$felder .= ", ";
						}
						$felder .= "field" . $field_id . " = '";
						$zeilen = '';
						foreach ($post_field as $k => $v) {
							$field_value = htmlspecialchars(addslashes($v), ENT_QUOTES);
							//$email_body .= $field_value."\n";  // besser noch strip_tags ??
							//$s1[$field['title']] .= $field_value; // save as label_name => value,,,
							$felder .= $database->mysql_escape($field_value) . ", ";
							$zeilen .= $database->mysql_escape($field_value) . "<br />";
						}
						$felder .= "'";
						//$email_body .= "\n";
						$html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $zeilen), $long_html);
						$html_data_site .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], $zeilen), $long_html);
					}
				} elseif($field['type'] == 'filename') {
					if($_FILES['field'.$field_id]['name'] != ""){
						$filename = preg_replace("/[^0-9a-zA-Z_\-\.]/", "", basename($_FILES['field'.$field_id]['name'])); // only allow valid chars in filename
						$newfilename = date('YmdHis') . "-" . rand(10000, 99999). "-" . $filename;
						$uploadfailed = upload_one_file('field'.$field_id, LEPTON_PATH.$upload_files_folder, $newfilename, $upload_only_exts, $upload_file_mask, $max_file_size);
						if ($uploadfailed) {
							$err_txt[$field_id] = sprintf($MOD_MPFORM['frontend']['err_upload'], $filename, $uploadfailed);
							$fer[]=$field_id;
						} else {
							$upload_filename = $upload_files_folder . "/". $newfilename;  	// for results table only
							$file_url = LEPTON_URL . $upload_files_folder . "/" . $newfilename; // for links in email to admin and backend
						
							if ($attach_file == 1) {
								$files_to_attach[LEPTON_PATH. $upload_files_folder. "/". $newfilename] = $filename;
							}
							if (strlen($felder) > 0) {
								$felder .= ", ";
							}
							$felder .= "field" . $field_id . " = '" . $upload_filename . "'";
							$fs = sprintf("%.1f", $_FILES['field'.$field_id]['size'] / 1024);  // file size in KB
							$html_data_user .= str_replace(array('{TITLE}', '{DATA}'), array($field['title'], "$filename ($fs KB)"), $short_html);
							$html_data_site .= str_replace(array('{TITLE}', '{DATA}', '{SIZE}'), array($field['title'], $file_url, $fs), $uploadfile_html);
						}
					} elseif ($field['required']==1) {
						$fer[]=$field_id;
					}
				} elseif ($field['type'] == 'fieldset_start') {
					$html_data_user .= "<fieldset><legend>". $field['title'] ."</legend>\n";
					$html_data_site .= "<fieldset><legend>". $field['title'] ."</legend>\n";
				} elseif ($field['type'] == 'fieldset_end') {
					$html_data_user .= "</fieldset>\n";
					$html_data_site .= "</fieldset>\n";
				} elseif ($field['type'] == 'html') {
					$html_data_user .= htmlspecialchars_decode($field['value']) . "<br />\n";
					$html_data_site .= htmlspecialchars_decode($field['value']) . "<br />\n";
				} elseif($field['required'] == 1) {
					$fer[]=$field_id;
				}
			}
		}
	}

	// Check if the user forgot to enter values into all the required fields
	if($fer != array()) {
		// paint form again:
		include_once(LEPTON_PATH .'/modules/mpform/paintform.php');
		paint_form($section_id, $fer, $err_txt, false);
	} else {
		// Check how many times form has been submitted in last hour
		$last_hour = time()-3600;
		$query_submissions = $database->query("SELECT submission_id FROM ".TABLE_PREFIX."mod_mpform_submissions WHERE submitted_when >= '$last_hour'");
		if($query_submissions->numRows() > $max_submissions) {
			// Too many submissions so far this hour
			echo $MESSAGE['MOD_FORM']['EXCESS_SUBMISSIONS']. " ";
			$success = false;
		} else {
			
			// execute private function in private.php, if available
			if (function_exists('private_function_before_email')) {
				$success = private_function_before_email($section_id);
			} else $success = true;
			
			// Now send the email + attachment...
			if($email_to != '') {  // $email_to is set in the backend, might consist of lists of addresses
				$body = str_replace(array('{DATA}', '{REFERER}', '{IP}', '{DATE}', '{USER}'), array($html_data_site, $_SESSION['href'], $ip, $jetzt, $wb_user), $email_text);
				$q = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_fields WHERE section_id = '$section_id' and type = 'email_recip' LIMIT 1");
				if ($q->numRows() > 0 and $mailto != "") {  // $mailto contains recipient as selected by user
					// recipient selectet by user: 
					$emails = preg_split('/[\r\n]/', $email_to);
					foreach ($emails as $recip) {
						if (strpos($recip, $mailto) === 0) {
							$teil = explode("<", $recip);
							if (count($teil) == 1) { // no name part found
								$mailto = trim($teil[0]);
								break;
							} elseif (count($teil) == 2) { // with name part
								$s = explode(">", $teil[1]);
								$mailto = trim($s[0]);
								break;
							}
						}
					}
				}
				if ($mailto =="") {
					// take all recipients from the list:
					$emails = preg_split('/[\r\n]/', $email_to);
					foreach ($emails as $recip) {  
						if ($recip != '') {
							$teil = explode("<", $recip);
							if (count($teil) == 1) { // no name part found
								$mailto .= trim($teil[0]).",";
							} elseif (count($teil) == 2) { // with name part
								$s = explode(">", $teil[1]);
								$mailto .= trim($s[0]).",";
							}
						}
					}
				}

				if($wbx->mailx($email_from, $mailto, $email_subject, $body, $email_fromname, $files_to_attach)) {
					$files_to_attach = array();
				} else {
					$success = false;
					echo (isset($TEXT['WBMAILER_FUNCTION']) ? $TEXT['WBMAILER_FUNCTION'] : $TEXT['MAILER_FUNCTION'])." (SITE) <br />\n".$_SESSION['mpform_wbx_error'];
					unlink( $_SESSION['mpform_wbx_error'] );
				}
			}
			
			if ($success==true AND $success_email_to != '') {
				$user_body = str_replace(array('{DATA}', '{REFERER}', '{IP}', '{DATE}', '{USER}'), array($html_data_user, $_SESSION['href'], $ip, $jetzt, $wb_user), $success_email_text);
				if (! $wbx->mailx($success_email_from, $success_email_to, $success_email_subject, $user_body, $success_email_fromname)) {
					$success = false;
					echo $TEXT['WBMAILER_FUNCTION']. " (CONFIRM) ";
				}
			}
			
			if ($success==true) {
				// Write submission to database	
				$us = $_SESSION['submission_id_'.$section_id];
				$started_when = $_SESSION['submitted_when'.$section_id];
				$body = str_replace(array('{DATA}', '{REFERER}', '{IP}', '{DATE}', '{USER}'), array($html_data_site, $_SESSION['href'], $ip, $jetzt, $wb_user), $submissions_text);
				$database->query("INSERT INTO ".TABLE_PREFIX."mod_mpform_submissions
						(page_id, section_id, submitted_when, submitted_by, upload_filename, ip_addr, body, started_when, session_id)
						VALUES ('".PAGE_ID."', '$section_id', '".time()."', '$submitted_by', '$upload_filename', '$ip', '$body', '$started_when', '$us')");
				if($database->is_error()) {
					$success = false;
					echo $TEXT['DATABASE']. " ";
				} else {
					// Make sure submissions table isn't too full
					$query_submissions = $database->query("SELECT submission_id FROM ".TABLE_PREFIX."mod_mpform_submissions ORDER BY submitted_when");
					$num_submissions = $query_submissions->numRows();
					if($num_submissions > $stored_submissions) {
						// Remove excess submission
						$num_to_remove = $num_submissions-$stored_submissions;
						while($submission = $query_submissions->fetchRow()) {
							if($num_to_remove > 0) {
								$submission_id = $submission['submission_id'];
								$database->query("DELETE FROM ".TABLE_PREFIX."mod_mpform_submissions WHERE submission_id = '$submission_id'");
								if($database->is_error()) {
									$success = false;
									echo $TEXT['DATABASE']. " ";
								}
								$num_to_remove -= 1;
							}
						}
					}
					
					$query_submitted = $database->query("SELECT session_id FROM ".TABLE_PREFIX."mod_mpform_results_$suffix WHERE session_id = '$us'");
					$num_submitted = $query_submitted->numRows();
					if ($num_submitted == 0) {
						// 1.0	new session:
						// 1.1	Bugfix Aldus: 2016-09-20
						//		To avoid problems/errors within fields "fieldxxxx" with have no default values
						//		we've to look first to the table:
						$table_info = array();
						$database->describe_table( TABLE_PREFIX."mod_mpform_results_".$suffix, $table_info);
						$defaults_fields = array();
						
						foreach( $table_info as $tRef) {
							if( strpos( $tRef['Field'],  "field" ) === 0) { //	Keep in mind that we're lokking for position 0 (first char)!
								$defaults_fields[] = $tRef['Field'];
							}
						}
						$fields = array(
							'session_id'	=> $us,
							'started_when'	=> $started_when,
							'referer'		=> $_SESSION['href'] 
						);
						foreach( $defaults_fields as &$temp_name ) $fields[ $temp_name] = "";
						
						$database->build_and_execute(
							'insert',
							TABLE_PREFIX."mod_mpform_results_".$suffix,
							$fields
						);
					}
					
					if($database->is_error()) {
						echo $TEXT['DATABASE']. " " . $qs;
						$success = false;
					} else {
						if ($felder != "") $felder .= ", ";
						$felder .= "submitted_when = '". time() ."'";
						$lf = array("\r\n", "\n", "\r");
						$qs = "UPDATE ".TABLE_PREFIX."mod_mpform_results_$suffix SET ". str_replace($lf, " ", $felder) ." WHERE session_id = '$us' LIMIT 1";
						
						$database->query($qs);
						if($database->is_error()) {
							echo $TEXT['DATABASE']. " " . $qs;
							$success = false;
						}
					}

					// Make sure results table isn't too full
					$qs = $database->query("SELECT session_id FROM ".TABLE_PREFIX."mod_mpform_results_$suffix ORDER BY submitted_when");
					$num_submissions = $qs->numRows();
					if($num_submissions > $stored_submissions) {
						// Remove excess submission
						$num_to_remove = $num_submissions-$stored_submissions;
						while($submission = $query_submissions->fetchRow()) {
							if($num_to_remove > 0) {
								$submission_id = $submission['session_id'];
								$database->query("DELETE FROM ".TABLE_PREFIX."mod_mpform_results_$suffix WHERE session_id = '$submission_id'");
								if($database->is_error()) {
									$success = false;
									echo $TEXT['DATABASE']. " ";
								}
								$num_to_remove -= 1;
							}
						}
					}

				}
			}
		}	
	}
	
	// Now check if the email was sent successfully
	if (isset($success) AND $success == true) {
		if (isset($_SESSION['captcha_time'])) unset($_SESSION['captcha_time']);	// can't do this in captcha module when multiple forms on one page!

		// execute private function in private.php, if available
		if (function_exists('private_function_on_success')) {
			$success = private_function_on_success($section_id);
		}
					
		if ($success == true) {
			if ($success_page=='none') {
				echo str_replace(array('{DATA}', '{REFERER}', '{IP}', '{DATE}', '{USER}'), array($html_data_user, $_SESSION['href'], $ip, $jetzt, $wb_user), $success_text);
				// delete the referer page reference after it did its work:
				unset($_SESSION['href']);
			} else {
				$query_menu = $database->query("SELECT link,target FROM ".TABLE_PREFIX."pages WHERE `page_id` = '$success_page'");
				if ($query_menu->numRows() > 0) {
					$fetch_settings = $query_menu->fetchRow();
				   $link = LEPTON_URL.PAGES_DIRECTORY.$fetch_settings['link'].PAGE_EXTENSION;
				   echo "<script type='text/javascript'>location.href='".$link."';</script>";
				}
			}
		}

		// delete the referer page reference after it did its work:
		unset($_SESSION['href']);
		unset($success);
	} else {
		if (isset($success) AND $success == false) {
			echo $TEXT['ERROR'];
			unset($success);
		}
	}
}
}
?>