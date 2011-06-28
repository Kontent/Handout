<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: codes.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_HTML_CODES')) {
    return;
} else {
    define('_HANDOUT_HTML_CODES', 1);
}

class HTML_HandoutCodes {
    function editCode($option, &$row, $lists)
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
				var errors = '';  
				if (form.name.value == "") {
					errors += "<?php echo JText::_('COM_HANDOUT_CODES_EMPTY');?>\n";
				} 
				if (form.docid.value == "0" || form.docid.value == "") {
					errors += "<?php echo JText::_('COM_HANDOUT_CODES_DOC_EMPTY');?>\n";
				} 				
				if (!form.getElementById('usage0').checked && !form.getElementById('usage1').checked) {
					errors += "<?php echo JText::_('COM_HANDOUT_CODES_USAGE_EMPTY');?>\n";
				}
				<?php if ($row->id==0): ?>
					//check duplicate codes if adding new code 
					var usedcodes = ['<?php if (sizeof($lists['usedcodes'])) echo implode("','", $lists['usedcodes']); ?>'];
					for (var i=0; i<usedcodes.length; i++) {
						if (usedcodes[i] == form.name.value) {
							errors += "<?php echo JText::_('COM_HANDOUT_CODES_DUPLICATE');?>\n";
							break;
						}
					} 	
				<?php endif; ?>
				if (errors) {
					alert(errors);
				}			
				else {
				  submitform( pressbutton );
				}
			}
			function generatecode() {
				document.getElementById("codename").value ='<?php echo strtoupper(md5(time()))?>';
			}
        </script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php
        $tmp = ($row->id ? JText::_('COM_HANDOUT_EDIT') : JText::_('COM_HANDOUT_ADD')) .' '.JText::_('COM_HANDOUT_CODE');
        HandoutHTML::adminHeading( $tmp, 'codes' )
        ?>

        <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
			<tr>
				<td width="20%" align="right"><?php echo JText::_('COM_HANDOUT_CODE');?>:</td>
				<td width="80%">
					<input class="inputbox" type="text" name="name" id="codename" size="50" maxlength="100" value="<?php echo $row->name;?>" /> <input type="button" name="autogenerate"  id="autogenerate" value="Autogenerate" onclick="generatecode()" />				</td>
			</tr>
			<tr>
				<td valign="top" align="right"><?php echo JText::_('COM_HANDOUT_CODE_DOWNLOAD');?>:</td>
				<td><?php echo $lists['downloads']; ?>				</td>
			</tr>
			<tr>
				<td valign="top" align="right"><?php echo JText::_('COM_HANDOUT_PUBLISHED');?>:</td>
				<td><?php echo $lists['published']; ?>				</td>
			</tr>
			<tr>
				<td valign="top" align="right"><?php echo JText::_('COM_HANDOUT_CODES_USAGE');?>:</td>
				<td><?php echo $lists['usage']; ?>				</td>
			</tr>
			<tr>
				<td> </td>
                <td><?php echo JText::_('COM_HANDOUT_CODES_USER_DESC'); ?></td>				
             </tr>
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="option" value="com_handout" />
			<input type="hidden" name="section" value="codes" />
			<input type="hidden" name="task" value="" />
            <?php echo HANDOUT_token::render();?>
	</table>
	</form>
    <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }
    function showCodes($option, $rows, $search, $pageNav)
    {
        $absolute_path = JPATH_ROOT;
        ?>
		<form action="index.php" method="post" name="adminForm">
        <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_TITLE_CODES'), 'codes' )?>
        <div class="hfilter">
            <?php echo JText::_('COM_HANDOUT_FILTER_CODE');?>: 
            <input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
        </div>

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
            <thead>
			<tr>
				<th width="2%" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows);?>);" /></th>
                <th class="title" width="15%"><?php echo JText::_('COM_HANDOUT_CODE')?></th>
				<th class="title" width="20%" nowrap="nowrap"><?php echo JText::_('COM_HANDOUT_CODE_DOWNLOAD')?></th>
                <th class="title" width="20%"><?php echo JText::_('COM_HANDOUT_CAT_LABEL')?></th>
				<!--<th class="title" width="10%" nowrap="nowrap"><?php //echo JText::_('COM_HANDOUT_CREATION_DATE')?></th>-->
				<th class="title" width="5%" nowrap="nowrap"><?php echo JText::_('COM_HANDOUT_PUBLISHED')?></th>
				<th class="title" width="10%" nowrap="nowrap"><?php echo JText::_('COM_HANDOUT_CODES_USAGE')?></th>
				<th class="title" width="10%" nowrap="nowrap"><?php echo JText::_('COM_HANDOUT_USER')?></th>
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
				$task = $row->published ? 'unpublish' : 'publish';
				$img = $row->published ? 'publish_g.png' : 'publish_x.png';
				$alt = $row->published ? JText::_('COM_HANDOUT_PUBLISHED') : JText::_('COM_HANDOUT_UNPUBLISH') ;

                ?>
						<?php echo JHTML::_('grid.id',$i, $row->id);?>				
    					</td>
    					<td align="left">
    						<a href="index.php?option=com_handout&section=codes&task=edit&cid[0]=<?php echo $row->id?>">
    						<?php echo $row->name;?>
    						</a>
    					</td>
                        <td align="left">
                            <?php echo $row->docname;?>
                        </td>
                        <td align="left">
                            <?php echo $row->category;?>
                        </td>
                        <td align="center">
                            <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
                            <img src="images/<?php echo $img;?>" border="0" alt="<?php echo $alt;?>" />
                            </a>
                        </td>
                        <td align="left">
                            <?php  $usage = HandoutCodes::getCodesUsage();	echo $usage[$row->usage]->text;?>
                        </td>
                        <td align="left">
                            <?php  $user = HandoutCodes::getCodesUser(); echo $user[$row->user]->text;?>
                        </td>
    				</tr>
    				<?php
                $k = 1 - $k;
            }

            ?>
            </tbody>
		  </table>


		  <input type="hidden" name="option" value="com_handout" />
		  <input type="hidden" name="section" value="codes" />
		  <input type="hidden" name="task" value="codes" />
		  <input type="hidden" name="boxchecked" value="0" />
          <?php echo HANDOUT_token::render();?>
		</form>
	   <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }
}

