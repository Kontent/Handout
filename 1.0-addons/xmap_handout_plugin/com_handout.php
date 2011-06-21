<?php

 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: com_handout.php
 * @package 	Xmap
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The Handout Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/** Adds support for Handout to Xmap */
class xmap_com_handout
{

    function isOfType(&$xmap, &$parent)
    {
        return (strpos($parent->link, 'option=com_handout') !== false);
    }

    /** Get the content tree for this kind of content */
    function &getTree(&$xmap, &$parent, &$params)
    {        
        $database = &JFactory::getDBO();
        
        $tree = array();
        
        $menu = & JSite::getMenu();
        $doc_params = $menu->getParams($parent->id);
        
        $link_query = parse_url($parent->link);
        parse_str(html_entity_decode($link_query['query']), $link_vars);
        $catid = intval(xmap_com_handout::getParam($link_vars, 'gid', 0));
        
        if (! $catid) {
            $catid = intval($doc_params->get('category_id', 0));
        }
        
        $include_documents = xmap_com_handout::getParam($params, 'include_documents', 1);
        $include_documents = ($include_documents == 1 || ($include_documents == 2 && $xmap->view == 'xml') || ($include_documents == 3 && $xmap->view == 'html'));
        $params['include_documents'] = $include_documents;
        
        $priority = xmap_com_handout::getParam($params, 'cat_priority', $parent->priority);
        $changefreq = xmap_com_handout::getParam($params, 'cat_changefreq', $parent->changefreq);
        if ($priority == '-1')
            $priority = $parent->priority;
        if ($changefreq == '-1')
            $changefreq = $parent->changefreq;
        
        $params['cat_priority'] = $priority;
        $params['cat_changefreq'] = $changefreq;
        
        $priority = xmap_com_handout::getParam($params, 'doc_priority', $parent->priority);
        $changefreq = xmap_com_handout::getParam($params, 'doc_changefreq', $parent->changefreq);
        if ($priority == '-1')
            $priority = $parent->priority;
        if ($changefreq == '-1')
            $changefreq = $parent->changefreq;
        
        $params['doc_priority'] = $priority;
        $params['doc_changefreq'] = $changefreq;
        
        $task = xmap_com_handout::getParam($params, 'doc_task', $parent->task);
        if ($task == '-1')
            $task = $parent->task;
        
        $params['doc_task'] = $task;        

        // Handout core interaction API
        include_once (JPATH_SITE . "/administrator/components/com_handout/handout.class.php");
        global $_HANDOUT;
        if (! is_object($_HANDOUT)) {
            $_HANDOUT = new HandoutMainFrame();
        }
        
        $_HANDOUT->setType(COM_HANDOUT_TYPE_MODULE);
        $_HANDOUT->loadLanguage('modules');
        
        require_once($_HANDOUT->getPath('classes', 'utils'));
        require_once($_HANDOUT->getPath('classes', 'file'));
        require_once($_HANDOUT->getPath('classes', 'model'));        

        xmap_com_handout::getCategoryTree($xmap, $parent, $params, $catid);
        return true;
    }

    /** Handout support */
    function &getCategoryTree(&$xmap, &$parent, &$params, $catid = 0)
    {        
        $database = &JFactory::getDBO();

        $list = array();
        $limits = 25;

        $query = 'SELECT `id`, `title`, `name`, `parent_id` FROM `#__categories` WHERE `published` = \'1\' AND `section`=\'com_handout\' AND `access`<=\'0\' AND `parent_id`=' . $catid . ' ORDER BY `parent_id`, `ordering`';
        $database->setQuery($query);
        
        $rows = $database->loadRowList();
        
        $xmap->changeLevel(1);
        // Get sub-categories list
        foreach ($rows as $row) {
            $node = new stdclass();
            $node->id = $parent->id;
            $node->uid = $parent->uid . 'd' . $row[0];
            $node->name = $row[1];
            $node->browserNav = $parent->browserNav;
            $node->priority = $params['cat_priority'];
            $node->changefreq = $params['cat_changefreq'];
            $node->link = 'index.php?option=com_handout&task=cat_view&gid=' . $row[0];
            if ($xmap->printNode($node) !== FALSE) {
                $node->tree = xmap_com_handout::getCategoryTree($xmap, $parent, $params, $row[0]);
            }
        }
        $xmap->changeLevel(- 1);

        if ($catid > 0 && $params['include_documents']) {
            
            $rows = HANDOUT_Docs::getDocsByUserAccess($catid, '', '', $limits);
            $xmap->changeLevel(1);
            // Get documents list
            foreach ($rows as $row) {                
                $node = new stdclass();                
                $node->id = $parent->id;
                $node->uid = $parent->uid . 'd' . $row->id;
                $node->link = 'index.php?option=com_handout&amp;task=' . $params['doc_task'] . '&amp;gid=' . $row->id . '&amp;Itemid=' . $parent->id;
                $node->name = $row->docname;
                $node->browserNav = $parent->browserNav;
                $node->priority = $params['doc_priority'];
                $node->changefreq = $params['doc_changefreq'];
                $node->type = 'separator';
                $xmap->printNode($node);
            }
            $xmap->changeLevel(- 1);
        }        

        return $list;
    }

    function &getParam($arr, $name, $def)
    {
    	$var = JArrayHelper::getValue($arr, $name, $def, '');
        return $var;
    }

}
?>
