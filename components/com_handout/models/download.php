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

jimport('joomla.application.component.model');
class HandoutModelDownload extends JModel
{

function __construct()
          {
          
          	
          if (defined('_HANDOUT_HTML_DOWNLOAD')) {
               define('_HANDOUT_HTML_DOWNLOAD', 1);
            }
          parent::__construct();
          
          }
          
          
          
	function licenseDocumentProcess($uid) {
		// this needs to use REQUEST , so onBeforeDownload plugins can use redirect
		$accepted = JRequest::getInt('agree');
		$inline = JRequest::getInt('inline');
		$doc = new HANDOUT_Document ( $uid );

		if ($accepted) {
			$this->download ( $doc, $inline );
		} else {
			HandoutHelper::_returnTo ( 'view_cat', JText::_('COM_HANDOUT_YOU_MUST'), $doc->getData ( 'catid' ) );
		}
	}
	function download(&$doc, $inline = false) {
		$handout = &HandoutFactory::getHandout();
		$handoutUser = &HandoutFactory::getHandoutUser();
		$db = &JFactory::getDBO();
		$config = &JFactory::getConfig();
		$tzoffset = $config->getValue('config.offset');

		require_once $handout->getPath ( 'classes', 'file' );

		$data = &$doc->getDataObject ();

		/* ------------------------------ *
	 *   CORE AUTHORIZATIONS		  *
	 * ------------------------------ */

		// if the user is not authorized to download this document, redirect
		if (! $handoutUser->canDownload ( $doc->getDBObject () )) {
			HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_NOLOG_DOWNLOAD'), $data->catid );
		}

		// If the document is not published, redirect
		if (! $data->published and ! $handoutUser->canPublish ()) {
			HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_NOPUBLISHED_DOWNLOAD'), $data->catid );
		}

		// if the document is checked out, redirect
		if ($data->checked_out && $handoutUser->userid != $data->checked_out) {
			HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_NOTDOWN'), $data->catid );
		}

		// If the remote host is not allowed, show anti-leech message and die.
		if (! HANDOUT_Utils::checkDomainAuthorization ()) {
			$from_url = parse_url ( $_SERVER ['HTTP_REFERER'] );
			$from_host = trim ( $from_url ['host'] );

			HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_ANTILEECH_ACTIVE') . " (" . $from_host . ")", $data->catid );
			exit ();
		}

		/* ------------------------------ *
	 *   GET FILE 					  *
	 * ------------------------------ */

		$file = new HANDOUT_File ( $data->docfilename, $handout->getCfg ( 'handoutpath' ) );

		// If the file doesn't exist, redirect
		if (! $file->exists ()) {
			HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_FILE_UNAVAILABLE'), $data->catid );
		}

		/* ------------------------------ *
	 *   PLUGIN - Setup All Plugins   *
	 * ------------------------------ */

		$doc_dbo = $doc->getDBObject (); //Fix for PHP 5


		$logbot = new HANDOUT_plugin ( 'onLog' );
		$prebot = new HANDOUT_plugin ( 'onBeforeDownload' );
		$postbot = new HANDOUT_plugin ( 'onAfterDownload' );
		$logbot->setParm ( 'document', $doc_dbo );
		$logbot->setParm ( 'file', $file );
		$logbot->setParm ( 'user', $handoutUser );
		$logbot->copyParm ( 'process', 'download' );
		$prebot->setParmArray ( $logbot->getParm () ); // Copy the parms over
		$postbot->setParmArray ( $logbot->getParm () );

		/* ------------------------------ *
	 *   PLUGIN - PREDOWNLOAD		 *
	 * ------------------------------ */
		$prebot->trigger ();
		if ($prebot->getError ()) {
			$logbot->copyParm ( 'msg', $prebot->getErrorMsg () );
			$logbot->copyParm ( 'status', 'LOG_ERROR' );
			$logbot->trigger ();
			HandoutHelper::_returnTo ( 'cat_view', $prebot->getErrorMsg () );
		}

		// let's increment the counter
		$dbobject = $doc->getDBObject ();
		$dbobject->incrementCounter ();

		// place an entry in the log
		if ($handout->getCfg ( 'log' )) {
			$browser = & JBrowser::getInstance ( $_SERVER ['HTTP_USER_AGENT'] );

			@session_start();
			$downloadcode = $_SESSION['handout.downloadcode'];
			$anonuser = $_SESSION['handout.anonuser'];

			$now = date ( "Y-m-d H:i:s", time ( "Y-m-d g:i:s" ) + $tzoffset * 60 * 60 );
			$remote_ip = $_SERVER ['REMOTE_ADDR'];
			$row_log = new HandoutLog ( $db );
			$row_log->log_docid = $data->id;
			$row_log->log_code = $downloadcode;
			$row_log->log_ip = $remote_ip;
			$row_log->log_datetime = $now;
			$row_log->log_user = $handoutUser->userid ? $handoutUser->userid : $anonuser;
			$row_log->log_browser = $browser->getBrowser ();
			$row_log->log_os = $browser->getPlatform ();
			if (! $row_log->store ()) {
				exit ();
			}
		}
		$logbot->copyParm ( array ('msg' => 'Download Complete', 'status' => 'LOG_OK' ) );
		$logbot->trigger ();
		$file->download ( $inline );

		/* ------------------------------ *
	 *   PLUGIN - PostDownload		*
	 * Currently - we die and no out  *
	 * ------------------------------ */
		$postbot->trigger ();
		/* if( $postbot->getError() ){
	*		$logbot->copyParm( array(	'msg'	=> $postbot->getErrorMsg() ,
	*			 			  			'status'=> 'LOG_ERROR'
	*								)
	*						);
	*		$logbot->trigger();
	*		HandoutHelper::_returnTo('cat_view',$postbot->getErrorMsg() );
	*}
	*/

		die (); // REQUIRED
	}
          
          
          


}