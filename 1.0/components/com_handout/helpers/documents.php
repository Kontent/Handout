<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: documents.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );

require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'documents.html.php');

class DocumentsHelper {
	
	function fetchDocument($id) {
		
		// onFetchDocument event, type = details

		$bot = new HANDOUT_plugin ( 'onFetchDocument' );
		$bot->setParm ( 'id', $id );
		$bot->copyParm ( 'type', 'details' );
		$bot->trigger ();
		if ($bot->getError ()) {
			HandoutHelper::_returnTo ( 'cat_view', $bot->getErrorMsg () );
		}
		
		// document

		$doc = & HANDOUT_Document::getInstance ( $id );
		
		// process content plugins

		HANDOUT_Utils::processContentPlugins ( $doc );
		
		$buttons = $doc->getLinkObject ();
		$paths = $doc->getPathObject ();
		$data = $doc->getDataObject ();

		$returnArray = array($buttons, $paths, $data);
		return $returnArray;
	}
	
	function fetchDocumentList($catid) {
		$handout = &HandoutFactory::getHandout ();
		
		$ordering = JRequest::getVar ( 'order', $handout->getCfg ( 'default_order' ) );
		$direction = strtoupper ( JRequest::getVar ( 'dir', $handout->getCfg ( 'default_order2' ) ) );
		$limit = JRequest::getInt ( 'limit', $handout->getCfg ( 'perpage' ) );
		$limitstart = JRequest::getInt ( 'limitstart' );
		
		if (! $catid) {
			return;
		}
		
		$rows = HANDOUT_Docs::getDocsByUserAccess ( $catid, $ordering, $direction, $limit, $limitstart );
		if (! is_array ( $rows )) {
			$rows = array ();
		}
		$params = array ('limit' => $limit, 'limitstart' => $limitstart );
		
		// create orderby object

		$links = array ();
		$links ['name'] = HandoutHelper::_taskLink ( 'cat_view', $catid, array_merge ( $params, array ('order' => 'name', 'dir' => $direction ) ) );
		$links ['date'] = HandoutHelper::_taskLink ( 'cat_view', $catid, array_merge ( $params, array ('order' => 'date', 'dir' => $direction ) ) );
		$links ['hits'] = HandoutHelper::_taskLink ( 'cat_view', $catid, array_merge ( $params, array ('order' => 'hits', 'dir' => $direction ) ) );
		
		if ($direction == 'ASC') {
			$links ['dir'] = HandoutHelper::_taskLink ( 'cat_view', $catid, array_merge ( $params, array ('order' => $ordering, 'dir' => 'DESC' ) ) );
		} else {
			$links ['dir'] = HandoutHelper::_taskLink ( 'cat_view', $catid, array_merge ( $params, array ('order' => $ordering, 'dir' => 'ASC' ) ) );
		}
		
		//set pathway information

		$pathway = new StdClass ( );
		$pathway->links = $links;
		
		//set order information

		$order = new StdClass ( );
		$order->links = $links;
		$order->direction = $direction;
		$order->orderby = $ordering;
		$order->limit = $limit;
		$order->limitstart = $limitstart;
		
		$items = array ();
		foreach ( $rows as $row ) {
			
			// onFetchDocument event, type = list

			$bot = new HANDOUT_plugin ( 'onFetchDocument' );
			$bot->setParm ( 'id', $row->id );
			$bot->copyParm ( 'type', 'list' );
			$bot->trigger ();
			if ($bot->getError ()) {
				HandoutHelper::_returnTo ( 'cat_view', $bot->getErrorMsg () );
			}
			
			// load doc

			$doc = & HANDOUT_Document::getInstance ( $row->id );
			
			// process content plugins

			HANDOUT_Utils::processContentPlugins ( $doc );
			
			$item = new StdClass ( );
			$item->buttons = &$doc->getLinkObject ();
			$item->paths = &$doc->getPathObject ();
			$item->data = &$doc->getDataObject ();
			
			$items [] = $item;
		}
		
		$returnArray = array($order, $items);
		return $returnArray;		
	}
	
