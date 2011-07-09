<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */

defined('_JEXEC') or die('Restricted access');

include_once dirname(__FILE__).DS.'doclink.html.php';
include_once dirname(__FILE__).DS.'defines.php';

global $_HANDOUT;

// Load classes and language
require_once $_HANDOUT->getPath('classes', 'utils');
require_once $_HANDOUT->getPath('classes', 'file');
require_once $_HANDOUT->getPath('classes', 'model');

JRequest::setVar('tmpl', 'component');

$lang = JFactory::getLanguage();
$lang->load('plg_editors-xtd_handoutdoclink');

function showDoclink() {
    $mainframe = &JFactory::getApplication();

    $assets = COM_HANDOUT_MEDIA;


    // add styles and scripts
    $doc =& JFactory::getDocument();
    JHTML::_('behavior.mootools');
    $doc->addStyleSheet($assets.'/css/doclink.css');
    $doc->addScript($assets.'/js/dlutils.js');
    $doc->addScript($assets.'/js/popup.js');
    $doc->addScript($assets.'/js/dialog.js');


    $rows = HANDOUT_utils::categoryArray();

    HTML_HandoutDoclink::showDoclink($rows);
}

function showListview(){
    $mainframe = &JFactory::getApplication();

    global $_HANDOUT;

	$assets = COM_HANDOUT_MEDIA;
    // add styles and scripts
    $doc =& JFactory::getDocument();
    JHTML::_('behavior.mootools');
    $doc->addStyleSheet($assets.'/css/doclink.css');
    $doc->addScript($assets.'/js/sortabletable.js');
    $doc->addScript($assets.'/js/listview.js');
    $doc->addScript($assets.'/js/dldialog.js');


    if (isset($_REQUEST['catid'])) {
        $cid =  intval($_REQUEST['catid']);
    } else {
        $cid = 0;
    }
        //get folders
        $cats = HANDOUT_Cats::getChildsByUserAccess($cid);

        //get items
        if ($cid) {
            $docs = HANDOUT_Docs::getDocsByUserAccess($cid, 'name', 'ASC', 999, 0);
        } else {
            $docs = array();
        }


        //if ($entries_cnt)
        HTML_HandoutDoclink::createHeader();
        HTML_HandoutDoclink::createFolders($cats,$cid);
        HTML_HandoutDoclink::createItems($docs, $cid);
        HTML_HandoutDoclink::createFooter();

}