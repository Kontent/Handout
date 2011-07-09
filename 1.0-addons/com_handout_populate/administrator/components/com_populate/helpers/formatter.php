<?php
/**
 * @category	HandoutPopulate
 * @package		HandoutPopulate
 * @copyright	Copyright (C) 2011 Kontent Design. All rights reserved.
 * @copyright	Copyright (C) 2003 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

class PopulateFormatter
{

    public static function stripExtension ($filename)
    {
        $pos = strrpos($filename, '.');
        if ($pos === false ) {
            return $filename;
        } else {
            return substr($filename, 0, $pos );
        }
    }

    public static function getNiceTitle ($title) {
        return ucwords( str_replace( '_', ' ', $title) );
    }
}