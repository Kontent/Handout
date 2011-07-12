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

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_handout'.DS.'handout.class.php';
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_handout'.DS.'classes'.DS.'HANDOUT_utils.class.php';
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_handout'.DS.'helpers'.DS.'factory.php';

$_HANDOUT = &HandoutFactory::getHandout();
$_HANDOUT_USER = &HandoutFactory::getHandoutUser();
if(!is_object($_HANDOUT)) {
	$_HANDOUT = new HandoutMainFrame();
	$_HANDOUT_USER = $_HANDOUT->getUser();
}

class HandoutRouterHelper {
	function getDoc($id) {

		static $docs;

		if(!isset($docs)) {
			$docs = array();
		}

		if(!isset($docs[$id])) {
			$docs[$id] = false;
			$db = & JFactory::getDBO();
			$docs[$id] = new HandoutDocument($db);
			$docs[$id]->load($id);
		}

		return $docs[$id];
	}
}


function HandoutBuildRoute(&$query) {
    jimport('joomla.filter.output');


    $segments = array();

    // check for task=...
    if(!isset($query['task'])) {
        return $segments;
    }

	//replace cat_view with category and doc_details with document
	switch ($query['task']) {
		case 'doc_details' :
			$segments[] = 'document';
			break;
		case 'cat_view' :
			$segments[] = 'category';
			break;
		default:
			$segments[] = $query['task'];
			
	}

    // check for gid=...
    $gid = isset($query['gid']) ? $query['gid'] : 0;

    if(in_array($query['task'], array('cat_view', 'upload')) ) {
        // create the category slugs
        $cats = & HANDOUT_Cats::getCategoryList();
        $cat_slugs = array();
        while($gid AND isset($cats[$gid])) {
        	$cat_slugs[] = $gid.':'.JFilterOutput::stringURLSafe($cats[$gid]->title);
            $gid = $cats[$gid]->parent_id;
        }
        $segments = array_merge($segments, array_reverse($cat_slugs));
    } else {
        // create the document slug
        $doc = HandoutRouterHelper::getDoc($gid);
        if($doc->id) {
            $segments[] = $gid.':'.JFilterOutput::stringURLSafe($doc->docname);
        }
    }

    unset($query['gid']);
    unset($query['task']);

    return $segments;
}

function HandoutParseRoute($segments){
    $vars = array();

    //Get the active menu item
    $menu =& JSite::getMenu();
    $item =& $menu->getActive();

    // Count route segments
    if(!($count = count($segments))) {
        return $vars;
    }

    if( isset($segments[0]) ) {
		switch ( $segments[0]) {
			case 'category':
				$vars['task'] = 'cat_view'; 
				break;
			case 'document':
				$vars['task'] = 'doc_details'; 
				break;
			default:
				$vars['task'] = $segments[0];		
		}

        if(in_array($segments[0], array('category', 'upload'))) {
            $vars['gid'] = (int) $segments[$count-1];
    	} else {
            $vars['gid'] = isset($segments[1]) ? (int) $segments[1] : 0;
        }
    }

    return $vars;
}