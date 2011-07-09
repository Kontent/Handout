<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	Improved by JoomDOC by Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */
defined('_JEXEC') or die;

require_once dirname( __FILE__ ) .'/includes/defines.php';

/**
* Handout Mainframe class using a singleton pattern
* Provide many supporting API functions
*/

class HandoutMainFrame
{
	/** @var object An object of configuration variables */
	var $_config = null;

	/** @var object An object that holds the user state */
	var $_user   = null;

	/** @var object An object that holds the user state */
	var $_type   = null;

	/** @var number An number that holds the menu id */
	var $_menuid = 0;

	/**
	* Class constructor
	*/
	function HandoutMainFrame( $type = COM_HANDOUT_TYPE_UNKNOWN)
	{
		$this->_initialise( $type );
	}

	function _initialise( $type )
	{
		$this->_setAdminPaths( JPATH_ROOT );
		$this->_setConfig();
		$this->_setUser();
		$this->_setMenuId();

		$this->setType($type);

		//load common language defines
		$lang = &JFactory::getLanguage();
        $lang->load('com_handout', JPATH_ADMINISTRATOR);

		//include php compatibility files
		$this->loadCompatibility();
	}

	/**
	* Determines the paths for including engine and menu files
	* @param string The base path from which to get the path
	*/
	function _setAdminPaths( $basePath = '.' ) {

		$this->_path = new stdClass();

		// sections
		if (file_exists( "$basePath/administrator/components/com_handout/classes")) {
			$this->_path->classes = "/administrator/components/com_handout/classes";
		}
		if (file_exists( "$basePath/administrator/components/com_handout/contrib")) {
			$this->_path->contrib = "/administrator/components/com_handout/contrib";
		}
		if (file_exists( "$basePath/administrator/components/com_handout/includes")) {
			$this->_path->includes = "/administrator/components/com_handout/includes";
		}
		if (file_exists( "$basePath/administrator/components/com_handout/images")) {
			$this->_path->images = "/administrator/components/com_handout/images";
		}
		if (file_exists( "$basePath/administrator/components/com_handout/temp")) {
			$this->_path->temp = "/administrator/components/com_handout/temp";
		}

		//backend
		if (file_exists( "$basePath/administrator/components/com_handout")) {
			$this->_path->admin_root = "/administrator/components/com_handout";
		}

		//frontend
		if (file_exists( "$basePath/components/com_handout/includes_frontend")) {
			$this->_path->includes_f = "/components/com_handout/includes_frontend";
		}

		if (file_exists( "$basePath/components/com_handout/helpers")) {
			$this->_path->helpers = "/components/com_handout/helpers";
		}

		//language
		if (file_exists( "$basePath/administrator/components/com_handout/language")) {
			$this->_path->language = "/administrator/components/com_handout/language";
		}
	}

	/**
	* Returns a stored path variable
	*
	*/
	function getPath( $folder, $file = '', $type = 0)
	{
		$root_path = JPATH_ROOT;
		$live_site = JURI::base();
        $folder = $this->cleanPath( $folder );
        $file   = $this->cleanPath( $file );

		$result = null;
		if (isset( $this->_path->$folder ))
		{
			switch($folder)
			{
				case 'classes' :
				{
					if($file) {
						$path = $this->_path->$folder.'/HANDOUT_'.$file.'.class.php';
						if(file_exists($root_path.$path))
						 $result = $path;
					} else
					  	$result = $this->_path->$folder;
				} break;

				case 'contrib' :
				{
					if($file) {
						$path = $this->_path->$folder.'/'.$file.'/';
						if(file_exists($root_path.$path))
						 $result = $path;
					} else
					  	$result = $this->_path->$folder;
				} break;

				case 'language' :
				{
					if($file) {
						$path = $this->_path->$folder.'/'.$file.'.php';
						if(file_exists($root_path.$path))
						 $result = $path;
					} else
					  	$result = $this->_path->$folder;
				} break;

				case 'includes':
				case 'includes_f':
				{
					if($file) {
						$path = $this->_path->$folder.'/'.$file.'.php';
						if(file_exists($root_path.$path))
						 $result = $path;
					} else
					  	$result = $this->_path->$folder;
				} break;

				case 'helpers':
				{
					if($file) {
						$path = $this->_path->$folder.'/'.$file.'.php';
						if(file_exists($root_path.$path))
						 $result = $path;
					} else
					  	$result = $this->_path->$folder;
				} break;

				case 'images' :
					$result = $this->_path->$folder;
					break;

				case 'temp'   :
					$result = $this->_path->$folder;
					break;

				case 'admin_root' :
					if($file) {
						$path = $this->_path->$folder.'/'.$file;
						if(file_exists($root_path.$path))
						$result = $path;
					} else
						$result = $this->_path->$folder;
						break;
			}
		} else {
			return false;
		}

		$path_type = $type ? $live_site :  $root_path;

		return $path_type.$result;
	}

