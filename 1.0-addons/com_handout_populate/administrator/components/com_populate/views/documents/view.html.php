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
require_once JPATH_COMPONENT.DS.'models'.DS.'categories.php';
JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers');

class PopulateViewDocuments extends JView
{
	public function display($tpl = null)
	{

		JToolBarHelper::title('Handout - Populate');
		JToolbarHelper::custom( "assign", "publish.png", "publish_f2.png", "Import", false );


        // config
		$apConfig   = TablePopulateConf::getInstance();
        $apParams   = TablePopulateParams::getInstance();

        // get files from model
        $model = $this->getModel('documents');
        $model->setState('handoutpath', $apParams->handoutpath);
        $model->setState('skipfiles', $apConfig->skipfiles);
        $model->setState('orphansonly', $apConfig->orphansonly);

        $this->assignRef('files', $model->getData());


        $this->setModel(new PopulateModelCategories);
		$this->assignRef('categories',	$this->getModel('categories')->getData());

		$this->assignRef('params', $apParams);

		parent::display($tpl);
	}
}