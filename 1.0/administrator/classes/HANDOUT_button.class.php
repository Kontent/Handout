<?php
/**
 * Handout - The Joomla Download Manager
 * @version 	$Id: handout_button.class.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );


if (defined('_HANDOUT_button')) {
    return true;
} else {
    define('_HANDOUT_button', 1);
}

require_once($_HANDOUT->getPath('classes', 'params'));

/**
 * @abstract
 */
class HANDOUT_Button extends JObject {
    /**
     * @abstract string
     */
	var $name;

    /**
     * @abstract string
     */
    var $text;

    /**
     * @abstract string
     */
    var $link;

    /**
     * @abstract HandoutParameters Object
     */
    var $params;

    /**
     * @constructor
     */
    function __construct($name, $text, $link = '#', $params = null) {
    	$this->name = $name;
        $this->text = $text;
        $this->link = $link;
        if(!is_object($params)) {
        	$this->params = new HandoutParameters('');
        } else {
        	$this->params = & $params;
        }
    }
}