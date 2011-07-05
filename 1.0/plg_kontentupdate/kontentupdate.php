<?php
/**
 * Kontent Upgrade Alert Module
 * @version 	$Id: kontentupdate.php
 * @package 	Kontent Upgrade Alert
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
**/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class  plgSystemKontentUpdate extends JPlugin{

	public function plgSystemKontentUpdate(&$subject, $config){
		parent::__construct($subject, $config);
		$this->mainframe = JFactory::getApplication();
		// Load javascript
		$this->loadPlugin();
	}

	function onAfterDispatch(){
		if($this->loadPlugin()){
			JHTML::_('behavior.modal');
		}
	}

	public function onAfterRender(){
		if($this->loadPlugin()){
			$this->renderStatus();
		}
	}

	public function loadPlugin(){
		// Load only for backend
		if($this->mainframe->isAdmin()){
			return true;
		}
		return false;
	}

	function setParamsLastDate(){
		$date = date('Y-m-d');
		$sql = "update #__plugins set params='lastcheck=".$date."' where element='kontentupdate'";
		$db =& JFactory::getDBO();
		$db->setQuery($sql);
		$db->query();
	}

	public function renderStatus(){
		$button	= $this->getButton();
		$this->setParamsLastDate();
		$html	= JResponse::getBody();

		preg_match('/div id="module-status"(.*)>/msU', $html, $finds);
		$replace_string = '<div id="module-status">';
		if(isset($finds) && is_array($finds) && count($finds) > 0){
			if(trim($finds["0"]) != ""){
				$replace_string = "<".trim($finds["0"]);
			}
		}

		$html	= str_replace($replace_string, $replace_string.$button, $html);
		JResponse::setBody($html);
	}

	function existComponent($component){
		$db =& JFactory::getDBO();
		$sql = "select count(*) from #__components where link = 'option=".$component."'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		if($result > 0){
			return true;
		}
		return false;
	}

	public function getButton(){
		$button		= '';
		$updateText	= 'Kontent Extensions are updated';
		$list_all_components = array("com_handout"=>"handout.xml",
									 "com_podcastpro"=>"podcastpro.xml",
									 "mod_kontenttweet"=>"kontenttweet.xml",
									 "mod_kontentbox"=>"kontentbox.xml");
		$list_installed_components = array();

		$show_button = false;
		foreach($list_all_components as $key=>$value){
			if($this->existComponent($key)){
				$list_installed_components[$key] = $value;
				$show_button = true;
			}
		}

		if(count($list_installed_components) > 0 && $show_button){
			foreach($list_installed_components as $key=>$value){
				$latest_version	 = $this->getCurrentVersionData($key);
				$current_version = $this->getLocalVersionString($key, $value);
				if($show_button === true && trim($latest_version) != "" && trim($current_version) != ""){
					if(trim($current_version) != trim($latest_version)){
						$updateText	= 'Kontent Extensions Upgrade Alert';
						$button	= '<span class="kontentupdate" style="padding-left:25px; background:#F0F0F0 url(\'../plugins/system/kontentupdate/kontent_icon.png\') no-repeat scroll 3px 3px;"><a style="color:red !important;" rel="{handler: \'iframe\', size: {x: 850, y: 290}}"  class="modal"  href="'.JURI::root()."plugins/system/kontentupdate/editversions.php".'">'.JText::_($updateText).'</a></span>';
						break;
					}
				}
				$latest_version = "";
				$current_version = "";
			}
		}
		return $button;
	}

	public function getCurrentVersionData($component){
		$version = "";
		$data = 'update.kontentdesign.com/latest_version.xml';
		$ch = @curl_init($data);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		@curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		$version = @curl_exec($ch);
		if(isset($version) && trim($version) != ""){
			$pattern = "/".$component."=(.*);/msU";
			preg_match($pattern, $version, $result);
			if(is_array($result) && count($result) > 0){
				$version = trim($result["1"]);
			}
			return $version;
		}
		return false;
	}

	public function getLocalVersionString($component, $xml_file){
		$version = '';
		$parser			=& JFactory::getXMLParser('Simple');
		$xml			= JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$component.DS.$xml_file;
		$parser->loadFile($xml);
		$document =& $parser->document;

		if($document){
			$element =& $document->getElementByPath('version');
			$version = $element->data();
		}
		return $version;
	}
}

?>