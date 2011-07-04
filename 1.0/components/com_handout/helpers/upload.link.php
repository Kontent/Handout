<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: upload.link.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_LINK_TRANSFER')) {
    return true;
} else {
    define('_HANDOUT_LINK_TRANSFER' , 1);
}

class HandoutUploadMethod
{
    function fetchMethodForm($uid, $step, $update = false)
    {
        $task = JRequest::getCmd('task');

        switch ($step)
        {
            case 2: // Input the remote URL(Form)
            {
                $lists = array();
                $lists['action']    = HandoutHelper::_taskLink($task, $uid, array('step' => $step + 1), false);
                
				return $lists;
            } break;

            case 3: // Create a link
            {
                $url = stripslashes(JRequest::getVar( 'url' , 'http://'));
                $err = HandoutUploadMethod::linkFileProcess($uid, $step, $url);
                if($err['_error']) {
                	HandoutHelper::_returnTo($task, $err['_errmsg'], '', array("method" => 'link' ,"step" => $step - 1 ,"localfile" => '' , "url" => HANDOUT_Utils::safeEncodeURL($url)));
                }

                $uploaded = HANDOUT_Utils::safeEncodeURL(COM_HANDOUT_DOCUMENT_LINK . $url);

                $catid = $update ? 0 : $uid;
                $docid = $update ? $uid : 0;

                return DocumentsHelper::fetchEditDocumentForm($docid , $uploaded, $catid);
            } break;

            default:
                break;
        }
        return true;
    }

    function linkFileProcess($uid, $step, $url)
    {
        HANDOUT_token::check() or die('Invalid Token');
        
        $_HANDOUT_USER = &HandoutFactory::getHandoutUser();
        $_HANDOUT = &HandoutFactory::getHandout();

        if ($url == '') {
        	return array(
				'_error' => 1,
				'_errmsg'=> JText::_('COM_HANDOUT_FILENAME_REQUIRED')
         	);
        }

    	$path = $_HANDOUT->getCfg('handoutpath');

   		//get file validation settings
   		if ($_HANDOUT_USER->isSpecial) {
      		$validate = COM_HANDOUT_VALIDATE_ADMIN;
   		} else {
     		if ($_HANDOUT->getCfg('user_all', false)) {
        		$validate = COM_HANDOUT_VALIDATE_USER_ALL ;
      		} else {
           		$validate = COM_HANDOUT_VALIDATE_USER;
       		}
  		}

  		//upload the file
  		$upload = new HANDOUT_FileUpload();
  		$file = $upload->uploadLINK($url , $validate);

        if (!$file) {

            $msg = JText::_('COM_HANDOUT_ERROR_LINKING') . " - " . $upload->_err;

            return array(
				'_error' => 1,
				'_errmsg'=> $msg
         	);
        }

       $msg = JText::_('COM_HANDOUT_LINKED');

       return array(
			'_error' => 0,
			'_errmsg'=> $msg
         );
    }
}

