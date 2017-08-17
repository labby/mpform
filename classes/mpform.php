<?php

class mpform
{

	static public function prepare_fields( &$all_fields )
	{
		foreach($all_fields as &$field)
		{
			switch( $field['type'] )
			{
				case 'checkbox':
				case 'select':
					$subfields = explode(",", $field['value']);
					$checkbox_options = array();
					foreach($subfields as $subfieldname)
					{
					
						$temp = explode("{(*#)}", $subfieldname);
						
						$checkbox_options[] = array(
								'title'	=> $temp[0],
								'checked'	=> ((count($temp) == 1) ? "" : "checked='checked'") 
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