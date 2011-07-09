<?php

/**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */

defined('_JEXEC') or die('Restricted access');

if (! defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);

require_once dirname(__FILE__) . DS . '..' . DS. 'handout.class.php';

$_HANDOUT = new HandoutMainFrame();
$_HANDOUT_USER = $_HANDOUT->getUser();

define('COM_HANDOUT_INSTALLER_ICONPATH', JURI::root() . 'administrator/components/com_handout/images/');

/**

 * Helper functions for the installer

 * @static

 */
class HandoutInstallHelper
{
    function checkWritable ()
    {
        $absolute_path = JPATH_ROOT;
        $paths = array(DS , DS . 'administrator' . DS . 'modules' . DS , DS . 'plugins' . DS);
        clearstatcache();
        $msgs = array();
        foreach ($paths as $path) {
            if (! is_writable($absolute_path . $path)) {
                $msgs[] = '<font color="red">Unwriteable: &lt;joomla root&gt;' . $path . '</font><br />';
            }
        }
        if (count($msgs)) {
            echo '<br /><p style="font-size:200%">';
            echo implode("\n", $msgs);
            echo '</p>';
            return false;
        }
        return true;
    }

    function getDefaultFiles ()
    {
        return array('.htaccess' , 'index.html');
    }

    function getComponentId ()
    {
        static $id;
        if (! $id) {
            $database = &JFactory::getDBO();
            $database->setQuery("SELECT id FROM #__components WHERE name= 'Handout'");
            $id = $database->loadResult();
        }
        return $id;
    }

    function fileOperations ()
    {

        $root = JPATH_ROOT;
        $site = $root . DS . 'components' . DS . 'com_handout';
        $admin = $root . DS . 'administrator' . DS . 'components' . DS . 'com_handout';
        $handoutdoc = $root . DS . 'handouts';

        @mkdir($handoutdoc, 0755);
        @rename($admin . DS . 'htaccess.txt', $handoutdoc . DS . '.htaccess');
        @copy($site . DS . 'index.html', $handoutdoc . DS . 'index.html');

        @chmod($site, 0755);
        @chmod($admin . DS . 'classes' . DS . 'HANDOUT_download.class.php', 0755);
        @chmod($admin . DS . 'classes' . DS . 'HANDOUT_utils.php', 0755);
    }

