<?php

 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: handout.migration.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

class HandoutMigration
{
    /**
     * Migrating data from other downloader components to Handout
     *
     */

	//TO DO: How to handle duplicate files during migration - rename or ignore?
	//TO DO: Create document alias, if empty in source component

    protected $sourceComponent;  //source component to migrate data from
	protected $categoriesIds;
    protected $docsIds;
    protected $groupsIds;
    protected $licencesIds;
    protected $logsIds;
    protected $historyIds;
	protected $tables;
	protected $handoutPath;
	protected $documentsPath;


    /**
     * @static
     */
    function & getInstance( $sourceComponent )
	{
		//check source component is supported by migration tool
		if (!$sourceComponent || !in_array($sourceComponent, array('com_docman', 'com_joomdoc', 'com_remository', 'com_rokdownloads', 'com_rubberdoc', 'folder'))) {
            $this->error(JText::sprintf('COM_HANDOUT_MGR_NOT_SUPPORTED', $sourceComponent), false);
		}

		$classname = ($sourceComponent=='folder') ? 'Folder' : ucwords(str_replace('com_', '', $sourceComponent)); // e.g. com_docman -> Docman
	    $classname = "HandoutMigration_".$classname;
        $instance = new $classname;
		$instance->setComponent($sourceComponent);
		$instance->init();
		$instance->check();
     	return $instance;
    }

	protected function setComponent($comp)
	{
		$this->sourceComponent = trim($comp);
	}

    /**
     * Init aplication
     *
     */
    protected function init ()
    {
		global $_HANDOUT;

        $this->categoriesIds = array();
        $this->docsIds = array();
        $this->groupsIds = array();
        $this->licencesIds = array();
        $this->logsIds = array();
        $this->historyIds = array();
		$this->tables = array();
        $this->handoutPath = $_HANDOUT->getCfg('handoutpath');
    }

	/**
     * Check to ensure Source component and Handout component are both installed
	 *
     */
	protected function check ()
    {
		$sourceComponent = $this->sourceComponent;

		//check if source component is installed
		$componentFile = ($sourceComponent == 'com_rubberdoc') ? 'rubberdoc.php' : 'admin.'.str_replace('com_', '', $sourceComponent).'.php';
        $oldComponentBase = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $sourceComponent . DS . $componentFile;

        if (! file_exists($oldComponentBase)) {
            $this->error(JText::sprintf('COM_HANDOUT_MGR_OLD_INSTALL_NO_EXISTS', $sourceComponent), false);
        }

		//check Handout Path is set
        if (! $this->handoutPath) {
            $this->error(JText::_('COM_HANDOUT_MGR_NEW_PATH_NOT_SET'), false);
        }

		//make Handout Path writable
        if (! file_exists($this->handoutPath)) {
            mkdir($this->handoutPath, 0777);
            if (! file_exists($this->handoutPath)) {
                $this->error(JText::_('COM_HANDOUT_MGR_DIR_NEW_NO_EXISTS') . $this->handoutPath, false);
            }
        }
		elseif (! is_writable($this->handoutPath)) {
            $this->error(JText::_('COM_HANDOUT_MGR_DIR_IS_UNWRITEABLE') . $this->handoutPath, false);
        }

    }

	/**
     * Clean migration after error
     *
     */
    protected function clean ()
    {
        $this->cleanCategories();
        $this->cleanDocs();
        $this->cleanGroups();
        $this->cleanLicenses();
        $this->cleanLog();
    }

    /**
     * Do migration
     *
     */
    function migrate ()
    {
        $this->tablesExist(true);
        $this->migrateLicences();
        $this->migrateGroups();
        $this->migrateCategories();
        $this->migrateFiles();
        $this->back(JText::_('COM_HANDOUT_MGR_SUCCESS'));
    }

    /**
     * Check if all tables needed for migration exists
     *
     * @param boolean $msg show error if any table no exist
     * @return boolean true - all tables exists
     */

    protected function tablesExist($msg = false)
    {
        $db = JFactory::getDBO();

        //DB config prefix
        $prefix = $db->_table_prefix;

        //list of noexists tables
        $noExists = array();
        foreach ($this->tables as $table) {
            $realName = $prefix . $table; // e.g. #__docman => jos_docman
            $query = "SHOW TABLES LIKE '$realName'";
            $db->setQuery($query);
            $exists = $db->loadResultArray();
            if (! count($exists)) {
                $noExists[] = $realName;
            }
        }
        if ($msg && count($noExists)) {
            $this->error(JText::_('COM_HANDOUT_MGR_TABLES_NO_EXISTS') . implode(', ', $noExists));
        }
        return (! count($noExists));
    }


