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

require_once JPATH_BASE . DS . 'components' . DS . 'com_handout' . DS . 'elements' . DS . 'helper.php';

class JElementDocument extends JElement
{
	public $_name = 'Document';

	function fetchElement ($name, $value, &$node, $control_name)
	{
		$db = &JFactory::getDBO();
		$document = new HandoutDocument($db);
		if ($value) {
			$document->load($value);
			$document->title = $document->docname;
		} else {
			$document->title = JText::_('COM_HANDOUT_SELECT_DOCUMENT');
		}
		return JElementHandoutHelper::fetchElement($name, $value, $control_name, $document, 'documents', 'Document');
	}
}
?>