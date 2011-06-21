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
 
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_BASE . DS . 'components' . DS . 'com_handout' . DS . 'handout.class.php');

class JElementHandoutHelper
{
    function fetchElement ($name, $value, $control_name, $row, $section, $title)
    {
        JHTML::_('behavior.modal', 'a.modal');
        
        $fieldName = $control_name . '[' . $name . ']';
        
        $doc = & JFactory::getDocument();
        $doc->addScript(JURI::root() . 'administrator/components/com_handout/includes/js/handout.js');
        
        $link = 'index.php?option=com_handout&section=' . $section . '&task=element&tmpl=component&object=' . $name;
        
        $html = "\n" . '<div style="float: left;"><input style="background: #ffffff;" type="text" id="' . $name . '_name" value="' . htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8') . '" disabled="disabled" /></div>';
        $html .= '<div class="button2-left"><div class="blank"><a class="modal" title="' . JText::_('COM_HANDOUT_TOOLBAR_SELECT_AN' . $title) . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 600}}">' . JText::_('COM_HANDOUT_TOOLBAR_SELECT') . '</a></div></div>' . "\n";
        $html .= '<div class="button2-left"><div class="blank"><a title="' . JText::_('COM_HANDOUT_TOOLBAR_CLEAR') . '" href="#" onclick="MM_resetElement(\''.$name.'\')">' . JText::_('COM_HANDOUT_TOOLBAR_CLEAR') . '</a></div></div>' . "\n";
        $html .= "\n" . '<input type="hidden" id="' . $name . '_id" name="' . $fieldName . '" value="' . (int) $value . '" />';
        
        return $html;
    }
}
?>