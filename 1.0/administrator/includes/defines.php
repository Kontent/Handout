<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: defines.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );

// Put all static value defines here that are neither language nor
// configuration specific

// Paths
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// Version
define('COM_HANDOUT_VERSION_NUMBER', '1.0');

// Help URL
define('COM_HANDOUT_HELP_URL', 'http://extensions.kontentdesign.com/support');

// HANDOUT mainframe types
define('COM_HANDOUT_TYPE_UNKNOWN'       , 0);
define('COM_HANDOUT_TYPE_SITE'			, 1);
define('COM_HANDOUT_TYPE_ADMIN'			, 2);
define('COM_HANDOUT_TYPE_MODULE'		, 3);
define('COM_HANDOUT_TYPE_DOCLINK'		, 4);

// Permissions for Documents
// NB: There one special flags used (don't use!):
// _NOOWNER is a special flag that is used to tell us they
// didn't pick from a list.

define('COM_HANDOUT_PERMIT_GROUP' 		, -10); // Handout groups (if < JText::_('COM_HANDOUT_OWNER_GROUP'))
define('COM_HANDOUT_PERMIT_NOOWNER' 	, -9); 	// Special flag

define('COM_HANDOUT_PERMIT_PUBLISHER'  	, -3);  //Joomla publisher group
define('COM_HANDOUT_PERMIT_EDITOR'    	, -6);  //Joomla editor group
define('COM_HANDOUT_PERMIT_AUTHOR'   	, -4);  //Joomla author group

define('COM_HANDOUT_PERMIT_CREATOR'   	, -2); 	// Permit the creator only
define('COM_HANDOUT_PERMIT_EVERYONE' 	, -1);
define('COM_HANDOUT_PERMIT_EVERYBODY' 	, -1); 	// Alias...
define('COM_HANDOUT_PERMIT_NOACCESS' 	, COM_HANDOUT_PERMIT_EVERYBODY);

define('COM_HANDOUT_PERMIT_REGISTERED'	, 0);
define('COM_HANDOUT_PERMIT_USER' 		, 0); // if > COM_HANDOUT_PERMIT_USER

// Permissions for Category Access Level (1.2+)
define('COM_HANDOUT_ACCESS_PUBLIC' 		, 0);
define('COM_HANDOUT_ACCESS_REGISTERED' 	, 1);
define('COM_HANDOUT_ACCESS_SPECIAL' 	, 2);

// Grant GUEST Users access (Against config 'registered')
define('COM_HANDOUT_GRANT_NO' 	, 0);
define('COM_HANDOUT_GRANT_X' 	, 1); // Execute == browse (like unix)
define('COM_HANDOUT_GRANT_RX' 	, 2); // Read/Exe == download/browse

define('COM_HANDOUT_GRANT_NONE' , COM_HANDOUT_GRANT_NO);

define('COM_HANDOUT_ASSIGN_NONE' 			 , 0);
define('COM_HANDOUT_ASSIGN_BY_AUTHOR' 		 , 0x0001);
define('COM_HANDOUT_ASSIGN_BY_EDITOR' 		 , 0x0002);
define('COM_HANDOUT_ASSIGN_BY_AUTHOR_EDITOR' , 0x0003);

define('COM_HANDOUT_AUTHOR_NONE' 			, 0);
define('COM_HANDOUT_AUTHOR_CAN_READ' 		, 0x0001);
define('COM_HANDOUT_AUTHOR_CAN_EDIT' 		, 0x0002);
define('COM_HANDOUT_AUTHOR_CAN_READ_EDIT' 	, 0x0003);

// Validation for uploads
define('COM_HANDOUT_VALIDATE_NAME' 		, 0x0001);
define('COM_HANDOUT_VALIDATE_PATH' 		, 0x0002);
define('COM_HANDOUT_VALIDATE_EXT' 		, 0x0004); // Extension
define('COM_HANDOUT_VALIDATE_SIZE' 		, 0x0008);
define('COM_HANDOUT_VALIDATE_EXISTS'	, 0x0010);
define('COM_HANDOUT_VALIDATE_PROTO' 	, 0x0020); // Protocol (URL transfer )

// Hard-coded filename regexes to reject, separate by '|'.
define('COM_HANDOUT_FNAME_REJECT'       , "\.htaccess|Thumbs\.db");

// Meta-validate values
define('COM_HANDOUT_VALIDATE_ADMIN' 	, COM_HANDOUT_VALIDATE_NAME | COM_HANDOUT_VALIDATE_PATH | COM_HANDOUT_VALIDATE_PROTO | COM_HANDOUT_VALIDATE_EXISTS);
define('COM_HANDOUT_VALIDATE_USER' 		, 0x00ff);
define('COM_HANDOUT_VALIDATE_ALL' 		, 0x00ff);
define('COM_HANDOUT_VALIDATE_USER_ALL'       , 0x00ff); // alias
define('COM_HANDOUT_VALIDATE_DEFAULT'	, 0x00ff);

// Special tags for files:
define('COM_HANDOUT_DOCUMENT_LINK' , "Link: ");
define('COM_HANDOUT_DOCUMENT_LINK_LNG', 6);

// Images
define( 'COM_HANDOUT_IMAGESPATH_ADMIN', '/administrator/components/com_handout/images/');
define( 'COM_HANDOUT_MEDIA', JURI::root(true).'/media/com_handout/');
define('COM_HANDOUT_IMAGESPATH', JURI::root(true) . '/components/com_handout/media/images/');
define('COM_HANDOUT_CSSPATH', 'components/com_handout/media/css/');
define('COM_HANDOUT_TOOLTIP_ICON', '../../../media/com_handout/images/icon-16-tooltip.png');

//Other
define('COM_HANDOUT_DOC_LANGUAGE_XML', JPATH_ROOT . '/administrator/components/com_handout/handout.doc.languages.xml' );