    function showLogo ()
    {
        ?>
        <style type="text/css">
			h1.hinstall-title {
				margin-left: 0;
				font-family: Helvetica, sans-serif;
				font-size: 20px;
			}
			#hinstall-container p {
				font-family: Helvetica, sans-serif;
				font-size: 13px;
			}
			#hinstall-container {
				margin-left: 30px;
				width: 700px;
			}
			#hinstall-container a {
				text-decoration: none;
			}
			#hinstall-container .clr {
				clear:both;
			}
			#hinstall-container .hicon {
				margin: 0 40px 0 10px;
				float:left;
			}
			#hinstall-container img {
				margin: 0 auto;
				padding: 10px 0;
				border: 0 none;
			}
			#hinstall-container div.hicon a {
				border: 1px solid #F0F0F0;
				color: #666666;
				display: block;
				float: left;
				height: 97px;
				text-decoration: none;
				vertical-align: middle;
				width: 108px;
			}
			#hinstall-container div.hicon {
				text-align: center;
			}
			#hinstall-container div.hicon {
				float: left;
				margin-bottom: 5px;
				margin-right: 5px;
				text-align: center;
			}
			#hinstall-container div.hicon {
				background: none repeat scroll 0 0 #FFFFFF;
			}
			#hinstall-container span {
				display: block;
				text-align: center;
			}
			#hinstall-container div.hicon a {
				color: #666666;
				text-decoration: none;
				font-family: Helvetica, sans-serif;
				font-size: 11px;
			}
			#hinstall-container div.hicon a:hover {
				background: none repeat scroll 0 0 #F9F9F9;
				border-color: #EEEEEE #CCCCCC #CCCCCC #EEEEEE;
				border-left: 1px solid #EEEEEE;
				border-style: solid;
				border-width: 1px;
				color: #0B55C4;
			}
			.hlogo {
				margin-left: 10px;
			}
			.hlogo-top {
				margin-left: 30px;
				margin-top: 30px;
				margin-bottom: 30px;
			}
			#hinstall-container .cta {
				font-size: 11px;
			}
			</style>
			<div class="hlogo-top">
				<a href="index.php?option=com_handout"><img border="0" alt="Handout" src="<?php echo JURI::root()?>/administrator/components/com_handout/images/logo_header.png" /></a>
			</div>
		<?php
			}

			function cpanel ()
			{
				?>

				<div id="hinstall-container">
					<h1 class="hinstall-title">Handout Successfully Installed.</h1>
					<p>Thank you for installing Handout, the ultimate download manager for Joomla. You can now get started by uploading a file, creating categories, changing the configuration or migrating documents from another extension.</p>
					<div class="clr">&nbsp;</div>
					<div class="hicon">
						<a href="index.php?option=com_handout">
							<img border="0" align="top" alt="" src="<?php echo JURI::root()?>/administrator/components/com_handout/images/icon-48-home.png" />
							<span>Handout Control Panel</span>
						</a>
					</div>
					<div class="hicon">
						<a href="index.php?option=com_handout&section=config">
							<img border="0" align="top" alt="" src="<?php echo JURI::root()?>/administrator/components/com_handout/images/icon-48-config.png" />
							<span>Edit Configuration</span>
						</a>
					</div>
					<div class="hicon">
						<a href="index.php?option=com_handout&section=config">
							<img border="0" align="top" alt="" src="<?php echo JURI::root()?>/administrator/components/com_handout/images/icon-48-migration.png" />
							<span>Migrate Documents</span>
						</a>
					</div>
					<div class="hicon">
						<a href="index.php?option=com_handout&section=files&task=upload">
							<img border="0" align="top" alt="" src="<?php echo JURI::root()?>/administrator/components/com_handout/images/icon-48-upload.png" />
							<span>Upload a File</span>
						</a>
					</div>
					<div class="hicon">
						<a href="index.php?option=com_handout&section=categories">
							<img border="0" align="top" alt="" src="<?php echo JURI::root()?>/administrator/components/com_handout/images/icon-48-category.png" />
							<span>Create Categories</span>
						</a>
					</div>
					<div class="clr">&nbsp;</div>
					<div class="hlogo">
						<a href="http://extensions.kontentdesign.com" target="_blank"><img border="0" alt="Kontent Extensions" src="<?php echo JURI::root()?>/administrator/components/com_handout/images/kontent-extensions-logo.png" /></a>
						<p class="cta"><a href="http://extensions.kontentdesign.com" target="_blank">Click here to view more products from Kontent Extensions &raquo;</a></p>
					</div>
				</div>
		<?php
    }

    /**

     * Count items in tables

     */
    function cntDbRecords ()
    {
        $database = &JFactory::getDBO();
        $cnt = array();
        $tables = HandoutInstallHelper::getTablesList();

        foreach ($tables as $table) {
            $database->setQuery("SELECT COUNT(*) FROM `$table`");
            $cnt[] = (int) $database->loadResult();
        }

        // count categories

        $database->setQuery("SELECT COUNT(*) FROM `#__categories` WHERE `section` = 'com_handout'");
        $cnt[] = (int) $database->loadResult();

        return array_sum($cnt);
    }

    function removeTables ()
    {
        $database = &JFactory::getDBO();
        $tables = HandoutInstallHelper::getTablesList();

        foreach ($tables as $table) {
            $database->setQuery("DROP TABLE IF EXISTS `$table`");
            $database->query();
        }
    }

    function getTablesList ()
    {
        return array('#__handout' , '#__handout_groups' , '#__handout_history' , '#__handout_licenses' , '#__handout_log');
    }

    /**

     * Count the number of files in /handouts

     */
    function cntFiles ()
    {
        global $_HANDOUT;
        if (! is_object($_HANDOUT)) {
            $_HANDOUT = new HandoutMainFrame();
        }
        if (! is_object($_HANDOUT)) {
            $_HANDOUT = new HandoutMainFrame();
        }
        $files = HandoutInstallHelper::getDefaultFiles();
        $dir = JFolder::files($_HANDOUT->getCfg('handoutpath'));
        return count(array_diff($dir, $files));
    }

    function removeHandoutDocuments ()
    {
        global $_HANDOUT;
        if (! is_object($_HANDOUT)) {
            $_HANDOUT = new HandoutMainFrame();
        }

        $handoutpath = $_HANDOUT->getCfg('handoutpath');

        $files = HandoutInstallHelper::getDefaultFiles();

        foreach ($files as $file) {
            @unlink($handoutpath . DS . $file);
        }
        @rmdir($handoutpath);
    }

    /**

     * Create index.html files

     */
    function createIndex ($path)
    {
        // create index.html in the path

        HandoutInstallHelper::_createIndexFile($path);

        if (! file_exists($path)) {
            return false;
        }
        // create index.html in subdirs

        $handle = opendir($path);
        while ($file = readdir($handle)) {
            if ($file != '.' and $file != '..') {
                $dir = $path . DS . $file;
                if (is_dir($dir)) {
                    HandoutInstallHelper::createIndex($dir);
                }
            }
        }
    }

    function _createIndexFile ($dir)
    {
        @$handle = fopen($dir . DS . 'index.html', 'w');
        @fwrite($handle, 'Restricted access');
    }



    /**
     * Upgrade tables
     */
    function upgradeTables ()
    {
        $database = &JFactory::getDBO();
        $queries = array();

        $database->setQuery("SHOW INDEX FROM #__handout");
        $database->query();
        $num_keys = $database->getNumRows();
        switch ($num_keys) {
            case 1: // there's only a primary index, add some more

                $queries[] = "ALTER TABLE `#__handout` ADD INDEX `pub_own_cat_name`  (`published`, `docowner`, `catid`, `docname`(64))";
                $queries[] = "ALTER TABLE `#__handout` ADD INDEX `pub_own_cat_date`  (`published`, `docowner`, `catid`, `docdate_published`)";
                $queries[] = "ALTER TABLE `#__handout` ADD INDEX `own_pub_cat_count` (`docowner`, `published`, `catid`, `doccounter`)";
            // pass through (more can be added later on)

            default:
                break;
        }

        foreach ($queries as $query) {
            $database->setQuery($query);
            if (! $database->query()) {
                echo 'Error upgrading tables';
            }
        }
    }
}

