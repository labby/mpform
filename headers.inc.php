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

$mod_headers = array();

/**	**********************************************************************************
 *	Ok - here we go.
 *	This is one of the reasons to use LEPTON 2.x now!
 *	As we're using the SLIMBOX2 for the frontend we are in the _need_ to tell the core
 *	to use a) jQuery Core and b) some js and css files for it.
 *	Fairly simple here:
 *
 */

/*
if ( file_exists( LEPTON_PATH.'/modules/lib_jquery/plugins/Slimbox2' ) ) {
    $mod_headers = array(
		'frontend' => array(
		    'css' => array(
				array(
					'media'	=> 'screen',
					'file'	=> '/modules/lib_jquery/plugins/Slimbox2/slimbox2.css',
				),
				array(
					'media'	=> 'screen',
					'file'	=> '/modules/module_info/rating/rating.css'
				)
			),
			'jquery' => array(
				array(
					'core'	=> true
				)
			),	
			'js' => array(
                '/modules/lib_jquery/plugins/Slimbox2/slimbox2.js'
			)
		),
		'backend' => array(
		    'css' => array(
				array(
					'media'	=> 'screen',
					'file'	=> '/modules/lib_jquery/plugins/Slimbox2/slimbox2.css',
				)
			),	
			'js' => array(
				'/modules/lib_jquery/jquery-ui/external/jquery.idTabs.min.js',
				'/modules/lib_jquery/jquery-ui/external/jquery-insert.js',
                '/modules/lib_jquery/plugins/Slimbox2/slimbox2.js',
                '/modules/module_info/js/jquery.dragsort-0.5.2.min.js'
			)
		)
	);
}
*/
/**
 *	That's all!
 */
?>