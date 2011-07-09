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

if (defined('_HANDOUT_token')) {
	return true;
} else {
	define('_HANDOUT_token', 1);
}

/**
 * Utility class to work with form tokens
 *
 * @example:
 * In a form:
 * <code>
 * <?php echo HANDOUT_token::render();?>
 * </code>
 * Where the form is submitted:
 * <code>
 * <?php HANDOUT_token::check() or die('Invalid Token'); ?>
 * </code>
 *
 * @static
 */
class HANDOUT_Token
{
	/**
	 * Generate new token and store it in the session
	 *
	 * @see render()
	 * @return	string	Token
	 */
	function get($forceNew = false)
	{
		static $token;

		if($forceNew || !isset($token))
		{
			@session_start();

			$token = md5(uniqid(rand(), TRUE));
			$_SESSION['handout.token'] = $token;
		}

		return $token;
	}

	/**
	 * Render the hidden input field with the token
	 *
	 * @return	string	Html
	 */
	function render()
	{
		return '<input type="hidden" name="'.HANDOUT_Token::get().'" value="1" />';
	}

	/**
	 * Check if a valid token was submitted
	 *
	 * @todo	When all forms are updated to fully use $_POST, so should this
	 *
	 * @return	boolean	True on success
	 */
	function check()
	{
		@session_start();

		if(!isset($_SESSION['handout.token']))
		{
			return false;
		}

		$token = $_SESSION['handout.token'];

		if(isset($_REQUEST[$token]) && $_REQUEST[$token])
		{
			return true;
		}

		return false;
	}


}