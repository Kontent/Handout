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

if (defined('_HANDOUT_FILE')) {
	return true;
}
else {
	define('_HANDOUT_FILE', 1);
}

$_HANDOUT = &HandoutFactory::getHandout();

require_once $_HANDOUT->getPath('classes', 'mime');
require_once $_HANDOUT->getPath('includes', 'defines');
// define JText::_('COM_HANDOUT_VALIDATE_')xxxx

class HANDOUT_File
{
	/**
	 * @access public
	 * @var string
	 */
	var $path = null;
	/**
	 * @access public
	 * @var string
	 */
	var $name = null;
	/**
	 * @access public
	 * @var string
	 */
	var $mime = null;
	/**
	 * @access public
	 * @var string
	 */
	var $ext = null;

	/**
	 * @access public
	 * @var string
	 */
	var $size = null;

	/**
	 * @access public
	 * @var string
	 */
	var $date = null;

	/**
	 * @access private
	 * @var string
	 */
	var $_err = null;

	/**
	 * @access private
	 * @var boolean
	 */
	var $_isLink;

	function HANDOUT_File($name, $path)
	{
		$path = &HandoutFactory::getPathName($path);
		if (!is_dir($path)) {
			$path = dirname($path);
			// Make sure there's a trailing slash in the path
			$path = &HandoutFactory::getPathName($path);
		}

		$this->name = trim($name);
		$this->path = $path;

		if (strcasecmp(substr($this->name, 0, COM_HANDOUT_DOCUMENT_LINK_LNG), COM_HANDOUT_DOCUMENT_LINK) == 0) {
			$this->_isLink = true;
			$this->size = 0;
			$this->mime = 'link';
		}
		else {
			$this->_isLink = false;
			$this->size = @filesize($this->path . $this->name);
			$this->mime = HANDOUT_MIME_Magic::filenameToMIME($this->name, false);
		}

		$this->ext = $this->getExtension();
	}

	/**
	 *	Downloads a file from the server
	 *
	 *	@desc This is the function handling files downloading using HTTP protocol
	 *	@param void
	 *	@return void
	 */

	function download($inline = false)
	{
		// Fix [3164]
		while (@ob_end_clean())
			;

		if ($this->_isLink) {
			header("Location: " . substr($this->name, 6));
			return;
		}

		$fsize = @filesize($this->path . $this->name);
		$mod_date = date('r', filemtime($this->path . $this->name));

		$cont_dis = $inline ? 'inline' : 'attachment';

		// required for IE, otherwise Content-disposition is ignored
		if (ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}

		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");

		header("Content-Transfer-Encoding: binary");
		header(
				'Content-Disposition:' . $cont_dis . ';' . ' filename="' . $this->name . '";' . ' modification-date="' . $mod_date . '";' . ' size=' . $fsize
						. ';'); //RFC2183
		header("Content-Type: " . $this->mime); // MIME type
		header("Content-Length: " . $fsize);

		if (!ini_get('safe_mode')) { // set_time_limit doesn't work in safe mode
			@set_time_limit(0);
		}

		// No encoding - we aren't using compression... (RFC1945)
		//header("Content-Encoding: none");
		//header("Vary: none");

		$this->readfile_chunked($this->path . $this->name);
		// The caller MUST 'die();'
	}

