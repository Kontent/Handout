<?php
/**
 * @category	HandoutPopulate
 * @package		HandoutPopulate
 * @copyright	Copyright (C) 2011 Kontent Design. All rights reserved.
 * @copyright	Copyright (C) 2003 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link	 	http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

class TablePopulateConf extends JTable
{

	var $id = 1;

	// these files won't be added to the database
	var $skipfiles			= null;

	// default description
	var $docdescription 		= '';

	// publish & approve document: 1 or 0
	var $published 			= null;
	var $approved 			= null;

	//thumbnail
	var $docthumbnail		= null;

	// license ID and display
	var $doclicense_id 		= null;
	var $doclicense_display 	= null; // 0 or 1

	var $docowner 			= null;
	var $docmaintainedby 		= null;
	var $doclastupdateby 	= null;
	var $docsubmittedby 		= null;

	var $docurl 				= null;
	var $access 			= 0;
	var $attribs 			= null;

	// drop the extensios from the filenames in the document titles
	var $stripextension		= null;

	// only show orphan files in the filelist
	var $orphansonly		= null;

	// remove underscores from title and use Title Case
	var $nicetitle			= null;

	// set document times according to file modification time
	var $usefiletime		= null;

	var $password		   = null;
	var $catid			  = null;

	public function __construct( $db ) {
		parent::__construct('#__populate_conf', 'id', $db);
	}

	public function getInstance()
	{
		static $instance;

		if(!isset($instance))
		{
	   		$instance = new TablePopulateConf(JFactory::getDBO());
			$instance->load(1);
		}
		return $instance;
	}

	public function check() {return true;}

	public function checkin(){return true;}
	public function checkout(){return true;}

}
