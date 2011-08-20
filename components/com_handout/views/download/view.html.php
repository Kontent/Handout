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

//require_once JPATH_COMPONENT_HELPERS . DS . 'helper.php';
//require_once JPATH_COMPONENT_HELPERS . DS . 'downloads.php';

class HandoutViewDownload extends JView {
	function display() {
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
			parent::display();
		} else {
			$model= & $this->getModel();
			$model->download ( $doc, false );
		}
	}
}