	function readfile_chunked($filename, $retbytes = true)
	{
		$chunksize = 1 * (1024 * 1024); // how many bytes per chunk
		$buffer = '';
		$cnt = 0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
			return false;
		}
		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			@ob_flush();
			flush();
			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}
		$status = fclose($handle);
		if ($retbytes && $status) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}

	function exists()
	{
		if ($this->_isLink) {
			return true;
		}
		return file_exists($this->path . "/" . $this->name);
	}

	function isLink()
	{
		return $this->_isLink;
	}

	/**
	 *	Get file size
	 *
	 *	@desc Gets the file size and convert it to friendly format
	 *	@param void
	 *	@return string Returns filesize in a friendly format.
	 */

	function getSize()
	{
		if ($this->_isLink) {
			return 'Link';
		}
		$kb = 1024;
		$mb = 1024 * $kb;
		$gb = 1024 * $mb;
		$tb = 1024 * $gb;

		$size = $this->size;

		if ($size) {
			if ($size < $kb) {
				$file_size = $size . ' ' . JText::_('COM_HANDOUT_BYTES');
			}
			elseif ($size < $mb) {
				$final = round($size / $kb, 2);
				$file_size = $final . ' ' . JText::_('COM_HANDOUT_KB');
			}
			elseif ($size < $gb) {
				$final = round($size / $mb, 2);
				$file_size = $final . ' ' . JText::_('COM_HANDOUT_MB');
			}
			elseif ($size < $tb) {
				$final = round($size / $gb, 2);
				$file_size = $final . ' ' . JText::_('COM_HANDOUT_GB');
			}
			else {
				$final = round($size / $tb, 2);
				$file_size = $final . ' ' . JText::_('COM_HANDOUT_TB');
			}
		}
		else {
			if ($size == 0) {
				$file_size = JText::_('COM_HANDOUT_EMPTY');
			}
			else {
				$file_size = JText::_('COM_HANDOUT_ERROR');
			}
		}
		return $file_size;
	}

	/**
	 *	@desc Gets the extension of a file
	 *	@return string The file extension
	 */

	function getExtension()
	{
		/*
		 * Fix for http://www.joomlatools.org/index.php?option=com_simpleboard&Itemid=0&func=view&id=13805&catid=506
		 */
		//if( $this->_isLink )
		//	return "lnk";

		$dotpos = strrpos($this->name, ".");
		if ($dotpos < 1)
			return "unk";

		return substr($this->name, $dotpos + 1);
	}

	function getDate($type = 'm')
	{
		$app = JFactory::getApplication();

		if ($this->_isLink) {
			return "";
		}

		$date = '';

		switch ($type) {
			case 'm':
				$date = filemtime($this->path . $this->name);
				break;
			case 'a':
				$date = fileatime($this->path . $this->name);
				break;
			case 'c':
				$date = filectime($this->path . $this->name);
				break;
		}

		return strftime(JText::_('COM_HANDOUT_DATEFORMAT_LONG'), $date + ($app->getCfg('offset') * 60 * 60));

	}

	function remove()
	{
		@unlink($this->path . $this->name);
		return !$this->exists;
	}
}

class HANDOUT_FileUpload
{
	/**
	 * @access public
	 * @var string
	 */
	var $max_file_size = null;
	/**
	 * @access public
	 * @var string
	 */
	var $ext_array = null;
	/**
	 * @access private
	 * @var string
	 */
	var $_err = null;
	/**
	 * @access private
	 * @var string
	 */
	var $fname_blank;
	/**
	 * @access private
	 * @var string
	 */
	var $fname_reject;
	/**
	 * @access private
	 * @var string
	 */
	var $fname_lc;
	/**
	 * @access private
	 * @var array
	 */
	var $proto_accept = null;
	/**
	 * @access private
	 * @var array
	 */
	var $proto_reject;

	function HANDOUT_FileUpload()
	{
		global $_HANDOUT;
		if (!is_object($_HANDOUT)) {
			$_HANDOUT = &HandoutFactory::getHandout();
		}
		$this->max_file_size = 0 + trim($_HANDOUT->getCfg('maxAllowed'));
		$this->ext_array = explode('|', strtolower($_HANDOUT->getCfg('extensions')));
		$this->_err = '';

		$this->fname_blank = $_HANDOUT->getCfg('fname_blank');
		$this->fname_reject = $_HANDOUT->getCfg('fname_reject');
		$this->fname_lc = $_HANDOUT->getCfg('fname_lc');
		$this->proto_reject = array('file', 'php', 'zlib', 'asp', 'pl', 'compress.zlib', 'compress.bzip2', 'ogg');
		$this->proto_accept = array('http', 'https', 'ftp');
	}

	/**
	 *	Uploads a file using the HTTP protocol
	 *
	 *	@desc Uploads a file using HTTP.
	 *	@param void
	 *	@return boolean Returns true if succeed and false if not. Sets $this->_err with false.
	 */

