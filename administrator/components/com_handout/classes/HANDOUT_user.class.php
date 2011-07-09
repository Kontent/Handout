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

if (defined ( '_HANDOUT_USER' )) {
	return true;
} else {
	define ( '_HANDOUT_USER', 1 );
}

/**
 * HANDOUT permissions class.
 *
 * @desc class purpose is to handle users and groups permissions and related functions
 */

class HANDOUT_User {
	/**
	 * @access 	publi
	 * @var 		int
	 */
	var $userid = null;

	/**
	 * @access 	public
	 * @var 		string
	 */
	var $usertype = null;

	/**
	 * @access 	public
	 * @var 		int
	 */
	var $gid = null;

	/**
	 * @access 	public
	 * @var 		string
	 */
	var $username = null;

	/**
	 * @access 	public
	 * @var 		bool
	 */
	var $isAdmin = 0;

	/**
	 * @access   public
	 * @var	  bool
	 */
	var $isSpecial = 0;

	/**
	 * @access 	public
	 * @var 		bool
	 */
	var $isEditor = 0;

	/**
	 * @access 	public
	 * @var 		bool
	 */
	var $isPublisher = 0;

	/**
	 * @access 	public
	 * @var 		bool
	 */
	var $isAuthor = 0;

	/**
	 * @access 	public
	 * @var 		bool
	 */
	var $isManager = 0;

	/**
	 * @access 	public
	 * @var 		bool
	 */
	var $isRegistered = 0;

	/**
	 * @access 	public
	 * @var 		string 		Contains a 'negative' number list.
	 */
	var $groupsIn = null;

	/**
	 * @access	public
	 * @var		integer		Special Compatibility mode
	 * 0 = Handout Style, 1 = Joomla-style(authors+ are special)
	 * Change this here in the code if needed.
	 */
	var $specialcompat = 0;

	/**
	 * @desc 	constructor
	 * @return 	void
	 */

	function HANDOUT_User() {
		$user = &JFactory::getUser ();

		$this->userid = $user->id;
		$this->username = $user->username;
		$this->usertype = strtolower ( $user->usertype );
		$this->gid = $user->gid;

		$this->setUsertype ();
		$this->groupsIn = $this->getGroupsIn ();

	}

	function setUsertype() {
		switch ($this->usertype) {
			case 'super administrator' :
				{
					$this->isAdmin = 1;
					$this->isRegistered = 1;
					$this->isSpecial = 1;
				}
				break;
			case 'administrator' :
				{
					$this->isAdmin = 1;
					$this->isRegistered = 1;
					$this->isSpecial = 1;
				}
				break;
			case 'manager' :
				{
					$this->isAdmin = 1;
					$this->isManager = 1;
					$this->isRegistered = 1;
					$this->isSpecial = 1;
				}
				break;
			case 'editor' :
				{
					$this->isEditor = 1;
					$this->isRegistered = 1;
					$this->isSpecial = $this->specialcompat;
				}
				break;
			case 'publisher' :
				{
					$this->isPublisher = 1;
					$this->isRegistered = 1;
					$this->isSpecial = $this->specialcompat;
				}
				break;
			case 'author' :
				{
					$this->isAuthor = 1;
					$this->isRegistered = 1;
					$this->isSpecial = $this->specialcompat;
				}
				break;
			case 'user' :
			case 'registered' :
				{
					$this->isRegistered = 1;
				}
				break;
		}
	}

	/**

	 * @desc 	Checks if the user can access the component.

	 * @return 	bool

	 */

	function getGroupsIn() {
		$db = &JFactory::getDBO ();

		$groups_in = array ();

		//Add Handout groups

		$db->setQuery ( "SELECT groups_id,groups_members " . "\n FROM #__handout_groups" );
		$all_groups = $db->loadObjectList ();

		if (count ( $all_groups )) {
			foreach ( $all_groups as $a_group ) {
				$group_list = array ();
				$group_list = explode ( ',', $a_group->groups_members );
				if (in_array ( $this->userid, $group_list )) {
					$groups_in [] = trim ( - 1 * ($a_group->groups_id + 10) );
				}
			}
		}

		//Add Joomla groups

		if ($this->isAuthor) {
			$groups_in [] = COM_HANDOUT_PERMIT_AUTHOR;
		}
		if ($this->isEditor) {
			$groups_in [] = COM_HANDOUT_PERMIT_EDITOR;
		}
		if ($this->isPublisher) {
			$groups_in [] = COM_HANDOUT_PERMIT_PUBLISHER;
		}

		if (empty ( $groups_in ))
			return '0,0';

		return implode ( ',', $groups_in );
	}