abstract class PackageInstallerHelper
{
	/**
	 * Display the results of the package install.
	 *
	 * @param	array	$modules	An array of the modules that were installed.
	 * @param	array	$plugins	An array of the plugins that were installed.
	 * @param	string	$title		The page title.
	 * @param	string	$name		The name of the component.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public static function displayInstalled(&$modules, &$plugins, $title, $name)
	{
?>
<?php echo $title;?>

<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('J_INSTALL_EXTENSION'); ?></th>
			<th width="30%"><?php echo JText::_('J_INSTALL_STATUS'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo JText::sprintf('J_INSTALL_COMPONENT', $name); ?></td>
			<td><strong><?php echo JText::_('J_INSTALL_INSTALLED'); ?></strong></td>
		</tr>
<?php if ($modules) : ?>
		<tr>
			<th><?php echo JText::_('J_INSTALL_MODULE'); ?></th>
			<th><?php echo JText::_('J_INSTALL_CLIENT'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($modules as $i => $module) : ?>
		<tr class="row<?php echo ($i % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong><?php echo JText::_('J_INSTALL_INSTALLED'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;
if ($plugins) : ?>
		<tr>
			<th><?php echo JText::_('J_INSTALL_PLUGIN'); ?></th>
			<th><?php echo JText::_('J_INSTALL_GROUP'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($plugins as $i => $plugin) : ?>
		<tr class="row<?php echo ($i % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong><?php echo JText::_('J_INSTALL_INSTALLED'); ?></strong></td>
		</tr>
	<?php endforeach;
endif; ?>
	</tbody>
</table>
<?php
	}

	/**
	 * Display the results of the package uninstall.
	 *
	 * @param	array	$modules	An array of the modules that were installed.
	 * @param	array	$plugins	An array of the plugins that were installed.
	 * @param	string	$title		The page title.
	 * @param	string	$name		The name of the component.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public static function displayUninstalled(&$modules, &$plugins, $title, $name)
	{
?>
<h2><?php echo $title;?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('J_INSTALL_EXTENSION'); ?></th>
			<th width="30%"><?php echo JText::_('J_INSTALL_STATUS'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo JText::sprintf('J_INSTALL_COMPONENT', $name); ?></td>
			<td><strong><?php echo JText::_('J_INSTALL_REMOVED'); ?></strong></td>
		</tr>
<?php if ($modules) : ?>
		<tr>
			<th><?php echo JText::_('J_INSTALL_MODULE'); ?></th>
			<th><?php echo JText::_('J_INSTALL_CLIENT'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($modules as $i => $module) : ?>
		<tr class="row<?php echo ($i % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong><?php echo JText::_('J_INSTALL_REMOVED'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;
if ($plugins) : ?>
		<tr>
			<th><?php echo JText::_('J_INSTALL_PLUGIN'); ?></th>
			<th><?php echo JText::_('J_INSTALL_GROUP'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($plugins as $i => $plugin) : ?>
		<tr class="row<?php echo ($i % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong><?php echo JText::_('J_INSTALL_REMOVED'); ?></strong></td>
		</tr>
	<?php endforeach;
endif; ?>
	</tbody>
</table>
<?php
	}

	/**
	 * Fixes a bug in the components table for backend only extensions.
	 *
	 * @param	string	$option	The name of the component folder.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public static function fixLink($option)
	{
		// Insert a new installation record in the version log if no rows are present.
		$db	= JFactory::getDBO();

		// Correct bug in components table for backend only extenions

		$db->setQuery(
			'UPDATE `#__components` SET `link` = '.$db->quote('').' WHERE `option` = '.$db->quote($option)
		);

		if (!$db->query()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
	}

	/**
	 * Install packaged modules.
	 *
	 * @param	object	$installer	The parent installer object.
	 *
	 * @return	array	An array of the installed modules.
	 * @since	1.0
	 */
	public static function installModules(&$installer)
	{
		$result		= array();
		$modules	= &$installer->manifest->getElementByPath('modules');

		if (is_a($modules, 'JSimpleXMLElement') && count($modules->children())) {
			foreach ($modules->children() as $module)
			{
				$mname		= $module->attributes('module');
				$mclient	= JApplicationHelper::getClientInfo($module->attributes('client'), true);

				// Set the installation path
				if (!empty ($mname)) {
					$installer->parent->setPath('extension_root', $mclient->path.'/modules/'.$mname);
				}
				else {
					$installer->parent->abort(JText::_('J_Install_Module').' '.JText::_('J_INSTALL_INSTALL').': '.JText::_('J_INSTALL_MODULE_FILE_MISSING'));
					return false;
				}

				// If the module directory already exists, then we will assume that the
				// module is already installed or another module is using that directory.
				if (file_exists($installer->parent->getPath('extension_root'))&&!$installer->parent->getOverwrite()) {
					$installer->parent->abort(JText::_('J_Install_Module').' '.JText::_('J_INSTALL_INSTALL').': '.JText::sprintf('J_INSTALL_MODULE_PATH_CONFLICT', $installer->parent->getPath('extension_root')));
					return false;
				}

				// If the module directory does not exist, lets create it
				$created = false;
				if (!file_exists($installer->parent->getPath('extension_root'))) {
					if (!$created = JFolder::create($installer->parent->getPath('extension_root'))) {
						$installer->parent->abort(JText::_('J_Install_Module').' '.JText::_('J_INSTALL_INSTALL').': '.JText::sprintf('J_INSTALL_MODULE_PATH_CREATE_FAILURE', $installer->parent->getPath('extension_root')));
						return false;
					}
				}

				// Since we created the module directory and will want to remove it if
				// we have to roll back the installation, lets add it to the
				// installation step stack
				if ($created) {
					$installer->parent->pushStep(array ('type' => 'folder', 'path' => $installer->parent->getPath('extension_root')));
				}

				// Copy all necessary files
				$element = &$module->getElementByPath('files');
				if ($installer->parent->parseFiles($element, -1) === false) {
					// Install failed, roll back changes
					$installer->parent->abort();
					return false;
				}

				// Copy language files
				$element = &$module->getElementByPath('languages');
				if ($installer->parent->parseLanguages($element, $mclient->id) === false) {
					// Install failed, roll back changes
					$installer->parent->abort();
					return false;
				}

				// Copy media files
				$element = &$module->getElementByPath('media');
				if ($installer->parent->parseMedia($element, $mclient->id) === false) {
					// Install failed, roll back changes
					$installer->parent->abort();
					return false;
				}

				$mtitle		= $module->attributes('title');
				$mposition	= $module->attributes('position');
				$mShowTitle	= $module->attributes('showtitle');
				$mPublished	= $module->attributes('published');

				if ($mtitle && $mposition) {
					// Check if module is already installed.
					$db = JFactory::getDBO();
					$db->setQuery(
						'SELECT id' .
						' FROM #__modules' .
						' WHERE client_id = '.(int) $mclient->id.
						'  AND module = '.$db->quote($mname)
					);
					$installed = $db->loadResult();
					$error	= $db->getErrorMsg();

					if ($error) {
						$installer->parent->abort(JText::_('J_INSTALL_MODULE').' '.JText::_('J_INSTALL_INSTALL').': '.$error);
						return false;
					}

					if (!$installed) {
						$row = JTable::getInstance('module');
						$row->title		= $mtitle;
						$row->ordering	= $row->getNextOrder("position='".$mposition."'");
						$row->position	= $mposition;
						$row->showtitle	= (boolean) $mShowTitle;
						$row->iscore	= 0;
						$row->access	= ($mclient->id) == 1 ? 2 : 0;
						$row->client_id	= $mclient->id;
						$row->module	= $mname;
						$row->published	= (boolean) $mPublished;
						$row->params	= '';

						if (!$row->store()) {
							// Install failed, roll back changes
							$installer->parent->abort(JText::_('J_INSTALL_MODULE').' '.JText::_('J_INSTALL_INSTALL').': '.$row->getError());
							return false;
						}
					}
				}

				$result[] = array('name'=>$mtitle,'client'=>$mclient->name);
			}
		}

		return $result;
	}

