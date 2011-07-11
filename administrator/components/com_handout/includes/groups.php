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

include_once dirname(__FILE__) . '/groups.html.php';
JArrayHelper::toInteger(( $cid ));

switch ($task) {
	case "new":
		editGroup($option, 0);
		break;
	case "edit":
		editGroup($option, $cid[0]);
		break;
	case "delete":
	case "remove":
		removeGroup($cid);
		break;
	case "apply":
	case "saveg":
	case "save":
		saveGroup($option);
		break;
	case "cancel":
		cancelGroup($option);
		break;
	case "emailgroup":
		emailGroup($gid);
		break;
	case "sendemail":
		sendEmail($gid);
		break;
	case "show" :
	default :
		showGroups($option);
}

function editGroup($option, $uid)
{
	$database = &JFactory::getDBO();

	// disable the main menu to force user to use buttons
	$_REQUEST['hidemainmenu']=1;

	$row = new HandoutGroups($database);
	$row->load($uid);

	$musers = array();
	$toAddUsers = array();
	// get selected members
	if ($row->groups_members) {
		$database->setQuery("SELECT id,name,username, block "
				. "\n FROM #__users "
				. "\n WHERE id IN (" . $row->groups_members . ")"
				. "\n ORDER BY block ASC, name ASC"
			);
		$usersInGroup = $database->loadObjectList();

		foreach($usersInGroup as $user) {
			$musers[] = JHTML::_('select.option',$user->id,
					$user->id . "-" . $user->name . " (" . $user->username . ")"
					. ($user->block ? ' - ['.JText::_('COM_HANDOUT_USER_BLOCKED').']':'')
					);
		}

	}
	// get non selected members
	$query = "SELECT id,name,username, block FROM #__users ";
	if ($row->groups_members) {
		$query .= "\n WHERE id NOT IN (" . $row->groups_members . ")" ;
	}
	$query .= "\n ORDER BY block ASC, name ASC";
	$database->setQuery($query);
	$usersToAdd = $database->loadObjectList();
	foreach($usersToAdd as $user) {
		$toAddUsers[] = JHTML::_('select.option',$user->id,
						$user->id . "-" . $user->name . " (" . $user->username . ")"
						. ($user->block ? ' - ['.JText::_('COM_HANDOUT_USER_BLOCKED').']':'')
						);
	}

	$usersList = JHTML::_('select.genericlist',$musers, 'users_selected[]',
		'class="inputbox" size="20" onDblClick="moveOptions(document.adminForm[\'users_selected[]\'], document.adminForm.users_not_selected)" multiple="multiple"', 'value', 'text', null);
	$toAddUsersList = JHTML::_('select.genericlist',$toAddUsers,
		'users_not_selected', 'class="inputbox" size="20" onDblClick="moveOptions(document.adminForm.users_not_selected, document.adminForm[\'users_selected[]\'])" multiple="multiple"',
		'value', 'text', null);

	HTML_HandoutGroups::editGroup($option, $row, $usersList, $toAddUsersList);
}

function saveGroup($option)
{
	HANDOUT_token::check() or die('Invalid Token');

	$app = &JFactory::getApplication();

	$database = &JFactory::getDBO(); $task = JRequest::getCmd('task');

	$row = new HandoutGroups($database);

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
	$members = JRequest::getVar( 'users_selected', array());
	$members_imploded = implode(',', $members);

	$database->setQuery("UPDATE #__handout_groups SET groups_members='" . $members_imploded . "' WHERE groups_id=". (int) $row->groups_id);
	$database->query();

	if( $task == 'save' OR $task == 'saveg' ) {
		$url = 'index.php?option=com_handout&section=groups&task=show';
	} else { // $task = 'apply'
		$url = 'index.php?option=com_handout&section=groups&task=edit&cid[0]='.$row->groups_id;
	}

	$app->redirect( $url, JText::_('COM_HANDOUT_SAVED_CHANGES'));
}

function showGroups($option)
{
	$database = &JFactory::getDBO();

	$search = trim(strtolower(JRequest::getVar( 'search', '')));
	$limit = intval(JRequest::getVar( 'limit', 10));
	$limitstart = intval(JRequest::getVar( 'limitstart', 0));
	$where = array();
	if ($search) {
		$where[] = "LOWER(groups_name) LIKE '%$search%'";
	}
	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__handout_groups" . (count($where) ? "\nWHERE " . implode(' AND ', $where) : ""));
	$total = $database->loadResult();

	echo $database->getErrorMsg();

	if ($limit > $total) {
		$limitstart = 0;
	}

	$query = "SELECT *"
			."\n FROM #__handout_groups"
			.(count($where) ? "\n WHERE " . implode(' AND ', $where) : "")
			."\n ORDER BY groups_name";
	$database->setQuery($query, $limitstart,$limit);
	$rows = $database->loadObjectList();

	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	HTML_HandoutGroups::showGroups($option, $rows, $search, $pageNav);
}

