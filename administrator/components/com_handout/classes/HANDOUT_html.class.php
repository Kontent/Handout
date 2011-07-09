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

if (defined('_HANDOUT_HTML_CLASS')) {
    return;
} else {
    define('_HANDOUT_HTML_CLASS', 1);
}

$handout = &HandoutFactory::getHandout();

$pear_path = $handout->getPath('contrib', 'pear');
require_once $pear_path . 'HTML_Select.php';
require_once $handout->getPath('classes', 'user');
require_once $handout->getPath('classes', 'groups');

/**
* Handout HTML Select Class
* Utility class for drawing select lists
* @package HANDOUT_1.0
*/

class HandoutHTML_Select extends HTML_Select
{
	/**
     * Class constructor
     *
     * @param     string    $name       (optional)Name attribute of the SELECT
     * @param     int       $size       (optional) Size attribute of the SELECT
     * @param     mixed     $attributes (optional)Either a typical HTML attribute string
     *                                  or an associative array
     * @param     int       $tabOffset  (optional)Number of tabs to offset HTML source
     * @access    public
     * @return    void
     * @throws
     */

	function HandoutHTML_Select($name = '', $size = 1, $attributes = null, $tabOffset = 0) {
       parent::HTML_Select($name, $size, false, $attributes, $tabOffset);
    }
}

/**
* Handout HTML UserSelect Class
* Utility class for drawing user select lists
* @package HANDOUT_1.0
*/

class HandoutHTML_UserSelect extends HandoutHTML_Select
{
	function HandoutHTML_UserSelect($name = '', $size = 1, $attributes = null, $tabOffset = 0)
    {
       parent::HandoutHTML_Select($name, $size, $attributes, $tabOffset);
    }

    function addLabel($text, $value)
    {
    	 $this->addOption($text, $value, false, 'class="label"');
    }

    function addGeneral($everyone_is = null , $codes = null)
    {
        $this->addLabel(JText::_('COM_HANDOUT_GENERAL'), COM_HANDOUT_PERMIT_NOOWNER);

        if (! $codes || stristr($codes, 'author') != false)
            $this->addOption(JText::_('COM_HANDOUT_CREATOR'), COM_HANDOUT_PERMIT_CREATOR);
        if (! $codes || stristr($codes, 'all') != false)
            $this->addOption( JText::_('COM_HANDOUT_ALL_REGISTERED'), COM_HANDOUT_PERMIT_REGISTERED);
        if ($everyone_is)
            $this->addOption($everyone_is, COM_HANDOUT_PERMIT_EVERYONE);
    }

    function addHandoutGroups()
    {

        $groups = & HANDOUT_groups::getList();

        if (count($groups))
        {
           	$this->addLabel( JText::_('COM_HANDOUT_HANDOUT_GROUPS'), COM_HANDOUT_PERMIT_NOOWNER);

         	foreach($groups as $group) {
                $addID = (-1 * $group->groups_id) + COM_HANDOUT_PERMIT_GROUP ;
                $this->addOption($group->groups_name, $addID);
            }
        }
    }

    function addJoomlaGroups()
    {
        $this->addLabel( JText::_('COM_HANDOUT_JOOMLA_GROUPS'), COM_HANDOUT_PERMIT_NOOWNER);
        $this->addOption('Author'   , COM_HANDOUT_PERMIT_AUTHOR);
        $this->addOption('Editor'   , COM_HANDOUT_PERMIT_EDITOR);
        $this->addOption('Publisher', COM_HANDOUT_PERMIT_PUBLISHER);
    }

  	function addUsers()
    {
        $handout = &HandoutFactory::getHandout();

        // only add users if 'Allow individual user permissions' is set to ON
        if ( !$handout->getCfg('individual_perm', 1))
        {
        	return;
        }

        $this->addLabel(JText::_('COM_HANDOUT_USERS'), COM_HANDOUT_PERMIT_NOOWNER);
        $users = & HANDOUT_users::getList();
        foreach($users as $user) {
            $this->addOption($user->username . "(" . $user->name . ")", $user->id);
        }
    }
}

/**
* Handout HTML Class
* Utility class for all HTML drawing classes
* @desc class General HTML creation class. We use it for back/front ends.
* @package HANDOUT_1.0
*/
class HandoutHTML extends JHTML
{

    // TODO :: merge categoryList and categoryParentList
    // add filter option ?
    function categoryList($id, $action, $options = array())
    {
        $handout = &HandoutFactory::getHandout();
        require_once $handout->getPath('classes', 'utils');
        $list = HANDOUT_utils::categoryArray();
        // assemble menu items to the array

        foreach ($list as $item) {
            $options[] = JHTML::_('select.option',$item->id, $item->treename,'value', 'text');
        }

        $parent = JHTML::_('select.genericlist',$options, 'catid', ' class="inputbox" size="1" onchange="' . $action . '"', 'value', 'text', $id,null,false,false);
        return $parent;
    }

