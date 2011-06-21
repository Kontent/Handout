<?php

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

if(!count($this->files)) {
	echo JText::_("All files in PATH are already present in the Handout database. In the configuration, set 'Orphans' to 'Show All Files' to add multiple entries for one file.");
	return;
}

$options= array();
foreach ($this->files as $file) {
    $options[]	=JHTML::_('select.option', htmlspecialchars($file));
}
$fileselectlist = JHTML::_('select.genericlist',$options, 'files[]', 'size="15" class="inputbox" multiple="true"', 'value', 'text', $options );
$catselectlist	= JHTML::_('populate.selects.tree', $this->categories, 0, array(), 'catid', 'class="inputbox" size="15"', 'value', 'text', '');

?>

<form action="index.php?option=com_populate&view=documents" method="post" name="adminForm" id="adminForm" class="adminForm">
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="task" value="" />
<fieldset class="adminform">
	<legend>Documents</legend>
	<table class="admintable">
	
		<tr>
			<td class="key">
				<label class="hasTip" title="<?php echo 'Files::Select the files you want to add. Use Ctrl and Shift to select multiple files.<br />You can add more files by uploading them to <br />' ?>">
					Files
				</label>
			</td>
			<td valign="top">
				<?php echo $fileselectlist;?>
			</td>
		</tr>
		
		<tr>
			<td class="key">
				<label class="hasTip" title="<?php echo 'Categories::Select the category where you want to add the files.' ?>">
					Categories
				</label><br />
			    <a href="index.php?option=com_handout&amp;section=categories"><small>( Edit Categories in Handout )</small></a>
			</td>
			<td valign="top">
				<?php echo $catselectlist;?>
			</td>
		</tr>
		<tr>
			<td class="key">Path to files</td>
			<td valign="top"><strong><?php echo $this->params->handoutpath;?></strong></td>
		</tr>
	</table>
</fieldset>
</form>

    