	/**

	 * @desc 			Checks if the the user is a member of a group

	 * @param 	int 	Group $ ID to check (must be a negative number)

	 * @return 	bool

	 */

	function isInGroup($group_number) {
		return preg_match ( "/(^|,)$group_number(,|$)/", $this->groupsIn );
	}

	/**

	 * @desc 	checks if the user can preform a certain task

	 * @access  	public

	 * @return 	string	error message

	 */
	function canPreformTask($doc = null, $task) {
		$err = '';

		if ($this->userid > COM_HANDOUT_PERMIT_USER) {
			//Make sure we have a document object

			$this->isDocument ( $doc );

			// user has no permissions to preform the operation

			$func = "can" . $task;
			if (! call_user_func ( array (&$this, "" . $func . "" ), $doc )) {
				$err .= JText::_('COM_HANDOUT_NOT_AUTHORIZED');
			}

			// document already checked out by other user

			if (! is_null ( $doc ) && $doc->checked_out) {
				if ($doc->checked_out != $this->userid) {
					$err .= JText::_('COM_HANDOUT_THE_MODULE') . " $doc->docname " . JText::_('COM_HANDOUT_IS_BEING');
				}
			}
		} else {
			$err .= JText::_('COM_HANDOUT_NOLOG');
		}

		return $err;
	}

	/**

	 * @desc checks in the user can access the component.

	 * @access  	public

	 * @return 	bool

	 */

	function canAccess() {
		$handout = &HandoutFactory::getHandout ();
		// if the user is not logged in...

		if (! $this->userid && $handout->getCfg ( 'registered' ) == COM_HANDOUT_GRANT_NO) {
			return 0;
		}
		// check if the component is down

		if (! $this->isSpecial && $handout->getCfg ( 'isDown' )) {
			return - 1;
		}

		return 1;
	}

	/**

	 * @desc 	checks if the user can download a document

	 * @access  	public

	 * @return 	bool

	 */

	function canUpload() {
		$handout = &HandoutFactory::getHandout ();

		// preform checks

		if ($this->isAdmin) {
			return true;
		}

		if ($this->userid) {
			$upload = $handout->getCfg ( 'user_upload' );

			if ($upload == $this->userid || $upload == COM_HANDOUT_PERMIT_REGISTERED) {
				return true;
			}

			if ($upload == COM_HANDOUT_PERMIT_AUTHOR and ($this->isAuthor or $this->isEditor or $this->isPublisher)) {
				return true;
			}

			if ($upload == COM_HANDOUT_PERMIT_EDITOR and ($this->isEditor or $this->isPublisher)) {
				return true;
			}

			if ($upload == COM_HANDOUT_PERMIT_PUBLISHER and $this->isPublisher) {
				return true;
			}

			if ($this->isInGroup ( $upload )) {
				return true;
			}
		}

		return false;
	}

	/**

	 * @desc 	Checks if the user can download a document

	 * @param 	mixed	object or numeric $doc

	 * @access  	public

	 * @return 	bool

	 */
	function canDownload($doc = null) {
		$handout = &HandoutFactory::getHandout ();
		$db = &JFactory::getDBO ();

		//Make sure we have a document object

		$this->isDocument ( $doc );

		//check if user has access to the document's category

		if (! $this->canAccessCategory ( $doc->catid )) {
			return false;
		}

		// preform checks

		if ($this->isSpecial) {
			return true;
		}

		if ($this->canEdit ( $doc, false )) {
			return true;
		}

		if ($this->userid == 0 && $handout->getCfg ( 'registered' ) != COM_HANDOUT_GRANT_RX) {
			return false;
		}

		if ($doc->docowner == COM_HANDOUT_PERMIT_EVERYONE) {
			return true;
		}

		if ($this->userid) {
			if ($doc->docowner == COM_HANDOUT_PERMIT_REGISTERED) {
				return true;
			}

			if ($doc->docowner > COM_HANDOUT_PERMIT_USER && $doc->docowner == $this->userid) {
				return true;
			}

			if ($doc->docowner < COM_HANDOUT_PERMIT_GROUP && $this->isInGroup ( $doc->docowner )) {
				return true;
			}

			if ($doc->docsubmittedby == $this->userid) {
				if (is_a ( $doc, 'HandoutDocument' )) {
					$authorCan = $doc->authorCan ();
				} else { // Naughty! No object. Create a temp one

					$tempDoc = new HandoutDocument ( $db );
					$tempDoc->attribs = $doc->attribs;
					$authorCan = $tempDoc->authorCan ();
				}
				if ($authorCan >= COM_HANDOUT_AUTHOR_CAN_READ) {
					return true;
				}
			}
		}
		return false;
	}

