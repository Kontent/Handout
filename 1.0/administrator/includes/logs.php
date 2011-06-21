<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: logs.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );

include_once dirname(__FILE__) . '/logs.html.php';
require_once($_HANDOUT->getPath('classes', 'plugins'));
JArrayHelper::toInteger(( $cid ));

switch ($task) {
    case "remove":
        removeLog($cid);
        break;
    case "show" :
    default :
        showLogs($option);
}

function showLogs($option) {
	$database = &JFactory::getDBO();
	$mainframe = &JFactory::getApplication();
	global $sectionid;

    // request
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 10);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}{$sectionid}limitstart", 'limitstart', 0);
    $search = $mainframe->getUserStateFromRequest("search{$option}{$sectionid}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));
    $wheres = array();
    $wheres2 = array();

    // get the total number of records
    $query = "SELECT count(*)"
            ."\n FROM #__handout_log";
    $database->setQuery($query);
    $total = $database->loadResult();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    //  WHERE clause
    //$wheres[] = "(l.log_user = u.id OR l.log_user = 0)";
    $wheres[] = "l.log_docid = d.id";
    if ($search) {
        $wheres[] = "( LOWER(l.log_ip) LIKE '%$search%'"
                    ."\n OR LOWER(l.log_datetime) LIKE '%$search%'"
                    ."\n OR LOWER(IF(l.log_user, u.name, '".JText::_('COM_HANDOUT_ANONYMOUS')."')) LIKE '%$search%'"
                    ."\n OR LOWER(d.docname) LIKE '%$search%'"
                    ."\n OR LOWER(l.log_browser) LIKE '%$search%'"
                    ."\n OR LOWER(l.log_os) LIKE '%$search%' )";
    }
    $where = "\n WHERE " . implode(' AND ', $wheres) ;

    $wheres2[] = "l.log_docid = d.id";
    if ($search) {
        $wheres2[] = "( LOWER(l.log_ip) LIKE '%$search%'"
                    ."\n OR LOWER(l.log_datetime) LIKE '%$search%'"
                    ."\n OR LOWER('".JText::_('COM_HANDOUT_ANONYMOUS')."') LIKE '%$search%'"
                    ."\n OR LOWER(d.docname) LIKE '%$search%'"
                    ."\n OR LOWER(l.log_browser) LIKE '%$search%'"
                    ."\n OR LOWER(l.log_os) LIKE '%$search%' )";
    }
    $where2 = "\n WHERE " . implode(' AND ', $wheres2) ;




    // NAvigation
    
    require_once (JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'pagination.php');
    $pageNav = new JPagination($total, $limitstart, $limit);

    // Query
    $query = "( SELECT l.*, u.name AS user, d.docname"
            ."\n FROM #__handout_log AS l, #__users AS u, #__handout AS d "
            .$where
            ."\n AND l.log_user = u.id )"
            ."\n UNION "
            ."( SELECT l.*, '".JText::_('COM_HANDOUT_ANONYMOUS')."' AS user, d.docname"
            ."\n FROM #__handout_log AS l, #__handout AS d "
            .$where2
            ."\n AND l.log_user = 0"
            .")"
            ."\n ORDER BY log_datetime DESC";
    $database->setQuery($query, $limitstart, $limit);
    $rows = $database->loadObjectList();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    HTML_HandoutLogs::showLogs($option, $rows, $search, $pageNav);
}

function removeLog($cid)
{
    HANDOUT_token::check() or die('Invalid Token');
    $mainframe = &JFactory::getApplication();

    $database = &JFactory::getDBO(); 
    $_HANDOUT_USER = &HandoutFactory::getHandout();
    
    $log = new HandoutLog($database);
    $rows = $log->loadRows($cid); // For log plugins

    if ($log->remove($cid)) {
        if ($rows) {
            $logbot = new HANDOUT_plugin('onLogDelete');
            $logbot->setParm('user' , $_HANDOUT_USER);
            $logbot->copyParm('process' , 'delete log');
            $logbot->setParm('rows' , $rows);
            $logbot->trigger(); // Delete the logs
        }
        $mainframe->redirect("index.php?option=com_handout&section=logs");
    }
}
