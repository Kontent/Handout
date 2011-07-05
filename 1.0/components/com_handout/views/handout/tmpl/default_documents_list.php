<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: default_documents_list.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );


/*
* Display the documents list
*
* Template variables :
*	$this->doc_list->items (array)  : holds an array of dcoument items
*	$this->doc_list->order (object) : holds the document list order information
*/

?>

<?php if(count($this->doc_list->items)) { ?>
    <div id="hdoc-list">

		<ul class="hdoc-order">
			<li class="horder-title"><?php echo JText::_('COM_HANDOUT_ORDER_BY'); ?>:</li>
			<?php
				if($this->doc_list->order->orderby != 'name') :
					?><li><a href="<?php echo $this->doc_list->order->links['name'] ?>"><?php echo JText::_('COM_HANDOUT_ORDER_NAME'); ?></a></li>
				<?php else :
					?><li class="active"><?php echo JText::_('COM_HANDOUT_ORDER_NAME'); ?></li>
				<?php endif;

				if($this->doc_list->order->orderby != 'date') :
					?><li><a href="<?php echo $this->doc_list->order->links['date'] ?>"><?php echo JText::_('COM_HANDOUT_ORDER_DATE'); ?></a></li>
				<?php else :
					?><li class="active"><?php echo JText::_('COM_HANDOUT_ORDER_DATE'); ?></li>
				<?php endif;

				if($this->doc_list->order->orderby != 'hits') :
					?><li><a href="<?php echo $this->doc_list->order->links['hits'] ?>"><?php echo JText::_('COM_HANDOUT_ORDER_DOWNLOADS'); ?></a></li>
				<?php else :
					?><li class="active"><?php echo JText::_('COM_HANDOUT_ORDER_DOWNLOADS'); ?></li>
				<?php endif;

				if ($this->doc_list->order->direction == 'ASC') :
					?><li><a href="<?php echo $this->doc_list->order->links['dir'] ?>">[<?php echo JText::_('COM_HANDOUT_ORDER_DESCENT'); ?>]</a></li><?php
				else :
					 ?><li><a href="<?php echo $this->doc_list->order->links['dir'] ?>">[<?php echo JText::_('COM_HANDOUT_ORDER_ASCENT'); ?>]</a></li><?php
				endif;
			?>
		</ul>
		<h3><?php echo JText::_('COM_HANDOUT_DOCS'); ?></h3>

		<ul>
		<?php
			foreach($this->doc_list->items as $item) :
				$this->doc = &$item; //add item to template variables
				echo $this->loadTemplate('document');
			endforeach;
		?>
		</ul>
    </div>
<?php } else { ?>
	<!--  Add conditional to show/hide the below notice based on config setting  -->
    <div id="hdoc-list"><?php echo JText::_('COM_HANDOUT_NO_DOCS'); ?></div>
<?php } ?>