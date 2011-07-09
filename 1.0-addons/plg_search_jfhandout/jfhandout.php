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

defined('_JEXEC') or die('Restricted access');

$handoutBase = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_handout' . DS;

require_once $handoutBase . 'classes' . DS . 'HANDOUT_user.class.php';
require_once $handoutBase . 'classes' . DS . 'HANDOUT_utils.class.php';
require_once $handoutBase . 'helpers' . DS . 'factory.php';
require_once $handoutBase . 'handout.class.php';

$mainframe->registerEvent('onSearch', 'plgSearchJFHandout');
$mainframe->registerEvent('onSearchAreas', 'plgSearchJFHandoutAreas');

function &plgSearchJFHandoutAreas ()
{
    static $areas = array('handout' => 'Documents');
    return $areas;
}

function plgSearchJFHandout ($text, $phrase = '', $ordering = '', $areas = null)
{
    $db = & JFactory::getDBO();
    $user = & JFactory::getUser();

    $searchText = $text;

    if (is_array($areas)) {
        if (! array_intersect($areas, array_keys(plgSearchJFHandoutAreas()))) {
            return array();
        }
    }

    $plugin = & JPluginHelper::getPlugin('search', 'jfhandout');
    $pluginParams = new JParameter($plugin->params);

    $limit = $pluginParams->def('search_limit', 50);
    $activeLang = $pluginParams->def('active_language_only', 0);

    $text = trim($text);
    if ($text == '') {
        return array();
    }

    $wheres = array();
    switch ($phrase) {
        case 'exact':
            $text = $db->Quote('%' . $db->getEscaped($text, true) . '%', false);
            $wheres2 = array();
            $wheres2[] = 'title LIKE ' . $text;
            $wheres2[] = 'text LIKE ' . $text;
            $where = '(' . implode(') OR (', $wheres2) . ')';
            break;

        case 'all':
        case 'any':
        default:
            $words = explode(' ', $text);
            $wheres = array();
            foreach ($words as $word) {
                $word = $db->Quote('%' . $db->getEscaped($word, true) . '%', false);
                $wheres2 = array();
                $wheres2[] = 'title LIKE ' . $word;
                $wheres2[] = 'text LIKE ' . $word;
                $wheres[] = implode(' OR ', $wheres2);
            }
            $where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
            break;
    }

    switch ($ordering) {
        case 'oldest':
            $order = 'created ASC';
            break;

        case 'popular':
            $order = 'hits DESC';
            break;

        case 'alpha':
            $order = 'title ASC';
            break;

        case 'category':
            $order = 'section ASC, title ASC';
            break;

        case 'newest':
        default:
            $order = 'created DESC';
    }

    $registry = & JFactory::getConfig();
    $lang = $registry->getValue("config.jflang");

    $query = "SELECT id FROM #__languages WHERE code = '$lang'";
    $db->setQuery($query);
    $lid = (int) $db->loadResult();

    $join = $activeLang ? 'RIGHT' : 'LEFT';

    $query = 'SELECT * FROM ( SELECT jmd.id, catid, COALESCE(jf1.value,docname) AS title, COALESCE(jf3.value,title) AS section,';
    $query .= ' COALESCE(jf2.value,docdescription) AS text, docdate_published AS created, doccounter AS hits';
    $query .= ' FROM #__handout AS jmd LEFT JOIN #__categories AS cat ON jmd.catid = cat.id';
    $query .= ' ' . $join . " JOIN #__jf_content AS jf1 ON jmd.id = jf1.reference_id AND jf1.reference_field = 'docname'";
    $query .= " AND jf1.reference_table = 'handout' AND jf1.language_id = $lid AND jf1.published = 1";
    $query .= ' ' . $join . " JOIN #__jf_content AS jf2 ON jmd.id = jf2.reference_id AND jf2.reference_field = 'docdescription'";
    $query .= " AND jf2.reference_table = 'handout' AND jf2.language_id = $lid AND jf2.published = 1";
    $query .= ' ' . $join . " JOIN #__jf_content AS jf3 ON cat.id = jf3.reference_id AND jf3.reference_field = 'title'";
    $query .= " AND jf3.reference_table = 'categories' AND jf3.language_id = $lid AND jf3.published = 1";
    $query .= ' WHERE jmd.published = 1 AND approved = 1';
    $query .= ' ) AS s WHERE ' . $where . ' ORDER BY ' . $order;
    $db->setQuery($query, 0, $limit);
    $rows = $db->loadObjectList();
    $handoutuser = HandoutFactory::getHandoutUser();

    foreach ($rows as $i => &$row) {
        $row->browsernav = 2;
        if ($handoutuser->canDownload($row->id) === false) {
            unset($rows[$i]);
        }
        $row->href = HANDOUT_Utils::taskLink('doc_details', $row->id, null, true);
    }

    return $rows;
}
?>