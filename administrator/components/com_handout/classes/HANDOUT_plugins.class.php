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

if (defined('_HANDOUT_PLUGIN')) {
    return;
} else {
    define('_HANDOUT_PLUGIN', 1);
}

/**
* HANDOUT Plugins Class
*
* @desc class purpose is to handle Plugin interactions
*/

class HANDOUT_plugin {
    var $_parms;
    var $_ds;
    var $_error;
    var $_errmsg;
    var $_group;
    var $_trigger;
    var $_pub;
    var $_return;

    function HANDOUT_plugin($trigger = null, $group = 'handout', $pub = false)
    {
        $this->_ds = array();
        $this->_group = $group;
        $this->_trigger = $trigger;
        $this->_pub = $pub;
        $this->_error = null;

        $this->_parms = array();
    }
    // Set all values to array
    function setParmArray(&$name)
    {
        foreach($name as $key => $value) {
            $this->_parms[ $key ] = &$name[$key];
        }
    }
    function setParm($name, &$getFirst)
    {
        if (is_array($name)) {
            $this->_parms += $name ;
        } else {
            if ($getFirst == null) {
                $this->_parms[ $name ] = null ;
            } else {
                $this->_parms[ $name ] = &$getFirst ;
            }
        }
    }

    function copyParm($name, $getFirst = null)
    {
        if (is_array($name)) {
            $this->_parms += $name ;
        } else {
            if ($getFirst == null) {
                $this->_parms[ $name ] = null ;
            } else {
                $this->_parms[ $name ] = $getFirst ;
            }
        }
    }

    function &getParm($name = null)
    {
        if (is_null($name)) {
            return $this->_parms;
        }
        return $this->_parms[ $name ];
    }

    function getError()
    {
        return (is_null($this->_error) ? 0: $this->_error);
    }
    function getErrorMsg()
    {
        return (is_null($this->_errmsg) ? 0: $this->_errmsg);
    }

    /**
    *
    * @desc Get the first occurance of a key
    * @param string $name the name to look for
    * @return Single value
    */
    function getFirst($name)
    {
        if (is_array($this->_return)) {
            foreach($this->_return as $row) {
                if (is_array($row) &&
                        array_key_exists($name , $row)) {
                    return $row[$name];
                }
            }
        }
        return null;
    }

    /**
    *
    * @desc This returns all the strings from all plugins
    * 		that returned a value of 'name'
    * @param string $name the name to look for
    *                 int the category id
    * @return array An array of ALL the matching entrys
    */

    function getAll($name)
    {
        if (is_array($this->_return)) {
            $all = array();
            foreach($this->_return as $row) {
                if (is_array($row) &&
                        array_key_exists($name , $row)) {
                    $all[] = $row[$name];
                }
            }
            return count($all)? $all: null;
        }
        return null;
    }

    /**
    *
    * @desc This performs the MAMBOT call and interfaces
    * 		what we want with what Joomla does
    * @param string $trigger the trigger to call.
    * @param boolean $pub - whether to call unpublished routines
    * @return boolean True or false if an error occured
    */

    function trigger($trigger = null , $pub = false) {

        $app = &JFactory::getApplication();

        $trigger = $trigger ? $trigger : $this->_trigger;
        if ($trigger == null || ! $this->_group) {
            $this->_error = 1;
            $this->_errmsg = JText::_('COM_HANDOUT_ERRORPLUGIN');
            return false;
        }

        $task =  isset($_GET['task']) ? $_GET['task'] : 'unknown';

        // Set required parms
        $this->_parms += array('content_src' => 'handout',
            'task' => $task,
            'mambo_ds' => &$this->_ds);

		JPluginHelper::importPlugin('handout');
		$this->_return = $app->triggerEvent( $trigger, array($this->_parms), $pub);

        $this->_error = $this->getFirst('_error');
        $errmsg = $this->getAll('_errmsg');
        if ($errmsg) {
            $this->_errmsg = implode('\n' , $errmsg);
        }
        return $this->getError();
    }

    function & getReturn() {
    	return $this->_return;
    }
}

