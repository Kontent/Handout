<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: handout_utils.class.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (defined('_HANDOUT_UTILS')) {
    return true;
} else {
    define('_HANDOUT_UTILS' , 1);
}

/**
* Handout utils static class
*
* @desc class purpose is to handle generic utils functions
*/
// We need to spec the following this way because of plugins
require_once dirname(__FILE__) . '/HANDOUT_config.class.php';

class HANDOUT_Utils
{
    function stripslashes($post)
    {
    	return get_magic_quotes_gpc() ? HandoutFactory::getStripslashes($post) : $post;
    }

	function loadAdminModules( $position='left', $style=0 ) {
        global $mainframe;

        $modules    =& JModuleHelper::getModules($position);
        $pane       =& JPane::getInstance('sliders');
        echo $pane->startPane("content-pane");

        foreach ($modules as $module) {
            $title = $module->title ;
            echo $pane->startPanel( $title, "$position-panel" );
            echo JModuleHelper::renderModule($module);
            echo $pane->endPanel();
        }

        echo $pane->endPane();
    }
    
	function categoryArray()
    {
        $database = &JFactory::getDBO(); 
        $_HANDOUT_USER = &HandoutFactory::getHandoutUser();

        // get a list of the menu items
        $query = "SELECT c.*, c.parent_id AS parent"
         . "\n FROM #__categories c"
         . "\n WHERE section='com_handout'"
         . "\n AND published AND access <= ".$_HANDOUT_USER->gid
         . "\n ORDER BY ordering" ;

        $database->setQuery($query);
        $items = $database->loadObjectList();
        // establish the hierarchy of the menu
        $children = array();
        // first pass - collect children
        foreach ($items as $v) {
            $pt = $v->parent;
            $list = @$children[$pt] ? $children[$pt] : array();
            array_push($list, $v);
            $children[$pt] = $list;
        }
        // second pass - get an indent list of the items
		jimport( 'joomla.html.html.menu' );
		$array = JHTML::_('menu.treerecurse', 0, '', array(), $children);

        // making sure it's an empty array if there were no results
        // looks silly huh?
        $array = is_array($array) ? $array : array();

        return $array;
    }

    /**
     * @param string  $ The icon name (ex. 'zip.png')
     * @param boolean $ The path type, live (1), absolute (2)
     * @param string  $ Image size
     * @param boolean $ The browser supports png alpha transparency
     * @return string the icon path
     **/
    function pathIcon($icon, $type = null, $size = null )
    {

        $_HANDOUT = &HandoutFactory::getHandout();
        
        $icon_path = $_HANDOUT->_path->themes .'/'. $_HANDOUT->getCfg('icon_theme') . "/images/";

        // set icon size
        if (!isset($size)) {
            //$icon_path .= $_HANDOUT->getCfg('icon_size') ? "64x64/" : "32x32/";
            $icon_path_size = $_HANDOUT->getCfg('icon_size') ? "icon-64-" : "icon-32-";
        } else {
            //$icon_path .= $size . "/";
            $icon_path_size = "icon-" . substr($size, 0, 2) . '-';
        }

		//Testing to see which folder the icon is in b/c of the change in folder structure. 
		//TO DO: This needs to be re-worked  
		if (file_exists(JPATH_ROOT . $icon_path . 'icons/' . $icon_path_size . $icon)) {
			$icon_path .= 'icons/' . $icon_path_size;		
		}
		else {
			$icon_path .= $icon_path_size;
		}

        // set path type
        $path_type = '';
        switch($type)
        {
            case 1 : $path_type = JURI::root()  ; break;
            case 2 : $path_type = JPATH_ROOT . "/";   break;
            default : break;
        }

        $outcome = $path_type . $icon_path . $icon;
        $outcome = str_replace('http://','#HTTP#',$outcome);
        $outcome = str_replace('https://','#HTTPS#',$outcome);
        $outcome = str_replace('//','/',$outcome);
        $outcome = str_replace('#HTTPS#','https://',$outcome);
        $outcome = str_replace('#HTTP#','http://',$outcome);

        return $outcome;
    }

    function pathThumb($thumbnail, $path="images/stories/handout/")
    {        
        $thumb_path = JURI::root() . $path . $thumbnail;
        return $thumb_path;
    }

    function implode_assoc($inner_glue = "=", $outer_glue = "\n", $array = null, $keepOuterKey = false)
    {
        $output = array();

        foreach($array as $key => $item)
        if (is_array ($item)) {
            if ($keepOuterKey)
                $output[] = $key;
            // This is value is an array, go and do it again!
            $output[] = implode_assoc($inner_glue, $outer_glue, $item, $keepOuterKey);
        } else
            $output[] = $key . $inner_glue . $item;

        return implode($outer_glue, $output);
    }

    function &get_object_vars($object)
    {
        $ar1 = get_class_vars(get_class($object));
        $ar2 = get_class_vars(get_parent_class($object));
        $ar = HANDOUT_Utils::array_diff_key($ar1, $ar2);

        $object_vars = new stdClass();
        foreach($ar as $key => $value)
        $object_vars->$key = $object->$key;

        return $object_vars;
    }

