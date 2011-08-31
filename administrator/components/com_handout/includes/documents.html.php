<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */


defined('_JEXEC') or die;

if (defined('_HANDOUT_HTML_DOCUMENTS')) {
	return;
} else {
	define('_HANDOUT_HTML_DOCUMENTS', 1);
}

class HTML_HandoutDocuments
{
	function showDocuments($rows, $lists, $search, $pageNav, $number_unpublished, $view_type = 1)
	{
		$database = &JFactory::getDBO();
		$user = &JFactory::getUser();
		$_HANDOUT = &HandoutFactory::getHandout();

		JHTML::_('behavior.tooltip');

		?>

		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_TITLE_DOCS'), 'documents' )?>

		<div class="hfilter">
			<?php echo JText::_('COM_HANDOUT_FILTER');?>
			<input class="text_area" type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
			<?php echo $lists['catid'];?>

			<span class="small">
				<?php
				if ($number_unpublished > 0) {
					echo " [$number_unpublished " . JText::_('COM_HANDOUT_DOCS_NOT_PUBLISHED') . "] ";
				}
				if ($number_unpublished < 1) {
					echo " [" . JText::_('COM_HANDOUT_NO_PENDING_DOCS') . "] ";
				}
				?>
			</span>
		</div>

		<table class="adminlist">
		  <thead>
		  <tr>
			<th width="2%" align="left" >
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows);?>);" />
			</th>
			<th width="15%" align="left">
				<a href="index.php?option=com_handout&section=documents&sort=name"><?php echo JText::_('COM_HANDOUT_DOCUMENT_NAME_LABEL');?></a>
			</th>
			<th width="15%" align="left" >
		   		<a href="index.php?option=com_handout&section=documents&sort=filename"><?php echo JText::_('COM_HANDOUT_FILENAME');?></a>
			</th>
			<th width="15%" align="left">
				<a href="index.php?option=com_handout&section=documents&sort=catsubcat"><?php echo JText::_('COM_HANDOUT_CAT_LABEL');?></a>
			</th>
			<th width="10%" align="center">
				<a href="index.php?option=com_handout&section=documents&sort=date"><?php echo JText::_('COM_HANDOUT_PUBLISH_DATE');?></a>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_HANDOUT_OWNER');?>
			</th>
			<th width="5%">
				<?php echo JText::_('COM_HANDOUT_PUBLISHED');?>
			</th>
			</th -->
			<th width="5%">
				<?php echo JText::_('COM_HANDOUT_SIZE');?>
			</th>
			<th width="5%">
				<?php echo JText::_('COM_HANDOUT_DOWNLOADS');?>
			</th>
			<th width="5%" nowrap="nowrap">
				<?php echo JText::_('COM_HANDOUT_CHECKED_OUT');?>
			</th>
		  </tr>
		  </thead>

		  <tfoot><tr><td colspan="11"><?php echo $pageNav->getListFooter();?></td></tr></tfoot>

		  <tbody>
		  <?php
		$k = 0;
		for ($i = 0, $n = count($rows);$i < $n;$i++) {
			$row = &$rows[$i];
			$task = $row->published ? 'unpublish' : 'publish';
			$img = $row->published ? 'publish_g.png' : 'publish_x.png';
			$alt = $row->published ? JText::_('COM_HANDOUT_PUBLISHED') : JText::_('COM_HANDOUT_UNPUBLISH') ;

			$file = new HANDOUT_File($row->docfilename, $_HANDOUT->getCfg('handoutpath'));

			?><tr class="row<?php echo $k;?>">
				<td width="20">
					<?php echo JHTML::_('grid.id',$i, $row->id, ($row->checked_out && $row->checked_out != $user->id));?>
				</td>
				<td width="15%">
					<?php
					if ($row->checked_out && ($row->checked_out != $user->id)) {
					?>
							<?php echo $row->docname;?>
							&nbsp;[ <em><?php echo JText::_('COM_HANDOUT_CHECKED_OUT');?></em> ]
					<?php
						} else {
					?>
						<a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','edit')" class="hasTip" title="<?php echo JText::_('COM_HANDOUT_EDIT_THIS_ITEM_LABEL');?>::<?php echo JText::_('COM_HANDOUT_EDIT_THIS_ITEM_DESC');?>">
							<?php echo $row->docname;?>
						</a>
					<?php
						}
					?>
				</td>
				<td>
					<?php if ($file->exists()) {?>
						<a href="index.php?option=com_handout&section=documents&task=download&bid=<?php echo $row->id;?>" target="_blank" class="hasTip" title="<?php echo JText::_('COM_HANDOUT_DOWNLOAD_THIS_ITEM_LABEL');?>::<?php echo JText::_('COM_HANDOUT_DOWNLOAD_THIS_ITEM_DESC');?>">
						<?php echo HANDOUT_Utils::urlSnippet($row->docfilename);?></a>
						<?php
					} else {
						echo "<span class='hfile-missing'>";
						echo JText::_('COM_HANDOUT_FILE_MISSING');
						echo "</span>";
					}
					?>
				</td>
				<td width="15%"><?php echo $row->treename ?></td>
			   	<td width="10%" align="center" nowrap="nowrap"><?php echo HandoutFactory::getFormatDate($row->docdate_published); ?></td>
			   	<td align="center"><?php echo HANDOUT_Utils::getUserName($row->docowner); ?></td>
				<td width="5%" align="center">
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
					<img src="images/<?php echo $img;?>" border="0" alt="<?php echo $alt;?>" />
					</a>
				</td>
				<td width="5%" align="center" nowrap="nowrap">
				<?php
				if ($file->exists()) {
					echo $file->getSize();
				}
				?>
				</td>
				<td width="5%" align="center"><?php echo $row->doccounter;?></td>
				<?php
				if ($row->checked_out) {
					?>
						<td width="5%" align="center"><?php echo $row->editor;?></td>
					<?php
				} else {
					?>
					<td width="5%" align="center">---</td>
					<?php
				}

				?></tr><?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		  </table>

		  <input type="hidden" name="option" value="com_handout" />
		  <input type="hidden" name="section" value="documents" />
		  <input type="hidden" name="task" value="" />
		  <input type="hidden" name="boxchecked" value="0" />
		  <?php echo HANDOUT_token::render();?>
	  </form>

   	  <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
	}

