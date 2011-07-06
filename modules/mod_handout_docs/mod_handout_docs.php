<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: mod_handout_latest_downloads.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	Improved by JoomDOC by Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

// Attach the Handout stylesheet to the document head
JHTML::stylesheet('handout.css', 'components/com_handout/media/css/');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_handout' . DS . 'helpers' . DS . 'factory.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_handout' . DS . 'handout.class.php');

global $_HANDOUT;
if (!$_HANDOUT) {
	$_HANDOUT = HandoutFactory::getHandout();
}

require_once ($_HANDOUT->getPath('classes', 'model'));

// Get the parameters
$show_icon 		 = abs($params->def( 'show_icon', 1 ));
$show_counter	 = abs($params->def( 'show_counter', 1 ));
$show_category 	 = abs($params->def( 'show_category', 1 ));
$moduleclass_sfx = $params->get( 'moduleclass_sfx' );
$text_pfx     	 = $params->def( 'text_pfx', '' );
$text_sfx     	 = $params->def( 'text_sfx', '' );

$class_prefix 	= "hmodule-prefix" . $moduleclass_sfx;
$class_suffix 	= "hmodule-suffix" . $moduleclass_sfx;

$is_mtree_listing = $params->get( 'is_mtree_listing', 0);

$can_display = true;
if ($is_mtree_listing) {
	//check to make sure this is a mtree listing page
	$link_id = JRequest::getVar('link_id', '');
	if ((JRequest::getVar('option') == 'com_mtree') && (JRequest::getVar('task') == 'viewlink') && ($link_id)) {
		$can_display = true;
	}
	else {
		$can_display = false;
	}
}

$menuid = $_HANDOUT->getMenuId();

if ($can_display) {
	$rows = modHandoutdocsHelper::getDocs($params);
	require(JModuleHelper::getLayoutPath('mod_handout_docs'));
}
