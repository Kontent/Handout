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
/*
defined('_JEXEC') or die;




if ($this->conf->item_tooltip) :
	JHTML::_('behavior.tooltip');
endif;

$app = &JFactory::getApplication();
$pathway = & $app->getPathWay();
$pathway->addItem($this->pagetitle[0][0]->name);
$app->setPageTitle( JText::_('COM_HANDOUT_TITLE_BROWSE') . ' | ' . $this->pagetitle[0][0]->name );

*/
?>

<!-- <div id="handout">
	
 subcategories
	<?php //echo $this->loadTemplate('categories_list'); ?>

 documents 
	<?php //echo $this->loadTemplate('documents_list'); ?>

	

	<?php //include_once(JPATH_COMPONENT . DS . 'footer.php'); ?>
	
	
</div>-->
<?php 




?>


<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/mobile/1.0b1/jquery.mobile-1.0b1.min.js"></script>

 <div data-role="page" id="index">
		<div data-role="header" data-theme="b">
		<?php if($this->category->data->title) { ?>
		<a href="javascript: history.go(-1);"  data-icon="arrow-l" >Back</a>
		<?php } ?>
			<h1>Handout Documents</h1>
		</div><!-- /header -->
		<div data-role="content">	
			<div class="main">
				<!-- AddThis Button BEGIN -->
				<?php echo  $this->loadTemplate('addthis'); ?>
				<!-- <h3 class="title">Various Document Categories</h3>
				<p class="title">This is a category header description</p>-->
				<?php if($this->category->data->title) { ?>
				<?php echo $this->loadTemplate('category_mobile'); ?>
				<?php }else { ?>
				<h3 class="title">Categories</h3>
				<p class="title"></p>
				<?php }?>
				<!-- AddThis Button END -->
				<div class="relax">&nbsp;</div>
			
				<?php echo $this->loadTemplate('categories_list_mobile'); ?>
				<div class="relax">&nbsp;</div>
								<?php if(count($this->doc_list->items)) { ?>
				<h3 class="title topped">Documents</h3>
				 <p class="title">Here documents of <?php echo $this->category->data->title; ?></p>
		<?php echo $this->loadTemplate('documents_list_mobile'); ?>
		<?php } ?>
				<!-- <h3 class="title topped">Various Documents</h3>
				<p class="title">This is a category header description</p>-->
			
				
				
		
			</div>
			<ul class="bottom clear">
				<li><a href="http://extensions.kontentdesign.com" rel="external">Handout for Joomla</a></li>
				<li>|</li>
				<li><a href="<?php echo JURI::root().'?option='.JRequest::getVar('option').'&task='.JRequest::getVar('task').'&gid='.JRequest::getVar('gid').'&dv=1'?>" rel="external">Switch to Desctop</a></li>
			</ul>
		</div><!-- /content -->
	</div>