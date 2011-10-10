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


/*
* Display the documents list
*
* Template variables :
*	$this->doc_list->items (array)  : holds an array of dcoument items
*	$this->doc_list->order (object) : holds the document list order information
*/

?>

<?php if(count($this->doc_list->items)) { ?>




	<ul class="docs ui-btn-corner-all ui-shadow" data-role="listview">
		<?php
			foreach($this->doc_list->items as $item) :
				$this->doc = &$item; //add item to template variables
				echo  '<li><a href="'.JURI::root().'index.php?option=com_handout&task=doc_details&gid='.$this->doc->data->id.'&tmpl=component">'.$this->doc->data->docname.'</a></li>';
			endforeach;
		?>
		</ul>

<?php } else { ?>
	<!--  Add conditional to show/hide the below notice based on config setting  -->
	<?php if($this->cat_empty): ?>
	<div id="hdoc-list"><?php echo JText::_('COM_HANDOUT_NO_DOCS'); ?></div>
	<?php endif;?>
	
<?php } ?>