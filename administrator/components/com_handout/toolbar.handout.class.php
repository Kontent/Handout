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
* MenuBar class
* @package HANDOUT_1.0
* */
class HandoutToolBar {

    function logo(){
        //JToolBarHelper::title('Handout');
    }

    /**
    * Writes the start of the button bar table
    */
    function startPanelle() {
    }

    /**
    * Writes a spacer cell
    * @param string The width for the cell
    */
    function spacer( $width='' ) {
        JToolBarHelper::spacer($width);
    }

    /**
    * Write a divider between menu buttons
    */
    function divider() {
        JToolBarHelper::divider();
    }

    /**
    * Writes the end of the menu bar table
    */
    function endPanelle() {
    }

    /**
    * Writes a common icon button
    * @param string The task
    * @param string The alt text
    * @param string The icon name
    */
    function icon( $task, $alt, $icon) {
        $bar = & JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Standard', $icon, $alt, $task, false, false );
    }

    function save($task='save', $alt='', $icon='save') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_SAVE');
        HandoutToolBar::icon($task, $alt, $icon);
    }

    function apply($task='apply', $alt='', $icon='apply') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_APPLY');
        HandoutToolBar::icon($task, $alt, $icon);
    }

    function cancel($task='cancel', $alt='', $icon='cancel' ) {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_CANCEL');
        HandoutToolBar::icon($task, $alt, $icon);
    }

    function addNew($task = 'new', $alt = '', $icon = 'newdocument') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_NEW_DOC');
        HandoutToolBar::icon($task, $alt, $icon);
    }

    function addNewDocument($task = 'new', $alt = '', $icon = 'newdocument') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_NEW_DOC');
        HandoutToolBar::icon($task, $alt, $icon);
    }

    function cpanel() {
        HandoutToolBar::icon('cpanel', JText::_('COM_HANDOUT_TOOLBAR_HOME'), 'cpanel');
    }

    function upload($task = 'upload', $alt = '', $icon = 'upload') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_UPLOAD_FILE');
        HandoutToolBar::icon($task, $alt, $icon);
    }

    function move($task = 'move', $alt = '', $icon='move') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_MOVE');
        HandoutToolBar::icon($task, $alt, $icon);
    }

    function copy($task = 'copy', $alt = '', $icon='copy') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_COPY');
        HandoutToolBar::icon($task, $alt, $icon);
    }

    function sendEmail(){
        HandoutToolBar::icon('sendemail', JText::_('COM_HANDOUT_TOOLBAR_SEND'), 'sendemail');

    }

    /**
    * Writes a cancel button that will go back to the previous page without doing
    * any other operation
    */
    function back($task = 'back', $alt = '', $href="javascript:window.history.back();", $icon='back') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_BACK');
        $bar = & JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Link', $icon, $alt, $href );
    }

    /**
    * Writes a common icon button for a list of records
    * @param string The task
    * @param string The alt text
    * @param string The icon name
    */
    function iconList( $task, $alt, $icon='edit') {
        $bar = & JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Standard', $icon, $alt, $task, true, false );
    }

    function iconListConfirm( $task, $alt, $icon='edit' ) {
        $bar = & JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Confirm', JText::_('COM_HANDOUT_ARE_YOU_SURE'), $icon, $alt, $task, true, false );
    }

    function publishList($task='publish', $alt='', $icon='publish') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_PUBLISH');
        $bar = & JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Standard', $icon, $alt, $task, true, false );
    }

    function unpublishList($task='unpublish', $alt='', $icon='unpublish') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_UNPUBLISH');
        $bar = & JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Standard', $icon, $alt, $task, true, false );
    }

    function deleteList($task='remove', $alt='', $icon='delete') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_DELETE_FILE');
        $bar = & JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Confirm', JText::_('COM_HANDOUT_ARE_YOU_SURE'), $icon, $alt, $task, true, false );
    }

    function clear($task='remove', $alt='') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_CLEAR');
        HandoutToolBar::deleteList($task, $alt, 'cleardata');
    }

    function editList($task='edit', $alt='', $icon='edit') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_EDIT');
        $bar = & JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Standard', $icon, $alt, $task, true, false );
    }

    function editCss( $task='edit_css', $alt='', $icon='editcss') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_EDIT_CSS');
        $bar = & JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Standard', $icon, $alt, $task, true, false );
    }

    function config( $task='config', $alt='', $icon='config') {
		if ($alt=='') $alt = JText::_('COM_HANDOUT_TOOLBAR_OPTIONS');
        $bar = & JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Standard', $icon, $alt, $task, true, false );
    }

    function help()
    {
        $bar = & JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Popup', 'help', 'Help', COM_HANDOUT_HELP_URL);
    }

}