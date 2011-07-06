<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: codes.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined ( '_JEXEC' ) or die ( 'Restricted access' );

include_once dirname(__FILE__) . '/codes.html.php';
JArrayHelper::toInteger(( $cid ));

switch ($task) {
    case "publish" :
        publishCode($cid, 1);
        break;
    case "unpublish":
        publishCode($cid, 0);
        break;
    case "edit":
        $cid = (isset( $cid[0] )) ? $cid[0] : 0;
        editCode($option, $cid);
        break;
    case "delete":
    case "remove":
        removeCode($cid, $option);
        break;
    case "apply":
    case "save":
        saveCode($option);
        break;
    case "cancel":
        cancelCode($option);
        break;
    case "show":
    default :
        showCodes($option);
}

function editCode($option, $uid)
{
    $database = &JFactory::getDBO();

    // disable the main menu to force user to use buttons
    $_REQUEST['hidemainmenu']=1;

    $row = new HandoutCodes($database);
    $row->load($uid);

	// build the html radio buttons for published
    $lists['published'] = JHTML::_('select.booleanlist','published', 'class="inputbox"', $row->published);

	// build the html select list for downloads
    $query = "SELECT id AS value, docname AS text"
     . "\n FROM #__handout"
     . "\n ORDER BY docname" ;

	$database->setQuery($query);

	$doclist[]         	= JHTML::_('select.option',  '0', JText::_( '- Select Download -' ), 'value', 'text' );
    $doclist        	= array_merge( $doclist, $database->loadObjectList() );
    $lists['downloads'] = JHTML::_('select.genericlist', $doclist, 'docid', 'class="inputbox" size="1"','value', 'text', intval( $row->docid ) );

	//build the html radio buttons for usage
	$usage = HandoutCodes::getCodesUsage();
	$lists['usage'] = JHTML::_( 'select.radiolist', $usage, 'usage', '', 'value', 'text', $row->usage );

	//fetch the set of codes already selected
	$query="SELECT name FROM #__handout_codes";
	$database->setQuery($query);
	$lists['usedcodes'] = $database->loadResultArray();

    HTML_HandoutCodes::editCode($option, $row, $lists);
}

function saveCode($option)
{
    HANDOUT_token::check() or die('Invalid Token');

    $database = &JFactory::getDBO();
    $task = JRequest::getCmd('task');
    $mainframe = &JFactory::getApplication();

    $row = new HandoutCodes($database);
    //$isNew = ($row->id == 0);

    if (!$row->bind(HANDOUT_Utils::stripslashes($_POST))) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }
    if (!$row->check()) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }
    if (!$row->store()) {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }
    $row->checkin();

    if( $task == 'save' ) {
        $url = 'index.php?option=com_handout&section=codes';
    } else { // $task = 'apply'
        $url = 'index.php?option=com_handout&section=codes&task=edit&cid[0]='.$row->id;
    }

    $mainframe->redirect( $url, JText::_('COM_HANDOUT_SAVED_CHANGES'));
}

function cancelCode($option)
{
    $database = &JFactory::getDBO();
    $mainframe = &JFactory::getApplication();
    $row = new HandoutCodes($database);
    $row->bind(HANDOUT_Utils::stripslashes($_POST));
    $row->checkin();
    $mainframe->redirect("index.php?option=$option&section=codes");
}

function showCodes($option)
{
    $database = &JFactory::getDBO();
    $mainframe = &JFactory::getApplication();
    global $sectionid;

    $catid = (int) $mainframe->getUserStateFromRequest("catid{$option}{$sectionid}", 'catid', 0);
    $limit = (int) $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 10);
    $limitstart = (int) $mainframe->getUserStateFromRequest("view{$option}{$sectionid}limitstart", 'limitstart', 0);
    $search = $mainframe->getUserStateFromRequest("search{$option}{$sectionid}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));
    $where = array();
    if ($search) {
        $where[] = "LOWER(c.name) LIKE '%$search%'";
    }
    // get the total number of records
    $database->setQuery("SELECT count(*) FROM #__handout_codes AS c" . (count($where) ? "\nWHERE " . implode(' AND ', $where) : ""));
    $total = $database->loadResult();
    echo $database->getErrorMsg();

    $id = JRequest::getVar( 'id', 0);

    require_once (JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'pagination.php');
    $pageNav = new JPagination($total, $limitstart, $limit);

    $query = "SELECT c.id, c.name, c.published, c.usage, h.docname, cat.name as category"
            ."\n FROM #__handout_codes AS c"
			."\n LEFT JOIN #__handout AS h"
			."\n ON c.docid=h.id"
			."\n LEFT JOIN #__categories AS cat"
			."\n ON cat.id=h.catid"
            .(count($where) ? "\n WHERE " . implode(' AND ', $where) : "")
            ."\n ORDER BY c.name";
    $database->setQuery( $query, $limitstart,$limit);
    $rows = $database->loadObjectList();

    // show the beginning of each code text
    foreach ( $rows as $key=>$row ) {
        $rows[$key]->code = substr( strip_tags($row->code), 0, 100 ) . ' (...)';
    }

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    HTML_HandoutCodes::showCodes($option, $rows, $search, $pageNav);
}

function removeCode($cid, $option)
{
    HANDOUT_token::check() or die('Invalid Token');
    $database = &JFactory::getDBO();

    $code = new HandoutCodes($database);
    if ($code->remove($cid)) {
        $mainframe = &JFactory::getApplication();
		$mainframe->redirect("index.php?option=com_handout&section=codes");
    } else {
    	echo "<script> alert('Problem removing codes'); window.history.go(-1);</script>\n";
        exit();
    }

}


function publishCode($cid, $publish = 1)
{
    HANDOUT_token::check() or die('Invalid Token');
    $database = &JFactory::getDBO();

    $code = new HandoutCodes($database);
    if ($code->publish($cid, $publish)) {
        $mainframe = &JFactory::getApplication();
		$mainframe->redirect("index.php?option=com_handout&section=codes");
    }
}