    function array_diff_key()
    {
        $arrays = func_get_args();
        // if only one array is given as argument, just return it
        if (count($arrays) == 1)
            return $arrays;
        elseif (count($arrays) < 1) {
            trigger_error(JText::_('COM_HANDOUT_NOTARGGIVEN') . ", " .
                count($arrays) . " given, > 1 needed", E_USER_WARNING);
            return false;
        }
        $array1 = array_shift($arrays);
        foreach ($array1 as $key => $val) {
            for ($i = 0; $i < count($arrays); $i++) {
                $array = &$arrays[$i];
                if (!is_array($array)) {
                    trigger_error(JText::_('COM_HANDOUT_ARG') . " $i " . JText::_('COM_HANDOUT_ISNOTARRAY'), E_USER_WARNING);
                    return false;
                }
                if (isset($array[$key])) {
                    unset($array1[$key]);
                }
            }
        }
        return $array1;
    }

    function taskLink($task, $gid = '', $params = null, $sef = true)
    {
        $link = HANDOUT_Utils::_rawLink($task, $gid, $params);
        $link = htmlspecialchars($link);
		
		$link = $sef ?  JRoute::_($link) : $link;
        return $link;
    }

    function returnTo($task, $msg = '', $gid = '', $params = null)
    {
        $mainframe = &JFactory::getApplication();
    	$link = HANDOUT_Utils::_rawLink($task, $gid, $params);
        $mainframe->redirect($link, $msg);
    }

    function _rawLink($task, $gid = '', $params = null)
    {
        $limitstart = JRequest::getInt('limitstart');  
        $limit = JRequest::getInt('limit');
		$order = JRequest::getVar('order');
		$dir = JRequest::getVar('dir');
        $Itemid = JRequest::getInt('Itemid');
		$indexfile = 'index.php';

        $link = "$indexfile?option=com_handout";

        if(!isset($params['Itemid']))
            $params['Itemid'] = $Itemid ? $Itemid : HANDOUT_Utils::getItemid();

        if (!empty($task))
            $link .= "&task=$task";
        if (!empty($gid))
            $link .= "&gid=$gid";
        if (!empty($limit))
            $link .= "&limit=$limit";
        if (!empty($limitstart))
            $link .= "&limitstart=$limitstart";
        if (!empty($order))
            $link .= "&order=$order";
        if (!empty($dir))
            $link .= "&dir=$dir";
        if (is_array($params))
            $link .= "&" . HANDOUT_Utils::implode_assoc('=', '&', $params);



        return $link;
    }


    //  @desc returning diff in days.
    //  @args string date in format dd-mm-yyyy
    //  @return int 0 is today. positive is future
    function DaysDiff($handoutDate)
    {
        $data_exp = explode("-", $handoutDate);
        $Y = intval($data_exp[0]);
        $m = intval($data_exp[1]);
        $d = intval($data_exp[2]);
        $diff = ((mktime(0, 0, 0, $m, $d, $Y) - mktime(0, 0, 0, date("m"), date("d"), date("Y"))) / 86400) ;
        if (abs($diff) == $diff) // it's positive so use ceil
            $diff = ceil($diff);
        else // it's negative so use floor
            $diff = floor($diff);
        return $diff;
    }

    //  @desc Safely decode a URL that was base64 encoded.
    //  @args string that might be encoded.
    //  @return string that is clean
    function safeDecodeURL(&$url)
    {
        if (substr($url , 0 , 6) == 'SEURL_') {
            $url = base64_decode(substr($url, 6));
        }
        return $url;
    }

    function safeEncodeURL($url)
    {
        return 'SEURL_' . base64_encode($url);
    }
/*
    //  @desc Convert a text string to a number string
    // The INPUT string can be any format:
    // +-nnn,nnnn.nn X
    // Where: +-nnn,nnnn.nn  is the number string
    // and X is K(ilobytes), M(egabytes) or G(igabytes)
    // Conversion gets rid of floating point stuff
    //  @args string Text string to be changed
    function text2number($textString) {
        $bytes = trim($textString);
        $itype = 0;

        $localinfo = localeconv();
        $dpoint = $localinfo['decimal_point'] ? $localinfo['decimal_point'] : '.';
        $markerString = '+-0123456789, .'
         . $dpoint
         . $localinfo['thousands_sep'];
        $marker = strspn($bytes , $markerString);
        if ($marker !== false && $marker != strlen($bytes)) {
            $type = strtolower(substr($bytes, $marker, 1));
            $itype = strpos('bkmgt' , $type);
            if ($itype === false) {
                $itype = 0;
            } else {
                $bytes = substr($bytes , 0 , $marker);
            }
        }
        $bytes = preg_replace("/[^\\" . $dpoint . '\d+-]/' , '', $bytes);
        if ($dpoint != '.') {
            $bytes = preg_replace('/[' . $dpoint . ']/' , '.' , $bytes);
        }
        $bytes = intval($bytes * pow(1024 , $itype));
        return $bytes ;
    }
    */

