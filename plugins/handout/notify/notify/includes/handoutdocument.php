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

class NotifyDocument  extends NotifyHandoutData
{
    protected $_key = 'document';

    /**
     * Populate object with data from Handout
     */
    protected function _loadParams()
    {
        // load normal document variables
        $vars = array( 'catid', 'id', 'docname', 'docdescription', 'docdate_published', 'docowner', 'docfilename',
                       'docurl', 'doccounter', 'checked_out_time', 'doclastupdateon', 'doclicense_id', 'access');
        foreach( $vars as $var ) {
            $this->_load( $var );
        }


        // load boolean document variables
        $vars = array( 'published', 'checked_out', 'doclicense_display');
        foreach( $vars as $var ) {
            $this->_loadBool( $var );
        }

        // load user related document variables
        $vars = array( 'docowner', 'doclastupdateby', 'docsubmittedby', 'docmaintainedby');
        foreach( $vars as $var ) {
            $this->_loadUser( $var );
        }

        // link
        $ssl = (JFactory::getApplication()->getCfg('force_ssl') == 2) ? 1 : -1;
        $this->link = JRoute::_('index.php?option=com_handout&task=doc_details&gid='.$this->id, 0, $ssl);

		// Category
        global $_HANDOUT;

		require_once $_HANDOUT->getPath('classes', 'model');
		$cat = new HANDOUT_Category($this->catid);
		$this->category = $cat->objDBTable->title;
    }

}