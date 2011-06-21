<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: cleardata.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_HTML_CLEARDATA')) {
    return;
} else {
    define('_HANDOUT_HTML_CLEARDATA', 1);
}

class HTML_HandoutClear {
    function showClearData( $rows ) {        
    	?>
        <table class="adminlist">        
          <thead>
          <tr>
            <th width="20" align="left">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows);?>);" />
            </th>
            <th width="98%" align="left">
                <?php echo JText::_('COM_HANDOUT_CLEARDATA_ITEM');?>
            </th>
          </tr>
          </thead>
          
          <tbody>
          <?php
          $k = 0;
          foreach( $rows as $i => $row ){?>
            <tr class="row<?php echo $k;?>">
                <td width="20">
                    <?php echo JHTML::_('grid.id',$i, $row->name);?>
                </td>
                <td>
                    <?php echo $row->friendlyname; ?>
                </td>
            </tr><?php
            $k = 1-$k;
          } ?>
          </tbody>
        </table>
        <input type="button" value="<?php echo JText::_('COM_HANDOUT_CLEARDATA');?>" name="Reset" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('Please make a selection from the list to Clear Data');}else{if(confirm('Are you sure?')){submitbutton('cleardata');}}" />	
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo HANDOUT_token::render();?>         
        <?php
    }
}