    /**
     * Alternative version
     */
    function text2number($val) {
        $val = preg_replace( "/[^0-9KMGkmg]/", '', $val);
        $last = strtolower($val{strlen($val)-1});
        switch($last) {
            case 'g':
                $val *= 1024;
                //pass through...
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }


    // Reverse of above function.
    function number2text($value) {
        $index = 0;
        $pow_label = ' KMGT?';

        if (is_numeric($value) && $value > 1023) {
            while (($value % 1024) == 0) {
                $value /= 1024;
                $index++;
            }
        }
        $value = number_format($value, 0, '.', '');

        $return  = trim($value . substr(' KMGT' , $index, 1));
        return $return;
    }

    //  @desc Translate the numeric ID to a character string
	//  @param integer $ The numeric ID of the user
	//  @return string Contains the user name in string format
	function getUserName($userid)
	{
 		$database = &JFactory::getDBO();

   		switch ($userid)
   		{
     		case COM_HANDOUT_PERMIT_EDITOR:
     			return JText::_('COM_HANDOUT_GROUP_EDITOR');
     			break;

     		case COM_HANDOUT_PERMIT_AUTHOR:
     			return JText::_('COM_HANDOUT_GROUP_AUTHOR');
     			break;
     		case COM_HANDOUT_PERMIT_PUBLISHER:
     			return JText::_('COM_HANDOUT_GROUP_PUBLISHER');
     			break;
     		case COM_HANDOUT_PERMIT_EVERYBODY:
        		return JText::_('COM_HANDOUT_EVERYBODY');
           		break;
     		case COM_HANDOUT_PERMIT_REGISTERED:
         		return JText::_('COM_HANDOUT_ALL_REGISTERED');
           		break;
            case COM_HANDOUT_PERMIT_CREATOR:
                return JText::_('COM_HANDOUT_CREATOR');
                break;

       		default:

          		if ($userid > '0')
           		{
                    $user = & HANDOUT_users::get($userid);
                    return $user->username;
          		}

				if($userid < '-5')
				{
      				$calcgroups = (abs($userid) - 10);
                    $group = & HANDOUT_groups::get($calcgroups);
        			return $group->groups_name;
				}
            	break;
   		}

   		return '*'.JText::_('COM_HANDOUT_UNKNOWN').'*';
	}

	function checkDomainAuthorization()
	{
		
		$_HANDOUT = &HandoutFactory::getHandout();

		if(!$_HANDOUT->getCfg('security_anti_leech')) {
			return true;
		}

		$this_url = parse_url(JURI::root());
        $this_host = trim($this_url['host']);

        if (isset($_SERVER['HTTP_REFERER'])) {
        	$from_url = parse_url($_SERVER['HTTP_REFERER']);
        	$from_host = trim($from_url['host']);
		}
	    else {
			$from_host = "";
	    }

        // Determine if they are local. They must:
        // 	1. match the defined server string
        //  2. match the local address or have 'localhost' as their hostname.
        // The last one is unlikely, but this will catch any case at all.
		// If $from_host (remote) is empty, it's considered local, too.

        if ( empty($from_host) || strcasecmp($this_host, $from_host) == 0 ||
				strcasecmp('127.0.0.1', $from_host) == 0 ||	strcasecmp('localhost', $from_host) == 0 )
		{
            $localhost = true;
        }
		else
		{
			$localhost = false;
		}

		$allowed = false;

        // If the connection is NOT local, check if the remote host is allowed.
        if ( !$localhost )
		{
			$allowed_hosts = explode('|',$_HANDOUT->getCfg('security_allowed_hosts'));

			//  If the $allowed_hosts list is empty, the remote host is not allowed by default.
			if ( count($allowed_hosts > 0) )
			{
				foreach ( $allowed_hosts as $allowed_host )
				{
					$allowed_host = HANDOUT_Utils::wild2regular(trim($allowed_host));
					if ( strlen($allowed_host) == 0 ) continue;
					$allowed_host .= 'i'; // make pattern case-insensitive
					if ( preg_match($allowed_host, $from_host)) {
						$allowed = true;
						break;
					}
				}
			}
		}

		return $localhost || $allowed;
	}

	function wild2regular($pattern)
	{
		if ( strlen($pattern) == 0 ) {
			return $pattern;
		}

		$pattern = preg_quote($pattern);
		$pattern = str_replace('/','\/',$pattern);
		$pattern = str_replace('\*','\w*',$pattern);
		$pattern = str_replace('\?','\w',$pattern);
		$pattern = '/'.$pattern.'/';

		return $pattern;
	}

    function getModuleIdByName( $name ) {
    	static $modules;
        if( !isset($modules)) {
            $database = &JFactory::getDBO();
        	$database->setQuery( "SELECT id, module FROM #__modules" );
            $rows = $database->loadObjectList();
            foreach( $rows as $row ) {
            	$modules[$row->module] = $row->id;
            }
        }

        if(isset($modules["mod_$name"])) {
            return $modules["mod_$name"];
        } else {
        	return false;
        }

    }

    function processContentPlugins( & $doc) {
        $_HANDOUT = HandoutFactory::getHandout();

        if(!$_HANDOUT->getCfg('process_bots', 0)) {
            return;
        }
			
		if ($doc instanceof HANDOUT_Document)
		{
			// get data from object
			$dataObj    = & $doc->getDataObject();
			$comments = HANDOUT_Utils::processKunenaDiscussPlugin($dataObj);
			$dataObj->kunena_discuss_contents = $comments ;
			$dataObj->kunena_discuss_count = substr_count($comments, 'class="kdiscuss-item');
		}	
    }
	
    function processKunenaDiscussPlugin ($dataObj) {
        $mainframe = &JFactory::getApplication('site');        

        // initialize objects
        $params     = new JParameter( '' ); // fake params
        $row        = new stdClass();
	
		if (JRequest::getVar('task')=='doc_details') {
			//Kunena Discuss settings
			$row->id = '100000' + $dataObj->id; // high article id
			$row->catid = $dataObj->catid;	
			$row->text ='';
			$row->title = $dataObj->docname . ' (Handout ID: ' . $dataObj->id . ")";
			if ($dataObj->kunena_discuss_id)
			{
				$row->text  = '{kunena_discuss:'.$dataObj->kunena_discuss_id.'}';
			}
			$mainframe->scope = 'com_content'; //most content plugins will work only for com_content
	
			//Load specific content plugins
			JPluginHelper::importPlugin('content', 'kunenadiscuss');
		}	

        $mainframe->triggerEvent( 'onPrepareContent', array( &$row, &$params, 0 ), true );
        $results = $mainframe->triggerEvent( 'onAfterDisplayContent', array( &$row, &$params, 0 ), true );		
		return trim(implode("\n", $results));
    }

    /**
     * Does the browser support PNG Alpha transparency?
     */
  //  function supportPng() {
    //    $_HANDOUT = &HandoutFactory::getHandout();
    //    jimport( 'joomla.environment.browser' );
    //    $browser = & JBrowser::getInstance();
    //    return !$browser->getQuirk('png_transparency');
   // }

    /**
     * Substr replacement, doesn't break words in the middle
     */
    function snippet($text,$length=200,$tail="(...)") {
       $text = trim($text);
       $txtl = strlen($text);
       if($txtl > $length) {
           for($i=1;$text[$length-$i]!=" ";$i++) {
               if($i == $length) {
                   return substr($text,0,$length) . $tail;
               }
           }
           $text = substr($text,0,$length-$i+1) . $tail;
       }
       return $text;
    }

    /**
     * Snips an string in the middle eg http://www.mysite(...)/myfile.zip
     */
    function urlSnippet($text,$length=60,$tail="(...)") {
        $text = trim($text);
        $txtl = strlen($text);
        if($txtl > $length) {
            $snip = floor(($length-strlen($tail)) / 2);
            $text = substr($text, 0, $snip) . $tail . substr($text, -$snip, $snip);
        }
        return $text;
    }

    /**
     * Custom tooltip method
     * When no href ( or '#') is given, the icon will become a link to a
     * js alert box
     */
    function mosToolTip( $tooltip, $title='', $width='', $image='../../../media/com_handout/images/icon-16-tooltip.png', $text='', $href='#', $link=1  ) {
        if ( $href=='#' OR $href=='') {
            $alert = strip_tags(HANDOUT_Utils::br2nl($tooltip));
        	$href="javascript:alert('".$alert."');";
        }
    	return HandoutFactory::getToolTip( $tooltip, $title, $width, $image, $text, $href, $link );
    }

    function br2nl($text){
       $text = str_replace("<br />","\\n",$text);
       $text = str_replace("<br>","\\n",$text);
       return $text;
   }

    /**
     * Get Handout's Itemid
     * We can't use global $Itemid because this class can be used outside of
     * Handout
     */
    function getItemid( $component='com_handout') {
    	static $ids;
        if( !isset($ids) ) {
        	$ids = array();
        }
        if( !isset($ids[$component]) ) {
        	$database = &JFactory::getDBO();
            $query = "SELECT id FROM #__menu"
                    ."\n WHERE link LIKE '%option=$component%'"
                    ."\n AND type = 'component'"
                    ."\n AND published = 1";
            $database->setQuery($query, 0, 1);
            $ids[$component] = $database->loadResult();
        }
        return $ids[$component];
    }

}

/**
* Handout document utils static class
*
* @desc class purpose is to handle generic utils functions
*/

class HANDOUT_Cats
{
    /**
    *
    * @desc This function selects every child category
    * 		from a parent category by user access level
    * @param object $ the user object
    * @param int $ the parent id category
    * @param string $ the ordering query
    * @returns array a db object with category rows
    */
    function getChildsByUserAccess($parent_id = 0, $ordering = "ordering ASC", $userID = null)
    {
        $database = &JFactory::getDBO();
        $_HANDOUT = &HandoutFactory::getHandout();

        if (! $userID) {
            $user = $_HANDOUT->getUser();
        } else {
            $user = &$userID;
        }

        $query = "SELECT * FROM #__categories "
         . "\n WHERE section = 'com_handout'"
         . "\n   AND published = 1 "
         . "\n   AND parent_id=". (int) $parent_id ." AND ";

        if($user->userid) {
        	if($user->isSpecial) {
        		$query .= "(access=0 OR access=1 OR access=2)";
        	} else {
        		$query .= "(access=0 OR access=1)";
        	}
        } else {
        	$query .= "access=0";
        }

        $query .= " ORDER BY " . $ordering;
        $database->setQuery($query);
        $childs = $database->loadObjectList();
        return $childs;
    }

