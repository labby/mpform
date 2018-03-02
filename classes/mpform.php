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
    /**
     *  The instance of this class.
     *  @var    instance
     */
    public static $instance;

    /**
     *  Initial call from the parent.
     *  Startpoint of all we need to do inside this module-class.
     */    
    public function initialize()
    {
    
    }

    /**
     *  Generates a full URL to a page via a section_id.
     *  The url includes also the #language param and the section anchor to the given section id.
     *
     *  @access static
     *  @param  integer $iSection_id    A valid section id. 
     *  @return string  The full absulut path to the page where the given section (-i) is used.
     *
     *  @code{.php}
     *
     *      mpform::build_action_url( 1234 );
     *      // will return something like
     *      // https:://www.all_the_best.tld/pages/examples/my-form.php?lang=RU#lep_1234
     *
     *  @endcode
     */ 
	public static function build_action_url ($iSection_id = -1) {
		$database = LEPTON_database::getInstance();
		$page_id = $database->get_one("SELECT page_id FROM ".TABLE_PREFIX."sections WHERE section_id = ".$iSection_id." ");
		
		$page_values = array ();
		$database->execute_query(
			"SELECT * FROM ".TABLE_PREFIX."pages WHERE page_id = ".$page_id." " ,
			true,
			$page_values,
			false
		);

		return LEPTON_URL.PAGES_DIRECTORY.$page_values['link'].PAGE_EXTENSION.'?lang='.LANGUAGE."#".SEC_ANCHOR."section_".$section_id;	
	}
	
	/**
	 *  Static function for internal use within TWIG to trransform given values for Twig.
	 *
	 *  @param  array $all_fields   Pass by reference!
	 *  @return void    As the param-value is passed by reference.
	 *
	 */
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