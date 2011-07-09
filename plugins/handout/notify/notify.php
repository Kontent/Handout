<?php
/**
 * @package 	Handout Notify
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */

defined('_JEXEC') or die('Restricted access');

global $_HANDOUT;
if(!is_object($_HANDOUT)){
	$handoutBase = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_handout' . DS;
	require_once $handoutBase . 'helpers' . DS . 'factory.php';
	$_HANDOUT = &HandoutFactory::getHandout();
}

// requires
define('NOTIFY_PATH', dirname(__FILE__).DS.'notify');
require_once NOTIFY_PATH.DS.'includes'.DS.'config.php';

jimport( 'joomla.plugin.plugin' );
class plgHandoutNotify extends JPlugin
{
	public function __construct($subject, $config = array())
	{
		$this->loadLanguage('plg_handout_notify', JPATH_ADMINISTRATOR);
		parent::__construct($subject, $config = array());
	}

	public function onAfterUpload($params)
	{
		return $this->_notify($params);
	}

	public function onAfterEditDocument($params)
	{
		return $this->_notify($params);
	}

	public function onBeforeDownload($params)
	{
		return $this->_notify($params);
	}

	protected function _notify($params)
	{
		require_once NOTIFY_PATH.DS.'includes'.DS.'emailtemplate.php';

		$email = new NotifyEmailTemplate( $params );

		switch($params['process'])
		{
			case 'new document':
			case 'edit document':
				$action = 'edit';
				$actionlang = $email->user->name.' '.JText::_('PLG_HANDOUT_NOTIFY_HAS_EDITED_A_FILE');
				break;
			case 'upload':
				$action = 'upload';
				$actionlang = $email->user->name.' '.JText::_('PLG_HANDOUT_NOTIFY_HAS_UPLOADED_A_FILE');
				break;
			case 'download':
				$action = 'download';
				$actionlang = $email->user->name.' '.JText::_('PLG_HANDOUT_NOTIFY_HAS_DOWNLOADED_A_FILE');
				break;
		}

		$cfg =  NotifyConfig::getInstance();

		// is the action activated in the plugin params?
		if( ! $cfg->doAction($action) ) {
			return;
		}

		$email->action		= $action;
		$email->actionlang	= $actionlang;
		$email->setSubject($actionlang );
		$email->send();
	}
}
