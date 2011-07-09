<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout Search
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	Improved by JoomDOC by Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */

defined('_JEXEC') or die('Restricted access');

$handoutBase = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_handout' . DS;

require_once $handoutBase . 'classes' . DS . 'HANDOUT_user.class.php';
require_once $handoutBase . 'classes' . DS . 'HANDOUT_utils.class.php';
require_once $handoutBase . 'helpers' . DS . 'factory.php';
require_once $handoutBase . 'handout.class.php';

$app = JFactory::getApplication();
$app->registerEvent('onSearch', 'plgSearchHandout');
$app->registerEvent('onSearchAreas', 'plgSearchHandoutAreas');

JPlugin::loadLanguage('plg_search_handout');

function &plgSearchHandoutAreas ()
{
	static $areas = array('handout' => 'Handout Downloads');
	return $areas;
}

function plgSearchHandout ($text, $phrase = '', $ordering = '', $areas = null)
{
	$db = & JFactory::getDBO();
	$user = & JFactory::getUser();

	$searchText = $text;

	if (is_array($areas)) {
		if (! array_intersect($areas, array_keys(plgSearchHandoutAreas()))) {
			return array();
		}
	}

	$plugin = & JPluginHelper::getPlugin('search', 'handout');
	$pluginParams = new JParameter($plugin->params);

	$limit = $pluginParams->def('search_limit', 50);

	$text = trim($text);
	if ($text == '') {
		return array();
	}

	$wheres = array();
	switch ($phrase) {
		case 'exact':
			$text = $db->Quote('%' . $db->getEscaped($text, true) . '%', false);
			$wheres2 = array();
			$wheres2[] = 'docname LIKE ' . $text;
			$wheres2[] = 'docdescription LIKE ' . $text;
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
				$wheres2[] = 'docname LIKE ' . $word;
				$wheres2[] = 'docdescription LIKE ' . $word;
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

	$query = 'SELECT * FROM ( SELECT jmd.id, catid, docname AS title, cat.title AS section,';
	$query .= ' docdescription AS text, docdate_published AS created, doccounter AS hits';
	$query .= ' FROM #__handout AS jmd LEFT JOIN #__categories AS cat ON jmd.catid = cat.id';
	$query .= ' WHERE ' . $where;
	$query .= ' AND jmd.published = 1) AS s ORDER BY ' . $order;

	$db->setQuery($query);
	$rows = $db->loadObjectList();

	$handoutUser = HandoutFactory::getHandoutUser();

	foreach ($rows as $i => &$row) {
		$row->browsernav = 2;
		if ($handoutUser->canDownload($row->id) === false) {
			unset($rows[$i]);
		}
		$row->href = HANDOUT_Utils::taskLink('doc_details', $row->id, null, true);
	}

	return $rows;
}
?>