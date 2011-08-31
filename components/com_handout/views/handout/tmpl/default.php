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

/* Display the document details page(required)
*
* This template is called when user browses Handout.
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

if ($this->conf->item_tooltip) :
	JHTML::_('behavior.tooltip');
endif;

$app = &JFactory::getApplication();
$pathway = & $app->getPathWay();
$pathway->addItem($this->pagetitle[0][0]->name);
$app->setPageTitle( JText::_('COM_HANDOUT_TITLE_BROWSE') . ' | ' . $this->pagetitle[0][0]->name );


?>

<div id="handout">
	<!-- menu -->
	<?php echo $this->loadTemplate('menu'); ?>

	<!-- category details -->
	<?php echo $this->loadTemplate('category'); ?>

	<!-- subcategories -->
	<?php echo $this->loadTemplate('categories_list'); ?>

	<!-- documents -->
	<?php echo $this->loadTemplate('documents_list'); ?>

	<?php if ($this->pagenav):  ?>
	<!-- pagination for documents  -->
	<div id="hnav">
		<?php echo $this->pagenav->getPagesLinks();?>
		<div>
			<?php echo $this->pagenav->getPagesCounter();?>
		</div>
	</div>
	<?php endif; ?>

	<?php include_once(JPATH_COMPONENT . DS . 'footer.php'); ?>
</div>