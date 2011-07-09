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

require_once $_HANDOUT->getPath('classes', 'utils');

include_once dirname(__FILE__) . '/config.html.php';
include_once dirname(__FILE__) . '/defines.php';
include_once dirname(__FILE__) . '/cleardata.php';

switch ($task) {
	case "cancel":
		$app = JFactory::getApplication();
		$app->redirect("index.php?option=com_handout");
		break;
	case "apply":
	case "save":
		saveConfig();
		break;
	case 'cleardata':
		clearData( $cid );
		break;
	case "show" :
	default :
		showConfig($option);
		break;
}

function showConfig($option)
{
	global $_HANDOUT;

	// disable the main menu to force user to use buttons
	$_REQUEST['hidemainmenu']=1;

	$std_inp = 'style="width: 125px" size="2"';
	$std_opt = 'size="2"';

	// Create the 'yes-no' radio options with default 0
	foreach(array('isDown' , 'display_license', 'log' , 'emailgroups',
			'user_all', 'fname_lc' , 'overwrite' , 'security_anti_leech',
			'trimwhitespace', 'process_bots', 'individual_perm', 'hide_remote',
			'allow_bulk_download', 'notify_onupload', 'notify_onedit', 'notify_ondownload',
			'notify_onedit_admin', 'thumbs_grayscale'

			)
		AS $field) {
		$lists[ $field ] = JHTML::_('select.booleanlist',$field, $std_opt,
			$_HANDOUT->getCfg($field , 0));
	}

	// Create the 'yes-no' radio options with default 1
	foreach(array('buttons_download', 'buttons_view', 'buttons_details', 'buttons_edit',
					'buttons_move', 'buttons_delete', 'buttons_update', 'buttons_reset',
					'buttons_checkout', 'buttons_publish'
			)
		AS $field) {
		$lists[ $field ] = JHTML::_('select.booleanlist',$field, $std_opt,
			$_HANDOUT->getCfg($field , 1));
	}


	//Create the show-hide radio options
	foreach(array('cat_empty' , 'cat_empty_notice' ,'menu_home', 'menu_search', 'menu_upload' ,'show_share',
					'show_share_facebook', 'show_share_twitter', 'show_share_googleplusone', 'show_share_email', 'show_share_compact',
					'item_description' , 'item_homepage' , 'item_hits',
					'item_filesize', 'item_filetype', 'item_date', 'item_tooltip', 'item_title_link',
					'details_name', 'details_image', 'details_description',
			'details_filename' , 'details_filesize' , 'details_filetype' , 'details_fileversion',
			'details_filelanguage', 'details_submitter', 'details_created' , 'details_readers' ,
			'details_maintainers' , 'details_downloads', 'details_updated' , 'details_homepage' ,
			'details_md5_checksum' , 'details_crc_checksum'
			)
		AS $field) {
		$lists[ $field ] = JHTML::_('select.booleanlist',$field, $std_opt,
			$_HANDOUT->getCfg($field , 0), 'Show', 'Hide');
	}

	$thumbs_output_format[] = JHTML::_( 'select.option', 'png', 'PNG' );
	$thumbs_output_format[] = JHTML::_( 'select.option', 'gif', 'GIF' );
	$thumbs_output_format[] = JHTML::_( 'select.option', 'jpeg', 'JPEG' );
	$lists['thumbs_output_format'] = JHTML::_( 'select.radiolist', $thumbs_output_format, 'thumbs_output_format',  '', 'value', 'text', $_HANDOUT->getCfg('thumbs_output_format' , 'png'));

	$cat_image[] = JHTML::_('select.option','0' , JText::_('COM_HANDOUT_NOIMAGE_LABEL'));
	$cat_image[] = JHTML::_('select.option','1' , JText::_('COM_HANDOUT_FOLDERICON_LABEL'));
	$cat_image[] = JHTML::_('select.option','2' , JText::_('COM_HANDOUT_THUMBNAIL_LABEL'));
	$lists['cat_image'] = JHTML::_('select.genericlist',$cat_image, 'cat_image',
		'' , 'value', 'text', $_HANDOUT->getCfg('cat_image', '0'));

	unset($cat_image);

	$doc_image[] = JHTML::_('select.option','0' , JText::_('COM_HANDOUT_NOIMAGE_LABEL'));
	$doc_image[] = JHTML::_('select.option','1' , JText::_('COM_HANDOUT_FILEICON_LABEL'));
	$doc_image[] = JHTML::_('select.option','2' , JText::_('COM_HANDOUT_THUMBNAIL_LABEL'));
	$lists['doc_image'] = JHTML::_('select.genericlist',$doc_image, 'doc_image',
		'' , 'value', 'text', $_HANDOUT->getCfg('doc_image', '0'));

	unset($doc_image);

	$guest[] = JHTML::_('select.option',COM_HANDOUT_GRANT_NO , JText::_('COM_HANDOUT_GUEST_NO_LABEL'));
	$guest[] = JHTML::_('select.option',COM_HANDOUT_GRANT_X , JText::_('COM_HANDOUT_GUEST_X_LABEL'));
	$guest[] = JHTML::_('select.option',COM_HANDOUT_GRANT_RX , JText::_('COM_HANDOUT_GUEST_RX_LABEL'));
	$lists['guest'] = JHTML::_('select.genericlist',$guest, 'registered',
		'' , 'value', 'text',
		$_HANDOUT->getCfg('registered', COM_HANDOUT_GRANT_RX));

	unset($guest);

  	$upload = new HandoutHTML_UserSelect('user_upload', 1 );
	$upload->addOption(JText::_('COM_HANDOUT_USER_UPLOAD_LABEL'), COM_HANDOUT_PERMIT_NOOWNER);
	$upload->addGeneral(JText::_('COM_HANDOUT_NO_USER_ACCESS_LABEL'), 'all');
	$upload->addJoomlaGroups();
	$upload->addHandoutGroups();
	$upload->addUsers();
	$upload->setSelectedValues(array($_HANDOUT->getCfg('user_upload', 0)));
	$lists['user_upload'] = $upload->toHtml();

	unset($upload);

	$publish = new HandoutHTML_UserSelect('user_publish', 1 );
	$publish->addOption(JText::_('COM_HANDOUT_USER_PUBLISH_LABEL'), COM_HANDOUT_PERMIT_NOOWNER);
	$publish->addGeneral(JText::_('COM_HANDOUT_AUTO_PUBLISH'), 'all');
	$publish->addJoomlaGroups();
	$publish->addHandoutGroups();
	$publish->addUsers();
	$publish->setSelectedValues(array($_HANDOUT->getCfg('user_publish', 0)));
	$lists['user_publish'] = $publish->toHtml();

	unset($publish);

	$viewer = new HandoutHTML_UserSelect('default_viewer', 1 );
	$viewer->addOption(JText::_('COM_HANDOUT_SELECT_USER_LABEL'), COM_HANDOUT_PERMIT_NOOWNER);
	$viewer->addGeneral(JText::_('COM_HANDOUT_EVERYBODY'));
	$viewer->addJoomlaGroups();
	$viewer->addHandoutGroups();
	$viewer->addUsers();
	$viewer->setSelectedValues(array($_HANDOUT->getCfg('default_viewer', 0)));
	$lists['default_viewer'] = $viewer->toHtml();

	unset($viewer);

	$maintainer = new HandoutHTML_UserSelect('default_editor', 1 );
	$maintainer->addOption(JText::_('COM_HANDOUT_SELECT_USER_LABEL'), COM_HANDOUT_PERMIT_NOOWNER);
	$maintainer->addGeneral(JText::_('COM_HANDOUT_NO_USER_ACCESS_LABEL'));
	$maintainer->addJoomlaGroups();
	$maintainer->addHandoutGroups();
	$maintainer->addUsers();
	$maintainer->setSelectedValues(array($_HANDOUT->getCfg('default_editor', 0)));
	$lists['default_maintainer'] = $maintainer->toHtml();

	unset($maintainer);

	$author_can = array();
	$author_can[] = JHTML::_('select.option',COM_HANDOUT_AUTHOR_NONE , JText::_('COM_HANDOUT_AUTHOR_NONE'));
	$author_can[] = JHTML::_('select.option',COM_HANDOUT_AUTHOR_CAN_READ , JText::_('COM_HANDOUT_AUTHOR_READ_LABEL'));
	$author_can[] = JHTML::_('select.option',COM_HANDOUT_AUTHOR_CAN_EDIT , JText::_('COM_HANDOUT_AUTHOR_BOTH_LABEL'));
	$lists['creator_can'] = JHTML::_('select.genericlist',$author_can, 'author_can',
		'', 'value', 'text',
		$_HANDOUT->getCfg('author_can', COM_HANDOUT_AUTHOR_CAN_EDIT));

	unset($author_can);

	// Blank handling for filenames
	$blanks[] = JHTML::_('select.option','0', JText::_('COM_HANDOUT_ALLOWBLANKS_LABEL'));
	$blanks[] = JHTML::_('select.option','1', JText::_('COM_HANDOUT_REJECT_LABEL'));
	$blanks[] = JHTML::_('select.option','2', JText::_('COM_HANDOUT_CONVERTUNDER_LABEL'));
	$blanks[] = JHTML::_('select.option','3', JText::_('COM_HANDOUT_CONVERTDASH_LABEL'));
	$blanks[] = JHTML::_('select.option','4', JText::_('COM_HANDOUT_REMOVEBLANKS_LABEL'));
	$lists['fname_blank'] = JHTML::_('select.genericlist',$blanks, 'fname_blank',
		'', 'value', 'text',
		$_HANDOUT->getCfg('fname_blank', 0));

	unset($blanks);

	// assemble icon sizes
	$size[] = JHTML::_('select.option','32', '32x32 pixel');
	$size[] = JHTML::_('select.option','64', '64x64 pixel');
	foreach(array('doc_icon_size' , 'toolbar_icon_size')
		AS $field) {
		$lists[ $field ] = JHTML::_('select.genericlist',$size, $field,
		$std_inp, 'value', 'text',
		$_HANDOUT->getCfg($field, 0));
	}
	unset($size);

	// assemble displaying order
	$order[] = JHTML::_('select.option','name', JText::_('COM_HANDOUT_NAME_LABEL'));
	$order[] = JHTML::_('select.option','date', JText::_('COM_HANDOUT_DATE_LABEL'));
	$order[] = JHTML::_('select.option','hits', JText::_('COM_HANDOUT_DOWNLOADS_LABEL'));
	$lists['default_order'] = JHTML::_('select.genericlist',$order, 'default_order',
		'style="width: 125px"', 'value', 'text',
		$_HANDOUT->getCfg('default_order', 'name'));
	$order2[] = JHTML::_('select.option','ASC', JText::_('COM_HANDOUT_ASCENDING_LABEL'));
	$order2[] = JHTML::_('select.option','DESC', JText::_('COM_HANDOUT_DESCENDING_LABEL'));
	$lists['default_order2'] = JHTML::_('select.genericlist',$order2, 'default_order2',
		'style="width: 125px"', 'value', 'text',
		$_HANDOUT->getCfg('default_order2', 'DESC'));

	unset($order2);

	// Assemble the methods we allow
	$methods = array();
	$methods[] = JHTML::_('select.option','http' , JText::_('COM_HANDOUT_OPTION_UPLOAD_A_FILE'));
	$methods[] = JHTML::_('select.option','link' , JText::_('COM_HANDOUT_OPTION_LINK_TO_A_FILE'));
	$methods[] = JHTML::_('select.option','transfer' , JText::_('COM_HANDOUT_OPTION_REMOTE_TRANSFER'));
	$default_methods = $_HANDOUT->getCfg('methods', array('http'));
	// ugh ... all because they like arrays of classes....
	$class_methods = array();
	foreach($default_methods as $a_method) {
		$class_methods[] = JHTML::_('select.option',$a_method);
	}

	$lists['methods'] = JHTML::_('select.genericlist',$methods, 'methods[]',
		'size="3" multiple', 'value', 'text', $class_methods);

	unset($methods);
	unset($class_methods);

	HTML_HandoutConfig::configuration($lists);
	$_HANDOUT->saveConfig(); // Save any defaults we created...

}

