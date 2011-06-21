<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: files.html.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_HTML_FILES')) {
    return;
} else {
    define('_HANDOUT_HTML_FILES', 1);
}

class HTML_HandoutFiles
{
	
	
    function showFiles($rows, $lists, $search, $pageNav)
    {
    	JHTML::_('behavior.tooltip');
    	
        ?>

        <form action="index.php" method="post" name="adminForm">

        <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_TITLE_FILES'), 'files' )?>

        <div class="hfilter">
            <?php echo JText::_('COM_HANDOUT_FILTER');?>
            <input class="text_area" type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
            <?php echo $lists['filter'];?>
        </div>

        <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
          <thead>
          <tr>
            <th width="2%" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows);?>);" /></th>
            <th width="15%" align="left"><?php echo JText::_('COM_HANDOUT_FILENAME');?></th>
            <th width="15%" align="center"><?php echo JText::_('COM_HANDOUT_CREATION_DATE');?></th>
            <th width="15%"><?php echo JText::_('COM_HANDOUT_EXT');?></th>
            <th width="15%"><?php echo JText::_('COM_HANDOUT_MIME');?></th>
            <th width="5%"><?php echo JText::_('COM_HANDOUT_SIZE');?></th>
            <th width="5%"># <?php echo JText::_('COM_HANDOUT_LINKS');?></th>
            <th width="5%" align="center"><?php echo JText::_('COM_HANDOUT_UPDATE');?></th>
          </tr>
          </thead>
          <tfoot><tr><td colspan="11"><?php echo $pageNav->getListFooter();?></td></tr></tfoot>
          <tbody>
          <?php
        $k = 0;
        for ($i = 0, $n = count($rows);$i < $n;$i++) {
            $row = &$rows[$i];
          	?>
        		<tr class="<?php echo "row$k";?>">
        			<td width="20">
						<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->name?>" onclick="isChecked(this.checked);" />
            		</td>
            	<td>
                    <a onclick="return listItemTask('cb<?php echo $i;?>','new')" href="#new" class="hasTip" title="<?php echo JText::_('COM_HANDOUT_CREATE_DOCUMENT_LABEL');?>::<?php echo JText::_('COM_HANDOUT_CREATE_DOCUMENT_DESC');?>">
                        <?php echo $row->name;?>
                    </a>
                </td>
            	<td align="center"><?php echo $row->getDate();?></td>
            	<td align="center"><?php echo $row->ext;?></td>
            	<td align="center"><?php echo $row->mime;?></td>
            	<td align="center"><?php echo $row->getSize();?></td>
            	<td align="center"><?php echo $row->links;?></td>
            	<td align="center">
                	<a href="index.php?option=com_handout&section=files&task=update&old_filename=<?php echo $row->name;?>"  class="hasTip" title="<?php echo JText::_('COM_HANDOUT_UPLOAD_NEW_DOCUMENT_LABEL');?>::<?php echo JText::_('COM_HANDOUT_UPLOAD_NEW_DOCUMENT_DESC');?>"><img src="<?php echo JURI::root();?>/administrator/components/com_handout/images/icon-16-upload.png" alt="<?php echo JText::_('COM_HANDOUT_UPDATE');?>" border="0" /></a>
            	</td>
			<?php
            $k = 1 - $k;
        }
        ?>
        </tbody>
      </table>



      <input type="hidden" name="option" value="com_handout" />
      <input type="hidden" name="section" value="files" />
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <?php echo HANDOUT_token::render();?>
      </form>

    <?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");
    }

    function uploadWizard(&$lists)
    {
        
        ?>

       <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_UPLOADWIZARD'), 'files' )?>

       <form action="index.php?option=com_handout&section=files&task=upload&step=2" method="post">
       <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
        <tr>
          <td colspan="3" align="center"><b><?php echo JText::_('COM_HANDOUT_UPLOADMETHOD');?></b></td>
        </tr>
        <tr>
          <td width="38%" rowspan="4" align="center">
	        <div align="right" >
	         <img src="<?php echo JURI::root();?>/administrator/components/com_handout/images/icon-48-upload.png">
            </div>
		  </td>
          <td width="4%" align="center"> <div align="right">
              <?php echo $lists['methods'];?>
            </div>
		  </td>
		  <td width="60%">&nbsp;</td>
        </tr>
        <tr>
          <td><div align="center">
              <input type="submit" name="Submit" value="<?php echo JText::_('COM_HANDOUT_NEXT');?>>>>">
            </div></td>
          <td>&nbsp;</td>
        </tr>
      </table>
    <?php echo HANDOUT_token::render();?>
    </form>
    <form action="index.php" method="post" name="adminForm">
        <input type="hidden" name="option" value="com_handout" />
        <input type="hidden" name="section" value="files" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
    </form>
	<?php
    }

    function uploadWizard_http($old_filename = null)
    {
        
        ?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="Javascript" src="<?php echo JURI::root();?>/includes/js/overlib_mini.js"></script>
		<script language="Javascript" src="<?php echo JURI::root();?>/administrator/components/com_handout/includes/js/handout.js"></script>

		<form action="index.php?option=com_handout&section=files&task=upload&step=3&method=http&old_filename=<?php echo $old_filename;?>" method="post" enctype="multipart/form-data" onSubmit="MM_showHideLayers('Layer1','','show')" name="fm_upload">

		<style type="text/css">
			<!--
			.style1 {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-weight: bold;
			}

			.style2 {color: #FF0000}
			.style3 {color: #FFFFFF}
			//-->
		</style>

		<div id="Layer1" style="position:absolute; margin-left: auto; margin-right: auto;  width:200px; height:130px; z-index:150; visibility: hidden; left: 14px; top: 11px; background-color: #99989D; layer-background-color: #FF0000; border: 3px solid #F19518;">

			<div align="center" class="style1">
				<p align="center" class="style2"><br />
					<span class="style3"><?php echo JText::_('COM_HANDOUT_ISUPLOADING');?></span>
				</p>

				<p align="center" class="style2"><img src="<?php echo JURI::root();?>/administrator/components/com_handout/images/uploader.gif" ></p>
				<p align="center" class="style3"><?php echo JText::_('COM_HANDOUT_PLEASEWAIT');?><br /></p>
			</div>
		</div>

        <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_UPLOADDISK'), 'files' )?>

        <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
        <tr>
          <td colspan="3" align="center"><b><?php echo JText::_('COM_HANDOUT_FILETOUPLOAD');?></b></td>
        </tr>
        <tr >
            <td width="40%" align="center" rowspan="6">
			<div align="right"><img src="<?php echo JURI::root();?>/administrator/components/com_handout/images/icon-48-upload.png">
            </td>
	    	<td nowrap ><?php echo JText::_('COM_HANDOUT_FILETOUPLOAD');?>:</td>
            <td  align="left" width="80%">
            <div align="left">
              <input name="upload" type="file" id="upload" size="35">
	    </div>
	    </td>
	 </tr>
         <?php if ($old_filename == '1') {?>
	 <tr>
	   <td><?php echo JText::_('COM_HANDOUT_BATCHMODE');?>:</td>
	   <td>
            <div align="left">
                <input name="batch" type="checkbox" id="batch" value="1"
			onClick="if( ! document.fm_upload.localfile.disabled ){document.fm_upload.localfile.value='';}
				 document.fm_upload.localfile.disabled=!document.fm_upload.localfile.disabled;
				 return(true);">                
                 <span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_CFG_HANDOUT_DESC');?>::<?php echo JText::_('COM_HANDOUT_BATCHMODE_DESC');?>">
						<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
						
            </div>
	  </td>
        </tr>
        <?php } ?>
        <tr>
	    <td align="left">
                <input type="button" name="Submit2" value="&lt;&lt;&lt;" onclick="window.history.back()">
	    </td>
            <td align="center"><div align="left">
                <input type="submit" name="Submit" value="<?php echo JText::_('COM_HANDOUT_SUBMIT') ?>">
            </td>
        </tr>
      </table>
    <?php echo HANDOUT_token::render();?>
    </form>

    <form action="index.php" method="post" name="adminForm">
        <input type="hidden" name="option" value="com_handout" />
        <input type="hidden" name="section" value="files" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
    </form>
    <?php
    }

    function uploadWizard_transfer()
    {
        
        ?>

		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
        <script language="Javascript" src="<?php echo JURI::root();?>/administrator/components/com_handout/includes/js/handout.js"></script>
		<script language="Javascript" src="<?php echo JURI::root();?>/includes/js/overlib_mini.js"></script>
    	<style type="text/css">
		.style1 {
    		font-family: Verdana, Arial, Helvetica, sans-serif;
    		font-weight: bold;
		}
		.style2 {color: #FF0000}
		.style3 {color: #FFFFFF}
		</style>

		<div id="Layer1" style="position:absolute; margin-left: auto; margin-right: auto;  width:200px; height:130px; z-index:1; visibility: hidden; left: 14px; top: 11px; background-color: #99989D; layer-background-color: #FF0000; border: 3px solid #F19518;">
  		<div align="center" class="style1">
    		<p align="center" class="style2"><br />
    		<span class="style3"><?php echo JText::_('COM_HANDOUT_HANDOUTISTRANSF');?></span></p>
    		<p align="center" class="style2"><img src="<?php echo JURI::root();?>/administrator/components/com_handout/images/uploader.gif" ></p>
    		<p align="center" class="style3"><?php echo JText::_('COM_HANDOUT_PLEASEWAIT');?><br />
    	</p>
  		</div>
		</div>
    	<form action="index.php?option=com_handout&section=files&task=upload&step=3&method=transfer" method="post" onSubmit="MM_showHideLayers('Layer1','','show')">
        <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_TRANSFERFROMWEB'), 'files' )?>
        <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
        <tr>
            <td width="40%" align="center"> <img src="<?php echo JURI::root();?>/administrator/components/com_handout/images/icon-48-upload.png">
            </td>
	    <td nowrap><?php echo JText::_('COM_HANDOUT_REMOTEURL');?>:</td>
            <td align="left">
            <div align="left">
                <input name="url" type="text" id="url" value="http://">
            </div></td>
	    <td align="left">
	    
	    <span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_REMOTEURL');?>::<?php echo JText::_('COM_HANDOUT_REMOTEURL_DESC');?>">
						<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
						</td>
	</tr>
	<tr><td colspan="4">&nbsp;</td></tr>
	<tr>
	    <td>&nbsp;</td>
            <td><?php echo JText::_('COM_HANDOUT_LOCALNAME');?>:</td>
            <td align="left">
            <div align="left">
                <input name="localfile" type="text" id="url" value="">
            </div></td>
	    <td align="left" width="40%">
	    
	    <span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_LOCALNAME');?>::<?php echo JText::_('COM_HANDOUT_LOCALNAME_DESC');?>">
						<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
						
						</td>
        </tr>
        <tr>
            <td colspan="2" align="center">&nbsp;</td>
            <td align="center"><div align="left">
                <input type="button" name="Submit2" value="&lt;&lt;&lt;" onclick="window.history.back()">
                <input type="submit" name="Submit" value="<?php echo JText::_('COM_HANDOUT_SUBMIT');?>">
            </td>
        </tr>
      </table>
    <?php echo HANDOUT_token::render();?>
    </form>

    <form action="index.php" method="post" name="adminForm">
        <input type="hidden" name="option" value="com_handout" />
        <input type="hidden" name="section" value="files" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
    </form>
    <?php
    }

    function uploadWizard_sucess(&$file, $batch = 0, $old_filename = null, $show_completion = 1)
    {
        
        $mainframe = &JFactory::getApplication();

        if ($old_filename <> '1') {
            $mainframe->redirect("index.php?option=com_handout&section=files", "&quot;" . $old_filename . "&quot; - " . JText::_('COM_HANDOUT_DOCUPDATED'));
        }
        ?>

        <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_UPLOADWIZARD'), 'files' )?>


        <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
  		<?php if ($show_completion) {
            /* backwards compatible */?>
        <tr>
          <td width="38%" align="center">
          	<div align="right">
          		<img src="<?php echo JURI::root();?>/administrator/components/com_hadnout/images/icon-48-upload.png" />
          	</div>
          </td>
          <td colspan="2"><div align="left">'<?php echo $file->name?>' -<?php echo JText::_('COM_HANDOUT_FILEUPLOADED');?></div></td>
        </tr>
	<tr>
	  <td colspan=2><div align="center"><hr /></td>
	<tr>
	<?php } ?>

	<!-- Give them a nice sub menu -->
  	<?php
        if (!$batch && $old_filename == '1') {
            /* Can't create docs from a batch or existing file */?>
    	<tr>
    		<td>
    		<div align="right">
    			<a href="index.php?option=com_handout&section=documents&task=new&uploaded_file=<?php echo $file->name;?>">
    			<img src="<?php echo JURI::root();?>/administrator/images/edit_f2.png" border="0">
    			</a>
    		</div>
    		</td>

    		<td>
    		<div align="left"><?php echo JText::_('COM_HANDOUT_MAKENEWENTRY');?></div>
    		</td>
    	</tr>
    	<?php } ?>

    <tr>

	<td>
		<div align="right">
			<a href="index.php?option=com_handout&section=files&task=upload">
			<img src="<?php echo JURI::root();?>/administrator/images/upload_f2.png" border="0">
			</a>
		</div>
	</td>
	<td><div align="left"><?php echo JText::_('COM_HANDOUT_UPLOADMORE');?></div></td>
	</tr>
	<tr>
		<td>
			<div align="right">
				<a href="index.php?option=com_handout&section=files">
					<img src="<?php echo JURI::root();?>/administrator/images/next_f2.png" border="0">
				</a>
			</div>
		</td>
		<td>
			<div align="left"><?php echo JText::_('COM_HANDOUT_DISPLAYFILES');?></div>
		</td>
	</tr>
	</table>

	<form action="index.php" method="post" name="adminForm">
        <input type="hidden" name="option" value="com_handout" />
        <input type="hidden" name="section" value="files" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
    </form>
	<?php
    }
}