	/**

	 * @desc 	Checks if the user can edit a document entry

	 * @param 	mixed	object or numeric $doc

	 * @access  	public

	 * @return 	bool

	 */

	function canEdit($doc = null, $checkCreator = true) {
		$db = &JFactory::getDBO ();

		//Make sure we have a document object

		$this->isDocument ( $doc );

		// preform checks

		if ($this->isSpecial) { // admin

			return true;
		}

		//check if user has access to the document's category

		if (! $this->canAccessCategory ( $doc->catid )) {
			return false;
		}

		$maintainer = $doc->docmaintainedby;

		if ($this->userid) {
			if (($maintainer == $this->userid) || ($maintainer == COM_HANDOUT_PERMIT_REGISTERED)) { // maintainer

				return true;
			}

			// Check Creator

			if ($checkCreator && $doc->docsubmittedby == $this->userid) {
				if (is_a ( $doc, 'HandoutDocument' )) {
					$authorCan = $doc->authorCan ();
				} else { // Naughty! No object. Create a temp one

					$tempDoc = new HandoutDocument ( $db );
					$tempDoc->attribs = $doc->attribs;
					$authorCan = $tempDoc->authorCan ();
				}
				if ($authorCan & COM_HANDOUT_AUTHOR_CAN_EDIT) {
					return true;
				}
			}

			if ($this->isInGroup ( $maintainer )) {
				return true;
			}
		}

		return false; // DEFAULT: can't edit

	}


	/**

	 * @desc 	Checks if the user can publish a document

	 * @param 	mixed	object or numeric $doc

	 * @access  	public

	 * @return 	bool

	 */

	function canPublish($doc = null) {
		$handout = &HandoutFactory::getHandout ();

		//Make sure we have a document object

		$this->isDocument ( $doc );

		if ($this->isSpecial) {
			return true;
		}

		if ($this->userid) {
			$publish = $handout->getCfg ( 'user_publish' );

			if ($publish == $this->userid || $publish == COM_HANDOUT_PERMIT_REGISTERED) {
				return true;
			}

			if ($this->isInGroup ( $publish )) {
				return true;
			}
		}
		return false; // DEFAULT: can't publish

	}

	/**

	 * @desc 	Checks if the user can unpublish a document

	 * @param 	mixed	object or numeric $doc

	 * @access  	public

	 * @return	bool

	 */

	function canUnPublish($doc = null) {
		$handout = &HandoutFactory::getHandout ();

		//Make sure we have a document object

		$this->isDocument ( $doc );

		if ($this->isSpecial) {
			return true;
		}

		if ($this->userid) {
			$publish = $handout->getCfg ( 'user_publish' );

			if ($publish == $this->userid || $publish == COM_HANDOUT_PERMIT_REGISTERED) {
				return true;
			}

			if ($this->isInGroup ( $publish )) {
				return true;
			}
		}
		return false; // DEFAULT: can't unpublish

	}

	/**

	 * @desc 	checks if the user can checkout a document

	 * @param 	mixed	object or numeric $doc

	 * @access  	public

	 * @return 	bool

	 */

	function canCheckOut($doc = null) {
		$handout = &HandoutFactory::getHandout ();

		//Make sure we have a document object

		$this->isDocument ( $doc );

		if ($doc->checked_out) {
			return false;
		}

		return $this->canEdit ( $doc );
	}

	/**

	 * @desc 	Checks if the user can checkin a document

	 * @param 	mixed	object or numeric $doc

	 * @access  	public

	 * @return 	bool

	 */

	function canCheckIn($doc = null) {
		$handout = &HandoutFactory::getHandout();

		//Make sure we have a document object

		$this->isDocument ( $doc );

		if (! $doc->checked_out) {
			return false;
		}

		return $this->canEdit ( $doc );
	}

	/**

	 * @desc 	Checks if the user can move a document

	 * @param 	mixed	object or numeric $doc

	 * @access  	public

	 * @return 	bool

	 */

	function canMove($doc = null) {
		//Make sure we have a document object

		$this->isDocument ( $doc );

		return $this->canEdit ( $doc );
	}

	/**

	 * @desc 	Checks if the user can reset a documents hit counter

	 * @param 	object $ or numeric $doc

	 * @access  	public

	 * @return 	bool

	 */
	function canReset($doc = null) {
		global $_HANDOUT;

		//Make sure we have a document object

		$this->isDocument ( $doc );

		return $this->canEdit ( $doc );
	}

	/**

	 * @desc 	Checks if the user can delete a document

	 * @param 	mixed	object or numeric $doc

	 * @access  	public

	 * @return 	bool

	 */