	/**
	 * Install packaged modules.
	 *
	 * @param	object	$installer	The parent installer object.
	 *
	 * @return	array	An array of the installed modules.
	 * @since	1.0
	 */
	public static function installPlugins(&$installer)
	{
		$result		= array();

		$plugins = &$installer->manifest->getElementByPath('plugins');
		if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {

			foreach ($plugins->children() as $plugin)
			{
				$pelement	= $plugin->attributes('plugin');
				$pgroup		= $plugin->attributes('group');
				$pname		= $plugin->attributes('name');

				// Set the installation path
				if (!empty($pelement) && !empty($pgroup)) {
					$installer->parent->setPath('extension_root', JPATH_ROOT.'/plugins/'.$pgroup);
				} else {
					$installer->parent->abort(JText::_('J_INSTALL_PLUGIN').' '.JText::_('J_INSTALL_INSTALL').': '.JText::_('J_INSTALL_PLUGIN_FILE_MISSING'));
					return false;
				}

				/**
				 * ---------------------------------------------------------------------------------------------
				 * Filesystem Processing Section
				 * ---------------------------------------------------------------------------------------------
				 */

				// If the plugin directory does not exist, lets create it
				// TO DO: Add index.html also to the folder
				$created = false;
				if (!file_exists($installer->parent->getPath('extension_root'))) {
					if (!$created = JFolder::create($installer->parent->getPath('extension_root'))) {
						$installer->parent->abort(JText::_('J_INSTALL_PLUGIN').' '.JText::_('J_INSTALL_INSTALL').': '.JText::sprintf('J_INSTALL_PLUGIN_PATH_CREATE_FAILURE', $installer->parent->getPath('extension_root')));
						return false;
					}
					@copy(JPATH_ROOT . DS . 'plugins' . DS . 'content' . DS . 'index.html', $installer->parent->getPath('extension_root') . DS . 'index.html');
				}

				// If we created the plugin directory and will want to remove it if we
				// have to roll back the installation, lets add it to the installation
				// step stack
				if ($created) {
					$installer->parent->pushStep(array ('type' => 'folder', 'path' => $installer->parent->getPath('extension_root')));
				}

				// Copy all necessary files
				$element = &$plugin->getElementByPath('files');
				if ($installer->parent->parseFiles($element, -1) === false) {
					// Install failed, roll back changes
					$installer->parent->abort();
					return false;
				}

				// Copy all necessary files
				$element = &$plugin->getElementByPath('languages');
				if ($installer->parent->parseLanguages($element, 1) === false) {
					// Install failed, roll back changes
					$installer->parent->abort();
					return false;
				}

				// Copy media files
				$element = &$plugin->getElementByPath('media');
				if ($installer->parent->parseMedia($element, 1) === false) {
					// Install failed, roll back changes
					$installer->parent->abort();
					return false;
				}

				/**
				 * ---------------------------------------------------------------------------------------------
				 * Database Processing Section
				 * ---------------------------------------------------------------------------------------------
				 */
				$db = &JFactory::getDBO();

				// Check to see if a plugin by the same name is already installed
				$query = 'SELECT `id`' .
						' FROM `#__plugins`' .
						' WHERE folder = '.$db->Quote($pgroup) .
						' AND element = '.$db->Quote($pelement);
				$db->setQuery($query);
				if (!$db->Query()) {
					// Install failed, roll back changes
					$installer->parent->abort(JText::_('J_INSTALL_PLUGIN').' '.JText::_('J_INSTALL_INSTALL').': '.$db->stderr(true));
					return false;
				}
				$id = $db->loadResult();

				// Was there a plugin already installed with the same name?
				if ($id) {

					if (!$installer->parent->getOverwrite()) {
						// Install failed, roll back changes
						$installer->parent->abort(JText::_('J_INSTALL_PLUGIN').' '.JText::_('J_INSTALL_INSTALL').': '.JText::sprintf('J_INSTALL_PLUGIN_ALREADY_EXISTS', $pelement));
						return false;
					}

				} else {
					$row =& JTable::getInstance('plugin');
					//$row->name = JText::_(ucfirst($pgroup)).' - '.JText::_(ucfirst($pname));
					$row->name = JText::_(ucfirst($pname));
					$row->ordering = 0;
					$row->folder = $pgroup;
					$row->iscore = 0;
					$row->access = 0;
					$row->client_id = 0;
					$row->element = $pelement;
					$row->published = 1;
					$row->params = '';

					if (!$row->store()) {
						// Install failed, roll back changes
						$installer->parent->abort(JText::_('J_INSTALL_PLUGIN').' '.JText::_('J_INSTALL_INSTALL').': '.$db->stderr(true));
						return false;
					}
				}

				$result[] = array('name'=>$pname,'group'=>$pgroup);
			}
		}

		return $result;
	}

