<?php
/**
 * @package 	Handout Notify
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

require_once NOTIFY_PATH.DS.'includes'.DS.'handoutdata.php';

class NotifyFile extends NotifyHandoutData
{
    /**
     * @var string
     */
    protected $_key = 'file';

    /**
     * Populate object with data from Handout
     */
	protected function _loadParams()
    {

        global $_HANDOUT;

		$vars = array( 'name', 'mime', 'ext', 'size', 'date');

        foreach( $vars as $var ) {
            $this->_load( $var );
        }

        if( isset($this->_params['file']))
        {
            if( is_string( $this->_params['file'] ))
            {
                $this->name = $this->_params['file'];
                require_once $_HANDOUT->getPath('classes', 'file');
                $full = new HANDOUT_File($this->name, $_HANDOUT->getCfg('handoutpath'));
                $this->mime 	= $full->mime;
                $this->ext 		= $full->ext;
                $this->size 	= $full->size;
                $this->data		= $full->date;
            }
        }


    }

}