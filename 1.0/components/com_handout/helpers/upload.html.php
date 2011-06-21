<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: upload.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_HTML_UPLOAD')) {
    return;
} else {
    define('_HANDOUT_HTML_UPLOAD', 1);
}

class HTML_HandoutUpload
{
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

    function updateDocumentForm($list, $links, $paths, $data)
    {
    	$action = _taskLink('doc_update_process', $data->id);

		ob_start();
        ?>
       <form action="<?php echo $action ?>" method="post" enctype="multipart/form-data" id="hupdate-form" class="hform" >
		   <fieldset>
				<p><label for="hform-upload"><?php echo JText::_('COM_HANDOUT_SELECT_FILE');?></label><input id="hform-upload" type="file" /></p>
				<p><input name="submit" class="button" value="<?php echo JText::_('COM_HANDOUT_UPLOAD') ?>" type="submit" /></p>
		   </fieldset>
       <?php echo HANDOUT_token::render();?>
 	   </form>
        <?php
 		$html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
