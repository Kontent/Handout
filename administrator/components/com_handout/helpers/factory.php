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
class HandoutFactory
{

    function getHandout ()
    {
        $app = &JFactory::getApplication();
        static $instance;
        if (! is_object($instance)) {
            $instance = new HandoutMainFrame();
        }
        if ($app->isSite()) {
            $lang = &JFactory::getLanguage();
            $lang->load('com_handout');
        }
        return $instance;
    }

    function getHandoutUser ()
    {
        static $instance;
        if (! is_object($instance)) {
            $handout = HandoutFactory::getHandout();
            $instance = $handout->getUser();
        }
        return $instance;
    }

    function getPathName ($p_path, $p_addtrailingslash = true)
    {
        jimport('joomla.filesystem.path');
        $path = JPath::clean($p_path);
        if ($p_addtrailingslash) {
            $path = rtrim($path, DS) . DS;
        }
        return $path;
    }

    function getToolTip ($tooltip, $title = '', $width = '', $image = 'tooltip.png', $text = '', $href = '', $link = 1)
    {
        // Initialize the tooltipsif required
        static $init;
        if (! $init) {
            JHTML::_('behavior.tooltip');
            $init = true;
        }

        return JHTML::_('tooltip', $tooltip, $title, $image, $text, $href, $link);
    }

    function getFormatDate ($date = 'now', $format = null, $offset = null)
    {

        if (! $format) {
            $format = JText::_('DATE_FORMAT_LC1');
        }

        return JHTML::_('date', $date, $format, $offset);
    }

    function getImageCheckAdmin ($file, $directory = '/images/', $param = NULL, $param_directory = '/images/', $alt = NULL, $name = NULL, $type = 1, $align = 'middle')
    {
        $attribs = array('align' => $align);
        return JHTML::_('image.administrator', $file, $directory, $param, $param_directory, $alt, $attribs, $type);
    }

    function getStripslashes (&$value)
    {
        $ret = '';
        if (is_string($value)) {
            $ret = stripslashes($value);
        } else {
            if (is_array($value)) {
                $ret = array();
                foreach ($value as $key => $val) {
                    $ret[$key] = HandoutFactory::getStripslashes($val);
                }
            } else {
                $ret = $value;
            }
        }
        return $ret;
    }
}
?>