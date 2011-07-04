<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: mod_handout_approval.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$docs = modHandoutApprovalHelper::getDocs($params);
require(JModuleHelper::getLayoutPath('mod_handout_approval'));

?>
