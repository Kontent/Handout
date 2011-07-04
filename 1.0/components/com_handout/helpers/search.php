<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: search.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$GLOBALS ['search_mode'] = JRequest::getString ( 'search_mode', 'any' );
$GLOBALS ['ordering'] = JRequest::getString ( 'ordering', 'newest' );
$GLOBALS ['invert_search'] = isset($_REQUEST['invert_search']) ? 1 : 0;
$GLOBALS ['reverse_order'] = isset($_REQUEST['reverse_order']) ? 1 : 0;
$GLOBALS ['search_where'] = JRequest::getVar('search_where',array(),'default','array',array());
$GLOBALS ['search_phrase'] = JRequest::getString ( 'search_phrase', '' );
$GLOBALS ['search_catid'] = JRequest::getInt ( 'catid', 0 );

class SearchHelper {

	function fetchSearchForm($gid, $itemid) {
		global $search_mode, $ordering, $invert_search, $reverse_order, $search_where, $search_phrase, $search_catid;
		// category select list

		$options = array (JHTML::_ ( 'select.option', '0', JText::_('COM_HANDOUT_ALLCATS'), 'value', 'text' ) );
		$lists ['catid'] = HandoutHTML::categoryList ( $search_catid, "", $options );

		$mode = array ();
		$mode [] = JHTML::_ ( 'select.option', 'any', JText::_('COM_HANDOUT_SEARCH_ANYWORDS'), 'value', 'text' );
		$mode [] = JHTML::_ ( 'select.option', 'all', JText::_('COM_HANDOUT_SEARCH_ALLWORDS'), 'value', 'text' );
		$mode [] = JHTML::_ ( 'select.option', 'exact', JText::_('COM_HANDOUT_SEARCH_PHRASE'), 'value', 'text' );
		$mode [] = JHTML::_ ( 'select.option', 'regex', JText::_('COM_HANDOUT_SEARCH_REGEX'), 'value', 'text' );

		$lists ['search_mode'] = JHTML::_ ( 'select.genericlist', $mode, 'search_mode', 'id="hsearch-mode" class="inputbox"', 'value', 'text', $search_mode, null, false, false );

		$orders = array ();
		$orders [] = JHTML::_ ( 'select.option', 'newest', JText::_('COM_HANDOUT_SEARCH_NEWEST'), 'value', 'text' );
		$orders [] = JHTML::_ ( 'select.option', 'oldest', JText::_('COM_HANDOUT_SEARCH_OLDEST'), 'value', 'text' );
		$orders [] = JHTML::_ ( 'select.option', 'popular', JText::_('COM_HANDOUT_SEARCH_POPULAR'), 'value', 'text' );
		$orders [] = JHTML::_ ( 'select.option', 'alpha', JText::_('COM_HANDOUT_SEARCH_ALPHABETICAL'), 'value', 'text' );
		$orders [] = JHTML::_ ( 'select.option', 'category', JText::_('COM_HANDOUT_SEARCH_CATEGORY'), 'value', 'text' );

		$lists ['ordering'] = JHTML::_ ( 'select.genericlist', $orders, 'ordering', 'id="hsearch-ordering" class="inputbox"', 'value', 'text', $ordering, null, false, false );

		$lists ['invert_search'] = '<input type="checkbox" class="inputbox" id="hsearch-invert" name="invert_search" ' . ($invert_search ? ' checked ' : '') . '/>';
		$lists ['reverse_order'] = '<input type="checkbox" class="inputbox" id="hsearch-reverse" name="reverse_order" '. ($reverse_order ? ' checked ' : '') . '/>';

		$matches = array ();
		if ($search_where && count ( $search_where ) > 0) {
			foreach ( $search_where as $val ) {
				$matches [] = JHTML::_('select.option', $val, $val );
			}
		} else {
			$matches [] = JHTML::_ ( 'select.option', 'search_description', 'search_description', 'value', 'text' );
		}

		$where = array ();
		$where [] = JHTML::_ ( 'select.option', 'search_name', JText::_('COM_HANDOUT_NAME'), 'value', 'text' );
		$where [] = JHTML::_ ( 'select.option', 'search_description', JText::_('COM_HANDOUT_DESCRIPTION'), 'value', 'text' );
		$lists ['search_where'] = JHTML::_ ( 'select.genericlist', $where, 'search_where[]', 'id="hsearch-location" class="inputbox" multiple="multiple" size="2"', 'value', 'text', empty($search_where) ? $where : $search_where, null, false, false );

		$returnArray = array($lists, $search_phrase);
		return $returnArray;
	}

	function getSearchResult($gid, $itemid) {
		global $search_mode, $ordering, $invert_search, $reverse_order, $search_where, $search_phrase, $search_catid;

		$search_mode = ($invert_search ? '-' : '') . $search_mode;
		$searchList = array (array ('search_mode' => $search_mode, 'search_phrase' => $search_phrase ) );
		$ordering = ($reverse_order ? '-' : '') . $ordering;

		$rows = HANDOUT_Docs::search ( $searchList, $ordering, $search_catid, '', $search_where );

		// This acts as the search header - so they can perform search again
		if (count ( $rows ) == 0) {
			$msg = JText::_('COM_HANDOUT_NOKEYWORD');
		} else {
			$msg = sprintf ( JText::_('COM_HANDOUT_SEARCH') . ' ' . JText::_('COM_HANDOUT_SEARCH_MATCHES'), count ( $rows ) );
		}

		$items = array ();
		if (count ( $rows ) > 0) {
			foreach ( $rows as $row ) {
				// onFetchDocument event, type = list
				$bot = new HANDOUT_plugin ( 'onFetchDocument' );
				$bot->setParm ( 'id', $row->id );
				$bot->copyParm ( 'type', 'list' );
				$bot->trigger ();
				if ($bot->getError ()) {
					HandoutHelper::_returnTo ( 'cat_view', $bot->getErrorMsg () );
				}

				// load doc
				$doc = & HANDOUT_Document::getInstance ( $row->id );

				// process content plugins
				HANDOUT_Utils::processContentPlugins ( $doc );

				$item = new StdClass ( );
				$item->buttons = &$doc->getLinkObject ();
				$item->paths = &$doc->getPathObject ();
				$item->data = &$doc->getDataObject ();
				$item->data->category = $row->section;

				$items [] = $item;
			}
		}

		return $items;
	}
}
