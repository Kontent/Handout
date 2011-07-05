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

include_once dirname(__FILE__) . '/documents.html.php';

require_once ($_HANDOUT->getPath('classes' , 'file'));
require_once($_HANDOUT->getPath('classes', 'plugins'));
include_once($_HANDOUT->getPath('classes', 'params'));

$task = JRequest::getCmd('task');

JArrayHelper::toInteger( $cid );
switch ($task) {
    case "publish" :
        publishDocument($cid, 1);
        break;
    case "unpublish":
        publishDocument($cid, 0);
        break;
    case "new":
        editDocument(0);
        break;
    case "edit":
        editDocument($cid[0]);
        break;
    case "move_form":
        moveDocumentForm($cid);
        break;
    case "move_process":
        moveDocumentProcess($cid);
        break;
    case "copy_form":
        copyDocumentForm($cid);
        break;
    case "copy_process":
        copyDocumentProcess($cid);
        break;
    case "delete":
    case "remove":
        removeDocument($cid);
        break;
    case "apply":
    case "save":
        saveDocument();
        break;
    case "cancel":
        cancelDocument();
        break;
    case "download" :
        $bid = JRequest::getVar( 'bid', 0);
        downloadDocument($bid);
        break;
    case "show":
    default :
        showDocuments($pend, $sort, 0);
}

function showDocuments($pend, $sort, $view_type)
{
    global $_HANDOUT;
    require_once($_HANDOUT->getPath('classes', 'utils'));

    $database = &JFactory::getDBO();
    $mainframe = &JFactory::getApplication();
    $option = JRequest::getCmd('option');
    $section = JRequest::getCmd('section');
	$list_limit = $mainframe->getCfg('list_limit');
    global $menutype;

    $catid = $mainframe->getUserStateFromRequest("catidarc{option}{$section}", 'catid', 0);
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $list_limit);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}{$section}limitstart", 'limitstart', 0);
    $levellimit = $mainframe->getUserStateFromRequest("view{$option}{$section}limit", 'levellimit', 10);

    $search = $mainframe->getUserStateFromRequest("searcharc{$option}{$section}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));

    $where = array();

    if ($catid > 0) {
        $where[] = "a.catid=$catid";
    }
    if ($search) {
        $where[] = "LOWER(a.docname) LIKE '%$search%'";
    }
    if ($pend == 'yes') {
        $where[] = "a.published LIKE '0'";
    }
    // get the total number of records
    $query = "SELECT count(*) "
     . "\n FROM #__handout AS a"
     . (count($where) ? "\n WHERE " . implode(' AND ', $where) : "");
    $database->setQuery($query);
    $total = $database->loadResult();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }
    // $where[] = "a.catid=cc.id";
    if ($sort == 'filename') {
        $sorttemp = "a.docfilename";
    } else if ($sort == 'name') {
        $sorttemp = "a.docname";
    } else if ($sort == 'date') {
        $sorttemp = "a.docdate_published";
    } else {
        $sorttemp = "a.catid,a.docname";
    }

    $query = "SELECT a.*, cc.name AS category, u.name AS editor"
     . "\n FROM #__handout AS a"
     . "\n LEFT JOIN #__users AS u ON u.id = a.checked_out"
     . "\n LEFT JOIN #__categories AS cc ON cc.id = a.catid"
     . (count($where) ? "\n WHERE " . implode(' AND ', $where) : "")
     . "\n ORDER BY " . $sorttemp . " ASC" ;
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    jimport('joomla.html.pagination');
    $pageNav = new JPagination($total, $limitstart, $limit);

    // slice out elements based on limits
    $rows = array_slice($rows, $pageNav->limitstart, $pageNav->limit);
    // add category name
    $list = HANDOUT_utils::categoryArray();
    for ($i = 0, $n = count($rows);$i < $n;$i++) {
        $row = &$rows[$i];
        $row->treename = array_key_exists($row->catid , $list) ?
        $list[$row->catid]->treename : '(orphan)';
    }
    // get list of categories
    $options = array();
    $options[] = HandoutHTML::_('select.option','0', JText::_('COM_HANDOUT_SELECT_CAT'));
    $options[] = HandoutHTML::_('select.option','-1', JText::_('COM_HANDOUT_ALL_CATS'));
    $lists['catid'] = HandoutHTML::categoryList($catid, "document.adminForm.submit();", $options);

    // get pending documents
    $database->setQuery("SELECT count(*) FROM #__handout WHERE published=0");
    $number_unpublished = $database->loadResult();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }
    if(JRequest::getString('task') == 'element'){
        HTML_HandoutDocuments::showDocumentsToSelect($rows, $lists, $search, $pageNav, $number_unpublished, $view_type);
    } else {
        HTML_HandoutDocuments::showDocuments($rows, $lists, $search, $pageNav, $number_unpublished, $view_type);
    }
}

