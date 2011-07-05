<?php
/**
 * @version		$Id$
 * @category	HandoutPopulate
 * @package		HandoutPopulate
 * @copyright	Copyright (C) 2011 Kontent Design. All rights reserved.
 * @copyright	Copyright (C) 2003 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers');

class PopulateViewConfig extends JView
{
	public function display($tpl = null)
	{
		// preload
		global $_HANDOUT, $_DMUSER;
		$_HANDOUT = PopulateDocman::get();
		$_DMUSER = $_HANDOUT->getUser();

        require_once(PopulateDocman::get()->getPath('classes', 'html'));


        JToolBarHelper::title('Handout - Populate');
        JToolbarHelper::save( 'save' );
		JToolbarHelper::cancel( 'cancel' );

        $database = JFactory::getDBO();

        $this->assignRef('config', TablePopulateConf::getInstance());

 		$pw = md5(JFactory::getApplication()->getCfg('secret'));
 		$this->assignRef('pw', $pw );
		parent::display($tpl);
	}
}