	/**
	* Loads the configuration file and creates a new class
	*/
	function _setConfig( ) {
		require_once $this->getPath('classes', 'config');
		$this->_config = new HANDOUT_Config('HandoutConfig', dirname(__FILE__)."/handout.config.php" );

        $this->_checkConfig();
	}

    /**
     * Check if the configuration values are valid
     */
    function _checkConfig() {
    	$task = JRequest::getCmd('task');
        $root_path = JPATH_ROOT;
        $save = false;

        // Get the path (ignore result) ... this sets a default value
        if(is_null($this->_config->getCfg('handoutpath'))) {
            $this->_config->setCfg('handoutpath', $root_path.DS.'handouts', true );
            $save = true;
        }

        // trim pipes and spaces in $fname_reject
        if(isset($this->_config->fname_reject)) {
            $this->_config->fname_reject = trim($this->_config->fname_reject, '| ');
            $save = true;
        }

        // never save config during download
        if( $task=='license_result' OR $task=='doc_download' ){
        	$save = false;
        }

        // save the config if necessary
        if($save) {
        	$this->_config->saveConfig();
        }

    }

	/**
	* @param string The name of the variable
	* @return mixed The value of the configuration variable or null if not found
	*/
	function getCfg( $varname , $default=null) {
		return $this->_config->getCfg($varname, $default);
	}

	/**
	* @param string The name of the variable
	* @param string The new value of the variable
	* @return bool True if succeeded, otherwise false.
	*/
	function setCfg( $varname, $value, $create=false) {
		return $this->_config->setCfg($varname, $value, $create);
	}

	/**
	* Saves the configuration object
	*/
	function saveConfig() {
        $handoutpath = $this->cleanPath($this->getCfg('handoutpath'));
        $this->setCfg('handoutpath', $handoutpath);

		return $this->_config->saveConfig();
	}

	/**
    *
    * @return object The configuration object
    */
    function getAllCfg()
    {
    	return $this->_config->getAllCfg();
    }


	/**
	* Create a user object
	*/
	function _setUser( ) {
		require_once $this->getPath('classes', 'user');
		$this->_user = new HANDOUT_User( $this->getCfg('specialcompat'));
	}

	function & getUser() {
        $null = null;
		if (isset( $this->_user )) {
			return $this->_user;
		} else {
			return $null;
		}
	}

	/**
	* Set the mainframe type
	*/
	function setType($type) {
		$this->_type = $type;
	}
	function getType($type) {
		return $this->_type;
	}

	/**
	* Set the menu id
	*/
	function _setMenuId() {
		$db = &JFactory::getDBO();

		$query = "SELECT id"
			. "\nFROM #__menu"
			. "\nWHERE link ='index.php?option=com_handout'";

		$db->setQuery($query);

		$this->_menuid = $db->loadResult();
	}

	function getMenuId() {
		return $this->_menuid;
	}

	/**
	* Load language files
	*/
	function loadLanguage($type)
	{
		/*
		$config = &JFactory::getConfig();
		$lang = $config->getValue('config.language');

		if (file_exists($this->getPath('language').'/'.$lang.'.'.$type.'.php')) {
    		include_once ($this->getPath('language').'/'.$lang.'.'.$type.'.php');
		} else {
    		include_once ($this->getPath('language').'/en-GB.'.$type.'.php');
		}
		*/
		return;
	}

