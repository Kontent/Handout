<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: handout.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_HTML_HANDOUT')) {
    return;
} else {
    define('_HANDOUT_HTML_HANDOUT', 1);
}

class HTML_HandoutHandout
{
    function _quickiconButton( $link, $image, $text, $path = '/administrator/images/', $target = '_self', $confirm = null )
    {
    	$confirm = strval($confirm);
    	$confirm = $confirm ? 'onclick="return confirm(\''.$confirm.'\');"' : '';
    	
        ?>
            <div class="icon">
                <a href="<?php echo $link; ?>" target="<?php echo $target;?>" <?php echo $confirm; ?>>
                    <?php echo HandoutFactory::getImageCheckAdmin( $image, $path, NULL, NULL, $text ); ?>
                    <span><?php echo $text; ?></span>
                </a>
            </div>
        <?php
    }

    function showCPanel()
    {
         
        global $_HANDOUT;

        ?><script language="JavaScript" src="<?php echo JURI::root();?>/administrator/components/com_handout/includes/js/handout.js"></script>

        <?php 
            JToolBarHelper::title('Handout', 'home');
		?>

        <table class="adminform">
            <tr>
                <td width="55%" valign="top">
                    <div id="cpanel">
                    <?php
                        $link = "index.php?option=com_handout&amp;section=documents";
                        HTML_HandoutHandout::_quickiconButton( $link, 'icon-48-documents.png', JText::_('COM_HANDOUT_DOCS'), COM_HANDOUT_IMAGESPATH_ADMIN );
                        $link = "index.php?option=com_handout&amp;section=documents&amp;task=new";
                        HTML_HandoutHandout::_quickiconButton( $link, 'icon-48-doc-add.png', JText::_('COM_HANDOUT_NEW_DOCUMENT'), COM_HANDOUT_IMAGESPATH_ADMIN );
                    
                        $link = "index.php?option=com_handout&amp;section=files";
                        HTML_HandoutHandout::_quickiconButton( $link, 'icon-48-files.png', JText::_('COM_HANDOUT_FILES'), COM_HANDOUT_IMAGESPATH_ADMIN);
                        $link = "index.php?option=com_handout&amp;section=files&amp;task=upload";
                        HTML_HandoutHandout::_quickiconButton( $link, 'icon-48-files-new.png', JText::_('COM_HANDOUT_NEW_FILE'), COM_HANDOUT_IMAGESPATH_ADMIN);

                        $link = "index.php?option=com_handout&amp;section=categories";
                        HTML_HandoutHandout::_quickiconButton( $link, 'icon-48-category.png', JText::_('COM_HANDOUT_CATS'), COM_HANDOUT_IMAGESPATH_ADMIN);
                        $link = "index.php?option=com_handout&amp;section=groups";
                        HTML_HandoutHandout::_quickiconButton( $link, 'icon-48-groups.png', JText::_('COM_HANDOUT_GROUPS'), COM_HANDOUT_IMAGESPATH_ADMIN);
                        $link = "index.php?option=com_handout&amp;section=licenses";
                        HTML_HandoutHandout::_quickiconButton( $link, 'icon-48-licenses.png', JText::_('COM_HANDOUT_AGREEMENTS'), COM_HANDOUT_IMAGESPATH_ADMIN );

                        $link = "index.php?option=com_handout&amp;task=codes";
                        HTML_HandoutHandout::_quickiconButton( $link, 'icon-48-codes.png', JText::_('COM_HANDOUT_CODES'), COM_HANDOUT_IMAGESPATH_ADMIN );                        
                        $link = "index.php?option=com_handout&amp;section=logs";
                        HTML_HandoutHandout::_quickiconButton( $link, 'icon-48-logs.png', JText::_('COM_HANDOUT_DOWNLOAD_LOG'), COM_HANDOUT_IMAGESPATH_ADMIN);

                        $link = "index.php?option=com_handout&amp;section=config";
                        HTML_HandoutHandout::_quickiconButton( $link, 'icon-48-config.png', JText::_('COM_HANDOUT_CONFIG'), COM_HANDOUT_IMAGESPATH_ADMIN);                        
                        $link = "index.php?option=com_handout&amp;section=files&amp;task=upload";
                        HTML_HandoutHandout::_quickiconButton( $link, 'icon-48-upload.png', JText::_('COM_HANDOUT_UPLOAD_FILE'), COM_HANDOUT_IMAGESPATH_ADMIN);
                    ?>
                    </div>
                </td>
                <td width="45%" valign="top">
                    <div style="width=100%;">
                        <form action="index.php" method="post" name="adminForm">
                            <?php HANDOUT_Utils::loadAdminModules('handout_cpanel') ?>
                            <input type="hidden" name="sectionid" value="" />
                            <input type="hidden" id="cid" name="cid[]" value="" />
                            <input type="hidden" name="option" value="com_handout" />
                            <input type="hidden" name="task" value="" />
                        </form>
                    </div>
                </td>
            </tr>
        </table>
    <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }

    function showStatistics(&$row)
    {
        
        ?>
       <form action="index.php?option=com_handout" method="post" name="adminForm" id="adminForm">

        <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_TITLE_TOP_DOWNLOADS'), 'stats' )?>

        <table class="adminlist" width="98%" cellspacing="2" cellpadding="2" border="0" align="center">
            <thead>
            <tr>
                <th class="title" width="5%" align="center"><?php echo JText::_('COM_HANDOUT_RANK');?></th>
                <th class="title" width="60%" align="center"><?php echo JText::_('COM_HANDOUT_DOCUMENT_NAME_LABEL');?></th>
                <th class="title" width="10%" align="center"><?php echo JText::_('COM_HANDOUT_DOWNLOADS');?></th>
            </tr>
            </thead>

            <tbody>
		<?php
        $enum = 1;
        $color = 0;
        foreach($row as $rows) {

            ?>
				<tr class="row<?php echo $color;?>">
					<td width="5%" align="center"><?php echo $enum;?></td>
					 <td width="60%" align="left"><?php echo $rows->docname;?></td>
					 <td width="10%" align="center"><b><?php echo $rows->doccounter;?></b></td>
				</tr>
				<?php
            if (!$color) {
                $color = 1;
            } else {
                $color = 0;
            }
            $enum++;
        }

        ?>
        </tbody>
		</table>
		<input type="hidden" name="task" value="">
        <input type="hidden" name="option" value="com_handout">
		</form>

        <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }
 
}