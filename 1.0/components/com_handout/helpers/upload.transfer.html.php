<?php
/**
 * JoomDOC - Joomla! Document Manager
 * @version $Id: upload.transfer.html.php 608 2008-02-18 13:31:26Z mjaz $
 * @package Handout
 * @copyright (C) 2009 Artio s.r.o.
 * @license see COPYRIGHT.php
 * @link http://www.artio.net Official website
 * JoomDOC is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_METHOD_TRANSFER_HTML')) {
    return;
} else {
    define('_HANDOUT_METHOD_TRANSFER_HTML', 1);
}

class HTML_HandoutUploadMethod
{
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
}
