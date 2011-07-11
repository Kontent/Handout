<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @subpackage	com_handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * @package 	Handout
 * @subpackage	com_handout
 */
class HandoutVersion
{
	/**
	 * Extension name string.
	 *
	 * @var		string
	 */
	const EXTENSION	= 'com_handout';

	/**
	 * Major.Minor version string.
	 *
	 * @var		string
	 */
	const VERSION	= '1.0';

	/**
	 * Maintenance version string.
	 *
	 * @var		string
	 */
	const SUBVERSION= '0';

	/**
	 * Version status string.
	 *
	 * @var		string
	 */
	const STATUS	= '';

	/**
	 * Version release time stamp.
	 *
	 * @var		string
	 */
	const DATE		= '2011-07-09 00:00:00';

	/**
	 * Source control revision string.
	 *
	 * @var		string
	 */
	const REVISION	= '0';

	/**
	 * Method to get the build number from the source control revision string.
	 *
	 * @return	integer	The version build number.
	 * @since	1.0
	 */
	public static function getBuild()
	{
		return intval(substr(self::REVISION, 11));
	}

	/**
	 * Gets the version number.
	 *
	 * @param	boolean	$build	Optionally show the build number.
	 * @param	boolean	$status	Optionally show the status string.
	 *
	 * @return	string
	 * @since	1.0.3
	 */
	public static function getVersion($build = false, $status = false)
	{
		$text = self::VERSION.'.'.self::SUBVERSION;

		if ($build) {
			$text .= ':'.self::getBuild();
		}

		if ($status) {
			$text .= ' '.self::STATUS;
		}

		return $text;
	}
}