	/**
	* Load PHP compatibility files
	*/
	function loadCompatibility()
	{
		if (phpversion() < '4.2.0') {
			require_once $this->getPath('contrib', 'pear').'/PHP_Compat.php';
		}
	}

    /**
    * Check a filename or path for '..', '//' or '\\'
    */
    function cleanPath( $path )
    {
        $path = trim( $path );
        // remove '..'
        $path = str_replace( '..', '', $path );
        // Remove double slashes and backslahses and convert all slashes and backslashes to DS
        $path = preg_replace('#[/\\\\]+#', DIRECTORY_SEPARATOR, $path);

        return $path;
    }


}

/**
* Document database table class
* @package HANDOUT_1.0
*/

class HandoutDocument extends JTable {
    var $id                 = null;
    var $catid              = null;
    var $docname             = null;
    var $docfilename         = null;
    var $docdescription      = null;
    var $docdate_published   = null;
    var $docowner            = null;
    var $published          = null;
    var $docurl              = null;
    var $doccounter          = null;
    var $checked_out        = null;
    var $checked_out_time   = null;
    var $docthumbnail        = null;
    var $doclastupdateon     = null;
    var $doclastupdateby     = null;
    var $docsubmittedby       = null;
    var $docmaintainedby      = null;
    var $doclicense_id       = null;
    var $doclicense_display  = null;
	var $docversion			= null;
	var $doclanguage		= null;
	var $doc_meta_keywords	= null;
	var $doc_meta_description	= null;
	var $kunena_discuss_id	= null;
	var $mtree_id	= null;
    var $access             = null;
    var $attribs            = null;

	function __construct (&$db)
	{
		parent::__construct ('#__handout', 'id', $db);
	}

	function load( $cid=0 )
	{
		if( $cid == 0 )			// Only read if recID passed
			return $this->init_record();
		else				// fill in some 'null' fields
			return parent::load( $cid );
	}

	/*
	*   @desc Internal routine - initialize some critical values
	*	@param none
	*	@param true
	*/

	function init_record()
	{
		$user = &JFactory::getUser();
		$handout = &HandoutFactory::getHandout();

		$this->id		= null;
		$this->published        = 0;
		$this->docsubmittedby     = $user->id;
		$this->doclastupdateby   = $user->id;

		$this->docowner		 = $handout->getCfg( 'default_viewer' );
		$this->docmaintainedby = $handout->getCfg( 'default_editor' );

		$this->docdate_published = date( "Y-m-d H:i:s" );

		if( $this->docowner  == COM_HANDOUT_PERMIT_CREATOR ){
			$this->docowner = $this->docsubmittedby;
		}
		if( $this->docmaintainedby == COM_HANDOUT_PERMIT_CREATOR ){
			$this->docmaintainedby = $this->docsubmittedby;
		}
		return true;
	}

	/*
	*   @desc Check a document
	*	@param nothing
	*	@returns boolean true if checked
	*/

