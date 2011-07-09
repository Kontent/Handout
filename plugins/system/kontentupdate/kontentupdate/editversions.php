<?php
/**
 * Kontent Upgrade Alert Module
 * @package 	Kontent Upgrade Alert
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
*/

define('_JEXEC', 1);
define('JPATH_BASE', substr(substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "plugins")),0,-1));
if (!isset($_SERVER["HTTP_REFERER"])) exit("Direct access not allowed.");
$mosConfig_absolute_path =substr(JPATH_BASE, 0, strpos(JPATH_BASE, "/administra"));
define( 'DS', DIRECTORY_SEPARATOR );
require_once JPATH_BASE .DS.'includes'.DS.'defines.php';
require_once JPATH_BASE .DS.'includes'.DS.'framework.php';
require_once JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'methods.php';
require_once JPATH_BASE .DS.'configuration.php';
require_once JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'base'.DS.'object.php';
require_once JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'database'.DS.'database.php';
require_once JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'database'.DS.'database'.DS.'mysql.php';
require_once JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'filesystem'.DS.'folder.php';
$jFolder=new JFolder();
$config = new JConfig();
$options = array ("host" => $config->host,"user" => $config->user,"password" => $config->password,"database" => $config->db,"prefix" => $config->dbprefix);
$database = new JDatabaseMySQL($options);

	function existComponent($component){
		global $database;
		$sql = "select count(*) from #__components where link = 'option=".$component."'";
		$database->setQuery($sql);
		$database->query();
		$result = $database->loadResult();
		if($result > 0){
			return true;
		}
		return false;
	}

	function getComponentName($component){
		global $database;
		$sql = "select name from #__components where link = 'option=".$component."'";
		$database->setQuery($sql);
		$database->query();
		$result = $database->loadResult();
		return $result;
	}

	function getCurrentVersionData($component){
		$version = "";
		$data = 'update.kontentdesign.com/kontent_latest_version.txt';
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

	function getLocalVersionString($component, $xml_file){
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

	$list_changelog = array("com_handout"=>"http://update.kontentdesign.com/handout/changelog/",
							"com_podcastpro"=>"http://update.kontentdesign.com/podcastpro/changelog/",
							"mod_kontenttweet"=>"http://update.kontentdesign.com/kontenttweet/changelog/",
							"mod_kontentbox"=>"http://update.kontentdesign.com/kontentbox/changelog/");

	$list_all_components = array("com_handout"=>"handout.xml",
								 "com_podcastpro"=>"podcastpro.xml",
								 "mod_kontenttweet"=>"kontenttweet.xml",
								 "mod_kontentbox"=>"kontentbox.xml");

	$list_installed_components = array();

	foreach($list_all_components as $key=>$value){
		if(existComponent($key)){
			$list_installed_components[$key] = $value;
			$show_button = true;
		}
	}
?>
<style>
	.adminlist{
		background-color:#E7E7E7;
		border-spacing:1px;
		color:#666666;
		width:100%;
		text-align:center;
		font-family:Arial, Helvetica, sans-serif;
		font-size:13px;
	}

	.pagetitle{
		font:bold;
		font-size:18px;
	}

	.header{
		background:#F0F0F0 none repeat scroll 0 0;
		border-bottom:1px solid #999999;
		border-left:1px solid #FFFFFF;
		color:#666666;
		text-align:center;
	}

	.row1{
		background:#F9F9F9 none repeat scroll 0 0;
		border-top:1px solid #FFFFFF;
	}

	a{
		color: blue;
	}
</style>
<img src="<?php echo JURI::root()."logo.png"; ?>" />
<table class="adminlist">
	<tr class="header">
		<th>#</th>
		<th>Component</th>
		<th>Installed Version</th>
		<th>Latest Version</th>
		<th>Change log</th>
		<th>Download</th>
	</tr>
	<?php
		$i = 1;
		$row = 2;
		foreach($list_installed_components as $component=>$xml_file){
			$latest_version	 = getCurrentVersionData($component);
			$current_version = getLocalVersionString($component, $xml_file);
			$color = "green";
			$color_version = "black";
			if($latest_version != $current_version){
				$color = "red";
				$color_version = "red";
			}
			if($row == "2"){
				$row = "1";
			}
			else{
				$row = "2";
			}
			echo "<tr class=\"row".$row."\">";
			echo 	"<td>";
			echo 		$i++;
			echo 	"</td>";
			echo 	"<td width=\"30%\" style=\"color:".$color."\" >";
			echo 		getComponentName($component);
			echo 	"</td>";
			echo 	"<td align=\"center\" style=\"color:".$color_version."\" >";
			echo 		$current_version;
			echo 	"</td>";
			echo 	"<td align=\"center\" style=\"color:".$color_version."\" >";
			echo 		$latest_version;
			echo 	"</td>";
			echo 	"<td align=\"center\">";
			echo 		"<a href=\"".$list_changelog[$component]."\" target=\"_blank\">Change Log</a>";
			echo 	"</td>";
			echo 	"<td align=\"center\">";
			echo 		"<a href=\"http://update.kontentdesign.com/redirect/general/latestversion.htm\" target=\"_blank\">Download</a>";
			echo 	"</td>";
			echo "</tr>";
		}
	?>
</table>