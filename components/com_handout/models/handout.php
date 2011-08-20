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
/* UTF-8 Encoding */
jimport('joomla.application.component.model');

class HandoutModelHandout extends JModel
{
       function  __construct()
       {
       	
       	
       	
           parent::__construct();
       }
      
       
function getCategory($id) {
		$handoutUser = HandoutFactory::getHandoutUser ();

		$cat = new HANDOUT_Category ( $id );

		// if the user is not authorized to access this category, redirect

		if (! $handoutUser->canAccessCategory ( $cat->getDBObject () )) {
			HandoutHelper::_returnTo ( '', JText::_('COM_HANDOUT_NOT_AUTHORIZED') );
		}

		HANDOUT_Utils::processContentPlugins ( $cat );

		$returnArray = array($cat->getLinkObject (), $cat->getPathObject (), $cat->getDataObject ());
		return $returnArray;
	}
	
	
	function getCategoryList($id) {
		$handout = &HandoutFactory::getHandout();

		$children = HANDOUT_Cats::getChildsByUserAccess ( $id );
		$items = array ();
		foreach ( $children as $child ) {
			$cat = new HANDOUT_Category ( $child->id );

			// process content plugins

			HANDOUT_Utils::processContentPlugins ( $cat );

			$item = new StdClass ( );
			$item->links = &$cat->getLinkObject ();
			$item->paths = &$cat->getPathObject ();
			$item->data = &$cat->getDataObject ();
			$items [] = $item;
		}

		return $items;
	}
	
	
function getMenuParams() {
		jimport ( 'joomla.application.menu' );
		$menu = JSite::getMenu();
		$Itemid = JRequest::getInt ( 'Itemid' );
		return $menu->getParams ( $Itemid );
	}

	function getMenu($gid = 0) {
		$handout = &HandoutFactory::getHandout ();
		$handoutUser = &HandoutFactory::getHandoutUser ();
		// create links
		$links = new StdClass ( );
		$links->home = $this->_taskLink ( null );
		$links->search = $this->_taskLink ( 'search_form' );
		$links->upload = $this->_taskLink ( 'upload', $gid );

		// create perms
		$perms = new StdClass ( );
		$perms->view = COM_HANDOUT_AUTHORIZED;
		$perms->search = COM_HANDOUT_AUTHORIZED;
		$perms->upload = COM_HANDOUT_NOT_AUTHORIZED;

		if ($handoutUser->canUpload ()) {
			$perms->upload = COM_HANDOUT_AUTHORIZED;
		} else {
			if ($handoutUser->userid == 0 && $handout->getCfg ( 'user_upload' ) != - 1) {
				$perms->upload = COM_HANDOUT_NOT_LOGGED_IN;
			}
		}

		$returnArray = array($links, $perms);
		return $returnArray;
	}

	function getPathWay($id) {
		if (! $id > 0) {
			return;
		}

		// get the category ancestors
		$ancestors = & HANDOUT_Cats::getAncestors ( $id );

		// add home link
		$home = new StdClass ( );
		$home->name = COM_HANDOUT_DOWNLOADS_HOME;
		$home->title = COM_HANDOUT_DOWNLOADS_HOME;
		$home->link = HANDOUT_Utils::taskLink ( '' );

		$ancestors [] = &$home;
		// reverse the array
		$ancestors = array_reverse ( $ancestors );

		$returnArray = array($ancestors);
		return $returnArray;
	}

	function _taskLink($task, $gid = '', $params = null, $sef = true) {
		return HANDOUT_Utils::taskLink ( $task, $gid, $params, $sef );
	}

	function getPageNav($gid) {
		$handoutUser = &HandoutFactory::getHandoutUser ();
		$handout = &HandoutFactory::getHandout ();

		$limit = JRequest::getInt ( 'limit', $handout->getCfg ( 'perpage' ) );
		$total = HANDOUT_Cats::countDocsInCatByUser ( $gid, $handoutUser );

		if ($total <= $limit) {
			return;
		}

		$Itemid = JRequest::getInt ( 'Itemid' );
		$ordering = JRequest::getVar ( 'ordering', $handout->getCfg ( 'default_order' ) );
		$direction = strtoupper ( JRequest::getVar ( 'dir', $handout->getCfg ( 'default_order2' ) ) );
		$limitstart = JRequest::getInt ( 'limitstart' );

		jimport ( 'joomla.html.pagination' );
		$pageNav = new JPagination ( $total, $limitstart, $limit );

		 // where is $link used?
		//$link = 'index.php?option=com_handout&amp;task=cat_view' . '&amp;gid=' . $gid . '&amp;dir=' . $direction . '&amp;order=' . $ordering . '&amp;Itemid=' . $Itemid;

		$returnArray = array($pageNav);
		return $returnArray;
	}

	function getPageTitle($id) {
		if (! $id > 0) {
			return;
		}
		// get the category ancestors
		$ancestors = & HANDOUT_Cats::getAncestors ( $id );
		// reverse the array
		$ancestors = array_reverse ( $ancestors );

		$returnArray = array($ancestors);
		return $returnArray;
	}

	function getGid() {
		$params = $this->getMenuParams ();
		return JRequest::getInt ( 'gid', $params->get ( 'cat_id', 0 ) );
	}


	function _returnTo($task, $msg = '', $gid = '', $params = null) {
		return HANDOUT_Utils::returnTo ( $task, $msg, $gid, $params );
	}
	
	
}