    // -- Dirty solution - Arrays needs to be merged.
    function countDocsInCatByUser($catid, $user, $include_childs = false) {
        $_HANDOUT = &HandoutFactory::getHandout(); $database = &JFactory::getDBO();


        /*
         * -- Count the documents per category --
         */

        $query = "SELECT catid, count( d.id ) AS count "
         . "\n FROM #__handout AS d";

        if (!$user->userid/*&& !$_HANDOUT->getCfg('registered')*/) {
            $query .= "\n   WHERE docowner=" . COM_HANDOUT_PERMIT_EVERYONE
             . "\n   AND d.published=1 ";
        } elseif ($user->isSpecial) {
           $query .= " ";
        } elseif ($user->canPublish()) {
        	 $query .= " ";
        } elseif ($user->userid) {
            $query .= "\n WHERE (docowner=" . $user->userid
             . "\n OR docmaintainedby=" . $user->userid
             . "\n OR docowner=" . COM_HANDOUT_PERMIT_EVERYONE
             . "\n OR docowner=" . COM_HANDOUT_PERMIT_REGISTERED;
            if ($user->groupsIn != '0,0') {
                $query .= "\n OR docowner IN (" . $user->groupsIn . ")";
            }
            $query .= "\n)";
            $query .= "\n  AND d.published=1";
        }
        $query .= "\n GROUP BY d.catid";

        // Performance: each query should only be executed once
        static $docresults = array();
        if( !isset( $docresults[$query])) {
            $database->setQuery($query);
            $docresults[$query] = $database->loadObjectList('catid');
        }
        $docs = & $docresults[$query];


        /*
         * -- Get a category hierarchy --
         */
        // Performance: query should only be executed once
        static $cats;
        if( !isset($cats)) {
            $query = "SELECT c.id, c.parent_id AS parent"
             . "\n FROM #__categories AS c"
             . "\n WHERE section='com_handout'"
             . "\n AND published <> -2"
             . "\n ORDER BY ordering" ;
            $database->setQuery($query);
            $cats = $database->loadObjectList();
        }

        $total = 0;
        if ($include_childs) {
            HANDOUT_Cats::countDocsInCatRecurse($catid, $cats, $docs, $total);
        }

        if (isset($docs[$catid])) {
            $total += $docs[$catid]->count;
        }

        return $total;
    }

