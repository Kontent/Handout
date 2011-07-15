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

include_once dirname(__FILE__) . '/categories.html.php';
JArrayHelper::toInteger($cid);

switch ($task) {
	case "edit":
		editCategory($option, $cid[0]);
		break;
	case "new":
		editCategory($option, 0);
		break;
	case "cancel":
		cancelCategory();
		break;
	case "save":
	case "apply":
		saveCategory();
		break;
	case "delete":
	case "remove":
		removeCategories($option, $cid);
		break;
	case "publish":
		publishCategories("com_handout", $id, $cid, 1);
		break;
	case "unpublish":
		publishCategories("com_handout", $id, $cid, 0);
		break;
	case "orderup":
		orderCategory($cid[0], -1);
		break;
	case "orderdown":
		orderCategory($cid[0], 1);
		break;
	case "accesspublic":
		accessCategory($cid[0], 0);
		break;
	case "accessregistered":
		accessCategory($cid[0], 1);
		break;
	case "accessspecial":
		accessCategory($cid[0], 2);
		break;
	case "show":
	default:
		showCategories();
}

function showCategories()
{
	$option = JRequest::getCmd('option');
	$app = &JFactory::getApplication();
	$database = &JFactory::getDBO();
	$user = &JFactory::getUser();
	$list_limit = $app->getCfg('list_limit');
	global $menutype;

	$section = "com_handout";

	$sectionid = $app->getUserStateFromRequest("sectionid{$section}{$section}", 'sectionid', 0);
	$limit = $app->getUserStateFromRequest("viewlistlimit", 'limit', $list_limit);
	$limitstart = $app->getUserStateFromRequest("view{$section}limitstart", 'limitstart', 0);
	$levellimit = $app->getUserStateFromRequest("view{$option}limit$menutype", 'levellimit', 10);

	$query = "SELECT  c.*, c.checked_out as checked_out_contact_category, c.parent_id as parent, g." . COM_HANDOUT_FIELD_GROUP_NAME
			. " AS groupname, u.name AS editor" . "\n FROM #__categories AS c" . "\n LEFT JOIN #__users AS u ON u.id = c.checked_out" . "\n LEFT JOIN "
			. COM_HANDOUT_TABLE_GROUPS . " AS g ON g.id = c.access" . "\n WHERE c." . COM_HANDOUT_FIELD_SECTION . "='$section'" . "\n AND c.published != -2"
			. "\n ORDER BY parent_id,ordering";

	$database->setQuery($query);

	$rows = $database->loadObjectList();

	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}
	// establish the hierarchy of the categories
	$children = array();
	// first pass - collect children
	foreach ($rows as $v) {
		$pt = $v->parent;
		$list = @$children[$pt] ? $children[$pt] : array();
		array_push($list, $v);
		$children[$pt] = $list;
	}
	// second pass - get an indent list of the items
	jimport('joomla.html.html.menu');
	$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max(0, $levellimit - 1));
	$list = is_array($list) ? $list : array();

	$total = count($list);

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	//$levellist = JHTML::_('select.integerlist',1, 20, 1, 'levellimit', 'size="1" onchange="document.adminForm.submit();"', $levellimit);
	// slice out elements based on limits
	$list = array_slice($list, $pageNav->limitstart, $pageNav->limit);

	$count = count($list);
	// number of Active Items
	for ($i = 0; $i < $count; $i++) {
		$query = "SELECT COUNT( d.id )" . "\n FROM #__handout AS d" . "\n WHERE d.catid = " . $list[$i]->id;
		// . "\n AND d.state <> '-2'";
		$database->setQuery($query);
		$active = $database->loadResult();
		$list[$i]->documents = $active;
	}
	// get list of sections for dropdown filter
	$javascript = 'onchange="document.adminForm.submit();"';

	if (JRequest::getString('task') == 'element') {
		HTML_HandoutCategories::showToSelect($list, $pageNav, $lists);
	}
	else {
		HTML_HandoutCategories::show($list, $user->id, $pageNav, $lists, 'other');
	}
}

