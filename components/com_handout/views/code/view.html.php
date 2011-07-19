<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */

defined('_JEXEC') or die;

jimport ( 'joomla.application.component.view' );

require_once JPATH_COMPONENT_HELPERS . DS . 'helper.php';
require_once JPATH_COMPONENT_HELPERS . DS . 'codes.php';

class HandoutViewCode extends JView {
	function display() {
		$handout = &HandoutFactory::getHandout();
		$db = &JFactory::getDBO ();

		$params = &JComponentHelper::getParams( 'com_handout' );

		$gid = HandoutHelper::getGid ();
		$doc = new HANDOUT_Document ( $gid );
		$data = &$doc->getDataObject ();

		$usertype = JRequest::getVar('usertype', 0);
		$code = JRequest::getVar('code', '');
		
		$handoutUser = &HandoutFactory::getHandoutUser ();
		if ($usertype==1 && $handoutUser->userid == 0) {
			//needs to be registered or logged on 
			$tmpl = 'restricted';				
		}
		else {
			$tmpl = null;
		}

		if ($code) {
			//process the code
			CodesHelper::processCode($code, $usertype);
		}
		else {
			$code = CodesHelper::getCode($data->id);
			$this->assignRef('data', $data);
			$this->assignRef('code', $code);
			$this->assignRef('conf', $handout->getAllCfg());
			$this->assignRef('usertype', $usertype);
			parent::display($tmpl);
		}
	}
}
