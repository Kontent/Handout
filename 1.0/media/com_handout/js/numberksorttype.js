 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: numberksorttype.js
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

function replace8a8(str) {
	str = str.toUpperCase();
	var splitstr = "____";
	var ar = str.replace(
		/(([0-9]*\.)?[0-9]+([eE][-+]?[0-9]+)?)(.*)/,
	 "$1"+splitstr+"$4").split(splitstr);
	var num = Number(ar[0]).valueOf();
	var ml = ar[1].replace(/\s*([KMGB])\s*/, "$1");

	if (ml == "K")
		num *= 1024;
	else if(ml == "M")
		num *= 1024 * 1024;
	else if (ml == "G")
		num *= 1024 * 1024 * 1024;
	else if (ml == "T")
		num *= 1024 * 1024 * 1024 * 1024;
	// B and no prefix

	return num;
}

SortableTable.prototype.addSortType( "NumberK", replace8a8 );