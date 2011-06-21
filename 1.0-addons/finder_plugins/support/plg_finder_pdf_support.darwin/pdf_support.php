<?php
/**
 * @version		$Id: pdf_support.php 981 2010-06-15 18:38:02Z robs $
 * @package		JXtended.Finder
 * @subpackage	plgFinderPDF_Support
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Set the adapter support flag.
define('FINDER_ADAPTER_PDF_SUPPORT', true);

/**
 * Finder adapter for PDF support.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderPDF_Support
 */
class plgFinderPDF_Support extends JPlugin
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
		$lang->load('plg_finder_pdf_support');
		$lang->load('plg_finder_pdf_support.custom');

		// Handle Windows.
		if (JUtility::isWinOS()) {
			$platform	= 'Windows';
			$command	= dirname(__FILE__).'/xpdf/pdftotext.exe';
		}
		// Handle FreeBSD.
		elseif (php_uname('s') == 'FreeBSD') {
			$platform	= 'FreeBSD';
			$command	= dirname(__FILE__).'/xpdf/pdftotext-freebsd';
		}
		// Handle Apple OS X.
		elseif (php_uname('s') == 'Darwin') {
			$platform	= 'Darwin';
			$command	= dirname(__FILE__).'/xpdf/pdftotext-darwin';
		}
		// Default to Linux.
		else {
			$platform	= 'Linux';
			$command	= dirname(__FILE__).'/xpdf/pdftotext-linux';
		}

		// Check if the correct command is available.
		if (!JFile::exists($command)) {
			JError::raiseNotice(0, JText::sprintf('FINDER_PDF_SUPPORT_OS_MISMATCH', $platform));
			return false;
		}

		return true;
	}
}