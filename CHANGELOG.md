### mpForm
Changelog
Please note: This change log may not be accurate

### MODULE VERSION HISTORY

#### 1.1.8
- Minor bugfixes and some minor codechanges.
- Update some index.php files.
- Change outdated mysql-function calls in upgrade.php

#### 1.1.7
- Change language-ref var to $MOD_MPFORM to avoid name-conflicts.
- Add missing language-key(-s).
- Bugfix for frontend-output warning when using multiple form pages.

#### 1.1.6
- Bugfix inside delete.php

#### 1.1.5
- CSS bugfix for backend-interface.
- Replace WB constants within LEPTON ones.
- Codechanges inside copy_field.php for L* 2

#### 1.1.4 (Dietrich Roland Pehlke - LEPTON-CMS: jun-2015)
- Bugfix for frontend-captcha within L* 1.3.2
- Bugfix for renaming files during installation.

#### 1.1.3 (Dietrich Roland Pehlke - LEPTON-CMS: Jun-2015)
- Some bugfixes and codechanges for LEPTON 2 (µ+)
- Add headers.inc.php to the project.
- Update missing register-class-secure entries.
- Codechanges for backend, css-classes, htt files
- Some bugfixes for outdated/unused class-functions calls from WB.
- Sorting submissions desc.

#### 1.1.2 (Dietrich Roland Pehlke - LEPTON-CMS: Jun-2015)
- serval Bugfixes and codechanges for LEPTON-CMS 2

#### 1.1.1 (Dietrich Roland Pehlke - LEPTON-CMS)
- Add Changelog.md
- Add Readme.md
- Remove WB code (e.g. LEPTON_PATH)
- Move some files (e.g. .js inside 'js' directory)
- Strict LEPTON
- Path-corrections for some icons (e.g. THEME-URL instead of ADMIN-URL)

#### 1.1.0 beta (Dietrich Roland Pehlke: Jun-2015)
- Try to get module to run within LEPTON-CMS 2.0 (µ+)

#### 1.0.4 stable (Frank Heyne: 16-Feb-2013)
- bugfix for checkbox (thanks to AndrÈ Herdling)

#### 1.0.3 stable (Frank Heyne: 06-Apr-2012)
- minor improvements
- solved problem because of stupid renaming of function in WB

#### 1.0.1 stable (Frank Heyne: 09-Aug-2011)
- disabled WB FTAN because it is not implemented for frontend, yet

#### 1.0. stable (Frank Heyne: 06-Aug-2011)
- made the module compatible with Lepton

#### 0.4.5 stable (Frank Heyne: 03-Jul-2011)
- This version is sponsored by www.usdumzuege.de
- added {USER} field for authenticated user
- added possibility to use email address of authenticated user

#### 0.4.4 stable (Frank Heyne: 23-Jan-2011)
- minor changes

#### 0.4.3 stable (Frank Heyne: 16-Jan-2011)
- some security fixes (CSRF, droplets)

#### 0.4.2 stable (Frank Heyne: 13-Dec-2010)
- some security fixes (Cross Page Writing, Reading and Deleting)

#### 0.4.1 stable (Frank Heyne: 01-Jan-2010)
- minor bug fixes
- slight usability improvements in the backend
- NL language file from Argos
- To protect your customisatione, update from older version will now no longer overwrite the following files:
-- frontend.css (update is delivered as frontend.css.txt)
-- backend.css  (update is delivered as backend.css.txt)
-- private.php (update is delivered as private.php.txt)
-- You need to manually compare the files and decide which versions to use!

#### 0.3.1 beta (Frank Heyne: 31-Jul-2009)
- bug fixes
 
#### 0.3.2 RC1 (Frank Heyne: 20-Aug-2009)
- added class for captcha

#### 0.4.0 stable (Frank Heyne: 20-Nov-2009)
- now submitted forms with errors will repaint form at once
- added {ERRORTEXT} for some field types
- now multiple forms are allowed on a page
- now separate texts are configurable for
- email to site owner
- confirmation mail to user
- text on web page
- text saved in submissions table
- removed unnecessary field in settings table
- improved frontend.css
- added optional enumeration for radio and checkbox
- added optional default values for radio and checkbox
- documentation for this module now consists of 20 pages and is available online at the module home page (see below)
- module now requires WB 2.8 + patch for Captcha (included)

#### 0.3.0 beta (Frank Heyne: 30-Jul-2009)
- added field type HTML-Code
- added readonly fields
- added place holder {CLASSES}
- added interface for private functions
- made module compatible with WB 2.8
- minor improvements
- thank goes to Stefek for icons for Backend and to Chio for alternate frontend.css

#### 0.2.2 RC3 (Frank Heyne: 02-Jul-2009)
- now multiple email addresses work again if you put one on each line
- minor improvements, thank goes to Argos for bug reports

#### 0.2.1 RC2 (Frank Heyne: 20-Jun-2009)
- now multiple email addresses in one line in the backend are allowed as follows:
- Recipient X <address1,address2,address3>
- minor improvements 

#### 0.2.0 RC1 (Frank Heyne: 10-Jun-2009)
- valid html code for the backend (as far as module code is concerned)
- minor improvements 

#### 0.1.4 beta (Frank Heyne: 31-May-2009)
- bug fix in Settings (save_settings.php) 
- spelling error fix in html (view.php) 

#### 0.1.3 beta (Frank Heyne: 29-May-2009)
- bug fix in Search function (install.php and upgrade.php) 

#### 0.1.2 beta (Frank Heyne: 26-May-2009)
- bug fix in Java Script (view.php) 
- status changed to beta

#### 0.1.0 alpha (Frank Heyne: 16-May-2009)
- first public version 
- derived from FormX version 2.4.8 (authors: Ryan Djurovich & Rudolph Lartey - additions John Maats - PCWacht - Greg Bosen - Stefano Ricci)
- Added fields to submissions table:
-- Added field for IP address
-- Added field for time stamp when form was sent to browser
-- Added field for session id (important for multipage forms)
-- Added field is_following (important for multipage forms)
-- Added new tables results_xxx
-- contains one column for each field, regardles of its page_id
-- contains one line for each session_id
-- column with timestamp when the first form of a session has been sent to the user
-- column with timestamp when the last form of a session has been sent back to the server
-- column with referer page from where the form was called
- Enhanced multilanguage support