    /**
     * Migrate licences from source component licenses table to #__handout_licenses
     *
     * @return array key is old id of licence, value is new id after migration
     */
    protected function migrateLicences ()
    {
		return array();
	}

    /**
     * Migrate groups from source component groups table to source component groups
     *
     * @return array key is old id of group, value is new id after migration
     */
    protected function migrateGroups ()
	{
		return array();
	}

    /**
     * Migrate categories with all documents
     *
     */
    protected function migrateCategories ()
    {
		return;
	}

    /**
     * Migrate Logs from source component log to #__handout_log
     *
     * @param int $newId new id of document after migration
     * @param int $oldId old id before migration
     */
    protected function migrateLogs ($newId, $oldId)
    {
		return;
	}

    /**
     * Migrate documents from source component to #__handout with all history and logs, where applicable
     *
     * @param int $oldCatid old category id
     * @param int $newCatid new category id after migration
     */
    protected function migrateDocs ($oldCatid=0, $newCatid=0)
    {
		return;
	}

    /**
     * Migrate files
     *
     */
    protected function migrateFiles ()
    {
		return;
	}

    /**
     * Migrate History from source component history to #__handout_history
     *
     * @param int $newId new id of document after migration
     * @param int $oldId old id before migration
     */
    protected function migrateHistory ($newId, $oldId)
    {
		return;
	}

    /**
     * Get all old source component categories
     *
     * @return array
     */
    protected function getCategories ()
    {
		return;
	}

    /**
     * Copy document data from old component to Handout
     *
     * @return array
     */
    protected function copyDoc ($oldRow, &$newRow)
    {
		return;
	}

