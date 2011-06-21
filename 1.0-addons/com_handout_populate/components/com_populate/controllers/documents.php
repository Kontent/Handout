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

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_populate'.DS.'models'.DS.'files.php';
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_populate'.DS.'helpers'.DS.'formatter.php';
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_populate'.DS.'tables'.DS.'config.php';
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_populate'.DS.'tables'.DS.'params.php';


jimport( 'joomla.application.component.controller' );
class PopulateControllerDocuments extends JController
{	
	public function assign()
	{
		$_HANDOUT = PopulateDocman::get();
		$my 		= JFactory::getUser();
        $db 	= JFactory::getDBO();

        $apConfig   = TablePopulateConf::getInstance();
        $apParams   = TablePopulateParams::getInstance();

        $files = array();
        $success = 0; $failed = 0;
        		
		$filesmodel = new PopulateModelFiles;

        $FilesInDatabase    = $filesmodel->getData(); //files that already have a doc entry
        if($FilesInDatabase===false) {
        	die('Error reading file list from database');
        }
        
        // if not set in config, get defaults
        $docowner        = $apConfig->docowner        ? $apConfig->docowner        : $apParams->docowner;
        $docmaintainedby  = $apConfig->docmaintainedby  ? $apConfig->docmaintainedby  : $apParams->docmaintainedby;

        if ( $apParams->handoutpath == '' ) {
            die("Please go to the Handout configuration first, check all settings and make sure to <b>save</b>, even if you change nothing." );
            return;
        }
        
        if ( !$handle = opendir($apParams->handoutpath) ) {
            die( "Problem opening handouts directory " . $apParams->handoutpath
                    .". Make sure you have Handout working correctly before using this component." );
            return;
        }


        while (false !== ($file = readdir($handle))) 
        {
            if (    !in_array($file, explode( "|", $apConfig->skipfiles ) )
                    && !in_array($file, $FilesInDatabase)
                    && !is_dir($file) ) {
                $files[] = $file;
            }
        }

        foreach ($files as $file) 
        {
            $title = $apConfig->stripextension ? PopulateFormatter::stripExtension( $file ) : $file;
            $title = $apConfig->nicetitle ? PopulateFormatter::getNiceTitle( $title ) : $title;

            // filetimes
            $docdate_published = $apParams->docdate_published;
            $doclastupdateon = $apParams->doclastupdateon;
            if ($apConfig->usefiletime) {
                $st = @stat($apParams->handoutpath.'/'.escapeshellarg($file));
                if ($st) {
                    $docdate_published = strftime('%Y-%m-%d %T', $st[10]);
                    $doclastupdateon = strftime('%Y-%m-%d %T', $st[9]);
                }
            }

             $db->setQuery(
                    "INSERT INTO `#__handout` "
                    ."\n SET "
                    ." \n `docfilename`			= ".$db->quote($file)
                    .",\n `docname`				= ".$db->quote($title)
                    .",\n `catid`				= ".(int) $apConfig->catid
                    .",\n `docdescription`		= ".$db->quote($apConfig->docdescription)
                    .",\n `docdate_published`    = ".$db->quote($docdate_published)
                    .",\n `docowner`				= ".(int) $docowner
                    .",\n `published`			= ".(int) $apConfig->published
                    .",\n `docurl`				= ".$db->quote($apConfig->docurl)
                    .",\n `doccounter`			= ".(int) $apParams->doccounter
                    .",\n `checked_out`			= ".(int) $apParams->checked_out
                    .",\n `checked_out_time`	= ".$db->quote($apParams->checked_out_time)
                    .",\n `approved`			= ".(int) $apConfig->approved
                    .",\n `docthumbnail`			= ".$db->quote($apConfig->docthumbnail)
                    .",\n `doclastupdateon`      = ".$db->quote($doclastupdateon)
                    .",\n `doclastupdateby`		= ".(int) $my->id
                    .",\n `docsubmittedby`		= ".(int) $my->id
                    .",\n `docmaintainedby`		= ".(int) $docmaintainedby
                    .",\n `doclicense_id`		= ".(int) $apConfig->doclicense_id
                    .",\n `doclicense_display`	= ".(int) $apConfig->doclicense_display
                    .",\n `access`				= ".(int) $apConfig->access
                    .",\n `attribs`				= ".$db->quote($apConfig->attribs)
                    );        
            if (!$db->query()) {
                $failed++;
            } else {
                $success++;
            }
        }

        echo 'Populate for Handout: ';
    	echo $success . ' files added successfully, ' . $failed . ' failed.';
    }
		
}
