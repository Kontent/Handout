<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
?>

<table class="adminlist">
    <thead>
		<tr>
        	<th class="title"><?php echo JText::_('Publish'); ?></th>
        	<th class="title"><?php echo JText::_('Edit Document'); ?></th>
        	<th class="title"><?php echo JText::_('Last Edited'); ?></th>
		</tr>
	</thead>
	<tbody>
    	<?php if (!count($docs)): ?>
    		<tr><td style="text-align:center !important;" colspan="3"><?php echo JText::_('All documents are published'); ?></td></tr>
    	<?php else: ?>	
        <?php foreach ($docs as $doc): ?>
        	<tr>
                <td width="5%" style="text-align:center">
                    <a href="index.php?option=com_handout&amp;section=documents&amp;task=publish&cid[]=<?php echo $doc->id?>&amp;<?php echo HANDOUT_Token::get();?>=1&amp;redirect=index2.php%3Foption%3Dcom_handout">
                    	<img src="images/publish_r.png" border=0 alt="publish" />
                    </a>
                </td>
                <td><a href="index.php?option=com_handout&amp;section=documents&task=edit&amp;cid[]=<?php echo $doc->id ?>"><?php echo $doc->docname;?></a></td>
                <td align="right"><?php echo $doc->doclastupdateon;?></td>
        	</tr>
        	<?php endforeach;?>
    	<?php endif; ?>	
    </tbody>
</table>