	function fetchEditDocumentForm($uid, $filename = null, $catid = 0) {
		$database = &JFactory::getDBO ();
		
		$_HANDOUT_USER = &HandoutFactory::getHandoutUser ();
		
		$doc = new HandoutDocument ( $database );
		if ($uid) {
			$doc->load ( $uid ); //Load the document

			

			//check user permissions

			$err = $_HANDOUT_USER->canPreformTask ( $doc, 'Edit' );
			if ($err) {
				HandoutHelper::_returnTo ( 'cat_view', $err, $doc->catid );
			}
		} else {
			$doc->init_record (); //Initialise a document

			

			//check user permissions

			$err = $_HANDOUT_USER->canPreformTask ( $doc, 'Upload' );
			if ($err) {
				HandoutHelper::_returnTo ( 'cat_view', $err, $doc->catid );
			}
		}
		
		//checkout the document

		$doc->checkout ( $_HANDOUT_USER->userid );
		
		// Set document filename

		if (! is_null ( $filename )) {
			$filename = HANDOUT_Utils::safeDecodeURL ( $filename );
			$doc->docfilename = $filename;
		}
		
		// Set document url

		$prefix = substr ( $doc->docfilename, 0, COM_HANDOUT_DOCUMENT_LINK_LNG );
		if (strcasecmp ( $prefix, COM_HANDOUT_DOCUMENT_LINK ) == 0) {
			$doc->handoutlink = substr ( $doc->docfilename, COM_HANDOUT_DOCUMENT_LINK_LNG );
			$doc->docfilename = COM_HANDOUT_DOCUMENT_LINK;
		}
		
		$lists = array ();
		
		// Set filename

		$lists ['docfilename'] = DocumentsHelper::filesSelectList ( $doc );
		
		// Built category select list

		$options = array (JHTML::_ ( 'select.option', '0', JText::_('COM_HANDOUT_SELECT_CAT'), 'value', 'text' ) );
		if ($uid) {
			$lists ['catid'] = HandoutHTML::categoryList ( $doc->catid, "", $options );
		} else {
			$lists ['catid'] = HandoutHTML::categoryList ( $catid, "", $options );
		}
		
		// Build select lists

		$lists ['published'] = JHTML::_ ( 'select.booleanlist', 'published', 'class="inputbox"', $doc->published, 'yes', 'no', false );
		
		$lists ['viewer'] = HandoutHTML::viewerList ( $doc, 'docowner' );
		$lists ['maintainer'] = HandoutHTML::maintainerList ( $doc, 'docmaintainedby' );
		
		$lists ['licenses'] = DocumentsHelper::licenseSelectList ( $doc );
		$lists ['licenses_display'] = DocumentsHelper::licenseDisplayList ( $doc );
		
		// Built image list

		$lists ['docthumbnail'] = HandoutHTML::imageList ( 'docthumbnail', $doc->docthumbnail );
		$lists ['docthumbnail_preview'] = $doc->docthumbnail;
		
		// Find lastupdate user

		$last = array ();
		if ($doc->doclastupdateby > COM_HANDOUT_PERMIT_USER) {
			$database->setQuery ( "SELECT id, name " . "\n FROM #__users " . "\n WHERE id=" . ( int ) $doc->doclastupdateby );
			$last = $database->loadObjectList ();
		} else {
			$last [0]->name = "Super Administrator";
		}
		
		// Find createdby user

		$created = array ();
		if ($doc->docsubmittedby > COM_HANDOUT_PERMIT_USER) {
			$database->setQuery ( "SELECT id, name " . "\n FROM #__users " . "\n WHERE id=" . ( int ) $doc->docsubmittedby );
			$created = $database->loadObjectList ();
		} else {
			$created [0]->name = "Super Administrator";
		}
		
		// update 'doclastupdateon'

		$doc->doclastupdateon = date ( "Y-m-d H:i:s" );
		
		// Params definitions

		$params = null;
		$params_path = JPATH_ROOT . '/administrator/components/com_handout/handout.params.xml';
		if (file_exists ( $params_path )) {
			$params =  new HandoutParametersHandler ( $doc->attribs, $params_path, 'params' );
		}
		
		/* ------------------------------ *

     *   PLUGIN - Setup All Plugins   *

     * ------------------------------ */
		$prebot = new HANDOUT_plugin ( 'onBeforeEditDocument' );
		$prebot->setParm ( 'document', $doc );
		$prebot->setParm ( 'filename', $filename );
		$prebot->setParm ( 'user', $_HANDOUT_USER );
		
		if (! $uid) {
			$prebot->copyParm ( 'process', 'new document' );
		} else {
			$prebot->copyParm ( 'process', 'edit document' );
		}
		
		$prebot->trigger ();
		
		if ($prebot->getError ()) {
			HandoutHelper::_returnTo ( 'cat_view', $prebot->getErrorMsg () );
		}
		
		return HTML_HandoutDocuments::editDocumentForm ( $doc, $lists, $last, $created, $params );
	}
	
