<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: handout_groups.class.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_GROUPS')) {
    return true;
} else {
    define('_HANDOUT_GROUPS', 1);
}

class HANDOUT_groups {

    /**
     * Provides a list of all groups
     *
     * @deprecated
     */
    function & getList() {
        static $groups;

        if( !isset( $groups )) {
            $database = &JFactory::getDBO();
            $database->setQuery("SELECT groups_id, groups_name "
             . "\n  FROM #__handout_groups "
             . "\n ORDER BY groups_name ASC");
            $groups = $database->loadObjectList();
        }

        return $groups;
    }

    /**
     * Get a group object, caches results
     */
    function & get($id)
    {
        static $groups;

        if( !isset( $groups )) {
            $groups = array();
        }

        if( !isset( $groups[$id] )) {
            $database = &JFactory::getDBO();
            $groups[$id] = new HandoutGroups($database);
            $groups[$id]->load($id);
        }

        return $groups[$id];
    }
}