    function countDocsInCatRecurse($id, &$cats, &$docs, &$total)
    {
        $i = 0;
        $size = count($cats);
        for($i; $i < $size; $i++) {
            if ($cats[$i]->parent == $id) {
                $new_id = $cats[$i]->id;
                if (isset($docs[$new_id])) {
                    $total += $docs[$new_id]->count;
                }
                HANDOUT_Cats::countDocsInCatRecurse($new_id, $cats, $docs, $total);
            }
        }
    }

    function & getAncestors($id)
    {
        $database = &JFactory::getDBO();
        static $results = array();

        if( !isset( $results[$id] ) ) {
            // get a category hierarchy
            $query = "SELECT id, name, title, parent_id AS parent"
             . "\n FROM #__categories"
             . "\n WHERE section='com_handout'"
             . "\n AND published <> -2"
             . "\n ORDER BY ordering" ;
            $database->setQuery($query);
            $cats = $database->loadObjectList('id');

            $arAncestors = array();
            HANDOUT_Cats::getAncestorsRecurse($id, $cats, $arAncestors);
            $results[$id] = $arAncestors;
        }
        return $results[$id];
    }

    function getAncestorsRecurse($id, &$cats, &$ancestors)
    {
        $cat = new StdClass();
        $cat->title = $cats[$id]->title;
        $sef = false;
        $cat->link = HANDOUT_Utils::taskLink('cat_view', $id, null, $sef);
        $ancestors[] = &$cat;

        $id = $cats[$id]->parent;
        if ($id != 0) {
            HANDOUT_Cats::getAncestorsRecurse($id, $cats, $ancestors);
        }
    }