/*
*    @desc Edit a document entry
*/
function editDocument($uid)
{


	require_once (JPATH_ROOT ."/administrator/components/com_handout/classes/HANDOUT_utils.class.php");
    require_once (JPATH_ROOT ."/administrator/components/com_handout/classes/HANDOUT_params.class.php");

    $database = &JFactory::getDBO();
    $user = &JFactory::getUser();
    global $_HANDOUT, $_HANDOUT_USER;

    // disable the main menu to force user to use buttons
    $_REQUEST['hidemainmenu']=1;

    $request = HANDOUT_Utils::stripslashes($_REQUEST);
    $uploaded_file = isset($request['uploaded_file']) ? $request['uploaded_file'] : '';
	//$uploaded_file = JRequest::getString("uploaded_file", "",HANDOUT_Utils::stripslashes($_REQUEST));

    $doc = new HandoutDocument($database);
    if ($uid) {
        $doc->load($uid);
        if ($doc->checked_out) {
            if ($doc->checked_out <> $user->id) {
                $mainframe = &JFactory::getApplication(); $mainframe->redirect("index.php?option=$option", JText::_('COM_HANDOUT_THE_MODULE') . " $row->title " . JText::_('COM_HANDOUT_IS_BEING'));
            }
        } else { // check out document...
            $doc->checkout($user->id);
        }
    } else {
        $doc->init_record();
    }

    // Begin building interface information...
    $lists = array();

    $lists['document_url']        = ''; //make sure
    $lists['original_docfilename'] = $doc->docfilename;
    if (strcasecmp(substr($doc->docfilename , 0, COM_HANDOUT_DOCUMENT_LINK_LNG) , COM_HANDOUT_DOCUMENT_LINK) == 0) {
        $lists['document_url'] = substr($doc->docfilename , COM_HANDOUT_DOCUMENT_LINK_LNG);
        $doc->docfilename = COM_HANDOUT_DOCUMENT_LINK ;
    }

    // category select list
    $options = array(JHTML::_('select.option','0', JText::_('COM_HANDOUT_SELECT_CAT')));
    $lists['catid'] = HandoutHTML::categoryList($doc->catid, "", $options);
    // check if we have at least one category defined
    $database->setQuery("SELECT id " . "\n FROM #__categories " . "\n WHERE section='com_handout'", 0, 1);

    if (!$checkcats = $database->loadObjectList()) {
        $mainframe = &JFactory::getApplication(); $mainframe->redirect("index.php?option=com_handout&section=categories", JText::_('COM_HANDOUT_PLEASE_SEL_CAT'));
    }

    // select lists
    $lists['published'] = JHTML::_('select.booleanlist','published', 'class="inputbox"', $doc->published);

    // licenses list
    $database->setQuery("SELECT id, name " . "\n FROM #__handout_licenses " . "\n ORDER BY name ASC");
    $licensesTemp = $database->loadObjectList();
    $licenses[] = JHTML::_('select.option','0', JText::_('COM_HANDOUT_NO_AGREEMENT'));

    foreach($licensesTemp as $licensesTemp) {
        $licenses[] = JHTML::_('select.option',$licensesTemp->id, $licensesTemp->name);
    }

    $lists['licenses'] = JHTML::_('select.genericlist',$licenses, 'doclicense_id',
        'class="inputbox" size="1"', 'value', 'text', $doc->doclicense_id);

    // licenses display list
    $licenses_display[] = JHTML::_('select.option','0', JText::_('COM_HANDOUT_NO'));
    $licenses_display[] = JHTML::_('select.option','1', JText::_('COM_HANDOUT_YES'));;
    $lists['licenses_display'] = JHTML::_('select.genericlist',$licenses_display,
        'doclicense_display', 'class="inputbox" size="1"', 'value', 'text', $doc->doclicense_display);

	//languages list
	$lists['languages'];
	$xml = new JSimpleXML;
	$xml->loadFile(COM_HANDOUT_DOC_LANGUAGE_XML);
	foreach( $xml->document->language as $lang ) {
	   $newlang['name'] = $lang->getElementByPath('name')->data();
	   $newlang['code'] = $lang->getElementByPath('code')->data();
		$lists['languages'][] = $newlang;
	 }

    if ($uploaded_file == '')
    {
        // Create docs List
        $handout_path      = $_HANDOUT->getCfg('handoutpath');
        $fname_reject = $_HANDOUT->getCfg('fname_reject');

        $docFiles = JFolder::files($handout_path);
        $docs = array(JHTML::_('select.option','', JText::_('COM_HANDOUT_SELECT_FILE')));
        $docs[] = JHTML::_('select.option',COM_HANDOUT_DOCUMENT_LINK , JText::_('COM_HANDOUT_LINKED'));

        if ( count($docFiles) > 0 )
        {
            foreach ( $docFiles as $file )
            {

                if ( substr($file,0,1) == '.' ) continue; //ignore files starting with .
                if ( @is_dir($handout_path . '/' . $file) ) continue; //ignore directories
                if ( $fname_reject && preg_match("/^(".$fname_reject.")$/i", $file) ) continue; //ignore certain filenames
                if ( preg_match("/^(".COM_HANDOUT_FNAME_REJECT.")$/i", $file) ) continue; //ignore certain filenames

                $docs[] = JHTML::_('select.option',$file);
				/*
               	//$query = "SELECT * FROM #__handout WHERE docfilename='" . $database->getEscaped($file) . "'";
              	//$database->setQuery($query);
             	//if (!($result = $database->query())) {
                //	echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
             	//}

                //if ($database->getNumRows($result) == 0 || $doc->docfilename == $file) {
                    $docs[] = JHTML::_('select.option',$file);
                //}
				*/
            } //end foreach $docsFiles
        }

        if ( count($docs) < 1 ) {
            $mainframe = &JFactory::getApplication(); $mainframe->redirect("index.php?option=$option&task=upload", JText::_('COM_HANDOUT_YOU_MUST_UPLOAD'));
        }

        $lists['docfilename'] = JHTML::_('select.genericlist',$docs, 'docfilename',
            'class="inputbox" size="1"', 'value', 'text', $doc->docfilename);
    } else { // uploaded_file isn't blank

    	$filename = preg_split("/\./", $uploaded_file);
     	$row->docname = $filename[0];

        $docs = array(JHTML::_('select.option',$uploaded_file));
        $lists['docfilename'] = JHTML::_('select.genericlist',$docs, 'docfilename',
            'class="inputbox" size="1"', 'value', 'text', $doc->docfilename);
    } // endif uploaded_file

    // permissions lists
    $lists['viewer']     = HandoutHTML::viewerList($doc, 'docowner');
    $lists['maintainer'] = HandoutHTML::maintainerList($doc, 'docmaintainedby');

    // updater user information
    $last = array();
    if ($doc->doclastupdateby > '0' && $doc->doclastupdateby != $user->id) {
        $database->setQuery("SELECT id, name FROM #__users WHERE id=" . (int) $doc->doclastupdateby);
        $last = $database->loadObjectList();
    } else $last[0]->name = $user->name ? $user->name : $user->username; // "Super Administrator"

    // creator user information
    $created = array();
    if ($doc->docsubmittedby > '0' && $doc->docsubmittedby != $user->id) {
        $database->setQuery("SELECT id, name FROM #__users WHERE id=". (int) $doc->docsubmittedby);
        $created = $database->loadObjectList();
    } else $created[0]->name = $user->name ? $user->name : $user->username; // "Super Administrator"

    // Imagelist
    $lists['image'] = HandoutHTML::imageList('docthumbnail', $doc->docthumbnail);

    // Params definitions
    $params_path = JPATH_ROOT . '/administrator/components/com_handout/handout.params.xml';
	if(file_exists($params_path)) {
		$params = new HandoutParametersHandler( $doc->attribs, $params_path , 'params' );
	}

	/* ------------------------------ *
     *   PLUGIN - Setup All Plugins   *
     * ------------------------------ */
    $prebot = new HANDOUT_plugin('onBeforeEditDocument');
    $prebot->setParm('document' , $doc);
    $prebot->setParm('filename' , $filename);
    $prebot->setParm('user'     , $_HANDOUT_USER);

     if (!$uid) {
        $prebot->copyParm('process' , 'new document');
    } else {
        $prebot->copyParm('process' , 'edit document');
    }

    $prebot->trigger();

    if ($prebot->getError()) {
    	$mainframe = &JFactory::getApplication(); $mainframe->redirect("index.php?option=com_handout&section=documents", $prebot->getErrorMsg());
    }

    HTML_HandoutDocuments::editDocument($doc, $lists, $last, $created, $params);
}

