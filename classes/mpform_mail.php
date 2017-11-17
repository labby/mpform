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

class mpform_mail
	{
		function __construct() {
		
		}
	
		// Validate send email
		function mailx($fromaddress, $toaddress, $subject, $message, $fromname='', $file_attached='') {
			/* 
				INTEGRATED OPEN SOURCE PHPMAILER CLASS FOR SMTP SUPPORT AND MORE
				SOME SERVICE PROVIDERS DO NOT SUPPORT SENDING MAIL VIA PHP AS IT DOES NOT PROVIDE SMTP AUTHENTICATION
				NEW MAILER CLASS IS ABLE TO SEND OUT MESSAGES USING SMTP WHICH RESOLVE THESE ISSUE (C. Sommer)
	
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
	$oPHPMailer = lib_phpmailer::getInstance();
			// set user defined from address
			if ($fromaddress!='') {
				if($fromname!='') $oPHPMailer->FromName = $fromname;         // FROM-NAME
				$oPHPMailer->From = $fromaddress;                            // FROM:
				$oPHPMailer->AddReplyTo($fromaddress);                       // REPLY TO:
			}
			
			// define recipient(s)
			$emails = explode(",", $toaddress);
			foreach ($emails as $recip) {
				if (trim($recip) != '')
				$oPHPMailer->AddAddress(trim($recip));                      // TO:
			}
			
			// define information to send out
			$oPHPMailer->Subject = $subject;                                // SUBJECT
			$oPHPMailer->Body = $htmlmessage;                               // CONTENT (HTML)
			$oPHPMailer->AltBody = $plaintext;                    			// CONTENT (PLAINTEXT)
			$oPHPMailer->CharSet="UTF-8";									// force text to be utf-8
			if (is_array($file_attached)) {
				foreach($file_attached as $k => $v) {
					$oPHPMailer->AddAttachment($k, $v);                  // ATTACHMENT (FILE)
				}
			}

			// check if there are any send mail errors, otherwise say successful
			if (!$oPHPMailer->Send()) {
				$_SESSION['mpform_wbx_error'] = $oPHPMailer->ErrorInfo;
				return false;
			} else {
				return true;
			}
		}
	}


?>