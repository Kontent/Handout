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

/* Display the document search page
*
* This template is called when user searches Handout.
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
$app->appendPathway(JText::_('COM_HANDOUT_TITLE_SEARCH'));
$app->setPageTitle( JText::_('COM_HANDOUT_TITLE_SEARCH'));
?>
<div id="handout">
	<?php $this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'handout' . DS . 'tmpl' );?>
	<?php echo $this->loadTemplate('menu'); ?>

	<h2><?php echo JText::_('COM_HANDOUT_TITLE_SEARCH') ?></h2>

	<div class="hsearch">
		<form action="<?php echo $this->action;?>" method="post" id="hsearch" >
			<fieldset>
				<table>
					<tr>
						<td><label for="hsearch-phrase"><?php echo JText::_('COM_HANDOUT_PROMPT_KEYWORD');?></label>:</td>
						<td class="hsearch-input"><input type="text" class="inputbox" id="hsearch-phrase" value="<?php echo htmlspecialchars(stripslashes($this->search_phrase), ENT_QUOTES); ?>" name="search_phrase" /></td>
						<td></td>
					</tr>
					<tr>
						<td><label for="hsearch-catid"><?php echo JText::_('COM_HANDOUT_SELECCAT');?></label>:</td>
						<td class="hsearch-input"><?php echo $this->lists['catid'] ;?></td>
						<td></td>
					</tr>
					<tr>
						<td><label for="hsearch-ordering"><?php echo JText::_('COM_HANDOUT_CMN_ORDERING');?></label>:</td>
						<td class="hsearch-input"><?php echo $this->lists['ordering'] ;?></td>
						<td><label for="hsearch-reverse"><?php echo $this->lists['reverse_order'] . JText::_('COM_HANDOUT_SEARCH_REVRS');?></label></td>
					</tr>
					<tr>
						<td><label for="hsearch-mode"><?php echo JText::_('COM_HANDOUT_SEARCH_MODE');?></label>:</td>
						<td class="hsearch-input"><?php echo $this->lists['search_mode']?></td>
						<td><label for="hsearch-invert"><?php echo $this->lists['invert_search'] . JText::_('COM_HANDOUT_NOT') ;?></label></td>
					</tr>
					<tr>
						<td><label for="hsearch-location"><?php echo JText::_('COM_HANDOUT_SEARCH_WHERE');?></label>:</td>
						<td class="hsearch-input"><?php echo $this->lists['search_where'] ;?></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td class="hsearch-input"><input type="submit" class="button" value="<?php echo JText::_('COM_HANDOUT_SEARCH');?>" /></td>
							<td></td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>

	<?php
	// If we have no items to show
	if (count($this->items) == 0) :
		// show a message if a search term was entered
		if( JRequest::getString( 'search_phrase') ) {
			$app = JFactory::getApplication();
			$app->enqueueMessage( JText::_('COM_HANDOUT_NO_ITEMS_FOUND') );
		}
		return;
	endif;
	?>
	<ul id="hsearch-results">
		<?php
		/*
			 * Include the list_item template and pass the item to it
			*/
		$category = '';
		foreach($this->items as $item) :
			if ($category != $item->data->category) :
				$category = $item->data->category ;
				?><li><h3><?php echo JText::_('COM_HANDOUT_CAT') .': '. $item->data->category ?></h3></li><?php
			endif;
			$this->doc = &$item; //add item to template variables
			echo $this->loadTemplate('document'); //from handout view - path to search is already set on top
		endforeach;
		?>
	</ul>
	<?php include_once(JPATH_COMPONENT . DS . 'footer.php'); ?>
</div>
