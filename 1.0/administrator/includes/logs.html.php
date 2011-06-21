<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: logs.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class HTML_HandoutLogs {
    function showLogs($option, $rows, $search, $pageNav)
    {
        $absolute_path = JPATH_ROOT;

        ?>
		<form action="index.php" method="post" name="adminForm">
        <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_TITLE_DOWNLOAD_LOG'), 'logs' )?>
			<div class="hfilter">
                <?php echo JText::_('COM_HANDOUT_FILTER');?>
				<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
            </div>
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
            <thead>
				<tr>
					<th width="2%" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows);?>);" /></th>
					<th class="title" width="10%" nowrap="nowrap"><div align="center"><?php echo JText::_('COM_HANDOUT_DOWNLOAD_DATE');?></div></th>
					<th class="title" width="20%" nowrap="nowrap"><div align="center"><?php echo JText::_('COM_HANDOUT_USER');?></div></th>
					<th class="title" width="20%" nowrap="nowrap"><div align="center"><?php echo JText::_('COM_HANDOUT_IP');?></div></th>
					<th class="title" width="20%" nowrap="nowrap"><div align="center"><?php echo JText::_('COM_HANDOUT_DOC');?></div></th>
					<th class="title" width="10%" nowrap="nowrap"><div align="center"><?php echo JText::_('COM_HANDOUT_BROWSER');?></div></th>
					<th class="title" width="10%" nowrap="nowrap"><div align="center"><?php echo JText::_('COM_HANDOUT_OS');?></div></th>
				</tr>
             </thead>

             <tfoot><tr><td colspan="11"><?php echo $pageNav->getListFooter();?></td></tr></tfoot>

             <tbody>

				<?php
        $k = 0;
        for ($i = 0, $n = count($rows);$i < $n;$i++) {
            $row = &$rows[$i];
            echo "<tr class=\"row$k\">";
            echo "<td width=\"20\">";
            ?>

			<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id;?>" onclick="isChecked(this.checked);" />
					</td>
					<td align="left">
						<?php echo $row->log_datetime;?>
					</td>
					<td align="left">
						<?php echo $row->user;?>
					</td>
					<td align="center">
						<a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php echo $row->log_ip;?>" target="_blank"><?php echo $row->log_ip;?></a>
					</td>
					<td align="center">
						 <?php echo $row->docname;?>
					</td>
					<td align="center">
						 <?php echo $row->log_browser;?>
					</td>
					<td align="center">
						 <?php echo $row->log_os;?>
					</td>
				</tr>
				<?php
            $k = 1 - $k;
        }

        ?>
        </tbody>
		</table>

		<input type="hidden" name="option" value="com_handout" />
		<input type="hidden" name="section" value="logs" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
        <?php echo HANDOUT_token::render();?>
		</form>

		 <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }
}
