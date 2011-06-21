<?php

  /**
 * @version 	$Id: config.php
 * @package 	Handout Notify
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/ 
 
defined('_JEXEC') or die('Restricted access');

class NotifyConfig extends JParameter
{
	public function NotifyConfig()
	{
		parent::__construct( JPluginHelper::getPlugin('handout', 'notify')->params );
	}

	/**
	 * @static
	 */
	public function getInstance()
	{
		static $instance;
		if( !isset($instance)) {
			$instance = new NotifyConfig();
		}
		return $instance;
	}
	public function getRecipients()
	{
		$cc = $this->get( 'cc', false );
		if($cc)
		{
			if (strstr($cc, "|")) {	//more than one recipient	
				$string = explode( '|', $cc );
			}
			else { // no pipe - single recipient
				$string = array($cc);
			}	
			return $string;
		}
		return false;
	}
	/**
	 * Find out if the specified action should be performed in the current
	 * application (site or admin)
	 *
	 * @param 	string 	$action
	 * @return 	boolean True if the action should be performed
	 */
	public function doAction( $action = '' )
	{
		$app = JFactory::getApplication()->isAdmin() ? 'admin' : 'site';
		return ( $this->get('on'.$action.'_'.$app, 0)); // onedit_site, onedit_admin etc...
	}
}