function removeDocument($cid)
{
    HANDOUT_token::check() or die('Invalid Token');
    $database = &JFactory::getDBO();

    $doc = new HandoutDocument($database);
    if ($doc->remove($cid)) {
        $mainframe = &JFactory::getApplication(); $mainframe->redirect("index.php?option=com_handout&section=documents");
    } else {
    	echo "<script> alert('Problem removing documents'); window.history.go(-1);</script>\n";
        exit();
    }
}

function cancelDocument()
{
    $database = &JFactory::getDBO();

    $doc = new HandoutDocument($database);
    if ($doc->cancel()) {
        $mainframe = &JFactory::getApplication(); $mainframe->redirect("index.php?option=com_handout&section=documents");
    }
}

function publishDocument($cid, $publish = 1)
{
    HANDOUT_token::check() or die('Invalid Token');
    $database = &JFactory::getDBO();

    $doc = new HandoutDocument($database);
    if ($doc->publish($cid, $publish)) {
        $mainframe = &JFactory::getApplication(); $mainframe->redirect("index.php?option=com_handout&section=documents");
    }
}


/*
*    @desc Saves a document
*/

function saveDocument()
{
    HANDOUT_token::check() or die('Invalid Token');

    $database = &JFactory::getDBO();
    global $task, $_HANDOUT_USER;


	//fetch current id
    $cid = (int) JRequest::getVar( 'id' , 0);

    //fetch params
    $params = JRequest::getVar( 'params', '' );
	if (is_array( $params )) {
		$txt = array();
		foreach ($params as $k=>$v) {
			$txt[] = "$k=$v";
		}
		$_POST['attribs'] = implode( "\n", $txt );
	}

    $doc = new HandoutDocument($database); // Create record
    $doc->load($cid); // Load from id
    $doc->bind(HANDOUT_Utils::stripslashes($_POST) );

     /* ------------------------------ *
     *   PLUGIN - Setup All Plugins   *
     * ------------------------------ */
    $logbot = new HANDOUT_plugin('onLog');
    $postbot = new HANDOUT_plugin('onAfterEditDocument');
    $logbot->setParm('document' , $doc);
    $logbot->setParm('file'     , HANDOUT_Utils::stripslashes($_POST['docfilename']));
    $logbot->setParm('user'     , $_HANDOUT_USER);

     if (!$cid) {
        $logbot->copyParm('process' , 'new document');
    } else {
        $logbot->copyParm('process' , 'edit document');
    }
    $logbot->copyParm('new' , !$cid);
    $postbot->setParmArray($logbot->getParm());

     $postbot->trigger();
    if ($postbot->getError()) {
      	$logbot->copyParm('msg' , $postbot->getErrorMsg());
       	$logbot->copyParm('status' , 'LOG_ERROR');
        $logbot->trigger();
        $mainframe = &JFactory::getApplication(); $mainframe->redirect("index.php?option=com_handout&section=documents", $postbot->getErrorMsg());
   	}

    if ($doc->save()) { // Update from browser
    	$logbot->copyParm('msg' , 'Document saved');
        $logbot->copyParm('status' , 'LOG_OK');
        $logbot->trigger();

        if( $task == 'save' ) {
            $url = 'index.php?option=com_handout&section=documents';
        } else { // $task = 'apply'
            $url = 'index.php?option=com_handout&section=documents&task=edit&cid[0]='.$doc->id;
        }

        $mainframe = &JFactory::getApplication(); $mainframe->redirect( $url, JText::_('COM_HANDOUT_SAVED_CHANGES'));
    }

    $logbot->copyParm('msg' , $doc->getError());
    $logbot->copyParm('status' , 'LOG_ERROR');
    $logbot->trigger();

    $mainframe = &JFactory::getApplication(); $mainframe->redirect( 'index.php?option=com_handout&section=documents', $doc->getError());
}

