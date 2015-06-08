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
date_default_timezone_set('Europe/Berlin');

echo "<script type=\"text/javascript\" src=\"". LEPTON_URL ."/include/jscalendar/calendar.js\"></script>";

// language
$jscal_lang = defined('LANGUAGE')?strtolower(LANGUAGE):'en';
$jscal_lang = $jscal_lang!=''?$jscal_lang:'en';
if(!file_exists(LEPTON_PATH."/include/jscalendar/lang/calendar-$jscal_lang.js")) {
	$jscal_lang = 'en';
}
// today
$jscal_today = date('Y/m/d');
// first-day-of-week
$jscal_firstday = '1'; // monday
if(LANGUAGE=='EN')
	$jscal_firstday = '0'; // sunday
// date and time format for the text-field and for jscal's "ifFormat". We offer dd.mm.yyyy or yyyy-mm-dd or mm/dd/yyyy
// ATTN: strtotime() fails with "dd.mm.yyyy" and PHP4. So the string has to be converted to e.g. "yyyy-mm-dd", which will work.
switch(DATE_FORMAT) {
	case 'd.m.Y':
	case 'd M Y':
	case 'l, jS F, Y':
	case 'jS F, Y':
	case 'D M d, Y':
	case 'd-m-Y':
	case 'd/m/Y':
		$jscal_format = 'd.m.Y'; // dd.mm.yyyy hh:mm
		$jscal_ifformat = '%d.%m.%Y';
		break;
	case 'm/d/Y':
	case 'm-d-Y':
	case 'M d Y':
	case 'm.d.Y':
		$jscal_format = 'm/d/Y'; // mm/dd/yyyy hh:mm
		$jscal_ifformat = '%m/%d/%Y';
		break;
	default:
		$jscal_format = 'Y-m-d'; // yyyy-mm-dd hh:mm
		$jscal_ifformat = '%Y-%m-%d';
		break;
}
if(isset($jscal_use_time) && $jscal_use_time==TRUE) {
	$jscal_format .= ' H:i';
	$jscal_ifformat .= ' %H:%M';
}
// load scripts for jscalendar
?>
<script type="text/javascript" src="<?php echo LEPTON_URL ?>/include/jscalendar/lang/calendar-<?php echo $jscal_lang ?>.js"></script>
<script type="text/javascript" src="<?php echo LEPTON_URL ?>/include/jscalendar/calendar-setup.js"></script>