    /**
     * Clean all migrated categories after error
     *
     */
    protected function cleanCategories ()
    {
		if (!count($this->categoriesIds))
			return;

        $db = &JFactory::getDBO();
        $query = "DELETE FROM `#__categories` WHERE `id` IN (" . implode(',', $this->categoriesIds) . ")";
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Clean all migrated documents after error
     *
     */
    protected function cleanDocs ()
    {
		if (!count($this->docsIds))
			return;

        $db = &JFactory::getDBO();
        $query = "DELETE FROM `#__handout` WHERE `id` IN (" . implode(',', $this->docsIds) . ")";
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Clean all migrated history after error
     *
     */
	protected function cleanHistory ()
    {
		if (!count($this->historyIds))
			return;

        $db = &JFactory::getDBO();
        $query = "DELETE FROM `#__handout_history` WHERE `id` IN (" . implode(',', $this->historyIds) . ")";
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Clean all migrated logs after error
     *
     */
    protected function cleanLog ()
    {
		if (!count($this->logsIds))
			return;

        $db = &JFactory::getDBO();
        $query = "DELETE FROM `#__handout_log` WHERE `id` IN (" . implode(',', $this->logsIds) . ")";
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Clean all migrated licenses after error
     *
     */
    protected function cleanLicenses ()
    {
		if (!count($this->licencesIds))
			return;

        $db = &JFactory::getDBO();
        $query = "DELETE FROM `#__handout_licenses` WHERE `id` IN (" . implode(',', $this->licencesIds) . ")";
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Clean all migrated groups after error
     *
     */
    protected function cleanGroups ()
    {
        $db = &JFactory::getDBO();
        $query = "DELETE FROM `#__handout_groups` WHERE `groups_id` IN (" . implode(',', $this->groupsIds) . ")";
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Get all old source component documents with specified category
     *
     * @param int $catid category id
     * @return array
     */
    protected function getDocs ($catid)
    {
		return array();
	}

    /**
     * Get all old source component histories with specified document id
     *
     * @param int $docid document id
     * @return array
     */
    protected function getHistory ($docid)
    {
		return array();
	}

    /**
     * Get all old source component logs with specified document id
     *
     * @param int $docid document id
     * @return array
     */
    protected function getLog ($docid)
    {
		return array();
	}

    /**
     * Get all old DOCman licences
     *
     * @return array
     */
    protected function getLicenses ()
    {
		return array();
	}


    /**
     * Get all old DOCman groups
     *
     * @return array
     */
    protected function getGroups ()
    {
		return array();
	}

    /**
     * Back into Handout cpanel
     *
     */
    protected function back ($msg = null)
    {
        $mainframe = &JFactory::getApplication();
        $url = 'index.php?option=com_handout&section=config';
        if ($msg) {
            $mainframe->redirect($url, $msg);
        } else {
            $mainframe->redirect($url);
        }
    }

    /**
     * Print error msg and clean
     *
     * @param string $msg
     * @param boolean $clean
     */
    protected function error ($msg = '', $clean = true)
    {

        if (! $clean) {
            JError::raiseNotice('SOME_ERROR_CODE', $msg);
        } else {
            $msg = JText::_('COM_HANDOUT_MGR_FATAL_ERROR') . ' ' . $msg;
            JError::raiseError('SOME_ERROR_CODE', $msg);
            $this->clean(); //clean migrating
        }
        $this->back(); //back into handout cpanel
    }

    /**
     * Check Source component document path exists and is readable
     * Check Handout component document path exists and is writable
     */
	protected function checkDocumentsPath()
	{
        //dir with old files does not exist
        if (! file_exists($this->documentsPath)) {
            $this->error(JText::_('COM_HANDOUT_MGR_DIR_NO_EXISTS') . $this->documentsPath, false);
        }

        //dir with old files is not readable
        if (! is_readable($this->documentsPath)) {
            chmod($this->documentsPath, 755);
            if (! is_readable($this->documentsPath)) {
                $this->error(JText::_('COM_HANDOUT_MGR_DIR_NO_READABLE') . $this->documentsPath, false);
            }
        }

        //new dir does not exists
        if (! file_exists($this->handoutPath)) {
            mkdir($this->handoutPath, 0755);
            if (! file_exists($this->handoutPath)) {
                $this->error(JText::_('COM_HANDOUT_MGR_DIR_NEW_NO_EXISTS') . $this->handoutPath, false);
            }
        }

        //new dir is unwriteable
        if (! is_writable($this->handoutPath)) {
            chmod($this->handoutPath, 755);
            if (! is_writable($this->handoutPath)) {
                $this->error(JText::_('COM_HANDOUT_MGR_DIR_IS_UNWRITEABLE') . $this->handoutPath, false);
            }
        }

	}

    /**
     * Set the documents path for source component based on its config file
     */
	protected function setDocumentsPath()
	{
		return;
	}

	/**
	 *   Fetch the current userid
	 */
	protected function getCurrentUserId()
	{
        $user =& JFactory::getUser();
		return $user->id;
	}

	/**
	 * Checks to see if a given filename already exists in the handout folder and if so, gives a new name to the file
	  * @param string $file
	 */
	protected function renameDuplicate(&$file)
	{
		if (!$file)
			return;

		$i=0;
		while (file_exists($this->handoutPath . DS . $file) && $i<=100)
		{
 			$i++;
			$file = preg_replace('/(.+)\.([a-zA-Z]{2,4})/', '${1}1.$2', $file);
		}
	}

}

class HandoutMigration_Docman extends HandoutMigration
{
    /**
     * Migrating data from Docman to Handout
     *
     */

	protected $tablePrefix;
	protected $tables;
	protected $configFile;

	protected function init()
	{
		parent::init();

		$this->tablePrefix = '#__docman';
		$this->tables = array('categories' , 'docman' , 'docman_groups' , 'docman_history' , 'docman_licenses' , 'docman_log');
		$this->configFile =	JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_docman' . DS . 'docman.config.php';
		$this->setDocumentsPath();
	}

    protected function migrateCategories ()
    {
        $db = &JFactory::getDBO();
        $categories = $this->getCategories(); //with section of source component
        foreach ($categories as $category) {
		     if (empty($category->name)) $category->name = $category->title; //fill empty category name

            $row = new HandoutCategory($db);
            if (! $row->bind($category)) {
                $this->error($row->getError);
            }
            $oldCatid = $row->id;
            $row->id = 0;
            $row->section = 'com_handout'; //migrate for Handout
            if (! $row->store()) {
                $this->error($row->getError);
            }
            $newCatid = $row->id;
            $this->categoriesIds[$oldCatid] = $newCatid;
            $this->migrateDocs($oldCatid, $newCatid);
        }

        foreach ($this->categoriesIds as $old => $new) { //update parent ids
            $query = "UPDATE `#__categories` SET
				      `parent_id` = '$new'
				      WHERE `parent_id` = '$old'
				      AND `section` = 'com_handout'";
            $db->setQuery($query);
            $db->query();
        }
    }

    protected function migrateDocs ($oldCatid, $newCatid)
    {
        $db = &JFactory::getDBO();
        $docs = $this->getDocs($oldCatid); //from source component with old category
        foreach ($docs as $doc) {
            $row = new HandoutDocument($db);
            $this->copyDoc($doc, $row); //
            $oldDocId = $row->id;
            $row->id = 0;
            $row->catid = $newCatid; //migrated into migrate category
			$this->renameDuplicate($row->docfilename);

            if (isset($this->licencesIds[$row->doclicense_id])) { //change id of migrate license
                $row->doclicense_id = $this->licencesIds[$row->doclicense_id];
            }
            if (isset($this->groupsIds[$row->docowner])) { //change id of migrate owners group
                $row->docowner = $this->groupsIds[$row->docowner];
            }
            if (isset($this->groupsIds[$row->docmaintainedby])) { //change id of migrate maintainer group
                $row->docmaintainedby = $this->groupsIds[$row->docmaintainedby];
            }
            if (! $row->store()) { //into #__handout
                $this->error($row->getError);
            }
            $newDocId = $row->id;
            $this->docsIds[$oldDocId] = $newDocId;
            $this->migrateHistory($newDocId, $oldDocId);
            $this->migrateLogs($newDocId, $oldDocId);
        }
    }

    protected function migrateLicences ()
    {
        $db = &JFactory::getDBO();
        $licences = $this->getLicenses(); //from source component licenses
        foreach ($licences as $licence) {
            $row = new HandoutLicenses($db);
            if (! $row->bind($licence)) {
                $this->error($row->getError);
            }
            $oldId = $row->id;
            $row->id = 0;
            if (! $row->store()) { //into #__handout_licenses
                $this->error($row->getError);
            }
            $this->licencesIds[$oldId] = $row->id; //save old and new id to change in documents
        }
    }

    protected function migrateGroups ()
    {
        $db = &JFactory::getDBO();
        $groups = $this->getGroups(); //from source component groups
        foreach ($groups as $group) {
            $row = new HandoutGroups($db);
            if (! $row->bind($group)) {
                $this->error($row->getError);
            }
            $oldId = $row->getId();
            $row->groups_id = 0;
            if (! $row->store()) { //into #__handout_groups
                $this->error($row->getError);
            }
            $this->groupsIds[$oldId] = $row->getId(); //save old and new id to change in documents
        }
    }

    protected function migrateLogs ($newId, $oldId)
    {
        $db = &JFactory::getDBO();
        $logs = $this->getLog($oldId); //from source component log
        foreach ($logs as $log) {
            $row = new HandoutLog($db);
            if (! $row->bind($log)) {
                $this->error($row->getError);
            }
            $oldLogId = $row->id;
            $row->id = 0;
            $row->log_docid = $newId;
            if (! $row->store()) { //into #__handout_log
                $this->error($row->getError);
            }
            $this->logsIds[$oldLogId] = $row->id;
        }
    }

    protected function migrateHistory ($newId, $oldId)
    {
        $db = &JFactory::getDBO();
        $histories = $this->getHistory($oldId); //from source component history
        foreach ($histories as $history) {
            $row = new HandoutHistory($db);
            if (! $row->bind($history)) {
                $this->error($row->getError);
            }
            $oldHistoryId = $row->id;
            $row->id = 0;
            $row->doc_id = $newId;
            if (! $row->store()) { //into #__handout_history
                $this->error($row->getError);
            }
            $this->historyIds[$oldHistoryId] = $row->id;
        }
    }

    protected function migrateFiles ()
    {
        $noCopy = array(); //list of no migrates files

		$this->checkDocumentsPath();

        //open old files dir
        $dir = opendir($this->documentsPath);

        //get all files to copy
        while (($file = readdir($dir))) {
            if (is_file($this->documentsPath . DS . $file)) { //no copy links, directories and no file links
				$oldFile = $newFile = $file;
				$this->renameDuplicate($newFile);

                $source = $this->documentsPath . DS . $oldFile; //old destination
                $target = $this->handoutPath . DS . $newFile; //new destination

                if (! copy($source, $target)) { //unable copy - maybe permision denied
                    $noCopy[] = $source;
                }
            }
        }

        if (count($noCopy)) {
            $this->error(JText::_('COM_HANDOUT_MGR_UNABLE_COPY') . implode(', ', $noCopy), false);
        }
    }

	protected function setDocumentsPath() {
        //configuration of old installation of component
        if (! file_exists($this->configFile)) { //does not exist - probably component was unistalled
            $this->error(JText::_('COM_HANDOUT_MGR_UNABLE_FIND_OLD_CONF_FILE') . $this->configFile, false);
        }

        if (! is_readable($this->configFile)) { //configuration not readable
            $this->error(JText::_('COM_HANDOUT_MGR_UNABLE_READ_OLD_CONF') . $this->configFile, false);
        }

        //content of old configuration
        $content = file_get_contents($this->configFile);

        //find config param $dmpath
        $regex = '#var( *)\$dmpath( *)=( *)\'([^\']*)\'#iU';
        $matches = array();

        //$dmpath not found
        if (! preg_match($regex, $content, $matches)) {
            $this->error(JText::_('COM_HANDOUT_MGR_UNABLE_FIND_OLD_CONF') . $this->configFile, false);
        }
        $this->documentsPath = $matches[4];
	}

    protected function getCategories ()
    {
        $db = &JFactory::getDBO();
        $query = "SELECT * FROM `#__categories` WHERE `section` = '{$this->sourceComponent}'";
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        if (is_null($categories)) {
            $this->error(JText::_('COM_HANDOUT_MGR_ERR_LOAD_OLD_CATEGORIES'));
        }
        return $categories;
    }

    protected function getDocs ($catid)
    {
        $db = &JFactory::getDBO();
        $query = "SELECT * FROM `{$this->tablePrefix}` WHERE `catid` = '$catid'";
        $db->setQuery($query);
        $docs = $db->loadObjectList();
        if (is_null($docs)) {
            $this->error(JText::_('COM_HANDOUT_MGR_ERR_LOAD_OLD_DOCUMENTS'));
        }
        return $docs;
    }

    protected function getLog ($docid)
    {
        $db = &JFactory::getDBO();
        $query = "SELECT * FROM `{$this->tablePrefix}_log` WHERE `log_docid` = '$docid'";
        $db->setQuery($query);
        $logs = $db->loadObjectList();
        if (is_null($logs)) {
            $this->error(JText::_('COM_HANDOUT_MGR_ERR_LOAD_OLD_LOGS'));
        }
        return $logs;
    }

    protected function getLicenses ()
    {
        $db = &JFactory::getDBO();
        $query = "SELECT * FROM `{$this->tablePrefix}_licenses`";
        $db->setQuery($query);
        $licences = $db->loadObjectList();
        if (is_null($licences)) {
            $this->error(JText::_('COM_HANDOUT_MGR_ERR_LOAD_OLD_LICENCES'));
        }
        return $licences;
    }

    protected function getHistory ($docid)
    {
        $db = &JFactory::getDBO();
        $query = "SELECT * FROM `{$this->tablePrefix}_history` WHERE `doc_id` = '$docid'";
        $db->setQuery($query);
        $history = $db->loadObjectList();
        if (is_null($history)) {
            $this->error(JText::_('COM_HANDOUT_MGR_ERR_LOAD_OLD_HISTORY'));
        }
        return $history;
    }

    protected function getGroups ()
    {
        $db = &JFactory::getDBO();
        $query = "SELECT * FROM `{$this->tablePrefix}_groups`";
        $db->setQuery($query);
        $groups = $db->loadObjectList();
        if (is_null($groups)) {
            $this->error(JText::_('COM_HANDOUT_MGR_ERR_LOAD_OLD_GROUPS'));
        }
        return $groups;
    }

    protected function copyDoc ($oldDoc, &$newDoc)
    {
		$newDoc->id = $oldDoc->id;
		$newDoc->catid = $oldDoc->catid;
		$newDoc->docname = $oldDoc->dmname;
		$newDoc->docdate_published = $oldDoc->dmdate_published;
		$newDoc->docowner = $oldDoc->dmowner;
		$newDoc->docfilename = $oldDoc->dmfilename;
		$newDoc->published = $oldDoc->published;
		$newDoc->docurl = $oldDoc->dmurl;
		$newDoc->doccounter = $oldDoc->dmcounter;
		$newDoc->checked_out = $oldDoc->checked_out;
		$newDoc->checked_out_time = $oldDoc->checked_out_time;
		$newDoc->docthumbnail = $oldDoc->dmthumbnail;
		$newDoc->doclastupdateon = $oldDoc->dmlastupdateon;
		$newDoc->doclastupdateby = $oldDoc->dmlastupdateby;
		$newDoc->docsubmittedby = $oldDoc->dmsubmittedby;
		$newDoc->docmaintainedby = $oldDoc->dmmaintainedby;
		$newDoc->doclicense_id = $oldDoc->dmlicense_id;
		$newDoc->doclicense_display = $oldDoc->dmlicense_display;
		$newDoc->access = $oldDoc->access;
		$newDoc->attribs = $oldDoc->attribs;
    }
}

class HandoutMigration_Joomdoc extends HandoutMigration_Docman
{
    /**
     * Migrating data from JoomDoc to Handout
     *
     */

	protected function init()
	{
		parent::init(); //values set in Docman class needs to be overwritten below

		$this->tablePrefix = '#__joomdoc';
		$this->tables = array('categories' , 'joomdoc' , 'joomdoc_groups' , 'joomdoc_history' , 'joomdoc_licenses' , 'joomdoc_log');
		$this->configFile =	JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_joomdoc' . DS . 'docman.config.php';
		$this->setDocumentsPath();
	}
}

class HandoutMigration_Rokdownloads extends HandoutMigration
{
    /**
     * Migrating data from RokDownloads to Handout
     *
     */


	protected function init()
	{
		parent::init();

		$this->tablePrefix = '#__rokdownloads';
		$this->tables = array('categories' , 'rokdownloads');
		$this->configFile =	JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_rokdownloads' . DS . 'admin_config.xml';
		$this->setDocumentsPath();
	}

    function migrate ()
    {
        $this->tablesExist(true);
        $this->migrateCategories();
        $this->migrateDocs();
		$this->migrateFiles();
        $this->back(JText::_('COM_HANDOUT_MGR_SUCCESS'));
    }

    protected function migrateCategories ()
    {

        $db = &JFactory::getDBO();
        $query = "SELECT * FROM `{$this->tablePrefix}` WHERE `folder`=1 ORDER BY path";

        $db->setQuery($query);
        $pathObjs = $db->loadObjectList();
        if (is_null($pathObjs)) {
            $this->error(JText::_('COM_HANDOUT_MGR_ERR_LOAD_OLD_CATEGORIES'));
        }

		foreach ($pathObjs as $pathObj) {
			$segments = explode("/", $pathObj->path);
			if (!count($segments)) {
	            $this->error(JText::_('COM_HANDOUT_MGR_ERR_LOAD_OLD_CATEGORIES'));
			}

			$pathSoFar = '';
			$parentId = 0;
			$i=0;
			foreach ($segments as $segment) {
				if (!strlen($segment)) continue;
				$pathSoFar .= '/' .$segment;
				if (!in_array($pathSoFar, array_keys($this->categoriesIds))) {
					$i++;

					//new category object
					$category = new HandoutCategory($db);
					$category->id=0;
					$category->parent_id = $parentId;
					$category->title = $segment;
					$category->name = $segment;
					$category->section = 'com_handout';
					$category->description = $pathObj->introtext;
					$category->published = $pathObj->published;
					$category->ordering = $i;
					$category->checked_out	= $pathObj->checked_out;
					$category->checked_out_time	= $pathObj->checked_out_time;
					$category->alias = $pathObj->alias;

					if (! $category->store()) {
						$this->error($category->getError);
					}
					$newCatId = $category->id;
					$this->categoriesIds[$pathSoFar] = $newCatId;
				}
			}
		}
	}

    protected function migrateDocs ($oldCatid=0, $newCatid=0)
    {
		$db = &JFactory::getDBO();
        $query = "SELECT DISTINCT * FROM `{$this->tablePrefix}` WHERE `folder`=0 ORDER BY name";
        $db->setQuery($query);
        $docObjs = $db->loadObjectList();
        if (is_null($docObjs)) {
            return;
        }

		foreach ($docObjs as $docObj) {
			//see if category exists
			$folder = str_replace('/'.$docObj->name, '', $docObj->path);
			if (!in_array($folder, array_keys($this->categoriesIds))) {
				continue;
			}
				//new document object
			$doc = new HandoutDocument($db);
    		$doc->id                 = null;
			$doc->catid              = $this->categoriesIds[$folder];
			$doc->docname             = $docObj->displayname;
			$doc->docfilename         = $this->renameDuplicate($docObj->name);
			$doc->docdescription      = $docObj->introtext;
			$doc->docdate_published   = $docObj->created_time;
			$doc->docowner            = $docObj->createdby;
			$doc->published          = $docObj->published;
			$doc->docurl              = null;
			$doc->doccounter          = $docObj->downloads;
			$doc->checked_out        = $docObj->checked_out;
			$doc->checked_out_time   = $docObj->checked_out_time;
			$doc->docthumbnail        = $docObj->thumbnail;
			$doc->doclastupdateon     = $docObj->modified_time;
			$doc->doclastupdateby     = $docObj->modified_by;
			$doc->docsubmittedby       = $docObj->createdby;
			$doc->docmaintainedby      = $docObj->createdby;
			$doc->doclicense_id       = null;
			$doc->doclicense_display  = null;
			$doc->docversion			= null;
			$doc->doclanguage		= null;
			$doc->doc_meta_keywords	= $docObj->metakey;
			$doc->doc_meta_description	= $docObj->metadesc;
			$doc->kunena_discuss_id = null;
			$doc->mtree_id 			= null;
			$doc->access             = $docObj->access;
			$doc->attribs            = null;

			if (! $doc->store()) {
				$this->error($doc->getError);
			}

		}
		return;
	}

    protected function migrateFiles ()
    {

		$this->checkDocumentsPath();

		//fetch all files recursively
		$files = JFolder::files($this->documentsPath, '', true, true);
		foreach ($files as $file) {
			$oldFile = $file;
			$newFile = basename($file);
			$this->renameDuplicate($newFile);
			@copy($oldFile, $this->handoutPath . DS . $newFile);
		}
	}

	protected function setDocumentsPath()
	{
		$this->documentsPath =  JPATH_SITE . '/rokdownloads/'; ////TO DO: need to replace with value from config file
	}

}

class HandoutMigration_Rubberdoc extends HandoutMigration_Docman
{
    /**
     * Migrating data from Rubberdoc to Handout
     *
     */

	protected function init()
	{
		parent::init();

		$this->tablePrefix = '#__rubberdoc';
		$this->tables = array('categories' , 'rubberdoc_docs');
		$this->configFile =	JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_rubberdoc' . DS . 'config.xml';
		$this->setDocumentsPath();
	}

    function migrate ()
    {
        $this->tablesExist(true);
        $this->migrateCategories();
		$this->migrateFiles();
        $this->back(JText::_('COM_HANDOUT_MGR_SUCCESS'));
    }

	protected function setDocumentsPath()
	{
		$this->documentsPath =  JPATH_SITE . '/rubberdoc/'; //TO DO: need to replace with value from config file
	}

    protected function migrateHistory ($oldCatid, $newCatid)
    {
		return;
	}

    protected function migrateLogs ($oldCatid, $newCatid)
    {
		return;
	}

    protected function copyDoc ($oldDoc, &$newDoc)
    {
		$newDoc->id = $oldDoc->id;
		$newDoc->catid = $oldDoc->category_id;
		$newDoc->docname = $oldDoc->title;
		$newDoc->docdate_published = $oldDoc->created;
		$newDoc->docowner = -1; //$this->getCurrentUserId();
		$newDoc->docfilename = $oldDoc->file;
		$newDoc->published = $oldDoc->published;
		$newDoc->doccounter = $oldDoc->downloads;
		$newDoc->checked_out = $oldDoc->checked_out;
		$newDoc->checked_out_time = $oldDoc->checked_out_time;
		$newDoc->doclastupdateon = $oldDoc->modified;
		$newDoc->doclastupdateby = $newDoc->docowner;
		$newDoc->docsubmittedby = $newDoc->docowner;
		$newDoc->docmaintainedby = $oldDoc->dmmaintainedby;
		$newDoc->doc_meta_keywords	= $oldDoc->metakey;
		$newDoc->doc_meta_description	= $oldDoc->metadesc;
		$newDoc->access = $oldDoc->access;
    }

    protected function getDocs ($catid)
    {
        $db = &JFactory::getDBO();
        $query = "SELECT * FROM `#__rubberdoc_docs` WHERE `category_id` = '$catid'";
        $db->setQuery($query);
        $docs = $db->loadObjectList();
        if (is_null($docs)) {
            $this->error(JText::_('COM_HANDOUT_MGR_ERR_LOAD_OLD_DOCUMENTS'));
        }
        return $docs;
    }

}

class HandoutMigration_Folder extends HandoutMigration
{
    /**
     * Migrating data from Folder to Handout
     *
     */

	private $tmpFolder;
	private $tmpFolderPath;

	protected function init()
	{
		$this->tmpFolder = urldecode(JRequest::getVar('folder', ''));
		$this->tmpFolderPath = JPATH_ROOT . DS . $this->tmpFolder;
		parent::init();
	}

	protected function check()
	{
		//check if folder name is not specified
		if ($this->tmpFolder == '') {
        	$this->error(JText::_('COM_HANDOUT_MGR_SERVERFOLDER_SPECIFY') . JPATH_ROOT, false);
		}

		//check if folder exists
		if (! file_exists($this->tmpFolderPath)) {
        	$this->error(JText::_('COM_HANDOUT_MGR_SERVERFOLDER_NO_EXISTS') . $this->tmpFolderPath, false);
       	}

		//check Handout Path is set
        if (! $this->handoutPath) {
            $this->error(JText::_('COM_HANDOUT_MGR_NEW_PATH_NOT_SET'), false);
        }

		//make Handout Path writable
        if (! file_exists($this->handoutPath)) {
            mkdir($this->handoutPath, 0777);
            if (! file_exists($this->handoutPath)) {
                $this->error(JText::_('COM_HANDOUT_MGR_DIR_NEW_NO_EXISTS') . $this->handoutPath, false);
            }
        }
		elseif (! is_writable($this->handoutPath)) {
            $this->error(JText::_('COM_HANDOUT_MGR_DIR_IS_UNWRITEABLE') . $this->handoutPath, false);
        }
	}

    function migrate ()
    {
		$this->migrateCategories();
        $this->back(JText::_('COM_HANDOUT_MGR_SUCCESS'));
    }

	protected function migrateCategories ()
	{
		$this->traverseFolder($this->tmpFolderPath);
	}

	/**
	 * step through the folder structurecreate categories for each sub-folder and documents for each file
	 * @param string $path folder to traverse
	 * @param string $parentId parent category id
	 */
	 private function traverseFolder($path, $parentId=0)
	 {
		$db = JFactory::getDBO();
		$handle = opendir($path);
        while (($file = readdir($handle)) !== false)
        {
            if (($file != '.') && ($file != '..')) {
                $dir =  $path . DS . $file;
                if (is_dir($dir)) {
					//add category
					$category = new HandoutCategory($db);
					$category->id=0;
					$category->parent_id = $parentId;
					$category->title = $file;
					$category->name = $file;
					$category->section = 'com_handout';
					$category->published = 1;

					if (! $category->store()) {
						$this->error($category->getError);
					}
					$newCatId = $category->id;

					//traverse subfolder
					$this->traverseFolder($dir, $newCatId);
                }
				else {
					//new document
					$error='';
					$oldFname = $file;

					if ($this->validateFileName($file, $error) && $this->validateExtension($file, $error) ) {
						$this->renameDuplicate($file);

						//copy file
						copy($path . DS . $oldFname, $this->handoutPath . DS . $file);

						//add document
						$doc = new HandoutDocument($db);
						$doc->id                 = null;
						$doc->catid              = $parentId;
						$doc->docname             = $file;
						$doc->docfilename         = $file;
						$doc->docdate_published   = date('Y-m-d H:i:s');
						$doc->docowner            = -1; //$this->getCurrentUserId();
						$doc->published          = 1;
						$doc->docurl              = null;
						$doc->doccounter          = 0;
						$doc->checked_out        = null;
						$doc->checked_out_time   = null;
						$doc->docthumbnail        = null;
						$doc->doclastupdateon     = null;
						$doc->doclastupdateby     = null;
						$doc->docsubmittedby       = $this->getCurrentUserId();
						$doc->docmaintainedby      = $this->getCurrentUserId();
						$doc->doclicense_id       = null;
						$doc->doclicense_display  = null;
						$doc->docversion			= null;
						$doc->doclanguage		= null;
						$doc->doc_meta_keywords	= null;
						$doc->doc_meta_description	= null;
						$doc->kunena_discuss_id  = null;
						$doc->mtree_id  		= null;
						$doc->access             = null;
						$doc->attribs            = null;

						if (! $doc->store()) {
							$this->error($doc->getError);
						}
					}
					else {
						//TO DO: For now, ignore the file in error and load others; how to handle error messages for the ignored file?
					}
                }
            }
        }
        closedir($handle);
	 }

	 private function validateFileName(&$name, &$error="")
	 {
	 	//validate file name
		global $_HANDOUT;

		$fname_blank   = $_HANDOUT->getCfg('fname_blank');
		$fname_reject  = $_HANDOUT->getCfg('fname_reject');
		$fname_lc      = $_HANDOUT->getCfg('fname_lc'   );

		//change to lowercase
		if($fname_lc ) {
			$name = strtolower( $name );
		}

		//check blanks in filename
		if(strchr($name , " "))
		{
			switch($fname_blank)
			{
				case 0: // Accept
				default:
				break;

				case 1: // REJECT
					$error .= JText::_('COM_HANDOUT_FILENAME')." &quot;" . $name . "&quot; ".JText::_('COM_HANDOUT_CONTAINBLANKS');
					return false;

				case 2: // convert to underscore
					$name=preg_replace( "/\s/" , '_' , $name );
					break;

				case 3: // convert to dash
					$name=preg_replace( "/\s/" , '-' , $name );
					break;

				case 4: // REMOVE
					$name=preg_replace( "/\s/" , '' , $name );
					break;
			}
		}

		//check files to reject
		if( ($fname_reject && preg_match( "/^(" . $fname_reject . ")$/i" , $name ) )
            || preg_match( "/^(" . COM_HANDOUT_FNAME_REJECT . ")$/i" , $name )){
			$error .= "&quot;" . $name . "&quot; ".JText::_('COM_HANDOUT_ISNOTVALID');
			return false;
		}

		return true;

	 }

	function validateExtension($name, &$error)
	{
	 	//validate extension
		global $_HANDOUT;
		require_once($_HANDOUT->getPath('classes', 'mime'));

		if ($_HANDOUT->getCfg('user_all')) {
			return true;
		}

		if(!$name ) {
			return false;
		}

		$ext_array   = explode('|', strtolower( $_HANDOUT->getCfg('extensions')));

		if(!$ext_array ) {
			return true;
		}

		$valid_ext = preg_replace( "/^[.](.*)$/", "$1" , $ext_array );

		// Simple lookup first ...
		$extension = @strtolower( @substr( $name , strrpos($name,".")+1 ));
		if( $extension && in_array( $extension, $valid_ext ) ) {
			return true;
		}

		// Translate to mimetype for wider test...
		$extension=HANDOUT_MIME_Magic::MIMEToExt(HANDOUT_MIME_Magic::filenameToMIME($name));

		if( in_array( $extension , $valid_ext ) ) {
			return true;
		}

		$error .= JText::_('COM_HANDOUT_FILETYPE')." &quot;".$extension."&quot; ".JText::_('COM_HANDOUT_NOTPERMITED');
		return false;
	}
}

?>