	function checkMoveDocument($gid) {
		//check if user can move documents
		$database = &JFactory::getDBO ();
		$_HANDOUT_USER = &HandoutFactory::getHandoutUser ();
		
		$doc = new HandoutDocument ( $database );
		$doc->load ( $gid );
		
		//check user permissions
		$err = $_HANDOUT_USER->canPreformTask ( $doc, 'Move' );
		if ($err) {
			HandoutHelper::_returnTo ( 'cat_view', $err, $doc->catid );
		}
	}
	
	function fetchMoveDocumentCategories($gid) {			
		$doc = new HANDOUT_Document ( $gid );
		
		// category select list
		$options = array (JHTML::_ ( 'select.option', '0', JText::_('COM_HANDOUT_SELECT_CAT') ) );
		$lists ['categories'] = HandoutHTML::categoryList ( $doc->getData ( 'catid' ), "", $options );
		
		return $lists;		
	}
	
	function moveDocumentProcess($uid) {
		HANDOUT_token::check () or die ( 'Invalid Token' );
		
		$database = &JFactory::getDBO ();
		$_HANDOUT_USER = &HandoutFactory::getHandoutUser ();
		
		$doc = new HandoutDocument ( $database );
		$doc->load ( $uid );
		
		//check user permissions

		$err = $_HANDOUT_USER->canPreformTask ( $doc, 'Move' );
		if ($err) {
			HandoutHelper::_returnTo ( 'cat_view', $err, $doc->catid );
		}
		
		// get the id of the category to move the document to

		$move = JRequest::getInt ( 'catid' );
		
		// preform move

		$doc = new HandoutDocument ( $database );
		$doc->move ( array ($uid ), $move );
		
		HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_DOCMOVED'), $move );
	}
	
	function updateDocumentProcess($uid) {
		HANDOUT_token::check () or die ( 'Invalid Token' );
		
		$step = ( int ) JRequest::getVar('step', 1); 
		$uploaded = JRequest::getVar( HANDOUT_Utils::stripslashes ( $_FILES ), 'upload' );
		uploadFile ( $step, $uploaded );
	}
	
	
	function publishDocument($cid, $publish = 1) {
		HANDOUT_token::check () or die ( 'Invalid Token' );
		
		$database = &JFactory::getDBO();
		$_HANDOUT_USER = &HandoutFactory::getHandoutUser();
		
		$doc = new HandoutDocument ( $database );
		$doc->load ( $cid [0] );
		
		//check user permissions

		$task = $publish ? 'Publish' : 'UnPublish';
		$err = $_HANDOUT_USER->canPreformTask ( $doc, $task );
		if ($err) {
			HandoutHelper::_returnTo ( 'cat_view', $err, $doc->catid );
		}
		
		//publish the document

		$doc->publish ( $cid, $publish );
		
		HandoutHelper::_returnTo ( 'cat_view', '', $doc->catid );
	}
	
	function saveDocument($uid) {
		//HANDOUT_token::check () or die ( 'Invalid Token' );
		
		$database = &JFactory::getDBO ();
		$_HANDOUT_USER = &HandoutFactory::getHandoutUser ();
		
		//fetch params

		$params = JRequest::getString ( 'params', '' );
		if (is_array ( $params )) {
			$txt = array ();
			foreach ( $params as $k => $v ) {
				$txt [] = "$k=$v";
			}
			$_POST ['attribs'] = implode ( "\n", $txt );
		}
		
		$doc = new HandoutDocument ( $database );
		$doc->load ( $uid ); // Load from id

		$doc->bind ( HANDOUT_Utils::stripslashes ( $_POST ) );
		
		/* ------------------------------ *

     *   PLUGIN - Setup All Plugins   *

     * ------------------------------ */
		$logbot = new HANDOUT_plugin ( 'onLog' );
		$postbot = new HANDOUT_plugin ( 'onAfterEditDocument' );
		$logbot->setParm ( 'document', $doc );
		$logbot->setParm ( 'file', $_POST ['docfilename'] );
		$logbot->setParm ( 'user', $_HANDOUT_USER );
		
		if (! $uid) {
			$logbot->copyParm ( 'process', 'new document' );
		} else {
			$logbot->copyParm ( 'process', 'edit document' );
		}
		$logbot->copyParm ( 'new', ! $uid );
		$postbot->setParmArray ( $logbot->getParm () );
		
		$postbot->trigger ();
		if ($postbot->getError ()) {
			$logbot->copyParm ( 'msg', $postbot->getErrorMsg () );
			$logbot->copyParm ( 'status', 'LOG_ERROR' );
			$logbot->trigger ();
			HandoutHelper::_returnTo ( 'cat_view', $postbot->getErrorMsg () );
		}
		
		// let's indicate last update information to store

		if ($doc->save ()) {
			$logbot->copyParm ( 'msg', 'Document saved' );
			$logbot->copyParm ( 'status', 'LOG_OK' );
			$logbot->trigger ();
			
			// if submited for the first time lets do auto-publish operation

			if (! $uid) {
				DocumentsHelper::autoPublish ( $doc );
			}
			/* removed $message: undefined

         * original code:

         * HandoutHelper::_returnTo('cat_view', JText::_('COM_HANDOUT_THANKSHANDOUT') . $message ? "<br />" . $message : '', $doc->catid);

         */
			HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_THANKS_FOR_SUBMISSION'), $doc->catid );
		}
		// doc->save failed. Log error

		$logbot->copyParm ( 'msg', $doc->getError () );
		$logbot->copyParm ( 'status', 'LOG_ERROR' );
		$logbot->trigger ();
		
		HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_PROBLEM_SAVING_DOCUMENT') );
	}
	