function editCategory($section = '', $uid = 0)
{
	$database = &JFactory::getDBO();
	$user = &JFactory::getUser();
	$app = JFactory::getApplication();

	// disable the main menu to force user to use buttons
	$_REQUEST['hidemainmenu'] = 1;

	$type = JRequest::getVar('type', '');
	$redirect = JRequest::getVar('section', '');

	$row = new HandoutCategory($database);
	// load the row from the db table
	$row->load($uid);
	// fail if checked out not by 'me'
	if ($row->checked_out && $row->checked_out <> $user->id) {
		$app->redirect('index.php?option=com_handout&task=categories', 'The category ' . $row->title . ' is currently being edited by another administrator.');
	}

	if ($uid) {
		// existing record
		$row->checkout($user->id);
		// code for Link Menu
	}
	else {
		// new record
		$row->section = $section;
		$row->published = 1;
	}
	// make order list
	$order = array();
	$fName = COM_HANDOUT_FIELD_SECTION;
	$database->setQuery("SELECT COUNT(*) FROM #__categories WHERE ".COM_HANDOUT_FIELD_SECTION."='".$row->$fName."'");
	$max = intval($database->loadResult()) + 1;

	for ($i = 1; $i < $max; $i++) {
		$order[] = JHTML::_('select.option', $i);
	}
	// build the html select list for ordering
	$query = "SELECT ordering AS value, title AS text" . "\n FROM #__categories" . "\n WHERE section = '$row->section'" . "\n ORDER BY ordering";

	if (!J16PLUS) {
		$lists['ordering'] = JHTML::_('list.specificordering', $row, $uid, $query);
	}

	// build the select list for the image positions
	$active = ($row->image_position ? $row->image_position : 'left');
	$lists['image_position'] = JHTML::_('list.positions', 'image_position', $active, null, 0, 0);
	// Imagelist
	$lists['image'] = HandoutHTML::imageList('image', $row->image);
	// build the html select list for the group access
	$lists['access'] = JHTML::_('list.accesslevel', $row);
	// build the html radio buttons for published
	$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);
	// build the html select list for paraent item
	$options = array();
	$options[] = JHTML::_('select.option', '0', JText::_('COM_HANDOUT_TOP'));
	$lists['parent'] = HandoutHTML::categoryParentList($row->id, "", $options);

	HTML_HandoutCategories::edit($row, $section, $lists, $redirect);
}