function downloadDocument($bid)
{
    $database = &JFactory::getDBO(); $_HANDOUT = &HandoutFactory::getHandout();
    // load document
    $doc = new HandoutDocument($database);
    $doc->load($bid);
    // download file
    $file = new HANDOUT_File($doc->docfilename, $_HANDOUT->getCfg('handoutpath'));
    $file->download();
    die; // Important!
}

function moveDocumentForm($cid)
{
    $database = &JFactory::getDBO();

    if (!is_array($cid) || count($cid) < 1) {
        echo "<script> alert('".JText::_('COM_HANDOUT_SELECT_ITEM_MOVE')."'); window.history.go(-1);</script>\n";
        exit;
    }
    // query to list items from documents
    $cids = implode(',', $cid);
    $query = "SELECT docname FROM #__handout WHERE id IN ( " . $cids . " ) ORDER BY id, docname";
    $database->setQuery($query);
    $items = $database->loadObjectList();
    // category select list
    $options = array(JHTML::_('select.option','1', JText::_('COM_HANDOUT_SELECT_CAT')));
    $lists['categories'] = HandoutHTML::categoryList("", "", $options);

    HTML_HandoutDocuments::moveDocumentForm($cid, $lists, $items);
}

function moveDocumentProcess($cid)
{
    HANDOUT_token::check() or die('Invalid Token');
    $database = &JFactory::getDBO();
    $user = &JFactory::getUser();

    // get the id of the category to move the document to
    $categoryMove = JRequest::getVar( 'catid', '');
    // preform move
    $doc = new HandoutDocument($database);
    $doc->move($cid, $categoryMove);
    // output status message
    $cids = implode(',', $cid);
    $total = count($cid);

    $cat = new HandoutCategory ($database);
    $cat->load($categoryMove);

    $msg = $total . ' '.JText::_('COM_HANDOUT_DOCUMENTS_MOVED_TO').' '. $cat->name;
    $mainframe = &JFactory::getApplication(); $mainframe->redirect('index.php?option=com_handout&section=documents',  $msg);
}