	function cancelDocument($gid) {
		$database = &JFactory::getDBO ();
		
		$uid = JRequest::getInt ( 'id' );
		
		if (! $uid) {
			HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_OP_CANCELED') );
		}
		
		$doc = new HandoutDocument ( $database );
		$doc->load ( $uid );
		
		if ($doc->cancel ()) {
			HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_OP_CANCELED'), $doc->catid );
		}
		
		HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_OP_CANCELED') );
	}
	
	function checkinDocument($uid) {
		$database = &JFactory::getDBO();
		$_HANDOUT_USER = &HandoutFactory::getHandoutUser();
		
		$doc = new HandoutDocument ( $database );
		$doc->load ( $uid );
		
		//check user permissions

		$err = $_HANDOUT_USER->canPreformTask ( $doc, 'CheckIn' );
		if ($err) {
			HandoutHelper::_returnTo ( 'cat_view', $err, $doc->catid );
		}
		
		//checkin the document

		$doc->checkin ();
		$msg = "&quot;" . $doc->docname . "&quot; " . JText::_('COM_HANDOUT_CHECKED_IN');
		
		HandoutHelper::_returnTo ( 'cat_view', $msg, $doc->catid );
	}
	
	function checkoutDocument($uid) {
		$database = &JFactory::getDBO();
		$_HANDOUT_USER = &HandoutFactory::getHandoutUser();
		
		$doc = new HandoutDocument ( $database );
		$doc->load ( $uid );
		
		//check user permissions

		$err = $_HANDOUT_USER->canPreformTask ( $doc, 'CheckOut' );
		if ($err) {
			HandoutHelper::_returnTo ( 'cat_view', $err, $doc->catid );
		}
		
		//checkout the document

		$doc->checkout ( $_HANDOUT_USER->userid );
		$msg = "&quot;" . $doc->docname . "&quot; " . JText::_('COM_HANDOUT_CHECKED_OUT');
		
		HandoutHelper::_returnTo ( 'cat_view', $msg, $doc->catid );
	}
	
	function resetDocument($uid) {
		$database = &JFactory::getDBO();
		$_HANDOUT_USER = &HandoutFactory::getHandoutUser();
		
		$doc = new HandoutDocument ( $database );
		$doc->load ( $uid );
		
		//check user permissions

		$err = $_HANDOUT_USER->canPreformTask ( $doc, 'Reset' );
		if ($err) {
			HandoutHelper::_returnTo ( 'cat_view', $err, $doc->catid );
		}
		
		//reset the document counter

		$doc->doccounter = 0;
		$doc->store ();
		
		HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_RESET_COUNTER'), $doc->catid );
	}
	
	function deleteDocument($uid) {		
		$database = &JFactory::getDBO();
		$_HANDOUT_USER = &HandoutFactory::getHandoutUser();
		
		$doc = new HandoutDocument ( $database );
		$doc->load ( $uid );
		
		//check user permissions

		$err = $_HANDOUT_USER->canPreformTask ( $doc, 'Delete' );
		if ($err) {
			HandoutHelper::_returnTo ( 'cat_view', $err, $doc->catid );
		}
		
		//delete the docmument

		$doc->remove ( array ($uid ) );
		HandoutHelper::_returnTo ( 'cat_view', JText::_('COM_HANDOUT_DOCDELETED'), $doc->catid );
	}
	
