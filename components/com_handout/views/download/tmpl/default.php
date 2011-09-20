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

/* Display the document download page
*
* This template is called when user views/downloads a document.
*
* General variables  :
*	$this->data (object) : document data
*	$this->conf (object) : configuration values
*
* Template variables  :
*	$this->licence (object) : licence text
*	$this->action (text) : form action
*	$this->inline (boolean) : inline / attachment
*
*/

JHTML::stylesheet('handout.css', COM_HANDOUT_CSSPATH);

?>
<div id="handout">
	<?php
	$app = &JFactory::getApplication();
	$pathway = & $app->getPathWay();
	$pathway->addItem($this->data->docname);
	$app->setPageTitle( JText::_('COM_HANDOUT_AGREEMENT_DOC') . ' | ' . $this->data->docname );
	?>

	<div class="hdoc-license">
		<?php echo $this->license; ?>
	</div>

	<div class="hdoc-license-form">
		<form action="<?php echo $this->action;?>" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="inline" value="<?php echo $this->inline?>" />
			<input type="radio" name="agree" value="0" checked /><?php echo JText::_('COM_HANDOUT_DONT_AGREE');?>
			<input type="radio" name="agree" value="1" /><?php echo JText::_('COM_HANDOUT_AGREE');?>
			<input name="submit" value="<?php echo JText::_('COM_HANDOUT_PROCEED');?>" type="submit" />
		</form>
	</div>

	<div class="hdoc-taskbar">
		<ul>
			<li><a href="javascript: history.go(-1);"><span><span><?php echo JText::_('COM_HANDOUT_BACK') ?></span></span></a></li>
		</ul>
	</div>

	<div class="clr"></div>
	<?php include_once(JPATH_COMPONENT . DS . 'footer.php'); ?>
</div>