function saveCategory()
{
	HANDOUT_token::check() or die('Invalid Token');

	$database = &JFactory::getDBO();
	$task = JRequest::getCmd('task');
	$app = &JFactory::getApplication();

	$row = new HandoutCategory($database);

	if (!$row->bind(HANDOUT_Utils::stripslashes($_POST))) {
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if (!$row->check()) {
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if (J16PLUS) {
		$row->extension = $row->section;

		unset($row->image);
		unset($row->image_position);
		unset($row->name);
		unset($row->ordering);
		unset($row->section);
	}

	if (!$row->store()) {
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->checkin();

	if (!J16PLUS) {
		$row->reorder("section='com_handout' AND parent_id=" . (int) $row->parent_id);
	}

	/* http://forum.joomlatools.org/viewtopic.php?f=14&t=316
	$oldtitle =  strip_tags( JRequest::getVar( 'oldtitle', null) );
	if ($oldtitle) {
	    if ($oldtitle != $row->title) {
	        $database->setQuery("UPDATE #__categories " . "\n SET name='$row->title' " . "\n WHERE name='$oldtitle' " . "\n	AND section='com_handout'");
	        $database->query();
	    }
	}
	 */

	if ($task == 'save') {
		$url = 'index.php?option=com_handout&section=categories';
	}
	else { // $task = 'apply'
		$url = 'index.php?option=com_handout&section=categories&task=edit&cid[0]=' . $row->id;
	}

	$app->redirect($url, JText::_('COM_HANDOUT_SAVED_CHANGES'));

}

/**
 * Deletes one or more categories from the categories table
 *
 * @param string $ The name of the category section
 * @param array $ An array of unique category id numbers
 */
function removeCategories($section, $cid)
{
	HANDOUT_token::check() or die('Invalid Token');

	$database = &JFactory::getDBO();

	$app = &JFactory::getApplication();

	if (count($cid) < 1) {
		echo "<script> alert('" . JText::_('COM_HANDOUT_SELECTCATTODELETE') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);
	// Check to see if the category holds child documents and/or subcategories
	$query = "SELECT c.id, c.title, c.parent_id, COUNT(s.catid) AS numcat, COUNT(u.id) as numkids" . "\n FROM #__categories AS c"
			. "\n LEFT JOIN #__handout	 AS s ON s.catid=c.id" . "\n LEFT JOIN #__categories AS u ON u.parent_id =c.id" . "\n WHERE c.id IN ($cids)"
			. "\n GROUP BY c.id";
	$database->setQuery($query);

	if (!($rows = $database->loadObjectList())) {
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
	}

	$err = array();
	$cid = array();

	foreach ($rows as $row) {
		if ($row->numcat == 0 && $row->numkids == 0) {
			$cid[] = $row->id;
		}
		else {
			$err[] = $row->title;
		}
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__categories WHERE id IN ($cids)");
		if (!$database->query()) {
			echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	if (count($err)) {
		if (count($err) > 1) {
			$cids = implode(', ', $err);
			$msg = JText::_('COM_HANDOUT_CATS') . ": $cids -";
		}
		else {
			$msg = JText::_('COM_HANDOUT_CAT_LABEL') . " " . $err[0];
		}
		$msg .= ' ' . JText::_('COM_HANDOUT_CATS_CANT_BE_REMOVED');

		$app->redirect('index.php?option=com_handout&section=categories', $msg);
	}

	$msg = (count($err) > 1 ? JText::_('COM_HANDOUT_CATS') : JText::_('COM_HANDOUT_CAT') . " ") . JText::_('COM_HANDOUT_DELETED');
	$app->redirect('index.php?option=com_handout&section=categories', $msg);
}

/**
 * Publishes or Unpublishes one or more categories
 *
 * @param string $ The name of the category section
 * @param integer $ A unique category id (passed from an edit form)
 * @param array $ An array of unique category id numbers
 * @param integer $ 0 if unpublishing, 1 if publishing
 * @param string $ The name of the current user
 */

function publishCategories($section, $categoryid = null, $cid = null, $publish = 1)
{
	if (!HANDOUT_token::check()) {
		die('Invalid Token');
	}

	$database = &JFactory::getDBO();
	$user = &JFactory::getUser();
	$app = &JFactory::getApplication();

	if (!is_array($cid)) {
		$cid = array();
	}
	if ($categoryid) {
		$cid[] = $categoryid;
	}

	if (count($cid) < 1) {
		$action = $publish ? _PUBLISH : JText::_('COM_HANDOUT_UNPUBLISH');
		echo "<script> alert('" . JText::_('COM_HANDOUT_SELECTCATTO') . " $action'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);

	$query = "UPDATE #__categories SET published=$publish" . "\n WHERE id IN ($cids) AND (checked_out=0 OR (checked_out=$user->id))";
	$database->setQuery($query);
	if (!$database->query()) {
		echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count($cid) == 1) {
		require_once JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'category.php';
		$row = new JTableCategory($database);
		$row->checkin($cid[0]);
	}

	$app->redirect('index.php?option=com_handout&section=categories');
}

/**
 * Cancels an edit operation
 *
 * @param string $ The name of the category section
 * @param integer $ A unique category id
 */
function cancelCategory()
{
	$database = &JFactory::getDBO();
	$app = &JFactory::getApplication();
	$row = new HandoutCategory($database);
	$row->bind(HANDOUT_Utils::stripslashes($_POST));
	$row->checkin();
	$app->redirect('index.php?option=com_handout&section=categories');
}

/**
 * Moves the order of a record
 *
 * @param integer $ The increment to reorder by
 */
function orderCategory($uid, $inc)
{
	$database = &JFactory::getDBO();
	$app = &JFactory::getApplication();
	$row = new HandoutCategory($database);
	$row->load($uid);
	$row->move($inc, "section='$row->section'");
	$app->redirect('index.php?option=com_handout&section=categories');
}

/**
 * changes the access level of a record
 *
 * @param integer $ The increment to reorder by
 */
function accessCategory($uid, $access)
{
	if (!HANDOUT_token::check()) {
		die('Invalid Token');
	}
	$app = &JFactory::getApplication();
	$database = &JFactory::getDBO();

	$row = new HandoutCategory($database);
	$row->load($uid);
	$row->access = $access;

	if (!$row->check()) {
		return $row->getError();
	}
	if (!$row->store()) {
		return $row->getError();
	}

	$app->redirect('index.php?option=com_handout&section=categories');
}