	function check()
	{
		$user = &JFactory::getUser();

		// Check fields to be sure they are correct
		$this->_error = "";
		if( ! $this->docname){
			$this->_error .= "\\n" . JText::_('COM_HANDOUT_ENTRY_NAME');
		}
		if( $this->docfilename == "" ){
			$this->_error .= "\\n". JText::_('COM_HANDOUT_ENTRY_DOC');
		}
        if( $this->docfilename == COM_HANDOUT_DOCUMENT_LINK
            AND $document_url = JRequest::getVar( 'document_url', false)
            AND parse_url($document_url))
        {
            $this->docfilename = COM_HANDOUT_DOCUMENT_LINK.$document_url;
        }

		if( ! $this->catid ){
			$this->_error .= "\\n" . JText::_('COM_HANDOUT_ENTRY_CAT');
		}
		if( $this->docowner == COM_HANDOUT_PERMIT_NOOWNER ||
			$this->docowner == "" ){
			$this->_error .= "\\n" . JText::_('COM_HANDOUT_ENTRY_OWNER');
		}

		if( $this->docmaintainedby == COM_HANDOUT_PERMIT_NOOWNER ||
		    	$this->docmaintainedby == "" ){
			$this->_error .= "\\n" . JText::_('COM_HANDOUT_ENTRY_MAINT');
		}
		if( $this->docdate_published == "" ){
			$this->_error .= "\\n" . JText::_('COM_HANDOUT_ENTRY_DATE');
		}

        // making sure...
        $this->id                 = (int) $this->id;
        $this->catid              = (int) $this->catid;
        $this->docname             = strip_tags($this->docname);
        $this->docdate_published   = strip_tags($this->docdate_published);
        $this->docowner            = (int) $this->docowner;
        $this->published          = strip_tags($this->published);
        $this->docurl              = strip_tags($this->docurl);
        $this->doccounter          = (int) $this->doccounter;
        $this->checked_out        = (int) $this->checked_out;
        $this->checked_out_time   = strip_tags($this->checked_out_time);
        $this->docthumbnail        = strip_tags($this->docthumbnail);
        $this->doclastupdateon     = strip_tags($this->doclastupdateon);
        $this->doclastupdateby     = (int) $this->doclastupdateby;
        $this->docsubmittedby       = (int) $this->docsubmittedby;
        $this->docmaintainedby      = (int) $this->docmaintainedby;
        $this->doclicense_id       = (int) $this->doclicense_id;
        $this->doclicense_display  = (int) $this->doclicense_display;
        $this->docversion  			= strip_tags($this->docversion);
        $this->doclanguage  		= 	strip_tags($this->doclanguage);
		$this->doc_meta_keywords	= strip_tags($this->doc_meta_keywords);
		$this->doc_meta_description	= strip_tags($this->doc_meta_description);
        $this->kunena_discuss_id  = (int) $this->kunena_discuss_id;
        $this->mtree_id  			= (int) $this->mtree_id;
        $this->access             = (int) $this->access;
        $this->attribs            = strip_tags( $this->attribs );

		// Check for links...
		if( strncasecmp( $this->docfilename , COM_HANDOUT_DOCUMENT_LINK , COM_HANDOUT_DOCUMENT_LINK_LNG )==0){

			$document_url = str_replace(COM_HANDOUT_DOCUMENT_LINK, '', $this->docfilename);
			$rmatch = '([a-zA-Z]*)';
			if( strncasecmp( 'file://' ,  $document_url , 7 ) == 0 ){
				$this->_error .= "\\n" . JText::_('COM_HANDOUT_ENTRY_DOCLINK_PROTOCOL') . ' (code 150) '.$document_url;
			}else if( ! preg_match( '/^' . $rmatch . ':\/\//' , $document_url ) ){
				$this->_error .= "\\n" . JText::_('COM_HANDOUT_ENTRY_DOCLINK_PROTOCOL') . ' (code 151) '.$document_url;
			}else if( ! preg_match( '/^' . $rmatch . ':\/\/(.+)$/' , $document_url ) ){
				$this->_error .= "\\n" . JText::_('COM_HANDOUT_ENTRY_DOCLINK_NAME') . ' (code 152) '.$document_url;
			/* Removed test, user is responsible for submitting existing urls
              }else if( ($fh = fopen( $document_url , 'r' ) ) == false ){
				$this->_error .= "\\n" . JText::_('COM_HANDOUT_ENTRY_DOCLINK_INVALID') . ' (code 153) '.$document_url;
                */
			}else{
				//fclose( $fh );
				$this->docfilename = COM_HANDOUT_DOCUMENT_LINK . $document_url;
			}
		}

		if( $this->_error ){
			$this->_error = JText::_('COM_HANDOUT_ENTRY_ERRORS')
			. "\\n---------------------------------"
			. $this->_error;
			return false;
		}

		// Fill in default submitted values
		$date = date( "Y-m-d H:i:s" );

		if( $user->id )
		{
			$this->doclastupdateby   = $user->id;
			if( $this->docowner  == COM_HANDOUT_PERMIT_CREATOR ){
				$this->docowner = $this->docsubmittedby;
			}
			if( $this->docmaintainedby == COM_HANDOUT_PERMIT_CREATOR ){
				$this->docmaintainedby = $this->docsubmittedby;
			}
			if( ! $this->docsubmittedby  ){
        		$this->docsubmittedby     = $user->id;
			}
		}

		if( ! $this->docdate_published )
			$this->docdate_published = $date;

		$this->doclastupdateon 	= $date;



		return true;
	}

