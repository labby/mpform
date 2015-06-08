<?php
/*
 * CMS module: MPForm
 *
 * This module allows you to create customised online forms, such as a feedback form with file upload and email attachment
 * MPForm allows forms over one or more pages.
 * User input for the same session_id will become a single row in the submitted table.
 * 
 * This file defines the variables required for Website Baker.
 * 
 * @author		Frank Heyne (mod 4 wb at heysoft dot de)
 * @copyright	(c) 2009 - 2011
 * @license		GNU General Public License
 *
 * ------------------------------------------------------------------------------------------------
 *	MODULE VERSION HISTORY
 * ------------------------------------------------------------------------------------------------
 *	0.1.0 alpha (Frank Heyne: 16-May-2009)
 *	first public version 
 *	derived from FormX version 2.4.8 (authors: Ryan Djurovich & Rudolph Lartey - additions John Maats - PCWacht - Greg Bosen - Stefano Ricci)
 *  Added fields to submissions table:
    + Added field for IP address
    + Added field for time stamp when form was sent to browser
    + Added field for session id (important for multipage forms)
	+ Added field is_following (important for multipage forms)
 *  Added new tables results_xxx<
    + contains one column for each field, regardles of its page_id
	+ contains one line for each session_id
	+ column with timestamp when the first form of a session has been sent to the user
	+ column with timestamp when the last form of a session has been sent back to the server
    + column with referer page from where the form was called
 *  Enhanced multilanguage support
 * ------------------------------------------------------------------------------------------------
 *	0.1.2 beta (Frank Heyne: 26-May-2009)
 *	bug fix in Java Script (view.php) 
 *	status changed to beta
 * ------------------------------------------------------------------------------------------------
 *	0.1.3 beta (Frank Heyne: 29-May-2009)
 *	bug fix in Search function (install.php and upgrade.php) 
 * ------------------------------------------------------------------------------------------------
 *	0.1.4 beta (Frank Heyne: 31-May-2009)
 *	bug fix in Settings (save_settings.php) 
 *	spelling error fix in html (view.php) 
 * ------------------------------------------------------------------------------------------------
 *	0.2.0 RC1 (Frank Heyne: 10-Jun-2009)
 *	valid html code for the backend (as far as module code is concerned)
 *	minor improvements 
 * ------------------------------------------------------------------------------------------------
 *	0.2.1 RC2 (Frank Heyne: 20-Jun-2009)
 *	now multiple email addresses in one line in the backend are allowed as follows:
 *		Recipient X <address1,address2,address3>
 *	minor improvements 
 * ------------------------------------------------------------------------------------------------
 *	0.2.2 RC3 (Frank Heyne: 02-Jul-2009)
 *	now multiple email addresses work again if you put one on each line
 *	minor improvements, thank goes to Argos for bug reports
 * ------------------------------------------------------------------------------------------------
 *	0.3.0 beta (Frank Heyne: 30-Jul-2009)
 *	added field type HTML-Code
 *	added readonly fields
 *	added place holder {CLASSES}
 *	added interface for private functions
 *	made module compatible with WB 2.8
 *	minor improvements
 *	thank goes to Stefek for icons for Backend and to Chio for alternate frontend.css
 * ------------------------------------------------------------------------------------------------
 *	0.3.1 beta (Frank Heyne: 31-Jul-2009)
 *	bug fixes
 * ------------------------------------------------------------------------------------------------
 *	0.3.2 RC1 (Frank Heyne: 20-Aug-2009)
 *	added class for captcha
 * ------------------------------------------------------------------------------------------------
 *	0.4.0 stable (Frank Heyne: 20-Nov-2009)
 *  now submitted forms with errors will repaint form at once
 *	added {ERRORTEXT} for some field types
 *  now multiple forms are allowed on a page
 *  now separate texts are configurable for
 *   - email to site owner
 *   - confirmation mail to user
 *   - text on web page
 *   - text saved in submissions table
 *	removed unnecessary field in settings table
 *	improved frontend.css
 *	added optional enumeration for radio and checkbox
 *	added optional default values for radio and checkbox
 *	documentation for this module now consists of 20 pages and is available online at the module home page (see below)
 *	module now requires WB 2.8 + patch for Captcha (included)
* ------------------------------------------------------------------------------------------------
 *	0.4.1 stable (Frank Heyne: 01-Jan-2010)
 *  minor bug fixes
 *  slight usability improvements in the backend
 *  NL language file from Argos
 *  
 *  To protect your customisatione, update from older version will now no longer overwrite the following files:
 *  	frontend.css (update is delivered as frontend.css.txt)
 *  	backend.css  (update is delivered as backend.css.txt)
 *  	private.php (update is delivered as private.php.txt)
 *  You need to manually compare the files and decide which versions to use!
 * ------------------------------------------------------------------------------------------------
 *	0.4.2 stable (Frank Heyne: 13-Dec-2010)
 *  some security fixes (Cross Page Writing, Reading and Deleting)
 * ------------------------------------------------------------------------------------------------
 *	0.4.3 stable (Frank Heyne: 16-Jan-2011)
 *  some security fixes (CSRF, droplets)
 * ------------------------------------------------------------------------------------------------
 *	0.4.4 stable (Frank Heyne: 23-Jan-2011)
 *  minor changes
 * ------------------------------------------------------------------------------------------------
 *	0.4.5 stable (Frank Heyne: 03-Jul-2011)
 *	This version is sponsored by www.usdumzuege.de
 *  added {USER} field for authenticated user
 *  added possibility to use email address of authenticated user
 * ------------------------------------------------------------------------------------------------
 *	1.0. stable (Frank Heyne: 06-Aug-2011)
 *	made the module compatible with Lepton
 * ------------------------------------------------------------------------------------------------
 *	1.0.1 stable (Frank Heyne: 09-Aug-2011)
 *	disabled WB FTAN because it is not implemented for frontend, yet
 * ------------------------------------------------------------------------------------------------
 *	1.0.3 stable (Frank Heyne: 06-Apr-2012)
 *	minor improvements
 *	solved problem because of stupid renaming of function in WB
 * ------------------------------------------------------------------------------------------------
 *	1.0.4 stable (Frank Heyne: 16-Feb-2013)
 *	bugfix for checkbox (thanks to André Herdling)
 *	Final version
 * ------------------------------------------------------------------------------------------------
*/

$module_directory 	= 'mpform';
$module_name 		= 'MPForm';
$module_function 	= 'page';
$module_version 	= '1.0.4';
$module_platform 	= '2.8.x';
$lepton_platform 	= '1.1';
$module_status		= 'stable';
$module_author 		= 'Frank Heyne';
$module_license 	= 'GNU General Public License';
$module_requirements	= 'Works best under the Lepton CMS';
$module_guid 			= '3FFE634D-D6BD-4C42-B449-D7A1DE3BA74A';
$module_description 	= 'This module allows you to create customised online forms';
$module_home			= 'http://wbdemo.heysoft.de/pages/en/mpform.php';
?>