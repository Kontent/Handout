<?php

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
jimport('joomla.html.pane');
$tabs = JPane::getInstance('tabs', array('useCookies' => true));
?>
<?/*
<script language="JavaScript" type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
</script>
		*/?>
<div style="width:700px; ">
<form action="index.php?option=com_populate&view=config" method="post" name="adminForm" id="adminForm" class="adminForm">
<input type="hidden" name="id" value="<?php echo $this->config->id; ?>" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>

<fieldset class="adminform">
	<legend>Document information</legend>
	<table class="admintable">
	<tr>
		<td class="key">Approved</td>
		<td><?php echo JHTML::_('select.booleanlist', 'approved', '', $this->config->approved ); ?></td>
	</tr>
	<tr>
		<td class="key">Published</td>
		<td><?php echo JHTML::_('select.booleanlist', 'published', '', $this->config->published ); ?></td>
	</tr>
	<tr>
		<td class="key">Description</td>
		<td>
			<?php
					jimport( 'joomla.html.editor' );
					$editor =& JFactory::getEditor();
					$editor->display('docdescription',  $this->config->docdescription , '500', '200', '50', '5') ;
		    ?>
		</td>
	</tr>
	</table>
</fieldset>

<?php echo $tabs->startPane('configtab');
echo $tabs->startPanel('Document', 'configtab');	?>

<fieldset class="adminform">
	<legend>Document information</legend>
	<table class="admintable">
	<tr>
	<td class="key">
		Thumbnail
	</td>
	<td><?php echo JHTML::_('list.images', 'docthumbnail', $this->config->docthumbnail);  ?></td>
	</tr>
	<tr>
		<td class="key">Homepage URL</td>
		<td><input type="text" size="50" maxsize="255" name="docurl" value="<?php echo $this->config->docurl; ?>" /></td>
	</tr>
	</table>
</fieldset>
<?php echo $tabs->endPanel();



echo $tabs->startPanel('Permissions', 'configtab');
?>
<fieldset class="adminform">
	<legend>Document permissions</legend>
	<table class="admintable">
	<tr>
		<td class="key">
			<label class="hasTip" title="<?php echo 'Owner::The default owner for new documents' ?>">
				Owner/Viewer
			</label>
		</td>
		<td><?php echo JHTML::_('populate.selects.owners', $this->config->docowner, 'docowner')?></td>
	</tr>
	<tr>
		<td class="key">
			<label class="hasTip" title="<?php echo 'Maintainer::The default maintainer for new documents.' ?>">
				Maintainer
			</label>
		</td>
		<td><?php echo JHTML::_('populate.selects.owners', $this->config->docmaintainedby, 'docmaintainedby')?></td>
	</tr>

	</table>
</fieldset>
<?php echo $tabs->endPanel();
echo $tabs->startPanel('License', 'configtab');
?>

<fieldset class="adminform">
	<legend>Document license</legend>
	<table class="admintable">
	<tr>
		<td class="key">License Type</td>
		<td><?php echo JHTML::_('populate.selects.licenses', $this->config->doclicense_id)?></td>
	</tr>
	<tr>
		<td class="key">Display Agreement / License when viewing</td>
		<td><?php echo JHTML::_('select.booleanlist', 'doclicense_display', '', $this->config->doclicense_display ); ?></td>
	</tr>
	</table>
</fieldset>

<?php echo $tabs->endPanel();
echo $tabs->startPanel('Details', 'configtab');
?>
<fieldset class="adminform">
	<legend>Document details</legend>
	<table class="admintable">
	<tr>
		<td class="key">Attributes</td>
		<td><textarea rows="5" cols="50" name="attribs"><?php echo $this->config->attribs; ?></textarea></td>
	</tr>

	</table>
</fieldset>
<?php echo $tabs->endPanel();
echo $tabs->startPanel('Config', 'configtab');
?>

<fieldset class="adminform">
	<legend>Global configuration</legend>
	<table class="admintable">
	<tr>
		<td class="key">
			<label class="hasTip" title="<?php echo 'Skip Files::These files will never be added as documents. Separate using the \'|\'-symbol.' ?>">
				Skip Files
			</label>
		</td>
		<td><textarea rows="7" cols="50" name="skipfiles"><?php echo $this->config->skipfiles; ?></textarea></td>
	</tr>
	<tr>
		<td class="key">
			<label class="hasTip" title="<?php echo 'Orphans::When selecting files to add as documents, you can choose to show all files, or only orphans (files that are not in the Handout database yet). This settig will be ignored when using cron execution.' ?>">
				Orphans
			</label>
		</td>
		<td><?php echo JHTML::_('select.booleanlist','orphansonly', '', $this->config->orphansonly, 'Only Show Orphans', 'Show All Files' ); ?></td>
	</tr>
	<tr>
		<td class="key">
			<label class="hasTip" title="<?php echo 'Strip Extension::Strip the extension from a document title. eg. my_file.jpg becomes my_file.' ?>">
				Strip Extension
			</label>
		</td>
		<td><?php echo JHTML::_('select.booleanlist','stripextension', '', $this->config->stripextension ); ?></td>
	</tr>

	<tr>
		<td class="key">
			<label class="hasTip" title="<?php echo 'Clean Up Title::Clean up the document title:<ul><li>Strip underscores</li><li>Change to Title Case</li></ul>eg. my_file -> My File' ?>">
				Clean Up Title
			</label>
		</td>
		<td><?php echo JHTML::_('select.booleanlist','nicetitle', '', $this->config->nicetitle ); ?></td>
	</tr>

	<tr>
		<td class="key">
			<label class="hasTip" title="<?php echo 'Use File Time::Set document times (date published and last modified) according to the file creation- and modification time.' ?>">
				Use File Time
			</label>
		</td>
		<td><?php echo JHTML::_('select.booleanlist','usefiletime', '', $this->config->usefiletime ); ?></td>
	</tr>
	</table>
</fieldset>

<fieldset class="adminform">
	<legend>Cron configuration</legend>
	<table class="admintable">


	<tr>
		<td class="key">
			<label class="hasTip" title="<?php echo 'Cron Command::Use this command for your cronjobs.' ?>">
				Cron Command
			</label>
		</td>
		<td>
		    <?php $url = JRoute::_(JURI::root().'index.php?option=com_populate&pw='.$this->pw)?>
		    <input readonly="readonly" size="80" value="wget -q &quot;<?php echo $url?>&quot;" /><br />
		    OR <br />
		    <input readonly="readonly" size="80" value="curl --silent --compressed &quot;<?php echo $url?>&quot;" /><br />
		    OR <br />
			<input readonly="readonly" size="80" value="/usr/bin/lynx -source &quot;<?php echo $url?>&quot;" /><br />
			<br />
			To test, try this link in your browser: <br />
			<a target="_blank" href="<?php echo $url?>"><?php echo $url?></a>
		</td>
	</tr>

	<tr>
		<td class="key">
			<label class="hasTip" title="<?php echo 'Cron Category::Category to use when running Populate through cron.' ?>">
				Cron Category
			</label>
		</td>
		<td><?php echo HandoutHTML::categoryList( $this->config->catid, '' ) ?></td>
	</tr>
	</table>
</fieldset>
<?php echo $tabs->endPanel();
echo $tabs->endPane();
?>

</form>
</div>
