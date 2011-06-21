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

class modHandoutLatestDownloadsHelper
{
	function getDocs(&$params)
	{
		$database = &JFactory::getDBO();
				
		$query = "SELECT l.log_docid, l.log_ip, l.log_datetime, l.log_user, d.docname, u.name"
		        ." FROM (#__handout_log AS l LEFT JOIN #__handout AS d ON l.log_docid = d.id)"
		        ." LEFT JOIN #__users AS u ON l.log_user = u.id"
		        ." ORDER BY l.log_datetime DESC";

		$database->setQuery( $query, 0, $params->get('limit', 10) );
		$rows = $database->loadObjectList();
	
		return $rows;
	}
}
