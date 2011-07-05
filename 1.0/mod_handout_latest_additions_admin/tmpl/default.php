<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>

<table class="adminlist">
	<thead>
		<tr>
	    	<th class="title"><?php echo JText::_('MOD_HANDOUT_LATEST_MOST_RECENT_DOCUMENTS'); ?></th>
        	<th class="title"><?php echo JText::_('MOD_HANDOUT_LATEST_DATE_ADDED'); ?></th>
		</tr>
	</thead>
	<tbody>
        <?php if (!count($docs)): ?>
        	<tr><td style="text-align:center !important;" colspan="2"><?php echo JText::_('MOD_HANDOUT_LATEST_NO_DOCUMENTS'); ?></td></tr>
       	<?php else: ?>
        <?php foreach ($docs as $doc): ?>
        	<tr>
        	    <td><a href="index.php?option=com_handout&amp;section=documents&task=edit&amp;cid[]=<?php echo $doc->id ?>"><?php echo $doc->docname;?></a>
        	    <?php if ($doc->published == '0') echo '(' . JText::_('MOD_HANDOUT_LATEST_NOT_PUBLISHED') . ')'; ?>
        	    </td>
        	    <td align="right"><?php echo $doc->docdate_published;?></td>
        	</tr>
        <?php endforeach;?>
        <?php endif; ?>
    </tbody>
</table>