function removeGroup($cid)
{
	HANDOUT_token::check() or die('Invalid Token');

	$database = &JFactory::getDBO();
	$app = &JFactory::getApplication();
	if (!is_array($cid) || count($cid) < 1) {
		echo "<script> alert('" . JText::_('COM_HANDOUT_SELECT_ITEM_DEL') . "'); window.history.go(-1);</script>\n";
		exit;
	}
	if (count($cid)) {
		$cids = implode(',', $cid);
		// lets see if some document is owned by this group and not allow to delete it
		for ($g = 0;$g < count($cid);$g++) {
			$ttt = $cid[$g];
			$ttt = ($ttt-2 * $ttt) -10;
			$query = "SELECT id FROM #__handout WHERE docowner=" . $ttt;
			$database->setQuery($query);
			if (!($result = $database->query())) {
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
			if ($database->getNumRows($result) != 0) {
				$app->redirect("index.php?option=com_handout&section=groups", JText::_('COM_HANDOUT_CANNOT_DEL_GROUP'));
			}
		}
		$database->setQuery("DELETE FROM #__handout_groups WHERE groups_id IN ($cids)");
		if (!$database->query()) {
			echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}
	$app->redirect("index.php?option=com_handout&section=groups");
}

function emailGroup($gid)
{
	$database = &JFactory::getDBO();
	$config = &JFactory::getConfig();
	$sitename = $config->getValue('config.sitename');
	$lists = array();

	$database->setQuery("SELECT * FROM #__handout_groups WHERE groups_id=$gid");
	$email_group = $database->loadObjectList();

	$lists['leadin'] = JText::_('COM_HANDOUT_THIS_IS') . " [" . $sitename . "] "
	 . JText::_('COM_HANDOUT_SENT_BY') . " '" . $email_group[0]->groups_name . "'";

	HTML_HandoutGroups::messageForm($email_group, $lists);
}

function cancelGroup($option)
{
	$database = &JFactory::getDBO();
	$app = &JFactory::getApplication();
	$row = new HandoutGroups($database);
	$row->bind(HANDOUT_Utils::stripslashes($_POST));
	$row->checkin();
	$app->redirect("index.php?option=$option&section=groups");
}

function sendEmail($gid)
{
	HANDOUT_token::check() or die('Invalid Token');

	// this is a generic mass mail sender to groups members.
	// From frontend you will find a email to group function specific for a document.
	$database = &JFactory::getDBO();
	$user = &JFactory::getUser();
	$app = &JFactory::getApplication();

	$config = &JFactory::getConfig();

	$mailfrom = $config->getValue('config.mailfrom');
	$fromname = $config->getValue('config.fromname');

	$this_index = 'index.php?option=com_handout&section=groups';

	$message = JRequest::getVar( "mm_message", '');
	$subject = JRequest::getVar( "mm_subject", '');
	$leadin = JRequest::getVar( "mm_leadin", '');

	if (!$message || !$subject) {
		$app->redirect($this_index . '&task=emailgroup&gid=' . $gid , JText::_('COM_HANDOUT_FILL_FORM'));
	}

	$usertmp = trim(strtolower($user->usertype));
	if ($usertmp != "super administrator" && $usertmp != "superadministrator" && $usertmp != "manager") {
		$app->redirect("index.php", JText::_('COM_HANDOUT_ONLY_ADMIN_EMAIL'));
	}
	// Get the 'TO' list of addresses
	$database->setQuery("SELECT * "
		 . "\n FROM #__handout_groups "
		 . "\n WHERE groups_id=" . (int) $gid);

	$email_group = $database->loadObjectList();
	$database->setQuery("SELECT id,name,username,email "
		 . "\n FROM #__users"
		 . "\n WHERE id in ( " . $email_group[0]->groups_members . ")"
		 . "\n   AND email !=''");
	$listofusers = $database->loadObjectList();
	if (! count($listofusers)) {
		$app->redirect($this_index , JText::_('COM_HANDOUT_NO_TARGET_EMAIL') . " " . $email_group[0]->name);
	}
	// Get 'FROM' sending email address (Use default)
	if (! $mailfrom) {
		$database->setQuery("SELECT email "
			 . "\n FROM #__users "
			 . "\n WHERE id=". $user->id);
		$user->email = $database->loadResult();
		echo $database->getErrorMsg();
		$mailfrom = $user->email;
	}
	// Build e-mail message format
	$message =
	($leadin ?
		(stripslashes($leadin) . "\r\n\r\n") :'')
	 . stripslashes($message);
	$subject = stripslashes($subject);

	foreach($listofusers as $emailtosend) {
		JUTility::sendMail($mailfrom, $fromname, $emailtosend->email, $subject, $message );
	}
	$app->redirect($this_index, JText::_('COM_HANDOUT_EMAIL_SENT_TO') . " " . count($listofusers) . " " . JText::_('COM_HANDOUT_USERS'));
}
