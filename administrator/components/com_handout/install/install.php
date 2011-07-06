<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: install.handout.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined ( '_JEXEC' ) or die ( 'Restricted access' );

require_once (dirname ( __FILE__ ) . DS . 'install.handout.helper.php');


$language = JFactory::getLanguage();
$language->load('com_handout', JPATH_ADMINISTRATOR.'/components/com_handout');


// Install the modules.
$modules = PackageInstallerHelper::installModules($this);
if ($modules === false) {
	return false;
}

// Install the plugins.
$plugins = PackageInstallerHelper::installPlugins($this);
if ($plugins === false) {
	return false;
}

	// Display the results.
	PackageInstallerHelper::displayInstalled(
		$modules,
		$plugins,
		'',
		JText::_('Handout')
	);

function com_install() {
	$absolute_path = JPATH_ROOT;
	$return = true;

	// Logo
	HandoutInstallHelper::showLogo ();

	if (! HandoutInstallHelper::checkWritable ()) {
		$link = 'index.php?option=com_installer&type=components&task=manage';
		// this should get the attention of people who prefer to ignore error messages!
?>
		<p style="font-size: 200%">
			Installation failed!
			<a href="<?php echo $link?>">Click here to uninstall Handout</a>. Make the folders listed above writable and try again.
		</p>
<?php
		$return = false;
	}

	// Upgrade tables
	HandoutInstallHelper::upgradeTables ();

	// Files
	HandoutInstallHelper::fileOperations ();

	// index.html files

	$paths = array ('components' . DS . 'com_handout', 'administrator' . DS . 'components' . DS . 'com_handout', 'handouts' );
	foreach ( $paths as $path ) {
		$path = $absolute_path . DS . $path;
		HandoutInstallHelper::createIndex ( $path );
	}

	// Link to add sample data
	HandoutInstallHelper::cpanel ();

	return $return;
}

