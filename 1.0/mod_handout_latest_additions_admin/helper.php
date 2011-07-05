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

class modHandoutLatestAdditionsHelper
{
	function getDocs(&$params)
	{
		$database = &JFactory::getDBO();

		$query = "SELECT id, docname,  published, catid, docdate_published"
		     . " FROM #__handout"
		     . " ORDER BY docdate_published DESC";

		$database->setQuery( $query, 0, $params->get('limit', 10) );
		$rows = $database->loadObjectList();

		return $rows;
	}
}