    function categoryParentList($id, $action, $options = array())
    {
        $handout = &HandoutFactory::getHandout();
        require_once $handout->getPath('classes', 'utils');
        $list = HANDOUT_utils::categoryArray();

        // using getInstance for performance
        // $cat = new HandoutCategory($database);
        // $cat->load($id);
        $cat = & HandoutCategory::getInstance( $id );

        $this_treename = '';
        foreach ($list as $item) {
            if ($this_treename) {
                if ($item->id != $cat->id && strpos($item->treename, $this_treename) === false) {
                    $options[] = JHTML::_('select.option',$item->id, $item->treename);
                }
            } else {
                if ($item->id != $cat->id) {
                    $options[] = JHTML::_('select.option',$item->id, $item->treename);
                } else {
                    $this_treename = "$item->treename/";
                }
            }
        }

        $parent = JHTML::_('select.genericlist',$options, 'parent_id', 'class="inputbox" size="1"', 'value', 'text', $cat->parent_id);
        return $parent;
    }

    function imageList($name, &$active, $javascript = null, $directory = null)
    {
        $root_path = JPATH_ROOT;

        if (!$javascript) {
            $javascript = "onchange=\"javascript:if (document.adminForm." . $name . ".options[selectedIndex].value!='') {document.imagelib.src='../images/stories/' + document.adminForm." . $name . ".options[selectedIndex].value} else {document.imagelib.src='../images/blank.png'}\"";
        }
        if (!$directory) {
            $directory = '/images/stories';
        }

        $imageFiles = JFolder::files($root_path . $directory);
        $images = array(JHTML::_('select.option','', JText::_('COM_HANDOUT_SELECTIMAGE')));
        foreach ($imageFiles as $file) {
            if (preg_match("/bmp|gif|jpg|png/", $file)) {
                $images[] = JHTML::_('select.option',$file);
            }
        }
        $images = JHTML::_('select.genericlist',$images, $name, 'id="'.$name.'" class="inputbox" size="1" ' . $javascript, 'value', 'text', $active);

        return $images;
    }

    function viewerList(&$doc, $name, $attributes = null, $tabOffset = 0)
    {
    	$handoutUser = &HandoutFactory::getHandoutUser();

    	$html = '';

    	if($handoutUser->canAssignViewer($doc))
    	{
   	 		//create select list
   			$select = new HandoutHTML_UserSelect($name, 1, $attributes, $tabOffset );
    		$select->addOption(JText::_('COM_HANDOUT_SELECT_USER'), COM_HANDOUT_PERMIT_NOOWNER);
    		$select->addGeneral(JText::_('COM_HANDOUT_EVERYBODY'));
    		$select->addJoomlaGroups();
    		$select->addHandoutGroups();
    		$select->addUsers();
    		$select->setSelectedValues(array($doc->docowner));
    		$html = $select->toHtml();
    	} else {
    		$username = HANDOUT_Utils::getUserName($doc->docowner);
    		$html .= '<input type="text" readonly="readonly" value="'.$username.'"  />';
    		$html .= '<input type="hidden" name="docowner" value="'.$doc->docowner.'" />';
    	}

		return $html;
    }

    function maintainerList(&$doc, $name, $attributes = null, $tabOffset = 0)
    {
    	$handoutUser = &HandoutFactory::getHandoutUser();

    	$html = '';

    	if($handoutUser->canAssignMaintainer($doc))
    	{
    		//create select list
    		$select = new HandoutHTML_UserSelect($name, 1, $attributes, $tabOffset );
    		$select->addOption(JText::_('COM_HANDOUT_SELECT_USER'), COM_HANDOUT_PERMIT_NOOWNER);
    		$select->addGeneral(JText::_('COM_HANDOUT_NO_USER_ACCESS_LABEL'));
    		$select->addJoomlaGroups();
    		$select->addHandoutGroups();
    		$select->addUsers();
    		$select->setSelectedValues(array($doc->docmaintainedby));
    		$html = $select->toHtml();
    	} else {
    		$username = HANDOUT_Utils::getUserName($doc->docmaintainedby);
    		$html .= '<input type="text" readonly="readonly" value="'.$username.'"  />';
    		$html .= '<input type="hidden" name="docmaintainedby" value="'.$doc->docmaintainedby.'" />';
    	}

		return $html;
    }

