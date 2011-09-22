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
	<div id="hcat-list">
		<h3><?php echo JText::_('COM_HANDOUT_CATS'); ?></h3>
		<ul>
			<?php
				foreach($this->cat_list->items as $category_item) :
				if($this->conf->cat_empty || $category_item->data->files != 0) :
						$icon_ext = strrchr($category_item->paths->icon, "/");
						$icon_ext = strrchr($icon_ext, "-");
			?>
				<li class="hcat-row">
					<?php
					switch ($this->conf->cat_image) :
						case 0 : //none
							//do nothing
						break;

						case 1 : //icon
							?><div class="hcat-icon"><a href="<?php echo $category_item->links->view;?>"><img src="<?php echo COM_HANDOUT_IMAGESPATH . 'icons/icon-'.$this->conf->doc_icon_size.$icon_ext ?>" alt="<?php echo $category_item->data->title;?>" /></a></div><?php
						break;

						case 2 : //thumb
							if($category_item->data->image) :
							?><div class="hcat-icon"><a href="<?php echo $category_item->links->view;?>"><img src="<?php echo $category_item->paths->thumb;?>" alt="<?php echo $category_item->data->title;?>" /></a></div><?php
							endif;
						break;
					endswitch;
					?>

					<h3><a href="<?php echo $category_item->links->view;?>"><?php echo $category_item->data->title;?></a></h3>
					<span class="hcat-files">Files: <?php echo $category_item->data->files;?></span>

					<?php
					if($category_item->data->description) :
						?><div class="hcat-description"><?php echo $category_item->data->description;?></div><?php
					endif;
					?>
				</li>
			<?php
				endif;
				endforeach;
			?>
		</ul>
	</div>
<?php } ?>