	/**
	 * Check if a component exists.
	 *
	 * @param	string	$option	The name of the component folder.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public static function componentExists($option)
	{
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT id' .
			' FROM #__components' .
			' WHERE `option` = '.$db->quote($option) .
			'  AND parent = 0'
		);
		$result = $db->loadResult();
		if ($error = $db->getErrorMsg()) {
			JError::raiseError(500, $error);
		}

		return (boolean) $result;
	}

	/**
	 * Uninstall packaged modules.
	 *
	 * @param	object	$installer	The parent installer object.
	 *
	 * @return	array	An array of the installed modules.
	 * @since	1.0
	 */
	public static function uninstallModules($installer)
	{
		$result = array();

		$modules = &$installer->manifest->getElementByPath('modules');
		if (is_a($modules, 'JSimpleXMLElement') && count($modules->children())) {

			foreach ($modules->children() as $module)
			{
				$mname		= $module->attributes('module');
				$mclient	= JApplicationHelper::getClientInfo($module->attributes('client'), true);
				$mposition	= $module->attributes('position');
				$mtitle		= $module->attributes('title');

				// Set the installation path
				if (!empty ($mname)) {
					$installer->parent->setPath('extension_root', $mclient->path.'/modules/'.$mname);
				}
				else {
					$installer->parent->abort(JText::_('J_INSTALL_MODULE').' '.JText::_('J_INSTALL_UNINSTALL').': '.JText::_('J_INSTALL_MODULE_FILE_MISSING'));
					return false;
				}

				/**
				 * ---------------------------------------------------------------------------------------------
				 * Database Processing Section
				 * ---------------------------------------------------------------------------------------------
				 */
				$db = &JFactory::getDBO();

				// Lets delete all the module copies for the type we are uninstalling
				$query = 'SELECT `id`' .
						' FROM `#__modules`' .
						' WHERE module = '.$db->Quote($mname) .
						' AND client_id = '.(int)$mclient->id;
				$db->setQuery($query);
				$modules = $db->loadResultArray();

				// Do we have any module copies?
				if (count($modules)) {
					JArrayHelper::toInteger($modules);
					$modID = implode(',', $modules);
					$query = 'DELETE' .
							' FROM #__modules_menu' .
							' WHERE moduleid IN ('.$modID.')';
					$db->setQuery($query);
					if (!$db->query()) {
						JError::raiseWarning(100, JText::_('J_INSTALL_MODULE').' '.JText::_('J_INSTALL_UNINSTALL').': '.$db->stderr(true));
						$retval = false;
					}
				}

				// Delete the modules in the #__modules table
				$query = 'DELETE FROM #__modules WHERE module = '.$db->Quote($mname);
				$db->setQuery($query);
				if (!$db->query()) {
					JError::raiseWarning(100, JText::_('J_INSTALL_MODULE').' '.JText::_('J_INSTALL_UNINSTALL').': '.$db->stderr(true));
					$retval = false;
				}

				/**
				 * ---------------------------------------------------------------------------------------------
				 * Filesystem Processing Section
				 * ---------------------------------------------------------------------------------------------
				 */

				// Remove all necessary files
				$element = &$module->getElementByPath('files');
				if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
					$installer->parent->removeFiles($element, -1);
				}

				// Remove all necessary files
				$element = &$module->getElementByPath('media');
				if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
					$installer->parent->removeFiles($element, -1);
				}

				$element = &$module->getElementByPath('languages');
				if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
					$installer->parent->removeFiles($element, $mclient->id);
				}

