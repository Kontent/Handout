<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
 /**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
?>
<!--

Changelog
------------

Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

------------
29-Jun-2011 Arvind
^ Moved all the parameters for the 3 plugins - buttons, notify, thumbs - to admin config panel

28-Jun-2011 Arvind
+ Completed front end of Download Codes section
+ Download code in log table
^ Cleaned up the last bits of index2.php in admin code
- Removed "Register" checkbox from Download Codes admin area
# Fixed bug with delete icons in the admin screens
! Run Query: ALTER TABLE `jos_handout_codes` DROP `user`, DROP `register`;
^ Moved user and register values to menu params from Codes page

25-Jun-2011 Arvind
- Special Compatability constants in defines.php
+ Languages from xml file into languages dropdown and frontend meta tag
^ Modified Import from Folder to take a folder in site root rather than zip file in handouts folder
# Fixed Thumbs plugin and display in front end
# Fixed home link from document details page only when there is a direct menu link to a document page

24-Jun-2011 Arvind
^ Modified install.sql to handle new fields
+ New table handout_codes to handle Download Codes
+ Download Codes admin functionality
# Added missing xml file for plg_handout_search in manifest.xml
# Added missing tmpl folder for quickicon module
+ Included Tooltip behaviour to some admin screens
# Fixed link to Codes section from Handout cpanel

22-Jun-2011 Arvind
Testing commit on Github

21-Jun-2011 Severdia
# Cleaned up import screen, new lang. strings

21-Jun-2011 Arvind
+ New field mtree_id to enable linking a document with a mtree node
^ Changed mod_handout_docs to filter documents belonging to a mtree listing

20-Jun-2011 Arvind
+ New field kunena_discuss_id to enable linking a document with a discussion forum topic
^ Modified Kunena Discuss integration based on the above new field

20-Jun-2011 Severdia
# Fixed spacing on install screen
- Removed cpanel button from toolbar

19-Jun-2011 Arvind
^ Modified Content Plugin to work with Kunena Discuss
^ Merge Approve/Publish process
^ Add This Google Analytics code
^ Menu Configuration settings to over-ride general configuration values
^ Rename duplicate files during migration

15-Jun-2011 Arvind
# Fixed bugs with component install / uninstall
^ Reworked the component installation to include all core modules and plugins
^ Renamed plg_handout_standardbuttons as plg_handout_buttons

14-Jun-2011 Arvind
- Removed all instances of approval and merged approve/publish steps
+ Sharing link Google Analytics code
+ Menu over-ride for general configuration values: per-page and ordering

13-Jun-2011 Arvind
+ Migration from RubberDoc, Server Folder

12-Jun-2011 Severdia
- Removed CP button from config toolbar
- Removed approved column from document list
+ Added tooltips
+ Added import icons and tooltips
+ New language strings/keys for admin

12-Jun-2011 Arvind
# Added "JHTML::_('behavior.tooltip');" to config.html.php to enable tooltip display
+ Migration from DOCman, JoomDoc, RokDownloads
^ Changed J1.0 style index2.php links to index.php links
^ Changed constant name in defines.php from _ICONPATH to _IMAGESPATH_ADMIN

11-Jun-2011 Severdia
# Fixed frontend tooltip
+ Added new tooltips on documents
+ Added new tooltips on documents in doc module
+ Config setting to show/hide empty category notice
# Fixed bug in search results, new styles
^ Updated language strings

9-Jun-2011 Severdia
^ Replaced all old tooltips with J 1.5/1.6 versions (still a few left)

9-Jun-2001 Arvind
# Removed additional '</span>' tags from tooltips
# Fixed bug in IE for Clear Data

8-Jun-2011 Arvind
- Removed traces of Mambots, legacy function calls (mos*) - some have been left in as it is not yet clear if they should be removed
- Removed redundant classes - Handout_compat, Handout_install
- Removed file includes/modules.php
- toolbar.handout.class15.php (moved contents into toolbar.handout.class.php)
^ Changed class and function names with "dm" to "Handout"
# Fixed path to logo_header and kontet_extensions_logo in install file
^ Completed functionality of Clear Data within Config tab
# Made batch mode upload of zip file functional - was not working in JoomDOC either
# Fix bug arising from removing Category Name 
^ Show last updated on only if the doc has been updated (to prevent 1970 being shown when a doc has not been updated)

6-Jun-2011 Severdia
+ Added Google +1 to sharing links, updated widget ID
^ Updated and customized the Google +1 link
# Fixed bugs in language XML file.
+ Added plural handling in doc module
^ Change all text in doc module to strings/keys
+ Added minimized CSS file for frontend (needs config setting)
^ Renamed icon image
+ Added upgrade alert plug-in (needs work)

5-Jun-2011 Severdia
+ Added XML file for languages
^ Changed params XML file to UTF-8

4-Jun-2011 Arvind
- Modules lister, top_downloads, latest_downloads moved to trash
+ Module handout_docs is a cleaned up MVC version of handout_lister module 

3-Jun-2011 Arvind
+ Andrew's Single Package Installer
- Docman's old Package Installer code (moved alibraries to trash)
^ Created a single installation package with five admin modules and two plugins
+ Added index.html files to module and plugin folders
^ All admin modules into MVC

3-Jun-2011 Severdia
+ Added icon for download codes
^ Updated default config.

2-Jun-2011 Arvind
^ Modified Lister module to display icon properly
# Footer of Admin > Documents was pointing to frontend footer
# Changed footer2.php to footer.php in a frontend view template file
^ Reorganised the submenu and control panel links
+ Dummy link to Codes section
^ Moved footer.php inside #handout div
+ File Version config
+ File Language config
^ Renamed docsubmitedby to docsubmittedby in DB table and code
+ New DB table fields - doc_meta_keywords, doc_meta_description, docversion, doclanguage
+ Config values for file version, file language, item filesize and item filetype
^ Statistics -? Top Downloads
+ Google Analytics code field and corresponding JS in footer.php
+ Metadata description and keywords for documents and categories
- Removed Category Name from Categories
+ Show/Hide mechanism for Add this icons

29-May-2011 Severdia
^ Language sync between frontend and backend, removed duplicate strings
+ New CSS and image for doc status
+ New sharing links on doc details pages and category pages (needs setting in config)

28-May-2011 Severdia
+ Added CSS file to frontend modules
^ Overhaul frontend language strings
^ New CSS styles
+ Added prefix and suffix option to modules
^ Replaced hardcoded languages with keys
^ Changed uploading image
^ Fixed footer
^ New default category icon
^ Misc. class changes
+ New quick icon module for Joomla admin

27-May-2011 Severdia
^ Overhauled all backend language strings and keys for bad grammar, consistency and redundancy
^ Changed "Licenses" to "Agreements"
# Fixed layout issues on backend
^ Moved inline CSS in backend to CSS file
# Fixed all footer links on backend
^ Changed toolbar buttons, added quick icon to control panel
^ Changed support URL
+ Added new meta fields in edit category and edit document (need to be finished)
+ Added new fields in config (need to be finished)

26-May-2011 Severdia
^ Fixed path to CSS file
+ New category icon 64x64
+ Added tmpl directories and CSS files for modules before making them MVC
^ Minor cleanup

26-May-2011 Arvind
# Several small changes to configuration, icons, buttons, footer, missing tags

25-May-2011 Severdia
# Fixed path to CSS file
^ Updated the default config.

25-May-2011 Arvind
^ Made frontend component views editable via templates
- Removed themes completely, including Savant2
^ Moved theme configuration options under main configuration screen
+ Added 1.0-Trash as a branch to hold the deleted themes and Savant2 folder until all testing is completed

24-May-2011 Severdia
^ Changed configuration layout, new tabs
# Fixed header icons on backend

16-May-2011 Severdia
^ Changed name of lister module
^ Added media folder in com_handout, copied CSS & images there
^ Clean up Top Downloads & Latest Downloads modules
^ More module cleanup & new module CSS

16-May-2011 Arvind
^ Clean up references to Joomla 1.0 functions
^ Clean up of global variables e.g. mosConfig*
^ Modified deprecated PHP functions e.g. eregi (except in phpthumbs)
^ Prepared 3 site modules - top_downloads, latest_downloads, lister

13-May-2011 Severdia
^ Clean up theme files, remove old classes & IDs
^ Update theme CSS
# Language file cleanup
^ Clean up and validate category, document, search layouts
^ Fix Notify email format
- Remove some Joomla 1.0 styles from admin
+ Added ePub MIME type
^ Changes to admin- new config button in toolbar
^ Admin cleanup and missing language strings
^ Admin config cleanup
+ New admin language strings
+ New button images
^ License updates

13-May-2011 Arvind
^ Modified plugin_thumbs and plugin_notify
^ Sorted out modules - added "_admin" suffix to admin module folders, removed duplicate modules
^ Four admin modules ready to be installed

12-May-2011 Severdia
+ New icons
^ Update theme CSS

12-May-2011 Arvind
^ Manifest.xml
- language/admin folder
- All references to Mambo and J10 versions including some files:
- HANDOUT_compat10
- HANDOUT_compat15
- HANDOUT_jbrowser
- HANDOUT_jobject
^ plugin_thumbs and plugin_notify

11-May-2011 Severdia
^ Theme changes, new CSS

11-May-2011 Arvind
^ Modifed plg_handout_search
^ Consolidated language .ini and .php files into two .ini files
^ Changed references to language strings to JText::_() references across the site 

10-May-2011 Severdia
^ Renamed search plug-in

10-May-2011 Arvind
^ Modified plg_search_handout
^ Modified plg_handout_doclink
- Removed plg_handout_search from 1.0-addons
^ Moved plugins between 1.0 and 1.0-addons folders

10-May-2011 Arvind
^ Modified plg_handout_standardbuttons
^ Language fixes
^ Changed require to require_once in line 138 of administrator/includes/themes.php
^ Revised constant JText::_('COM_HANDOUT_VERSION') to JText::_('COM_HANDOUT_VERSION_NUMBER') in defines.php and corresponding files
^ Removed extra "1" after Name of Theme

9-May-2011 Arvind
^ Changed folders, filenames, classes and other instances of JoomDOC and DOCman to Handout
^ Rearranged some files and folders to create a proper component installation package
^ Modified manifest.xml to reflect above changes. 
+ Added htaccess file to prevent access to the /handouts folder

9-May-2011 Severdia
^ Redo frontend template
+ New CSS styles
^ Language fixes

1-May-2011 Severdia
^ Changed names to Handout
+ New CSS styles

30-April-2011 Severdia
+ Added JX Finder plug-ins

28-April-2011 Severdia
- Removed Joomla 1.0 stuff
+ Update icons and new icon set for documents

27-April-2011 Severdia
^ Changed icons to Joomla 1.6-style
^ Moved extensions to top level of folder structure
^ Renamed editor plugin
^ Copied installation files to install folder
^ Set up new language files

-------------------- 1.0 Pre-release [26-April 2011] ------------------

26-April-2011 Severdia
! Here we go.


-->