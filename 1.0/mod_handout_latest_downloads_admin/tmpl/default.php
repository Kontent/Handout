<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
?>

<table class="adminlist">
	<thead>
    	<tr>
    	    <th class="title"><?php echo JText::_('MOD_HANDOUT_LOGS_LOGGED_DOWNLOADS'); ?></th>
            <th class="title"><?php echo JText::_('MOD_HANDOUT_LOGS_USER'); ?></th>
            <th class="title"><?php echo JText::_('MOD_HANDOUT_LOGS_IP'); ?></th>
            <th class="title"><?php echo JText::_('MOD_HANDOUT_LOGS_DATE'); ?></th>
    	</tr>
	</thead>
	<tbody>
    	<?php if (!$_HANDOUT->getCfg('log') || !count($docs)): ?> 
    		<tr><td style="text-align:center !important;" colspan="4"><?php echo JText::_('MOD_HANDOUT_LOGS_LOGGING_IS_DISABLED_IN_THE_CONFIGURATION'); ?></td></tr>
    	<?php else: ?>	
        <?php foreach ($docs as $doc): ?>
        	<tr>
        	    <td>
                    <a href="index.php?option=com_handout&amp;section=documents&amp;task=edit&amp;cid[]=<?php echo $doc->log_docid;?>">
                    <?php echo $doc->docname;?>
                    </a>
        	    </td>
                <td align="right"><?php echo ($doc->log_user == 0) ? JText::_('MOD_HANDOUT_LOGS_GUEST') : $doc->name; ?></td>
        	    <td align="right"><a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php echo $doc->log_ip;?>" target="_blank"><?php echo $doc->log_ip;?></a></td>
        	    <td align="right"><?php echo $doc->log_datetime;?></td>
        	</tr>
        <?php endforeach;?>
        <?php endif; ?>
    </tbody>
</table>