	function autoPublish($doc) {
		$database = &JFactory::getDBO ();
		$handout = &HandoutFactory::getHandout ();
		
		$publish = $handout->getCfg ( 'user_publish' );
		if ($publish == COM_HANDOUT_PERMIT_EVERYONE) {
			//$doc = new HandoutDocument($database);

			$doc->publish ( array ($doc->id ), 1 );
		}
	}
	
	function licenseSelectList(&$doc) {
		
		$database = &JFactory::getDBO ();
		
		$database->setQuery ( "SELECT id, name FROM #__handout_licenses ORDER BY name ASC" );
		$result = $database->loadObjectList ();
		
		$options = array ();
		$options [] = JHTML::_ ( 'select.option', '0', JText::_('COM_HANDOUT_NO_AGREEMENT') );
		
		foreach ( $result as $license ) {
			$options [] = JHTML::_ ( 'select.option', $license->id, $license->name );
		}
		
		$selected = $doc->doclicense_id;
		
		$std_opt = 'class="inputbox" size="1"';
		$list = JHTML::_ ( 'select.genericlist', $options, 'doclicense_id', $std_opt, 'value', 'text', $selected );
		return $list;
	}
	
	function filesSelectList(&$doc) {
		/*

	 * PROGRAMMER NOTE:

     * Do NOT use FULL url for description on links. This could expose passwords

     * (Not a wise idea though they could get them elsewhere in the system...)

     */
		
		$options = array ();
		$selected = null;
		
		if (! $doc->id) {
			if ($doc->docfilename == COM_HANDOUT_DOCUMENT_LINK) {
				//create options

				$options [] = JHTML::_ ( 'select.option', COM_HANDOUT_DOCUMENT_LINK, JText::_('COM_HANDOUT_LINKED') );
				$selected = COM_HANDOUT_DOCUMENT_LINK;
				
				//change document data

				$parsed_url = parse_url ( $doc->handoutlink );
				
				$doc->docname = JText::_('COM_HANDOUT_LINKTO') . (isset ( $parsed_url ['path'] ) ? basename ( $parsed_url ['path'] ) : $parsed_url ['host']);
				
				$doc->docdescription = "\n" . JText::_('COM_HANDOUT_DOCLINKTO') . ':' . $parsed_url ['scheme'] . '://' . $parsed_url ['host'] . (isset ( $parsed_url ['path'] ) ? $parsed_url ['path'] : '') . (isset ( $parsed_url ['query'] ) ? $parsed_url ['query'] : '') . "\n\n" . JText::_('COM_HANDOUT_DOCLINKON') . ':' . strftime ( "%a, %Y-%b-%d %R" );
			} else {
				//create options

				$options [] = JHTML::_ ( 'select.option', $doc->docfilename );
				$selected = $doc->docfilename;
				
				//change document data

				$doc->docname = substr ( $doc->docfilename, 0, strrpos ( $doc->docfilename, "." ) );
			}
		} else {
			//create options

			$options [] = JHTML::_ ( 'select.option', '', JText::_('COM_HANDOUT_SELECT_FILE'), 'value', 'text' );
			
			if (! is_null ( $doc->id )) {
				$options [] = JHTML::_ ( 'select.option', $doc->docfilename, JText::_('COM_HANDOUT_CURRENT_DOCUMENT') . ': ' . $doc->docfilename, 'value', 'text' );
			}
			
			$files = HANDOUT_docs::getFilesByUserAccess ();
			foreach ( $files as $file ) {
				if (is_null ( $doc->id ) || $file->docfilename != $doc->docfilename) {
					$options [] = JHTML::_ ( 'select.option', $file->docfilename, '', 'value', 'text' );
				}
			}
			
			if (count ( $options ) < 2) {
				//HandoutHelper::_returnTo('upload', JText::_('COM_HANDOUT_YOU_MUST_UPLOAD'));

			}
			
			$selected = $doc->docfilename;
		}
		
		$std_opt = 'class="inputbox" size="1"';
		$list = JHTML::_ ( 'select.genericlist', $options, 'docfilename', $std_opt, 'value', 'text', $selected, null, false, false );
		return $list;
	}
	
	function licenseDisplayList(&$doc) {
		$options = array ();
		$options [] = JHTML::_ ( 'select.option', '0', JText::_('COM_HANDOUT_NO') );
		$options [] = JHTML::_ ( 'select.option', '1', JText::_('COM_HANDOUT_YES') );
		
		$selected = $doc->doclicense_display;
		
		$std_opt = 'class="inputbox" size="1"';
		$list = JHTML::_ ( 'select.genericlist', $options, 'doclicense_display', $std_opt, 'value', 'text', $selected );
		return $list;
	}
}
?>