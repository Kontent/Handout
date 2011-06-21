<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: view.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 

defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.view' );

require_once (JPATH_COMPONENT_HELPERS . DS . 'helper.php');
require_once (JPATH_COMPONENT_HELPERS . DS . 'search.php');

class HandoutViewSearch extends JView {
	function display($tpl = null) {
		$handout = &HandoutFactory::getHandout();
		$gid = HandoutHelper::getGid ();
		$Itemid = JRequest::getInt ( 'Itemid' );
		$action = HandoutHelper::_taskLink('search_result');

		list($links, $perms) = HandoutHelper::fetchMenu ( 0 );
		list($lists, $search_phrase) = SearchHelper::fetchSearchForm ( $gid, $Itemid );

		$task = JRequest::getCmd ( 'task' );
		switch ($task) {
			case 'search_form' :
				$items = array ();
				break;
			case 'search_result' :
				$items = SearchHelper::getSearchResult ( $gid, $Itemid );
				break;
		}

		$this->assignRef('lists', $lists);
		$this->assignRef('search_phrase', $search_phrase);
		$this->assignRef('action', $action);
		$this->assignRef('items', $items);
		$this->assignRef('links', $links);
		$this->assignRef('perms', $perms);
		$this->assignRef('conf', $handout->getAllCfg());
		parent::display();													
	}
}

?>