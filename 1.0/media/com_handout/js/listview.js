 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: listview.js
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

var st, st1, st2; //sortable tables identifiers

//Initialise listview
function _listview_init()	{

	if(document.getElementById("tableBody") != null)	{
		st = new SortableTable(
			document.getElementById("tableBody"),
			["None", "CaseInsensitiveString", "Number", "CaseInsensitiveString"]
		);
	}
}

function onclickFolder(parid, catid, name, url, icon)	{
	window.parent.setFields(name, url, catid, icon, '', '');
	window.parent.setListCtrl(parid, catid);
}

function onclickItem(name, id, cid, ext, size, time)	{
	window.parent.setFields(name, id, cid, ext, size, time);
}

function setListView(catid) {
	location.href = "index.php?option=com_handout&task=doclink-listview&catid="+catid;
}

window.onload = _listview_init
//always hide the loading status
window.parent.changeDialogStatus('load');