<?php
/**
 * @version		$Id$
 * @category	HandoutPopulate
 * @package		HandoutPopulate
 * @copyright	Copyright (C) 2011 Kontent Design. All rights reserved.
 * @copyright	Copyright (C) 2003 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

class PopulateSelects
{
	function tree( &$src_list, $src_id, $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected )
	{
		// establish the hierarchy of the menu
		$children = array();
		
		// first pass - collect children
		foreach ($src_list as $v ) {
			$pt = $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		// second pass - get an indent list of the items
		jimport( 'joomla.html.html.menu' );
		$ilist = JHTML::_('menu.treerecurse', 0, '', array(), $children );

		// assemble menu items to the array
		$this_treename = '';
		foreach ($ilist as $item) 
		{
			if ($this_treename) {
				if ($item->id != $src_id && strpos( $item->treename, $this_treename ) === false) {
					$tgt_list[] = JHTML::_('select.option', $item->id, $item->treename );
				}
			} else {
				if ($item->id != $src_id) {
					$tgt_list[] = JHTML::_('select.option', $item->id, $item->treename );
				} else {
					$this_treename = "$item->treename/";
				}
			}
		}
		// build the html select list
		return JHTML::_('select.genericlist', $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected );
	}
	
	public function licenses($doclicense_id) 
    {
        $database = JFactory::getDBO();

        $options[] =  JHTML::_('select.option', 0, '- No License -' );

        $database->setQuery("SELECT id, name FROM #__HANDOUT_licenses ORDER BY name");
        $licenses = $database->loadObjectList();

        if (is_array( $licenses ) ) {
            foreach ($licenses as $license) {
                $options[] = JHTML::_('select.option', $license->id, $license->name);
            }
        }

        return  JHTML::_('select.genericlist',  $options, 'doclicense_id', '', 'value', 'text', $doclicense_id );

    }
    
    public function owners ($selected, $owner = 'docowner') // $owner = 'docowner' or 'docmaintainedby'
    { 
        $database = JFactory::getDBO();

        // Default options
        $options[] = JHTML::_('select.option', _DM_PERMIT_NOOWNER,       '------ Select User ------');
        $options[] = JHTML::_('select.option', _DM_PERMIT_NOOWNER,       '------ General ------');
        $options[] = JHTML::_('select.option', _DM_PERMIT_CREATOR,       'Creator');
        $options[] = JHTML::_('select.option', _DM_PERMIT_REGISTERED,    'All Registered Users');
        if ($owner == 'docowner' ) {
            $options[] = JHTML::_('select.option', _DM_PERMIT_EVERYBODY, 'Everybody');
        } else {
            $options[] = JHTML::_('select.option', _DM_PERMIT_NOACCESS,  'No User Access');
        }
        $options[] = JHTML::_('select.option', _DM_PERMIT_NOOWNER,       '------ Joomla Groups ------');
        $options[] = JHTML::_('select.option', _DM_PERMIT_AUTHOR,        'Author');
        $options[] = JHTML::_('select.option', _DM_PERMIT_EDITOR,        'Editor');
        $options[] = JHTML::_('select.option', _DM_PERMIT_PUBLISHER,     'Publisher');


        //groups
        $options[] = JHTML::_('select.option', _DM_PERMIT_NOOWNER,       '------ Docman Groups ------');
        $database->setQuery("SELECT groups_id AS id, groups_name AS name FROM #__HANDOUT_groups ORDER BY groups_name");
        $groups = $database->loadObjectList();
        if (is_array( $groups ) ) {
            foreach ($groups as $group) {
                $options[] = JHTML::_('select.option', ((-1 * $group->id) - 10), $group->name);
            }
        }


        //users
        $options[] = JHTML::_('select.option', _DM_PERMIT_NOOWNER,       '------ Users ------');
        $database->setQuery("SELECT id, CONCAT( username, ' ( ', name, ' )') AS name FROM #__users ORDER BY username");
        $users = $database->loadObjectList();
        if (is_array( $users ) ) {
            foreach ($users as $user) {
                $options[] = JHTML::_('select.option', $user->id, $user->name);
            }
        }


        return $html =  JHTML::_('select.genericlist', $options, $owner, '', 'value', 'text', $selected );

    }
}