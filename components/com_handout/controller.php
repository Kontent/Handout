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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.environment.browser');
//require_once JPATH_COMPONENT_HELPERS . DS . 'documents.php';
 $_HANDOUT = &HandoutFactory::getHandout();
require_once $_HANDOUT->getPath('classes', 'html');
//component constants
define('COM_HANDOUT_IMAGESPATH', JURI::root(true) . '/components/com_handout/media/images/');
define('COM_HANDOUT_CSSPATH', 'components/com_handout/media/css/');

class HandoutController extends JController
{

	
	public $_tmpl;
	public $_format;
	public $_dv;
	function __construct ($config = array())
	{
			
	
		
		
		$this->_tmpl=JRequest::getVar('tmpl','');
		$this->_dv=JRequest::getVar('dv',0);
		if($this->_tmpl=='')
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPod')) {
			
			JRequest::setVar('tmpl','component');
			$this->_tmpl='component';
			
		}
		$this->_format=JRequest::getVar('format','');
		
		parent::__construct($config);
		$this->registerTask('license_result', 'license_result');
		
	}

	function display ()
	{
		$handout_model = $this->getModel('handout');
		$document_model = $this->getModel('document');
		$gid=HandoutHelper::getGid();
		switch ($this->getTask()) {
			case 'cat_view':
				
			
		if($this->_tmpl=='component' && $this->_format=='' && $this->_dv!=1)
				{
					
					$view = $this->getView('handout', 'mobile');
					$view->display();
					
				}else 
				{
					
					JRequest::setVar('view', 'handout');
				
				}
							
				break;
			case 'doc_download':
			case 'doc_view':
				
				
		if($this->_tmpl=='component' && $this->_format=='' && $this->_dv!=1 )
				{
					
					$view = $this->getView('download', 'mobile');
					$view->display();
					
				}else 
				{
					
					JRequest::setVar('view', 'download');
				
				}
				
				
				
				break;
			case 'doc_code':
				
		 if($this->_tmpl=='component' && $this->_format=='' && $this->_dv!=1)
				{
					
					$view = $this->getView('code', 'mobile');
					$view->display();
					
				}else 
				{
					JRequest::setVar('view', 'code');
				
				}
				
				break;
			case 'search_form':
			case 'search_result':
				JRequest::setVar('view', 'search');
				break;
			case 'doc_details':
		        if($this->_tmpl=='component' && $this->_format=='' && $this->_dv!=1)
				{
					
					$view = $this->getView('document', 'mobile');
					$view->display();
					
				}else 
				{
					JRequest::setVar('view', 'document');
				
				}
				break;
			case 'doc_edit':
				$view = $this->getView('document', 'html');
				$view->_displayEdit();
				return;
			case 'doc_save':
			case 'save':
	                   $document_model->saveDocument($gid);
				break;
			case 'doc_cancel':
			case 'cancel':
				$document_model->cancelDocument($gid);
				break;
			case 'doc_move':
				$view = $this->getView('document', 'html');
				$view->_displayMove();
				return;
			case 'doc_move_process':
				$document_model->moveDocumentProcess($gid);
				break;
			case 'doc_checkin':
				$document_model->checkinDocument($gid);
				break;
			case 'doc_checkout':
				$document_model->checkoutDocument($gid);
				break;
			case 'doc_reset':
				$document_model->resetDocument($gid);
				break;
			case 'doc_delete':
				$document_model->deleteDocument($gid);
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
				$document_model->publishDocument(array($gid), 0);
				break;
			case 'doc_publish':
				$document_model->publishDocument(array($gid));
				break;
		}
		if($this->_tmpl!='component' || $this->_format!='' || $this->_dv==1 ){
		parent::display(true);
	}else if(!$this->getTask() ){
		
					$view = $this->getView('handout', 'mobile');
					$view->display();
	}
		
	}

	function license_result ()
	{   $download = $this->getModel('download');
		//require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'downloads.php';
		$download->licenseDocumentProcess(HandoutHelper::getGid());
	}
}
?>