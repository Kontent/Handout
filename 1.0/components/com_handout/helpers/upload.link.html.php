<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: upload.link.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_METHOD_LINK_HTML')) {
    return;
} else {
    define('_HANDOUT_METHOD_LINK_HTML', 1);
}

class HTML_HandoutUploadMethod
{
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
}

