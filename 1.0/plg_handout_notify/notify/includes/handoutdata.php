<?php

/* @version 	$Id: handoutdata.php
 * @package 	Handout Notify
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined('_JEXEC') or die('Restricted access');

class NotifyHandoutData
{
    /**
     * @var string
     *
     */
    protected $_params;

    /**
     * @var string
     */
    protected $_key;


    /**
     * Parameters received from Handout's plugin handling
     */
    public function __construct( & $params ) {
        $this->_params = & $params;
        $this->_loadParams();
    }

    /**
     * Populate object with data from Handout
     */
    protected function _loadParams()
    {
        //overload in child
        die('NotifyHandoutData::_loadParams( must be overloaded');
    }

    protected function _load( $var )
    {
        if( @isset( $this->_params[$this->_key]->$var ) && @$this->_params[$this->_key]->$var )
        {
            $this->$var = $this->_params[$this->_key]->$var;
        }
        else
        {
            $this->$var = JText::_('PLG_HANDOUT_NOTIFY_NOT_AVAILABLE');
        }
    }

    protected function _loadBool( $var )
    {
        if( isset( $this->_params[$this->_key]->$var )  )
        {
            $this->$var = (bool) $this->_params[$this->_key]->$var ? JText::_('PLG_HANDOUT_NOTIFY_YES') : JText::_('PLG_HANDOUT_NOTIFY_NO');
        }
        else
        {
            $this->$var = JText::_('PLG_HANDOUT_NOTIFY_NOT_AVAILABLE');
        }
    }

    protected function _loadUser( $var )
    {
        if( isset( $this->_params[$this->_key]->$var) &&  @$this->_params[$this->_key]->$var  )
        {
            $id = $this->_params[$this->_key]->$var;
            $this->$var = HANDOUT_Utils::getUserName( $id );
        }
        else
        {
            $this->$var = JText::_('PLG_HANDOUT_NOTIFY_NOT_AVAILABLE');
        }
    }


}