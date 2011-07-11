<?php
/**
 * @category	HandoutPopulate
 * @package		HandoutPopulate
 * @copyright	Copyright (C) 2011 Kontent Design. All rights reserved.
 * @copyright	Copyright (C) 2003 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link	 	http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).DS.'helper.php';
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_populate'.DS. 'helpers'.DS.'handout.php';

function com_install()
{
	PopulateInstallHelper::config();
	PopulateInstallHelper::logo();
}
