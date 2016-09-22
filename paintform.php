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

require_once(LEPTON_PATH.'/modules/mpform/constants.php');

// Function for generating an options for a select field
if (!function_exists('make_option')) {
	function make_option(&$n, $k, $params) {
		global $code;
		$values = $params[0];
		$isnew = $params[1];
		$def = strpos($n, IS_DEFAULT);
		($def > 0) ? $h = substr($n, 0, $def) : $h = $n;

		// start option group if it exists
		if (substr($n, 0, 2) == '[=') {
			$n = '<optgroup label="'.substr($n,2,strlen($n)).'">';
		} elseif ($n == ']') {
			$n = '</optgroup>';
		} else {
			if (in_array($h, $values) or ($isnew and $def > 0)) {
				$n = '<option selected="selected" value="'.$h.'">'.$h.'</option>';
			} else {
				$n = '<option value="'.$h.'">'.$h.'</option>';
			}
		}
	}
}

// Function for generating a checkbox
if (!function_exists('make_checkbox')) {
	function make_checkbox(&$n, $idx, $params) {
		global $code;
		$def = strpos($n, IS_DEFAULT);
		($def > 0) ? $h = substr($n, 0, $def) : $h = $n;
		if ($code=="") {
			$v = $h;
		} else {
			$v = $code;
			$code = chr(ord($code)+1);
		}
		$field_id = $params[0];
		$seperator = $params[1];
		$err_class = $params[3];
		$isnew = $params[4];
		$label_i = urlencode($n) . $field_id;
		$bad = array("%", "+", "&");
		$label_id = SEC_ANCHOR.str_replace($bad, "", $label_i);
		if (in_array($v, $params[2]) or ($isnew and $def > 0) or in_array(html_entity_decode($v), $params[2])) {
			$n = '<input class="'.$err_class.'checkbox" type="checkbox" id="'.$label_id.'" name="field'.$field_id.'['.$idx.']" value="'.$v.
			'" checked="checked" />'.'<label for="'.$label_id.'" class="mpform_checkbox_label">'.$h.'</label>'.$seperator."      \n";
		} else {
			$n = '<input class="'.$err_class.'checkbox" type="checkbox" id="'.$label_id.'" name="field'.$field_id.'['.$idx.']" value="'.$v.
			'" />'.'<label for="'.$label_id.'" class="mpform_checkbox_label">'.$h.'</label>'.$seperator."      \n";
		}	
	}
}

// Function for generating a radio button
if (!function_exists('make_radio')) {
	function make_radio(&$n, $idx, $params) {
		global $code;
		$def = strpos($n, IS_DEFAULT);
		($def > 0) ? $h = substr($n, 0, $def) : $h = $n;
		if ($code=="") {
			$v = $h;
		} else {
			$v = $code;
			$code = chr(ord($code)+1);
		}
		$field_id = $params[0];
		$seperator = $params[1];
		$err_class = $params[3];
		$isnew = $params[4];
		$label_i = urlencode($n) . $field_id;
		$bad = array("%", "+");
		$label_id = SEC_ANCHOR.str_replace($bad, "", $label_i);
		if (($v == $params[2]) or ($isnew and $def > 0)) {
			$n = '<input class="'.$err_class.'radio" type="radio" id="'.$label_id.'" name="field'.$field_id.'" value="'.$v.
			'" checked="checked" />'.'<label for="'.$label_id.'" class="mpform_radio_label">'.$h.'</label>'.$seperator."     \n";
		} else {
			$n = '<input class="'.$err_class.'radio" type="radio" id="'.$label_id.'" name="field'.$field_id.'" value="'.$v.
			'" />'.'<label for="'.$label_id.'" class="mpform_radio_label">'.$h.'</label>'.$seperator."     \n";
		}
	}
}

// Generate temp submission id
if (!function_exists('new_submission_id')) {
		function new_submission_id() {
		$submission_id = '';
		$salt = "abchefghjkmnpqrstuvwxyz0123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($salt, $num, 1);
			$submission_id = $submission_id . $tmp;
			$i++;
		}
		return $submission_id;
	}
}

