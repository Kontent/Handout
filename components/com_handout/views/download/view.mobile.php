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



class HandoutViewDownload extends JView {
	function display() {
		
			  JHTML::stylesheet('mobile.css', COM_HANDOUT_CSSPATH);
		  JHTML::stylesheet('jquery.mobile-1.0b1.min.css', 'http://code.jquery.com/mobile/1.0b1/');
		  
		$handout = &HandoutFactory::getHandout();
		$db = &JFactory::getDBO ();
		$gid = HandoutHelper::getGid ();
		$doc = new HANDOUT_Document ( $gid );
		$data = &$doc->getDataObject ();

		//check if we need to display an agreement
		if ($handout->getCfg ( 'display_license' ) && ($data->doclicense_display && $data->doclicense_id)) {
			//fetch the license form action
			$action = HandoutHelper::_taskLink('license_result', $gid , array('bid' => $data->id));
			$inline = 0;

			//get the agreement text
			$license = new HandoutLicenses ( $db );
			$license->load ( $data->doclicense_id );

			$this->assignRef('data', $data);
			$this->assignRef('inline', $inline);
			$this->assignRef('action', $action);
			$this->assignRef('license', $license->license);
			$this->assignRef('conf', $handout->getAllCfg());
			parent::display('mobile');
		} else {
			$model=JModel::getInstance('Download','HandoutModel');
			$isdownloaded=$model->download ( $doc, false );
			
			if($isdownloaded==true)
			{
				die();
			}else {
				echo "System error you can't download now, contact with site support";
			//HandoutHelper::_returnTo ( 'cat_view', $isdownloaded, $data->catid );
			}
		}
	}
}