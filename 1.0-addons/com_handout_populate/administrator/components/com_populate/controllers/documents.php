<?php
/**
 * @category	HandoutPopulate
 * @package		HandoutPopulate
 * @copyright	Copyright (C) 2011 Kontent Design. All rights reserved.
 * @copyright	Copyright (C) 2003 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link	 	http://www.sharehouts.com
 */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT.DS.'helpers'.DS.'formatter.php';

jimport( 'joomla.application.component.controller' );
class PopulateControllerDocuments extends JController
{

	public function display()
	{
		// check path
		if ( TablePopulateParams::getInstance()->handoutpath == '' )
		{
			throw new Exception("Please go to the Handout configuration first, check all settings and make sure to save." );
			return;
		}
		parent::display();
	}


	public function assign()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$my	   	= JFactory::getUser();
		$db 		= JFactory::getDBO();

		$apConfig   = TablePopulateConf::getInstance();
		$apParams   = TablePopulateParams::getInstance();


		$files	= JRequest::getVar('files', false, 'post');
		$catid	= JRequest::getInt('catid', false, 'post');

		// if not set in config, get defaults
		$docowner		= $apConfig->docowner		? $apConfig->docowner		: $apParams->docowner;
		$docmaintainedby	= $apConfig->docmaintainedby	? $apConfig->docmaintainedby	: $apParams->docmaintainedby;

		// count
		$success = 0; $failed = 0;
		foreach ($files as $file)
		{
			$title = $apConfig->stripextension ? PopulateFormatter::stripExtension( $file ) : $file;
			$title = $apConfig->nicetitle ? PopulateFormatter::getNiceTitle( $title ) : $title;

			// filetimes
			$docdate_published = $apParams->docdate_published;
			$doclastupdateon = $apParams->doclastupdateon;
			if ($apConfig->usefiletime)
			{
				$st = @stat($apParams->handoutpath.'/'.escapeshellarg($file));
				if ($st)
				{
					$docdate_published = strftime('%Y-%m-%d %T', $st[10]);
					$doclastupdateon = strftime('%Y-%m-%d %T', $st[9]);
					// $docdate_published = $doclastupdateon;
				}
			}

			$db->setQuery(
					"INSERT INTO `#__handout` "
					."\n SET "
					." \n `docfilename`			= ".$db->quote($file)
					.",\n `docname`				= ".$db->quote($title)
					.",\n `catid`				= ".(int) $catid
					.",\n `docdescription`		= ".$db->quote($apConfig->docdescription)
					.",\n `docdate_published`	= ".$db->quote($docdate_published)
					.",\n `docowner`				= ".(int) $docowner
					.",\n `published`			= ".(int) $apConfig->published
					.",\n `docurl`				= ".$db->quote($apConfig->docurl)
					.",\n `doccounter`			= ".(int) $apParams->doccounter
					.",\n `checked_out`			= ".(int) $apParams->checked_out
					.",\n `checked_out_time`	= ".$db->quote($apParams->checked_out_time)
					.",\n `approved`			= ".(int) $apConfig->approved
					.",\n `docthumbnail`			= ".$db->quote($apConfig->docthumbnail)
					.",\n `doclastupdateon`	  = ".$db->quote($doclastupdateon)
					.",\n `doclastupdateby`		= ".(int) $my->id
					.",\n `docsubmittedby`		= ".(int) $my->id
					.",\n `docmaintainedby`		= ".(int) $docmaintainedby
					.",\n `doclicense_id`		= ".(int) $apConfig->doclicense_id
					.",\n `doclicense_display`	= ".(int) $apConfig->doclicense_display
					.",\n `access`				= ".(int) $apConfig->access
					.",\n `attribs`				= ".$db->quote($apConfig->attribs)
					);
					if (!$db->query())  {
				JError::raiseNotice('Error: '.$db->getErrorMsg());
				$failed++;
			} else {
				$success++;
			}
		}
		$this->setRedirect('index.php?option=com_populate&view=documents', "$success documents added, $failed failed");

	}

}
