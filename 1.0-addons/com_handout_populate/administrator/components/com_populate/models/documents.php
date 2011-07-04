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

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT.DS.'models'.DS.'files.php';

class PopulateModelDocuments extends JModel
{

	public function getData()
	{
		$database 	= JFactory::getDBO();

		$filesmodel = new PopulateModelFiles;

		$FilesInDatabase = $this->getState('orphansonly', 1) ? $filesmodel->getData() : array(); //files that already have a doc entry

		$handoutpath 	= $this->getState('handoutpath');
		$skipfiles	= explode( "|", $this->getState('skipfiles'));

	    if(!$handoutpath || !$handle = opendir($handoutpath))
	    {
            throw new Exception("Problem opening handouts directory <b>" . $handoutpath
                    ."</b>. Make sure you have Handout working correctly before using this component." );
        }

		$files = array();
        while (false !== ($file = readdir($handle)))
        {
            if (!in_array($file, $skipfiles )
                    && !in_array($file, $FilesInDatabase)
                    && !is_dir($file) ) {
                $files[] = $file;
            }
        }

        natcasesort($files);
		return $files;
	}

}
