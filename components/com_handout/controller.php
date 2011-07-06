<?php

 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: controller.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
require_once (JPATH_COMPONENT_HELPERS . DS . 'documents.php');

//component constants
define('COM_HANDOUT_IMAGESPATH', JURI::root(true) . '/components/com_handout/media/images/');
define('COM_HANDOUT_CSSPATH', 'components/com_handout/media/css/');

class HandoutController extends JController
{
    function __construct ($config = array())
    {
        parent::__construct($config);
        $this->registerTask('license_result', 'license_result');
    }

    function display ()
    {
        $gid = HandoutHelper::getGid();
        switch ($this->getTask()) {
            case 'cat_view':
                JRequest::setVar('view', 'handout');
                break;
            case 'doc_download':
            case 'doc_view':
                JRequest::setVar('view', 'download');
                break;
            case 'doc_code':
                JRequest::setVar('view', 'code');
                break;
            case 'search_form':
            case 'search_result':
                JRequest::setVar('view', 'search');
                break;
            case 'doc_details':
                JRequest::setVar('view', 'document');
                break;
            case 'doc_edit':
                $view = $this->getView('document', 'html');
                $view->_displayEdit();
                return;
            case 'doc_save':
            case 'save':
                DocumentsHelper::saveDocument($gid);
                break;
            case 'doc_cancel':
            case 'cancel':
                DocumentsHelper::cancelDocument($gid);
                break;
            case 'doc_move':
                $view = $this->getView('document', 'html');
                $view->_displayMove();
                return;
            case 'doc_move_process':
                DocumentsHelper::moveDocumentProcess($gid);
                break;
            case 'doc_checkin':
                DocumentsHelper::checkinDocument($gid);
                break;
            case 'doc_checkout':
                DocumentsHelper::checkoutDocument($gid);
                break;
            case 'doc_reset':
                DocumentsHelper::resetDocument($gid);
                break;
            case 'doc_delete':
                DocumentsHelper::deleteDocument($gid);
                break;
            case 'upload':
                $view = $this->getView('document', 'html');
                $view->_displayUpload(0);
                return;
            case 'doc_update':
                $view = $this->getView('document', 'html');
                $view->_displayUpload(1);
                return;
            case 'doc_unpublish':
                DocumentsHelper::publishDocument(array($gid), 0);
                break;
            case 'doc_publish':
                DocumentsHelper::publishDocument(array($gid));
                break;
        }
        parent::display(true);
    }

    function license_result ()
    {
        require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'downloads.php');
        DownloadsHelper::licenseDocumentProcess(HandoutHelper::getGid());
    }
}
?>