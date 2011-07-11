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

if (defined('_HANDOUT_TOOLBAR')) {
	return;
} else {
	define('_HANDOUT_TOOLBAR', 1);
}

class TOOLBAR_handout {
	function NEW_DOCUMENT_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::save();
		HandoutToolBar::apply();
		HandoutToolBar::cancel();
		HandoutToolBar::spacer();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function MOVE_DOCUMENT_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::save('move_process');
		HandoutToolBar::cancel();
		HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function COPY_DOCUMENT_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::save('copy_process');
		HandoutToolBar::cancel();
		HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function DOCUMENTS_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();

		HandoutToolBar::addNew();
		HandoutToolBar::editList();
		HandoutToolBar::copy('copy_form');
		HandoutToolBar::move('move_form');
		HandoutToolBar::deleteList('delete', JText::_('COM_HANDOUT_TOOLBAR_DELETE'));
		HandoutToolBar::divider();
		HandoutToolBar::publishList();
		HandoutToolBar::unpublishList();
		HandoutToolBar::divider();
		//HandoutToolBar::cpanel();
		//HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function UPLOAD_FILE_MENU()
	{
		$step = (int) JRequest::getVar( 'step', '');
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		switch ($step) {
			case '2':
			case '4';
				HandoutToolBar::back( 'back',JText::_('COM_HANDOUT_TOOLBAR_BACK'), 'index.php?option=com_handout&amp;section=files&amp;task=upload');
				HandoutToolBar::divider();
				break;
			default:
				break;
		}
		HandoutToolBar::cancel();
		HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function FILES_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::upload();
		HandoutToolBar::addNewDocument();
		HandoutToolBar::divider();
		//HandoutToolBar::cpanel();
	   // HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function EDIT_CATEGORY_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::save();
		HandoutToolBar::apply();
		HandoutToolBar::cancel();
		HandoutToolBar::spacer();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function CATEGORIES_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();

		HandoutToolBar::addNew('new', JText::_('COM_HANDOUT_ADD'));
		HandoutToolBar::editList();
		HandoutToolBar::deleteList('delete', JText::_('COM_HANDOUT_TOOLBAR_DELETE'));
		HandoutToolBar::divider();
		HandoutToolBar::publishList();
		HandoutToolBar::unpublishList();
		HandoutToolBar::divider();
	   //HandoutToolBar::cpanel();
		//HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function LOGS_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::deleteList('delete', JText::_('COM_HANDOUT_TOOLBAR_DELETE'));
		HandoutToolBar::divider();
		//HandoutToolBar::cpanel();
		//HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function EDIT_GROUPS_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::save('saveg');
		HandoutToolBar::apply();
		HandoutToolBar::cancel();
		HandoutToolBar::spacer();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function GROUPS_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::addNew('new', JText::_('COM_HANDOUT_NEW_GROUP'));
		HandoutToolBar::editList();
		HandoutToolBar::deleteList('delete', JText::_('COM_HANDOUT_TOOLBAR_DELETE'));
		HandoutToolBar::divider();
		//HandoutToolBar::cpanel();
		//HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function EMAIL_GROUPS_MENU(){
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::sendEmail();
		HandoutToolBar::cancel();
		HandoutToolBar::endPanelle();

	}

	function EDIT_LICENSES_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::save();
		HandoutToolBar::apply();
		HandoutToolBar::cancel();
		HandoutToolBar::spacer();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function LICENSES_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::addNew('edit', JText::_('COM_HANDOUT_TOOLBAR_NEW'));
		HandoutToolBar::editList();
		HandoutToolBar::deleteList('delete', JText::_('COM_HANDOUT_TOOLBAR_DELETE'));
		HandoutToolBar::divider();
		//HandoutToolBar::cpanel();
	 	//HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function EDIT_CODES_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::save();
		HandoutToolBar::apply();
		HandoutToolBar::cancel();
		HandoutToolBar::spacer();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function CODES_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::addNew('edit', JText::_('COM_HANDOUT_TOOLBAR_NEW'));
		HandoutToolBar::editList();
		HandoutToolBar::deleteList('delete', JText::_('COM_HANDOUT_TOOLBAR_DELETE'));
		HandoutToolBar::divider();
		HandoutToolBar::publishList();
		HandoutToolBar::unpublishList();
		HandoutToolBar::divider();
		//HandoutToolBar::cpanel();
	 	//HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function STATS_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		//HandoutToolBar::cpanel();
		//HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function CONFIG_MENU()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::save();
		HandoutToolBar::apply();
		HandoutToolBar::cancel();
		HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function CPANEL_MENU()
	{
		HandoutToolBar::startPanelle();
		//HandoutToolBar::cpanel();
		HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function CREDITS_MENU(){
		HandoutToolBar::startPanelle();
		HandoutToolBar::cpanel();
		HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}

	function CLEARDATA_MENU(){
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::clear();
		HandoutToolBar::divider();
		HandoutToolBar::cpanel();
		HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}
	function _DEFAULT()
	{
		HandoutToolBar::startPanelle();
		HandoutToolBar::logo();
		HandoutToolBar::addNew();
		HandoutToolBar::editList();
		HandoutToolBar::deleteList();
		//HandoutToolBar::cpanel();
		//HandoutToolBar::divider();
		HandoutToolBar::help();
		HandoutToolBar::spacer();
		HandoutToolBar::endPanelle();
	}
} // end class


