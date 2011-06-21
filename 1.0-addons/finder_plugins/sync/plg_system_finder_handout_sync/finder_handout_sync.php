<?php
/**
 * @version		$Id$
 * @package		JXtended.Finder
 * @subpackage	plgSystemFinder_HANDOUT_Sync
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License
 */

defined('JPATH_BASE') or die;

/**
 * System plugin class for Finder to synchronize content with Docman.
 *
 * @package		JXtended.Finder
 * @subpackage	plgSystemFinder_Handout_Sync
 */
class plgSystemFinder_Handout_Sync extends JPlugin
{
	/**
	 * Method to catch the onAfterRoute system event. We catch this event so we
	 * can monitor requests and catch certain actions that do not have triggers
	 * such as trashing and deleting content; archiving content; and things of
	 * that nature.
	 */
	public function onAfterRoute()
	{
		// Get the user object.
		$user = JFactory::getUser();

		// Check if the user is a guest.
		if ($user->get('guest')) {
			return true;
		}

		// Check if this is a site page.
		if (!JFactory::getApplication()->isAdmin()) {
			return true;
		}

		// Get the option and task.
		$option	= strtolower(JRequest::getCmd('option'));
		$task	= strtolower(JRequest::getCmd('task'));

		// Check if we are in com_docman.
		if ($option !== 'com_handout') {
			return true;
		}

		// Check for tasks that we care about.
		switch ($task)
		{
			// Handle save tasks.
			case 'apply':
			case 'save':
			{
				// Get the item id.
				$id = JRequest::getInt('id');

				// Fire the onBeforeSaveHandoutDocument event.
				$this->_fireEvent('onBeforeSaveHandoutDocument', array($id));
				break;
			}

			// Handle published state tasks.
			case 'publish':
			case 'unpublish':
			{
				// Get the ids.
				$cid = JRequest::getVar('cid', array(), 'method', 'array');
				JArrayHelper::toInteger($cid);

				// Get the new value.
				switch ($task) {
					default:
					case 'publish':
						$value = 1;
						break;
					case 'unpublish':
						$value = 0;
						break;
				}

				// Fire the onChangeHandoutDocument event.
				$this->_fireEvent('onChangeHandoutDocument', array($cid, 'published', $value));
				break;
			}

			// Handle remove task.
			case 'remove':
			{
				// Get the ids.
				$cid = JRequest::getVar('cid', array(), 'method', 'array');
				JArrayHelper::toInteger($cid);

				// Fire the onDeleteHandoutDocument event.
				$this->_fireEvent('onDeleteHandoutDocument', array($cid));
				break;
			}
		}

		return true;
	}

	/**
	 * Method to fire the event triggers that we are monitoring and handle any
	 * errors that might have been encountered during execution of the plugins.
	 *
	 * @param	string		The event to fire.
	 * @param	array		The parameters for that event.
	 * @return	boolean		True on success, false on failure.
	 */
	private function _fireEvent($event, $options = array())
	{
		// Get the event dispatcher.
		$dispatcher	= JDispatcher::getInstance();

		// Load the finder plugin group.
		JPluginHelper::importPlugin('finder');

		try {
			// Trigger the event.
			$results = $dispatcher->trigger($event, $options);

			// Check the returned results. This is for plugins that don't throw
			// exceptions when they encounter serious errors.
			if (in_array(false, $results)) {
				throw new Exception($dispatcher->getError(), 500);
			}
		}
		catch (Exception $e) {
			// Handle a caught exception.
			JError::raiseError(500, $e->getMessage());
			return false;
		}

		return true;
	}
}