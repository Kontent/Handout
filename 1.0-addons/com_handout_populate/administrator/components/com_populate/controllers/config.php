<?php
/**
 * @package		HandoutPopulate
 * @copyright	Copyright (C) 2011 Kontent Design. All rights reserved.
 * @copyright	Copyright (C) 2003 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
class PopulateControllerConfig extends JController
{
	public function save()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

        $database  = JFactory::getDBO();
        $apConfig =  TablePopulateConf::getInstance();

        // store it in the db
        if ( !$apConfig->save( $_POST ) ) {
            echo "<script> alert('"
                .$apConfig->getError()
                ."'); window.history.go(-1); </script>n";
            exit();
        }

        $this->setRedirect( "index.php?option=com_populate&view=config", "Configuration Saved" );
	}

	public function cancel()
	{
		$this->setRedirect( "index.php?option=com_populate", "Configuration Saved" );
	}
}