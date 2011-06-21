<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: groups.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_HTML_GROUPS')) {
    return;
} else {
    define('_HANDOUT_HTML_GROUPS', 1);
}
require_once($_HANDOUT->getPath('classes', 'file'));

class HTML_HandoutGroups
{
    function showGroups($option, $rows, $search, $pageNav)
    {
        $database = &JFactory::getDBO(); 
        $user = &JFactory::getUser();

        ?>
        <form action="index.php" method="post" name="adminForm">
        <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_TITLE_GROUPS'), 'groups' )?>
        <div class="hfilter">
            <?php echo JText::_('COM_HANDOUT_FILTER_NAME');?>:
            <input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
        </div>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
            <thead>
			<tr>
				<th width="2%" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows);?>);" /></th>
				<th class="title" width="30%"><div align="center"><?php echo JText::_('COM_HANDOUT_GROUP');?></div></th>
				<th class="title" width="65%"><div align="center"><?php echo JText::_('COM_HANDOUT_DESCRIPTION');?></div></th>
				<th class="title" width="5%"><div align="center"><?php echo JText::_('COM_HANDOUT_EMAIL');?></div></th>
			</tr>
            </thead>
            <tfoot><tr><td colspan="11"><?php echo $pageNav->getListFooter();?></td></tr></tfoot>
            <tbody>
			<?php
            $k = 0;
            for ($i = 0, $n = count($rows);$i < $n;$i++) {
                $row = &$rows[$i];
                echo "<tr class='row$k'>";
                echo "<td width='20'>";

                ?>
    				<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->groups_id;?>" onclick="isChecked(this.checked);" />
    					</td>
    					<td align="left">
    						<a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','edit')">
    					<?php echo $row->groups_name;?>
    						</a>
    					</td>
    					<td width="60%" align="left"><?php echo $row->groups_description;?></td>
    					<td width="10%" align="center"><a href="index.php?option=com_handout&section=groups&task=emailgroup&gid=<?php echo $row->groups_id;?>"><img src="<?php echo JURI::root()?>/administrator/components/com_handout/images/icon-16-sendmail.png" border=0></a></td>
    			  <?php
                echo "</tr>";
                $k = 1 - $k;
            }
        ?>
        </tbody>
		</table>

	  <input type="hidden" name="option" value="com_handout" />
      <input type="hidden" name="section" value="groups" />
	  <input type="hidden" name="task" value="" />
	  <input type="hidden" name="boxchecked" value="0" />
      <?php echo HANDOUT_token::render();?>
	</form>

   <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }

    function editGroup($option, &$row, $usersList, $toAddUsersList)
    {
         
        JFilterOutput::objectHTMLSafe($row);
        $tabs = new JPaneTabs(0);

        ?>
		<script>
			function submitbutton(pressbutton) {

			  var form = document.adminForm;

			  if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			  }

			  // do field validation

			  if (form.groups_name.value == ""){
				alert( "<?php echo JText::_('COM_HANDOUT_ENTRY_NAME');?>" );
			  } else {
				allSelected(document.adminForm['users_selected[]']);
				submitform( pressbutton );
			  }
			}
		</script>

		<script>
			// moves elements from one select box to another one
			function moveOptions(from,to) {
			  // Move them over
			  for (var i=0; i<from.options.length; i++) {
				var o = from.options[i];
				if (o.selected) {
				  to.options[to.options.length] = new Option( o.text, o.value, false, false);
				}
			  }
			  // Delete them from original
			  for (var i=(from.options.length-1); i>=0; i--) {
				var o = from.options[i];
				if (o.selected) {
				  from.options[i] = null;
				}
			  }
			  from.selectedIndex = -1;
			  to.selectedIndex = -1;
			}

			function allSelected(element) {

			   for (var i=0; i<element.options.length; i++) {
					var o = element.options[i];
					o.selected = true;

				}
			 }
		</script>

		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="Javascript" src="<?php echo JURI::root();?>/includes/js/overlib_mini.js"></script>

		<?php $tmp = ($row->groups_id ? JText::_('COM_HANDOUT_EDIT') : JText::_('COM_HANDOUT_ADD')).' '.JText::_('COM_HANDOUT_GROUP');
        HandoutHTML::adminHeading( $tmp, 'groups' )
        ?>
        <form action="index.php" method="post" name="adminForm" id="adminForm">

            <div style="float:left;width:45%;margin-right:30px;">
                <table cellpadding="0" cellspacing="0" border="0" width="100%" class="adminform">
                    <tr><th><?php echo JText::_('COM_HANDOUT_GROUP')?></th></tr>
					<tr><td width="20%" align="right"><?php echo JText::_('COM_HANDOUT_GROUP');?>:</td></tr>
					<tr><td width="80%">
                        <input class="inputbox" type="text" name="groups_name" size="40" maxlength="100" value="<?php echo htmlspecialchars($row->groups_name, ENT_QUOTES);?>" />
					</td></tr>
					<tr><td valign="top" align="right"><?php echo JText::_('COM_HANDOUT_DESCRIPTION');?></td></tr>
					<tr><td valign="top">
						<textarea name="groups_description" cols="50" rows="14"><?php echo htmlspecialchars($row->groups_description, ENT_QUOTES);?></textarea>
					</td></tr>
    		   </table>
            </div>

            <div style="width:50%;padding-bottom:50px;">
               <table cellpadding="0" cellspacing="0" border="0" width="100%" class="adminform">
                    <tr>
                        <th colspan="3"><?php echo JText::_('COM_HANDOUT_ADD_REMOVE_MEMBERS')?></th>
                    </tr>

                    <tr>
                        <td width="40%">
                            <?php echo JText::_('COM_HANDOUT_USERS_AVAILABLE');?>
                            <span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_ADDING_USERS');?>::<?php echo JText::_('COM_HANDOUT_ADD_GROUP_DESC');?>">
								<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
                        </td>
                        <td width="20%">&nbsp;</td>
                        <td width="40%"><?php echo JText::_('COM_HANDOUT_MEMBERS_IN_GROUP');?></td>
                    </tr>
                    <tr>
                        <td width="40%"><?php echo $toAddUsersList;?></td>
                        <td width="20%">
                            <input style="width:50px" type="button" name="Button" value="&gt;" onClick="moveOptions(document.adminForm.users_not_selected, document.adminForm['users_selected[]'])" />
                            <br /><br />
                            <input style="width:50px" type="button" name="Button" value="&lt;" onClick="moveOptions(document.adminForm['users_selected[]'],document.adminForm.users_not_selected)" />
                            <br /><br />
                        </td>
                        <td width="40%"><?php echo $usersList;?></td>
                    </tr>
                </table>
            </div>

            <input type="hidden" name="groups_id" value="<?php echo $row->groups_id;?>" />
            <input type="hidden" name="option" value="com_handout" />
            <input type="hidden" name="section" value="groups" />
            <input type="hidden" name="task" value="" />
            <?php echo HANDOUT_token::render();?>
        </form><?php
        include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }

    function messageForm($group, &$list)
    {
        
        ?>
        <form action="index.php" name="adminForm" method="POST">
        <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_EMAIL_GROUP'), 'sendemail' )?>

        <table cellpadding="5" cellspacing="1" border="0" width="100%" class="adminform">
            <tr>
                <td width="150"><?php echo JText::_('COM_HANDOUT_GROUP');?>:</td>
                <td width="85%"><?php echo $group[0]->groups_name;?></td>
			</tr>
            <tr>
                <td width="150"><?php echo JText::_('COM_HANDOUT_SUBJECT');?>:</td>
                <td width="85%"><input class="inputbox" type="text" name="mm_subject" value="" size="50"></td>
            </tr>
            <tr>
                <td width="150"><?php echo JText::_('COM_HANDOUT_EMAIL_LEADIN');?>:</td>
                <td width="85%"><textarea cols="50" rows="2" name="mm_leadin" wrap="virtual"
					class="inputbox"><?php echo $list['leadin'];?></textarea></td>
			</tr>
            <tr>
                <td width="150" valign="top"><?php echo JText::_('COM_HANDOUT_MESSAGE');?>:</td>
                <td width="85%"><textarea cols="50" rows="5" name="mm_message" wrap="virtual" class="inputbox"></textarea></td>
            </tr>
        </table>
        <!--<input type="submit" name="submit" value="<?php echo JText::_('COM_HANDOUT_SEND_EMAIL');?>">-->
        <input type="hidden" name="option" value="com_handout" />
        <input type="hidden" name="section" value="groups" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="gid" value="<?php echo $group[0]->groups_id;?>" />
        <?php echo HANDOUT_token::render();?>
        </form>
        <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }
}

