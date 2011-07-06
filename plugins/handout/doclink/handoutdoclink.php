<?php

 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: handoutdoclink.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	Improved by JoomDOC by Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined('_JEXEC') or die('Restricted access');

/**
 * plgButtonHandoutDocLink Class
 */
class plgButtonHandoutDocLink extends JPlugin
{

    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param   object $subject The object to observe
     * @param   array  $config  An array that holds the plugin configuration
     * @since 1.5
     */
    function plgButtonHandoutDocLink (& $subject, $config)
    {
        parent::__construct($subject, $config);
    }

    /**
     * Display the button
     */
    function onDisplay ($name)
    {
        $mainframe = &JFactory::getApplication();

        $doc = & JFactory::getDocument();

        $lang = JFactory::getLanguage();
        $lang->load('plg_editors-xtd_handoutdoclink', JPATH_ADMINISTRATOR);

        $doclink_url = JURI::root() . "plugins/editors-xtd/handoutdoclink";
        $HANDOUT_url = JURI::root() . "components/com_handout/";

        $style = ".button2-left .handoutdoclink {
                background:transparent url($doclink_url/images/btn_handoutdoclink.png) no-repeat scroll 100% 0pt;
                }";
        $doc->addStyleDeclaration($style);

        $js = 'media/com_handout/js/';

        $doc->addScript($js . 'doclink.js');
        $doc->addScript($js . 'dldialog.js');
        $doc->addScript($js . 'popup.js');
        $doc->addScript($js . 'dlutils.js');

        $button = new JObject();
        $button->set('modal', true);
        $button->set('text', JText::_('PLG_HANDOUT_DOCLINK'));
        $button->set('name', 'handoutdoclink');
        $button->set('link', 'index.php?option=com_handout&task=doclink&e_name=' . $name);
        $button->set('options', "{handler: 'iframe', size: {x: 570, y: 510}}");

        return $button;
    }
}
?>
