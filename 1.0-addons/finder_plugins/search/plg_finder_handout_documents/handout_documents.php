<?php
/**
 * @version		$Id$
 * @package		JXtended.Finder
 * @subpackage	plgFinderHANDOUT_Documents
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

// Load the language files for the adapter.
$lang = JFactory::getLanguage();
$lang->load('plg_finder_handout_documents');
$lang->load('plg_finder_handout_documents.custom');

/**
 * Finder adapter for Handout Documents.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderHANDOUT_Documents
 */
class plgFinderHANDOUT_Documents extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'Handout_Documents';

	/**
	 * @var		string		The type of content the adapter indexes.
	 */
	protected $_type_title = 'Document';

	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $_layout = 'document';

	/**
	 * Method to reindex the link information for an item that has been saved.
	 * This event is fired before the data is actually saved so we are going
	 * to queue the item to be indexed later.
	 *
	 * @param	integer		The id of the item.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onBeforeSaveHandoutDocument($id)
	{
		// Queue the item to be reindexed.
		FinderIndexerQueue::add($this->_context, $id, JFactory::getDate()->toMySQL());

		return true;
	}

	/**
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item is published,
	 * unpublished, archived, or unarchived from the list view.
	 *
	 * @param	array		An array of item ids.
	 * @param	string		The property that is being changed.
	 * @param	integer		The new value of that property.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onChangeHandoutDocument($ids, $property, $value)
	{
		// Check if we are changing the published state.
		if ($property === 'published') {
			$property = 'state';
		}

		// Update the items.
		return $this->_change($ids, $property, $value);
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param	array		An array of item ids.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onDeleteHandoutDocument($ids)
	{
		// Remove the items.
		return $this->_remove($ids);
	}

		/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param	object		The item to index as an FinderIndexerResult object.
	 * @throws	Exception on database error.
	 */
	protected function _index(FinderIndexerResult $item)
	{
		// Initialize the item parameters.
		$item->params = new JParameter($item->params);

		// Let's do a little trick to get the Itemid.
		$tmp = array('option' => 'com_handout', 'task' => 'doc_download', 'gid' => $item->id);
		DocmanBuildRoute($tmp);
		$Itemid = !empty($tmp['Itemid']) ? '&Itemid='.$tmp['Itemid'] : null;

		// Build the necessary route and path information.
//		$item->alias	= JFilterOutput::stringURLSafe($item->title);
//		$item->slug		= $item->id.':'.$item->alias;
		$item->url		= $this->_getURL($item->id);
		$item->route	= $this->_getURL($item->id).$Itemid;
		$item->path		= FinderIndexerHelper::getContentPath($item->route);

		// Get the menu title if it exists.
		$title = $this->_getItemMenuTitle($item->url);

		// Adjust the title if necessary.
		if (!empty($title) && $this->params->get('use_menu_title', true)) {
			$item->title = $title;
		}

		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'filename');

		// Skip the file if it is a link.
		if (strpos($item->filename, 'Link:') !== 0)
		{
			// Set the mime type (not a real mime type but it will do).
			$item->mime = strtolower(JFile::getExt($item->filename));

			// Set the full file path.
			$item->filepath = JPath::clean($this->_handout_config->handoutpath.DS.$item->filename);

			// Make sure that the path exists.
			if (JFile::exists($item->filepath) !== false)
			{
				// Get the physical file size.
				$item->size = filesize($item->filepath);

				// Open the stream.
				$item->body = $this->_openFile($item->filepath, $item->mime);

				// Read the first 2K from the stream for the summary if empty.
				if (empty($item->summary) && is_resource($item->body)) {
					$item->summary = fread($item->body, 2048);
				}
			}
		}

		// Set the language.
		$item->language	= FinderIndexerHelper::getDefaultLanguage();

		// If a mime type is defined for this item, use that as the item type.
		if (!empty($item->mime))
		{
			// Override the adapter mime type.
			$this->_mime = $item->mime;

			// Override the type title.
			$this->_type_title = strtoupper($item->mime);

			// Check if the file type is already defined.
			$this->_type_id = $this->_getTypeId();

			// Add the type and get the new type id if necessary.
			if (empty($this->_type_id)) {
				$this->_type_id = FinderIndexerHelper::addContentType($this->_type_title, $this->_mime);
			}

			// Override the type id and layout for the result item.
			$item->type_id	= $this->_type_id;
			$item->layout	= $this->_mime;

			// Add the type taxonomy data.
			$item->addTaxonomy('Type', $this->_type_title);
		}
		// If no file type is defined, use the base type of "Document".
		else
		{
			// Add the type taxonomy data.
			$item->addTaxonomy('Type', 'Document');
		}

		// Add the license taxonomy data.
		if (!empty($item->license)) {
			$item->addTaxonomy('License', $item->license);
		}

		// Add the category taxonomy data.
		if (!empty($item->category)) {
			$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);
		}

		// Get content extras.
		FinderIndexerHelper::getContentExtras($item);

		// Index the item.
		FinderIndexer::index($item);

		// Close the file.
		if (is_resource($item->body)) {
			pclose($item->body);
		}
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return	boolean		True on success, false on failure.
	 */
	protected function _setup()
	{
		// Load dependent classes.
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.path');

		// Load the router.
		require_once JPATH_SITE.'/components/com_handout/router.php';

		// Register the DOCman configuration class.
		JLoader::register('HandoutConfig', JPATH_ADMINISTRATOR.'/components/com_handout/handout.config.php');

		// Check if the Handout class is available.
		if (!class_exists('HandoutConfig', true)) {
			throw new Exception(JText::_('FINDER_PLG_HANDOUT_DOCUMENTS_CONFIG_NOT_FOUND'), 500);
		}

		// Instantiate the Handout configuration class.
		$this->_handout_config = new HandoutConfig();

		// Check if the Handout documents path is valid.
		if (empty($this->_handout_config->handoutpath) || !JFolder::exists($this->_handout_config->handoutpath)) {
			throw new Exception(JText::sprintf('FINDER_PLG_HANDOUT_DOCUMENTS_PATH_NOT_FOUND', $this->_handout_config->handoutpath), 500);
		}

		return true;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param	mixed		A JDatabaseQuery object or null.
	 * @return	object		A JDatabaseQuery object.
	 */
	protected function _getListQuery($sql = null)
	{
		// Check if we can use the supplied SQL query.
		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : new JDatabaseQuery();
		$sql->select('a.id, a.docname AS title, a.docdescription AS summary, a.docfilename AS filename');
		$sql->select('a.published AS state, a.access, a.docdate_published AS start_date');
		$sql->select('a.docthumbnail AS media, a.attribs AS params, a.catid');
		$sql->select('c.title AS category, c.published AS cat_state, c.access AS cat_access');
		$sql->select('a.doclicense_id, a.doclicense_display, l.name AS license');
		$sql->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug');
		$sql->select('u.name AS author');
		$sql->from('#__handout AS a');
		$sql->join('LEFT', '#__handout_licenses AS l ON l.id = a.doclicense_id');
		$sql->join('LEFT', '#__categories AS c ON c.id = a.catid');
		$sql->join('LEFT', '#__users AS u ON u.id = a.docsubmittedby');

		return $sql;
	}

	/**
	 * Method to get the query clause for getting items to update by time.
	 *
	 * @param	string		The modified timestamp.
	 * @return	object		A JDatabaseQuery object.
	 */
	protected function _getUpdateQueryByTime($time)
	{
		// Build an SQL query based on the modified time.
		$sql = new JDatabaseQuery();
		$sql->order('a.id DESC');

		return $sql;
	}

	/**
	 * Method to get the URL for the item. The URL is how we look up the link
	 * in the Finder index.
	 *
	 * @param	string		The id of the item.
	 * @return	string		The URL of the item.
	 */
	protected function _getURL($id)
	{
		return 'index.php?option=com_handout&task=doc_download&gid='.$id;
	}

	/**
	 * Method to open a file as a stream.
	 *
	 * @param	string		The file path to open.
	 * @param	string		The file mime type.
	 * @return	mixed		A popen() resource on success, false on failure.
	 */
	private function _openFile($path, $mime)
	{
		$return = false;

		// Make sure the path is clean!
		$path = JPath::clean($path);

		// Handle the supported mime types.
		switch (strtoupper($mime))
		{
			/*
			 * Handle Handout files using antiword.
			 */
			case 'DOC':
			{
				// Check if Handout support is available.
				if (!defined('FINDER_ADAPTER_DOC_SUPPORT')) break;

				// Handle Windows.
				if (JUtility::isWinOS()) {
					$command = dirname(__FILE__).'/antiword/antiword.exe';
				}
				// Handle FreeBSD.
				elseif (php_uname('s') == 'FreeBSD') {
					$command = dirname(__FILE__).'/antiword/antiword-freebsd';
				}
				// Handle Apple OS X.
				elseif (php_uname('s') == 'Darwin') {
					$command = dirname(__FILE__).'/antiword/antiword-darwin';
				}
				// Default to Linux.
				else {
					$command = dirname(__FILE__).'/antiword/antiword-linux';
				}

				// Antiword requires the HOME environment variable to be set.
				if (!getenv('HOME')) {
					putenv('HOME="'.dirname(__FILE__).'"');
				}

				// Open the file as a process resource.
				if (JFile::exists($command) && JFile::exists($path)) {
					$return = popen(sprintf('%s -m UTF-8 "%s" - 2>&1', $command, $path), 'r');
				}
			} break;

			/*
			 * Handle DOCX files using docx2txt.
			 */
			case 'DOCX':
			{
				// Handle all environments.
				$command = dirname(__FILE__).'/docx2txt/docx2txt.pl';

				// Open the file as a process resource.
				if (JFile::exists($command) && JFile::exists($path)) {
					$return = popen(sprintf('perl %s "%s" - 2>&1', $command, $path), 'r');
				}
			} break;

			/*
			 * Handle PDF files using pdftotext.
			 */
			case 'PDF':
			{
				// Check if PDF support is available.
				if (!defined('FINDER_ADAPTER_PDF_SUPPORT')) break;

				// Handle Windows.
				if (JUtility::isWinOS()) {
					$command = dirname(__FILE__).'/xpdf/pdftotext.exe';
				}
				// Handle FreeBSD.
				elseif (php_uname('s') == 'FreeBSD') {
					$command = dirname(__FILE__).'/xpdf/pdftotext-freebsd';
				}
				// Handle Apple OS X.
				elseif (php_uname('s') == 'Darwin') {
					$command = dirname(__FILE__).'/xpdf/pdftotext-darwin';
				}
				// Default to Linux.
				else {
					$command = dirname(__FILE__).'/xpdf/pdftotext-linux';
				}

				// Open the file as a process resource.
				if (JFile::exists($command) && JFile::exists($path)) {
					$return = popen(sprintf('%s -enc "UTF-8" -eol unix -nopgbrk "%s" - 2>&1', $command, $path), 'r');
				}
			} break;

			/*
			 * Handle TXT, HTML, and XML files.
			 */
			case 'HTM':
			case 'HTML':
			case 'TXT':
			case 'XML':
			{
				// Open the file as a resource.
				if (JFile::exists($path)) {
					$return = @fopen($path, 'r');
				}
			} break;
		}

		return $return;
	}
}