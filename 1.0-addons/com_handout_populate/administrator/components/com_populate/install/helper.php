<?php
/**
 * @category	HandoutPopulate
 * @package		HandoutPopulate
 * @copyright	Copyright (C) 2011 Kontent Design. All rights reserved.
 * @copyright	Copyright (C) 2003 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link	 	http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

class PopulateInstallHelper
{
	public static function logo()
	{

		?><br />
		<table>
			<tr>
				<th>
			   	  <img border="0" src="http://ping.joomlatools.eu/?<?php echo PopulateInstallHelper::getInfo()?>" />
				  <a href='index.php?option=com_populate&view=config'><img border="0" alt="Handout" src="<?php echo JURI::root(0)?>/administrator/components/com_handout/images/handout_logo.png" /></a>
				</th>
			</tr>
		</table><?php
	}

	public static function config()
	{
		$db = JFactory::getDBO();
		$db->setQuery('SELECT COUNT(*) FROM #__populate_conf');
		if(!$db->loadResult())
		{
			$query = "INSERT INTO `#__populate_conf` (
				`id` , `skipfiles` , `docdescription` ,
				`published` , `approved` , `docthumbnail` ,
				`doclicense_id` , `doclicense_display` , `docmaintainedby` ,
				`doclastupdateby` , `docsubmittedby` , `docowner` ,
				`docurl` , `access` , `attribs`,
				`stripextension`, `orphansonly`, `nicetitle`, `password` ,
				`catid`, `usefiletime`  )
				VALUES ('1', '.|..|.htaccess|index.php|index.htm|index.html', '',
				'0', '1', NULL ,
				NULL , '0', '-1' ,
				NULL , NULL , '0' ,
				'' , '0', '',
				'1', '1', '1', '', '0', '0' );";
			$db->setQuery($query);
			$db->query();
		}
	}

	public static function getInfo()
	{
		$db = JFactory::getDBO();
		$version = new JVersion();

		$info = array(
			'ext'	=> 'Populate',
			'v'		=> '1.5.2',
			'up'	=> 'unknown',
			'cms'	=> $version->PRODUCT.' '.$version->RELEASE .'.'. $version->DEV_LEVEL,
			'k'		=> class_exists('Koowa') &&  method_exists('Koowa', 'getVersion') ? Koowa::getVersion() : 0,
			'p' 	=> phpversion(),
			'db'	=> $db->getVersion(),
		);
		return http_build_query($info);

	}
}