				// Remove the installation folder
				if (!JFolder::delete($installer->parent->getPath('extension_root'))) {
				}

				$result[] = array('name'=>$mtitle,'client'=>$mclient->name);
			}
		}
		return $result;
	}

	/**
	 * Uninstall packaged plugins.
	 *
	 * @param	object	$installer	The parent installer object.
	 *
	 * @return	array	An array of the installed modules.
	 * @since	1.0
	 */
	public static function uninstallPlugins(&$installer)
	{
		$result = array();
		$plugins = &$installer->manifest->getElementByPath('plugins');
		if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {

			foreach ($plugins->children() as $plugin)
			{
				$pelement		= $plugin->attributes('plugin');
				$pgroup		= $plugin->attributes('group');
				$pname		= $plugin->attributes('name');

				// Set the installation path
				if (!empty($pelement) && !empty($pgroup)) {
					$installer->parent->setPath('extension_root', JPATH_ROOT.'/plugins/'.$pgroup);
				} else {
					$installer->parent->abort(JText::_('J_INSTALL_PLUGIN').' '.JText::_('J_INSTALL_UNINSTALL').': '.JText::_('J_INSTALL_PLUGIN_FILE_MISSING'));
					return false;
				}

				/**
				 * ---------------------------------------------------------------------------------------------
				 * Database Processing Section
				 * ---------------------------------------------------------------------------------------------
				 */
				$db = &JFactory::getDBO();

				// Delete the plugins in the #__plugins table
				$query = 'DELETE FROM #__plugins WHERE element = '.$db->Quote($pelement).' AND folder = '.$db->Quote($pgroup);
				$db->setQuery($query);
				if (!$db->query()) {
					JError::raiseWarning(100, JText::_('J_INSTALL_PLUGIN').' '.JText::_('J_INSTALL_UNINSTALL').': '.$db->stderr(true));
					$retval = false;
				}

				/**
				 * ---------------------------------------------------------------------------------------------
				 * Filesystem Processing Section
				 * ---------------------------------------------------------------------------------------------
				 */

				// Remove all necessary files
				$element = &$plugin->getElementByPath('files');
				if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
					$installer->parent->removeFiles($element, -1);
				}

				$element = &$plugin->getElementByPath('languages');
				if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
					$installer->parent->removeFiles($element, 1);
				}

				// If the folder is empty or has only index.html, let's delete it
				$files = JFolder::files($installer->parent->getPath('extension_root'));
				if (!count($files) || (count($files)==1 && $files[0]=='index.html')) {
					JFolder::delete($installer->parent->getPath('extension_root'));
				}

				$result[] = array('name'=>$pname,'group'=>$pgroup);
			}
		}
		return $result;
	}

	/**
	 * Upgrade the database with an XML schema file.
	 *
	 * @param	string	$xml	The XML string.
	 *
	 * @return	array	Returns the database upgrade log.
	 * @since	1.0
	 */
	public static function upgrade($xml)
	{
		// Include dependancies.
		require_once dirname(dirname(__FILE__)).'/libraries/joomla/database/database/mysqlxml.php';

		JDatabaseMySQLXML::import($xml);

		return JDatabaseMySQLXML::getLog();
	}
}

