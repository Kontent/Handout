<?php
/**
 * @version		$Id: docx_support.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgFinderDOCX_Support
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Set the adapter support flag.
define('FINDER_ADAPTER_DOCX_SUPPORT', true);

/**
 * Finder adapter for DOCX support.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderDOCX_Support
 */
class plgFinderDOCX_Support extends JPlugin
{
	/**
	 * Method to catch the finder setup check event and report any potential
	 * installation or setup problems.
	 *
	 * @return	boolean		True on success, false on error.
	 */
	public function onFinderSetupCheck()
	{
		jimport('joomla.filesystem.file');

		// Load the language files for the adapter.
		$lang = JFactory::getLanguage();
		$lang->load('plg_finder_docx_support');
		$lang->load('plg_finder_docx_support.custom');

		return true;
	}
}