<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: helper.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modHandoutdocsHelper
{
	function getDocs(&$params)
	{
		global $_HANDOUT;
		require_once ($_HANDOUT->getPath('classes', 'utils'));

		$limits 	= abs($params->def('limits', ''));
		$cat_ids	= $params->def( 'cat_ids', '' );
		$order_by   = abs($params->def( 'order_by', 0 ));
		$is_mtree_listing = $params->get( 'is_mtree_listing', 0);

		// Sort out the ordering
		switch ( $order_by ) {
			case 0:
				// Most hits
				$order = "hits";
				$dir   = "DESC";
				break;
			case 1:
				// Least hits
				$order = "hits";
				$dir   = "ASC";
				break;
			case 2:
				// Newest
				$order = "date";
				$dir   = "DESC";
				break;
			case 3:
				// Oldest
				$order = "date";
				$dir   = "ASC";
				break;
			case 4:
				// Alphabetically
				$order = "name";
				$dir   = "ASC";
				break;
			case 5:
				// Alphabetically, reverse
				$order = "name";
				$dir   = "DESC";
				break;
		}

		$where = $is_mtree_listing ? "\n AND d.mtree_id=" . (int) $link_id : '';
		return HANDOUT_Docs::getDocsByUserAccess($cat_ids, $order, $dir, $limits, $where);
	}
}