	/*
	* @desc Publish a document
	* @param array an array with ids
	* @param boolean publish/unpublish
	*/

	function publish( $cid, $publish )
	{
		$db = &JFactory::getDBO();
		$user = &JFactory::getUser();

		if (!is_array($cid) || count($cid) <1) {
			$action = $publish ? 'publish' : 'unpublish';
			echo "<script> alert('". JText::_('COM_HANDOUT_SELECT_ITEM_MOVE') ." $action'); window.history.go(-1);</script>\n";
			return false;
		}

		$cids = implode(',', $cid);
		$db->setQuery(
			"UPDATE #__handout SET published=" . (int) $publish
			." \n WHERE id IN ($cids) "
			." \n AND (checked_out=0 OR (checked_out=$user->id))");

		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg() ."'); window.history.go(-1); </script>\n";
			return false;
		}

		return true;
	}

	/*
	* @desc Move a document
	* @param array an array with ids
	* @param int an int with the new category id
	*/

	function move( $cid, $catid )
	{
		$db = &JFactory::getDBO();
		$user = &JFactory::getUser();

		if (!(is_array($cid)) || (count($cid) < 1)) {
			echo "<script> alert('". JText::_('COM_HANDOUT_SELECT_ITEM_MOVE') ." ); window.history.go(-1);</script>\n";
			return false;
		}

		$cids = implode(',', $cid);
        $query = "UPDATE #__handout SET catid=". (int) $catid
                ."\n WHERE id IN ($cids)"
                ."\n AND (checked_out = 0 OR (checked_out=".$user->id."))";
		$db->setQuery($query);

		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg() ."'); window.history.go(-1); </script>\n";
			return false;
		}

	        return true;
	}

   /*
    * @desc Copy a document
    * @param array an array with ids
    * @param int an int with the new category id
    */

    function copy( $cid, $catid )
    {
        $db = &JFactory::getDBO();

        if (!(is_array($cid)) || (count($cid) < 1)) {
            echo "<script> alert('". JText::_('COM_HANDOUT_SELECT_ITEM_COPY') ." ); window.history.go(-1);</script>\n";
            return false;
        }

        foreach( $cid as $id ) {
           $docold = new HandoutDocument($db);
           $docnew = new HandoutDocument($db);
           $docold->load($id);
           $docnew->bind( (array) $docold );
           $docnew->id = 0;
           $docnew->catid = $catid;
           if($docold->catid == $docnew->catid) {
               $docnew->docname = JText::_('COM_HANDOUT_COPY_OF') . ' ' . $docnew->docname;
           }
           $docnew->store();
        }


        return true;
    }

	/*
	* @desc Deletes documents
	* @param array an array with ids
	*/

	function remove($cid)
	{
		$db = &JFactory::getDBO();

		if (!is_array($cid) || count($cid) <1) {
			echo "<script>alert(". JText::_('COM_HANDOUT_SELECT_ITEM_DEL') ."); window.history.go(-1);</script>\n";
			return false;
		}

		$cids = implode(',', $cid);
		$db->setQuery("DELETE FROM #__handout WHERE id IN ($cids)");

		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg() ."'); window.history.go(-1); </script>\n";
			return false;
		}

		return true;
	}

	function save()
	{
		$post = HANDOUT_Utils::stripslashes($_POST);

		if (!$this->bind($post)) {
			echo "<script> alert('".$this->_error ."'); window.history.go(-1); </script>\n";
			exit();
		}
		$this->_tbl_key = "id";

		if (!$this->check()) { // Javascript SHOULD catch all this!
			echo "<script> alert('".$this->_error ."'); window.history.go(-1); </script>\n";
			exit();
		}


		if (!$this->store()) {
			echo "<script> alert('".$this->getError() ."'); window.history.go(-1); </script>\n";
			exit();
		}
		$this->checkin();
		return true;
	}

	function cancel()
	{
		$this->bind(HANDOUT_Utils::stripslashes($_POST));
		$this->checkin();

		return true;
	}

	function incrementCounter()
	{
		$db = &JFactory::getDBO();
		$db->setQuery("UPDATE #__handout SET doccounter=doccounter+1 WHERE id=". (int) $this->id);
		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg() ."'); window.history.go(-1); </script>\n";
			exit();
		}
		return true;
	}

	function _returnParam( $field , $config_field='',$attribs=null )
	{
		$handout = &HandoutFactory::getHandout();
		if( ! $config_field ) { $config_field = $field ; }
		if( is_null($attribs)){ $attribs = $this->attribs ;}
		if( ! isset($this->_params) && $attribs )
		{
			$this->_params =  new mosParameters( $attribs );
		}
		if( isset( $this->_params->$field ) )
		{
			return( $this->_params->$field );
		}
		return $handout->getCfg( $config_field );
	}
	function authorCan($a=null)    { return $this->_returnParam( 'author_can'   ,'',$a );}
	function readerAssign($a=null) { return $this->_returnParam( 'reader_assign','',$a );}
	function editorAssign($a=null) { return $this->_returnParam( 'editor_assign','',$a );}
}

