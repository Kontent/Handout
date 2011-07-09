<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */

defined('_JEXEC') or die;

define ( 'JPATH_COMPONENT_HELPERS', JPATH_COMPONENT_SITE . DS . 'helpers' );
define ( 'JPATH_COMPONENT_AHELPERS', JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' );
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'handout.class.php';
require_once JPATH_COMPONENT_HELPERS . DS . 'helper.php';
require_once JPATH_COMPONENT_AHELPERS . DS . 'factory.php';
require_once JPATH_COMPONENT_SITE . DS . 'controller.php';

$handout = &HandoutFactory::getHandout ();

define ( 'C_HANDOUT_HTML', $handout->getPath ( 'classes', 'html' ) );
define ( 'C_HANDOUT_UTILS', $handout->getPath ( 'classes', 'utils' ) );
define ( 'C_HANDOUT_TOKEN', $handout->getPath ( 'classes', 'token' ) );
define ( 'C_HANDOUT_MODEL', $handout->getPath ( 'classes', 'model' ) );
define ( 'C_HANDOUT_PARAMS', $handout->getPath ( 'classes', 'params' ) );
define ( 'C_HANDOUT_PLUGINS', $handout->getPath ( 'classes', 'plugins' ) );
define ( 'C_HANDOUT_FILE', $handout->getPath ( 'classes', 'file' ) );

require_once C_HANDOUT_HTML;
require_once C_HANDOUT_UTILS;
require_once C_HANDOUT_TOKEN;
require_once C_HANDOUT_MODEL;
require_once C_HANDOUT_PARAMS;
require_once C_HANDOUT_PLUGINS;
require_once C_HANDOUT_FILE;

$controller = new HandoutController ( );
$controller->execute ( JRequest::getString ( 'task' ) );
$controller->redirect ();
