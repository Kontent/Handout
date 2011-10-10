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
			<h1>Handout Documents</h1>
		</div><!-- /header -->
		<div data-role="content">	
			<div class="main">
				<!-- AddThis Button BEGIN -->
				<div class="mainpage addthis_toolbox addthis_default_style ">
					<a class="addthis_button_facebook"></a>
					<a class="addthis_button_twitter"></a>
					<a class="addthis_button_email"></a>							
					<a class="addthis_bu`tton_compact"></a>
					<a class="addthis_bubble_style"></a>
				</div>
			
				<!--<?php echo $this->loadTemplate('category_mobile'); ?>-->
				<h3 class="title">Categories</h3>
				 <p class="title">This</p>
				
				<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e3a8ee02fc5fbcd"></script>
				<!-- AddThis Button END -->
				<div class="relax">&nbsp;</div>
				<!-- <ul class="sample" data-role="listview">
					<li>
						<a href="#">
							<img src="<?php echo COM_HANDOUT_CSSPATH; ?>../images/icons/folder.gif" alt="pic">
							<h3>Sample Images</h3>
							<p>These are just a few sample images for testing</p>
							<span class="ui-li-count">4</span>
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php echo COM_HANDOUT_CSSPATH; ?>../images/icons/folder.gif" alt="pic"/>
							<h3>Sample Documents</h3>
							<p>These are just a few sample images for testing</p>
							<span class="ui-li-count">4</span>
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php echo COM_HANDOUT_CSSPATH; ?>../images/icons/folder.gif" alt="pic"/>
							<h3>Private</h3>
							<p>Some private documents</p>
							<span class="ui-li-count">0</span>
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php echo COM_HANDOUT_CSSPATH; ?>../images/icons/folder.gif" alt="pic"/>
							<h3>Public</h3>
							<p>Some public documents</p>
							<span class="ui-li-count">12</span>
						</a>
					</li>
				</ul>-->
				<?php echo $this->loadTemplate('categories_list_mobile'); ?>
				<div class="relax">&nbsp;</div>
			
				<!-- <ul class="docs ui-btn-corner-all ui-shadow" data-role="listview">
					<li><a href="#"><img src="<?php echo COM_HANDOUT_CSSPATH; ?>../images/icons/docsmall.gif" alt="pic" class="ui-li-icon"/>Sample Word Doc</a></li>
					<li><a href="#"><img src="<?php echo COM_HANDOUT_CSSPATH; ?>../images/icons/zip.gif" alt="pic" class="ui-li-icon"/>Archive ZIP</a></li>
					<li><a href="#"><img src="<?php echo COM_HANDOUT_CSSPATH; ?>../images/icons/png.gif" alt="pic" class="ui-li-icon"/>Image PNG</a></li>
					<li><a rel="external" href="docdetail.html"><img src="<?php echo COM_HANDOUT_CSSPATH; ?>../images/icons/epub.gif" alt="pic" class="ui-li-icon"/>Shakespeare EPUB</a></li>
					<li><a href="#"><img src="<?php echo COM_HANDOUT_CSSPATH; ?>../images/icons/gif.gif" alt="pic" class="ui-li-icon"/>Image GIF</a></li>
				</ul>-->
				
					<?php if(count($this->doc_list->items)) { ?>
				<h3 class="title topped">Documents</h3>
				 <p class="title">Here documents of <?php echo $this->category->data->title; ?></p>
		<?php echo $this->loadTemplate('documents_list_mobile'); ?>
		<?php } ?>
			</div>
			<ul class="bottom clear">
				<li><a href="#" rel="external">Handout for Joomla</a></li>
				<li>|</li>
				<li><a href="#" rel="external">Switch to Desctop</a></li>
			</ul>
		</div><!-- /content -->
	</div>