/**
* Category database table class
* @package HANDOUT_1.0
*/
class HandoutCategory extends JTable {

	/** @var int */
	var $id			= null;
	var $parent_id		= null;
	var $title		= null;
	var $name		= null;
	var $image		= null;
	var $section		= null;
	var $image_position	= null;
	var $description	= null;
	var $published		= null;
	var $checked_out	= null;
	var $checked_out_time	= null;
	var $editor		= null;
	var $ordering		= null;
	var $access		= null;
	var $count		= null;
	var $params		= null;

	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct( '#__categories', 'id', $db );
	}

    function & getInstance( $id = 0 ) {
        $db = &JFactory::getDBO();
    	static $instances = array();

        if( !$id) {
        	$new = new HandoutCategory( $db );
            return $new;
        }

        if( !isset( $instances[$id] )) {

            $instances[$id] = new HandoutCategory( $db );
            //$instances[$id]->load( $id );

            // instead of loading, we'll use the the following method to improve performance
            $list = & HANDOUT_Cats::getCategoryList(); // get a list of categories, $list[$id] is our current category
            $instances[$id]->bind( (array) $list[$id] ); // assign each property of the category to the category object
        }

        return $instances[$id];
    }

	/**
	* @desc	  generic check method
	* @return boolean True if the object is ok
	*/
	function check()	{
		$this->section = "com_handout";
		return true;
	}
}

/**
* Group database table class
* @package HANDOUT_1.0
*/

class HandoutGroups extends JTable {
	var $groups_id		= null;
	var $groups_name	= null;
	var $groups_description = null;
	var $groups_access 	= null;
	var $groups_members 	= null;

	function __construct(&$db)
	{
		parent::__construct('#__handout_groups', 'groups_id', $db);
	}

	function getId(){
		return ( -1 * $this->groups_id + COM_HANDOUT_PERMIT_GROUP ) ;
	}
}

/**
* License database table class
* @package HANDOUT_1.0
*/

class HandoutLicenses extends JTable {
	var $id 	= null;
	var $name 	= null;
	var $license 	= null;

	function __construct(&$db)
	{
		parent::__construct('#__handout_licenses', 'id', $db);
	}
}

/**
* Codes database table class
* @package HANDOUT_1.0
*/

class HandoutCodes extends JTable {
	var $id 	= null;
	var $name 	= null;
	var $docid 	= null;
	var $usage 	= null;
	var $published 	= null;

	function __construct(&$db)
	{
		parent::__construct('#__handout_codes', 'id', $db);
	}

	/*
	* @desc Publish a code
	* @param array an array with ids
	* @param boolean publish/unpublish
	*/

