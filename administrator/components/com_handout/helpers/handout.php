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

/**
 * Handout display helper.
 *
 * @package     Handout
 * @subpackage  com_handout
 * @since       1.0
 */
class HandoutHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_HANDOUT_TOOLBAR_HOME'),
			'index.php?option=com_handout&section=handout',
			$vName == 'cpanel'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_HANDOUT_DOCS'),
			'index.php?option=com_handout&section=documents',
			$vName == 'documents'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_HANDOUT_FILES'),
			'index.php?option=com_handout&section=files',
			$vName == 'files'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_HANDOUT_CATS'),
			'index.php?option=com_handout&section=categories',
			$vName == 'categories'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_HANDOUT_GROUPS'),
			'index.php?option=com_handout&section=groups',
			$vName == 'groups'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_HANDOUT_SUBMENU_LICENSES'),
			'index.php?option=com_handout&section=licenses',
			$vName == 'licenses'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_HANDOUT_CODES'),
			'index.php?option=com_handout&section=codes',
			$vName == 'codes'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_HANDOUT_STATS'),
			'index.php?option=com_handout&task=stats',
			$vName == 'stats'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_HANDOUT_LOGS'),
			'index.php?option=com_handout&section=logs',
			$vName == 'logs'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_HANDOUT_CONFIG'),
			'index.php?option=com_handout&section=config',
			$vName == 'config'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  JObject
	 * @since   1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, 'com_handout'));
		}

		return $result;
	}
}