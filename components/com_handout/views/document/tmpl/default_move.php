<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: default_move.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined('_JEXEC') or die;

/* Display the move form page(required)
*
* This template is called when user preforms the Move operation on a document.
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

?>
<?php
	$mainframe = &JFactory::getApplication();
    $mainframe->setPageTitle(JText::_('COM_HANDOUT_TITLE_MOVE') );
    $pathway = & $mainframe->getPathWay();
    $pathway->addItem($this->data->docname);
    $pathway->addItem('Move');

    JHTML::stylesheet('handout.css', COM_HANDOUT_CSSPATH);
	JHTML::_('behavior.tooltip');
?>
<div id="handout" class="hmove">
	<?php $this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'handout' . DS . 'tmpl' );?>
	<?php echo $this->loadTemplate('menu'); ?>

	<h2><?php echo JText::_('COM_HANDOUT_TITLE_MOVE');?></h2>

	<form action="<?php echo $this->action ?>" method="post" id="hmove-form" class="hform" >
		<fieldset>
			<p><?php echo JText::_('COM_HANDOUT_DOCUMENT');?>: <span class="hdoc-docname"><?php echo $this->data->docname;?> (<?php echo $this->data->filename;?>)</span></p>
			<p><label for="hform-catid"><?php echo JText::_('COM_HANDOUT_MOVETO');?></label>: <?php echo $this->lists['categories'];?> <input name="submit" class="button" value="<?php echo JText::_('COM_HANDOUT_MOVE');?>" type="submit" /><a href="javascript: history.go(-1);"><span class="hcancel"><?php echo JText::_('COM_HANDOUT_CANCEL') ?></span></a></p>
	 	</fieldset>
	    <?php echo $this->token; ?>
 	</form>
	<div class="clr"></div>
	<?php include_once(JPATH_COMPONENT . DS . 'footer.php'); ?>
</div>
