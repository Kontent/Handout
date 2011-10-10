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

/* Display the list of categories
*
* This template is called when user browses Handout.
*
* General variables  :
*	$this->cat_list->items (array) : holds an array of categories
*	$this->cat_list->items->data (object) : holds the category data
*	$this->cat_list->items->paths (object) : configuration values
*	$this->cat_list->items->links (object) : path to links
*	$this->conf (object) : configuration values
*/

JHTML::stylesheet('handout.css', COM_HANDOUT_CSSPATH);

?>
<?php if (count($this->cat_list->items)) { ?>

		<!-- <h3><?php echo JText::_('COM_HANDOUT_CATS'); ?></h3>-->
	<ul class="sample" data-role="listview">
			<?php
				foreach($this->cat_list->items as $category_item) :
				
				if($this->conf->cat_empty || $category_item->data->files != 0) :
						$icon_ext = strrchr($category_item->paths->icon, "/");
						$icon_ext = strrchr($icon_ext, "-");
						

			?>
						<li>
							<a href="<?php echo JURI::root().'index.php?option=com_handout&task=cat_view&gid='.$category_item->data->id.'&tmpl=component';?>"><img src="<?php echo COM_HANDOUT_CSSPATH; ?>../images/icons/folder.gif" alt="pic">

							<h3><?php echo $category_item->data->title;?></h3>
							

							<?php
							if($category_item->data->description) :
								?><p><?php echo $category_item->data->description;?></p><?php
							endif;
							?>
							
							<span  class="ui-li-count"> <?php echo $category_item->data->files;?></span></a>
						</li>
			<?php
				endif;
				endforeach;
			?>
		</ul>
	
<?php } ?>