    /**
     * Returns an array of category objects with their id as key
     */
    function & getCategoryList(){
    	static $list;

        if( !isset($list) ) {
        	$database = &JFactory::getDBO();
            $database->setQuery( "SELECT * FROM #__categories" .
                                "\n WHERE section = 'com_handout'" );
            $list = $database->loadObjectList( 'id' );
        }
        return $list;
    }
}

class HANDOUT_Docs
{
    /**
    *
    * @desc This function selects every documents in a category
    * 		by user access level
    * @param mixed $catid Integer or comma separated string of catids
    * @param int $ the category id
    * @param string $ the ordering query
    */
    function getDocsByUserAccess($catid = 0, $ordering = '', $direction = '', $limit = '', $limitstart = 0, $where='')
    {
		$database = &JFactory::getDBO();
        $_HANDOUT = &HandoutFactory::getHandout();
        $user = $_HANDOUT->getUser();
        // get ordering
        $ordering = trim($ordering);
        if ($ordering == '')
            $ordering = $_HANDOUT->getCfg('default_order');

        switch ($ordering) {
            case 'name' : $ordering = 'd.docname';
                break;
            case 'date' : $ordering = 'd.docdate_published';
                break;
            case 'hits' : $ordering = 'd.doccounter';
                break;
            default :
                $ordering = 'd.docname';
        }
        // get direction
        $direction = (string) strtoupper(trim($direction));
        if ($direction == '') {
            $direction = $_HANDOUT->getCfg('default_order2');
        }
        if(!in_array($direction, array('ASC', 'DESC')) ) {
        	$direction = 'ASC';
        }

        // get limit
        if ($limit == '') {
            $limit = $_HANDOUT->getCfg ( 'perpage' ) ;
        }
        // preform query
        $query = "SELECT d.*, c.title AS cat_title FROM #__handout AS d"
        	. "\n LEFT JOIN #__categories AS c ON d.catid = c.id ";

         if (!$user->userid)
         {
         	if(!$_HANDOUT->getCfg('registered')) {
         		return array();
         	}

            $query .= "WHERE d.docowner=" . COM_HANDOUT_PERMIT_EVERYONE
                 . "\n AND d.published=1";

           	$query .= $catid ? "\n AND d.catid IN ($catid) " : "";

        }
        else
       	{
        	if ($user->isSpecial) {
        		 $query .= $catid ? "\n WHERE d.catid=$catid " : "";
        	} elseif ($user->canPublish()) {
        	 	$query .= $catid ? "\n AND d.catid=$catid " : "";
        	} elseif ($user->userid) {
            	$query .= "WHERE d.published=1"
             		. "\n AND (d.docowner=" . $user->userid
             	 	. "\n OR d.docmaintainedby=" . $user->userid
             	 	. "\n OR d.docowner=" . COM_HANDOUT_PERMIT_EVERYONE
             	 	. "\n OR d.docowner=" . COM_HANDOUT_PERMIT_REGISTERED;
           	 	if ($user->groupsIn != '0,0') {
                	$query .= "\n OR d.docowner IN (" . $user->groupsIn . ")";
                	$query .= "\n OR d.docmaintainedby IN (" . $user->groupsIn . ")";
           		}
            	if ($_HANDOUT->getCfg('author_can') != COM_HANDOUT_AUTHOR_NONE) {
                	$query .= "\n OR d.docsubmittedby = " . $user->userid;
            	}
            	$query .= ")";

            	$query .= $catid ? "\n AND d.catid=$catid " : "";
        	}
         }

		$query .= $where; //additional where conditions
        $query .= "\n ORDER BY $ordering $direction";
        $database->setQuery($query, $limitstart, $limit);

        return $database->loadObjectList();
    }

    function getFilesByUserAccess($extra_files = null)
    {
    	$database = &JFactory::getDBO();
    	$_HANDOUT_USER = &HandoutFactory::getHandoutUser();

        if (! $_HANDOUT_USER->userid) {
            return null;
        }

        $doq = false;
        // perform query
        $query = "SELECT * FROM #__handout "
         . "\n WHERE "
         . "\n    ( ";

        $where = '';
        if (! $_HANDOUT_USER->isSpecial) {
            $doq = true;
            $where .= "\n  docsubmittedby=" . $_HANDOUT_USER->userid . "\n  ";
        }
        if ($extra_files) {
            if ($doq) {
                $query .= "  OR " ;
            }
            if (is_array($extra_files)) {
                $where .= "docfilename in ( '" . implode("','", $extra_files) . "')\n  ";
            } else {
                $doq = true;
                $where .= "docfilename = '" . $extra_files . "'\n  ";
            }
        }

        if ($where == '') {
            return array();
        }

        $query .= $where;
        $query .= "  )"
         . "\n ORDER BY docfilename";

        $database->setQuery($query);

        return $database->loadObjectList();
    }

