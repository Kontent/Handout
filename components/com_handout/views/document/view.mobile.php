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
//require_once JPATH_COMPONENT_HELPERS . DS . 'upload.php';

class HandoutViewDocument extends JView {
	function display() {
		
		
		 
		  JHTML::stylesheet('mobile.css', COM_HANDOUT_CSSPATH);
		  JHTML::stylesheet('jquery.mobile-1.0b1.min.css', 'http://code.jquery.com/mobile/1.0b1/');
		  
		$handout = &HandoutFactory::getHandout ();
		$document_model=& JModel::getInstance('Document','HandoutModel');
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
	
		parent::display('mobile');
	}
}

?>