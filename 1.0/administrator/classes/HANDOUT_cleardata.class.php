<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: handout_cleardata.class.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );


if (defined('_HANDOUT_cleardata')) {
    return true;
} else {
    define('_HANDOUT_cleardata', 1);
}

class HANDOUT_CleardataItem{
    /**
     * @abstract
     */
	var $name;

    /**
     * @abstract
     */
    var $friendlyname;

    var $msg;

    /**
     * @static
     */
     function & getInstance( $item ){
        $classname = "HANDOUT_CleardataItem_$item";
        $instance = new $classname;
     	return $instance;
     }

    function clear(){
    	if (!$this->check()) {
    		return false;
    	}
        return true;
    }

    function check(){return true;}
}


/**
 * @abstract
 */
class HANDOUT_CleardataItemTable extends HANDOUT_CleardataItem{
    var $table;
    var $where;
    function clear(){
        if(!$this->check()) {
            return false;
        }
    	$database = &JFactory::getDBO();
        $database->setQuery("DELETE FROM ".$this->table
                            ."\n ".$this->where);
        if( $database->query()){
            $this->msg = JText::_('COM_HANDOUT_CLEARDATA_CLEARED').$this->friendlyname;
            return true;
        } else {
        	$this->msg = JText::_('COM_HANDOUT_CLEARDATA_FAILED').$this->friendlyname;
            return false;
        }
    }
}

class HANDOUT_CleardataItem_handout extends HANDOUT_CleardataItemTable{
	var $name = 'handout';
    var $friendlyname = 'Documents';
    var $table = '#__handout';
}
class HANDOUT_CleardataItem_handout_groups extends HANDOUT_CleardataItemTable{
    var $name = 'handout_groups';
    var $friendlyname = 'User Groups';
    var $table = '#__handout_groups';
}
class HANDOUT_CleardataItem_handout_history extends HANDOUT_CleardataItemTable{
    var $name = 'handout_history';
    var $friendlyname = 'Document History';
    var $table = '#__handout_history';
}
class HANDOUT_CleardataItem_handout_licenses extends HANDOUT_CleardataItemTable{
    var $name = 'handout_licenses';
    var $friendlyname = 'Licenses';
    var $table = '#__handout_licenses';
}
class HANDOUT_CleardataItem_handout_log extends HANDOUT_CleardataItemTable{
    var $name = 'handout_log';
    var $friendlyname = 'Download Logs';
    var $table = '#__handout_log';
}

class HANDOUT_CleardataItem_categories extends HANDOUT_CleardataItemTable{
    var $name = 'categories';
    var $friendlyname = 'Categories';
    var $table = '#__categories';
    var $where = "WHERE section = 'com_handout'";

    function check(){
        $database = &JFactory::getDBO();
        $database->setQuery("SELECT COUNT(*) FROM #__handout");
        if( $database->loadResult() >=1 ){
        	$this->msg = JText::_('COM_HANDOUT_CLEARDATA_CATS_CONTAIN_DOCS');
            return false;
        }
        return true;
    }
}

class HANDOUT_CleardataItem_files extends HANDOUT_CleardataItem{
	var $name = 'files';
    var $friendlyname = 'Files';
    function clear(){
        if(!$this->check()) {
            return false;
        }
        global $_HANDOUT;
        require_once($_HANDOUT->getPath('classes', 'file'));
    	$folder = new HANDOUT_Folder( $_HANDOUT->getCfg('handoutpath' ));
        $files = $folder->getFiles();
        $this->msg = JText::_('COM_HANDOUT_CLEARDATA_CLEARED').$this->friendlyname;
        if( count($files)){
            foreach( $files as $file ){
        	   if( !$file->remove() ){
        		  $this->msg = JText::_('COM_HANDOUT_CLEARDATA_FAILED').$this->friendlyname;
                  return false;
        	   }
            }
        }
        return true;
    }

    function check(){
        $database = &JFactory::getDBO();
        $database->setQuery("SELECT COUNT(*) FROM #__handout");
        if( $database->loadResult() >=1 ){
            $this->msg = JText::_('COM_HANDOUT_CLEARDATA_DELETE_DOCS_FIRST');
            return false;
        }
        return true;
    }
}


class HANDOUT_Cleardata {
	var $items = array();

    /**
     * @constructor
     */
    function HANDOUT_Cleardata( $items = null ){
    	if ( !$items ) {
            $items = array( 'handout', 'categories', 'files', 'handout_groups', 'handout_history', 'handout_licenses', 'handout_log');
        }
        foreach ($items as $item){
        	$this->items[] = & HANDOUT_CleardataItem::getInstance( $item );
        }
    }

    function clear(){
    	foreach( $this->items as $item){
    		$item->clear();
    	}
    }

    function & getList(){
    	return $this->items;
    }

}

