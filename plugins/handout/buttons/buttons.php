<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: buttons.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	Improved by JoomDOC by Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
    defined ( '_JEXEC' ) or die ( 'Restricted access' );

    $mainframe->registerEvent( 'onFetchButtons', 'bot_buttons' );

    $factory = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_handout' . DS . 'helpers' . DS . 'factory.php';
    if(file_exists($factory)){
        require_once $factory;
    }

    function bot_buttons($params) {
        $lang = JFactory::getLanguage();
        $lang->load('plg_handout_buttons', JPATH_ADMINISTRATOR);

        $_HANDOUT = &HandoutFactory::getHandout();
    	$_HANDOUT_USER = &HandoutFactory::getHandoutUser();
        require_once($_HANDOUT->getPath('classes', 'button'));
        require_once($_HANDOUT->getPath('classes', 'token'));

        $doc        = & $params['doc'];
        $file       = & $params['file'];
        $objDBDoc   = $doc->objDBTable;

        //set parameters
	 	$plugin =& JPluginHelper::getPlugin('handout', 'buttons');
	 	$pluginParams = new JParameter( $plugin->params );

		$pluginParams->set('download', $_HANDOUT->getCfg('buttons_download', '1'));
		$pluginParams->set('view', $_HANDOUT->getCfg('buttons_view', '1'));
		$pluginParams->set('details', $_HANDOUT->getCfg('buttons_details', '1'));
		$pluginParams->set('edit', $_HANDOUT->getCfg('buttons_edit', '1'));
		$pluginParams->set('move', $_HANDOUT->getCfg('buttons_move', '1'));
		$pluginParams->set('delete', $_HANDOUT->getCfg('buttons_delete', '1'));
		$pluginParams->set('update', $_HANDOUT->getCfg('buttons_update', '1'));
		$pluginParams->set('reset', $_HANDOUT->getCfg('buttons_reset', '1'));

        $js = "javascript:if(confirm('".JText::_('PLG_HANDOUT_STANDARD_BTNS_ARE_YOU_SURE')."')) {window.location='%s'}";

        // format document links, ONLY those the user can perform.
        $buttons = array();

        if ($_HANDOUT_USER->canDownload($objDBDoc) &&  $pluginParams->get('download', 1)) {
            $buttons['download'] = new HANDOUT_Button('download', JText::_('PLG_HANDOUT_STANDARD_BTNS_DOWNLOAD'), $doc->_formatLink('doc_download'));
        }

        if ($_HANDOUT_USER->canDownload($objDBDoc) &&  $pluginParams->get('view', 1)) {
            $viewtypes = trim($_HANDOUT->getCfg('viewtypes'));
            if ($viewtypes != '' && ($viewtypes == '*' || stristr($viewtypes, $file->ext))) {
				$link_params = array('tmpl' => 'component', 'format' => 'raw');
                $link = $doc->_formatLink('doc_view', $link_params, true);
                $params = new HandoutParameters('popup=1');
                $buttons['view'] = new HANDOUT_Button('view', JText::_('PLG_HANDOUT_STANDARD_BTNS_VIEW'), $link, $params);
            }
        }

        if($pluginParams->get('details', 1)) {
            $buttons['details'] = new HANDOUT_Button('details', JText::_('PLG_HANDOUT_STANDARD_BTNS_DETAILS'), $doc->_formatLink('doc_details'));
        }

        if ($_HANDOUT_USER->canEdit($objDBDoc) &&  $pluginParams->get('edit', 1)) {
            $buttons['edit'] = new HANDOUT_Button('edit', JText::_('PLG_HANDOUT_STANDARD_BTNS_EDIT'), $doc->_formatLink('doc_edit'));
        }

        if ($_HANDOUT_USER->canMove($objDBDoc) &&  $pluginParams->get('move', 1)) {
            $buttons['move'] = new HANDOUT_Button('move', JText::_('PLG_HANDOUT_STANDARD_BTNS_MOVE'), $doc->_formatLink('doc_move'));
        }

        if ($_HANDOUT_USER->canDelete($objDBDoc) &&  $pluginParams->get('delete', 1)) {
            $link = $doc->_formatLink('doc_delete', null, null, true);
            $buttons['delete'] = new HANDOUT_Button('delete', JText::_('PLG_HANDOUT_STANDARD_BTNS_DELETE'), sprintf($js, $link));
        }

        if ($_HANDOUT_USER->canUpdate($objDBDoc) &&  $pluginParams->get('update', 1)) {
            $buttons['update'] = new HANDOUT_Button('update', JText::_('PLG_HANDOUT_STANDARD_BTNS_UPDATE'), $doc->_formatLink('doc_update'));
        }

        if ($_HANDOUT_USER->canReset($objDBDoc) &&  $pluginParams->get('reset', 1)) {
            $buttons['reset'] = new HANDOUT_Button('reset', JText::_('PLG_HANDOUT_STANDARD_BTNS_RESET'), sprintf($js, $doc->_formatLink('doc_reset')));
        }

        if ($_HANDOUT_USER->canCheckin($objDBDoc) && $objDBDoc->checked_out &&  $pluginParams->get('checkout', 1)) {
            $params = new HandoutParameters('class=checkin');
            $buttons['checkin'] = new HANDOUT_Button('checkin', JText::_('PLG_HANDOUT_STANDARD_BTNS_CHECKIN'), $doc->_formatLink('doc_checkin'), $params);
        }

        if ($_HANDOUT_USER->canCheckout($objDBDoc) && !$objDBDoc->checked_out &&  $pluginParams->get('checkout', 1)) {
            $buttons['checkout'] = new HANDOUT_Button('checkout', JText::_('PLG_HANDOUT_STANDARD_BTNS_CHECKOUT'), $doc->_formatLink('doc_checkout'));
        }

        if ($_HANDOUT_USER->canPublish($objDBDoc) && !$objDBDoc->published &&  $pluginParams->get('publish', 1)) {
            $params = new HandoutParameters('class=publish');
            $link   = $doc->_formatLink('doc_publish', null, null, true);
            $buttons['publish'] = new HANDOUT_Button('publish', JText::_('PLG_HANDOUT_STANDARD_BTNS_PUBLISH'), $link, $params);
        }

        if ($_HANDOUT_USER->canUnPublish($objDBDoc) && $objDBDoc->published &&  $pluginParams->get('publish', 1)) {
            $link   = $doc->_formatLink('doc_unpublish', null, null, true);
            $buttons['unpublish'] = new HANDOUT_Button('unpublish', JText::_('PLG_HANDOUT_STANDARD_BTNS_UNPUBLISH'), $link);
        }

        return $buttons;

    }
?>