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

class CategoriesHelper {

	function fetchCategory($id) {
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

	function fetchCategoryList($id) {
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
}
?>