function saveConfig()
{
	HANDOUT_token::check() or die('Invalid Token');

	global $_HANDOUT;
	$task = JRequest::getCmd('task');
	$app = &JFactory::getApplication();

	$_POST = HANDOUT_Utils::stripslashes($_POST);

	$handoutMax = HANDOUT_Utils::text2number($_POST['maxAllowed']);
	$_POST[ 'maxAllowed'] = $handoutMax;

	$sysUploadMax = HANDOUT_Utils::text2number(ini_get('upload_max_filesize'));
	$sysPostMax = HANDOUT_Utils::text2number(ini_get('post_max_size'));
	$max = min($sysUploadMax , $sysPostMax);

	if ($handoutMax < 0) {
		$app->redirect("index.php?option=com_handout&section=config", JText::_('COM_HANDOUT_CONFIG_ERROR_UPLOAD'));
	}

	$override_edit = COM_HANDOUT_ASSIGN_NONE;
	$author = JRequest::getVar( 'assign_edit_author', 0);
	$editor = JRequest::getVar( 'assign_edit_editor', 0);
	if ($author) {
		$override_edit = COM_HANDOUT_ASSIGN_BY_AUTHOR;
	}
	if ($editor) {
		$override_edit = COM_HANDOUT_ASSIGN_BY_EDITOR;
	}
	if ($author && $editor) {
		$override_edit = COM_HANDOUT_ASSIGN_BY_AUTHOR_EDITOR;
	}
	$_POST['editor_assign'] = $override_edit;
	unset($_POST['assign_edit_author']);
	unset($_POST['assign_edit_editor']);

	$override_down = COM_HANDOUT_ASSIGN_NONE;
	$author = JRequest::getVar( 'assign_download_author', 0);
	$editor = JRequest::getVar( 'assign_download_editor', 0);
	if ($author) {
		$override_down = COM_HANDOUT_ASSIGN_BY_AUTHOR;
	}
	if ($editor) {
		$override_down = COM_HANDOUT_ASSIGN_BY_EDITOR;
	}
	if ($author && $editor) {
		$override_down = COM_HANDOUT_ASSIGN_BY_AUTHOR_EDITOR;
	}
	$_POST['reader_assign'] = $override_down;
	unset($_POST['assign_download_author']);
	unset($_POST['assign_download_editor']);

	foreach($_POST as $key => $value) {
		$_HANDOUT->setCfg($key, $value);
	}

	if ($_HANDOUT->saveConfig()) {
		if ($max < $handoutMax) {
			$app->redirect("index.php?option=com_handout&section=config", JText::_('COM_HANDOUT_CONFIG_WARNING') . HANDOUT_UTILS::number2text($max));
		} else {
			$section = ($task=='apply') ? '&section=config' : '';
			$app->redirect('index.php?option=com_handout'.$section, JText::_('COM_HANDOUT_CONFIG_UPDATED'));
		}
	} else {
		$app->redirect("index.php?option=com_handout&section=config", JText::_('COM_HANDOUT_CONFIG_ERROR'));
	}
}