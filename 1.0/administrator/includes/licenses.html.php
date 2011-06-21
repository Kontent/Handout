<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: licenses.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_HTML_AGREEMENTS')) {
    return;
} else {
    define('_HANDOUT_HTML_AGREEMENTS', 1);
}

class HTML_HandoutLicenses {
    function editLicense($option, &$row)
    {
        JFilterOutput::objectHTMLSafe($row);
        ?>
        <script language="javascript" type="text/javascript">
            function submitbutton(pressbutton) {
				  var form = document.adminForm;
				  if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				  }
				if (form.name.value == "") {
					alert ( "<?php echo _E_WARNTITLE;?>" );
				} else {
				  <?php 
				  	  jimport( 'joomla.html.editor' );
					  $editor =& JFactory::getEditor();
	                  echo $editor->save( 'license' );	
				  ?>
				  submitform( pressbutton );
				}
			}
        </script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php
        $tmp = ($row->id ? JText::_('COM_HANDOUT_EDIT') : JText::_('COM_HANDOUT_ADD')) .' '.JText::_('COM_HANDOUT_AGREEMENT');
        HandoutHTML::adminHeading( $tmp, 'licenses' )
        ?>

        <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
			<tr>
				<td width="20%" align="right"><?php echo JText::_('COM_HANDOUT_AGREEMENT_NAME');?>:</td>
				<td width="80%">
					<input class="inputbox" type="text" name="name" size="50" maxlength="100" value="<?php echo $row->name;?>" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="right"><?php echo JText::_('COM_HANDOUT_AGREEMENT_TEXT');?>:</td>
				<td>
				<?php
					jimport( 'joomla.html.editor' );
					$editor =& JFactory::getEditor();
					echo $editor->display('license', $row->license , '700', '600', '60', '30') ;
				?>
				</td>
			</tr>

			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="option" value="com_handout" />
			<input type="hidden" name="section" value="licenses" />
			<input type="hidden" name="task" value="" />
            <?php echo HANDOUT_token::render();?>
		</form>
	</table>
	</form>
    <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }
    function showLicenses($option, $rows, $search, $pageNav)
    {
        $absolute_path = JPATH_ROOT;
        ?>
		<form action="index.php" method="post" name="adminForm">
        <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_TITLE_AGREEMENTS'), 'licenses' )?>
        <div class="hfilter">
            <?php echo JText::_('COM_HANDOUT_FILTER_NAME');?>
            <input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
        </div>

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
            <thead>
			<tr>
				<th width="2%" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows);?>);" /></th>
				<th class="title" width="30%" nowrap="nowrap"><?php echo JText::_('COM_HANDOUT_AGREEMENT_NAME')?></th>
                <th class="title" width="68%"><?php echo JText::_('COM_HANDOUT_AGREEMENT_TEXT')?></th>
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
    						<a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','edit')">
    						<?php echo $row->name;?>
    						</a>
    					</td>
                        <td align="left">
                            <?php echo $row->license;?>
                        </td>
    				</tr>
    				<?php
                $k = 1 - $k;
            }

            ?>
            </tbody>
		  </table>


		  <input type="hidden" name="option" value="com_handout" />
		  <input type="hidden" name="section" value="licenses" />
		  <input type="hidden" name="task" value="licenses" />
		  <input type="hidden" name="boxchecked" value="0" />
          <?php echo HANDOUT_token::render();?>
		</form>
	   <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }
}

