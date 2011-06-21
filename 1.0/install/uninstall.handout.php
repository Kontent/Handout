<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: uninstall.handout.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__) . DS . 'install.handout.helper.php');
require_once (dirname(__FILE__) . DS . '..' . DS . 'helpers' . DS . 'factory.php');

// Load the component language file
$language = JFactory::getLanguage();
$language->load('com_redirect');

// Uninstall the modules.
$modules = PackageInstallerHelper::uninstallModules($this);
if ($modules === false) {
	return false;
}

// Uninstall the plugins.
$plugins = PackageInstallerHelper::uninstallPlugins($this);
if ($plugins === false) {
	return false;
}

// Display the results.
PackageInstallerHelper::displayUninstalled(
	$modules,
	$plugins,
	'',
	JText::_('Handout')
);


function com_uninstall ()
{
           
    // delete the handouts folder if it's empty

    if (HandoutInstallHelper::cntFiles() == 0) {
        HandoutInstallHelper::removeDmdocuments();
    }

    // if there's no more data, we remove the tables

    if (HandoutInstallHelper::cntDbRecords() == 0) {
        HandoutInstallHelper::removeTables();
    }
    
}