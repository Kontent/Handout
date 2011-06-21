<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: default_edit.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined('_JEXEC') or die;

/* Display the edit form page(required)
*
* This template is called when user preforms the Edit operation on a document.
*
* General variables  :
*	$this->data (object) : configuration values
*	$this->buttons (object) : permission values
*	$this->paths (object) : configuration values
*	$this->links (object) : path to links
*	$this->permission (object) : permission values
*	$this->conf (object) : configuration values
*/

?>
<?php 
	$mainframe = &JFactory::getApplication();
    $mainframe->setPageTitle(JText::_('COM_HANDOUT_TITLE_EDIT') . ' | ' . $this->data->docname); 
    $pathway = & $mainframe->getPathWay();
    $pathway->addItem($this->data->docname);
    $pathway->addItem('Edit');
    
    JHTML::stylesheet('handout.css', COM_HANDOUT_CSSPATH);
	JHTML::_('behavior.tooltip');
	JHTML::_('behavior.calendar');
?>
<div id="handout" class="hedit">
	<?php $this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'handout' . DS . 'tmpl' );?>
	<?php echo $this->loadTemplate('menu'); ?>    

	<h2><?php echo JText::_('COM_HANDOUT_TITLE_EDIT');?></h2>
	
	<ul>
		<li><a title="<?php echo JText::_('COM_HANDOUT_CANCEL')?>" href="javascript:submitbutton('cancel');" ><span><span><?php echo JText::_('COM_HANDOUT_CANCEL')?></span></span></a></li>
		<li><a title="<?php echo JText::_('COM_HANDOUT_SAVE')?>" href="javascript:submitbutton('save');"><span><span><?php echo JText::_('COM_HANDOUT_SAVE')?></span></span></a></li>
	</ul>
	
	<?php echo $this->editform ?>
	
	<ul>
		<li><a title="<?php echo JText::_('COM_HANDOUT_CANCEL')?>" href="javascript:submitbutton('cancel');" ><span><span><?php echo JText::_('COM_HANDOUT_CANCEL')?></span></span></a></li>
		<li><a title="<?php echo JText::_('COM_HANDOUT_SAVE')?>" href="javascript:submitbutton('save');"><span><span><?php echo JText::_('COM_HANDOUT_SAVE')?></span></span></a></li>
	</ul>
	
	<div class="clr"></div>
	
	<script language="javascript" type="text/javascript">
	<!--
		list = document.getElementById('docthumbnail');
		img  = document.getElementById('docthumbnail_preview');
		list.onchange = function() {
			var index = list.selectedIndex;
			if(list.options[index].value!='') {
				img.src = 'images/stories/' + list.options[index].value;
			} else {
				img.src = 'images/blank.png';
			}
		}
	//-->
	</script>
	<?php include_once(JPATH_COMPONENT . DS . 'footer.php'); ?>
</div>
