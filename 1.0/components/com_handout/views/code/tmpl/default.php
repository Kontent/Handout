<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: default.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

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
	$mainframe = &JFactory::getApplication();
    $mainframe->setPageTitle( JText::_('COM_HANDOUT_CODE_DOC') );
    ?>
	<div class="hdoc-code-form">
	    <div class="hlogo"></div>
		<?php // show form for inputing code and email id?>
		<form action="<?php echo $this->action;?>" method="POST" enctype="multipart/form-data">
        	<div>Enter your download code:</div>
	         <input type="text" name="code" maxlength="100" size="50"  /> 
             <?php if ($this->usertype==2): //email required ?>  
        	<div>Enter a valid email address:</div>
	         <input type="text" name="email" maxlength="100" size="50"  />   
            <?php endif; ?>
            <input name="submit" value="<?php echo JText::_('COM_HANDOUT_BUTTON_DOWNLOAD');?>" type="submit" />
		</form>
		
	</div>
       
    <div class="clr"></div>
    <?php include_once(JPATH_COMPONENT . DS . 'footer.php'); ?>
</div>