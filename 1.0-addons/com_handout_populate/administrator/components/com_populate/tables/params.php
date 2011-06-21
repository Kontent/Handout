<?php
/**
 * @version		$Id$
 * @category	HandoutPopulate
 * @package		HandoutPopulate
 * @copyright	Copyright (C) 2011 Kontent Design. All rights reserved.
 * @copyright	Copyright (C) 2003 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

class TablePopulateParams 
{

    var $doccounter 			= 0;
    var $checked_out 		= 0;
    var $checked_out_time 	= '0000-00-00 00:00:00';
    var $owner				= 0;
    var $docowner            = 0;
    var $docmaintainedby      = -1;
    var $doclastupdateon 	= null;
    var $docdate_published 	= null;
    var $HANDOUT_version		= null;
    var $handoutpath				= null;


    public function __construct() 
    {
        $this->HANDOUT_version	= _DM_VERSION;
        $this->handoutpath			= PopulateDocman::get()->getCfg( 'handoutpath' );

        // calculated at runtime
        $this->docdate_published = date( 'Y-m-d H:i:s' );
        $this->doclastupdateon	= date( 'Y-m-d H:i:s' );
    }
    
    public static function getInstance()
    {
    	static $instance;
    	if(!isset($instance))
    	{ 
        	$instance = new TablePopulateParams();
    	}
        return $instance;
    }
}