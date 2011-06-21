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

if (defined('_HANDOUT_HTML_CONFIG')) {
    return;
} else {
    define('_HANDOUT_HTML_CONFIG', 1);
}

include_once('handout.html.php');

class HTML_HandoutConfig
{
    function configuration(&$lists)
    {
		JHTML::_('behavior.tooltip');

        global $_HANDOUT;
        $tabs = new JPaneTabs(1);
        ?>
        <script language="javascript" src="<?php echo JURI::root();?>/includes/js/overlib_mini.js" type="text/javascript"></script>

        <?php HandoutHTML::adminHeading( JText::_('COM_HANDOUT_TITLE_CONFIGURATION'), 'config' )?>

        <div class="hfilter">
            <p class="componentheading">The configuration file is 
			 <?php echo is_writable(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_handout'.DS.'handout.config.php') ? '<span class="hactive">'.JText::_('COM_HANDOUT_WRITABLE').'</span>.' : '<span color="inactive">'.JText::_('COM_HANDOUT_UNWRITABLE').'</span>.' ?>
			</p>
        </div>

        <script language="javascript" type="text/javascript">
            function submitbutton(pressbutton) {
                var form = document.adminForm;
                if (pressbutton == 'cancel') {
                    submitform( pressbutton );
                    return;
                }
		  $msg = "";
          if (form.handoutpath.value == ""){
			$msg = "\n<?php echo JText::_('COM_HANDOUT_ERR_DOCPATH') ;?>";
		  }
		  if( isNaN( parseInt( form.perpage.value ) ) ||
			  parseInt( form.perpage.value ) < 1 ) {
			$msg += "\n<?php echo JText::_('COM_HANDOUT_ERR_PERPAGE');?>";
		  }
		  if( isNaN( parseInt( form.days_for_new.value ) ) ||
			  parseInt( form.days_for_new.value ) < 0 ) {
			$msg += "\n<?php echo JText::_('COM_HANDOUT_ERR_NEW');?>";
		  }
		  if( isNaN( parseInt( form.hot.value ) ) ||
			  parseInt( form.hot.value ) < 0 ) {
			$msg += "\n<?php echo JText::_('COM_HANDOUT_ERR_HOT');?>";
		  }
		  if( form.user_upload.value == "<?php echo COM_HANDOUT_PERMIT_NOOWNER;?>"){
			$msg += "\n<?php echo JText::_('COM_HANDOUT_ERR_UPLOAD');?>";
		  }
		  if( form.default_viewer.value == "<?php echo COM_HANDOUT_PERMIT_NOOWNER;?>" ){
			$msg += "\n<?php echo JText::_('COM_HANDOUT_ERR_DOWNLOAD');?>";
		  }
		  if( form.default_editor.value == "<?php echo COM_HANDOUT_PERMIT_NOOWNER;?>" ){
			$msg += "\n<?php echo JText::_('COM_HANDOUT_ERR_EDIT');?>";
		  }

          if ( $msg != "" ){
                $msghdr = "<?php echo JText::_('COM_HANDOUT_ENTRY_ERRORS');?>";
                $msghdr += '\n=================================';
                alert( $msghdr+$msg+'\n' );

          } else {
        	   submitform( pressbutton );
          }
        }

        /* Make sure the user can only use 0-9 and K, M, G */
        function handoutFilesize(f) {
        	var re = /[0-9KMGkmg]*/;
            f.value = f.value.match(re);
        }
        </script>

        <form action="index.php?option=com_handout&amp;task=saveconfig" method="post" name="adminForm" id="adminForm">
        <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>

        <?php
        echo $tabs->startPane("configPane");
        echo $tabs->startPanel(JText::_('COM_HANDOUT_GENERAL'), "general-page");
        ?>
    <table class="adminform">
		<tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_GENERALCONFIG');?></td>
        </tr>
     	 <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_SECTIONOFFLINE_LABEL');?></td>
            <td class="col2"><?php echo $lists['isDown'];?></td>
            <td><span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_SECTIONOFFLINE_LABEL');?>::<?php echo JText::_('COM_HANDOUT_SECTIONOFFLINE_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
            </td>
        </tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_PATH_LABEL');?></td>
            <td>
                <?php
					$newpath = JPATH_ROOT.DS.'handouts';
					$path = $_HANDOUT->getCfg('handoutpath') ? $_HANDOUT->getCfg('handoutpath') : $newpath;
                ?>
                <input size="50" type="text" name="handoutpath" value="<?php echo $path?>" />
            </td>
            <td>
            	<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_PATH_LABEL');?>::<?php echo JText::_('COM_HANDOUT_PATH_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
					
                <input type="button" value="<?php echo JText::_('COM_HANDOUT_RESETDEFAULT_LABEL');?>" name="Reset" onclick="document.adminForm.handoutpath.value='<?php echo addslashes($newpath);?>';" />
            </td>
        </tr>
         <tr >
            <td><?php echo JText::_('COM_HANDOUT_PROCESS_PLUGINS_LABEL');?></td>
            <td><?php echo $lists['process_bots'];?></td>
            <td>
            	<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_PROCESS_PLUGINS_LABEL');?>::<?php echo JText::_('COM_HANDOUT_PROCESS_PLUGINS_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
                </td>
        </tr>
         <tr >
            <td><?php echo JText::_('COM_HANDOUT_ANALYTICS_LABEL');?></td>
            <td> <input size="10" type="text" name="ga_code" value="<?php echo $_HANDOUT->getCfg('ga_code') ?>"  /></td>
            <td>
                <span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_ANALYTICS_LABEL');?>::<?php echo JText::_('COM_HANDOUT_ANALYTICS_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
    </table>
    <table class="adminform">
        <tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_DOCUMENT_SETTINGS');?></td>
        </tr>	
         <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DAYSFORNEW_LABEL');?></td>
            <td class="col2"><input type="text" name="days_for_new" value="<?php echo $_HANDOUT->getCfg('days_for_new', 5);?>" /></td>
            <td>
            	<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_DAYSFORNEW_LABEL');?>::<?php echo JText::_('COM_HANDOUT_DAYSFORNEW_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
					</td>
		</tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_HOT_LABEL');?></td>
            <td><input type="text" name="hot" value="<?php echo $_HANDOUT->getCfg('hot', 100);?>" /></td>
            <td>
            
                <span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_HOT_LABEL');?>::<?php echo JText::_('COM_HANDOUT_HOT_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
					
        </tr>
        <tr >
            <td><?php echo JText::_('COM_HANDOUT_DISPLAY_AGREEMENTS_LABEL');?></td>
            <td><?php echo $lists['display_license'];?></td>
            <td>&nbsp;</td>
        </tr>

    </table>
	<?php
	echo $tabs->endPanel();
	echo $tabs->startPanel(JText::_('COM_HANDOUT_FRONTEND'), "frontend-page");
	?>

    <table class="adminform">
        <tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_GENERAL_SETTINGS');?></td>
        </tr>
     
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_EXTENSIONSVIEWING_LABEL');?>:</td>
            <td class="col2"><input type="text" name="viewtypes" value="<?php
        echo $_HANDOUT->getCfg('viewtypes', "pdf|doc|txt|jpg|jpeg|gif|png")?>" style="width: 200px" /></td>
            <td>
            
        <span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_EXTENSIONSVIEWING_LABEL');?>::<?php echo JText::_('COM_HANDOUT_EXTENSIONSVIEWING_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
					
       </td>
        </tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_NUMBEROFDOCS_LABEL');?></td>
            <td><input type="text" name="perpage" value="<?php echo $_HANDOUT->getCfg('perpage', 5);?>" /></td>
            <td>
            
        	<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_NUMBEROFDOCS_LABEL');?>::<?php echo JText::_('COM_HANDOUT_NUMBER_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
					
        </td>
        </tr>
         <tr>
            <td><?php echo JText::_('COM_HANDOUT_DEFAULTLISTING_LABEL');?></td>
            <td width="300"><?php echo $lists['default_order'];?>&nbsp;&nbsp;<?php echo $lists['default_order2'];?></td>
            <td>
            
        <span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_DEFAULTLISTING_LABEL');?>::<?php echo JText::_('COM_HANDOUT_DEFAULTLISTING_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
					
		</td>
        </tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_EMAILGROUP_LABEL');?></td>
            <td><?php echo $lists['emailgroups'];?></td>
            <td>
            
        <span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_EMAILGROUP_LABEL');?>::<?php echo JText::_('COM_HANDOUT_EMAILGROUP_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
					
		</td>
        </tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_EMPTYCATEGORIES_LABEL');?></td>
            <td><?php echo $lists['cat_empty'];?></td>
            <td>
				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_EMPTYCATEGORIES_LABEL');?>::<?php echo JText::_('COM_HANDOUT_EMPTYCATEGORIES_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
         <tr>
            <td><?php echo JText::_('COM_HANDOUT_EMPTYCATEGORIES_NOTICE_LABEL');?></td>
            <td><?php echo $lists['cat_empty_notice'];?></td>
            <td>
        		<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_EMPTYCATEGORIES_NOTICE_LABEL');?>::<?php echo JText::_('COM_HANDOUT_EMPTYCATEGORIES_NOTICE_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
         <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_SHOWSHARE_LABEL');?></td>
            <td class="col2"><?php echo $lists['show_share'];?></td>
            <td>
            
        <span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_SHOWSHARE_LABEL');?>::<?php echo JText::_('COM_HANDOUT_SHOWSHARE_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
					</td>
        </tr>
         <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_SHOWSHARE_FACEBOOK_LABEL');?></td>
            <td class="col2"><?php echo $lists['show_share_facebook'];?></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_SHOWSHARE_TWITTER_LABEL');?></td>
            <td class="col2"><?php echo $lists['show_share_twitter'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_SHOWSHARE_GOOGLEPLUS_LABEL');?></td>
            <td class="col2"><?php echo $lists['show_share_googleplusone'];?></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_SHOWSHARE_EMAIL_LABEL');?></td>
            <td class="col2"><?php echo $lists['show_share_email'];?></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_SHOWSHARE_COMPACT_LABEL');?></td>
            <td class="col2"><?php echo $lists['show_share_compact'];?></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_ALLOW_BULK_DOWNLOAD_LABEL');?></td>
            <td class="col2"><?php echo $lists['allow_bulk_download'];?></td>
            <td>
            
        <span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_ALLOW_BULK_DOWNLOAD_LABEL');?>::<?php echo JText::_('COM_HANDOUT_ALLOW_BULK_DOWNLOAD_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
					
        </td>
        </tr>
	</table>
	
	<table class="adminform">
        <tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_IMAGES_AND_ICONS');?></td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCUMENT_ICONSIZE_LABEL');?></td>
            <td class="col2"><?php echo $lists['doc_icon_size'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_TOOLBAR_ICONSIZE_LABEL');?></td>
            <td class="col2"><?php echo $lists['toolbar_icon_size'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_TOOLBAR_HOME_LABEL');?></td>
            <td class="col2"><?php echo $lists['menu_home'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_TOOLBAR_SEARCH_LABEL');?></td>
            <td class="col2"><?php echo $lists['menu_search'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_TOOLBAR_UPLOAD_LABEL');?></td>
            <td class="col2"><?php echo $lists['menu_upload'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_CATIMAGE_LABEL');?></td>
            <td class="col2"><?php echo $lists['cat_image'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCIMAGE_LABEL');?></td>
            <td class="col2"><?php echo $lists['doc_image'];?></td>
            <td>&nbsp;</td>
        </tr>
    </table>    

	<table class="adminform">
        <tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_DOCUMENT_PROPERTIES');?></td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCITEM_DESCRIPTION_LABEL');?></td>
            <td class="col2"><?php echo $lists['item_description'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCITEM_INFOURL_LABEL');?></td>
            <td class="col2"><?php echo $lists['item_homepage'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCITEM_DOWNLOADS_LABEL');?></td>
            <td class="col2"><?php echo $lists['item_hits'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCITEM_DATE_ADDED_LABEL');?></td>
            <td class="col2"><?php echo $lists['item_date'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCITEM_TOOLTIP_LABEL');?></td>
            <td class="col2"><?php echo $lists['item_tooltip'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCITEM_FILETYPE_LABEL');?></td>
            <td class="col2"><?php echo $lists['item_filetype'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCITEM_FILESIZE_LABEL');?></td>
            <td class="col2"><?php echo $lists['item_filesize'];?></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCITEM_LINKTYPE_LABEL');?></td>
            <td class="col2"><?php echo $lists['item_title_link'];?></td>
            <td>&nbsp;</td>
        </tr>
    </table>    

	<table class="adminform">
        <tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_DOCDETAILS');?></td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_NAME_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_name'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_IMAGE_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_image'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_DESCRIPTION_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_description'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_FILENAME_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_filename'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_FILESIZE_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_filesize'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_FILETYPE_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_filetype'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_FILEVERSION_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_fileversion'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_FILELANGUAGE_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_filelanguage'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_SUBMITTER_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_submitter'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_CREATED_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_created'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_READERS_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_readers'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_MAINTAINERS_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_maintainers'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_DOWNLOADS_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_downloads'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_UPDATED_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_updated'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_INFOURL_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_homepage'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_MD5_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_md5_checksum'];?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_DOCDETAILS_CRC_LABEL');?></td>
            <td class="col2"><?php echo $lists['details_crc_checksum'];?></td>
            <td>&nbsp;</td>
        </tr>
	</table>
    
	<?php
	echo $tabs->endPanel();
	echo $tabs->startPanel(JText::_('COM_HANDOUT_PERMISSIONS'), "permissions-page");
	?>
    <table class="adminform">
    	<tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_GUEST_PERMISSIONS');?></td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_GUEST_LABEL') ;?></td>
            <td class="col2"><?php echo $lists['guest'];?></td>
            <td>
				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_GUEST_LABEL');?>::<?php echo JText::_('COM_HANDOUT_GUEST_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
    </table>
    <table class="adminform">
        <tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_FRONTPERM');?></td>
        </tr>
         <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_UPLOAD_LABEL');?></td>
            <td class="col2"><?php echo $lists['user_upload'] ?></td>
            <td>
				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_UPLOAD_LABEL');?>::<?php echo JText::_('COM_HANDOUT_UPLOAD_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
         <tr>
            <td><?php echo JText::_('COM_HANDOUT_PUBLISH_LABEL');?></td>
            <td><?php echo $lists['user_publish'];?></td>
            <td>
				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_PUBLISH_LABEL');?>::<?php echo JText::_('COM_HANDOUT_PUBLISH_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
    </table>
    <table class="adminform">
		<tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_DOCPERM');?></td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_VIEW_LABEL');?></td>
            <td class="col2"><?php echo $lists['default_viewer'];?></td>
            <td>
				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_VIEW_LABEL');?>::<?php echo JText::_('COM_HANDOUT_VIEW_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
			 <?php
				$author_checked = '';
				$editor_checked = '';
				$assign = $_HANDOUT->getCfg('reader_assign');
				if (($assign == 1) || ($assign == 3)) {
					$author_checked = 'checked';
				}
				if (($assign == 2) || ($assign == 3)) {
					$editor_checked = 'checked';
				}
			?>
		<tr>
			<td><?php echo JText::_('COM_HANDOUT_OVERRIDEVIEW_LABEL');?></td>
			<td class="checkList">
				<input type="checkbox" name="assign_download_author" id="assign_download_author" <?php echo $author_checked;?> /><label for="assign_download_author"><?php echo JText::_('COM_HANDOUT_OWNER') ?></label><br />
				<input type="checkbox" name="assign_download_editor" id="assign_download_editor" <?php echo $editor_checked;?> /><label for="assign_download_editor"><?php echo JText::_('COM_HANDOUT_EDITOR') ?></label><br />
			</td>
			<td>
				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_OVERRIDEVIEW_LABEL');?>::<?php echo JText::_('COM_HANDOUT_OVERRIDEVIEW_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
		</tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_MAINTAIN_LABEL');?></td>
            <td><?php echo $lists['default_maintainer'];?></td>
            <td>
				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_MAINTAIN_LABEL');?>::<?php echo JText::_('COM_HANDOUT_MAINTAIN_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
        <?php
	        $author_checked = '';
	        $editor_checked = '';
	        $assign = $_HANDOUT->getCfg('editor_assign');
	        if (($assign == 1) || ($assign == 3)) {
	            $author_checked = 'checked';
	        }
	        if (($assign == 2) || ($assign == 3)) {
	            $editor_checked = 'checked';
	        }
        ?>
		<tr>
			<td><?php echo JText::_('COM_HANDOUT_OVERRIDEMANT_LABEL');?></td>
			<td class="checkList">
				<input type="checkbox" name="assign_edit_author" id="assign_edit_author" <?php echo $author_checked;?> /><label for="assign_edit_author"><?php echo JText::_('COM_HANDOUT_CREATOR') ?></label><br />
				<input type="checkbox" name="assign_edit_editor" id="assign_edit_editor" <?php echo $editor_checked;?> /><label for="assign_edit_editor"><?php echo JText::_('COM_HANDOUT_EDITOR') ?></label><br />
			</td>
			<td>
        		<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_OVERRIDEMANT_LABEL');?>::<?php echo JText::_('COM_HANDOUT_OVERRIDEMANT_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
		</tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_INDIVIDUAL_PERM_LABEL');?></td>
            <td><?php echo $lists['individual_perm'];?></td>
            <td>
				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_INDIVIDUAL_PERM_LABEL');?>::<?php echo JText::_('COM_HANDOUT_INDIVIDUAL_PERM_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
   	</table>
    <table class="adminform">
   		<tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_OWNER_PERMISSIONS');?></td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_OWNER_PERMISSIONS_LABEL');?></td>
            <td class="col2"><?php echo $lists['creator_can'];?></td>
            <td>
				<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_OWNER_PERMISSIONS_LABEL');?>::<?php echo JText::_('COM_HANDOUT_OWNER_PERMISSIONS_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>

    </table>
        <?php
        	echo $tabs->endPanel();
       	 	echo $tabs->startPanel(JText::_('COM_HANDOUT_UPLOAD'), "upload-page");
        ?>
    <table class="adminform">
    	<tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_GENERAL_SETTINGS');?></td>
        </tr>
        <tr>
			<td class="col1"><?php echo JText::_('COM_HANDOUT_UPMETHODS_LABEL');?></td>
			<td class="col2"><?php echo $lists['methods'];?></td>
			<td>
        		<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_UPMETHODS_LABEL');?>::<?php echo JText::_('COM_HANDOUT_UPMETHODS_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
		</tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_MAXFILESIZE_LABEL');?></td>
            <td><input type="text" name="maxAllowed" onkeyup="javascript:handoutFilesize(this)" value="<?php echo HANDOUT_Utils::number2text($_HANDOUT->getCfg('maxAllowed', 1024000));?>" /></td>
            <td>
       			<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_MAXFILESIZE_LABEL');?>::<?php echo JText::_('COM_HANDOUT_MAXFILESIZE_DESC'). ini_get('upload_max_filesize');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
         <tr>
            <td><?php echo JText::_('COM_HANDOUT_OVERWRITEFILES_LABEL');?></td>
            <td><?php echo $lists['overwrite'];?></td>
            <td>
        		<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_OVERWRITEFILES_LABEL');?>::<?php echo JText::_('COM_HANDOUT_OVERWRITEFILES_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
        </table>
     <table class="adminform">
        <tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_FILEXTENSIONS');?></td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_EXTALLOWED_LABEL');?></td>
            <td class="col2"><input type="text" name="extensions" value="<?php echo $_HANDOUT->getCfg('extensions', "zip|rar|pdf|txt")?>" /></td>
            <td>
        		<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_EXTALLOWED_LABEL');?>::<?php echo JText::_('COM_HANDOUT_EXTALLOWED_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_USERCANUPLOAD_LABEL');?></td>
            <td><?php echo $lists['user_all'];?></td>
            <td>
        		<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_USERCANUPLOAD_LABEL');?>::<?php echo JText::_('COM_HANDOUT_USERCANUPLOAD_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
    </table>
    <table class="adminform">
         <tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_FILENAMES');?></td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_LOWERCASE_LABEL');?></td>
            <td class="col2"><?php echo $lists['fname_lc'];?></td>
            <td>
        		<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_LOWERCASE_LABEL');?>::<?php echo JText::_('COM_HANDOUT_LOWERCASE_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_FILENAMEBLANKS_LABEL');?>:</td>
            <td><?php echo $lists['fname_blank'];?></td>
            <td>
        		<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_FILENAMEBLANKS_LABEL');?>::<?php echo JText::_('COM_HANDOUT_FILENAMEBLANKS_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_REJECTFILENAMES_LABEL');?>:</td>
            <td><input type="text" name="fname_reject" value="<?php echo htmlentities( $_HANDOUT->getCfg('fname_reject', ''), ENT_QUOTES);?>" /></td>
            <td>
        		<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_REJECTFILENAMES_LABEL');?>::<?php echo JText::_('COM_HANDOUT_REJECTFILENAMES_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>

    </table>
        <?php
        echo $tabs->endPanel();
        echo $tabs->startPanel(JText::_('COM_HANDOUT_SECURITY'), "security-page");
        ?>
    <table class="adminform">
      	<tr>
        	<td class="hadmin-subtitle" colspan="3"><?php echo JText::_('COM_HANDOUT_GENERALSECURITY');?></td>
        </tr>
        <tr>
            <td class="col1"><?php echo JText::_('COM_HANDOUT_ANTILEECH_LABEL');?></td>
            <td class="col2"><?php echo $lists['security_anti_leech'];?></td>
            <td>
        		<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_ANTILEECH_LABEL');?>::<?php echo JText::_('COM_HANDOUT_ANTILEECH_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_ALLOWEDHOSTS_LABEL');?></td>
            <td><input type="text" name="security_allowed_hosts" value="<?php echo $_HANDOUT->getCfg('security_allowed_hosts' , $_SERVER["HTTP_HOST"])?>" /></td>
            <td>
        		<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_ALLOWEDHOSTS_LABEL');?>::<?php echo JText::_('COM_HANDOUT_ALLOWEDHOSTS_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
            	<input type="button" value="<?php echo JText::_('COM_HANDOUT_RESETDEFAULT_LABEL');?>" name="Reset" onclick="document.adminForm.security_allowed_hosts.value='<?php echo $_SERVER['HTTP_HOST'];?>';" />
            </td>
        </tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_LOG_LABEL');?></td>
            <td><?php echo $lists['log'];?></td>
            <td>
        		<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_LOG_LABEL');?>::<?php echo JText::_('COM_HANDOUT_LOG_DESC');?>">
				<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
        <tr>
            <td><?php echo JText::_('COM_HANDOUT_HIDE_REMOTE_LABEL');?></td>
            <td><?php echo $lists['hide_remote'];?></td>
            <td>
       			<span class="hasTip" title="<?php echo JText::_('COM_HANDOUT_HIDE_REMOTE_LABEL');?>::<?php echo JText::_('COM_HANDOUT_HIDE_REMOTE_DESC');?>">
					<img border="0" alt="Tooltip" src="../media/com_handout/images/icon-16-tooltip.png" /></span>
			</td>
        </tr>
    </table>
     <?php
        echo $tabs->endPanel();
        echo $tabs->startPanel(JText::_('COM_HANDOUT_CLEARDATA'), "clear-data-page");
    	showClearData();
        echo $tabs->endPanel();
        echo $tabs->startPanel(JText::_('COM_HANDOUT_IMPORT'), "migration-page");
        ?>
    <table class="adminform">
      	<tr>
        	<td class="hadmin-subtitle" colspan="5"><?php echo JText::_('COM_HANDOUT_CHOOSE_IMPORT');?></td>
        </tr>       
        <tr>
            <td class="bgwhite center vtop"> 
            
	            <div class="hserver-import">
	            <div class="himport-title"><?php echo JText::_('COM_HANDOUT_MIGRATE_EXTENSION_TITLE');?></div>
		            <div class="himport-icon">
						<a href="index.php?option=com_handout&amp;task=migration&amp;migratefrom=com_docman" target="_self" onclick="return confirm('<?php echo JText::sprintf('COM_HANDOUT_MGR_CONFIRM', 'DOCman'); ?>');" class="hasTip" title="<?php echo JText::_('COM_HANDOUT_MIGRATE_DOCMAN_LABEL');?>::<?php echo JText::_('COM_HANDOUT_MIGRATE_DOCMAN_DESC');?>"><img src="components/com_handout/images/migrate_docman.jpg" /></a>
		            </div>
		            <!-- Disabled until ready 
		            <div class="himport-icon">
						<a href="#" class="hasTip" title="<?php echo JText::_('COM_HANDOUT_MIGRATE_REMOSITORY_LABEL');?>::<?php echo JText::_('COM_HANDOUT_MIGRATE_REMOSITORY_DESC');?>"><img src="components/com_handout/images/migrate_remository.jpg" /></a>
		            </div>
		            -->
		             <div class="himport-icon">
						<a href="index.php?option=com_handout&amp;task=migration&amp;migratefrom=com_rokdownloads" target="_self" onclick="return confirm('<?php echo JText::sprintf('COM_HANDOUT_MGR_CONFIRM', 'RokDownloads'); ?>');" class="hasTip" title="<?php echo JText::_('COM_HANDOUT_MIGRATE_ROKDOWNLOADS_LABEL');?>::<?php echo JText::_('COM_HANDOUT_MIGRATE_ROKDOWNLOADS_DESC');?>"><img src="components/com_handout/images/migrate_rokdownloads.jpg" /></a>
		            </div>
		            
		             <div class="himport-icon">
						<a href="index.php?option=com_handout&amp;task=migration&amp;migratefrom=com_joomdoc" target="_self" onclick="return confirm('<?php echo JText::sprintf('COM_HANDOUT_MGR_CONFIRM', 'JoomDoc'); ?>');" class="hasTip" title="<?php echo JText::_('COM_HANDOUT_MIGRATE_JOOMDOC_LABEL');?>::<?php echo JText::_('COM_HANDOUT_MIGRATE_JOOMDOC_DESC');?>"><img src="components/com_handout/images/migrate_joomdoc.jpg" /></a>
		            </div>
		            <div class="himport-icon">
						<a href="index.php?option=com_handout&amp;task=migration&amp;migratefrom=com_rubberdoc" class="hasTip" title="<?php echo JText::_('COM_HANDOUT_MIGRATE_RUBBERDOC_LABEL');?>::<?php echo JText::_('COM_HANDOUT_MIGRATE_RUBBERDOC_DESC');?>"><img src="components/com_handout/images/migrate_rubberdoc.jpg" /></a>
		            </div>
		            <div class="clr"></div>
	            </div>
	        </td> 
	        </tr>
	        <tr>
	        <td class="bgwhite center vtop">   
	            <div class="hserver-import">
	            	<div class="himport-title"><?php echo JText::_('COM_HANDOUT_MIGRATE_SERVER_TITLE');?></div>
					<div class="himport-icon">
						<a href="#" class="hasTip" title="<?php echo JText::_('COM_HANDOUT_MIGRATE_SERVER_LABEL');?>::<?php echo JText::_('COM_HANDOUT_MIGRATE_SERVER_DESC');?>" ><img src="components/com_handout/images/migrate_server.jpg" /></a> 
	            	</div>
	            	<div class="hlabel"><?php echo JText::_('COM_HANDOUT_MIGRATE_FOLDER_NAME_LABEL');?>: 
	            	<br /><input type="text" size="20" name="serverfolder" id="serverfolder" />
					<br /><button onclick="javascript:serverfoldersubmit()"><?php echo JText::_('COM_HANDOUT_MIGRATE_START_IMPORT');?></button></div>
	            	<div class="clr"></div>
	            </div>
            </td>
        </tr>
    </table>
		<script language="javascript" type="text/javascript">
        	function serverfoldersubmit() 
			{
				var foldername = document.getElementById('serverfolder').value;
				if (foldername=='') {	
					alert('<?php echo JText::_('COM_HANDOUT_MGR_SERVERFOLDER_SPECIFY')?>');
				}	
				else {
					var x = confirm('<?php echo JText::sprintf('COM_HANDOUT_MGR_CONFIRM', 'Server Folder'); ?>');
					if (x) {
						window.location = 'index.php?option=com_handout&task=migration&migratefrom=folder&folder=' + encodeURIComponent(foldername);
					}
				} 
			}
        </script>
        <?php
        	echo $tabs->endPanel();
       		echo $tabs->endPane();
        ?>
        <input type="hidden" name="id" value="" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="option" value="com_handout" />
        <input type="hidden" name="section" value="config" />
        <input type="hidden" name="HANDOUT_version" value="<?php echo COM_HANDOUT_VERSION_NUMBER;?>" />
        <?php echo HANDOUT_token::render();?>
    </form>
	<?php include_once(JPATH_ADMINISTRATOR."/components/com_handout/footer.php");?>

    <?php }
}