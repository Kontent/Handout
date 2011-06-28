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
    
        $botParams  = bot_buttonsParams();
        $js = "javascript:if(confirm('".JText::_('PLG_HANDOUT_STANDARD_BTNS_ARE_YOU_SURE')."')) {window.location='%s'}";
    
        // format document links, ONLY those the user can perform.
        $buttons = array();
    
        if ($_HANDOUT_USER->canDownload($objDBDoc) AND $botParams->get('download', 1)) {
            $buttons['download'] = new HANDOUT_Button('download', JText::_('PLG_HANDOUT_STANDARD_BTNS_DOWNLOAD'), $doc->_formatLink('doc_download'));
        }
    
        if ($_HANDOUT_USER->canDownload($objDBDoc) AND $botParams->get('view', 1)) {
            $viewtypes = trim($_HANDOUT->getCfg('viewtypes'));
            if ($viewtypes != '' && ($viewtypes == '*' || stristr($viewtypes, $file->ext))) {
				$link_params = array('tmpl' => 'component', 'format' => 'raw');
                $link = $doc->_formatLink('doc_view', $link_params, true);
                $params = new HandoutParameters('popup=1');
                $buttons['view'] = new HANDOUT_Button('view', JText::_('PLG_HANDOUT_STANDARD_BTNS_VIEW'), $link, $params);
            }
        }
    
        if($botParams->get('details', 1)) {
            $buttons['details'] = new HANDOUT_Button('details', JText::_('PLG_HANDOUT_STANDARD_BTNS_DETAILS'), $doc->_formatLink('doc_details'));
        }
    
        if ($_HANDOUT_USER->canEdit($objDBDoc) AND $botParams->get('edit', 1)) {
            $buttons['edit'] = new HANDOUT_Button('edit', JText::_('PLG_HANDOUT_STANDARD_BTNS_EDIT'), $doc->_formatLink('doc_edit'));
        }
    
        if ($_HANDOUT_USER->canMove($objDBDoc) AND $botParams->get('move', 1)) {
            $buttons['move'] = new HANDOUT_Button('move', JText::_('PLG_HANDOUT_STANDARD_BTNS_MOVE'), $doc->_formatLink('doc_move'));
        }
    
        if ($_HANDOUT_USER->canDelete($objDBDoc) AND $botParams->get('delete', 1)) {
            $link = $doc->_formatLink('doc_delete', null, null, true);
            $buttons['delete'] = new HANDOUT_Button('delete', JText::_('PLG_HANDOUT_STANDARD_BTNS_DELETE'), sprintf($js, $link));
        }
    
        if ($_HANDOUT_USER->canUpdate($objDBDoc) AND $botParams->get('update', 1)) {
            $buttons['update'] = new HANDOUT_Button('update', JText::_('PLG_HANDOUT_STANDARD_BTNS_UPDATE'), $doc->_formatLink('doc_update'));
        }
    
        if ($_HANDOUT_USER->canReset($objDBDoc) AND $botParams->get('reset', 1)) {
            $buttons['reset'] = new HANDOUT_Button('reset', JText::_('PLG_HANDOUT_STANDARD_BTNS_RESET'), sprintf($js, $doc->_formatLink('doc_reset')));
        }
    
        if ($_HANDOUT_USER->canCheckin($objDBDoc) AND $objDBDoc->checked_out AND $botParams->get('checkout', 1)) {
            $params = new HandoutParameters('class=checkin');
            $buttons['checkin'] = new HANDOUT_Button('checkin', JText::_('PLG_HANDOUT_STANDARD_BTNS_CHECKIN'), $doc->_formatLink('doc_checkin'), $params);
        }
    
        if ($_HANDOUT_USER->canCheckout($objDBDoc) AND !$objDBDoc->checked_out AND $botParams->get('checkout', 1)) {
            $buttons['checkout'] = new HANDOUT_Button('checkout', JText::_('PLG_HANDOUT_STANDARD_BTNS_CHECKOUT'), $doc->_formatLink('doc_checkout'));
        }
       
        if ($_HANDOUT_USER->canPublish($objDBDoc) AND !$objDBDoc->published AND $botParams->get('publish', 1)) {
            $params = new HandoutParameters('class=publish');
            $link   = $doc->_formatLink('doc_publish', null, null, true);
            $buttons['publish'] = new HANDOUT_Button('publish', JText::_('PLG_HANDOUT_STANDARD_BTNS_PUBLISH'), $link, $params);
        }
    
        if ($_HANDOUT_USER->canUnPublish($objDBDoc) AND $objDBDoc->published AND $botParams->get('publish', 1)) {
            $link   = $doc->_formatLink('doc_unpublish', null, null, true);
            $buttons['unpublish'] = new HANDOUT_Button('unpublish', JText::_('PLG_HANDOUT_STANDARD_BTNS_UNPUBLISH'), $link);
        }
    
        return $buttons;
    
    }
    
    function bot_buttonsParams() {
        global $_HANDOUT_PLUGINS;
        $database = &JFactory::getDBO();
       
    	// check if param query has previously been processed
        if ( !isset($_HANDOUT_PLUGINS->_params['buttons']) ) {
            // load plugin params info
            $query = "SELECT params"
            . "\n FROM #__plugins"
            . "\n WHERE element = 'buttons'"
            . "\n AND folder = 'handout'"
            ;
            $database->setQuery( $query );
            $params = $database->loadResult();
    
            // save query to class variable
            $_HANDOUT_PLUGINS->_params['buttons'] = $params;
        }
    
        // pull query data from class variable
        $botParams = new JParameter(  $_HANDOUT_PLUGINS->_params['buttons'] );
        return $botParams;
    }
?>