function copyDocumentForm($cid)
{
    $database = &JFactory::getDBO();

    if (!is_array($cid) || count($cid) < 1) {
        echo "<script> alert('".JText::_('COM_HANDOUT_SELECT_ITEM_COPY')."'); window.history.go(-1);</script>\n";
        exit;
    }
    // query to list items from documents
    $cids = implode(',', $cid);
    $query = "SELECT docname FROM #__handout WHERE id IN ( " . $cids . " ) ORDER BY id, docname";
    $database->setQuery($query);
    $items = $database->loadObjectList();
    // category select list
    $options = array(JHTML::_('select.option','1', JText::_('COM_HANDOUT_SELECT_CAT')));
    $lists['categories'] = HandoutHTML::categoryList("", "", $options);

    HTML_HandoutDocuments::copyDocumentForm($cid, $lists, $items);
}

function copyDocumentProcess($cid)
{
    HANDOUT_token::check() or die('Invalid Token');

    $database = &JFactory::getDBO();
	$user = &JFactory::getUser();

	// get the id of the category to copy the document to
    $categoryCopy = JRequest::getVar( 'catid', '');
    // preform move
    $doc = new HandoutDocument($database);
    $doc->copy($cid, $categoryCopy);
    // output status message
    $cids = implode(',', $cid);
    $total = count($cid);

    $cat = new HandoutCategory ($database);
    $cat->load($categoryCopy);

    $msg = $total . ' '.JText::_('COM_HANDOUT_DOCUMENTS_COPIED_TO').' '. $cat->name;
    $mainframe = &JFactory::getApplication(); $mainframe->redirect('index.php?option=com_handout&section=documents',  $msg);
}