    /**
    *
    * @desc This function performs a generic search
    * 		against the database. Originaly from the plugin
    * 		but enhanced for wider searches
    * @param array $ of arrays $searchArray The lists of what to search for
    * 		i.e.: array( array( 'phrase'=>'search phrases', mode=>'exact'),
    * 			         array( 'phrase=>'.....
    * 		Currently only uses the FIRST array request. (FUTURE: multiples)
    * @param string $ The ordering of the results (newest...etc).
    * 		Prefix with a '-' to reverse the ordering.
    * @param int $ the categories to search for (0=all)
    * @param mixed $ Either an array of terms to return or '*'
    * 		(Array is 'column-name' => 'return name'.)
    * @param array $ List of options for searching
    *
    * NOTE: We are NOT assured that we have $_HANDOUT and all the other goodies.
    * 	    (we may be just from plugin)
    */
    function search(&$searchArray, $ordering = '', $cats = '', $columns = '', $options = array())
    {
        $database = &JFactory::getDBO();
        $user = &JFactory::getUser();
        $_HANDOUT = &HandoutFactory::getHandout();
        $_HANDOUT_USER = $_HANDOUT->getUser();

        $searchterms = array_pop($searchArray); // Only do one (for now)
        if ( empty($options) ) {
            $options = array('search_name', 'search_description');
        }

        if($ordering == '') {
        	$ordering = 'newest';
        }

        $registered = $_HANDOUT->getCfg('registered');
        $perpage = $_HANDOUT->getCfg('perpage');
        $authorCan = $_HANDOUT->getCfg('author_can', '9999');

        $userid = intval($user->id);
        $isAdmin = $_HANDOUT_USER->isAdmin;


        // -------------------------------------
        // Fetch the search options. Passed in options array
        // -------------------------------------
        $search_col = array();
        if (is_array($options)) {
            if (in_array('search_name', $options)) {
                $search_col[] = 'HANDOUT.docname ';
            }
            if (in_array('search_description', $options) || in_array('search_desc', $options)) {
                $search_col[] = 'HANDOUT.docdescription ';
            }
            if (in_array('search_cat' , $options)) {
                $search_col[] = "CAT.title ";
                $search_col[] = "SUB.title ";
            }
        }

        if (count($search_col) == 0) {
            return array(); // Have to search SOMETHING!
        }
        // BUILD QUERY PARTS
        $search_mode = $searchterms['search_mode'];
        $text = trim($searchterms['search_phrase']);
        // fix for http://joomlacode.org/gf/project/docman/tracker/?action=TrackerItemEdit&tracker_item_id=7999
        $text = htmlentities($text, ENT_QUOTES);
        if (! $text) {
            return array();
        }
        // (1) Format search 'phrase' into SQL
        $invert = false;
        if (substr($search_mode , 0 , 1) == '-') {
            $invert = true;
            $search_mode = substr($search_mode, 1);
        }

        $wheres = array();
        switch ($search_mode) {
            case 'exact':
                foreach($search_col as $col) {
                    $wheres[] = $col . "LIKE '%$text%'";
                }

                $where = '(' . implode(') OR (' , $wheres) . ')';
                break;

            case 'any': // Fall through for regex
                $text = implode('|', explode(' ', $text));

            case 'regex':
                foreach($search_col as $col) {
                    $wheres[] = $col . "RLIKE '$text'";
                }

                $where = '(' . implode(' OR ' , $wheres) . ')';
                break;

            case 'all':
            default:
                $words = explode(' ', $text);
                foreach($search_col as $col) {
                    $wheres2 = array();
                    foreach ($words as $word) {
                        $wheres2[] = $col . "LIKE '%$word%'";
                    }

                    $wheres[] = implode(' AND ' , $wheres2) ;
                }
                $where = '(' . implode(') OR (', $wheres) . ')';
                break;
        }
        if ($invert) {
            $where = 'NOT ( ' . $where . ')';
        }
        // DEBUG:
        // echo "<pre>WHERE is: $where</pre>";
        // (2) Create the 'ORDER BY' section based on user request
        $searchSortOrder = array(
            'newest' => 'HANDOUT.doclastupdateon DDD',
            'oldest' => 'HANDOUT.doclastupdateon AAA',
            'popular' => 'HANDOUT.doccounter DDD',
            'alpha' => 'HANDOUT.docname AAA',
            'category' => 'CAT.title AAA, SUB.title AAA, HANDOUT.docname AAA'
            );
        $searchPattern = array('/DDD/', '/AAA/');
        $invert = false;
        if (substr($ordering , 0 , 1) == '-') {
            $ordering = substr($ordering, 1);
            $invert = true;
        }

        $order = $searchSortOrder[$ordering ];


        if ($invert) {
            $order = preg_replace($searchPattern ,
                array('ASC' , 'DESC') , $order);
        } else {
            $order = preg_replace($searchPattern ,
                array('DESC' , 'ASC') , $order);
        }
        // (3) SQL WHERE portion based on user access priviledges
        if ($isAdmin) {
            $user_filter = " (SUB.access=" . COM_HANDOUT_ACCESS_PUBLIC . " OR   SUB.access=" . COM_HANDOUT_ACCESS_REGISTERED . ")" ;
        } else {
            if ($userid > 0) { // Logged IN
                $user_groups = HANDOUT_Docs::_handoutCheckGroupsUserIn();
                $user_filter = "("
                 . "\n    HANDOUT.docowner=" . COM_HANDOUT_PERMIT_EVERYONE
                 . "\n OR HANDOUT.docowner=" . COM_HANDOUT_PERMIT_REGISTERED
                 . "\n OR HANDOUT.docowner=" . $userid
                 . "\n OR HANDOUT.docowner IN ($user_groups) "
                 . "\n OR HANDOUT.docmaintainedby=" . $userid
                 . "\n OR HANDOUT.docmaintainedby IN ($user_groups) " ;
                if ($authorCan > 0) {
                    $user_filter .= "\n OR HANDOUT.docsubmittedby = $userid";
                }
                $user_filter .= ")"
                 . "\n AND (SUB.access=" . COM_HANDOUT_ACCESS_PUBLIC
                 . "\n OR   SUB.access=" . COM_HANDOUT_ACCESS_REGISTERED . ")" ;
            } else { // NOT logged in
                $user_filter = " HANDOUT.docowner=" . COM_HANDOUT_PERMIT_EVERYONE
                 . "\n AND SUB.access=" . COM_HANDOUT_ACCESS_PUBLIC;
            } // endif $userid
        } // endif isAdmin
        // (4)Build up the category list (if they selected it)

        if ($cats != '' && $cats != 0) {
            $user_filter .= "\n AND HANDOUT.catid ";
            if (is_array($cats)) {
                $user_filter .= 'IN (' . implode(',' , $cats) . ')';
            } else {
                $user_filter .= "= $cats";
            }
        }
        // (5) Build up list of columns to return
        if (is_array($columns)) {
            foreach($columns as $key => $value) {
                $list[] = "\n\t$key  AS $value";
            }
            $list_terms = implode(',' , $list);
        } else {
            if ($columns != '' && $columns != '*') {
                $list_terms = $columns;
            } else {
                $list_terms = 'HANDOUT.* , HANDOUT.catid AS HANDOUT_catid';
            }
        }
        // (*) Build final query for SQL lookup
        $query = "SELECT $list_terms "
         . "\nFROM #__handout AS HANDOUT "
         . "\nLEFT JOIN #__categories AS SUB ON SUB.id = HANDOUT.catid"
         . "\nLEFT JOIN #__categories AS CAT ON CAT.id = SUB.parent_id"
         . "\nWHERE $user_filter "
         . "\n  AND HANDOUT.published=1 "
         . "\n  AND ($where) "
         . "\nORDER BY $order";
         

        // TODO: add proper pagination instead of hardcoded limit?
        $database->setQuery($query, 0, 20);
        $rows = $database->loadObjectList();


        $cache = array(); // Fill in the correct sections
        for($r = 0; $r < count($rows); $r++) {
            $rows[$r]->section = @$options['section_prefix']
                        .HANDOUT_Docs::_handoutSearchSection($rows[$r]->catid, $cache , '/')
                        .@$options['section_suffix'];
        }
        // FINAL SORT:
        // We couldn't sort by category until now (we didn't HAVE a category)
        if ($order == 'category') {
            if ($invert) {
                usort($rows , create_function("$a,$b","return strcasecmp($a->section . $a->docname , $b->section . $b->docname);"));
            } else {
                usort($rows , create_function("$a,$b","return strcasecmp($b->section . $b->docname , $a->section . $a->docname);"));
            }
        }
        return $rows;
    }
 /*
 * This is a similar to a routine handout.php but I've moved the id check to the
 * database SQL and altered the string build operation
 */