if (!function_exists('js_for_help')) {
function js_for_help() {
echo <<<JS
	<script language="javascript" type="text/javascript">
	//<![CDATA[
	var theRowOpened = -2;
	var theTableOpened = -2;
	function helpme(id,msg,title,help) {
		var theTableBody = document.getElementById(id).parentNode.parentNode.parentNode.parentNode.tBodies[0];
		var row = 1+document.getElementById(id).parentNode.parentNode.rowIndex;
		if ((theRowOpened == row) && (theTableOpened == theTableBody)) {
			removeRow(theRowOpened, theTableOpened);
			theRowOpened = -1;
		} else {
			if (theRowOpened > 0) {
				removeRow(theRowOpened, theTableOpened);
			}
			insertTableRow(row,msg,title,help,theTableBody);
			theRowOpened = row;
			theTableOpened = theTableBody;
		}
	}
	function insertTableRow(row,msg,title,help,theTableBody) {
		var newCell;
		var newRow = theTableBody.insertRow(row);
		newCell = newRow.insertCell(0);
		newCell = newRow.insertCell(1);
		newCell.colSpan = 2;
		newCell.className = "mpform_help_box_td";
		newCell.innerHTML = "<div class='mpform_help_box_div'>" +((title) ? '<h5 class="mpform_help_box_h5">'+help+': '
								+title+'<\/h5><hr class="mpform_help_box_hr" noshade="noshade" size="1" />' : '')
								+'<h6 class="mpform_help_box_h6">'+msg+'<\/h6><\/div>';
	}
	function removeRow(row,theTableBody) {
		theTableBody.deleteRow(row);
	}
	//]]>
	</script>	
JS;
	}
}

////////////////// Main function ///////////////////////

