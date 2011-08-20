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

class HandoutModelCode extends JModel
{
	
	function __construct()
	{
		parent::__construct();
		
	}
	
	

	function processCode($codeVal, $usertype) {
		$email = trim(JRequest::getVar('email', ''));

		if (!$codeVal) {
			HandoutHelper::_returnTo ( 'doc_code', JText::_('COM_HANDOUT_CODE_INVALID'), '', $params) ;
		}

		//fetch code for given code and docid
		$code = $this->getCode($codeVal);

		if (!$code) {
			HandoutHelper::_returnTo ( 'doc_code', JText::_('COM_HANDOUT_CODE_INVALID'), '', $params) ;
		}
		else {
			$docid = $code->docid;
			$params = array('usertype'=>$usertype);
			@session_start();
			$_SESSION['handout.downloadcode']=$codeVal;
			//check if code is single-use or unlimited
			switch ($code->usage) {
				case '0': //single-use
					//check if it has been used already
					if ( $this->getUsage($codeVal, $docid) > 0) {
						HandoutHelper::_returnTo ( 'doc_code', JText::_('COM_HANDOUT_CODE_LIMIT_EXCEED'), '', $params);
					}
					break;
				case '1': //unlimited
					//do nothing
					break;
			}

			$handout = &HandoutFactory::getHandout ();
			$handoutUser = &HandoutFactory::getHandoutUser ();
			$database = &JFactory::getDBO ();

			//check if user may be anonymous or needs to be registered or an email id is required
			switch ($usertype) {
				case '0': //anonymous
					//redirect to the download page
					HandoutHelper::_returnTo ( 'doc_download', '', $docid);
					break;
				case '1': //registered
					// check if user is logged in
					if ($handoutUser->userid == 0) {
						//not logged in
						HandoutHelper::_returnTo ( 'doc_code', JText::_('COM_HANDOUT_CODE_NOLOGIN'), '', $params);
					}
					else {
						//logged in - redirect to download page
						HandoutHelper::_returnTo ( 'doc_download', '', $docid);
					}
					break;
				case '2': // email required
					//validity of email
					if (!$email || !preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i", $email)) {
						HandoutHelper::_returnTo ( 'doc_code', JText::_('COM_HANDOUT_CODE_INVALID_EMAIL'), '', $params);
					}

					//check to see if email exists in the user table
					$query = "SELECT COUNT(*) FROM #__users WHERE email='".$email."'";
					$database->setQuery($query);
					$numUsers = $database->loadResult();
					if ($numUsers>0){
						//email exists, so go to download page
						HandoutHelper::_returnTo ( 'doc_download', '', $docid);
					}
					else {
						//register new user with email and no password
						$query = "INSERT INTO #__users(name, username, email, password, gid, userType, registerDate, activation) VALUES('{$email}', '{$email}', '{$email}', '', 18, 'Registered', '".date('Y-m-d H:i:s')."', '')";
						$database->setQuery($query);
						$database->query();
						$userid=$database->insertid();

						//Insert into jos_core_acl_aro
						$query="INSERT INTO #__core_acl_aro (section_value,value,name) VALUES('users','{$userid}','{$email}')";
						$database->setQuery($query);
						$database->query();
						$aroid=$database->insertid();

						$query="INSERT INTO jos_core_acl_groups_aro_map (group_id,aro_id) VALUES ('18','${aroid}')";
						$database->setQuery($query);
						$database->query();

						$_SESSION['handout.anonuser'] = $userid;
						//go to download page
						HandoutHelper::_returnTo ( 'doc_download', '', $docid);
					}
					break;
			}
		}
	}

	function getCode($codeVal) {
		$database = &JFactory::getDBO ();
		$query = "SELECT * FROM #__handout_codes " .
				 " WHERE `name`='". $codeVal."'" .
				 " LIMIT 1";
		$database->setQuery($query);
		return $database->loadObject();
	}

	function getUsage($codeVal, $docid) {
		$database = &JFactory::getDBO ();
		$query = "SELECT COUNT(*) FROM #__handout_log " .
			 	 " WHERE `log_code`='".$codeVal."'" .
				 " AND `log_docid`='". (int) $docid."'";

		$database->setQuery($query);
		return $database->loadResult();
	}
	
	
}



?>