    /* uploadSelectList
	 * 		Return a select list for what UPLOAD methods are available to
	 *		this user: link, transfer, upload
	 *		Parm: $method - method to select. If blank, we pick first one.
	 */
    function uploadSelectList($method = '')
    {
        $handout = &HandoutFactory::getHandout();
        $handoutUser = &HandoutFactory::getHandoutUser();

        $allow_all = $handoutUser->isSpecial ? true : false;

        if (! $allow_all) {
            $allowed = $handout->getCfg('methods' , array('http'));
        }

        $default_method = null;
        if ($method) {
            $default_method = $method;
        }

        $methods = array();
        if ($allow_all || in_array('http' , $allowed)) {
            $methods[] = JHTML::_('select.option','http', JText::_('COM_HANDOUT_OPTION_UPLOAD_A_FILE'));
            if (! $default_method) {
                $default_method = 'http' ;
            }
        }
        if ($allow_all || in_array('transfer' , $allowed)) {
            $methods[] = JHTML::_('select.option','transfer', JText::_('COM_HANDOUT_OPTION_REMOTE_TRANSFER'));
            if (! $default_method) {
                $default_method = 'transfer' ;
            }
        }
        if ($allow_all || in_array('link' , $allowed)) {
            $methods[] = JHTML::_('select.option','link', JText::_('COM_HANDOUT_OPTION_LINK_TO_A_FILE'));
            if (! $default_method) {
                $default_method = 'link' ;
            }
        }
        return JHTML::_('select.genericlist',$methods,
            'method', 'class="inputbox" size="3"', 'value', 'text', $default_method);
    }

    function docEditFieldsJS($checkList = null)
    {
        $checks = array();
        if ($checkList) {
            $checks = explode("|" , $checkList);
        }

        ?>
			$msg="";
            if (form.docname.value == ""){
                $msg += '\n<?php echo JText::_('COM_HANDOUT_ENTRY_NAME');
        ?>';
            } if (form.docdate_published.value == "") {
                $msg += "\n<?php echo JText::_('COM_HANDOUT_ENTRY_DATE');
        ?>";
            } if (form.docfilename.value == "") {
                $msg += "\n<?php echo JText::_('COM_HANDOUT_ENTRY_DOC');
        ?>" ;
            } if (form.catid.value == "0") {
                $msg +="\n<?php echo JText::_('COM_HANDOUT_ENTRY_CAT');
        ?>" ;
            } if (form.docowner.value == "<?php echo COM_HANDOUT_PERMIT_NOOWNER;
        ?>" ||
                  form.docowner.value == "" ) {
                    $msg +="\n<?php echo JText::_('COM_HANDOUT_ENTRY_OWNER');
        ?>" ;
            } if (form.docmaintainedby.value == "<?php echo COM_HANDOUT_PERMIT_NOOWNER;
        ?>"||
                  form.docmaintainedby.value == "" ) {
                    $msg +="\n<?php echo JText::_('COM_HANDOUT_ENTRY_MAINT');
        ?>" ;
            } if( form.document_url ){
				if( form.document_url.value != "" ){
				if( form.docfilename.value != "<?php echo COM_HANDOUT_DOCUMENT_LINK;
        ?>"){
				  if( form.docfilename.value != "" ){
					$msg += "\n<?php echo JText::_('COM_HANDOUT_ENTRY_DOCLINK');
        ?>";
				  }
				}else{

					var linkname = form.document_url.value.toLowerCase();;
					var cind = linkname.indexOf( "://" );
					if(
						cind < 0 <?php
        if (count($checks) > 0) {
            echo " || \n\t(\n\t\t";

            $useAnd = false;
            foreach($checks as $check) {
                if ($useAnd) {
                    echo " &&\n\t\t";
                }
                $lng = 3 + strlen($check);
                echo "linkname.substr( 0 , $lng ) != \"" . $check . '://"';
                $useAnd = true;
            }
            echo "\n\t)";
        }

        ?>

					){ // Invalid URL (no schema://)
							if( cind >= 0 ){
								linkname = linkname.substr( 0, cind+3 );
							}else{
								linkname = "none";
							}
							$msg += "\n<?php echo JText::_('COM_HANDOUT_ENTRY_DOCLINK_PROTOCOL');
        ?>";
							$msg += " (" + linkname + ")";
				  }else{
					if( cind+3 == linkname.length ){
							$msg += "\n<?php echo JText::_('COM_HANDOUT_ENTRY_DOCLINK_NAME');
        ?>";
							$msg += " (" + linkname + "???)";
					}
				  }
				}
            }
			}

	<?php
    }

    function adminHeading( $title, $icon ) {
        JToolBarHelper::title($title, "$icon");
    }
}

require_once JPATH_ROOT .DS .'libraries'.DS.'joomla'.DS.'html'.DS.'pane.php';

class HandoutTabs extends JPaneTabs {
	function startPanel($text, $id) {
		return parent::startPanel($text, $id);
	}
	function endPanel() {
		return parent::endPanel();
	}
}
