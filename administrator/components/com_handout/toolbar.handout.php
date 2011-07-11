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

$app = JFactory::getApplication();
require_once $app->getPath('toolbar_html');
require_once $app->getPath('toolbar_default');

require_once dirname(__FILE__) . '/toolbar.handout.class.php';

global $section;

if ($task == "cpanel") {
	TOOLBAR_handout::CPANEL_MENU ();
} else {
	switch ($section) {
		case "categories" : {
				switch ($task) {
					case "new":
					case "edit":
						TOOLBAR_handout::EDIT_CATEGORY_MENU ();
						break;

					case "show" :
					default :
						TOOLBAR_handout::CATEGORIES_MENU ();
				}
			}
			break;

		case "documents" : {
				switch ($task) {
					case "new":
					case "edit":
						TOOLBAR_handout::NEW_DOCUMENT_MENU();
						break;
					case "move_form":
						TOOLBAR_handout::MOVE_DOCUMENT_MENU();
						break;
					case "copy_form":
						TOOLBAR_handout::COPY_DOCUMENT_MENU();
						break;
					case "show":
					default:
						TOOLBAR_handout::DOCUMENTS_MENU();
				}
			}
			break;

		case "files" : {
				switch ($task) {
					case "new":
						TOOLBAR_handout::NEW_DOCUMENT_MENU();
						break;
					case "upload":
						TOOLBAR_handout::UPLOAD_FILE_MENU();
						break;
					case "show":
					default:
						TOOLBAR_handout::FILES_MENU();
						break;
				}
			}
			break;

		case "groups" : {
				switch ($task) {
					case "emailgroup":
						TOOLBAR_handout::EMAIL_GROUPS_MENU();
						break;
					case "new":
					case "edit":
						TOOLBAR_handout::EDIT_GROUPS_MENU();
						break;
					case "show":
					default:
						TOOLBAR_handout::GROUPS_MENU();
				}
			}
			break;

		case "licenses" : {
				switch ($task) {
					case "new":
					case "edit":
						TOOLBAR_handout::EDIT_LICENSES_MENU();
						break;
					case "show":
					default:
						TOOLBAR_handout::LICENSES_MENU();
				}
			}
			break;

		case "codes" : {
				switch ($task) {
					case "new":
					case "edit":
						TOOLBAR_handout::EDIT_CODES_MENU();
						break;
					case "show":
					default:
						TOOLBAR_handout::CODES_MENU();
				}
			}
			break;

		case "logs" : {
				switch ($task) {
					case "show":
					default:
						TOOLBAR_handout::LOGS_MENU();
				}
			}
			break;

		case "config" : {
				switch ($task) {
					case "show":
					default:
						TOOLBAR_handout::CONFIG_MENU ();
				}
			}
			break;

		case "cleardata":
			TOOLBAR_handout::CLEARDATA_MENU ();
			break;

		case "handout" :
		default : {
				switch ($task) {
					case "stats":
						TOOLBAR_handout::STATS_MENU ();
						break;

					case "cpanel":
					default:
						TOOLBAR_handout::CPANEL_MENU ();
						break;
				}
			}
	}
}

