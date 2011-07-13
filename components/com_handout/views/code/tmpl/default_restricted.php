<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */

defined('_JEXEC') or die;

JHTML::stylesheet('handout.css', COM_HANDOUT_CSSPATH);

?>
<div id="handout">
	<?php
		$app = &JFactory::getApplication();
		$app->setPageTitle( JText::_('COM_HANDOUT_CODE_DOC') );
	?>
	<div class="hdoc-code-form">
		<?php $returnUrl = base64_encode(JRoute::_($_SERVER['REQUEST_URI']));?>
		<?php echo JText::sprintf('COM_HANDOUT_CODE_LOGIN_REQUIRED', $returnUrl, $returnUrl); ?>	 
	</div>
	<div class="clr"></div>
	<?php include_once(JPATH_COMPONENT . DS . 'footer.php'); ?>
</div>