    function _handoutCheckGroupsUserIn()
    {
        $user = &JFactory::getUser(); 
        $database = &JFactory::getDBO();

        $this_user = intval($user->id);
        $prefix = '';

        $query = "SELECT groups_id " . "FROM   #__handout_groups " . "WHERE  groups_members REGEXP '(^|[^0-9])0*$this_user([^0-9]|$)'" ;
        $database->setQuery($query);
        $all_groups = $database->loadObjectList();

        $user_groups = '';
        if (count($all_groups)) {
            foreach ($all_groups as $a_group) {
                $user_groups .= $prefix . trim((-1 * $a_group->groups_id)-10);
                $prefix = ',';
            }
        }
        if ($user_groups == '')
            return("0,0");

        return ($user_groups);
    }

    function _handoutSearchSection($id , &$cache , $sep)
    {
        $database = &JFactory::getDBO();

        if (! $id) return "";
        if ( isset($cache[ $id ]) ) return $cache[ $id ];

        // Find it...
        $query = "SELECT parent_id, title FROM #__categories WHERE id = ". (int) $id;
        $database->setQuery($query);
        $row = $database->loadObjectList();
        if (count($row)) {
            if ($row[0]->parent_id) {
                $cache[ $id ] = HANDOUT_Docs::_handoutSearchSection($row[0]->parent_id, $cache, $sep) . $sep . $row[0]->title ;
            } else {
                $cache[ $id ] = $row[0]->title;
            }
        }
        return $cache[ $id ];
    }


}
