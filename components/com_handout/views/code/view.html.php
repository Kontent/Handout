<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: view.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.view' );

require_once (JPATH_COMPONENT_HELPERS . DS . 'helper.php');
require_once (JPATH_COMPONENT_HELPERS . DS . 'codes.php');

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
			parent::display();
		}
	}
}