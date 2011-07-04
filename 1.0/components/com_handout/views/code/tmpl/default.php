<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: default.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined('_JEXEC') or die;

JHTML::stylesheet('handout.css', COM_HANDOUT_CSSPATH);

?>
<div id="handout">
	<?php
		$mainframe = &JFactory::getApplication();
    	$mainframe->setPageTitle( JText::_('COM_HANDOUT_CODE_DOC') );
    ?>
	<div class="hdoc-code-form">
		<?php // show form for inputing code and email id?>
		<form action="<?php echo $this->action;?>" method="POST" enctype="multipart/form-data">
        	<p class="hdoc-entercode"><?php echo JText::_('COM_HANDOUT_ENTER_CODE');?>:</p>
	        <input type="text" name="code" maxlength="100" class="hdoc-inputcode" />
            <?php if ($this->usertype==2): //email required ?>
        		<p class="hdoc-enteremail"><?php echo JText::_('COM_HANDOUT_ENTER_EMAIL');?>:</p>
	         	<input type="text" name="email" maxlength="100" class="hdoc-inputemail" />
            <?php endif; ?>
            <span><input name="submit" value="<?php echo JText::_('COM_HANDOUT_BUTTON_DOWNLOAD_FILE');?>" type="submit" class="hdoc-btn"/></span>
		</form>
	</div>
    <div class="clr"></div>
    <?php include_once(JPATH_COMPONENT . DS . 'footer.php'); ?>
</div>