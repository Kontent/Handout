<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: default_upload.php
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

JHTML::stylesheet('handout.css', COM_HANDOUT_CSSPATH);
JHTML::_('behavior.tooltip');

?>
<?php $this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'handout' . DS . 'tmpl' );?>
<?php echo $this->loadTemplate('menu'); ?>    

<div id="handout" class="hupload">

	<?php if($this->update) : ?>
		<h2><?php echo JText::_('COM_HANDOUT_TITLE_UPDATE');?></h2>
	<?php else : ?>
		<h2><?php echo JText::_('COM_HANDOUT_TITLE_UPLOAD');?></h2>
	<?php endif; ?>
	
	<?php
		switch($this->step) :
			case '1' :  
				echo '<p class="hupload-steps">'.JText::_('COM_HANDOUT_UPLOAD_STEP')." ".$this->step." ".JText::_('COM_HANDOUT_UPLOAD_OF')." 3".'</p>';
				echo $this->uploadform;
				break;
			
			case '2' :  
				echo '<p class="hupload-steps">'.JText::_('COM_HANDOUT_UPLOAD_STEP')." ".$this->step." ".JText::_('COM_HANDOUT_UPLOAD_OF')." 3 <span>(";
				switch($this->method) :
					case 'http' 	: 	echo JText::_('COM_HANDOUT_UPLOAD_A_FILE'); break;
					case 'transfer' : 	echo JText::_('COM_HANDOUT_REMOTELY_TRANSFER_A_FILE'); break;
					case 'link'     :	echo JText::_('COM_HANDOUT_LINK_TO_FILE'); break;
					default : break;
				endswitch;
				echo ')</span></p>';
				echo $this->uploadform;				
				break;
			
			case '3' :  
				JHTML::_('behavior.calendar');
				?>	
				
				<div class="hdoc-taskbar">
					<ul>
						<li><a title="<?php echo JText::_('COM_HANDOUT_CANCEL')?>" href="javascript:submitbutton('cancel');" ><?php echo JText::_('COM_HANDOUT_CANCEL')?></a></li>
						<li><a title="<?php echo JText::_('COM_HANDOUT_SAVE')?>" href="javascript:submitbutton('save');"><?php echo JText::_('COM_HANDOUT_SAVE')?></a></li>
					</ul>
				</div>
				
				<?php echo $this->uploadform; ?>			
				<ul id="handout_toolbar">
					<li><a title="<?php echo JText::_('COM_HANDOUT_CANCEL')?>" href="javascript:submitbutton('cancel');" ><?php echo JText::_('COM_HANDOUT_CANCEL')?></a></li>
					<li><a title="<?php echo JText::_('COM_HANDOUT_SAVE')?>" href="javascript:submitbutton('save');"><?php echo JText::_('COM_HANDOUT_SAVE')?></a></li>
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
			<?php 
				break;
		endswitch;
	?>
	<?php include_once(JPATH_COMPONENT . DS . 'footer.php'); ?>
</div>