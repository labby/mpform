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

class mpform extends LEPTON_abstract
{
    public static $instance;
    
    public function initialize()
    {
    
    }
    
	static public function prepare_fields( &$all_fields )
	{
		foreach($all_fields as &$field)
		{
			switch( $field['type'] )
			{
				case 'checkbox':
				case 'radio':
				case 'select':
					$subfields = explode(",", $field['value']);
					$checkbox_options = array();
					foreach($subfields as $subfieldname)
					{
					
						$temp = explode("{(*#)}", $subfieldname);
						
						$checkbox_options[] = array(
								'title'	=> $temp[0],
								'checked'	=> ((count($temp) == 1) ? "" : ( $field['type']=='select' ? "selected='selected'" : "checked='checked'") ) 
							);
					}
					$field['options'] = $checkbox_options; 
					break;
				
				
				default:
					break;
			}
		}
	}

}

?>