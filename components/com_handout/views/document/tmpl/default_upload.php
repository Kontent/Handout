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

/* Display the edit form page(required)
*
* This template is called when user preforms the Edit operation on a document.
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
JHTML::_('behavior.tooltip');

?>
<?php $this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'handout' . DS . 'tmpl' );?>
<?php echo $this->loadTemplate('menu'); ?>

<div id="handout" class="hupload">

	<?php if($this->update) : ?>
		<h2><?php echo JText::_('COM_HANDOUT_TITLE_UPDATE');?></h2>
	<?php else : ?>
		<h2><?php echo JText::_('COM_HANDOUT_TITLE_UPLOAD');?></h2>
	<?php endif; ?>

	<?php
		switch($this->step) :
			case '1' :
				echo '<p class="hupload-steps">'.JText::_('COM_HANDOUT_UPLOAD_STEP')." ".$this->step." ".JText::_('COM_HANDOUT_UPLOAD_OF')." 3".'</p>';
				echo uploadMethodsForm ( $this->lists );
				break;

			case '2' :
				echo '<p class="hupload-steps">'.JText::_('COM_HANDOUT_UPLOAD_STEP')." ".$this->step." ".JText::_('COM_HANDOUT_UPLOAD_OF')." 3 <span>(";
				switch($this->method) :
					case 'http' 	: 	echo JText::_('COM_HANDOUT_UPLOAD_A_FILE'); break;
					case 'transfer' : 	echo JText::_('COM_HANDOUT_REMOTELY_TRANSFER_A_FILE'); break;
					case 'link'	 :	echo JText::_('COM_HANDOUT_LINK_TO_FILE'); break;
					default : break;
				endswitch;
				echo ')</span></p>';
				switch($this->method) :
					case 'http' 	: 	echo uploadFileForm($this->lists); break;
					case 'transfer' : 	echo transferFileForm($this->lists); break;
					case 'link'	 :	echo linkFileForm($this->lists); break;
					default : break;
				endswitch;
				break;

			case '3' :
				//display the document edit form
				?>
					<ul>
						<li><a title="<?php echo JText::_('COM_HANDOUT_CANCEL')?>" href="javascript:submitbutton('cancel');" ><span><span><?php echo JText::_('COM_HANDOUT_CANCEL')?></span></span></a></li>
						<li><a title="<?php echo JText::_('COM_HANDOUT_SAVE')?>" href="javascript:submitbutton('save');"><span><span><?php echo JText::_('COM_HANDOUT_SAVE')?></span></span></a></li>
					</ul>
				<?php
				echo $this->loadTemplate('edit');
				echo editDocumentForm ( $this->edit_doc, $this->edit_lists, $this->edit_last, $this->edit_created, $this->edit_params );
				?>
					<ul>
						<li><a title="<?php echo JText::_('COM_HANDOUT_CANCEL')?>" href="javascript:submitbutton('cancel');" ><span><span><?php echo JText::_('COM_HANDOUT_CANCEL')?></span></span></a></li>
						<li><a title="<?php echo JText::_('COM_HANDOUT_SAVE')?>" href="javascript:submitbutton('save');"><span><span><?php echo JText::_('COM_HANDOUT_SAVE')?></span></span></a></li>
					</ul>
				<?php
				break;
		endswitch;
	?>
	<?php include_once(JPATH_COMPONENT . DS . 'footer.php'); ?>
</div>

<?php
	function transferFileForm($lists)
	{
		ob_start();
		?>
		<form action="<?php echo $lists['action'] ; ?>" method="post" id="hupload-form" class="hform">
			<fieldset class="input">
				<p><label for="url"><?php echo JText::_('COM_HANDOUT_REMOTEURL') ?></label><br />
				<input name="url" type="text" id="url" value="<?php echo $lists['url'];?>" />

					<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_REMOTEURL');?>::<?php echo JText::_('COM_HANDOUT_REMOTEURL_DESC');?>">
						<img border="0" alt="Tooltip" src="media/com_handout/images/icon-16-tooltip.png" /></span>
						</p>
				<p><label for="localfile"><?php echo JText::_('COM_HANDOUT_LOCALNAME') ;?></label><br />
				<input name="localfile" type="text" id="url" value="<?php echo $lists['localfile'];?>">


				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_LOCALNAME');?>::<?php echo JText::_('COM_HANDOUT_LOCALNAME_DESC');?>">
						<img border="0" alt="Tooltip" src="media/com_handout/images/icon-16-tooltip.png" /></span>
				</p>
				<input name="submit" class="button" value="<?php echo JText::_('COM_HANDOUT_BACK');?>" onclick="window.history.back()" type="button" >
				<input name="submit" class="button" value="<?php echo JText::_('COM_HANDOUT_REMOTELY_TRANSFER_A_FILE');?>" type="submit" />
			</fieldset>
			<input type="hidden" name="method" value="transfer" />
			<?php echo HANDOUT_token::render();?>
		</form>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}


	function linkFileForm($lists)
	{
		ob_start();
		?>
		<form action="<?php echo $lists['action'] ;?>" method="post" id="hupload-form" class="hform">
			<fieldset class="input">
				<p><label for="hform-url"><?php echo JText::_('COM_HANDOUT_REMOTEURL');?></label><br />
				<input name="url" type="text" id="hform-url" value="<?php /*echo $parms['url'];*/ ?>" />

				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_REMOTEURL');?>::<?php echo JText::_('COM_HANDOUT_LINKURL_DESC');?>">
						<img border="0" alt="Tooltip" src="media/com_handout/images/icon-16-tooltip.png" /></span>
				</p>

				<input name="submit" class="button" value="<?php echo JText::_('COM_HANDOUT_BACK');?>" onclick="window.history.back()" type="button" >
				<input name="submit" class="button" value="<?php echo JText::_('COM_HANDOUT_LINK');?>" type="submit" />
			 </fieldset>
			<input type="hidden" name="method" value="link" />
		 	<?php echo HANDOUT_token::render();?>
	   	</form>
   		<?php
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
	}

	function uploadFileForm($lists)
	{
		$progressImg = JURI::root().'/administrator/components/com_handout/images/uploader.gif';
		ob_start();
		?>
		<form action="<?php echo $lists['action'] ;?>" method="post" enctype="multipart/form-data" id="hupload-form" class="hform">
			<fieldset class="input">
				<div>
					<div id="progress" style="display:none;"><img src="<?php echo $progressImg?>" alt="Upload Progress" />&nbsp;<?php echo JText::_('COM_HANDOUT_ISUPLOADING')?></div>
					<p><label for="upload"><?php echo JText::_('COM_HANDOUT_SELECT_FILE');?></label>
					<input id="upload" name="upload" type="file" name="file" />&nbsp;&nbsp;
					<input name="submit" class="button" value="<?php echo JText::_('COM_HANDOUT_UPLOAD');?>" type="submit" onclick="document.getElementById('progress').style.display = 'block';" /></p>
				</div>
				<input name="submit" class="button" value="<?php echo JText::_('COM_HANDOUT_BACK');?>" onclick="window.history.back()" type="button" >
			</fieldset>
			<input type="hidden" name="method" value="http" />
			<?php echo HANDOUT_token::render();?>
		</form>

		<?php
			$html = ob_get_contents();
			ob_end_clean();
	   		return $html;
	}

	function uploadMethodsForm($lists)
	{
		ob_start();
		?>
	   <form action="<?php echo $lists['action'];?>" method="post" id="hupload-form" class="hform">
		   <fieldset>
				<p><label for="method"><?php echo JText::_('COM_HANDOUT_UPLOADMETHOD');?></label></p>
				<p><?php echo $lists['methods'];?></p>
				<p><input name="submit" class="button" value="<?php echo JText::_('COM_HANDOUT_NEXT');?>" type="submit" /></p>
		   </fieldset>
		</form>
		<?php
 		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

?>