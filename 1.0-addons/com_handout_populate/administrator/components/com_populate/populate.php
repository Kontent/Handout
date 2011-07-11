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

require_once JPATH_COMPONENT.DS.'tables'.DS.'config.php';
require_once JPATH_COMPONENT.DS.'tables'.DS.'params.php';
require_once JPATH_COMPONENT.DS. 'helpers'.DS.'handout.php' ;

// auth
$user = & JFactory::getUser();
if (!$user->authorize( 'com_users', 'manage' )) {
	$app = JFactory::getApplication();
	$app->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}


// version
$app = JFactory::getApplication();
if(!PopulateDocman::isInstalled() || !PopulateDocman::checkVersion()) {
	$app->redirect('index.php', 'You need Handout 1.5 for Handout Populate to work. Please vist http://joomlahandout.com for information.');
	exit;
}

// view
$view = JRequest::getCmd('view', 'documents', 'get');
JRequest::setVar('view', $view); // set in case default is used


require_once JPATH_COMPONENT.DS.'controllers'.DS.$view.'.php';
$controllerName = 'PopulateController'.$view;
$controller		= new $controllerName;

// Perform the Request task
$controller->execute( JRequest::getCmd('task', 'display', 'post'));
$controller->redirect();