	function canDelete($doc = null) {
		//Make sure we have a document object

		$this->isDocument ( $doc );

		return $this->canEdit ( $doc );
	}

	/**

	 * @desc 	Checks if the user can update a document

	 * @param 	mixed	object or numeric $doc

	 * @access  	public

	 * @return 	bool

	 */

	function canUpdate($doc = null) {
		//Make sure we have a document object

		$this->isDocument ( $doc );

		return $this->canEdit ( $doc );
	}

	/**

	 * @desc 	Checks if the user can assign viewers

	 * @param 	mixed	object or numeric $doc

	 * @access  	public

	 * @return 	bool

	 */

	function canAssignViewer($doc = null) {
		$handout = &HandoutFactory::getHandout();

		//Make sure we have a document object

		$this->isDocument ( $doc );

		if ($this->isSpecial) {
			return true;
		}

		if ($handout->getCfg ( 'reader_assign' ) & COM_HANDOUT_ASSIGN_BY_AUTHOR) {
			if ($this->userid == $doc->docsubmittedby) {
				return true;
			}
		}

		if ($handout->getCfg ( 'reader_assign' ) & COM_HANDOUT_ASSIGN_BY_EDITOR) {
			if ($this->canEdit ( $doc, false )) {
				return true;
			}
		}

		return false; // DEFAULT: can't assign viewer

	}

	/**

	 * @desc 	Checks if the user can assign maintainer

	 * @param 	mixed	object or numeric $doc

	 * @access  	public

	 * @return 	bool

	 */
	function canAssignMaintainer($doc = null) {
		$handout = &HandoutFactory::getHandout ();

		//Make sure we have a document object

		$this->isDocument ( $doc );

		if ($this->isSpecial) {
			return true;
		}

		if ($handout->getCfg ( 'editor_assign' ) & COM_HANDOUT_ASSIGN_BY_AUTHOR) {
			if ($this->userid == $doc->docsubmittedby) {
				return true;
			}
		}

		if ($handout->getCfg ( 'editor_assign' ) & COM_HANDOUT_ASSIGN_BY_EDITOR) {
			if ($this->canEdit ( $doc, false )) {
				return true;
			}
		}

		return false; // DEFAULT: can't assign maintainer

	}

	/**

	 * @desc 	Checks if the user can access a category

	 * @param 	mixed	object or numeric $doc

	 * @access  	public

	 * @return 	bool

	 */
	function canAccessCategory($category = null) {
		//Make sure we have a document object

		$category = $this->isCategory ( $category );

		if (! $category->published and ! $this->isSpecial) {
			return false;
		}

		switch ($category->access) {
			case '0' : //public

				return true;
				break;
			case '1' : //registered

				if ($this->isRegistered) {
					return true;
				}
				break;
				break;
			case '2' : //special

				if ($this->isSpecial) {
					return true;
				}
				break;
				break;
		}
		return false;
	}

	/**

	 * @desc 	Transform the document to a object if necessary

	 * @param 	mixed	object or numeric $doc

	 * @access  	private

	 * @return 	object 	a document object

	 */

	function isDocument(&$doc) {
		$db = &JFactory::getDBO ();

		// check to see if we have a object

		if (! is_a ( $doc, 'HandoutDocument' )) {
			$id = $doc;
			// try to create a document db object

			if (is_numeric ( $id )) {
				$doc = new HandoutDocument ( $db );
				$doc->load ( $id );
			}
		}
	}

	/**

	 * @desc 	Transform the document to a object if necessary

	 * @param 	mixed	object or numeric $category

	 * @access  	private

	 * @return 	object 	a document object

	 */

	function isCategory(&$category) {

		// check to see if we have a object

		if (! is_a ( $category, 'HandoutCategory' )) {
			// try to create a category db object

			if (is_object ( $category )) {
				$id = ( int ) @ $category->id;
			} else {
				$id = ( int ) $category;
			}

			$category = & HandoutCategory::getInstance ( $id );
		}

		return $category;
	}

} // end class



class HANDOUT_users {

	/**

	 * Provides a list of all users

	 *

	 * @deprecated

	 */
	function &getList() {
		static $users;

		if (! isset ( $users )) {
			$db = &JFactory::getDBO ();
			$db->setQuery ( "SELECT * " . "\n FROM #__users " . "\n ORDER BY name ASC" );
			$users = $db->loadObjectList ( 'id' );
		}

		return $users;
	}

	/**

	 * Get a User object, caches results

	 */
	function &get($id) {
		static $users;

		if (! isset ( $users )) {
			$users = array ();
		}

		if (! isset ( $users [$id] )) {
			$users [$id] = new JUser($id);
		}

		return $users [$id];
	}
}