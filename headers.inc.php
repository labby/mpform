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
 
$mod_headers = array(
	'frontend' => array(
		'js' => array(
			"modules/lib_jquery/jquery-ui/jquery-ui.min.js",
		),
		'css'	=> array(
			array(
					'media'		=> 'screen',
					'file'		=> "modules/lib_jquery/jquery-ui/jquery-ui.min.css"
				)
		)

	),
	'backend' => array(
        'css' => array(
		array(
			'media'  => 'all',
			'file'  => 'modules/lib_semantic/dist/semantic.min.css'
			)		
 		),				
		'js' => array(
			'modules/lib_jquery/jquery-core/jquery-core.min.js',
			'modules/lib_jquery/jquery-core/jquery-migrate.min.js',
			'modules/lib_semantic/dist/semantic.min.js'
		),
	)	
);
