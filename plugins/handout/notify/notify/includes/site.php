<?php
/**
 * @package 	Handout Notify
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

class NotifySite
{
	public $name;
	public $url;
	public function __construct()
	 {
		$app = JFactory::getApplication();
		$this->name	= $app->getCfg('sitename');
		$this->url	= JURI::root();
	 }
}