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

defined('_JEXEC') or die;

include_once dirname(__FILE__) . DS.'cleardata.html.php';
require_once $_HANDOUT->getPath('classes', 'cleardata');
/*
switch ($task) {
    case 'remove':
        clearData( $cid );
        break;

    default:
    case 'show':
        showClearData();
}
*/
function clearData( $cid = array() )
{
    HANDOUT_token::check() or die('Invalid Token');
	$app = &JFactory::getApplication();
    $msgs=array();

    $cleardata = new HANDOUT_Cleardata( $cid );
    $cleardata->clear();
    $rows = & $cleardata->getList();
    foreach( $rows as $row ){
        $msgs[] = $row->msg;
    }
    $app->redirect( 'index.php?option=com_handout&section=config', implode(' | ', $msgs));
}

function showClearData(){
    $cleardata = new HANDOUT_Cleardata();
    $rows = & $cleardata->getList();
	HTML_HandoutClear::showClearData( $rows );
}
