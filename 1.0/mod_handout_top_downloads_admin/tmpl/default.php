<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>

<table class="adminlist">
	<thead>
    	<tr>
    	    <th class="title"><?php echo JText::_('MOD_HANDOUT_TOP_MOST_DOWNLOADED_DOCUMENTS'); ?></th>
            <th class="title"><?php echo JText::_('MOD_HANDOUT_TOP_DOWNLOADS'); ?></th>
    	</tr>
	</thead>
	<tbody>
        <?php if (!count($docs)): ?>
        	<tr><td colspan="2" style="text-align:center !important;"><?php echo JText::_('MOD_HANDOUT_TOP_THERE_ARE_NO_DOCUMENTS_DOWNLOADED'); ?></td></tr>
        <?php else: ?>
        <?php foreach ($docs as $doc): ?>
        	<tr>
        	    <td><a href="index.php?option=com_handout&amp;section=documents&amp;task=edit&amp;cid[]=<?php echo $doc->id;?>"><?php echo $doc->docname;?></a>
        	    </td>
        	    <td style="text-align:center !important;"><?php echo $doc->doccounter;?></td>
        	</tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>