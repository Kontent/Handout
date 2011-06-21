<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: thumbs.php
 * @package 	Handout Thumbnails
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
defined('_JEXEC') or die('Restricted access');

// requires
require_once(dirname(__FILE__).DS.'thumbs'.DS.'defines.php');

jimport( 'joomla.plugin.plugin' );
class plgHandoutThumbs extends JPlugin
{
	public function onFetchDocument($params) 
	{
        global $_HANDOUT;
    	if(!is_object($_HANDOUT)){
			$handoutBase = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_handout' . DS;
			require_once ($handoutBase . 'helpers' . DS . 'factory.php');
    		$_HANDOUT = &HandoutFactory::getHandout();
		}
		
	    // load plugin params info
	 	$plugin =& JPluginHelper::getPlugin('handout', 'thumbs');
	 	$pluginParams = new JParameter( $plugin->params );
	
	    // DOCman config
	    $handoutpath = $_HANDOUT->getCfg('handoutpath', JPATH_ROOT.DS.'handouts');

	    // Parameters
	    $id     = $params['id'];
	    $type   = $params['type'];

	    // get the document instance
	    $doc = & HANDOUT_Document::getInstance($id);	

	    // check for existing thumbnail
	    if($doc->objDBTable->docthumbnail) { return; }

	    // docfilename exists?
	    if(!file_exists($handoutpath)) { return; }
	    if(!file_exists($handoutpath.DS.$doc->objDBTable->docfilename)) { return; }

	    // check extension
	    $extensions = $pluginParams->get( 'extensions', _AT_FILETYPE_LIST );
	    if(!in_array(strtolower($doc->objFormatData->filetype), explode(',', $extensions))) { return; }

	    // Target path writable?
	    if(!is_writable(_AT_PATH_IMAGES)) { return; }

	    // build target filename
	    $output_format  = $pluginParams->get('output_format', 'png');
	    $target = _AT_PATH_IMAGES.DS.'~thumbs.'.$doc->objDBTable->docfilename.'.'.date('U').'.'.$output_format;

	    // phpThumb
	    require_once(_AT_PATH_LIBRARIES.DS.'phpthumb'.DS.'phpthumb.class.php');
	    $phpThumb  = new phpThumb();

	    // parameters
	    $phpThumb->setSourceFilename( $handoutpath.DS.$doc->objDBTable->docfilename );
	    $phpThumb->setParameter('w',    $pluginParams->get('width', 64));
	    $phpThumb->setParameter('h',    $pluginParams->get('height', 64));
	    $phpThumb->setParameter('far',  'C');
	    $phpThumb->setParameter('f',    $pluginParams->get('output_format', 'png'));
	    $phpThumb->setParameter('q',    $pluginParams->get('jpeg_quality', 75));
	    $phpThumb->setParameter('bg',   $pluginParams->get('background_color', 'FFFFFF'));
	    if ($pluginParams->get('grayscale', 0)) {
	        $phpThumb->setParameter('fltr', 'gray' );
	    }

	    // generate
	    if ( !$phpThumb->GenerateThumbnail() ) { return; }

	    // render
	    if (!$phpThumb->RenderToFile($target)) { return; }
	    unset($phpThumb);

	    // assign thumbnail
	    $doc->objDBTable->docthumbnail       = basename($target);
	    $doc->objFormatData->docthumbnail    = $doc->objDBTable->docthumbnail;
	    $doc->objFormatPath->thumb 			= HANDOUT_Utils::pathThumb($doc->objDBTable->docthumbnail, 1);
	
	    // store
	    $doc->objDBTable->store();
	    return;
	}
}