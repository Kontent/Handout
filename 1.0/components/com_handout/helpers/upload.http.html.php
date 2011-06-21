<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: upload.http.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_METHOD_HTTP_HTML')) {
    return;
} else {
    define('_HANDOUT_METHOD_HTTP_HTML', 1);
}

class HTML_HandoutUploadMethod
{
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
}