	function publish( $cid, $publish )
	{
		$db = &JFactory::getDBO();

		if (!is_array($cid) || count($cid) <1) {
			$action = $publish ? 'publish' : 'unpublish';
			echo "<script> alert('". JText::_('COM_HANDOUT_SELECT_ITEM_MOVE') ." $action'); window.history.go(-1);</script>\n";
			return false;
		}

		$cids = implode(',', $cid);
		$db->setQuery(
			"UPDATE #__handout_codes SET published=" . (int) $publish
			." \n WHERE id IN ($cids) ");

		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg() ."'); window.history.go(-1); </script>\n";
			return false;
		}

		return true;
	}


	/*
	* @desc Deletes codes
	* @param array an array with ids
	*/

	function remove($cid)
	{
		$db = &JFactory::getDBO();

		if (!is_array($cid) || count($cid) <1) {
			echo "<script>alert(". JText::_('COM_HANDOUT_SELECT_ITEM_DEL') ."); window.history.go(-1);</script>\n";
			return false;
		}

		$cids = implode(',', $cid);
		$db->setQuery("DELETE FROM #__handout_codes WHERE id IN ($cids)");

		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg() ."'); window.history.go(-1); </script>\n";
			return false;
		}

		return true;
	}

	public static function getCodesUsage() {
		$usage1 = new stdClass();
		$usage1->value = 0;
		$usage1->text = JText::_('COM_HANDOUT_CODES_SINGLE_USE');
		$usage2 = new stdClass();
		$usage2->value = 1;
		$usage2->text = JText::_('COM_HANDOUT_CODES_UNLIMITED');
		return array('0' => $usage1, '1' => $usage2);
	}

	public static function getCodesUser() {
		$user1 = new stdClass();
		$user1->value = 0;
		$user1->text = JText::_('COM_HANDOUT_CODES_ANONYMOUS');
		$user2 = new stdClass();
		$user2->value = 1;
		$user2->text = JText::_('COM_HANDOUT_CODES_REGISTERED');
		$user3 = new stdClass();
		$user3->value = 2;
		$user3->text = JText::_('COM_HANDOUT_CODES_EMAIL_REQUIRED');
		return array('0' => $user1, '1' => $user2, '2' => $user3);
	}

}

/**
* History database table class
* @package HANDOUT_1.0
*/

class HandoutHistory extends JTable  {
	var $id 	= null;
	var $doc_id 	= null;
	var $his_date 	= null;
	var $his_who 	= null;
	var $his_obs 	= null;

	function __construct(&$db)
	{
		parent::__construct('#__handout_history', 'id', $db);
	}
}

/**
* Log database table class
* @package HANDOUT_1.0
*/

class HandoutLog extends JTable {
	var $id 		= null;
	var $log_docid 		= null;
	var $log_ip		= null;
	var $log_datetime 	= null;
	var $log_user 		= null;
	var $log_browser 	= null;
	var $log_os 		= null;

	function __construct(&$db)
	{
		parent::__construct('#__handout_log', 'id', $db);
	}

	/*
	* @desc Deletes download logs entries
	* @param array the log ids as an array
	*/

	function remove($cid) //removeLog
	{
		$db = &JFactory::getDBO();

		if (!is_array($cid) || count($cid) <1) {
			echo "<script> alert(". JText::_('COM_HANDOUT_SELECT_ITEM_DEL') ."); window.history.go(-1);</script>\n";
			return false;
		}

		if (count($cid)) {
			$cids = implode(',', $cid);
			$db->setQuery(
				"DELETE FROM #__handout_log "
				. "\n WHERE id IN ($cids)"
			);

			if (!$db->query()) {
				echo "<script> alert('".$db->getErrorMsg() ."'); window.history.go(-1); </script>\n";
				return false;
			}
		}
		return true;
	}

	function loadRows($cid)
	{
		$db = &JFactory::getDBO();

		if( is_array( $cid ) ) {
			if( count( $cid )){
				$cids = implode(',', $cid);
			}
		} else {
			$cids = $cid;
		}
		if( ! $cids )
			return null;

		$db->setQuery(
			  "SELECT * FROM #__handout_log "
			. "\n WHERE id IN ($cids)"
		);
		return $db->loadObjectlist();
	}
}