if (!function_exists('paint_form')) {
function paint_form($section_id, $missing=array(), $err_txt=array(), $isnew=true) {
	global $database, $MENU, $TEXT, $MOD_MPFORM;
	global $code, $admin;
	
	if($missing != array()) {
		if(!isset($MOD_MPFORM['frontend']['REQUIRED_FIELDS'])) {
			$msg = 'Please complete or correct the fields in red color!';
		} else {
			$msg = $MOD_MPFORM['frontend']['REQUIRED_FIELDS'];
		}
		echo "<div class='mpform_missing'>$msg</div>";
	}

	// Get settings
	$query_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_settings WHERE section_id = '$section_id'");
	if($query_settings->numRows() > 0) {
		$fetch_settings = $query_settings->fetchRow();
		$header = str_replace('{LEPTON_URL}',LEPTON_URL,$fetch_settings['header']);
		$field_loop = $fetch_settings['field_loop'];
		$footer = str_replace('{LEPTON_URL}',LEPTON_URL,$fetch_settings['footer']);
		$use_captcha = $fetch_settings['use_captcha'];
		$is_following = $fetch_settings['is_following'];
		$max_file_size = $fetch_settings['max_file_size_kb'] * 1024;
		$date_format = $fetch_settings['date_format'];
		$email_to = $fetch_settings['email_to'];
		$upload_only_exts = $fetch_settings['upload_only_exts'];
		$enum_start = $fetch_settings['enum_start'];
		$success_page = $fetch_settings['success_page'];
	} else {
		exit($TEXT['UNDER_CONSTRUCTION']);
	}
	$needhelpbutton = (strpos($field_loop, "{HELP}") !== false);  // we only need a help button if this variable is used

	// execute private function in private.php, if available
	if (function_exists('private_function_before_new_form')) {
		private_function_before_new_form($section_id);
	}

	// Set new submission ID in session if it is not a follower on a multipage form
	if (!$is_following) {
		$_SESSION['submission_id_'.$section_id] = new_submission_id();
	}
	if ($success_page != 'none') {
		$qs = $database->query("SELECT * FROM ".TABLE_PREFIX."sections WHERE page_id = '$success_page' AND module = 'mpform'");
		if($qs->numRows() > 0) {
			$s = $qs->fetchRow();
			$sid = $s['section_id'];
			if (!isset($_SESSION['submission_id_'.$section_id])) $_SESSION['submission_id_'.$section_id] = "";
			$_SESSION['submission_id_'.$sid] = substr($_SESSION['submission_id_'.$section_id], 0, 8);
		}
	}

	// remember the referer page:
	if (isset($_SERVER['HTTP_REFERER'])) {
		if (!isset($_SESSION['href'])) $_SESSION['href'] = addslashes(htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES));
	} else {
		$_SESSION['href'] = 'unknown';
	}

	// Do i need to include calendar files ?
	$query_fields= $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_fields WHERE section_id = '$section_id' AND type = 'date'");
	if($query_fields->numRows() > 0) {
		// include jscalendar-setup
		$jscal_use_time = true; // whether to use a clock, too
		require_once(dirname(__FILE__) . "/jscalendar.php");
		$jscal_firstday = "1"; // - first-day-of-week (0-sunday, 1-monday, ...) (default: 0(EN) or 1(everything else))
		if ($date_format) $jscal_ifformat = $date_format; //"%Y-%m-%d"; // - format for jscalendar (default: from wb-backend-date-format)
	}

	echo "\n<div class=\"mpform\">
	<form name=\"form_$section_id\"  enctype='multipart/form-data' action=\"". htmlspecialchars(strip_tags($_SERVER['SCRIPT_NAME'])) ."#".SEC_ANCHOR."section_".$section_id."\" method=\"post\">
	<input type=\"hidden\" name=\"submission_id\" value=\"". $_SESSION['submission_id_'.$section_id] ."\" />\n";
	
	
	if(ENABLED_ASP) { // first add some honeypot-fields
		$t = time();
		$_SESSION['submitted_when'.$section_id] = $t; 
		echo "<input type=\"hidden\" name=\"submitted_when$section_id\" value=\"$t\" />
		<p style=\"display:none\">
		email address:
		<label for=\"email_$section_id\">Leave this field email-address blank:</label>
		<input id=\"email_$section_id\" name=\"email\" size=\"56\" value=\"\" /><br />
		Homepage:
		<label for=\"homepage_$section_id\">Leave this field homepage blank:</label>
		<input id=\"homepage_$section_id\" name=\"homepage\" size=\"55\" value=\"\" /><br />
		URL:
		<label for=\"url_$section_id\">Do not fill out this field url:</label>
		<input id=\"url_$section_id\" name=\"url\" size=\"63\" value=\"\" /><br />
		Comment:
		<label for=\"comment_$section_id\">Leave this field comment blank:</label>
		<textarea id=\"comment_$section_id\" name=\"comment\" cols=\"50\" rows=\"10\"></textarea><br />
		</p>";
	}

	// Print header
	echo $header;
	$first_MAX = true;

	// Get list of fields
	$query_fields = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_mpform_fields WHERE section_id = '$section_id' ORDER BY position ASC");
	
	$cla= array();
	$onehelp=false;
	//$is_table_layout = (stripos($header, "<table") !== false);  // consists the form of a table?  // php5
	$is_table_layout = (strpos($header, "<table") !== false || strpos($header, "<TABLE") !== false);  // consists the form of a table?  // php4 version
	if($query_fields->numRows() > 0) {
		while($field = $query_fields->fetchRow()) {
			// Set field values
			$field_id = $field['field_id'];
			$value = $field['value'];
			$classes = "fid$field_id mpform_" . $field['type']; 
			if ($field['extra'] == '') {
				$maxlength = '';
			} else {
				$maxlength = ' maxlength="'.$field['extra'].'" '; 
			}

			if (in_array($field_id, $missing)) {
				$err_class = "mpform_err_";
				$classes .= " mpform_error";
			} else {
				$err_class = "mpform_";
			}
			
			// Print field_loop after replacing vars with values
			$vars = array('{TITLE}', '{REQUIRED}');
			
			// Title:
			if (($field['type'] == "radio") || ($field['type'] == "checkbox")) {
				$field_title = $field['title'];
			} else {
				$field_title = '<label for="field'.$field_id.'">'.$field['title'].'</label>';
			}
			$values = array($field_title);
			
			// mark required fields:
			if ($field['required'] == 1) {
				$values[] = '<span class="mpform_required">*</span>';
				$classes .= " mpform_required";
			} else {
 				$values[] = '';
			}
			
			// mark read only fields:
			if ($field['required'] == 2) {
				$readonly = ' readonly="readonly"';
				$classes .= " mpform_readonly";
			} else {
 				$readonly = '';
			}
			
			if($field['type'] == 'textfield') {
				$vars[] = '{FIELD}';
				$values[] = '<input type="text" name="field'.$field_id.'" id="field'.$field_id.'" '.$maxlength.' value="'.(isset($_SESSION['field'.$field_id])?$_SESSION['field'.$field_id]:$value).'" class="'.$err_class.'text" '."$readonly />";
			} elseif($field['type'] == 'email_subj') {
				$vars[] = '{FIELD}';
				$values[] = '<input type="text" name="field'.$field_id.'" id="field'.$field_id.'" '.$maxlength.' value="'.(isset($_SESSION['field'.$field_id])?$_SESSION['field'.$field_id]:$value).'" class="'.$err_class.'text" '."$readonly />";
			} elseif ($field['type'] == 'integer_number') {
				$vars[] = '{FIELD}';
				$js = 'onkeypress="if(event.which) {
						if((event.which &lt; 48 || event.which > 57) &amp;&amp; event.which != 8){return false;}' // Opera
					.'} else if(document.all){ 
						if(event.keyCode &lt; 48 || event.keyCode > 57){return false;}' // IE
					.'}else{
						if((event.charCode &lt; 48 || event.charCode > 57) &amp;&amp; event.charCode != 0){return false;}}"';  // FF
				$values[] = '<input type="text" '.$js.' name="field'.$field_id.'" id="field'.$field_id.'" '.$maxlength.' value="'.(isset($_SESSION['field'.$field_id])?$_SESSION['field'.$field_id]:$value).'" class="'.$err_class.'text" '."$readonly />";
			} elseif ($field['type'] == 'decimal_number') {
				$vars[] = '{FIELD}';						
				$js = 'onkeypress="if(event.which) {
						if((event.which &lt; 43 || event.which > 57 || event.which == 47) &amp;&amp; event.which != 8){return false;}' // Opera
					.'} else if(document.all){ 
						if(event.keyCode &lt; 43 || event.keyCode > 57 || event.keyCode == 47){return false;}' // IE
					.'}else{
						if((event.charCode &lt; 43 || event.charCode > 57 || event.charCode == 47) &amp;&amp; event.charCode != 0){return false;}}"';  // FF
				$values[] = '<input type="text" '.$js.' name="field'.$field_id.'" id="field'.$field_id.'" '.$maxlength.' value="'.(isset($_SESSION['field'.$field_id])?$_SESSION['field'.$field_id]:$value).'" class="'.$err_class.'text" '."$readonly />";
			} elseif ($field['type'] == 'filename') {
				$vars[] = '{FIELD}';
				if ($first_MAX) $vmax = '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_file_size.'" />'; else $vmax = '';
				$s = $MOD_MPFORM['frontend']['MAX_FILESIZE'];
				$values[] = $vmax.'<input type="file" name="field'.$field_id.'" id="field'.$field_id.'" '.$maxlength.' value="'.(isset($_SESSION['field'.$field_id])?$_SESSION['field'.$field_id]:$value).'"
					class="'.$err_class.'text" /><span style="font-size:9px;"><br />'.sprintf($s, $max_file_size/1024, $upload_only_exts).'</span>';
				$first_MAX = false;
			} elseif($field['type'] == 'textarea') {
				$vars[] = '{FIELD}';
				$cr = explode(",", $field['extra']);
				if (isset($cr[0]) and is_numeric($cr[0])) {
					$cols = $cr[0];
				} else {
					$cols = 25;
				}
				if (isset($cr[1]) and is_numeric($cr[1])) {
					$rows = $cr[1];
				} else {
					$rows = 5;
				}
				$values[] = "<textarea name='field$field_id' id='field$field_id' class='$err_class"."textarea' cols='$cols' rows='$rows' $readonly>".(isset($_SESSION['field'.$field_id])?$_SESSION['field'.$field_id]:$value).'</textarea>';
			} elseif($field['type'] == 'select') {
				$vars[] = '{FIELD}';
				$options = explode(',', $value);
				array_walk($options, 'make_option', array((isset($_SESSION['field'.$field_id])?$_SESSION['field'.$field_id]:array()), $isnew));
				$field['extra'] = explode(',',$field['extra']);
				$extras = '';
				if (is_numeric($field['extra'][0])) {
					$extras =  'size="' .$field['extra'][0]. '" ';
				}
				if ($field['extra'][1] == "multiple") {
					$extras .= 'multiple="multiple" ';
				}
				$values[] = '<select name="field'.$field_id.'[]" id="field'.$field_id.'" '. $extras .' class="'.$err_class.'select">'.implode($options).'</select>';
			} elseif($field['type'] == 'email_recip') {
				$vars[] = '{FIELD}';
				$options = array();
				array_push($options, $MOD_MPFORM['frontend']['select']);
				$emails = preg_split('/[\r\n]/', $email_to);
				foreach ($emails as $recip) {
					$teil = explode("<", $recip);
					if (trim($teil[0])!='')
					array_push($options, htmlspecialchars($teil[0], ENT_QUOTES));
				}
				array_walk($options, 'make_option', array((isset($_SESSION['field'.$field_id])?$_SESSION['field'.$field_id]:array()), false));
				$values[] = '<select name="field'.$field_id.'[]" id="field'.$field_id.'" class="'.$err_class.'select">'.implode($options).'</select>';
			} elseif($field['type'] == 'heading') {
				$vars[] = '{FIELD}';
				$values[] = '<input type="hidden" name="field'.$field_id.'" id="field'.$field_id.'" value="===['.$field['title'].']===" />';
				$tmp_field_loop = $field_loop;		// temporarily modify the field loop template
				$field_loop = $field['extra'];
			} elseif($field['type'] == 'fieldset_start') {
				$vars[] = '';
				$values[] = '';
				$tmp_field_loop = $field_loop;		// temporarily modify the field loop template
				$field_loop = '';
				if ($is_table_layout) $field_loop .= "</table>\n";
				$field_loop .= "<fieldset><legend>". $field['title'] ."</legend>\n";
				if ($is_table_layout) $field_loop .= "$header\n";
			} elseif($field['type'] == 'fieldset_end') {
				$vars[] = '';
				$values[] = '';
				$tmp_field_loop = $field_loop;		// temporarily modify the field loop template
				$field_loop = '';
				if ($is_table_layout) $field_loop .= "</table>\n";
				$field_loop .= "</fieldset>\n";
				if ($is_table_layout) $field_loop .= "$header\n";
			} elseif($field['type'] == 'checkbox') {
				$vars[] = '{FIELD}';
				$options = explode(',', $value);
				$code = $enum_start;
				array_walk($options, 'make_checkbox', array($field_id, $field['extra'], (isset($_SESSION['field'.$field_id])?$_SESSION['field'.$field_id]:array()), $err_class, $isnew));
				$options[count($options)-1]=substr($options[count($options)-1],0,strlen($options[count($options)-1])-strlen($field['extra']));
				$values[] = implode($options);
			} elseif($field['type'] == 'radio') {
				$vars[] = '{FIELD}';
				$options = explode(',', $value);
				$code = $enum_start;
				array_walk($options, 'make_radio', array($field_id, $field['extra'], (isset($_SESSION['field'.$field_id])?$_SESSION['field'.$field_id]:''), $err_class, $isnew));
				$options[count($options)-1]=substr($options[count($options)-1],0,strlen($options[count($options)-1])-strlen($field['extra']));
				$values[] = implode($options);
			} elseif($field['type'] == 'email') {
				$vars[] = '{FIELD}';
				$values[] = '<input type="text" name="field'.$field_id.'" id="field'.$field_id.'" value="'.(isset($_SESSION['field'.$field_id])?$_SESSION['field'.$field_id]:'').'"'
					.$maxlength.' class="'.$err_class.'email" '."$readonly placeholder='".$field['value']."'/>";
			} elseif($field['type'] == 'date') {
				$vars[] = '{FIELD}';
				$cla['field'.$field_id] = "field".$field_id;
				$values[] = '<table cellpadding="0" cellspacing="0" border="0">'."\n<tr>\n<td>\n".
				'<input type="text" name="field'.$field_id.'" id="field'.$field_id.'"'
					.$maxlength.' value="'.(isset($_SESSION['field'.$field_id])?$_SESSION['field'.$field_id]:$value).'" class="'.$err_class.'date" />'.
				"</td>\n<td>\n"
				.'<img src="'.LEPTON_URL .'/modules/mpform/images/cal.gif" id="field'.$field_id.'_trigger" class="mpform_date_img" title="'.$TEXT['CALENDAR']
				.'" alt="'.$TEXT['CALENDAR'].'" />'
				."</td>\n</tr>\n</table>\n";
			}
			if(isset($_SESSION['field'.$field_id])) unset($_SESSION['field'.$field_id]);
			
			if ($field['help']) {
				//$help = addslashes(preg_replace('/[\r\n]/', "<br />", $field['help']));
				$help = preg_replace('/[\r\n]/', "<br />", $field['help']);
				$help_txt = "<p class='help_txt'>$help</p>\n";
				$help = str_replace('&quot;', '\\&quot;', $help);
				$values[] = "<a id=\"mpform_a_". $field_id . "\" class=\"mpform_a_help\" href=\"#\" onclick='javascript:helpme(\"mpform_a_$field_id\", \"$help\", \""
					.$field['title']."\",\"".$MENU['HELP']."\");return false;' title=\"".$MENU['HELP']."\">\n<img class=\"mpform_img_help\"
					src=\"".LEPTON_URL."/modules/mpform/images/help.gif\" alt=\"".$MENU['HELP']."\" /></a>";
				if ($needhelpbutton) $onehelp=true;
			} else {
				$values[] = "";
				$help_txt = "";
			}

			if ($field['type'] != 'html') {
				$vars[] = '{HELP}';  // Help Button + JS
				$vars[] = '{HELPTXT}';  // help text always to show 
				$values[] = htmlspecialchars_decode($help_txt);	// help text always to show 
				$vars[] = '{CLASSES}';  // new in v 0.3.0
				$values[] = $classes;	// new in v 0.3.0
				$vars[] = '{ERRORTEXT}';  // new in v 0.4.0
				(isset($err_txt[$field_id])) ? $values[] = '<p>'.$err_txt[$field_id].'</p>' : $values[] = '';	// new in v 0.4.0
				if($field['type'] != '') {
					echo str_replace($vars, $values, $field_loop)."\n";
				}
			} else {
				echo htmlspecialchars_decode($field['value']);  // output html field without any translation
			}
			if (isset($tmp_field_loop)) $field_loop = $tmp_field_loop;
		}
	}
	
	// Captcha
	if($use_captcha) {
		if (in_array('captcha'.$section_id, $missing)) {
			$classes = "captcha_err";
		} else {
			$classes = "captcha";
		}

		$field_loop = $fetch_settings['field_loop'];
		$vars = array('{TITLE}', '{REQUIRED}', '{FIELD}', '{HELP}', '{HELPTXT}', '{CLASSES}', '{ERRORTEXT}');
		$values = array($MOD_MPFORM['frontend']['VERIFICATION'], '<span class="mpform_required">*</span>',
						"'; call_captcha('all', '', $section_id); echo '", "", "", $classes,
						(isset($err_txt['captcha'.$section_id])) ? $err_txt['captcha'.$section_id] : '');
		$cmd = "{echo '" . str_replace($vars, $values, $field_loop) . "';}";
		eval($cmd);
	}
	
	// Print footer
	echo $footer;
	echo "\n</form>";
	echo "\n</div>\n";
	
	if($onehelp) js_for_help();
	
	foreach($cla as $k => $v) {
		$s = "<script type=\"text/javascript\">\n";
		$s .= "Calendar.setup( {\n\tinputField  : \"$k\",\n\tifFormat    : \"$jscal_ifformat\",\n\tbutton      : \"$k"."_trigger\",\n\tfirstDay    : $jscal_firstday,\n";
		if (isset($jscal_use_time) && $jscal_use_time==TRUE) { 
			$s .= "	showsTime   : \"true\",\n\ttimeFormat  : \"24\",\n";
		} 
		$s .= "	date        : \"$jscal_today\",\n\trange       : [1970, 2037],\n\tstep        : 1\n} );\n</script>";
		echo $s;
	}
}
}

?>