function showDocumentsToSelect($rows, $lists, $search, $pageNav, $number_unpublished, $view_type = 1)
	{
		$database = &JFactory::getDBO();
		$user = &JFactory::getUser();
		$_HANDOUT = &HandoutFactory::getHandout();


		$link = 'index.php?option=com_handout&section=documents&task=element&tmpl=component&object=' . JRequest::getString('object').'&sort=';
		?>

		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_DOCS'), 'documents' )?>

		<div class="hfilter">
			<?php echo JText::_('COM_HANDOUT_FILTER');?>
			<input class="text_area" type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
			<?php echo $lists['catid'];?>
		</div>

		<table class="adminlist">
		  <thead>
		  <tr>
			<th width="15%" align="left">
			<a href="<?php echo $link; ?>name"><?php echo JText::_('COM_HANDOUT_NAME');?></a>
			</th>
			<th width="15%" align="left" >
			<a href="<?php echo $link; ?>filename"><?php echo JText::_('COM_HANDOUT_FILE');?></a>
			</th>
			<th width="15%" align="left">
			<a href="<?php echo $link; ?>catsubcat"><?php echo JText::_('COM_HANDOUT_CAT_LABEL');?></a>
			</th>
			<th width="5%">
			<?php echo JText::_('COM_HANDOUT_PUBLISHED');?>
			</th>
		  </tr>
		  </thead>

		  <tfoot><tr><td colspan="5"><?php echo $pageNav->getListFooter();?></td></tr></tfoot>

		  <tbody>
		  <?php
		$k = 0;
		for ($i = 0, $n = count($rows);$i < $n;$i++) {
			$row = &$rows[$i];
			$task = $row->published ? 'unpublish' : 'publish';
			$img = $row->published ? 'publish_g.png' : 'publish_x.png';
			$alt = $row->published ? JText::_('COM_HANDOUT_PUBLISHED') : JText::_('COM_HANDOUT_UNPUBLISH') ;

			$file = new HANDOUT_File($row->docfilename, $_HANDOUT->getCfg('handoutpath'));

			?><tr class="row<?php echo $k;?>">
				<td width="15%">
					<a style="cursor: pointer;" href="#" onclick="window.parent.MM_selectElement('<?php echo $row->id; ?>', '<?php echo str_replace(array("'", "\""), array("\\'", ""),$row->docname); ?>', '<?php echo JRequest::getVar('object'); ?>');">
					<?php echo $row->docname;?></a>
				</td>
				<td>
					<?php if ($file->exists()) {?>
						<?php echo HANDOUT_Utils::urlSnippet($row->docfilename);?>
			   		<?php
					} else {
						echo JText::_('COM_HANDOUT_FILE_MISSING');
					}
			?>
				</td>
				<td width="15%"><?php echo $row->treename ?></td>


			   	<td width="5%" align="center">
			   		<img src="images/<?php echo $row->published ? 'tick' : 'publish_x'; ?>.png" border=0 alt="<?php echo $row->published ? 'unpublish' : 'publish'; ?>" />
			   	</td>

			</tr><?php
			$k = 1 - $k;
		}
		?>
		</tbody>

	  </table>


	  <input type="hidden" name="option" value="com_handout" />
	  <input type="hidden" name="section" value="documents" />
	  <input type="hidden" name="task" value="element" />
	  <input type="hidden" name="tmpl" value="component" />
	  <input type="hidden" name="object" value="<?php echo JRequest::getString('object'); ?>" />
	  <input type="hidden" name="boxchecked" value="0" />
	  <?php echo HANDOUT_token::render();?>
	  </form>

   	  <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
	}

	function editDocument(&$row, &$lists, $last, $created, &$params)
	{

		$tabs = new JPaneTabs(1);
		JFilterOutput::objectHTMLSafe($row);

		JHTML::_('behavior.calendar');


		?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="JavaScript" src="<?php echo JURI::root();?>/includes/js/overlib_mini.js" type="text/javascript"></script>
		<script language="JavaScript" type="text/javascript">
			<!--
			function submitbutton(pressbutton) {
			  var form = document.adminForm;
			  if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			  }
			  // do field validation
			<?php HandoutHTML::docEditFieldsJS();/* Include all edits at once */?>
			if ( $msg != "" ){
					$msghdr = "<?php echo JText::_('COM_HANDOUT_ENTRY_ERRORS');?>";
					$msghdr += '\n=================================';
					alert( $msghdr+$msg+'\n' );
			}else {
			<?php
				jimport( 'joomla.html.editor' );
				$editor =& JFactory::getEditor();
				echo $editor->save( 'docdescription' );
			?>
				submitform( pressbutton );
				}
			}
			//--> end submitbutton
		</script>

		<style>
			select option.label { background-color: #EEE; border: 1px solid #DDD; color : #333; }
		</style>

		<?php
		$tmp = ($row->id ? JText::_('COM_HANDOUT_EDIT') : JText::_('COM_HANDOUT_ADD')).' '.JText::_('COM_HANDOUT_DOC');
		HandoutHTML::adminHeading( $tmp, 'documents' )
		?>

		<form action="index.php" method="post" name="adminForm" class="adminform" id="handout_formedit">
			<table class="hadmin adminform">
				<tr>
					<th colspan="3"><?php echo JText::_('COM_HANDOUT_TITLE_DOCINFORMATION') ?></th>
				</tr>

				<?php HTML_HandoutDocuments::_showTabBasic($row, $lists, $last, $created);?>

				<tr>
					<td colspan="2">
						<?php
						echo $tabs->startPane("content-pane");
						echo $tabs->startPanel(JText::_('COM_HANDOUT_DOC'), "document-page");

						HTML_HandoutDocuments::_showTabDocument($row, $lists, $last, $created);

						echo $tabs->endPanel();
						echo $tabs->startPanel(JText::_('COM_HANDOUT_TAB_PERMISSIONS'), "ownership-page");

						HTML_HandoutDocuments::_showTabPermissions($row, $lists, $last, $created);

						echo $tabs->endPanel();
						echo $tabs->startPanel(JText::_('COM_HANDOUT_TAB_AGREEMENT'), "license-page");

						HTML_HandoutDocuments::_showTabLicense($row, $lists, $last, $created);

						if(isset($params)) :
						echo $tabs->endPanel();
						echo $tabs->startPanel(JText::_('COM_HANDOUT_TAB_DETAILS'), "details-page");

						HTML_HandoutDocuments::_showTabDetails($row, $lists, $last, $created, $params);
						endif;

						echo $tabs->endPanel();
						echo $tabs->startPanel(JText::_('COM_HANDOUT_TAB_META'), "meta-page");

						HTML_HandoutDocuments::_showTabMetadata($row, $lists, $last, $created);

						echo $tabs->endPanel();
						echo $tabs->startPanel(JText::_('COM_HANDOUT_TAB_INTEGRATION'), "integration-page");

						HTML_HandoutDocuments::_showTabIntegration($row, $lists, $last, $created);
						echo $tabs->endPanel();
						echo $tabs->endPane();

						?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="original_docfilename" value="<?php echo $lists['original_docfilename'];?>" />
			<input type="hidden" name="docsubmittedby" value="<?php echo $row->docsubmittedby;?>" />
			<input type="hidden" name="id" value="<?php echo $row->id;?>" />
			<input type="hidden" name="option" value="com_handout" />
			<input type="hidden" name="section" value="documents" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="doccounter" value="<?php echo $row->doccounter;?>" />
			<input type="hidden" name="doclastupdateon" value="<?php echo date('Y-m-d H:i:s') ?>" />
			<?php echo HANDOUT_token::render();?>
		</form>
	   <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
	}

	function _showTabBasic(&$row, &$lists, &$last, &$created)
	{
		?>

		<tr>
			<td width="250" align="right"><?php echo JText::_('COM_HANDOUT_DOCUMENT_NAME_LABEL');?></td>
			<td colspan="2">
				<input class="inputbox" type="text" name="docname" size="50" maxlength="100" value="<?php echo $row->docname ?>" />
			</td>
		</tr>

		<tr>
			<td align="right"><?php echo JText::_('COM_HANDOUT_CAT_LABEL');?></td>
			<td><?php echo $lists['catid'];?></td>
		</tr>

		<tr>
			<td valign="top" align="right"><?php echo JText::_('COM_HANDOUT_PUBLISHED_LABEL'); ?></td>
			<td>
			<?php echo $lists['published'];
			// echo HANDOUT_Utils::mosToolTip(JText::_('COM_HANDOUT_PUBLISHED'), JText::_('COM_HANDOUT_PUBLISHED'));
			?>
			</td>
		</tr>

		<tr>
			<td valign="top"><?php echo JText::_('COM_HANDOUT_DESCRIPTION_LABEL');?></td>
			<td colspan="2">
			<?php
			jimport( 'joomla.html.editor' );
			$editor =& JFactory::getEditor();
			echo $editor->display('docdescription', $row->docdescription , '500', '200', '50', '5') ;
			?>
			</td>
		</tr>

		<?php
	}

	function _showTabDocument(&$row, &$lists, &$last, &$created)
	{
		?>
		<table class="adminform">
			<tr>
				<td>
					<?php echo JText::_('COM_HANDOUT_THUMBNAIL');?>
				</td>
				<td>
					<?php echo $lists['image'];?>
				</td>
				<td rowspan="4" width="50%">
					<script language="javascript" type="text/javascript">
					<!--
					if (document.forms[0].docthumbnail.options.value){
						jsimg='../images/stories/' + getSelectedValue( 'adminForm', 'docthumbnail' );
					} else {
						jsimg='../images/M_images/blank.png';
					}
						document.write('<img src=' + jsimg + ' name="imagelib" width="80" height="80" border="2" alt="Preview" />');
					//-->
				</script>
				</td>
			</tr>
			<tr>
				<td align="right"><?php echo JText::_('COM_HANDOUT_FILE_LABEL');?></td>
				<td><?php echo $lists['docfilename'];?></td>
			</tr>
			<tr>
				<td width="20%" align="right"><?php echo JText::_('COM_HANDOUT_PUBLISH_DATE_LABEL');?></td>
				<td>
					<?php echo JHTML::_('calendar', $row->docdate_published, 'docdate_published', 'docdate_published', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); ?>
				</td>
			</tr>
			<tr>
				<td width="20%" align="right"><?php echo JText::_('COM_HANDOUT_FILEVERSION_LABEL');?></td>
				<td>
					<input type="text" value="<?php echo $row->docversion; ?>" maxlength="20" size="8" class="inputbox" id="docversion" name="docversion">
				</td>
			</tr>
			<tr>
				<td width="20%" align="right"><?php echo JText::_('COM_HANDOUT_FILELANGUAGE_LABEL');?></td>
				<td>
					<select size="1" class="inputbox" id="doclanguage" name="doclanguage">
						<option selected="selected" value="">Select Language</option>
						<?php
							foreach ($lists['languages'] as $lang) {
								$sel = $row->doclanguage == $lang['code'] ? ' selected="selected"' : '';
								echo '<option value="'.$lang['code'].'" '.$sel.'>'.$lang['name'].'</option>';
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo JText::_('COM_HANDOUT_DOCURL_LABEL'); ?></td>
				<td>
					<input class="inputbox" type="text" name="document_url" size="50" maxlength="200" value="<?php echo htmlspecialchars($lists['document_url'], ENT_QUOTES); ?>" />
				
					<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_DOCURL_LABEL');?>::<?php echo JText::_('COM_HANDOUT_DOCURL_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
				</td>
			</tr>
			<tr>
				<td width="250" valign="top"><?php echo JText::_('COM_HANDOUT_INFOURL_LABEL');?>
					<!--<i>(<?php echo JText::_('COM_HANDOUT_MAKE_SURE');?>)</i>-->
				</td>
				<td>
					<input class="inputbox" type="text" name="docurl" size="50" maxlength="200" value="<?php echo $row->docurl;/*htmlspecialchars($row->docurl, ENT_QUOTES);*/?>" />
					<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_INFOURL_LABEL');?>::<?php echo JText::_('COM_HANDOUT_INFOURL_DESC');?>">
								<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
				
				</td>
			</tr>
			
			<tr>
								<td><label for="hform-filename"><?php echo JText::_('DOWNLOAD_LIMIT');?></label><br />
								</td><td><input class="inputbox" type="text" name="download_limit" size="15" maxlength="200" value="<?php echo $row->download_limit; ?>" />
                                 
								<span class="hasTip" title="<?php echo JText::_('DOWNLOAD_LIMIT');?>::<?php echo JText::_('DOWNLOAD_LIMIT_TOOLTIP');?>">
								<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
                                </td>
		                </tr>
						<tr>
							<td><?php echo JText::_('ALLOW_SINGLE_DOWNLOAD');?></td><td><input type="checkbox" <?php if($row->allow_single_download==1)echo 'checked="checked"';?> name="allow_single_download"  value="1"/> 
	
								<span class="hasTip" title="<?php echo JText::_('ALLOW_SINGLE_DOWNLOAD');?>::<?php echo JText::_('ALLOW_SINGLE_DOWNLOAD_TOOLTIP');?>">
								<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
							</td>
						</tr>
		</table>
		<?php
	}

	function _showTabPermissions(&$row, &$lists, &$last, &$created)
	{
   		?>
		<table class="adminform">

		<tr>
			<td width="250" align="right"><?php echo JText::_('COM_HANDOUT_OWNER_LABEL');?></td>
			<td>
			<?php
			echo $lists['viewer'];
			echo HANDOUT_Utils::mosToolTip(JText::_('COM_HANDOUT_OWNER_DESC') ,  JText::_('COM_HANDOUT_OWNER_LABEL'));
			?>
			</td>
		</tr>
		<tr>
			<td valign="top" align="right"><?php echo JText::_('COM_HANDOUT_MAINTAINER_LABEL');?></td>
			<td>
			<?php
			echo $lists['maintainer'];
			echo HANDOUT_Utils::mosToolTip(JText::_('COM_HANDOUT_MANT_DESC'),  JText::_('COM_HANDOUT_MAINTAINER_LABEL'));
			?>
			</td>
		</tr>
		<tr>
			<td valign="top" align="right"><?php echo JText::_('COM_HANDOUT_CREATED_BY_LABEL');?></td>
			<td><strong><?php echo $created[0]->name;?></strong> on
				<?php echo HandoutFactory::getFormatDate($row->docdate_published) ?>
			</td>
		</tr>
		<tr>
			<td valign="top" align="right"><?php echo JText::_('COM_HANDOUT_UPDATED_BY_LABEL');?></td>
			<td><strong><?php echo $last[0]->name;?></strong>
			<?php
			if ($row->doclastupdateon) {
				echo " on " . HandoutFactory::getFormatDate($row->doclastupdateon);
			}
			?>

			</td>
		</tr>
		</table>
		<?php
	}

	function _showTabLicense(&$row, &$lists, &$last, &$created)
	{
   		?>
		<table class="adminform">

		<tr>
			<td width="250" ><?php echo JText::_('COM_HANDOUT_SELECT_AGREEMENT_LABEL');?></td>
			<td>
			<?php
			echo $lists['licenses'];
			echo HANDOUT_Utils::mosToolTip(JText::_('COM_HANDOUT_AGREEMENT_DESC') ,  JText::_('COM_HANDOUT_SELECT_AGREEMENT_LABEL'));
			?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_HANDOUT_DISPLAY_AGREEMENT_LABEL');?></td>
			<td>
			<?php
			echo $lists['licenses_display'];
			echo HANDOUT_Utils::mosToolTip(JText::_('COM_HANDOUT_DISPLAY_AGREEMENT_DESC') ,  JText::_('COM_HANDOUT_DISPLAY_AGREEMENT_LABEL'));
			?>
			</td>
		</tr>
		</table>
		<?php
	}

	function _showTabDetails(&$row, &$lists, &$last, &$created, &$params)
	{
		?>
		<table class="adminform" >
			<tr>
				<td>
					<?php echo $params->render();?>
				</td>
			</tr>
		</table>
		<?php
	}

	  function _showTabMetadata(&$row, &$lists, &$last, &$created)
	{
		?>
		<table class="adminform" >
			<tr>
					<td>
						<?php echo JText::_('COM_HANDOUT_METADESCRIPTION_LABEL');?>
					</td>
					<td><textarea rows="3" cols="80" wrap="virtual" name="doc_meta_description"><?php echo $row->doc_meta_description;?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('COM_HANDOUT_METAKEYWORDS_LABEL');?>
					</td>
					<td><textarea rows="3" cols="80" wrap="virtual" name="doc_meta_keywords"><?php echo $row->doc_meta_keywords;?></textarea>
					</td>
				</tr>
		</table>
		<?php
	}


	function _showTabIntegration(&$row, &$lists, &$last, &$created)
	{
   		?>
		<table class="adminform">
		<tr>
			<td width="250" ><?php echo JText::_('COM_HANDOUT_KUNENA_DISCUSS_LABEL');?></td>
			<td>
			<input class="inputbox" type="text" name="kunena_discuss_id" size="10" maxlength="10" value="<?php echo $row->kunena_discuss_id;?>" />
			<?php
			echo HANDOUT_Utils::mosToolTip(JText::_('COM_HANDOUT_KUNENA_DISCUSS_DESC') ,  JText::_('COM_HANDOUT_KUNENA_DISCUSS_LABEL'));
			?>
			</td>
		</tr>
		<tr>
			<td width="250" ><?php echo JText::_('COM_HANDOUT_MTREE_LABEL');?></td>
			<td>
			<input class="inputbox" type="text" name="mtree_id" size="10" maxlength="10" value="<?php echo $row->mtree_id;?>" />
			<?php
			echo HANDOUT_Utils::mosToolTip(JText::_('COM_HANDOUT_MTREE_DESC') ,  JText::_('COM_HANDOUT_MTREE_LABEL'));
			?>
			</td>
		</tr>

		</table>
		<?php
	}
		function moveDocumentForm($cid, &$lists, &$items)
	{
		?>
		<?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_MOVETOCAT'), 'categories' )?>

		<form action="index.php" method="post" name="adminForm" class="adminform" id="handout_moveform">
			<table class="adminform">
				<tr>
					<td align="left" valign="middle" width="10%">
						<strong><?php echo JText::_('COM_HANDOUT_MOVETOCAT');?></strong>
						<?php echo $lists['categories'] ?>
					</td>
					<td align="left" valign="top" width="20%">
						<strong><?php echo JText::_('COM_HANDOUT_DOCSMOVED');?></strong>
						<?php
						echo "<ol>";
						foreach ($items as $item) {
							echo "<li>" . $item->docname . "</li>";
						}
						echo "</ol>";?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_handout" />
			<input type="hidden" name="section" value="documents" />
			<input type="hidden" name="task" value="move_process" />
			<input type="hidden" name="boxchecked" value="1" />
			<?php
			foreach ($cid as $id) {
				echo "\n <input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
			}
			?>
			<?php echo HANDOUT_token::render();?>
		</form>
		<?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
	}

	function copyDocumentForm($cid, &$lists, &$items)
	{

		?>
		<?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_COPYTOCAT'), 'categories' )?>

		<form action="index.php" method="post" name="adminForm" class="adminform" id="handout_moveform">
			<table class="adminform">
			<tr>
				<td align="left" valign="middle" width="10%">
					<strong><?php echo JText::_('COM_HANDOUT_COPYTOCAT');?></strong>
					<?php echo $lists['categories'] ?>
				</td>
				<td align="left" valign="top" width="20%">
					<strong><?php echo JText::_('COM_HANDOUT_DOCSCOPIED');?></strong>
					<?php
					echo "<ol>";
					foreach ($items as $item) {
						echo "<li>" . $item->docname . "</li>";
					}
					echo "</ol>";?>
				</td>
			</tr>
			</table>
			<input type="hidden" name="option" value="com_handout" />
			<input type="hidden" name="section" value="documents" />
			<input type="hidden" name="task" value="copy_process" />
			<input type="hidden" name="boxchecked" value="1" />
			<?php
			foreach ($cid as $id) {
				echo "\n <input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
			}
			?>
			<?php echo HANDOUT_token::render();?>
		</form>
		<?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
	}
}