	function uploadHTTP(&$file, $path, $validate = COM_HANDOUT_VALIDATE_ALL)
	{
		$name = $file['name'];
		$errorcode = $file['error'] ? $file['error'] : 0;
		$temp_name = trim($file['tmp_name']);

		if (($validate & COM_HANDOUT_VALIDATE_PATH && !$this->validatePath($path))
				|| ($validate & COM_HANDOUT_VALIDATE_NAME && !$this->validateName($name))
				|| ($validate & COM_HANDOUT_VALIDATE_EXISTS && !$this->validateExists($name, $path))
				|| ($validate & COM_HANDOUT_VALIDATE_SIZE && !$this->validateSize($temp_name))
				|| ($validate & COM_HANDOUT_VALIDATE_EXT && !$this->validateExt($name))) {

			return false;
		}

		if ($errorcode == 0) {
			return $this->_upload($name, $temp_name, $path);
		}

		// Finish by handling errors
		switch ($errorcode) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$this->_err = JText::_('COM_HANDOUT_SIZEEXCEEDS');
				break;

			case UPLOAD_ERR_PARTIAL:
				$this->_err = JText::_('COM_HANDOUT_ONLYPARTIAL');
				break;

			case UPLOAD_ERR_NO_FILE:
				$this->_err = JText::_('COM_HANDOUT_NOUPLOADED');
				break;

			default:
				$this->_err = JText::_('COM_HANDOUT_TRANSFERERROR') . " $errorcode";
				break;
		}
		return false;
	}

	function _upload($name, $temp_name, $path)
	{
		JPath::check($path);

		if (!is_dir($path)) {
			$this->_err = JText::sprintf(
				'COM_HANDOUT_ERROR_PATH_NOT_FOUND',
				str_replace(JUri::root(), '', JPath::clean($path))
			);
		}

		if (is_uploaded_file($temp_name)) {
			if (move_uploaded_file($temp_name, $path . '/' . $name)) {
				$file = new HANDOUT_File($name, $path);
				return $file;
			}
			else {
				$this->_err = JText::_('COM_HANDOUT_DIRPROBLEM_MOVE') . " ";
			}
		}
		else {
			$this->_err = JText::_('COM_HANDOUT_DIRPROBLEM') . " ";
		}
		return false;
	}

	/**
	 *	transfer a file using HTTP protocol between servers
	 *
	 *	@desc Member function handling file transfer using HTTP protocol from a foreign server to local server
	 *	@param void
	 *	@return boolean Returns false if file could not be transfered
	 *					and true if it does. Sets _error if false.
	 */

	function uploadURL($url, $path, $validate = COM_HANDOUT_VALIDATE_ALL, $name = null)
	{

		$errid = null;
		$errmsg = null;

		if (!$parsedurl = parse_url($url)) {
			$this->_err = 'Malformed url: ' . $url;
			return false;
		}

		if (!$name) {
			$name = basename($parsedurl["path"]);
		}

		if (($validate & COM_HANDOUT_VALIDATE_PATH && !$this->validatePath($path))
				|| ($validate & COM_HANDOUT_VALIDATE_NAME && !$this->validateName($name))
				|| ($validate & COM_HANDOUT_VALIDATE_EXISTS && !$this->validateExists($name, $path))
				|| ($validate & COM_HANDOUT_VALIDATE_EXT && !$this->validateExt($name))
				|| ($validate & COM_HANDOUT_VALIDATE_PROTO && !$this->validateProtocol($parsedurl['scheme']))) {
			return false;
		}

		// Open the URL source using PHP fopen schema.
		$bufferhandle = @fopen($url, 'rb'); //Binary read-mode
		if (!$bufferhandle) {
			$this->_err = JText::_('COM_HANDOUT_COULDNOTCONNECT') . " " . @$parsedurl['host'];
			return false;
		}

		// Open the local file and copy contents
		$file_to_open = $path . $name;
		if ($fh = fopen($file_to_open, "w")) {
			$filesize = 0;
			while (!feof($bufferhandle)) {
				$buffer = fread($bufferhandle, 40960);
				$bsize = strlen($buffer);
				if ($validate & COM_HANDOUT_VALIDATE_SIZE) {
					if ($filesize + $bsize > $this->max_file_size) {
						fclose($fh);
						fclose($bufferhandle);
						unlink($file_to_open);
						$this->_err .= JText::_('COM_HANDOUT_SIZEEXCEEDS');
						return (false);
					}
				}
				fwrite($fh, $buffer);
				$filesize += $bsize;
			}
			fclose($fh);
			fclose($bufferhandle);

			$file = new HANDOUT_File($name, $path);
			return $file;
		}
		else {
			$this->_err = JText::_('COM_HANDOUT_COULDNOTOPEN') . " $file_to_open , $path , $name";
			return false;
		}
	}

	/**
	 *	Check a file for linking
	 *
	 *	@desc Member function handling link testing using internet protocol from a foreign server to local server
	 *	@param void
	 *	@return boolean Returns false if file could not be transfered
	 *					and true if it does. Sets _error if false.
	 */
	function uploadLINK($url, $validate = COM_HANDOUT_VALIDATE_ALL)
	{
		if (!$parsedurl = parse_url($url)) {
			$this->_err = 'Malformed url: ' . $url;
			return false;
		}

		if ($validate & COM_HANDOUT_VALIDATE_PROTO && !$this->validateProtocol($parsedurl['scheme'])) {
			return false;
		}

		if ($parsedurl['host'] == '') {
			$this->_err = JText::_('COM_HANDOUT_ENTRY_DOCLINK_HOST');
			return false;
		}

		/* Removed test, user is responsible for submitting existing urls
		 *
		// Open the URL source using PHP fopen schema. this is a test ONLY!
		$bufferhandle = fopen( $url , 'rb' );	//Binary read-mode
		if( ! $bufferhandle ){
		    $this->_err = JText::_('COM_HANDOUT_COULDNOTCONNECT')." " . @$parsedurl['host'];
		    return false;
		}
		fclose( $bufferhandle );
		 */

		return true;
	}

	/**
	 *	Validate file extension
	 *
	 *	@desc This is the function handling the file extension validation when uploading.
	 *	@param void
	 *	@return boolean Returns true if extension is valid and false if not. Sets $this->err with error message if false.
	 */

	function validateExt($name)
	{
		if (!$name) {
			return false;
		}
		if (!$this->ext_array) {
			return true;
		}

		$valid_ext = preg_replace("/^[.](.*)$/", "$1", $this->ext_array);

		// Simple lookup first ...
		$extension = @strtolower(@substr($name, strrpos($name, ".") + 1));
		if ($extension && in_array($extension, $valid_ext)) {
			return true;
		}

		// Translate to mimetype for wider test...
		$extension = HANDOUT_MIME_Magic::MIMEToExt(HANDOUT_MIME_Magic::filenameToMIME($name));

		if (in_array($extension, $valid_ext)) {

			return true;
		}

		$this->_err .= JText::_('COM_HANDOUT_FILETYPE') . " &quot;" . $extension . "&quot; " . JText::_('COM_HANDOUT_NOTPERMITED');

		return false;
	}

	/**
	 *	Validate file size
	 *
	 *	@desc This is the function handling the file size validation when uploading.
	 *	@param void
	 *	@return boolean Returns true if size is valid and false if not. Sets $this->err with error message if false.
	 */

	function validateSize($temp_name)
	{
		if ($temp_name) {
			$size = filesize($temp_name);
			if ($size <= $this->max_file_size && $size > 0) {
				return true;
			}
		}

		$this->_err .= JText::_('COM_HANDOUT_SIZEEXCEEDS');
		return false;
	}

	function validatePath($path)
	{
		if ($path) {
			$path = &HandoutFactory::getPathName($path);
			if (!is_dir($path)) {
				$path = dirname($path);
			}

			if (@substr($path, -1) != "/") {
				$path = $path . "/";
			}

			$handle = @opendir($path);

			if ($handle) {
				closedir($handle);
			}
			else {
				$path = false;
			}
		}
		else {
			$path = false;
		}

		if (!$path) {
			$this->_err = JText::_('COM_HANDOUT_DIRPROBLEM') . ": $path";
		}

		return $path;
	}

	/**
	 *	Check file existence
	 *
	 *	@desc This is the function handling the file existence validation when uploading.
	 *	@param file name
	 *	@return boolean Returns true if file exists and false if not. Sets $this->err file exists.
	 */

	function validateExists($name, $path)
	{
		global $_HANDOUT;
		if (!$_HANDOUT->getCfg('overwrite') && file_exists($path . "/" . $name)) {
			$this->_err .= JText::_('COM_HANDOUT_FILE') . " &quot;" . $name . "&quot; " . JText::_('COM_HANDOUT_ALREADYEXISTS');
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 *	Validate protocol passed.
	 *	We never want 'file' to be used as this could expose server
	 *	readable files to the outside world.
	 *
	 *	@desc This function confirms the protocol is supported
	 *	@param pointer to filename
	 *	@return boolean Returns true if filename is supported, else false.
	 */
	function validateProtocol($proto)
	{
		$proto = strtolower($proto);
		if (!$proto)
			return true;

		if (($this->proto_reject && in_array($proto, $this->proto_reject)) || ($this->proto_accept && !in_array($proto, $this->proto_accept))) {
			$this->_err = JText::_('COM_HANDOUT_PROTOCOL') . " &quot;" . $proto . "&quot; " . JText::_('COM_HANDOUT_NOTSUPPORTED');
			return false;
		}
		return true;
	}
	/**
	 *	Validate filename passed.
	 *
	 *	@desc This is the function handling the file name
	 *	@param pointer to filename
	 *	@return boolean Returns true if filename is good, else false.
	 */

	function validateName(&$name)
	{
		$name = trim($name);
		if (!$name) {
			$this->_err = JText::_('COM_HANDOUT_NOFILENAME');
			return false;
		}

		if ($this->fname_lc) {
			$name = strtolower($name);
		}

		if (strchr($name, " ")) {
			switch ($this->fname_blank) {
				case 0: // Accept
				default:
					break;

				case 1: // REJECT
					$this->_err .= JText::_('COM_HANDOUT_FILENAME') . " &quot;" . $name . "&quot; " . JText::_('COM_HANDOUT_CONTAINBLANKS');
					return false;

				case 2: // convert to underscore
					$name = preg_replace("/\s/", '_', $name);
					break;

				case 3: // convert to dash
					$name = preg_replace("/\s/", '-', $name);
					break;

				case 4: // REMOVE
					$name = preg_replace("/\s/", '', $name);
					break;
			}
		}

		if (($this->fname_reject && preg_match("/^(" . $this->fname_reject . ")$/i", $name)) OR preg_match("/^(" . COM_HANDOUT_FNAME_REJECT . ")$/i", $name)) {
			$this->_err .= "&quot;" . $name . "&quot; " . JText::_('COM_HANDOUT_ISNOTVALID');
			return false;
		}

		return true;
	}
}

class HANDOUT_Folder
{
	var $path = null;

	function HANDOUT_Folder($path)
	{
		$this->path = $path;
	}

	/**
	 * Utility function to read the files in a directory
	 * @param string The file system path
	 * @param string A filter for the names
	 */
	function getFiles($match_filter = null, $ignore_filter = null, $filter = null)
	{
		global $_HANDOUT;

		// Don't show the 'ignore files'. They are...er, magic.
		if (empty($ignore_filter)) {
			$ignore_filter = $_HANDOUT->getCfg('fname_reject');
		}

		$arr = array();
		if (!@is_dir($this->path)) {
			return $arr;
		}
		$handle = @opendir($this->path);

		while ($file = @readdir($handle)) {
			if (substr($file, 0, 1) == '.')
				continue;
			if (@is_dir($this->path . '/' . $file))
				continue;
			if (!empty($ignore_filter) && preg_match("/^" . $ignore_filter . '/', $file))
				continue;
			if (preg_match("/^" . COM_HANDOUT_FNAME_REJECT . "^/", $file))
				continue;

			if (!empty($match_filter) && !preg_match("/^" . $match_filter . '/', $file))
				continue;

			//check for xml files with two periods . in the title
			//for example: template.xml.bak, which we want to avoid
			if ($filter == ".xml") {
				$file_count = explode(".", $file);
				if (count($file_count) == "2") {
					$arr[] = new HANDOUT_File(trim($file), $this->path);
				}
			}
			else {
				$arr[] = new HANDOUT_File(trim($file), $this->path);
			}
		}
		@closedir($handle);
		asort($arr);
		return $arr;
	}
}
