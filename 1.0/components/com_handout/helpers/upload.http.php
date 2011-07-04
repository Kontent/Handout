<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: upload.http.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_METHOD_HTTP')) {
    return true;
} else {
    define('_HANDOUT_METHOD_HTTP' , 1);
}

class HandoutUploadMethod
{
    function fetchMethodForm($uid, $step, $update)
    {
        $task = JRequest::getCmd('task');

        switch ($step)
        {
            case 2: // Input the filename (Form)
            {
                $lists = array();
                $lists['action'] = HandoutHelper::_taskLink($task, $uid, array('step' => $step + 1), false);
                
				return $lists;
            } break;

            case 3: // Process the file and edit the document
            {
                //upload file
                $hash = HANDOUT_Utils::stripslashes($_FILES);
                $file = isset($hash['upload']) ? $hash['upload'] : null;

                $err  = HandoutUploadMethod::uploadFileProcess($uid, $step, $file);
                if($err['_error']) {
                	HandoutHelper::_returnTo($task, $err['_errmsg'], '', array('step' => $step - 1, 'method' => 'http'));
                }

   				$catid = $update ? 0 : $uid;
                $docid = $update ? $uid : 0;
				
  				return DocumentsHelper::fetchEditDocumentForm($docid , $file->name, $catid);

            } break;

            default: break;
        }
        return true;
    }

    function uploadFileProcess($uid, $step, &$file)
	{
        HANDOUT_token::check() or die('Invalid Token');
        
  		$_HANDOUT_USER = &HandoutFactory::getHandoutUser();
  		$_HANDOUT = &HandoutFactory::getHandout();

  		if ($file['name'] == '') {
            return array(
				'_error' => 1,
				'_errmsg'=> JText::_('COM_HANDOUT_FILENAME_REQUIRED')
         	);
        }

  		/* ------------------------------ *
     	 *   PLUGIN - Setup All Plugins   *
     	 * ------------------------------ */
    	$logbot = new HANDOUT_plugin('onLog');
   		$prebot = new HANDOUT_plugin('onBeforeUpload');
    	$postbot = new HANDOUT_plugin('onAfterUpload');

   		$logbot->setParm('filename' , $file['name']);
    	$logbot->setParm('user' , $_HANDOUT_USER);
    	$logbot->copyParm('process' , 'upload');
    	$prebot->setParmArray ($logbot->getParm()); // Copy the parms over
    	$postbot->setParmArray($logbot->getParm());

   		/* ------------------------------ *
     	*   Pre-upload                    *
     	* ------------------------------ */
    	$prebot->trigger();
    	if ($prebot->getError()) {
      		$logbot->setParm('msg' , $prebot->getErrorMsg());
        	$logbot->copyParm('status' , 'LOG_ERROR');
        	$logbot->trigger();

        	return array(
				'_error' => 1,
				'_errmsg'=> $prebot->getErrorMsg()
         	);
    	}

    	/* ------------------------------ *
     	*   Upload                        *
     	* ------------------------------ */

    	$path = $_HANDOUT->getCfg('handoutpath');
   		//get file validation settings
   		if ($_HANDOUT_USER->isSpecial) {
      		$validate = COM_HANDOUT_VALIDATE_ADMIN;
   		} else {
     		if ($_HANDOUT->getCfg('user_all', false)) {
        		$validate = COM_HANDOUT_VALIDATE_ALL ;
      		} else {
           		$validate = COM_HANDOUT_VALIDATE_USER;
       		}
  		}

  		//upload the file
  		$upload = new HANDOUT_FileUpload();
  		$file = $upload->uploadHTTP($file, $path, $validate);

   		/* -------------------------------- *
	 	 *    Post-upload                   *
	 	 * -------------------------------- */

   		if (!$file) {
     		$linkOpt = array('step' => $step - 1, 'method' => 'http');
       		$msg = JText::_('COM_HANDOUT_ERROR_UPLOADING') . " - " . $upload->_err;
       		$logbot->setParm('file', $file['name']);
       		$logbot->setParm('msg' , $msg);
       		$logbot->copyParm('status' , 'LOG_ERROR');
       		$logbot->trigger();

       		return array(
				'_error' => 1,
				'_errmsg'=> $msg
         	);

   		}

       	$msg = "&quot;" . $file->name . "&quot; " . JText::_('COM_HANDOUT_UPLOADED');

       	$logbot->copyParm(array('msg' => $msg ,'status' => 'LOG_OK'));
       	$logbot->trigger();

       	$postbot->setParm('file', $file->name);
       	$postbot->trigger();

       	if ($postbot->getError()) {
           	$logbot->setParm('msg' , $postbot->getErrorMsg());
            $logbot->copyParm('status' , 'LOG_ERROR');
            $logbot->trigger();

            return array(
				'_error' => 1,
				'_errmsg'=> $postbot->getErrorMsg()
         	);
       	}
        $linkOpt['step'] = $step + 1;

        return array (
			'_error' => 0,
			'_errmsg'=> $msg
        );
	}
}

