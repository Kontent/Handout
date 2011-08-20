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

jimport ( 'joomla.application.component.view' );

//require_once JPATH_COMPONENT_HELPERS . DS . 'categories.php';
//require_once JPATH_COMPONENT_HELPERS . DS . 'documents.php';
require_once JPATH_COMPONENT_HELPERS . DS . 'upload.php';

class HandoutViewDocument extends JView {
	function display() {
		$handout = &HandoutFactory::getHandout ();
				$document_model=& $this->getModel();
		$gid =  HandoutHelper::getGid ();
		list($buttons, $paths, $data) = $document_model->getDocument ( $gid );
		list($links, $perms) = HandoutHelper::fetchMenu ( $gid );

		//overwrite home link
		$links->home = 'index.php?option=com_handout&task=cat_view';

		$this->assignRef('data', $data);
		$this->assignRef('buttons', $buttons);
		$this->assignRef('paths', $paths); //may not be necessary - only holds thumbs path
		$this->assignRef('links', $links);
		$this->assignRef('perms', $perms);
		$this->assignRef('conf', $handout->getAllCfg());
		parent::display();
	}

	function _displayEdit() {
		$handout = &HandoutFactory::getHandout ();
		
		$document_model=& $this->getModel();
		$gid =  HandoutHelper::getGid ();
		list($buttons, $paths, $data) = $document_model->getDocument ( $gid );
		list($links, $perms) =  HandoutHelper::fetchMenu ( $gid );
		
		list($edit_doc, $edit_lists, $edit_last, $edit_created, $edit_params) = $document_model->getEditDocumentForm ( $gid );

		$this->assignRef('data', $data);
		$this->assignRef('links', $links);
		$this->assignRef('perms', $perms);
		$this->assignRef('edit_doc', $edit_doc);
		$this->assignRef('edit_lists', $edit_lists);
		$this->assignRef('edit_last', $edit_last);
		$this->assignRef('edit_created', $edit_created);
		$this->assignRef('edit_params', $edit_params);
		$this->assignRef('conf', $handout->getAllCfg());

		HandoutViewDocument::importScript ();

		parent::display('edit');
	}

	function _displayMove() {
		$handout = &HandoutFactory::getHandout ();
		$document_model= & $this->getModel();
		$gid =  HandoutHelper::getGid ();
		
		
		$document_model->checkMoveDocument ( $gid );

		list($buttons, $paths, $data) = $document_model->getDocument ( $gid );
		list($links, $perms) =  HandoutHelper::fetchMenu ( $gid );
		$lists = $document_model->getMoveDocumentCategories($gid);
		$action = $document_model->_taskLink('doc_move_process', $data->id);
		$token = HANDOUT_token::render();

		$this->assignRef('data', $data);
		$this->assignRef('links', $links);
		$this->assignRef('perms', $perms);
		$this->assignRef('action', $action);
		$this->assignRef('lists', $lists);
		$this->assignRef('token', $token);

		$this->assignRef('conf', $handout->getAllCfg());

		parent::display('move');
	}

	function _displayUpload($update) {
		//To Do: Test this functionality
		$handout = &HandoutFactory::getHandout ();
		$step = JRequest::getInt ( 'step', 1 );
		$method = JRequest::getVar ( 'method' );

		$document_model=& $this->getModel();
		$gid =  HandoutHelper::getGid ();

		list($links, $perms) =  HandoutHelper::fetchMenu ( $gid );
		

		$lists = UploadHelper::fetchDocumentUploadForm ( $gid, $step, $method, $update );

		$this->assignRef('links', $links);
		$this->assignRef('perms', $perms);
		$this->assignRef('uploadform', $uploadform);
		$this->assignRef('conf', $handout->getAllCfg());
		$this->assignRef ( 'step', $step );
		$this->assignRef ( 'method', $method );
		$this->assignRef ( 'update', $update );
		$this->assignRef ( 'lists', $lists );

		if ($step == 3) {
			HandoutViewDocument::importScript ();
		}

		parent::display('upload');
	}

	function importScript() {
		echo '<script type="text/javascript">';
		echo '
			onunload = WarnUser;
			var folderimages = new Array;

			function submitbutton(pressbutton)
			{
				var form = document.adminForm;
				if (pressbutton == \'cancel\') {
					submitform( pressbutton );
					return;
				}
				form.goodexit.value=1
				try {
					form.onsubmit();
				}
				catch(e){}

				msg = \'\';

				if (form.docname.value == \'\') {
			  		msg += \'\n' . JText::_('COM_HANDOUT_ENTRY_NAME') .'\';
				} if (form.docdate_published.value == \'\') {
				 	msg += \'\n' . JText::_('COM_HANDOUT_ENTRY_DATE') .'\';
				} if (form.docfilename.value == \'\') {
				 	msg += \'\n' . JText::_('COM_HANDOUT_ENTRY_DOC') .'\';
				} if (form.catid.value == \'0\') {
				 	msg += \'\n' . JText::_('COM_HANDOUT_ENTRY_CAT') .'\';
				} if (form.docowner.value == \''.COM_HANDOUT_PERMIT_NOOWNER.'\' || form.docowner.value == \'\' ) {
				 	msg += \'\n' . JText::_('COM_HANDOUT_ENTRY_OWNER') .'\';
				} if (form.docmaintainedby.value == \''.  COM_HANDOUT_PERMIT_NOOWNER .'\' || form.docmaintainedby.value == \'\' ) {
				 	msg += \'\n' . JText::_('COM_HANDOUT_ENTRY_MAINT') .'\';
				} if( form.document_url ){
					if( form.document_url.value != \'\' ){
						if( form.docfilename.value != \''. COM_HANDOUT_DOCUMENT_LINK .'\'){
							if( form.docfilename.value != \'\' ){
								msg += "\n' . JText::_('COM_HANDOUT_ENTRY_DOCLINK') .'";
							}
						}
					}
				}
				if ( msg != \'\' ){
					msghdr = \''. JText::_('COM_HANDOUT_ENTRY_ERRORS') . '\';
					msghdr += \'\n=================================\';
					alert( msghdr + msg + \'\n\' );
				} else { ';
					/* for static content */

						jimport( 'joomla.html.editor' );
						$editor =& JFactory::getEditor();
						echo $editor->save( 'docdescription' );
					echo '
					submitform(pressbutton);
				}
			}

			function setgood() {
				document.adminForm.goodexit.value=1;
			}

			function WarnUser() {

			}
		</script>';
	}
}
?>