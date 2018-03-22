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
 
function mpform_settings_set_email_from ( aSelectRef )
{
	var curr = aSelectRef.options[ aSelectRef.selectedIndex ].value;
	// alert( curr );
	var ref = document.getElementById("email_from_f_slave");
	if(ref)
	{
		ref.style.display = (curr=="") ? 'block' : 'none';
 		//alert("s: "+ref.style.display );
	}
	return true;
}

function mpform_settings_set_email_fromname ( aSelectRef )
{
	var curr = aSelectRef.options[ aSelectRef.selectedIndex ].value;
	// alert( curr );
	var ref = document.getElementById("email_fromname_f_slave");
	if(ref)
	{
		ref.style.display = (curr=="") ? 'block' : 'none';
 		//alert("s: "+ref.style.display );
	}
	return true;
}