<?php
/**
 * @package 	Handout Notify
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

class NotifyInfo
{
	public $date;
	public $time;
	public $os;
	public $browser;
	public function __construct()
	{
		$this->date	= date('Y-m-d');
		$this->time	= date('H:i:s');
		jimport('joomla.environment.browser');
		$browser 		= JBrowser::getInstance($_SERVER['HTTP_USER_AGENT']);
		$this->os 		= $browser->getPlatform();
		$this->browser 	= $browser->getBrowser();
	}
}