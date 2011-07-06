<?php

 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: categories.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_HTML_CATEGORIES')) {
    return;
} else {
    define('_HANDOUT_HTML_CATEGORIES', 1);
}

class HTML_HandoutCategories
{
    function show(&$rows, $myid, &$pageNav, &$lists, $type)
    {
        $user = &JFactory::getUser();

        $section = "com_handout";
        $section_name = "Handout";

        ?>
		<form action="index.php" method="post" name="adminForm">

        <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_TITLE_CATS'), 'categories' )?>


		<table class="adminlist">
        <thead>
		<tr>
			<th width="20">
			#
			</th>
			<th width="20">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows);?>);" />
			</th>
			<th class="title"><?php echo JText::_('COM_HANDOUT_CAT_LABEL');?></th>
			<th width="10%"><?php echo JText::_('COM_HANDOUT_PUBLISHED');?></th>
			<th colspan="2"><?php echo JText::_('COM_HANDOUT_REORDER');?></th>
			<th width="10%"><?php echo JText::_('COM_HANDOUT_ACCESS');?></th>
			<th width="12%"><?php echo JText::_('COM_HANDOUT_CAT_LABEL');?> ID</th>
			<th width="12%"># <?php echo JText::_('COM_HANDOUT_DOCS');?></th>
			<th width="12%"><?php echo JText::_('COM_HANDOUT_CHECKED_OUT');?></th>
		  </tr>
        </thead>
        <tfoot>
        	<tr>
       			<td colspan="11"><?php echo $pageNav->getListFooter();?></td>
        	</tr>
        </tfoot>
        <tbody>
		<?php
        $k = 0;
        $i = 0;
        $n = count($rows);
        foreach ($rows as $row) {
            $img = $row->published ? 'tick.png' : 'publish_x.png';
            $task = $row->published ? 'unpublish' : 'publish';
            $alt = $row->published ? 'Published' : 'Unpublished';
            if (!$row->access) {
                $color_access = 'class="active"';
                $task_access = 'accessregistered';
            } else if ($row->access == 1) {
                $color_access = 'class="inactive"';
                $task_access = 'accessspecial';
            } else {
                $color_access = 'class="black"';
                $task_access = 'accesspublic';
            }

            ?>
			<tr class="<?php echo "row$k";?>">
				<td width="20" align="right">
					<?php echo ( $i + 1 + $pageNav->limitstart ); ?>
				</td>
				<td width="20">
					<?php echo JHTML::_('grid.id',$i, $row->id, ($row->checked_out_contact_category && $row->checked_out_contact_category != $user->id));?>
				</td>
				<td width="35%">
					<?php
	           			 if ($row->checked_out_contact_category && ($row->checked_out_contact_category != $user->id)) {
	                ?>
					<?php echo $row->treename . ' ( ' . $row->title . ' )';?>
						&nbsp;[ <em><?php echo JText::_('COM_HANDOUT_CHECKED_OUT')?></em> ]
					<?php
	           			 } else {
	                ?>
					<a href="#edit" onClick="return listItemTask('cb<?php echo $i;?>','edit')">
						<?php echo $row->treename . ' ( ' . $row->title . ' )';?>
					</a>
						<?php
	            		}
	            	?>
				</td>
				<td align="center">
					<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
						<img src="images/<?php echo $img;?>"  border="0" alt="<?php echo $alt;?>" />
					</a>
				</td>
				<?php
		            if ($section <> 'content') {
		                ?>
				<td>
					<?php echo $pageNav->orderUpIcon($i);?>
				</td>
				<td>
					<?php echo $pageNav->orderDownIcon($i, $n);?>
				</td>
				<?php
		            }
		            ?>
				<td align="center">
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_access;?>')" <?php echo $color_access;?>>
						<?php echo $row->groupname;?>
					</a>
				</td>
				<td align="center">
					<?php echo $row->id;?>
				</td>
				<td align="center">
					<?php echo $row->documents;?>
				</td>
				<td align="center">
					<?php echo $row->checked_out_contact_category ? $row->editor : "";?>
				</td>
				<?php
            		$k = 1 - $k;
           		?>
			</tr>
			<?php
            	$k = 1 - $k;
            	$i++;
       			 }
       		 ?>
        </tbody>
		</table>

		<input type="hidden" name="option" value="com_handout" />
		<input type="hidden" name="section" value="categories" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="chosen" value="" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="type" value="<?php echo $type;?>" />
        <?php echo HANDOUT_token::render();?>
	</form>
		<?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    	}
    	 function showToSelect(&$rows, &$pageNav, $type)
    		{
        	?>

    		<form action="index.php" method="post" name="adminForm">
    		<?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_CATS'), 'categories' )?>

			    <table class="adminlist">
			       <thead>
					<tr>
						<th class="title"><?php echo JText::_('COM_HANDOUT_CATNAME_LABEL');?></th>
						<th width="10%"><?php echo JText::_('COM_HANDOUT_PUBLISHED');?></th>
					</tr>
			       </thead>
			       <tfoot><tr><td colspan="2"><?php echo $pageNav->getListFooter();?></td></tr></tfoot>
				    <tbody>
				    <?php
				    $k = 0;
				    foreach ($rows as $row) {
				    $img = $row->published ? 'tick.png' : 'publish_x.png';
				    $alt = $row->published ? 'Published' : 'Unpublished';
				    ?>
				    <tr class="<?php echo "row$k";?>">
					    <td width="35%">
					    	<a style="cursor: pointer;" href="#" onclick="window.parent.MM_selectElement('<?php echo $row->id; ?>', '<?php echo str_replace(array("'", "\""), array("\\'", ""),$row->title); ?>', '<?php echo JRequest::getVar('object'); ?>');">
							<?php echo $row->treename . ' ( ' . $row->title . ' )';?></a>
						</td>
					    <td align="center"><img src="images/<?php echo $img;?>"  border="0" alt="<?php echo $alt;?>" /></td>
					</tr>
				    <?php $k = 1 - $k; ?>
						<?php } ?>
				     </tbody>
			    </table>
				<input type="hidden" name="option" value="com_handout" />
			    <input type="hidden" name="section" value="categories" />
			    <input type="hidden" name="task" value="" />
			    <input type="hidden" name="chosen" value="" />
			    <input type="hidden" name="act" value="" />
			    <input type="hidden" name="task" value="element" />
			    <input type="hidden" name="tmpl" value="component" />
			    <input type="hidden" name="object" value="<?php echo JRequest::getString('object'); ?>" />
			    <input type="hidden" name="type" value="<?php echo $type;?>" />
			    <?php echo HANDOUT_token::render();?>
    		</form>
    <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }

    /**
    * Writes the edit form for new and existing categories
    *
    */
    function edit(&$row, $section, &$lists, $redirect)
    {
		JHTML::_('behavior.tooltip');

        if ($row->image == "") {
            $row->image = 'blank.png';
        }
        JFilterOutput::objectHTMLSafe($row, ENT_QUOTES, 'description');
        ?>
		<script language="javascript" type="text/javascript">
			function submitbutton(pressbutton, section) {
				var form = document.adminForm;
				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				}

				if ( form.title.value == "" ) {
					alert('<?php echo JText::_('COM_HANDOUT_CAT_MUST_SELECT_TITLE');?>');
				} else {
					form.getElementById("catname").value = form.title.value; //copy title into name
					<?php
						jimport( 'joomla.html.editor' );
						$editor =& JFactory::getEditor();
						echo $editor->save( 'description' );
					?>
					submitform(pressbutton);
				}
			}
		</script>

		<form action="index.php" method="post" name="adminForm">
	        <?php
	        $tmp = ($row->id ? JText::_('COM_HANDOUT_EDIT') : JText::_('COM_HANDOUT_ADD')).' '.JText::_('COM_HANDOUT_CAT_LABEL').' '.$row->title;
			HandoutHTML::adminHeading( $tmp, 'categories' )
	        ?>
			<table width="100%">
				<tr>
					<td valign="top">
						<table class="adminform">
						<tr>
							<th colspan="3">
							<?php echo JText::_('COM_HANDOUT_CATDETAILS');?>
							</th>
						</tr>
						<tr>
							<td>
							<?php echo JText::_('COM_HANDOUT_CATTITLE_LABEL');?>
							</td>
							<td colspan="2">
							<input class="text_area" type="text" name="title" value="<?php echo $row->title;?>" size="50" maxlength="50" title="A short name to appear in menus" />
		                    <input type="hidden" name="name" id="catname" value="<?php echo $row->name;?>" style="display:hidden" />
							</td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_HANDOUT_PARENTITEM_LABEL');?></td>
							<td>
							<?php echo $lists['parent'];?>
							</td>
						</tr>
						<tr>
							<td>
							<?php echo JText::_('COM_HANDOUT_IMAGE_LABEL');?>
							</td>
							<td>
							<?php echo $lists['image'];?>
							</td>
							<td rowspan="4" width="50%">
							<script language="javascript" type="text/javascript">
							if (document.forms[0].image.options.value!=''){
							  jsimg='../images/stories/' + getSelectedValue( 'adminForm', 'image' );
							} else {
							  jsimg='../images/M_images/blank.png';
							}
							document.write('<img src=' + jsimg + ' name="imagelib" width="80" height="80" border="2" alt="<?php echo JText::_('COM_HANDOUT_IMAGE_PREVIEW');?>" />');
							</script>
							</td>
						</tr>
						<tr>
							<td>
							<?php echo JText::_('COM_HANDOUT_IMAGEPOS_LABEL');?>
							</td>
							<td>
							<?php echo $lists['image_position'];?>
							</td>
						</tr>
						<tr>
							<td>
							<?php echo JText::_('COM_HANDOUT_ORDERING_LABEL');?>
							</td>
							<td>
							<?php echo $lists['ordering'];?>
							</td>
						</tr>
						<tr>
							<td>
							<?php echo JText::_('COM_HANDOUT_ACCESSLEVEL_LABEL');?>
							</td>
							<td>
							<?php echo $lists['access'];?>
							</td>
						</tr>
						<tr>
							<td>
							<?php echo JText::_('COM_HANDOUT_PUBLISHED_LABEL');?>
							</td>
							<td>
							<?php echo $lists['published'];?>
							</td>
						</tr>
						<tr>
							<td valign="top">
							<?php echo JText::_('COM_HANDOUT_DESCRIPTION_LABEL');?>
							</td>
							<td colspan="2">
							<?php
								jimport( 'joomla.html.editor' );
								$editor =& JFactory::getEditor();
								echo $editor->display('description', $row->description , '600', '300', '50', '5') ;
							?>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<?php echo JText::_('COM_HANDOUT_METADESCRIPTION_LABEL');?>
							</td>
							<td>
								<!-- TO DO: hook this textbox up -->
								<textarea rows="7" cols="50"> </textarea>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<?php echo JText::_('COM_HANDOUT_METAKEYWORDS_LABEL');?>
							</td>
							<td>
								<!-- TO DO: hook this textbox up -->
								<textarea rows="7" cols="50"> </textarea>
							</td>
						</tr>
					</table>
				</td>
		      </tr>
		</table>

			<input type="hidden" name="option" value="com_handout" />
			<input type="hidden" name="section" value="categories" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="oldtitle" value="<?php echo $row->title ;?>" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="sectionid" value="com_handout" />
			<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
	        <?php echo HANDOUT_token::render();?>
		</form>
        <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }
}