<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: defines.php
 * @package 	Handout Thumbnails
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined('_JEXEC') or die('Restricted access');;

// Version
define('_AT_VERSION', '1.0');

// Paths
define('_AT_PATH', dirname(__FILE__));
define('_AT_PATH_IMAGES', JPATH_ROOT. '/images/stories/handout');
define('_AT_PATH_LIBRARIES', _AT_PATH.DS.'libraries');

// Urls
define('_AT_URL_IMAGES', JURI::root(true).'/images/stories/handout');

// Filetypes
define('_AT_FILETYPE_LIST', 'art,avi,avs,bmp,cgm,cin,cmyk,cmyka,cr2,crw,cur,cut,'
    .'dcm,dcr,dcx,dib,djvu,dng,dpx,emf,epdf,epi,eps,epsf,epsi,ept,exr,fax,fig,'
    .'fits,fpx,gif,gplt,gray,hpgl,htm,html,ico,icon,jbig,bie,jbg,jng,jp2,jpc,'
    .'jpeg,jpg,man,mat,miff,mono,mng,mpeg,m2v,mpc,mrw,msl,mtv,mvg,nef,orf,otb,p7,'
    .'palm,pam,pbm,pcd,pcds,pcx,pdb,pdf,pef,pfa,pfb,pfm,pgm,picon,pict,pix,png,'
    .'pnm,ppm,ps,ps2,ps3,psd,ptif,pwp,rad,raf,rgb,rgba,rla,rle,sct,sfw,sgi,shtml,'
    .'sun,svg,tga,tiff,tif,tim,ttf,txt,uyvy,vicar,viff,wbmp,wmf,wpg,x,xbm,xcf,'
    .'xpm,pm,xwd,x3f,ycbcr,ycbcra,yuv');