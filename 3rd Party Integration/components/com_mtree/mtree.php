<?php
/**
 * @version		$Id: mtree.php 855 2010-03-10 13:22:44Z cy $
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

global $task, $link_id, $cat_id, $user_id, $img_id, $start, $limitstart, $mtconf;

require_once(  JPATH_COMPONENT.DS.'init.php' );
$database 	=& JFactory::getDBO();
$my			=& JFactory::getUser();
$document	=& JFactory::getDocument();

require_once( JPATH_ADMINISTRATOR.DS.'components' .DS.'com_mtree'.DS.'admin.mtree.class.php' );
require_once( JPATH_COMPONENT.DS.'mtree.class.php' );
require_once( JPATH_COMPONENT.DS.'mtree.tools.php' );

# Caches
global $cache_cat_names, $cache_paths, $cache_lft_rgt;
$cache_cat_names = array();
$cache_paths = array();
$cache_lft_rgt = array();
$cache =& JFactory::getCache('com_mtree');

# Savant Class
require_once( JPATH_COMPONENT_SITE.DS.'Savant2.php');

$task		= JRequest::getCmd('task', '');
$link_id	= JRequest::getInt('link_id', 0);
$cat_id		= JRequest::getInt('cat_id', 0);
$user_id	= JRequest::getInt('user_id', 0);
$img_id		= JRequest::getInt('img_id', 0);
$cf_id 		= JRequest::getInt( 'cf_id'		,0 	);
$alpha		= JString::substr(JString::trim(JRequest::getVar('alpha', '')), 0, 3);
$limitstart = JRequest::getInt('limitstart', 0);

# Itemid
global $Itemid;
$menu = &JSite::getMenu();
$items	= $menu->getItems('link', 'index.php?option=com_mtree');
if(isset($items[0])) {
	$Itemid = $items[0]->id;
}

$jdate 		= JFactory::getDate();
$now		= $jdate->toMySQL();

global $savantConf;
$savantConf = array (
		'template_path' => JPATH_SITE.DS.'components'.DS.'com_mtree'.DS.'templates'.DS.$mtconf->get('template').DS,
		'plugin_path' => JPATH_SITE.DS.'components'.DS.'com_mtree'.DS.'Savant2'.DS,
		'filter_path' => JPATH_SITE.DS.'components'.DS.'com_mtree'.DS.'Savant2'.DS
);

mtAppendPathWay( $option, $task, $cat_id, $link_id, $img_id );

switch ($task) {
	
	case "att_download":
		$field_type	= JRequest::getCmd( 'ft'		,''	);
		$ordering	= JRequest::getInt( 'o'			,0 	);
		$filename 	= JRequest::getVar( 'file'		,''	);
		$link_id 	= JRequest::getInt( 'link_id'	,0 	);
		$img_id 	= JRequest::getInt( 'img_id'	,0 	);
		$size 		= JRequest::getInt( 'size'		,0	);
		att_download( $field_type, $ordering, $filename, $link_id, $cf_id, $img_id, $size );
		break;
		
	case "viewimage":
		viewimage( $img_id, $option );
		break;

	case "viewgallery":
		viewgallery( $link_id, $option );
		break;

	case "viewlink":
		viewlink( $link_id, $my, $limitstart, $option );
		break;

	case "print":
		printlink( $link_id, $option );
		break;

	/* RSS feed */
	case 'rss':
		$type = JRequest::getCmd('type', 'new');
		$token = JRequest::getCmd('token', '');
		$rss_secret_token = $mtconf->get( 'rss_secret_token');
		if( 
			($type == 'new' && $mtconf->get('show_listnewrss') == 0) 
			|| 
			($type == 'type' && $mtconf->get('show_listupdatedrss') ==  0) 
		) {
			echo JText::_("ALERTNOTAUTH");
		} elseif( !empty($rss_secret_token) && $token != $rss_secret_token ) {
			echo JText::_("ALERTNOTAUTH");
		} else {
			require_once( JPATH_SITE.DS.'components'.DS.'com_mtree'.DS.'rss.php');
			rss( $option, $type, $cat_id );
		}
		break;

	/* Visit a URL */
	case "visit":
		visit( $link_id, $cf_id );
		break;

	/* Reviews */
	case "writereview":
		writereview( $link_id, $option );
		break;
	case "addreview":
		addreview( $link_id, $option );
		break;

	/* Ratings */
	case "rate":
		rate( $link_id, $option );
		break;
	case "addrating":
		addrating( $link_id, $option );
		break;
	
	/* Favourite */
	case "fav":
		$action = JRequest::getInt('action', 1);
		fav( $link_id, $action, $option );
		break;

	/* Vote review */
	case 'votereview':
		$rev_vote	= JRequest::getInt('vote', 0);
		$rev_id		= JRequest::getInt('rev_id', 0);
		votereview( $rev_id, $rev_vote, $option );
		break;

	/* Report review */
	case "reportreview":
		$rev_id	= JRequest::getInt('rev_id', 0);
		reportreview( $rev_id, $option );
		break;
	case "send_reportreview":
		$rev_id	= JRequest::getInt('rev_id', 0);
		send_reportreview( $rev_id, $option );
		break;

	/* Reply review */
	case 'replyreview':
		$rev_id	= JRequest::getInt('rev_id', 0);
		replyreview( $rev_id, $option );
		break;
	case 'send_replyreview':
		$rev_id	= JRequest::getInt('rev_id', 0);
		send_replyreview( $rev_id, $option );
		break;

	/* Recommend to Friend */
	case "recommend":
		recommend( $link_id, $option );
		break;
	case "send_recommend":
		send_recommend( $link_id, $option );
		break;

	/* Contact Owner */
	case "contact":
		contact( $link_id, $option );
		break;
	case "send_contact":
		send_contact( $link_id, $option );
		break;

	/* Report Listing */
	case "report":
		report( $link_id, $option );
		break;
	case "send_report":
		send_report( $link_id, $option );
		break;

	/* Claim Listing */
	case "claim":
		claim( $link_id, $option );
		break;
	case "send_claim":
		send_claim( $link_id, $option );
		break;

	/* Add Listing */
	case "addlisting":
		editlisting( 0, $option );
		break;
	case "editlisting":
		editlisting( $link_id, $option );
		break;
	case "savelisting":
		require_once( JPATH_COMPONENT_SITE.DS.'includes'.DS.'diff.php');
		savelisting( $option );
		break;

	/* Add Category */
	case "addcategory":
		addcategory( $option );
		break;
	case "addcategory2":
		addcategory2( $option );
		break;

	/* Delete Listing */
	case "deletelisting":
		deletelisting( $link_id, $option );
		break;
	case "confirmdelete":
		confirmdelete( $link_id, $option );
		break;

	/* My Page */
	case "mypage":
		viewowner( $my->id, $limitstart, $option );
		break;

	/* All listing from this owner */
	case "viewowner":
		viewowner( $user_id, $limitstart, $option );
		break;

	/* All review from this user */
	case "viewusersreview":
		viewusersreview( $user_id, $limitstart, $option );
		break;

	/* All user's favourites */
	case "viewusersfav":
		viewusersfav( $user_id, $limitstart, $option );
		break;

	/* List Alphabetically */
	case "listalpha":
		listalpha( $cat_id, $alpha, $limitstart, $option );
		break;
	
	/* List Listing */
	case "listpopular":
	case "listmostrated":
	case "listtoprated":
	case "listmostreview":
	case "listnew":
	case "listupdated":
	case "listfeatured":
	case "listfavourite":
		require_once( JPATH_SITE.'/components/com_mtree/listlisting.php');
		listlisting( $cat_id, $option, $my, $task, $limitstart );
		break;

	/* Search */
	case "search":
		search( $option );
		break;
	case "searchby":
		searchby( $option );
		break;
	case "advsearch":
		advsearch( $option );
		break;
	case "advsearch2":
		advsearch2( $option );
		break;
		
	/* Ajax Category */
	case "getcats":
		getCats( $cat_id );
		break;
		
	/* Default Main Index */
	case "listcats":
	default:
		showTree( $cat_id, $limitstart, $option, $my );
		break;
}

// Append CSS file to Head
if( $mtconf->get('load_css') && $document->getType() == 'html')
{
	if ( file_exists( $savantConf['template_path'] . 'template.css' ) ) {
		$document->addCustomTag("<link href=\"" . str_replace(DS,'/',str_replace($mtconf->getjconf('absolute_path'),$mtconf->getjconf('live_site'),$savantConf['template_path'] . 'template.css')) . "\" rel=\"stylesheet\" type=\"text/css\"/>");
	} elseif ( file_exists( $mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $mtconf->get('template') . '/template.css' ) ) {
		$document->addCustomTag("<link href=\"". $mtconf->getjconf('live_site') ."/components/com_mtree/templates/".$mtconf->get('template')."/template.css\" rel=\"stylesheet\" type=\"text/css\"/>");
	} else {
		$document->addCustomTag("<link href=\"". $mtconf->getjconf('live_site') ."/components/com_mtree/templates/m2/template.css\" rel=\"stylesheet\" type=\"text/css\"/>");
	}
}

function getCats( $parent_cat_id ) {

	$database =& JFactory::getDBO();

	# Get pathway
	$mtPathWay = new mtPathWay($parent_cat_id);
	$return = $mtPathWay->printPathWayFromCat_withCurrentCat($parent_cat_id,0);
	$return .= "\n";
	
	$database->setQuery( 'SELECT cat_id, cat_name FROM #__mt_cats WHERE cat_parent = ' . $database->quote($parent_cat_id) . ' && cat_published = 1 && cat_approved = 1 ORDER BY cat_name ASC' );
	$cats = $database->loadObjectList();
	if($parent_cat_id > 0) {
		$database->setQuery( 'SELECT cat_parent FROM #__mt_cats WHERE cat_id = ' . $database->quote($parent_cat_id) . ' && cat_published = 1 && cat_approved = 1 LIMIT 1');
		$browse_cat_parent = $database->loadResult();
		$return .= $browse_cat_parent . "|" . JText::_( 'Arrow back' );
		if(!empty($cats)) {
			$return .= "\n";
		}
	} else {
		//
	}
	if(!empty($cats)) {
		foreach( $cats as $key => $cat )
		{
			$return .= $cat->cat_id . '|' . $cat->cat_name;
			if($key<(count($cats)-1)) {
				$return .=  "\n";
			}
		}
	}
	echo $return;
	return true;
}

function showTree( $cat_id, $limitstart, $option, $my ) {
	global $mtconf, $mainframe;

	$database	=& JFactory::getDBO();
	$document	=& JFactory::getDocument();
	
	$database->setQuery( 'SELECT * FROM #__mt_cats '
		.	'WHERE cat_id=' . $database->quote($cat_id) . ' AND cat_published = 1 LIMIT 1' );
	$cat = $database->loadObject();

	if ( $cat ) {
		# Set Page Title
		if ( $cat_id == 0 ) {
			$document->setTitle(JText::_( 'Root' ));
			$cat->cat_allow_submission = $mtconf->get('allow_listings_submission_in_root');
		} elseif( !empty($cat->title) ) {
			$document->setTitle($cat->title);
		} else {
			$document->setTitle($cat->cat_name);
		}

		# Add canonical URL if SEF URL is enabled
		if( $mainframe->getCfg('sef') )
		{
			$uri =& JURI::getInstance();
			$document->addHeadLink( 
				$uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_('index.php?option=com_mtree&task=listcats&cat_id='.$cat_id)
				,'canonical'
				,'rel'
			);
		}
		
		# Add META tags
		if ($mtconf->getjconf('MetaTitle')=='1') {
			if( $cat_id == 0 ) {
				$document->setMetadata( 'title' , JText::_( 'Root' ) );
			} else {
				$document->setMetadata( 'title' , htmlspecialchars($cat->cat_name) );
			}
		}

		$rss_secret_token = $mtconf->get( 'rss_secret_token');
		if( $mtconf->get( 'show_category_rss' ) && empty($rss_secret_token) ) {
			$document->addCustomTag( '<link rel="alternate" type="application/rss+xml" title="' . $mtconf->getjconf('sitename') . ' - ' . $cat->cat_name . '" href="index.php?option=com_mtree&task=rss&type=new&cat_id=' . $cat_id . '" />' );
		}

		if ($cat->metadesc <> '') {
			$document->setDescription( htmlspecialchars($cat->metadesc) );
		}
		
		if ($cat->metakey <> '') {
			$document->setMetaData('keywords', htmlspecialchars($cat->metakey));
		}
	}

	$cache =& JFactory::getCache('com_mtree');
	$cache->call( 'showTree_cache', $cat, $limitstart, $option, $my );
}

function showTree_cache( $cat, $limitstart, $option, $my ) {
	global $Itemid, $savantConf, $mtconf;

	$database	=& JFactory::getDBO();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	$nullDate	= $database->getNullDate();

	if ( empty($cat->cat_id) ) {
		$cat_id = 0;
	} else {
		$cat_id = $cat->cat_id;
	}

	if ( isset($cat->cat_published) && $cat->cat_published == 0 && $cat_id > 0 ) {
		
		echo JText::_( 'NOT_EXIST' );

	} else {

		# Page Navigation
		$database->setQuery( 'SELECT COUNT(*) FROM (#__mt_links AS l, #__mt_cl AS cl) WHERE l.link_published = 1 AND l.link_approved = 1 && cl.cat_id = ' . $database->quote($cat_id)
			. "\n AND ( l.publish_up = ".$database->Quote($nullDate)." OR l.publish_up <= '$now'  ) "
			. "\n AND ( l.publish_down = ".$database->Quote($nullDate)." OR l.publish_down >= '$now' ) "
			. "\n AND cl.link_id = l.link_id "
		);
		$total_links = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total_links, $limitstart, $mtconf->get('fe_num_of_links'));

		# Retrieve categories
		$sql = 'SELECT cat.* FROM #__mt_cats AS cat ';
		$sql .= 'WHERE cat_published=1 && cat_approved=1 && cat_parent= ' . $database->quote($cat_id);

		if ( !$mtconf->get('display_empty_cat') ) { $sql .= ' && ( cat_cats > 0 || cat_links > 0 ) ';	}

		if( $mtconf->get('first_cat_order1') != '' )
		{
			$sql .= ' ORDER BY ' . $mtconf->get('first_cat_order1') . ' ' . $mtconf->get('first_cat_order2');
			if( $mtconf->get('second_cat_order1') != '' )
			{
				$sql .= ', ' . $mtconf->get('second_cat_order1') . ' ' . $mtconf->get('second_cat_order2');
			}
		}

		$database->setQuery( $sql );
		$cats = $database->loadObjectList("cat_id");

		$cat_desc = '';
		$related_categories = null;
		$cat_ids = array();
		
		foreach ( $cats AS $c ) {
			$cat_ids[] = $c->cat_id;
		}

		$sub_cats = array();
		
		# Only shows sub-cat if this is a root category
		if ( ($cat_id == 0 || $cat->cat_usemainindex == 1) && $mtconf->getTemParam('numOfSubcatsToDisplay',3) != '0') {
			# Get all sub-cats
			$sql = "SELECT cat_id, cat_name, cat_cats, cat_links, cat_parent FROM #__mt_cats WHERE cat_parent IN (".implode(',',$cat_ids).") && cat_published='1' && cat_approved='1' ";

			if ( !$mtconf->get('display_empty_cat') ) { $sql .= " && ( cat_cats > 0 || cat_links > 0 ) ";	}
			
			if( $mtconf->get('first_cat_order1') != '' )
			{
				$sql .= "\nORDER BY cat_featured DESC, " . $mtconf->get('first_cat_order1') . ' ' . $mtconf->get('first_cat_order2');
				if( $mtconf->get('second_cat_order1') != '' )
				{
					$sql .= ', ' . $mtconf->get('second_cat_order1') . ' ' . $mtconf->get('second_cat_order2');
				}
			}
			
			$database->setQuery( $sql );
			$sub_cats_tmp = $database->loadObjectList();
			
			if(!empty($sub_cats_tmp)) {
				foreach($sub_cats_tmp AS $sub_cat) {
					if( isset($sub_cats[$sub_cat->cat_parent]) ) {
						if( $mtconf->getTemParam('numOfSubcatsToDisplay',3) > 0 && count($sub_cats[$sub_cat->cat_parent]) < $mtconf->getTemParam('numOfSubcatsToDisplay',3) ) {
							array_push($sub_cats[$sub_cat->cat_parent],$sub_cat);
						}
					} else {
						$sub_cats[$sub_cat->cat_parent] = array($sub_cat);
					}
					if(!isset($sub_cats_total[$sub_cat->cat_parent])) {
						$total_sub_cats = $cats[$sub_cat->cat_parent]->cat_cats;
						$sub_cats_total[$sub_cat->cat_parent] = (($total_sub_cats) ? $total_sub_cats : 0 );
					}
				}
			}
			if (isset($sub_cats)) {
				foreach($cat_ids AS $c) {
					if(!array_key_exists($c,$sub_cats)) {
						$sub_cats[$c] = array();
					}
				}
			}
			unset($sub_cats_tmp);

		} else {

			# Get related categories
			$database->setQuery( 'SELECT r.rel_id FROM #__mt_relcats AS r '
				.	'LEFT JOIN #__mt_cats AS c ON c.cat_id = r.rel_id '
				.	'WHERE r.cat_id = ' . $database->quote($cat_id) . ' AND c.cat_published = 1' );
			$related_categories = $database->loadResultArray();

		}

		# Get subset of listings
		if( ($cat_id == 0 || $cat->cat_usemainindex == 1) && is_numeric($mtconf->getTemParam('numOfLinksToDisplay',3)) && $mtconf->getTemParam('numOfLinksToDisplay',3)!=0) {

			$sql = "SELECT l.link_id, link_name, cl.cat_id FROM #__mt_links AS l "
				.	"\n LEFT JOIN #__mt_cl AS cl ON cl.link_id = l.link_id "
				.	"\n WHERE link_published='1' && link_approved='1' && cl.cat_id IN (".implode(',',$cat_ids).')'
				.	"\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
				.	"\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) ";
			if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
				$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2');
			} else {
				$sql .= "\n ORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2');
			}

			$database->setQuery( $sql );
			$cat_links_tmp = $database->loadObjectList();
			if(!empty($cat_links_tmp)) {
				foreach($cat_links_tmp AS $cat_link) {
					if(isset($cat_links[$cat_link->cat_id])) {
						if($mtconf->getTemParam('numOfLinksToDisplay',3) > 0 && count($cat_links[$cat_link->cat_id]) < $mtconf->getTemParam('numOfLinksToDisplay',3)) {
							array_push($cat_links[$cat_link->cat_id],$cat_link);
						}
					} else {
						$cat_links[$cat_link->cat_id] = array($cat_link);
					}
				}
			}
			foreach($cat_ids AS $c) {
				if(!isset($cat_links) || !array_key_exists($c,$cat_links)) {
					$cat_links[$c] = array();
				}
			}

		}
		
		# Retrieve Links
		$sql = "SELECT l.*, cl.*, cat.*, u.username AS username, u.name AS owner, img.filename AS link_image FROM #__mt_links AS l"
			.	"\n LEFT JOIN #__mt_cl AS cl ON cl.link_id = l.link_id "
			.	"\n LEFT JOIN #__users AS u ON u.id = l.user_id "
			.	"\n LEFT JOIN #__mt_cats AS cat ON cl.cat_id = cat.cat_id "
			.	"\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
			.	"\n WHERE link_published='1' && link_approved='1' && cl.cat_id = " . $database->quote($cat_id) . ' '
			.	"\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			.	"\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) ";
		
		if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
			$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2');
		} else {
			$sql .= "\n ORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2');
		}
		$sql .= "\n LIMIT $limitstart, " . $mtconf->get('fe_num_of_links');
		$database->setQuery( $sql );

		$links = $database->loadObjectList();

		# Pathway
		$pathWay = new mtPathWay( $cat_id );

		if ( isset($cat->cat_template) && $cat->cat_template <> '' ) {
			loadCustomTemplate(null,$savantConf,$cat->cat_template);
		}

		# Support Plugins
		if( isset($cat->cat_desc) && !empty($cat->cat_desc) ) {
			$cat->text = $cat->cat_desc;
		} else {
			$cat->text = '';
		}
		if( isset($cat_id) )$cat->id = $cat_id;
		if( isset($cat->cat_name) )$cat->title = $cat->cat_name;
		if($mtconf->get('cat_parse_plugin')) {
			$params =& new JParameter( '' );
			$dispatcher	=& JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$results = $dispatcher->trigger('onPrepareContent', array (& $cat, & $params->params, 0));
		}

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonListlinksVar( $savant, $links, $pathWay, $pageNav );
		$savant->assign('user_addlisting', $mtconf->get('user_addlisting'));
		
		if (isset($cat->cat_allow_submission)) {
			$savant->assign('cat_allow_submission',$cat->cat_allow_submission);
		} else {
			$savant->assign('cat_allow_submission',0);
		}

		if (isset($cat->cat_show_listings)) {
			$savant->assign('cat_show_listings',$cat->cat_show_listings);
		} else {
			$savant->assign('cat_show_listings',0);
		}

		if (isset($cat_links)) $savant->assign('cat_links', $cat_links);
		$savant->assign('cat_id', $cat_id);
		$savant->assign('categories', $cats);
		if (isset($sub_cats)) $savant->assign('sub_cats', $sub_cats);
		if (isset($sub_cats_total)) $savant->assign('sub_cats_total', $sub_cats_total);
		$savant->assign('related_categories', $related_categories);
		$savant->assignRef('links', $links);
		if (isset($cat->cat_desc)) $savant->assign('cat_desc', $cat->text);
		if (isset($cat->cat_image)) $savant->assign('cat_image', $cat->cat_image);
		if (isset($cat->cat_title)) $savant->assign('cat_title', $cat->title);
		if (isset($cat->cat_name)) $savant->assign('cat_name', $cat->cat_name);
		
		$savant->assign('total_listing', $total_links);

		if ( $cat_id == 0 || $cat->cat_usemainindex == 1 ) {
			$savant->assign('display_listings_in_root', $mtconf->get('display_listings_in_root'));
			$savant->display( 'page_index.tpl.php' );
		} else {
			$savant->display( 'page_subCatIndex.tpl.php' );
		}

	}

}

/***
* Search By
*/
function searchby( $option )
{
	global $mtconf, $savantConf;
	
	$database 	=& JFactory::getDBO();
	$uri 		=& JURI::getInstance();
	$nullDate	= $database->getNullDate();

	$value 		= JRequest::getString( 'value', '' );
	$cf_id 		= JRequest::getInt( 'cf_id', '' );
	$limitstart	= JRequest::getInt('limitstart', 0);
	$search_cat	= JRequest::getInt('cat_id', 0);
	if( $limitstart < 0 ) $limitstart = 0;

	if( empty($value) ) {
		JError::raiseError(404, JText::_('Resource Not Found'));
	}

	$only_subcats_sql = '';
	if ( $search_cat > 0 ) {
		$mtCats = new mtCats( $database );
		$subcats = $mtCats->getSubCats_Recursive( $search_cat, true );
		$subcats[] = $search_cat;
		if ( !empty($subcats) ) {
			$only_subcats_sql = "\n AND c.cat_id IN (" . implode( ", ", $subcats ) . ")";
		}
	}

	$jdate = JFactory::getDate();
	$now = $jdate->toMySQL();
	
	# Retrieve information about custom field
	$database->setQuery( 'SELECT * FROM #__mt_customfields AS cf'
		. ' WHERE cf.cf_id = ' . $database->Quote($cf_id) . ' AND published = 1 AND tag_search = 1 LIMIT 1');
	$customfield = $database->loadObject();
	
	if( is_null($customfield) ) {
		JError::raiseError(404, JText::_('Resource Not Found'));
	}
	
	# Retrieve links
	$sql = 'SELECT ';
	$sql .= 'l.*, u.username, c.*, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl';
	$sql .= ")";
	if( !$customfield->iscore ) {
		$sql .= "\n LEFT JOIN #__mt_cfvalues AS cfv ON cfv.link_id = l.link_id AND cfv.cf_id = " . $database->Quote($cf_id);
	}
	$sql .=	"\n	LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 " 
		.	"\n	LEFT JOIN #__mt_cats AS c ON c.cat_id = cl.cat_id " 
		.	"\n LEFT JOIN #__users AS u ON u.id = l.user_id "
		.	"\n	WHERE " 
		. 	"\n	link_published='1' AND link_approved='1' AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' )"
		.	"\n AND cl.link_id = l.link_id "
		.	"\n AND cl.main = 1 ";
	if( !$customfield->iscore ) {
		$sql .= "\n AND cfv.value LIKE " . $database->Quote('%'.$value.'%');
	} else {
		$sql .= "\n AND l.".substr($customfield->field_type,4)." LIKE " . $database->Quote('%'.$value.'%');
	}
	$sql .=	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' );
	
	if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
		$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
	} else {
		$sql .= "\n ORDER BY " . $mtconf->get('first_search_order1') . ' ' . $mtconf->get('first_search_order2') . ', ' . $mtconf->get('second_search_order1') . ' ' . $mtconf->get('second_search_order2');
	}
	
	$sql .=	"\n LIMIT $limitstart, " . $mtconf->get('fe_num_of_searchresults');
	$database->setQuery( $sql );
	$links = $database->loadObjectList();	
	
	# Get total
	$sql = "SELECT COUNT(DISTINCT l.link_id) FROM (#__mt_links AS l, #__mt_cl AS cl";
		$sql .= ")";
		if( !$customfield->iscore ) {
			$sql .= "\n LEFT JOIN #__mt_cfvalues AS cfv ON cfv.link_id = l.link_id AND cfv.cf_id = " . $database->Quote($cf_id);
		}
		$sql .=	"\n	LEFT JOIN #__mt_cats AS c ON c.cat_id = cl.cat_id " 
			.	"\n	WHERE " 
			.	"link_published='1' AND link_approved='1' AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' )"
			.	"\n AND cl.link_id = l.link_id "
			.	"\n AND cl.main = 1 ";
		if( !$customfield->iscore ) {
			$sql .= "\n AND cfv.value LIKE " . $database->Quote('%'.$value.'%');
		} else {
			$sql .= "\n AND l.".substr($customfield->field_type,4)." LIKE " . $database->Quote('%'.$value.'%');
		}
		$sql .= ( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' );
		$database->setQuery( $sql );

	$total = $database->loadResult();
	
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $mtconf->get('fe_num_of_searchresults'));

	$document=& JFactory::getDocument();
	$document->setTitle(JText::sprintf( 'Search By Title', $customfield->caption, $value ));

	# Pathway
	$pathWay = new mtPathWay();

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonListlinksVar( $savant, $links, $pathWay, $pageNav );

	$savant->assign('searchword', $value);
	$savant->assign('customfieldcaption', $customfield->caption);
	$savant->assign('cat_id', $search_cat);
	$savant->assign('total_listing', $total);

	$savant->display( 'page_searchByResults.tpl.php' );
}

/***
* Simple Search
*/
function search( $option ) {
	global $savantConf, $Itemid, $custom404, $mtconf, $mainframe;

	$database 	=& JFactory::getDBO();
	$uri 		=& JURI::getInstance();
	$nullDate	= $database->getNullDate();

	# Search word
	$post['searchword'] = JRequest::getString('searchword', null, 'post');
	$post['cat_id'] = JRequest::getInt('cat_id', 0, 'post');

	if( is_null($uri->getVar( 'searchword' )) && isset($post['searchword']) && !empty($post['searchword']) ) {
		$uri->setVar('option', 'com_mtree');
		$uri->setVar('task', 'search');
		$uri->setVar('searchword', $post['searchword']);
		$uri->setVar('cat_id', $post['cat_id']);

		// set Itemid id for links
		$menu = &JSite::getMenu();
		$items	= $menu->getItems('link', 'index.php?option=com_mtree');
		if(isset($items[0])) {
			$uri->setVar('Itemid', $items[0]->id);
		}

		$mainframe->redirect(JRoute::_('index.php'.$uri->toString(array('query', 'fragment')), false));
	}

	# slashes cause errors, <> get stripped anyway later on. # causes problems.
	$badchars = array('#','>','<','\\'); 
	$searchword = trim(str_replace($badchars, '', JRequest::getString('searchword', null)));
	
	# if searchword enclosed in double quotes, strip quotes and do exact match
	if (substr($searchword,0,1) == '"' && substr($searchword, -1) == '"') { 
		$post['searchword'] = substr($searchword,1,-1);
	}
	else {
		$post['searchword'] = $searchword;
	}
	// $searchword = $post['searchword'];
	
	# limit searchword to 20 (configurable) characters
	$restriction = false;
	if ( JString::strlen( $searchword ) > $mtconf->get('limit_max_chars') ) {
		$searchword 	= JString::substr( $searchword, 0, ($mtconf->get('limit_max_chars')-1) );
		$restriction 	= true;
	}

	// searchword must contain a minimum of 3 (configurable) characters
	if ( $searchword && JString::strlen( $searchword ) < $mtconf->get('limit_min_chars') ) {
		$searchword 	= '';
		$restriction 	= true;
	}
	
	if($restriction)
	{
		$mainframe->enqueueMessage(JText::sprintf('SEARCH_MESSAGE',$mtconf->get('limit_min_chars'),$mtconf->get('limit_max_chars')));
	}

	# Using Built in SEF feature in Joomla!
	if ( !isset($custom404) && $mtconf->getjconf('sef') ) {
		$searchword = urldecode($searchword);
	}

	# Search Category
	$search_cat	= JRequest::getInt('cat_id', 0);
	
	# Redirect to category page if searchword is empty and a category is selected
	if(!empty($search_cat) && empty($searchword)) {
		$mainframe->redirect( JRoute::_("index.php?option=$option&task=listcats&cat_id=$search_cat&Itemid=$Itemid") );
	}
	
	$only_subcats_sql = '';
	if ( $search_cat > 0 ) {
		$mtCats = new mtCats( $database );
		$subcats = $mtCats->getSubCats_Recursive( $search_cat, true );
		$subcats[] = $search_cat;
		if ( !empty($subcats) ) {
			$only_subcats_sql = "\n AND c.cat_id IN (" . implode( ", ", $subcats ) . ")";
		}
	}

	# Page Navigation
	$limitstart	= JRequest::getInt('limitstart', 0);
	if( $limitstart < 0 ) $limitstart = 0;
	
	$jdate = JFactory::getDate();
	$now = $jdate->toMySQL();
	
	$cats = array(0);
	
	# Construct WHERE
	$link_fields = array('link_name', 'link_desc', 'address', 'city', 'postcode', 'state', 'country', 'email', 'website', 'telephone', 'fax', 'metakey', 'metadesc', 'price' );

	$total = 0;
	$cats = array();

	if(!empty($searchword) || $searchword == '0') {
		$words = parse_words($searchword);
		
		foreach($words AS $key => $value) {
			$words[$key] = $database->getEscaped( $value, true );
		}
		
		$database->setQuery("SELECT field_type,published,simple_search FROM #__mt_customfields WHERE iscore = 1");
		$searchable_core_fields = $database->loadObjectList('field_type');

		# Determine if there are custom fields that are simple searchable
		$database->setQuery("SELECT COUNT(*) FROM #__mt_customfields WHERE published = 1 AND simple_search = 1 AND iscore = 0");
		$searchable_custom_fields_count = $database->loadResult();
		// @TODO: Lee, 9/4/2010
		// Hack to disable searching within custom fields even when configured so.
		// This is to allow Finder to index custom field data.
		$searchable_custom_fields_count = 0;
		
		$wheres0 = array();
		$wheres_cat = array();
		$wheres1 = array();
		foreach ($words as $word) {
			$wheres_cat[] = "\nLOWER(c.cat_name) LIKE '%$word%' OR LOWER(c.cat_desc) LIKE '%$word%'";

			foreach( $link_fields AS $lf ) {
				if ( 
					(substr($lf, 0, 5) == "link_" && array_key_exists('core'.substr($lf,5),$searchable_core_fields) && $searchable_core_fields['core'.substr($lf,5)]->published == 1 && $searchable_core_fields['core'.substr($lf,5)]->simple_search == 1)
					OR
					(array_key_exists('core'.$lf,$searchable_core_fields) && $searchable_core_fields['core'.$lf]->published == 1 && $searchable_core_fields['core'.$lf]->simple_search == 1)
				) {
					if(in_array($lf,array('metakey','metadesc','email'))) {
						$wheres0[] = "\n LOWER(l.$lf) LIKE '%$word%'";
					} else {
						$wheres0[] = "\n LOWER($lf) LIKE '%$word%'";
					}
				}
			}
			if($searchable_custom_fields_count > 0) {
				$wheres0[] = "\n" .' (cf.simple_search = 1 AND cf.published = 1 AND LOWER(cfv.value) LIKE \'%' . $word . '%\')';
			}
			$wheres1[] = "\n (" . implode( ' OR ', $wheres0 ) . ")";
			unset($wheres0);
		}
		$where = "(\n" . implode( "\nAND\n", $wheres1 ) . "\n)";
		$where_cat = '(' . implode( ') AND (', $wheres_cat ) . ')';

		# Retrieve categories
		if ( $limitstart == 0 ) {
			# Search Categories 
			$database->setQuery( "SELECT * FROM #__mt_cats AS c" 
				.	"\n WHERE " . $where_cat
				.	"\n AND cat_published='1' AND cat_approved='1' "
				.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' )
			);
			$cats = $database->loadObjectList();
		}
		# Retrieve links
		$sql = 'SELECT ';
		if( !empty($searchable_custom_fields_count) ) {
			$sql .= 'DISTINCT ';
		}
		$sql .= 'l.link_id, l.*, u.username, c.*, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl';
		if( !empty($searchable_custom_fields_count) ) {
			$sql .= ", #__mt_customfields AS cf";
		}
		$sql .= ")";
		if($searchable_custom_fields_count > 0) {
			$sql .= "\n LEFT JOIN #__mt_cfvalues AS cfv ON cfv.link_id = l.link_id AND cfv.cf_id = cf.cf_id";
		}
		$sql .=	"\n	LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 " 
			.	"\n	LEFT JOIN #__mt_cats AS c ON c.cat_id = cl.cat_id " 
			.	"\n LEFT JOIN #__users AS u ON u.id = l.user_id "
			.	"\n	WHERE " 
			. 	"\n	link_published='1' AND link_approved='1' AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' )"
			.	"\n AND cl.link_id = l.link_id "
			.	"\n AND cl.main = 1 ";
		$sql .= "\n AND ".$where
			.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' );
		
		if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
			$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		} else {
			$sql .= "\n ORDER BY " . $mtconf->get('first_search_order1') . ' ' . $mtconf->get('first_search_order2') . ', ' . $mtconf->get('second_search_order1') . ' ' . $mtconf->get('second_search_order2');
		}
	
		$sql .=	"\n LIMIT $limitstart, " . $mtconf->get('fe_num_of_searchresults');
		$database->setQuery( $sql );
		$links = $database->loadObjectList();

		# Get total
		$sql = "SELECT COUNT(DISTINCT l.link_id) FROM (#__mt_links AS l, #__mt_cl AS cl";
			if($searchable_custom_fields_count > 0) {
				$sql .= ", #__mt_customfields AS cf";
			}
			$sql .= ")";
			if($searchable_custom_fields_count > 0) {
				$sql .= "\n LEFT JOIN #__mt_cfvalues AS cfv ON cfv.link_id = l.link_id AND cfv.cf_id = cf.cf_id";
			}
			$sql .=	"\n	LEFT JOIN #__mt_cats AS c ON c.cat_id = cl.cat_id " 
				.	"\n	WHERE " 
				.	"link_published='1' AND link_approved='1' AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' )"
				.	"\n AND cl.link_id = l.link_id "
				.	"\n AND cl.main = 1 ";
			$sql .=	"\n AND ".$where
				.	( (!empty($only_subcats_sql)) ? $only_subcats_sql : '' );
			$database->setQuery( $sql );

		$total = $database->loadResult();
	}

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $mtconf->get('fe_num_of_searchresults'));

	$document=& JFactory::getDocument();
	$document->setTitle(JText::sprintf( 'SEARCH RESULTS FOR KEYWORD', $searchword ));

	# Pathway
	$pathWay = new mtPathWay();

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonListlinksVar( $savant, $links, $pathWay, $pageNav );

	$savant->assign('searchword', $searchword);
	$savant->assign('cat_id', $search_cat);
	$savant->assign('total_listing', $total);
	if ( $limitstart == 0 ) {
		$savant->assign('cats', $cats);	
		$savant->assign('categories', $cats);	
	}

	$savant->display( 'page_searchResults.tpl.php' );
}

/***
* Advanced Search
*/

function advsearch( $option ) {
	$document=& JFactory::getDocument();
	$document->setTitle(JText::_( 'Advanced search' ));

	advsearch_cache( $option );
}

function advsearch_cache( $option ) {
	global $savantConf, $Itemid, $mtconf;

	$database =& JFactory::getDBO();

	require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'mfields.class.php' );

	# Pathway
	$pathWay = new mtPathWay();

	# Get category's tree
	getCatsSelectlist( 0, $cat_tree, 0 );
	if( !empty($cat_tree) ) {
		$cat_options[] = JHTML::_('select.option', '', '');
		foreach( $cat_tree AS $ct ) {
			$cat_options[] = JHTML::_('select.option', $ct["cat_id"], str_repeat("&nbsp;",($ct["level"]*3)) .(($ct["level"]>0) ? " -":''). $ct["cat_name"]);
		}
		$catlist = JHTML::_('select.genericlist', $cat_options, 'cat_id', 'class="inputbox"', 'value', 'text', '');
	}
	
	# Search condition
	$searchConditions[] = JHTML::_('select.option', 1, strtolower(JText::_( 'Any' )));
	$searchConditions[] = JHTML::_('select.option', 2, strtolower(JText::_( 'All' )));
	$lists['searchcondition'] = JHTML::_('select.genericlist', $searchConditions, 'searchcondition', 'class="inputbox" size="1"', 'value', 'text', $mtconf->get('default_search_condition'));

	# Load all CORE and custom fields
	$database->setQuery( "SELECT cf.*, '0' AS link_id, '' AS value, '0' AS attachment, ft.ft_class FROM #__mt_customfields AS cf "
		.	"\nLEFT JOIN #__mt_fieldtypes AS ft ON ft.field_type=cf.field_type"
		.	"\nWHERE cf.published='1' && advanced_search = '1' ORDER BY ordering ASC" );
	$fields = new mFields($database->loadObjectList());
	
	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonListlinksVar( $savant, $links, $pathWay, $pageNav );
	$savant->assignRef('catlist', $catlist);
	$savant->assignRef('fields', $fields);
	$savant->assignRef('lists', $lists);
	$savant->display( 'page_advSearch.tpl.php' );

}

function advsearch2( $option ) {
	global $savantConf, $Itemid, $mtconf;

	$database =& JFactory::getDBO();
	$document=& JFactory::getDocument();

	require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'mfields.class.php' );
	require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'mAdvancedSearch.class.php' );

	$document->setTitle(JText::_( 'Advanced search results' ));

	# Load up search ID if available
	$search_id	= JRequest::getInt('search_id', 0);
	
	if ($search_id > 0) {
		$database->setQuery( 'SELECT search_text FROM #__mt_searchlog WHERE search_id = ' . $database->quote($search_id) );
		$post = unserialize($database->loadResult());
	} else { $post = JRequest::get( 'post' ); }

	# Load all published CORE & custom fields
	$database->setQuery( "SELECT cf.*, '0' AS link_id, '' AS value, '0' AS attachment, ft.ft_class FROM #__mt_customfields AS cf "
		.	"\nLEFT JOIN #__mt_fieldtypes AS ft ON ft.field_type=cf.field_type"
		.	"\nWHERE cf.published='1' ORDER BY ordering ASC" );
	$fields = new mFields($database->loadObjectList());
	$searchParams = $fields->loadSearchParams($post);
	
	$advsearch = new mAdvancedSearch( $database );
	if( intval( $post['searchcondition'] ) == 2 ) {
		$advsearch->useAndOperator();
	} else {
		$advsearch->useOrOperator();
	}

	# Search Category
	$search_cat	= intval( $post['cat_id'] );

	$only_subcats_sql = '';

	if ( $search_cat > 0 && is_int($search_cat) ) {
		$mtCats = new mtCats( $database );
		$subcats = $mtCats->getSubCats_Recursive( $search_cat, true );
		$subcats[] = $search_cat;
		if ( !empty($subcats) ) {
			$advsearch->limitToCategory( $subcats );
		}
	}

	$fields->resetPointer();
	while( $fields->hasNext() ) {
		$field = $fields->getField();
		$searchFields = $field->getSearchFields();

		if( isset($searchFields[0]) && isset($searchParams[$searchFields[0]]) && $searchParams[$searchFields[0]] != '' ) {
			foreach( $searchFields AS $searchField ) {
				$searchFieldValues[] = $searchParams[$searchField];
			}
			if( !empty($searchFieldValues) && $searchFieldValues[0] != '' ) {
				if( is_array($searchFieldValues[0]) && empty($searchFieldValues[0][0]) ) {
					// Do nothing
				} else {
					$tmp_where_cond = call_user_func_array(array($field, 'getWhereCondition'),$searchFieldValues);
					if( !is_null($tmp_where_cond) ) {
						$advsearch->addCondition( $field, $searchFieldValues );
					} 
				}
			}
			unset($searchFieldValues);
		}
		
		$fields->next();
	}

	$limit		= JRequest::getInt('limit', $mtconf->get('fe_num_of_searchresults'), 'get');
	$limitstart	= JRequest::getInt('limitstart', 0, 'get');
	if( $limitstart < 0 ) $limitstart = 0;

	$advsearch->search(1,1);
	
	// Total Results
	$total = $advsearch->getTotal();

	if ( $search_id <= 0 && $total > 0 ) {

		# Store search for later retrieval.
		if ( $search_id < 1 ) {
			$database->setQuery("INSERT INTO #__mt_searchlog (search_text) VALUES (".$database->quote(serialize($post)).")");
			if(!$database->query())
			{
				echo $database->getErrorMsg();
			}
		}

		# Get the above search ID
		$database->setQuery("SELECT search_id FROM #__mt_searchlog WHERE search_text =".$database->quote(serialize($post))); 
		$database->query();
		$search_id = $database->loadResult();

		$document->addCustomTag('<meta http-equiv="Refresh" content="1; URL='.JRoute::_("index.php?option=com_mtree&task=advsearch2&search_id=$search_id&Itemid=$Itemid").'">');

		# Savant template
		$savant = new Savant2($savantConf);
		$savant->assign('redirect_url', JRoute::_("index.php?option=com_mtree&task=advsearch2&search_id=$search_id&Itemid=$Itemid"));
		$savant->display( 'page_advSearchRedirect.tpl.php' );

	} else {
		$links = $advsearch->loadResultList( $limitstart, $limit );

		# Page Navigation
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		# Pathway
		$pathWay = new mtPathWay();

		# Savant template
		$savant = new Savant2($savantConf);
		assignCommonListlinksVar( $savant, $links, $pathWay, $pageNav );

		$savant->assign('search_id', $search_id);

		$savant->display( 'page_advSearchResults.tpl.php' );

	}
}

function listalpha( $cat_id, $alpha, $limitstart, $option ) {
	$database =& JFactory::getDBO();
	$document=& JFactory::getDocument();
	
	$database->setQuery( 'SELECT cat_name FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_id) . ' LIMIT 1' );
	$cat_name = $database->loadResult();

	$document->setTitle(sprintf(JText::_( 'List alpha by listings and cats' ), strtoupper($alpha), $cat_name));

	listalpha_cache( $cat_id, $alpha, $limitstart, $option );
}

function listalpha_cache( $cat_id, $alpha, $limitstart, $option ) {
	global $savantConf, $Itemid, $mtconf;
	
	$database	=& JFactory::getDBO();
	$nullDate	= $database->getNullDate();

	$where = array();
	
	# Number (0-9)
	if ( $alpha == '0' ) {
		for( $i=48; $i <= 57; $i++) {
			$cond_seq_link[] = "link_name LIKE '" . $database->getEscaped( chr($i), true ) . "%'";
			$cond_seq_cat[] = "cat1.cat_name LIKE '" . $database->getEscaped( chr($i), true ) . "%'";
		}
		$where[] = "(".implode(" OR ",$cond_seq_link).")";
		$where_cat[] = "(".implode(" OR ",$cond_seq_cat).")";

	# Alphabets (A-Z)
	} elseif ( preg_match('/[a-z0-9]{1}[0-9]*/', $alpha) OR ($mtconf->get('alpha_index_additional_chars') <> '' AND JString::strpos(JString::strtolower($mtconf->get('alpha_index_additional_chars')),JString::strtolower($alpha)) !== false ) ) {
		$where[] = "link_name LIKE '" . $database->getEscaped( $alpha, true ) . "%'";
		$where_cat[] = "cat1.cat_name LIKE '" . $database->getEscaped( $alpha, true ) . "%'";
	}

	if(!empty($where)) {
	
		# SQL condition to display category specific results
		$subcats = implode(", ",getSubCats_Recursive($cat_id));

		if ($subcats) $where[] = "cl.cat_id IN (" . $subcats . ")";
		if ($subcats) $where_cat[] = "cat1.cat_parent IN (" . $subcats . ")";

		// Get Total results - Links
		$jdate = JFactory::getDate();
		$now = $jdate->toMySQL();

		$sql = "SELECT COUNT(*) FROM (#__mt_links AS l, #__mt_cl AS cl) ";
		$where[] = "l.link_id = cl.link_id";
		$where[] = "cl.main = '1'";
		$where[] = "link_approved = '1'";
		$where[] = "link_published = '1'";
		$where[] = "( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  )";
		$where[] = "( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' )";
	
		$sql .= (!empty( $where ) ? " WHERE " . implode( ' AND ', $where ) : "");

		$database->setQuery( $sql );
		$total = $database->loadResult();

		// Get Links
		$link_sql = "SELECT l.*, u.username, cl.cat_id AS cat_id, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl) ";
		$link_sql .= " LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 ";
		$link_sql .= " LEFT JOIN #__users AS u ON u.id = l.user_id ";
		$link_sql .= (!empty( $where ) ? " WHERE " . implode( ' AND ', $where ) : "");
		$link_sql .= " AND l.link_id = cl.link_id AND cl.main = '1' ";
		
		if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
			$link_sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		} else {
			$link_sql .= "\n ORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		}
		
		
		$link_sql .= "LIMIT $limitstart, " . $mtconf->get('fe_num_of_links');

		# Shows categories if this is the first page. ie: $limitstart = 0
		$num_of_cats = 0;
		
		if ( $limitstart == 0 ) {
			
			$database->setQuery( "SELECT * FROM #__mt_cats AS cat1 WHERE "
				.	implode( ' AND ', $where_cat )	
				.	"AND cat_approved = '1' "
				.	"AND cat_published = '1' "
				.	($mtconf->getTemParam('onlyShowRootLevelCatInListalpha',0) ? "AND cat_parent = 0 " : "AND cat_parent >= 0 ")
				.	"ORDER BY cat_name ASC ");
			$categories = $database->loadObjectList();
			
			// Add parent category name to distinguish categories with same name
			$sql = 'SELECT DISTINCT cat1.cat_name FROM (#__mt_cats AS cat1, #__mt_cats AS cat2) ';
			$sql .= 'WHERE ' . implode( ' AND ', $where_cat ) . ' ';
			$sql .= 'AND cat1.cat_name = cat2.cat_name AND cat1.cat_id != cat2.cat_id ';
			$sql .= 'ORDER BY cat1.cat_name ASC';
			$database->setQuery( $sql );
			$same_name_cats = $database->loadResultArray();
		
			if( !empty($same_name_cats) ) {
				$mtcat = new mtCats( $database );
				for( $i=0; $i<count($categories); $i++ ) {
					if( in_array( $categories[$i]->cat_name, $same_name_cats ) ) {
						if( $categories[$i]->cat_parent > 0 ) {
							$categories[$i]->cat_name .= ' (' . $mtcat->getName($categories[$i]->cat_parent) . ')';
						}
					}
				}
			}

		}

		# SQL - Links
		$database->setQuery( $link_sql );
		$links = $database->loadObjectList();

		# Page Navigation
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $mtconf->get('fe_num_of_links'));
	
		# Pathway
		$pathWay = new mtPathWay( 0 );

		# Load custom template
		loadCustomTemplate( $cat_id, $savantConf);

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonListlinksVar( $savant, $links, $pathWay, $pageNav );

		if(!isset($categories)) {
			$savant->assign('categories', array());
		} else {
			$savant->assign('categories', $categories);
		}
		$savant->assign('alpha', urldecode($alpha));
		$savant->display( 'page_listAlpha.tpl.php' );
	} else {
		echo JText::_( 'NOT_EXIST' );
	}
}

function listlisting( $cat_id, $option, $my, $task, $limitstart ) {
	global $mtconf, $Itemid;

	$database		=& JFactory::getDBO();
	$listListing	= new mtListListing( $task );
	$listListing->setLimitStart( $limitstart );
	$document=& JFactory::getDocument();
	
	if( $cat_id == 0 ) {
		$cat_name = JText::_( 'Root' );
	} else {
		$database->setQuery( 'SELECT cat_name FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_id) . ' LIMIT 1' );
		$cat_name = $database->loadResult();
	}

	$document->setTitle($listListing->getTitle() . $cat_name);

	if(in_array($task,array('listnew','listupdated')) && $mtconf->get('show_list' . substr($task,4) . 'rss') ) {
		$document->addCustomTag(
			'<link rel="alternate" type="application/rss+xml" title="' . $mtconf->getjconf('sitename') 
			. ' - ' 
			. (
				($task=='listnew')
				?
				JText::_( 'New listing' )
				:
				JText::_( 'Recently updated listing' )
				) 
			. '" href="index.php?option=com_mtree&task=rss&type=' . substr($task,4) . '&Itemid=' . $Itemid . '" />'
		);
	}

	$cache =& JFactory::getCache('com_mtree');
	$cache->call('listlisting_cache', $cat_id, $option, $listListing);
}

function listlisting_cache( $cat_id, $option, $listListing ) {
	global $savantConf, $Itemid;

	$database =& JFactory::getDBO();
	
	# Retrieve Links
	$listListing->setSubcats( getSubCats_Recursive($cat_id) );

	$listListing->prepareQuery();
	$links = $listListing->getListings();

	# Load custom template
	loadCustomTemplate( $cat_id, $savantConf);

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonListlinksVar( $savant, $links, new mtPathWay(), $listListing->getPageNav() );

	$savant->assign('title', $listListing->getHeader());
	$savant->display( 'page_listListings.tpl.php' );
}

function viewowner( $user_id, $limitstart, $option ) {
	$database 	=& JFactory::getDBO();
	$document	=& JFactory::getDocument();
	
	# Get owner's info
	$database->setQuery( 'SELECT id, name, username, email FROM #__users WHERE id = ' . $database->quote($user_id) . ' LIMIT 1' );
	$owner = $database->loadObject();

	if( !empty($owner) ) {
		$document->setTitle(sprintf(JText::_( 'Listing by' ), $owner->username));
		viewowner_cache( $owner, $limitstart, $option );
	} else {
		echo JText::_( 'NOT_EXIST' );
	}

}

function viewowner_cache( $owner, $limitstart, $option ) {
	global $Itemid, $savantConf, $mtconf;

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	$user_id 	= $owner->id;
	$nullDate	= $database->getNullDate();
	
	if ( $owner ) {
		
		if( $mtconf->get( 'display_pending_approval_listings_to_owners' ) && $my->id == $user_id ) {
			$show_approved_and_published_listings_only = false;
		} else {
			$show_approved_and_published_listings_only = true;
		}
		
		# Page Navigation
		$database->setQuery("SELECT COUNT(*) FROM #__mt_links WHERE "
			. "\n " . (($show_approved_and_published_listings_only) ? "link_published='1' AND link_approved='1' AND " : 'link_approved >= 0 AND ') 
			. "\n user_id ='".$user_id."'"
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
			);
		$total_links = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total_links, $limitstart, $mtconf->get('fe_num_of_links'));

		# Retrieve Links
		$sql = "SELECT l.*, u.username, cat.*, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat)"
			. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
			. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
			. "\n WHERE " . (($show_approved_and_published_listings_only) ? "link_published='1' AND link_approved='1' AND " : 'link_approved >= 0 AND ') 
			. "\n user_id='".$user_id."' "
			. "\n AND l.link_id = cl.link_id AND cl.main = '1'"
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
			. "\n AND cl.cat_id = cat.cat_id ";
		
		if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
			$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		} else {
			$sql .= "\n ORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		}
		
		$sql .= "\n LIMIT $limitstart, " . $mtconf->get('fe_num_of_links');
		$database->setQuery( $sql );
		$links = $database->loadObjectList();
		
		# Get total reviews
		$database->setQuery("SELECT COUNT(*) FROM #__mt_reviews AS r"
			.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			.	"\nWHERE r.user_id = '".$user_id."' AND rev_approved='1' AND l.link_published='1' AND l.link_approved='1'"
			);
		$total_reviews = $database->loadResult();

		# Get total favourites
		$database->setQuery("SELECT COUNT(DISTINCT f.link_id) FROM #__mt_favourites AS f"
			.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = f.link_id"
			.	"\nWHERE f.user_id = '".$user_id."' AND l.link_published='1' AND l.link_approved='1'"
			);
		$total_favourites = $database->loadResult();

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonListlinksVar( $savant, $links, new mtPathWay(), $pageNav );
		$savant->assign('owner', $owner);
		$savant->assign('total_reviews', $total_reviews);
		$savant->assign('total_favourites', $total_favourites);

		$savant->display( 'page_ownerListing.tpl.php' );

	} else {
		echo JText::_( 'NOT_EXIST' );
	}
}

function viewusersreview( $user_id, $limitstart, $option ) {
	global $mtconf;

	$database =& JFactory::getDBO();
	$document=& JFactory::getDocument();

	# Get owner's info
	$database->setQuery( "SELECT id, name, username, email FROM #__users WHERE id = '".$user_id."'" );
	$owner = $database->loadObject();
	
	if( count($owner) == 1 && $mtconf->get('show_review') ) {
		$document->setTitle(sprintf(JText::_( 'Reviews by' ), $owner->username));
		viewusersreview_cache( $owner, $limitstart, $option );
	} else {
		echo JText::_( 'NOT_EXIST' );
	}

}

function viewusersreview_cache( $owner, $limitstart, $option ) {
	global $savantConf, $mtconf;

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$user_id 	= $owner->id;
	$nullDate	= $database->getNullDate();

	if ( $owner ) {

		$jdate = JFactory::getDate();
		$now = $jdate->toMySQL();

		# Page Navigation
		$database->setQuery("SELECT COUNT(*) FROM #__mt_reviews AS r"
			.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			.	"\nWHERE r.user_id = '".$user_id."' AND rev_approved='1' AND l.link_published='1' AND l.link_approved='1'"
			);
		$total_reviews = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total_reviews, $limitstart, $mtconf->get('fe_num_of_links'));

		# Retrieve reviews
		$database->setQuery( "SELECT r.*, l.*, u.username, log.value AS rating, img.filename AS link_image FROM #__mt_reviews AS r"
			.	"\nLEFT JOIN #__mt_log AS log ON log.user_id = r.user_id AND log.link_id = r.link_id AND log_type = 'vote' AND log.rev_id = r.rev_id"
			.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			.	"\nLEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1"
			.	"\n LEFT JOIN #__users AS u ON u.id = r.user_id "
			.	"\nWHERE r.user_id = '".$user_id."' AND r.rev_approved = 1 AND l.link_published='1' AND link_approved='1'"
			.	"\nORDER BY r.rev_date DESC"
			.	"\nLIMIT $limitstart, " . $mtconf->get('fe_num_of_links')
			);
		$reviews = $database->loadObjectList();
		
		for( $i=0; $i<count($reviews); $i++ ) {
			$reviews[$i]->rev_text = nl2br(htmlspecialchars(trim($reviews[$i]->rev_text)));
			$reviews[$i]->ownersreply_text = nl2br(htmlspecialchars(trim($reviews[$i]->ownersreply_text)));
		}
		
		# Get total links
		$database->setQuery("SELECT COUNT(*) FROM #__mt_links WHERE "
			. "\n	link_published='1' AND link_approved='1' AND user_id ='".$user_id."'"
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
			);
		$total_links = $database->loadResult();

		# Get total favourites
		$database->setQuery("SELECT COUNT(DISTINCT f.link_id) FROM #__mt_favourites AS f"
			.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = f.link_id"
			.	"\nWHERE f.user_id = '".$user_id."' AND l.link_published='1' AND l.link_approved='1'"
			);
		$total_favourites = $database->loadResult();

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonListlinksVar( $savant, $reviews, new mtPathWay(), $pageNav );
		$savant->assign('owner', $owner);
		$savant->assign('reviews', $reviews);
		$savant->assign('total_links', $total_links);
		$savant->assign('total_favourites', $total_favourites);

		$savant->display( 'page_usersReview.tpl.php' );

	} else {
		echo JText::_( 'NOT_EXIST' );
	}
}

function viewusersfav( $user_id, $limitstart, $option ) {
	global $mtconf;

	$database =& JFactory::getDBO();
	$document=& JFactory::getDocument();

	# Get owner's info
	$database->setQuery( "SELECT id, name, username, email FROM #__users WHERE id = '".$user_id."'" );
	$owner = $database->loadObject();
	
	if( count($owner) == 1 && $mtconf->get('show_favourite')) {
		$document->setTitle(sprintf(JText::_( 'Favourites by' ), $owner->username));
		viewusersfav_cache( $owner, $limitstart, $option );
	} else {
		echo JText::_( 'NOT_EXIST' );
	}

}

function viewusersfav_cache( $owner, $limitstart, $option ) {
	global $Itemid, $savantConf, $mtconf;

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	$user_id 	= $owner->id;
	$nullDate	= $database->getNullDate();

	if ( $owner ) {

		# Page Navigation
		$database->setQuery("SELECT COUNT(DISTINCT f.link_id) FROM #__mt_favourites AS f "
			.	"\n LEFT JOIN #__mt_links AS l ON l.link_id = f.link_id "
			. "\n WHERE "
			. "\n	l.link_published='1' AND l.link_approved='1' AND f.user_id ='".$user_id."'"
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
			);
		$total_favourites = $database->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total_favourites, $limitstart, $mtconf->get('fe_num_of_links'));

		# Retrieve Links
		$sql = "SELECT DISTINCT l.*, u.username, cat.*, img.filename AS link_image "
			. "FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat, #__mt_favourites AS f)"
			. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
			. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
			. "\n WHERE link_published='1' AND link_approved='1' AND f.user_id='".$user_id."' AND f.link_id = l.link_id "
			. "\n AND l.link_id = cl.link_id AND cl.main = '1'"
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
			. "\n AND cl.cat_id = cat.cat_id ";
		
		if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
			$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		} else {
			$sql .= "\n ORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2') . ' ';
		}
		
		$sql .= "\n LIMIT $limitstart, " . $mtconf->get('fe_num_of_links') ;
		$database->setQuery( $sql );
		$links = $database->loadObjectList();
		
		# Get total reviews
		$database->setQuery("SELECT COUNT(*) FROM #__mt_reviews AS r"
			. "\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			. "\nWHERE r.user_id = '".$user_id."' AND rev_approved='1' AND l.link_published='1' AND l.link_approved='1'"
			);
		$total_reviews = $database->loadResult();

		# Get total links
		$database->setQuery("SELECT COUNT(*) FROM #__mt_links WHERE "
			. "\n	link_published='1' AND link_approved='1' AND user_id ='".$user_id."'"
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
			);
		$total_links = $database->loadResult();

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonListlinksVar( $savant, $links, new mtPathWay(), $pageNav );
		$savant->assign('owner', $owner);
		$savant->assign('total_reviews', $total_reviews);
		$savant->assign('total_links', $total_links);

		$savant->display( 'page_usersFavourites.tpl.php' );

	} else {
		echo JText::_( 'NOT_EXIST' );
	}
}

/***
* Visit URL
*/

function visit( $link_id, $cf_id ) {
	global $mtconf, $mainframe;

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	$nullDate	= $database->getNullDate();

	$database->setQuery( "SELECT website FROM #__mt_links"
		.	"\n	WHERE link_published='1' AND link_approved > 0 AND link_id='".$link_id."' " 
		. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
	);

	$link = $database->loadObject();

	// Checks if the listing is an approved & published listing
	if (empty($link)) {
		
		echo JText::_( 'NOT_EXIST' );

	} else {
		
		if( !empty($cf_id) ) {
			
			// Get custom field link
			$database->setQuery( 'SELECT value FROM #__mt_cfvalues WHERE cf_id = ' . $database->Quote($cf_id) . ' AND link_id = ' . $database->Quote($link_id) . ' LIMIT 1' );
			$url = $database->loadResult();
			if( !empty($url) ) {
				$mainframe->redirect( 
					(
						substr($url,0,7) == 'http://' 
						|| 
						substr($url,0,8) == 'https://') 
						? 
						$url : 'http://'.$url 
					);
			} else {
				echo JText::_( 'NOT_EXIST' );
			}
			
		} else {
			if($mtconf->get('log_visit'))
			{
				$remote_addr = JRequest::getCmd( 'REMOTE_ADDR', '', 'server');
				$mtLog = new mtLog( $database, $remote_addr, $my->id, $link_id );
				$mtLog->logVisit();
			}

			# Update #__mt_links table
			$database->setQuery( 'UPDATE #__mt_links SET link_visited = link_visited + 1 WHERE link_id = ' . $database->quote($link_id) );
			if (!$database->query()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}

			$mainframe->redirect( (substr($link->website,0,7) == 'http://' || substr($link->website,0,8) == 'https://') ? $link->website : 'http://'.$link->website );
		}

	}

}

/***
* View Gallery
*/
function viewgallery( $link_id, $option ) {
	global $savantConf;
	
	$database 	=& JFactory::getDBO();
	$link 		= loadLink( $link_id, $savantConf, $fields, $params );
	$document	=& JFactory::getDocument();

	if($link === false)	{
		echo JText::_( 'NOT_EXIST' );
	} else {

		$document->setTitle(sprintf(JText::_( 'Gallery2' ), $link->link_name));
	
		$database->setQuery('SELECT img_id, filename FROM #__mt_images WHERE link_id = ' . $database->quote($link_id) . ' ORDER BY ordering ASC');
		$images = $database->loadObjectList();
		
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
		$savant->assign('images', $images);
		$savant->display( 'page_gallery.tpl.php' );
	}	
}

/***
* View Image
*/
function viewimage( $img_id, $option ) {
	global $savantConf;
	
	$database 	=& JFactory::getDBO();
	$document	=& JFactory::getDocument();

	$database->setQuery('SELECT img_id, link_id, filename, ordering from #__mt_images WHERE img_id = ' . $database->quote($img_id) . ' LIMIT 1');
	$image = $database->loadObject();
	
	if(isset($image) && $image->link_id > 0) {
		$link = loadLink( $image->link_id, $savantConf, $fields, $params );
	} else {
		$link = false;
	}

	if($link === false)	{
		echo JText::_( 'NOT_EXIST' );
	} else {
		$database->setQuery('SELECT img_id, filename FROM #__mt_images WHERE link_id = ' . $database->quote($image->link_id) . ' ORDER BY ordering ASC');
		$images = $database->loadObjectList();

		$document->setTitle(sprintf(JText::_( 'Image2' ), $link->link_name));

		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
		$savant->assign('image', $image);
		$savant->assign('images', $images);
		$savant->display( 'page_image.tpl.php' );
	}	
}

/***
* View Listing
*/
function viewlink( $link_id, $my, $limitstart, $option ) {
	global $savantConf, $mtconf, $mainframe;

	$link 		= loadLink( $link_id, $savantConf, $fields, $params );
	$document	=& JFactory::getDocument();

	if($link === false)	{
		if( $mtconf->get('unpublished_message_cfid') > 0 )
		{
			$database =& JFactory::getDBO();
			$database->setQuery( 
				'SELECT l.*, u.username AS username FROM #__mt_links AS l '
				. 'LEFT JOIN #__users AS u ON u.id = l.user_id '
				. 'WHERE link_id = ' 
				. $database->quote($link_id) 
				. ' LIMIT 1' );
			$link = $database->loadObject();
			if( $link->link_published == 0 )
			{
				$database->setQuery( 
					'SELECT value FROM #__mt_cfvalues WHERE link_id = ' . $database->quote($link_id) 
					. ' AND cf_id = ' . $database->quote($mtconf->get('unpublished_message_cfid'))
					. ' LIMIT 1' );
				$unpublished_message = $database->loadResult();

				if( !empty($unpublished_message) ) {

					$params =& new JParameter( $link->attribs );
					$savant = new Savant2($savantConf);
					$fields = loadFields($link);
                  
					assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
					
					$unpublished_message_cf = $fields->getFieldById($mtconf->get('unpublished_message_cfid'));
					$unpublished_message = $unpublished_message_cf->getOutput();
					
					$savant->assign('error_msg', JText::sprintf( 'This listing has been unpublished for the following reason:', $unpublished_message ));
					$savant->assign('my', $my);
					$savant->display( 'page_errorListing.tpl.php' );
					
				} else {
					JError::raiseError(404, JText::_('Resource Not Found'));
				}
			} else {
				JError::raiseError(404, JText::_('Resource Not Found'));
			}
		}
		else
		{
			JError::raiseError(404, JText::_('Resource Not Found'));
		}
	} else {
	
		# Set Page Title
		$document->setTitle($link->link_name);
		
		# Add canonical URL if SEF URL is enabled
		if( $mainframe->getCfg('sef') )
		{
			$uri =& JURI::getInstance();
			$document->addHeadLink( 
				$uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_('index.php?option=com_mtree&task=viewlink&link_id='.$link_id)
				,'canonical'
				,'rel'
			);
		}
		
		# Add META tags
		if ($mtconf->getjconf('MetaTitle')=='1') {
			$document->setMetadata( 'title', htmlspecialchars($link->link_name));
		}
		if ($mtconf->getjconf('MetaAuthor')=='1') {
			$document->setMetadata( 'author' , htmlspecialchars($link->owner) );
		}

		if ( !empty($link->metadesc) )
		{
			$document->setDescription( htmlspecialchars($link->metadesc) );
		}
		elseif( !empty($link->link_desc) )
		{
			$metadesc_maxlength = 300;
			
			// Get the first 300 characters
			$metadesc = JString::trim(strip_tags($link->link_desc));
			$metadesc = JString::str_ireplace("\r\n","",$metadesc);
			$metadesc = JString::substr($metadesc,0,$metadesc_maxlength);
			
			// Make sure the meta description is complete and is not truncated in the middle of a sentence.
			if( JString::strlen($link->link_desc) > $metadesc_maxlength && substr($metadesc,-1,1) != '.') {
				if( strrpos($metadesc,'.') !== false )
				{
					$metadesc = JString::substr($metadesc,0,JString::strrpos($metadesc,'.')+1);
				}
			}
			$document->setDescription( htmlspecialchars($metadesc) );
		}
		
		if ($link->metakey <> '') $document->setMetaData( 'keywords', $link->metakey );

		$document->addCustomTag(' <script src="'.$mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js_library') . '" type="text/javascript"></script>');
		$document->addCustomTag(' <script src="'.$mtconf->getjconf('live_site').'/components/com_mtree/js/vote.js" type="text/javascript"></script>');

		# Predefine variables:
		$prevar = " <script type=\"text/javascript\"><!-- \n";
		$prevar .= "jQuery.noConflict();\n";
		$prevar .= "var mtoken=\"".JUtility::getToken()."\";\n";
		$prevar .= "var mosConfig_live_site=\"".$mtconf->getjconf('live_site')."\";\n";

		$prevar .= "var ratingText=new Array();\n";
		$prevar .= "ratingText[5]=\"" . JText::_( 'Rating 5', true) . "\";\n";
		$prevar .= "ratingText[4]=\"" . JText::_( 'Rating 4', true) . "\";\n";
		$prevar .= "ratingText[3]=\"" . JText::_( 'Rating 3', true) . "\";\n";
		$prevar .= "ratingText[2]=\"" . JText::_( 'Rating 2', true) . "\";\n";
		$prevar .= "ratingText[1]=\"" . JText::_( 'Rating 1', true) . "\";\n";
		$prevar .= "//--></script>";
		$document->addCustomTag($prevar);
		
		if( !empty($my->id) && !$mtconf->get('cache_registered_viewlink') ) {
			viewlink_cache( $link, $limitstart, $fields, $params, $my, $option );
		} else {
			$cache = &JFactory::getCache('com_mtree');
			$cache->call( 'viewlink_cache', $link, $limitstart, $fields, $params, $my, $option );
		}
	}
}

function viewlink_cache( $link, $limitstart, $fields, $params, $my, $option ) {
	global $savantConf, $Itemid, $mtconf;

	$database	=& JFactory::getDBO();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	$link_id	= $link->link_id;
	
	if ( !isset($link->link_id) || $link->link_id <= 0 ) {
		echo JText::_( 'NOT_EXIST' );
	} else {

		# Increase 1 hit
		$cookiename = "mtlink_hit$link->link_id";
		$visited = JRequest::getVar( $cookiename, '0', 'COOKIE', 'INT');
		
		if (!$visited) {
			$database->setQuery( "UPDATE #__mt_links SET link_hits=link_hits+1 WHERE link_id='".$link_id."'" );
			$database->query();
		}

		setcookie( $cookiename, '1', time()+$mtconf->get('hit_lag') );
	
		# Get reviews
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_reviews AS r WHERE link_id = '".$link_id."' AND r.rev_approved = 1" );
		$total_reviews = $database->loadResult();
		
		$sql = "SELECT r.*, u.username, log.value AS rating FROM #__mt_reviews AS r"
			.	"\n LEFT JOIN #__users AS u ON u.id = r.user_id"
			.	"\n LEFT JOIN #__mt_log AS log ON log.user_id = r.user_id AND log.link_id = r.link_id AND log_type = 'vote' AND log.rev_id = r.rev_id"
			.	"\n WHERE r.link_id = '".$link_id."' AND r.rev_approved = 1 ";
		if( $mtconf->get('first_review_order1') != '' )
		{
			$sql .= "\n ORDER BY " . $mtconf->get('first_review_order1') . ' ' . $mtconf->get('first_review_order2') ;
			if( $mtconf->get('second_review_order1') != '' )
			{
				$sql .= ', ' . $mtconf->get('second_review_order1') . ' ' . $mtconf->get('second_review_order2');
				if( $mtconf->get('third_review_order1') != '' )
				{
					$sql .= ', ' . $mtconf->get('third_review_order1') . ' ' . $mtconf->get('third_review_order2');
				}
			}
		}
		$sql .= "\n LIMIT $limitstart, " . $mtconf->get('fe_num_of_reviews');
		$database->setQuery( $sql );
		$reviews = $database->loadObjectList();

		# Add <br /> to all new lines & gather an array of review_ids
		for( $i=0; $i<count($reviews); $i++ ) {
			$reviews[$i]->rev_text = nl2br(htmlspecialchars(trim($reviews[$i]->rev_text)));
			$reviews[$i]->ownersreply_text = nl2br(htmlspecialchars(trim($reviews[$i]->ownersreply_text)));
		}
		
		# If the user is logged in, get all voted rev_ids
		if( $my->id > 0 ) {
			$database->setQuery( 'SELECT value, rev_id FROM #__mt_log WHERE log_type = \'votereview\' AND user_id = \''.$my->id.'\' AND link_id = \''.$link_id.'\' LIMIT '.$total_reviews );
			$voted_reviews = $database->loadObjectList( 'rev_id' );
		} else {
			$voted_reviews = array();
		}
		# Get image ids
		$database->setQuery("SELECT img_id AS id, filename FROM #__mt_images WHERE link_id = '" . $link_id . "' ORDER BY ordering ASC");
		$images = $database->loadObjectList();
		
		# Page Navigation
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total_reviews, $limitstart, $mtconf->get('fe_num_of_reviews'));

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Mambots
		global $_MAMBOTS;
		$page = 0;

		# Load Parameters
		$params =& new JParameter( $link->attribs );
		$mtconf->set('show_rating',$params->def( 'show_rating', $mtconf->get('show_rating') ));
		$mtconf->set('show_review',$params->def( 'show_review', $mtconf->get('show_review') ));
		
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
		
		$savant->assign('pageNav', $pageNav);
		$savant->assign('my', $my);
		$savant->assign('reviews', $reviews);
		$savant->assign('images', $images);
		$savant->assign('voted_reviews', $voted_reviews);
		$savant->assign('total_reviews', ((isset($total_reviews)) ? $total_reviews : 0 ));
		$savant->assign('user_report_review',$mtconf->get('user_report_review'));
		
		if( $my->id > 0 && $mtconf->get('user_vote_review') == 1 ) {
			$savant->assign('show_review_voting', 1);
		} else {
			$savant->assign('show_review_voting', 0);
		}
		$savant->assign('user_report_review',$mtconf->get('user_report_review'));
		$savant->display( 'page_listing.tpl.php' );

	}
}

function printlink( $link_id, $option ) {
	global $_MAMBOTS, $savantConf, $Itemid, $mtconf;

	$database	=& JFactory::getDBO();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	$link 		= loadLink( $link_id, $savantConf, $fields, $params );

	if (empty($link)) {
		echo JText::_( 'NOT_EXIST' );
	} else {

		$page = 0;

		# Get image ids
		$database->setQuery("SELECT img_id AS id, filename FROM #__mt_images WHERE link_id = '" . $link_id . "' ORDER BY ordering ASC");
		$images = $database->loadObjectList();

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
		$savant->assign('images', $images);

		$savant->display( 'page_print.tpl.php' );

	}
}

/***
* Report Listing
*/
function report( $link_id, $option ) {
	global $savantConf;

	$document=& JFactory::getDocument();

	$link = loadLink( $link_id, $savantConf, $fields, $params );
	$document->setTitle(JText::_( 'Report2' ) . $link->link_name);

	report_cache( $link, $fields, $params, $option );
}

function report_cache( $link, $fields, $params, $option ) {
	global $savantConf, $mtconf;

	$my			=& JFactory::getUser();

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	if ( empty($link) || $mtconf->get('user_report') == '-1' ) {
		echo JText::_( 'NOT_EXIST' );
	} elseif ( $mtconf->get('user_report') == 1 && $my->id < 1 ) {
		# User is not logged in
		$savant->assign('error_msg', JText::_( 'Please login before report' ));
		$savant->display( 'page_errorListing.tpl.php' );
	} else {
		$savant->display( 'page_report.tpl.php' );
	}

}

function send_report( $link_id, $option ) {
	global $Itemid, $mtconf, $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();

	if ( $mtconf->get('show_report') == 0 ) {
		echo JText::_( 'NOT_EXIST' );
	} elseif ( $mtconf->get('user_report') == '-1' || ($mtconf->get('user_report') == 1 && $my->id  < 1) ) {
		# User is not logged in
		echo JText::_( 'NOT_EXIST' );
	} else {

		$link = new mtLinks( $database );
		$link->load( $link_id );

		$your_name	= JRequest::getString('your_name', '', 'post');
		$report_type= JRequest::getInt('report_type', '', 'post');
		$report_type2 = "REPORT PROBLEM ".$report_type;

		$message = JRequest::getVar( 'message',	'', 'post');

		$uri =& JURI::getInstance();
		$text = JText::sprintf( 
			'Report email', 
			$uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$link_id&Itemid=$Itemid",false), 
			$link->link_name,
			JText::_( $report_type2 ), 
			$link_id, 
			$message);

		$subject = JText::_( 'Report' )." - ".$mtconf->getjconf('sitename');

		if( mosMailToAdmin( $subject, $text ) )
		{
			if( $my->id > 0 )  {
				# User is logged on, store user ID
				$database->setQuery( "INSERT INTO #__mt_reports "
					.	"( `link_id` , `user_id` , `subject` , `comment`, created ) "
					.	'VALUES (' . $database->quote($link_id) . ', ' . $database->quote($my->id) . ', ' . $database->quote( JText::_($report_type2) ) . ', ' . $database->quote($message) . ', ' . $database->quote($now) . ')');

			} else {
				# User is not logged on, store Guest name
				$database->setQuery( "INSERT INTO #__mt_reports "
					.	"( `link_id` , `guest_name` , `subject` , `comment`, created ) "
					.	'VALUES (' . $database->quote($link_id) . ', ' . $database->quote($your_name) . ', ' . $database->quote( JText::_( $report_type2 ) ) . ', ' . $database->quote($message) . ', ' . $database->quote($now) . ')');

			}

			if (!$database->query()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}

			$mainframe->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid"), JText::_( 'Report have been sent' ) );
		}
	}

}

/***
* Report Listing
*/
function claim( $link_id, $option ) {
	global $savantConf;

	$document=& JFactory::getDocument();

	$link = loadLink( $link_id, $savantConf, $fields, $params );
	$document->setTitle(JText::_( 'Claim listing' ) . ': ' . $link->link_name);

	claim_cache( $link, $fields, $params, $option );
}

function claim_cache( $link, $fields, $params, $option ) {
	global $_MAMBOTS, $savantConf, $Itemid, $mtconf;

	$my			=& JFactory::getUser();
	$page = 0;
	$jdate = JFactory::getDate();
	$now = $jdate->toMySQL();

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	if ( $my->id <= 0 ) {
		
		# User is not logged in
		$savant->assign('error_msg', JText::_( 'Please login before claim' ));
		$savant->display( 'page_errorListing.tpl.php' );

	} elseif( $mtconf->get('show_claim') == 0 || empty($link) ) {
		
		echo JText::_( 'NOT_EXIST' );

	} else {

		$savant->display( 'page_claim.tpl.php' );

	}
}

function send_claim( $link_id, $option ) {
	global $Itemid, $mtconf, $mainframe;
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );
	
	$my 		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	
	if ( $mtconf->get('show_claim') == 0 || $my->id <= 0 ) {
		
		echo JText::_( 'NOT_EXIST' );

	} else {
		$database 	=& JFactory::getDBO();
		$my			=& JFactory::getUser();

		$link = new mtLinks( $database );
		$link->load( $link_id );

		$message = JRequest::getVar( 'message',	'', 'post');
		
		$uri =& JURI::getInstance();
		$text = sprintf( JText::_( 'Claim email' ), $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$link_id&Itemid=$Itemid"), $link->link_name, $link_id, $message);

		$subject = JText::_( 'Claim' ) . ' - ' . $mtconf->getjconf('sitename');

		if( mosMailToAdmin( $subject, stripslashes ($text) ) )
		{
			# User is logged on, store user ID
			$database->setQuery( "INSERT INTO #__mt_claims "
				.	"( `link_id` , `user_id` , `comment`, `created` ) "
				.	'VALUES (' . $database->quote($link_id) . ', ' . $database->quote($my->id) . ', ' . $database->quote($message) . ', ' . $database->quote($now) . ')');

			if (!$database->query()) {
				echo "<script> alert('".$database->stderr()."');</script>\n";
				exit();
			}

			$mainframe->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid"), JText::_( 'Claim have been sent' ) );
		}
	}

}

/***
* Delete Listing
*/
function deletelisting( $link_id, $option ) {
	global $savantConf, $Itemid, $mtconf;

	$my		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();

	$link = loadLink( $link_id, $savantConf, $fields, $params );

	if ($mtconf->get('user_allowdelete') && $my->id == $link->user_id && $my->id > 0 ) {

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

		$savant->display( 'page_confirmDelete.tpl.php' );

	} else {
		echo JText::_( 'NOT_EXIST' );
	}

}

function confirmdelete( $link_id, $option ) {
	global $mtconf, $mainframe, $Itemid;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	$nullDate	= $database->getNullDate();

	$database->setQuery( "SELECT * FROM #__mt_links WHERE "
		. "\n link_published='1' AND link_approved > 0 AND link_id='".$link_id."'" 
		. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
		);
	$link = $database->loadObject();

	if ($mtconf->get('user_allowdelete') && $my->id == $link->user_id && $my->id > 0 ) {
		
		$link = new mtLinks( $database );
		$link->load( $link_id );
		
		if ( $mtconf->get('notifyadmin_delete') == 1 ) {

			// Get owner's email
			$database->setQuery( "SELECT email FROM #__users WHERE id = '".$my->id."' LIMIT 1" );
			$my_email = $database->loadResult();

			$subject = JText::_( 'Admin notify delete subject' );
			$body = sprintf(JText::_( 'Admin notify delete msg' ), $link->link_name, $link->link_name, $link->link_id, $my->username, $my_email, $link->link_created);

			mosMailToAdmin( $subject, $body );
			
		}
		
		$link->updateLinkCount( -1 );
		$link->delLink();

		$cache = &JFactory::getCache('com_mtree');
		$cache->clean();

		$mainframe->redirect( JRoute::_("index.php?option=$option&task=viewowner&user_id=".$my->id."&Itemid=$Itemid"), JText::_( 'Listing have been deleted' ) );

	} else {
		echo JText::_( 'NOT_EXIST' );
	}

}

/***
* Review
*/
function writereview( $link_id, $option ) {
	global $savantConf;

	$document=& JFactory::getDocument();

	$link = loadLink( $link_id, $savantConf, $fields, $params );
	$document->setTitle(JText::_( 'Review' ) ." ". $link->link_name);
	
	writereview_cache( $link, $fields, $params, $option );
}

function writereview_cache( $link, $fields, $params, $option ) {
	global $_MAMBOTS, $savantConf, $Itemid, $mtconf;
	
	$database	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();

	if (empty($link) || $mtconf->get('show_review') == 0) {
		echo JText::_( 'NOT_EXIST' );
	} else {

		$page = 0;

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
		
		$user_rev = null;
		$user_rating = 0;
		if( $my->id > 0 ) {
			
			# Check if this user has reviewed this listing previously
			$database->setQuery( "SELECT rev_id FROM #__mt_reviews WHERE link_id = '".$link->link_id."' AND user_id = '".$my->id."'" );
			$user_rev = $database->loadObjectList();

			# Check if this user has voted for this listing previously
			$database->setQuery( "SELECT value FROM #__mt_log WHERE link_id = '".$link->link_id."' AND user_id = '".$my->id."' AND log_type = 'vote' LIMIT 1" );
			$user_rating = $database->loadResult();
		}

		if ( count($user_rev) > 0 &&  $mtconf->get('user_review_once') == '1') {
			# This user has already reviewed this listing
			$savant->assign('error_msg', JText::_( 'You can only review once' ));
			$savant->display( 'page_errorListing.tpl.php' );
		} elseif ( $mtconf->get('user_review') == 1 && $my->id < 1 ) {
			# User is not logged in
			$savant->assign('error_msg', JText::_( 'Please login before review' ));
			$savant->display( 'page_errorListing.tpl.php' );
		} elseif ( 
			$mtconf->get('user_review') == 2 && ( ($my->id > 0 && $my->id == $link->user_id) || $my->id == 0) 
			||
			$mtconf->get('user_review') == -1
			) {
			# Listing owners are not allowed to review
			# Display error when user_review is set to None (-1)
			$savant->assign('error_msg', JText::_( 'You are not allowed to review' ));
			$savant->display( 'page_errorListing.tpl.php' );
		} elseif( $mtconf->get('allow_owner_review_own_listing') == 0 && $my->id == $link->user_id ) {
			# Owner is trying to review own listing
			$savant->assign('error_msg', JText::_( 'You re not allowed to review own listing' ));
			$savant->display( 'page_errorListing.tpl.php' );
		} else {
			# OK. User is allowed to review
			$savant->assign('user_rating', (($user_rating>0)?$user_rating:0));
			$savant->display( 'page_writeReview.tpl.php' );

		}

	}
}

function addreview( $link_id, $option ) {
	global $savantConf, $Itemid, $mtconf, $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();

	# Get the review text
	$rev_text	= JRequest::getString( 'rev_text', '', 'post');
	$rev_title 	= JRequest::getString( 'rev_title', '', 'post');
	$guest_name	= JRequest::getString( 'guest_name', '', 'post');

	$remote_addr = JRequest::getCmd( 'REMOTE_ADDR', '', 'server');

	$link = loadLink( $link_id, $savantConf, $fields, $params );

	if( 
		$mtconf->get('user_rating') == '-1' 
		|| 
		($mtconf->get('user_rating') == 1 && $my->id <= 0) 
		||
		($mtconf->get('user_rating') == 2 && $my->id > 0 && $my->id == $link->user_id )
		||
		$mtconf->get('allow_rating_during_review') == 0
	) {
		$rating = 0;
	} else {
		$rating	= JRequest::getInt('rating', 0, 'post');
	}

	$user_rev = array();
	if( $my->id > 0 ) {
		# Check if this user has reviewed this listing previously
		$database->setQuery( 'SELECT rev_id FROM #__mt_reviews WHERE link_id = ' . $database->quote($link->link_id) . ' AND user_id = ' . $database->quote($my->id) . ' LIMIT 1' );
		$user_rev = $database->loadObjectList();
	} elseif ( $my->id == 0 && $mtconf->get('user_review') == 0 ) {
		# Check log if this user's IP has been used to review this listing before
		$database->setQuery( 'SELECT rev_id FROM #__mt_log WHERE link_id = ' . $database->quote($link->link_id) . ' AND log_ip = ' . $database->quote($remote_addr) . ' AND log_type = \'review\' LIMIT 1' );
		$user_rev = $database->loadObjectList();
	}
	
	if ( count($user_rev) > 0 &&  $mtconf->get('user_review_once') == '1') {
		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
		
		# This user has already reviewed this listing
		$savant->assign('error_msg', JText::_( 'You can only review once' ));
		$savant->display( 'page_errorListing.tpl.php' );

	} elseif( $mtconf->get('allow_owner_review_own_listing') == 0 && $my->id == $link->user_id ) {
		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );
		
		# Owner is trying to review own listing
		$savant->assign('error_msg', JText::_( 'You re not allowed to review own listing' ));
		$savant->display( 'page_errorListing.tpl.php' );

	} elseif (empty($link) || $mtconf->get('show_review') == 0) {
		# Link does not exists, is not published or Show Review is disabled
		echo JText::_( 'NOT_EXIST' );

	} elseif ( 
		$mtconf->get('user_review') == '-1' 
		|| 
		($mtconf->get('user_review') == 1 && $my->id  < 1) 
		|| 
		($mtconf->get('user_review') == 2 && $my->id  > 0 && $my->id == $link->user_id) 
		) {
		# Not accepting review / User is not logged in / Listing owners are not allowed to review
		echo JText::_( 'NOT_EXIST' );

	} elseif ( $rev_text == '' ) {
		# Review text is empty
		echo "<script> alert('".JText::_( 'Please fill in review' )."'); window.history.go(-1); </script>\n";
		exit();
		
	} elseif ( $rev_title == '' ) {
		# Review title is empty
		echo "<script> alert('".JText::_( 'Please fill in title' )."'); window.history.go(-1); </script>\n";
		exit();
		
	} elseif ( 
		$rating == 0 
		&&
		$mtconf->get('require_rating_with_review') 
		&&
		$mtconf->get('allow_rating_during_review') 
		&&
		(
			$mtconf->get('user_rating') == '0'
			||
			($mtconf->get('user_rating') == '1' && $my->id > 0)
			||
			($mtconf->get('user_rating') == '2' && $my->id > 0 && $my->id != $link->user_id)
		)
	) {
		# No rating given
		echo "<script> alert('".JText::_( 'Please fill in rating' )."'); window.history.go(-1); </script>\n";
		exit();

	} else {
		# Everything is ok, add the review
		$jdate = JFactory::getDate();
		$now = $jdate->toMySQL();
		
		if ( $mtconf->get('needapproval_addreview') == 1 ) {
			$rev_approved = 0;
		} else {
			$rev_approved = 1;
			
			// Clean cache only when a review is auto-approved
			$cache = &JFactory::getCache('com_mtree');
			$cache->clean();
		}

		if ( $my->id > 0 )
		{
			# User is logged on, store user ID
			$database->setQuery( 'INSERT INTO #__mt_reviews '
				. '( `link_id` , `user_id` , `rev_title` , `rev_text` , `rev_date` , `rev_approved` ) '
				. 'VALUES (' . $database->quote($link_id) . ', ' . $database->quote($my->id) . ', ' . $database->quote($rev_title) . ', ' . $database->quote($rev_text) . ', ' . $database->quote($now) . ', ' . $database->quote($rev_approved) . ')');
		}
		else
		{
			# User is not logged on, store Guest name
			$database->setQuery( 'INSERT INTO #__mt_reviews '
				. '( `link_id` , `guest_name` , `rev_title` , `rev_text` , `rev_date` , `rev_approved` ) '
				. 'VALUES (' . $database->quote($link_id) . ', ' . $database->quote($guest_name) . ', ' . $database->quote($rev_title) . ', ' . $database->quote($rev_text) . ', ' . $database->quote($now) . ', ' . $database->quote($rev_approved) . ')');
		}

		if (!$database->query())
		{
			echo "<script> alert('".$database->stderr()."');</script>\n";
			exit();
		}
		$rev_id = $database->insertid();

		$mtLog = new mtLog( $database, $remote_addr, $my->id, $link_id, $rev_id );
		$mtLog->logReview();

		if( $rating > 0 && $rating <= 5 ) {

			$users_last_rating = $mtLog->getUserLastRating();

			# User has voted before. 
			# This review will update his vote and recalculate the listing rating while maintaining the number of votes.
			if( $mtconf->get('rate_once') && $users_last_rating > 0 ) {
				if($rating <> $users_last_rating) {
					$new_rating = ((($link->link_rating * $link->link_votes) + ($rating-$users_last_rating) ) / $link->link_votes);
					# Update the new rating
					$database->setQuery( "UPDATE #__mt_links SET link_votes = link_votes + 1, link_rating = '$new_rating' WHERE link_id = '$link_id' ");
					if (!$database->query()) {
						echo "<script> alert('".$database->stderr()."');</script>\n";
						exit();
					}
				}
				$mtLog->deleteVote();

			# User has not voted before. Simply add a new vote for the listing.
			} else {

				$new_rating = ((($link->link_rating * $link->link_votes) + $rating) / ++$link->link_votes);

				# Update #__mt_links table
				$database->setQuery( 'UPDATE #__mt_links '
					. ' SET link_rating = ' . $database->quote($new_rating)
					. ', link_votes = ' . $database->quote($link->link_votes)
					. ' WHERE link_id = ' . $database->quote($link_id));
				if (!$database->query()) {
					echo "<script> alert('".$database->stderr()."');</script>\n";
					exit();
				}

			}

			$mtLog->logVote( $rating );
		}

		# Notify Admin
		if ( $mtconf->get('notifyadmin_newreview') == 1 ) {
			
			$database->setQuery( "SELECT * FROM #__mt_links WHERE link_id = '".$link_id."' LIMIT 1" );
			$link = $database->loadObject();
			
			if ( $my->id > 0 ) {
				$database->setQuery( "SELECT name, username, email FROM #__users WHERE id = '".$my->id."' LIMIT 1" );
				$author = $database->loadObject();
				$author_name = $author->name;
				$author_username = $author->username;
				$author_email = $author->email;
			} else {
				$author_name = $guest_name;
				$author_username = JText::_( 'Guest' );
				$author_email = '';
			}

			if ( $rev_approved == 0 ) {
				$subject = sprintf(JText::_( 'New review email subject waiting approval' ), $link->link_name);
				$msg = sprintf(JText::_( 'Admin new review msg waiting approval' ), $link->link_name, $rev_title, $author_name, $author_username, $author_email, stripslashes(html_entity_decode($rev_text)));
			} else {
				$uri =& JURI::getInstance();
				$subject = sprintf(JText::_( 'New review email subject approved' ), $link->link_name);
				$msg = sprintf(JText::_( 'Admin new review msg approved' ), $link->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$link_id&Itemid=$Itemid"), $rev_title, $author_name, $author_username, $author_email, stripslashes(html_entity_decode($rev_text)));
			}

			mosMailToAdmin( $subject, $msg );

		}

		if ( $mtconf->get('needapproval_addreview') == 1 ) {
			$mainframe->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid"), JText::_( 'Review will be reviewed' ) );
		} else {
			$mainframe->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid"), JText::_( 'Review have been successfully added' ) );
		}

	}
}

/***
* Rating
*/
function rate( $link_id, $option ) {
	global $savantConf;

	$document=& JFactory::getDocument();

	$link = loadLink( $link_id, $savantConf, $fields, $params );
	$document->setTitle(JText::_( 'Rate' ) . $link->link_name);

	rate_cache( $link, $fields, $params, $option );

}

function rate_cache( $link, $fields, $params, $option ) {
	global $_MAMBOTS, $savantConf, $Itemid, $mtconf;

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();

	# User IP Address
	$vote_ip = JRequest::getCmd( 'REMOTE_ADDR', '', 'server');

	if (empty($link)) {
		echo JText::_( 'NOT_EXIST' );
	} else {

		$page = 0;

		# Pathway
		$pathWay = new mtPathWay( $link->cat_id );

		# Savant Template
		$savant = new Savant2($savantConf);
		assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

		# Check if this user has voted before
		if ( $my->id == 0 ) {
			$database->setQuery( 'SELECT log_date FROM #__mt_log WHERE link_id =' . $database->quote($link->link_id) . ' AND log_ip = ' . $database->quote($vote_ip) . ' AND log_type = \'vote\'' );
		} else {
			$database->setQuery( 'SELECT log_date FROM #__mt_log WHERE link_id =' . $database->quote($link->link_id) . ' AND user_id = ' . $database->quote($my->id) . ' AND log_type = \'vote\'' );
		}
		
		$voted = false;
		$voted = ($database->loadResult() <> '') ? true : false;

		if ( $mtconf->get('user_rating') == '1' && $my->id < 1) {
			# Error. Please login before you can vote
			$savant->assign('error_msg', JText::_( 'Please login before rate' ));
			$savant->display( 'page_errorListing.tpl.php' );
			
		} elseif( $mtconf->get('user_rating') == '2' && $my->id > 0 && $my->id == $link->user_id ) {
			# Error. Listing owner is not allow to rate
			$savant->assign('error_msg', JText::_( 'You are not allowed to rate' ));
			$savant->display( 'page_errorListing.tpl.php' );

		} elseif ( $voted && $mtconf->get('rate_once') == '1') {
			# This user has already voted this listing
			$savant->assign('error_msg', JText::_( 'You can only rate once' ));
			$savant->display( 'page_errorListing.tpl.php' );

		} elseif( $mtconf->get('allow_owner_rate_own_listing') == 0 && $my->id == $link->user_id ) {
			# Owner is trying to vote own listing
			$savant->assign('error_msg', JText::_( 'You re not allowed to rate own listing' ));
			$savant->display( 'page_errorListing.tpl.php' );

		} else {
			# OK. User is logged in
			$savant->display( 'page_rating.tpl.php' );

		}

	}
}

function addrating( $link_id, $option ) {
	$database =& JFactory::getDBO();

	# Get the rating
	$rating	= JRequest::getInt('rating', 0);

	$result = saverating( $link_id, $rating );

	$cache = &JFactory::getCache('com_mtree');
	$cache->clean();

	if( $result ) {
		$database->setQuery( "SELECT link_votes FROM #__mt_links WHERE link_id = '".$link_id."' LIMIT 1" );
		$total_votes = $database->loadResult();
		echo JText::_( 'Thanks for rating' ) . '|' . $total_votes . ' ' . strtolower(JText::_( 'Votes' ));
	} else {
		echo 'NA';
	}

}

function saverating( $link_id, $rating ) {
	global $savantConf, $Itemid, $mtconf;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	$nullDate	= $database->getNullDate();

	$database->setQuery( "SELECT * FROM #__mt_links WHERE "
		.	"\n	link_published='1' AND link_approved > 0 AND link_id='".$link_id."'" 
		. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
		);
	$link = $database->loadObject();

	# User IP Address
	$vote_ip = JRequest::getCmd( 'REMOTE_ADDR', '', 'server');

	if (empty($link)) {
		# Link does not exists, or is not published
		echo JText::_( 'NOT_EXIST' );
		return false;
	
	} elseif ($mtconf->get('user_rating') == '-1') {
		# Rating is disabled
		echo JText::_( 'NOT_EXIST' );
		return false;
	
	} elseif ( 
		$mtconf->get('user_rating') == '1' && $my->id < 1
		||
		($mtconf->get('user_rating') == '2' && $my->id == 0)
	) {
		# User is not logged in
		echo JText::_( 'NOT_EXIST' );
		return false;

	} elseif ( $mtconf->get('user_rating') == '2' && $my->id > 0 && $my->id == $link->user_id ) {
		# Listing owners are not allowed to rate
		echo JText::_( 'You are not allowed to rate' );
		return false;
		
	} elseif ( $rating <= 0 || $rating > 5 ) {
		# Invalid rating. User did not fill in rating, or attempt misuse
		echo JText::_( 'Please select a rating' );
		return false;
		
	} elseif( $mtconf->get('allow_owner_rate_own_listing') == 0 && $my->id == $link->user_id ) {
		# Owner is trying to vote own listing
		echo JText::_( 'You re not allowed to rate own listing' );

	} else {

		# Everything is ok, add the rating
		$jdate = JFactory::getDate();
		$now = $jdate->toMySQL();

		if ( $my->id < 1 ) $my->id = 0;

		# Check if this user has voted before
		if ( $my->id == 0 ) {
			$database->setQuery( 'SELECT log_date FROM #__mt_log WHERE link_id =' . $database->quote($link_id) . ' AND log_ip = ' . $database->quote($vote_ip) . ' AND log_type = \'vote\'' );
		} else {
			$database->setQuery( 'SELECT log_date FROM #__mt_log WHERE link_id =' . $database->quote($link_id) . ' AND user_id = ' . $database->quote($my->id) . ' AND log_type = \'vote\'' );
		}
		
		$voted = false;
		$voted = ($database->loadResult() <> '') ? true : false;
		
		if ( !$voted || ($voted && !$mtconf->get('rate_once')) ) {

			$mtLog = new mtLog( $database, $vote_ip, $my->id, $link_id );
			$mtLog->logVote( $rating );

			$new_rating = ((($link->link_rating * $link->link_votes) + $rating) / ++$link->link_votes);

			# Update #__mt_links table
			$database->setQuery( "UPDATE #__mt_links "
				.	" SET link_rating = '$new_rating', link_votes = '$link->link_votes' "
				.	"WHERE link_id = '$link_id' ");
			if (!$database->query()) {
				echo $database->stderr();
				exit();
				return false;
			}

			return true;

		} else {
			return false;
		}

	}

}

function fav( $link_id, $action, $option ) {
	$database =& JFactory::getDBO();
	$result = savefav( $link_id, $action, $option );

	if( $result ) {
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_favourites WHERE link_id = '".$link_id."'" );
		$total_fav = $database->loadResult();
		if( !is_numeric($total_fav) || $total_fav < 0 ) {
			$total_fav = 0;
		}
		if( $action == 1 ) {
			echo JText::_( 'Added as favourite' ) . '|' . $total_fav;
		} else {
			echo JText::_( 'Favourite removed' ) . '|' . $total_fav;
		}
	} else {
		echo 'NA';
	}
}

function savefav( $link_id, $action ) {
	global $savantConf, $Itemid, $mtconf;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();

	if($mtconf->get('show_favourite') == 0) {
		return false;
	}

	$jdate = JFactory::getDate();
	$now = $jdate->toMySQL();
	$nullDate	= $database->getNullDate();

	if ( $my->id < 1 ) $my->id = 0;

	$database->setQuery( "SELECT * FROM #__mt_links WHERE "
		.	"\n	link_published='1' AND link_approved > 0 AND link_id='".$link_id."'" 
		. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
		);
	$link = $database->loadObject();

	$database->setQuery( "SELECT COUNT(*) FROM #__mt_favourites WHERE user_id = '".$my->id."' AND link_id = '".$link_id."' LIMIT 1" );
	if( $action == 1 ) {
		# If user is adding a favourite, make sure the link has not been added to the user's favourite before
		if( $database->loadResult() > 0 ) {
			return false;
		}
	} else {
		# If user is removing a favourite, make sure he has the favourite
		if( $database->loadResult() < 1 ) {
			return false;
		}
	}

	# User IP Address
	$vote_ip = JRequest::getCmd( 'REMOTE_ADDR', '', 'server');

	if (empty($link)) {
		# Link does not exists, or is not published
		echo JText::_( 'NOT_EXIST' );
		return false;

	} elseif ( $my->id < 1) {
		# User is not logged in
		echo JText::_( 'NOT_EXIST' );
		return false;
	
	} elseif ( $action != -1 && $action != 1 ) {
		echo JText::_( 'NOT_EXIST' );
		return false;
		
	} else {

		# Everything is ok, add the rating

		$mtLog = new mtLog( $database, $vote_ip, $my->id, $link_id );
		$mtLog->logFav($action);

		# Add favourite
		if( $action == 1 ) {
			$database->setQuery( "INSERT INTO #__mt_favourites "
				.	"(`user_id`, `link_id`, `fav_date`) "
				.	"VALUES ( "
				.	"'" . $my->id . "',"
				.	"'" . $link_id . "',"
				.	"'" . $now . "'"
				.	")");
		} else {
			$database->setQuery( "DELETE FROM #__mt_favourites WHERE user_id = '" . $my->id . "' AND link_id = '" . $link_id . "' LIMIT 1" );
		}
		if (!$database->query()) {
			echo $database->stderr();
			return false;
		}

		return true;

	}

}

/***
* Vote Review - Process the vote and redirect to the listing with message
* @param int review id
* @param int review vote. 1 = helpful, -1 = not helpful
* @param string option
*/
function votereview( $rev_id, $rev_vote, $option ) {
	$database =& JFactory::getDBO();

	$database->setQuery( "SELECT * FROM #__mt_reviews WHERE rev_approved='1' AND rev_id='".$rev_id."' LIMIT 1" );
	$review = $database->loadObject();
	$result = savevotereview( $review, $rev_vote, $option );
	
	if( $result ) {
		$return = sprintf( JText::_( 'People find this review helpful' ), (($rev_vote == 1)? $review->vote_helpful +1:$review->vote_helpful), ($review->vote_total +1) );
		$return .= '|'.JText::_( 'Thanks for your vote' );
		echo $return;
	} else {
		echo 'NA';
	}

}

/**
* Save the vote review to database
* @param object review object
* @param int review vote. 1 = helpful, -1 = not helpful
* @param string option
* @return TRUE=save is successful, FALSE=save is not successful or vote has been recorded in the past
*/
function savevotereview( $review, $rev_vote, $option ) {
	global $mtconf;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	
	# User IP Address
	$vote_ip = JRequest::getCmd( 'REMOTE_ADDR', '', 'server');

	if (empty($review)) {
		# Review does not exists, or is not published
		echo JText::_( 'NOT_EXIST' );
		return false;

	} elseif ( $mtconf->get('user_vote_review') == '0' ) {
		# Feature has been disabled
		echo JText::_( 'NOT_EXIST' );
		return false;

	} elseif( $my->id < 1) {
		# User is not logged in
		echo JText::_( 'NOT_EXIST' );
		return false;

	} elseif ( $rev_vote <> -1 && $rev_vote <> 1 ) {
		# Invalid review vote. User did not fill in rating, or attempt misuse
		echo JText::_( 'NOT_EXIST' );
		return false;
		
	} else {

		# Everything is ok, add the rating
		$jdate = JFactory::getDate();
		$now = $jdate->toMySQL();

		if ( $my->id < 1 ) $my->id = 0;

		# Check if this user has voted before
		if ( $my->id == 0 ) {
			$database->setQuery( 'SELECT log_date FROM #__mt_log WHERE rev_id =' . $database->quote($review->rev_id) . ' AND log_ip = ' . $database->quote($vote_ip) . ' AND log_type = \'votereview\'' );
		} else {
			$database->setQuery( 'SELECT log_date FROM #__mt_log WHERE rev_id =' . $database->quote($review->rev_id) . ' AND user_id = ' . $database->quote($my->id) . ' AND log_type = \'votereview\'' );
		}
		
		$voted = false;
		$voted = ($database->loadResult() <> '') ? true : false;
		
		if ( !$voted ) {

			# Update #__mt_log table
			$database->setQuery( 'INSERT INTO #__mt_log '
				. ' ( `log_ip` , `log_type`, `user_id` , `log_date` , `link_id`, `rev_id`, `value` )'
				. ' VALUES ( ' . $database->quote($vote_ip) . ', ' . $database->quote('votereview') . ', ' . $database->quote($my->id) . ', ' . $database->quote($now) . ', ' . $database->quote($review->link_id) . ', ' . $database->quote($review->rev_id) . ', ' . $database->quote( ($rev_vote == -1) ? '-1':'1' ) . ')');
			if (!$database->query()) {
				echo $database->stderr();
				return false;
			}

			# Update review
			$database->setQuery( 'UPDATE #__mt_reviews '
				. 'SET vote_total = vote_total + 1' . ( ($rev_vote == 1) ? ', vote_helpful = vote_helpful + 1 ':' ' )
				. 'WHERE rev_id = \''.$review->rev_id.'\' LIMIT 1'
				);
			if (!$database->query()) {
				echo $database->stderr();
				return false;
			}

			return true;

		} else {
			return false;
		}

	}

}

/***
* Report Review
*/
function reportreview( $rev_id, $option ) {
	global $savantConf, $mtconf;
	
	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$document	=& JFactory::getDocument();
	
	if( $mtconf->get('user_report_review') == -1 || ($mtconf->get('user_report_review') == 1 && $my->id == 0) ) {
		echo JText::_( 'NOT_EXIST' );
	} else {
		$database->setQuery( "SELECT r.*, u.username, l.value AS rating FROM #__mt_reviews AS r "
			.	"\nLEFT JOIN #__users AS u ON u.id = r.user_id"
			.	"\nLEFT JOIN #__mt_log AS l ON l.user_id = r.user_id AND l.link_id = r.link_id AND log_type = 'vote'"
			.	"\nWHERE r.rev_id = '".$rev_id."' LIMIT 1" );
		$review = $database->loadObject();

		if( $review->link_id > 0 ) {

			$link = loadLink( $review->link_id, $savantConf, $fields, $params );
			$document->setTitle(JText::_( 'Report review' ) . ': ' . $review->rev_title);

			reportreview_cache( $review, $link, $fields, $params, $option );
	
		} else {
			echo JText::_( 'NOT_EXIST' );
		}
	}
}

function reportreview_cache( $review, $link, $fields, $params, $option ) {
	global $savantConf;

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	$savant->assign('review', $review);

	if (empty($link)) {
		echo JText::_( 'NOT_EXIST' );
	} else {
		$savant->display( 'page_reportReview.tpl.php' );
	}

}

function send_reportreview( $rev_id, $option ) {
	global $Itemid, $mtconf, $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	
	if( $mtconf->get('user_report_review') == -1 || ($mtconf->get('user_report_review') == 1 && $my->id == 0) ) {
		
		echo JText::_( 'NOT_EXIST' );

	} else {

		$database->setQuery( "SELECT l.link_id, rev_title, rev_text, l.link_name FROM #__mt_reviews AS r "
			. "\n LEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			. "\n WHERE rev_id ='".$rev_id."' AND r.rev_approved = 1 AND l.link_published = 1 AND l.link_approved = 1" 
			. "\n LIMIT 1"
			);
		$link = $database->loadObject();

		if( count($link) == 1 && $link->link_id > 0 ) {
			
			if( $my->id > 0 ) {
				$your_name = $my->name.' ('.$my->username.')';
			} else {
				$your_name = JRequest::getVar( 'your_name', '', 'post');
			}

			$message = JRequest::getVar( 'message', '', 'post');
			
			$uri =& JURI::getInstance();
			$text = sprintf( JText::_( 'Report review email' ), $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$link->link_id&Itemid=$Itemid"), $your_name, $message, $link->rev_title, $link->link_name, $link->rev_text, $link->rev_text );

			$subject = JText::_( 'Report review' ) . ' - ' . $link->rev_title;

			if( mosMailToAdmin( $subject, $text ) )
			{
				if( $my->id > 0 )  {
					# User is logged on, store user ID
					$database->setQuery( 'INSERT INTO #__mt_reports '
						. '( `link_id` , `rev_id` , `user_id` , `comment`, created ) '
						. 'VALUES (' . $database->quote($link->link_id) . ', ' . $database->quote($rev_id) . ', ' . $database->quote($my->id) . ', ' . $database->quote($message) . ', ' . $database->quote($now) . ')');

				} else {
					# User is not logged on, store Guest name
					$database->setQuery( 'INSERT INTO #__mt_reports '
						. ' ( `link_id` , `rev_id` , `guest_name` , `comment`, created ) '
						. ' VALUES (' . $database->quote($link->link_id) . ', ' . $database->quote($rev_id) . ', ' . $database->quote($your_name) . ', ' . $database->quote($message) . ', ' . $database->quote($now) . ')');

				}

				if (!$database->query()) {
					echo "<script> alert('".$database->stderr()."');</script>\n";
					exit();
				}

				$mainframe->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link->link_id&Itemid=$Itemid"), JText::_( 'Report have been sent' ));
			}

		}

	}

}

/***
* Reply Review
*/
function replyreview( $rev_id, $option ) {
	global $savantConf, $mtconf;

	$database	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$document	=& JFactory::getDocument();
	
	$database->setQuery( "SELECT r.*, u.username, l.value AS rating FROM #__mt_reviews AS r "
		.	"\nLEFT JOIN #__users AS u ON u.id = r.user_id"
		.	"\nLEFT JOIN #__mt_log AS l ON l.user_id = r.user_id AND l.link_id = r.link_id AND log_type = 'vote'"
		.	"\nWHERE r.rev_id = '".$rev_id."' LIMIT 1" );
	$review = $database->loadObject();
	
	# Replying review are restricted to the listing owner only.
	if( isset($review) && $review->link_id > 0 && $my->id > 0 && $mtconf->get('owner_reply_review') ) {

		$link = loadLink( $review->link_id, $savantConf, $fields, $params );

		if( $link->user_id == $my->id ) {
			$document->setTitle(JText::_( 'Reply review' ) . ': ' . $review->rev_title);
			replyreview_cache( $review, $link, $fields, $params, $option );
		} else {
			echo JText::_( 'NOT_EXIST' );
		}

	} else {
		echo JText::_( 'NOT_EXIST' );
	}
}

function replyreview_cache( $review, $link, $fields, $params, $option ) {
	global $savantConf;

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	$savant->assign('review', $review);

	if (empty($link)) {
		echo JText::_( 'NOT_EXIST' );
	} elseif ( !empty($review->ownersreply_text) ) {
		$savant->assign('error_msg', JText::_( 'You can only reply a review once' ));
		$savant->display( 'page_errorListing.tpl.php' );
	} else {
		$savant->display( 'page_replyReview.tpl.php' );
	}

}

function send_replyreview( $rev_id, $option ) {
	global $Itemid, $mtconf, $mainframe;
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$database	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	
	$message = JRequest::getVar( 'message', '', 'post');

	if ( !$mtconf->get('owner_reply_review') ) {

		echo JText::_( 'NOT_EXIST' );

	} else {

		if ( $message == '' ) {
			# Reply text is empty
			echo "<script> alert('".JText::_( 'Please fill in reply' )."'); window.history.go(-1); </script>\n";
			exit();
		}

		if ( $mtconf->get('needapproval_replyreview') == 1 ) {
			$rr_approved = 0;
		} else {
			$rr_approved = 1;
		}

		$database->setQuery( "SELECT l.link_id, l.user_id AS link_owner_user_id, rev_title, rev_text, l.link_name, r.ownersreply_text FROM #__mt_reviews AS r "
			. "\n LEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
			. "\n WHERE rev_id ='".$rev_id."' AND r.rev_approved = 1 AND l.link_published = 1 AND l.link_approved = 1" 
			. "\n LIMIT 1"
			);
		$link = $database->loadObject();

		if( count($link) == 1 && empty($link->ownersreply_text) && $link->link_id > 0 && $my->id > 0 && $link->link_owner_user_id == $my->id ) {

			# Notify Admin
			if ( $my->id > 0 ) {
				$database->setQuery( "SELECT name, username, email FROM #__users WHERE id = '".$my->id."' LIMIT 1" );
				$author = $database->loadObject();
				$author_name = $author->name;
				$author_username = $author->username;
				$author_email = $author->email;
			} else {
				$author_name = $guest_name;
				$author_username = JText::_( 'Guest' );
				$author_email = '';
			}

			if ( $rr_approved == 0 ) {
				$subject = sprintf(JText::_( 'New review reply email subject waiting approval' ), $link->link_name);
				$msg = sprintf( JText::_( 'Admin new review reply msg waiting approval' ), $my->name, $message, $link->rev_title, $link->link_name, $link->rev_text );
			} else {
				$subject = sprintf(JText::_( 'New review reply email subject approved' ), $link->link_name);
				$msg = sprintf( JText::_( 'Admin new review reply msg approved' ), $my->name, $message, $link->rev_title, $author_name, $author_username, $author_email, $link->rev_text );
			}

			if( mosMailToAdmin( $subject, $msg ) )
			{
				$database->setQuery( 'UPDATE #__mt_reviews SET ownersreply_text = ' . $database->quote($message) . ', ownersreply_date = ' . $database->quote($now) . ', ownersreply_approved = ' . $database->quote($rr_approved) . ' WHERE rev_id = ' . $database->quote($rev_id) );

				if (!$database->query()) {
					echo "<script> alert('".$database->stderr()."');</script>\n";
					exit();
				}

				$remote_addr = JRequest::getCmd( 'REMOTE_ADDR', '', 'server');
				$mtLog = new mtLog( $database, $remote_addr, $my->id, $link->link_id, $rev_id );
				$mtLog->logReplyReview();

				if ( $mtconf->get('needapproval_replyreview') == 1 ) {
					$mainframe->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link->link_id&Itemid=$Itemid"), JText::_( 'Reply review will be reviewed' ));
				} else {
					$mainframe->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link->link_id&Itemid=$Itemid"), JText::_( 'Reply review have been successfully added' ));
				}
			}

		} else {

			echo JText::_( 'NOT_EXIST' );

		}
	}

}

/***
* Recommend to Friend
*/

function recommend( $link_id, $option ) {
	global $savantConf;

	$document=& JFactory::getDocument();

	$link = loadLink( $link_id, $savantConf, $fields, $params );
	$document->setTitle(JText::_( 'Recommend' ) ." ". $link->link_name);

	recommend_cache( $link, $fields, $params, $option );

}

function recommend_cache( $link, $fields, $params, $option ) {
	global $_MAMBOTS, $savantConf, $Itemid, $mtconf;

	$my		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();

	$page = 0;

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	if ( empty($link) || $mtconf->get('user_recommend') == -1 ) {
		echo JText::_( 'NOT_EXIST' );

	} elseif ( $mtconf->get('user_recommend') == '1' && $my->id < 1 ) {
		# Error. Please login before you can recommend
		$savant->assign('error_msg', JText::_( 'Please login before recommend' ));
		$savant->display( 'page_errorListing.tpl.php' );

	} else {
		$savant->display( 'page_recommend.tpl.php' );
	}

}

function send_recommend( $link_id, $option ) {
	global $Itemid, $mtconf, $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$my			=& JFactory::getUser();

	if ( $mtconf->get('show_recommend') == 0 || ($mtconf->get('user_recommend') == '1' && $my->id < 1) || $mtconf->get('user_recommend') == -1 ) {
		echo JText::_( 'NOT_EXIST' );

	} else {

		$your_name		= JRequest::getVar( 'your_name', '', 'post');
		$your_email		= JRequest::getVar( 'your_email', '', 'post');
		$friend_name	= JRequest::getVar( 'friend_name', '', 'post');
		$friend_email	= JRequest::getVar( 'friend_email', '', 'post');

		if (!$your_email || !$friend_email || (is_email($your_email)==false) || (is_email($friend_email)==false) ){
			echo "<script>alert (\"".JText::_( 'You must enter valid email' )."\"); window.history.go(-1);</script>";
			exit(0);
		}

		$uri =& JURI::getInstance();
		$msg = sprintf( JText::_( 'Recommend msg' ),
			$mtconf->getjconf('sitename'),
			$your_name,
			$your_email,
			$uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_('index.php?option=com_mtree&task=viewlink&link_id='.$link_id.'&Itemid='.$Itemid, false)
			);

		$subject = sprintf(JText::_( 'Recommend subject' ), $your_name);

		if  (!validateInputs( $friend_email, $subject, $msg ) ) {
			$document =& JFactory::getDocument();
			JError::raiseWarning( 0, $document->getError() );
			return false;
		} else {
			JUTility::sendMail( $your_email, $your_name, $friend_email, $subject, wordwrap($msg) );
			$mainframe->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid"), sprintf(JText::_( 'Recommend email have been sent' ), $friend_name) );
		}
	}
}

/***
* Contact Owner
*/

function contact( $link_id, $option ) {
	global $savantConf;

	$document=& JFactory::getDocument();

	$link = loadLink( $link_id, $savantConf, $fields, $params );
	$document->setTitle(JText::_( 'Contact2' ) . $link->link_name);

	contact_cache( $link, $fields, $params, $option );

}

function contact_cache( $link, $fields, $params, $option ) {
	global $_MAMBOTS, $savantConf, $Itemid, $mtconf;

	$my		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	$page = 0;

	# Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonViewlinkVar( $savant, $link, $fields, $pathWay, $params );

	if (
		empty($link)
		OR
		$mtconf->get( 'show_contact' ) == 0
		OR
		$mtconf->get( 'use_owner_email' ) == 0 && empty($link->email)
		OR
		$mtconf->get( 'user_contact' ) == -1
	) {
		echo JText::_( 'NOT_EXIST' );

	} elseif ( $mtconf->get('user_contact') == '1' && $my->id < 1 ) {
		# Error. Please login before you can contact the owner
		$savant->assign('error_msg', JText::_( 'Please login before contact' ));
		$savant->display( 'page_errorListing.tpl.php' );

	} else {
		$savant->display( 'page_contactOwner.tpl.php' );
	}

}

function send_contact( $link_id, $option ) {
	global $Itemid, $mtconf, $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$database	=& JFactory::getDBO();
	$my			=& JFactory::getUser();

	$link = new mtLinks( $database );
	$link->load( $link_id );

	if ( 
		$mtconf->get('show_contact') == 0 
		OR
		($mtconf->get('user_contact') == '1' && $my->id < 1)
		OR
		$mtconf->get( 'use_owner_email' ) == 0 && empty($link->email)
		OR
		$mtconf->get( 'user_contact' ) == -1
	) {
		echo JText::_( 'NOT_EXIST' );

	} else {

		$your_name	= JRequest::getVar( 'your_name', '', 'post');
		$your_email	= JRequest::getVar( 'your_email', '', 'post');

		$uri =& JURI::getInstance();

		$message = sprintf( JText::_( 'Contact message' ), $your_name, $your_email, $link->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_( "index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid", false ), JRequest::getVar( 'message', '', 'post') );

		if (!$your_email || (is_email($your_email)==false) ){
			echo "<script>alert (\"".JText::_( 'You must enter valid email' )."\"); window.history.go(-1);</script>";
			exit(0);
		}

		$subject = sprintf(JText::_( 'Contact subject' ), $mtconf->getjconf('sitename'), $link->link_name);
		
		if( empty($link->email) ) {
			$database->setQuery( 'SELECT email FROM #__users WHERE id = '.$link->user_id.' LIMIT 1' );
			$email = $database->loadResult();
		} else {
			$email = $link->email;
		}

		if  (!validateInputs( $email, $subject, $message ) ) {
			$document =& JFactory::getDocument();
			JError::raiseWarning( 0, $document->getError() );
			return false;
		} else {
			JUTility::sendMail( $your_email, $your_name, $email, $subject, wordwrap($message) );
			$mainframe->redirect( JRoute::_("index.php?option=$option&task=viewlink&link_id=$link_id&Itemid=$Itemid"), JText::_( 'Contact email have been sent' ));
		}
	}

}

function is_email($email){
	$rBool=false;

	if(preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $email)){
		$rBool=true;
	}
	return $rBool;
}

/***
* Edit Listing
*/
function editlisting( $link_id, $option ) {
	global $savantConf, $Itemid, $mtconf;

	$database	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$document	=& JFactory::getDocument();

	require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'mfields.class.php' );

	# Get cat_id if user is adding new listing. 
	$cat_id	= JRequest::getInt('cat_id', 0);
	
	// This var retrieve the link_id for adding listing
	$link_id_passfromurl = JRequest::getInt('link_id', 0);
	
	if ( $link_id_passfromurl > 0 && $cat_id == 0 ) {
		$database->setQuery( "SELECT cat_id FROM (#__mt_links AS l, #__mt_cl AS cl) WHERE l.link_id ='".$link_id_passfromurl."' AND cl.link_id = l.link_id" );
		$cat_id = $database->loadResult();
	}

	$link = new mtLinks( $database );

	# Do not allow Guest to edit listing
	if ( $link_id > 0 && $my->id <= 0 ) {
		$link->load( 0 );
	} else {
		$link->load( $link_id );
	}

	# Load all published CORE & custom fields
	$sql = "SELECT cf.*, " . ($link_id ? $link_id : 0) . " AS link_id, cfv.value AS value, cfv.attachment, cfv.counter, ft.ft_class FROM #__mt_customfields AS cf "
		.	"\nLEFT JOIN #__mt_cfvalues AS cfv ON cf.cf_id=cfv.cf_id AND cfv.link_id = " . $link_id
		.	"\nLEFT JOIN #__mt_fieldtypes AS ft ON ft.field_type=cf.field_type"
		.	"\nWHERE cf.hidden ='0' AND cf.published='1' ORDER BY ordering ASC";
	$database->setQuery($sql);

	$fields = new mFields();
	$fields->setCoresValue( $link->link_name, $link->link_desc, $link->address, $link->city, $link->state, $link->country, $link->postcode, $link->telephone, $link->fax, $link->email, $link->website, $link->price, $link->link_hits, $link->link_votes, $link->link_rating, $link->link_featured, $link->link_created, $link->link_modified, $link->link_visited, $link->publish_up, $link->publish_down, $link->metakey, $link->metadesc, $link->user_id, '' );
	$fields->loadFields($database->loadObjectList());
	
	# Load images
	$database->setQuery( "SELECT img_id, filename FROM #__mt_images WHERE link_id = '" . $link_id . "' ORDER BY ordering ASC" );
	$images = $database->loadObjectList();
	
	# Get current category's template
	$database->setQuery( "SELECT cat_name, cat_parent, cat_template, metakey, metadesc FROM #__mt_cats WHERE cat_id='".$cat_id."' AND cat_published='1' LIMIT 1" );
	$cat = $database->loadObject();
	
	if( $link->link_id == 0 )
	{
		if( $cat ) {
			$document->setTitle(sprintf(JText::_( 'Add listing2' ), $cat->cat_name));
		} else {
			$document->setTitle(JText::_( 'Add listing' ));
		}
	} else {
		$document->setTitle(sprintf(JText::_( 'Edit listing2' ), $link->link_name));
	}

	if ( isset($cat->cat_template) && $cat->cat_template <> '' ) {
		loadCustomTemplate(null,$savantConf,$cat->cat_template);
	}

	# Get other categories
	$database->setQuery( "SELECT cl.cat_id FROM #__mt_cl AS cl WHERE cl.link_id = '$link_id' AND cl.main = '0'");
	$other_cats = $database->loadResultArray();

	# Pathway
	$pathWay = new mtPathWay( $cat_id );
	$pw_cats = $pathWay->getPathWayWithCurrentCat( $cat_id );
	$pathWayToCurrentCat = '';
	$mtCats = new mtCats($database);
	$pathWayToCurrentCat = ' <a href="'.JRoute::_("index.php?option=com_mtree&task=listcats&Itemid=".$Itemid).'">'.JText::_( 'Root' )."</a>";
	foreach( $pw_cats AS $pw_cat ) {
		$pathWayToCurrentCat .= JText::_( 'Arrow' ) .' <a href="'.JRoute::_("index.php?option=com_mtree&task=listcats&cat_id=".$pw_cat."&Itemid=".$Itemid).'">'.$mtCats->getName($pw_cat)."</a>";
	}

	# Savant Template
	$savant = new Savant2($savantConf);

	assignCommonVar($savant);
	$savant->assign('pathway', $pathWay);
	$savant->assign('pathWayToCurrentCat',$pathWayToCurrentCat);
	$savant->assign('cat_id', (($link_id == 0) ? $cat_id : $link->cat_id ) );
	$savant->assign('other_cats', $other_cats );
	$savant->assignRef('link', $link);
	$savant->assignRef('fields',$fields);
	$savant->assignRef('images',$images);

	if( $mtconf->get('image_maxsize') > 1048576 ) {
		$savant->assign('image_size_limit', round(($mtconf->get('image_maxsize')/1048576),1) . 'MB' );
	} else {
		$savant->assign('image_size_limit', round($mtconf->get('image_maxsize')/1024) . 'KB' );
	}

	# Check permission
	if ( ($mtconf->get('user_addlisting') == 1 && $my->id < 1) || ($link_id > 0 && $my->id == 0) ) {

		$savant->assign('error_msg', JText::_( 'Please login before addlisting' ));
		$savant->display( 'page_error.tpl.php' );
	
	} elseif( ($link_id > 0 && $my->id <> $link->user_id) || ($mtconf->get('user_allowmodify') == 0 && $link_id > 0) || ($mtconf->get('user_addlisting') == -1 && $link_id == 0) || ($mtconf->get('user_addlisting') == 1 && $my->id == 0) ) {
		
		echo JText::_( 'NOT_EXIST' );

	} else {
		// OK, you can edit
		$database->setQuery( "SELECT CONCAT('cust_',cf_id) as varname, caption As value, field_type, prefix_text_mod, suffix_text_mod FROM #__mt_customfields WHERE hidden <> '1' AND published = '1'" );
		$custom_fields = $database->loadObjectList('varname');
		$savant->assign('custom_fields', $custom_fields);

		# Load custom fields' value from #__mt_cfvalues to $link
		$database->setQuery( "SELECT CONCAT('cust_',cf_id) as varname, value FROM #__mt_cfvalues WHERE link_id = '".$link_id."'" );
		$cfvalues = $database->loadObjectList('varname');

		foreach( $custom_fields as $cfkey => $value )
		{
			if( isset($cfvalues[$cfkey]) ) {
				$savant->custom_data[$cfkey] = $cfvalues[$cfkey]->value;
			} else {
				$savant->custom_data[$cfkey] = '';
			}
		}

		// Get category's tree
		if($mtconf->get('allow_changing_cats_in_addlisting')) {
			getCatsSelectlist( $cat_id, $cat_tree, 1 );
			if ( $cat_id > 0 ) {
				$cat_options[] = JHTML::_('select.option', $cat->cat_parent, JText::_( 'Arrow back' ));
				
			}
			
			if( $mtconf->get('allow_listings_submission_in_root') ) {
				$cat_options[] = JHTML::_('select.option', '0', JText::_( 'Root' ));
			}
			if(count($cat_tree)>0) {
				foreach( $cat_tree AS $ct ) {
					if( $ct["cat_allow_submission"] == 1 ) {
						$cat_options[] = JHTML::_('select.option', $ct["cat_id"], str_repeat("&nbsp;",($ct["level"]*3)) .(($ct["level"]>0) ? " -":''). $ct["cat_name"]);
					} else {
						$cat_options[] = JHTML::_('select.option', ($ct["cat_id"]*-1), str_repeat("&nbsp;",($ct["level"]*3)) .(($ct["level"]>0) ? " -":''). "(".$ct["cat_name"].")");
					}
				}
			}
			$catlist = JHTML::_('select.genericlist', $cat_options, 'new_cat_id', 'size=8 class="inputbox"', 'value', 'text', '', 'browsecat' );
			$savant->assignRef('catlist', $catlist );
		}
		
		// Give warning is there is already a pending approval for modification.
		if ( $link_id > 0 ) {
			$database->setQuery( "SELECT link_id FROM #__mt_links WHERE link_approved = '".(-1*$link_id)."'" );
			if ( $database->loadResult() > 0 ) {
				$savant->assign('warn_duplicate', 1);
			} else {
				$savant->assign('warn_duplicate', 0);
			}
		}
		$savant->assign('pathWay', $pathWay);
		$savant->display( 'page_addListing.tpl.php' );
	}
}

function savelisting( $option ) {
	global $Itemid, $mtconf, $mainframe, $link_id;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$database	=& JFactory::getDBO();
	$my		=& JFactory::getUser();

	require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'mfields.class.php' );
	require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'tools.mtree.php' );
	
	$raw_filenames = array();
	
	# Get cat_id / remove_image / link_image
	$cat_id	= JRequest::getInt('cat_id', 0);
	
	$other_cats = explode(',', JRequest::getString('other_cats', null, 'post'));
	JArrayHelper::toInteger($other_cats);
	if( isset($other_cats) && empty($other_cats[0]) ) {
		$other_cats = array();
	}
	
	# Check if any malicious user is trying to submit link
	if ( 
		($mtconf->get('user_addlisting') == 1 && $my->id < 1 && $link_id == 0) 
		|| 
		($mtconf->get('user_addlisting') == -1 && $link_id == 0)
		||
		($mtconf->get('user_allowmodify') == 0 && $link_id > 0) 
		) {
		
		JError::raiseError( 403, JText::_( 'Access Forbidden' ) );

	} else {
	# Allowed
		
		$row = new mtLinks( $database );
		$post = JRequest::get( 'post' );
		if (!@$row->bind( $post )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$isNew = ($row->link_id < 1) ? 1 : 0;

		# Assignment for new record
		if ($isNew) {

			$jdate				= JFactory::getDate();
			$row->link_created 	= $jdate->toMySQL();
			$row->publish_up 	= $jdate->toMySQL();
			$row->ordering 		= 999;

			// Set an expire date for listing if enabled in configuration
			if( $mtconf->get('days_to_expire') > 0 )
			{
				$jdate->setOffset(intval($mtconf->get('days_to_expire'))*24); 
	            $row->publish_down  = $jdate->toMySQL(true); 
	            $jdate->setOffset(intval($mtconf->get('days_to_expire'))*-24);	
			}
			
			if ( $my->id > 0) {
				$row->user_id = $my->id;
			} else {
				$database->setQuery( 'SELECT id FROM #__users WHERE usertype = \'Super Administrator\' LIMIT 1' );
				$row->user_id = $database->loadResult();
			}

			if( empty($row->alias) )
			{
				$row->alias = JFilterOutput::stringURLSafe($row->link_name);
			}

			// Approval for adding listing
			if ( $mtconf->get('needapproval_addlisting') ) {
				$row->link_approved = '0';
			} else {
				$row->link_approved = 1;
				$row->link_published = 1;
				$row->updateLinkCount( 1 );
				$cache = &JFactory::getCache('com_mtree');
				$cache->clean();
			}

		# Modification to existing record
		} else {
			
			# Validate that this user is the rightful owner
			$database->setQuery( "SELECT user_id FROM #__mt_links WHERE link_id = '".$row->link_id."'" );
			$user_id = $database->loadResult();
			
			if (  $user_id <> $my->id ) {
				JError::raiseError( 403, JText::_( 'Access Forbidden' ) );
			} else {

				// Get the name of the old photo and last modified date
				$sql="SELECT link_id, link_modified, link_created FROM #__mt_links WHERE link_id='".$row->link_id."'";
				$database->setQuery($sql);
				$old = $database->loadObject();

				// Retrive last modified date
				$old_modified = $old->link_modified;
				$link_created = $old->link_created;

				// $row->link_published = 1;
				$row->user_id = $my->id;
				
				// Get other info from original listing
				// $database->setQuery( "SELECT link_name, link_desc, link_hits, link_votes, link_rating, link_featured, link_created, link_visited, ordering, publish_down, publish_up, attribs, internal_notes, link_published, link_approved FROM #__mt_links WHERE link_id = '$row->link_id'" );
				$database->setQuery( "SELECT * FROM #__mt_links WHERE link_id = '$row->link_id'" );
				$original = $database->loadObject();
				$original_link_id = $row->link_id;
				
				$row->link_modified = $row->getLinkModified( $original_link_id, $post );

				foreach( $original AS $k => $v ) {
					if( in_array($k,array('alias', 'link_hits', 'link_votes', 'link_rating', 'link_featured', 'link_created', 'link_visited', 'ordering', 'publish_down', 'publish_up', 'attribs', 'internal_notes', 'link_published', 'link_approved')) ) {
						$row->$k = $v;
					}
				}
				
				if( !isset($row->metadesc) && isset($original->metadesc) && !empty($original->metadesc) ) {
					$row->metadesc = $original->metadesc;
				}

				if( !isset($row->metakey) && isset($original->metakey) && !empty($original->metakey) ) {
					$row->metakey = $original->metakey;
				}

				// Remove any listing that is waiting for approval for this listing
				$database->setQuery( 'SELECT link_id FROM #__mt_links WHERE link_approved = \''.(-1*$row->link_id).'\' LIMIT 1' );
				$tmp_pending_link_id = $database->loadResult();
				if( $tmp_pending_link_id > 0 ) {
					$database->setQuery( 'SELECT CONCAT(' . $database->quote(JPATH_SITE.$mtconf->get('relative_path_to_attachments')) . ',raw_filename) FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($tmp_pending_link_id) );
					$raw_filenames = array_merge($raw_filenames,$database->loadResultArray());
					
					$database->setQuery( "DELETE FROM #__mt_cfvalues WHERE link_id = '".$tmp_pending_link_id."'" );
					$database->query();
					$database->setQuery( "DELETE FROM #__mt_cfvalues_att WHERE link_id = '".$tmp_pending_link_id."'" );
					$database->query();
					$database->setQuery( "DELETE FROM #__mt_links WHERE link_id = '".$tmp_pending_link_id."' LIMIT 1" );
					$database->query();
					$database->setQuery( "DELETE FROM #__mt_cl WHERE link_id = '".$tmp_pending_link_id."'" );
					$database->query();
					$database->setQuery( "SELECT filename FROM #__mt_images WHERE link_id = '".$tmp_pending_link_id."'" );
					$tmp_pending_images = $database->loadResultArray();
					if(count($tmp_pending_images)) {
						foreach($tmp_pending_images AS $tmp_pending_image) {
							unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $tmp_pending_image);
							unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $tmp_pending_image);
							unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $tmp_pending_image);
						}
					}
					$database->setQuery( "DELETE FROM #__mt_images WHERE link_id = '".$tmp_pending_link_id."'" );
					$database->query();
				}
				
				// Approval for modify listing
				if( $original->link_published && $original->link_approved )
				{
					if ( $mtconf->get('needapproval_modifylisting') ) {
						$row->link_approved = (-1 * $row->link_id);
						$row->link_id = null;
					} else {
						$row->link_approved = 1;
						$cache = &JFactory::getCache('com_mtree');
						$cache->clean();

						// Get old state (approved, published)
						$database->setQuery( "SELECT cat_id FROM #__mt_cl AS cl WHERE link_id ='".$row->link_id."' AND main = 1 LIMIT 1" );
						$old_state = $database->loadObject();
						if($row->cat_id <> $old_state->cat_id) {
							$row->updateLinkCount( 1 );
							$row->updateLinkCount( -1, $old_state->cat_id );
						}
					}					
				}

			}

		} // End of $isNew

		# Load field type
		$database->setQuery('SELECT cf_id, field_type, hidden, published, iscore FROM #__mt_customfields');
		$fieldtype = $database->loadObjectList('cf_id');
		$hidden_cfs = array();
		foreach($fieldtype AS $ft) {
			if($ft->hidden && $ft->published) {
				$hidden_cfs[] = $ft->cf_id;
			}
			if($ft->iscore && $ft->hidden) {
				if( isset($original->{substr($ft->field_type,4)}) )
				{
					$row->{substr($ft->field_type,4)} = $original->{substr($ft->field_type,4)};
				} else {
					$row->{'link_'.substr($ft->field_type,4)} = $original->{'link_'.substr($ft->field_type,4)};
				}
			}
		}

		# Load original custom field values, for use in mosetstree plugins
		$sql="SELECT cf_id, value FROM #__mt_cfvalues WHERE link_id='".$row->link_id."'";
		if( !empty($hidden_cfs) ) {
			$sql .= " AND cf_id NOT IN (" . implode(',',$hidden_cfs) . ")";
		}
		$database->setQuery($sql);
		$original_cfs = $database->loadAssocList('cf_id');
		if( !empty($original_cfs) )
		{
			foreach( $original_cfs AS $key_cf_id => $value )
			{
				$original_cfs[$key_cf_id] = $value['value'];
			}
		}
		
		# Erase Previous Records, make way for the new data
		$sql="DELETE FROM #__mt_cfvalues WHERE link_id='".$row->link_id."' AND attachment <= 0";
		if( !empty($hidden_cfs) ) {
			$sql .= " AND cf_id NOT IN (" . implode(',',$hidden_cfs) . ")";
		}
		$database->setQuery($sql);
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if( !empty($fieldtype) ) {
			$load_ft = array();
			foreach( $fieldtype AS $ft ) {
				if(!in_array($ft->field_type,$load_ft)) {
					$load_ft[] = $ft->field_type;
				}
			}
			$database->setQuery('SELECT ft_class FROM #__mt_fieldtypes WHERE field_type IN (\'' . implode('\',\'',$load_ft) . '\')');
			$ft_classes = $database->loadResultArray();
			foreach( $ft_classes AS $ft_class ) {
				eval($ft_class);
			}
		}

		# Collect all active custom field's id
		$active_cfs = array();
		$additional_cfs = array();
		$core_params = array();
		foreach($post AS $k => $v) {
			if( in_array($k,array('alias', 'link_hits', 'link_votes', 'link_rating', 'link_featured', 'link_created', 'link_visited', 'ordering', 'publish_down', 'publish_up', 'attribs', 'internal_notes', 'link_published', 'link_approved', 'metadesc', 'metakey')) ) {
				continue;
			}
			
			$v = JRequest::getVar( $k, '', 'post', '', 2);
			if ( substr($k,0,2) == "cf" && ( (!is_array($v) && (!empty($v) || $v == '0')) || (is_array($v) && !empty($v[0])) ) ) {
				if(strpos(substr($k,2),'_') === false && is_numeric(substr($k,2))) {
					// This custom field uses only one input. ie: cf17, cf23, cf2
					$active_cfs[intval(substr($k,2))] = $v;
					if( is_array($v) && array_key_exists(intval(substr($k,2)),$original_cfs) ) {
						$original_cfs[intval(substr($k,2))] = explode('|',$original_cfs[intval(substr($k,2))]);
						
					}
				} else {
					// This custom field uses more than one input. The date field is an example of cf that uses this. ie: cf13_0, cf13_1, cf13_2
					$ids = explode('_',substr($k,2));
					if(count($ids) == 2 && is_numeric($ids[0]) && is_numeric($ids[1]) ) {
						$additional_cfs[intval($ids[0])][intval($ids[1])] = $v;
					}
				}
			} elseif( substr($k,0,7) == 'keep_cf' ) {
				$cf_id = intval(substr($k,7));
				$keep_att_ids[] = $cf_id;

			# Perform parseValue on Core Fields
			} elseif( substr($k,0,2) != "cf" && isset($row->{$k}) ) {
				if(strpos(strtolower($k),'link_') === false) {
					$core_field_type = 'core' . $k;
				} else {
					$core_field_type = 'core' . str_replace('link_','',$k);
				}
				$class = 'mFieldType_' . $core_field_type;

				if(class_exists($class)) {
					if(empty($core_params)) {
						$database->setQuery('SELECT field_type, params FROM #__mt_customfields WHERE iscore = 1');
						$core_params = $database->loadObjectList('field_type');
					}
					$mFieldTypeObject = new $class(array('params'=>$core_params[$core_field_type]->params));
					$v = call_user_func(array(&$mFieldTypeObject, 'parseValue'),$v);
					$row->{$k} = $v;
				}
			}
		}
				
		# OK. Store new or updated listing into database
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		} else {
			if( !$isNew && $row->link_id > 0 ) {
				// Find if there are any additional categories assigned to the listinig
				if( $original_link_id <> $row->link_id ) {
					$database->setQuery( 'SELECT DISTINCT cat_id FROM #__mt_cl WHERE link_id = '.$database->Quote($original_link_id).' and main=\'0\' ' );
					$tmp_cats = $database->loadResultArray();
					if( !empty($tmp_cats) ){
						foreach( $tmp_cats AS $tmp_cat_id ) {
							$database->setQuery( 'INSERT INTO #__mt_cl (`link_id`,`cat_id`,`main`) VALUES('.$database->Quote($row->link_id).','.$database->Quote($tmp_cat_id).',\'0\')');
							$database->query();
						}
					}
					unset($tmp_cats);
				}
			}
		}
         //echo $row->link_id ;
        // exit();
        
		/* Plugin start */
	
		/* End Plugin */
		
		
		# Update "Also appear in these categories" aka other categories
		if($mtconf->get('allow_user_assign_more_than_one_category')) {
			$mtCL = new mtCL_main0( $database );
			$mtCL->load( $row->link_id );
			$mtCL->update( $other_cats );
		}

		// $files_cfs is used to store attachment custom fields. 
		// This will be used in the next foreach loop to 
		// prevent it from storing it's value to #__mt_cfvalues 
		// table
		$file_cfs = array();

		// $file_values is used to store parsed data through 
		// mFieldType_* which will be done in the next foreach 
		// loop
		$file_values = array();

		$files = JRequest::get( 'files' );
		/* Handout Plugin Triggering Start*/
		
		
			$dispatcher	=& JDispatcher::getInstance();
		JPluginHelper::importPlugin('handout','handout');
		$args=array();
		$args[]=$files;
		$args[]=$row;
			 $results = $dispatcher->trigger('onUpload',$args);
	          $handouterrors=$results[0];
	          $terr=count($handouterrors);
	          $handmsg="";
	          if($terr>0)
	          { 
	          	foreach ($handouterrors as $handerr)
	          {
	          	$handmsg=$handerr.'\n';
	          	
	          }
	          	
	          	echo "<script> alert('".$handmsg."'); window.location='index.php?option=com_mtree&task=editlisting&link_id=".$row->link_id."';</script>\n";
			exit();
	          }
	          
  	


	 /* Handout Plugin Triggering End */
	 
	 
	 
	 
		foreach($files AS $k => $v) {
			if ( substr($k,0,2) == "cf" && is_numeric(substr($k,2)) && $v['error'] == 0) {
				$active_cfs[intval(substr($k,2))] = $v;
				$file_cfs[] = substr($k,2);
			}
		}

		if( !empty($active_cfs) ) {
			$database->setQuery('SELECT cf_id, params FROM #__mt_customfields WHERE iscore = 0 AND cf_id IN (\'' . implode('\',\'',array_keys($active_cfs)). '\') LIMIT ' . count($active_cfs));
			$params = $database->loadObjectList('cf_id');

			foreach($active_cfs AS $cf_id => $v) {
				if(class_exists('mFieldType_'.$fieldtype[$cf_id]->field_type)) {
					$class = 'mFieldType_'.$fieldtype[$cf_id]->field_type;
				} else {
					$class = 'mFieldType';
				}

				# Perform parseValue on Custom Fields
				
				$mFieldTypeObject = new $class(array('id'=>$cf_id,'params'=>$params[$cf_id]->params));
				if(array_key_exists($cf_id,$additional_cfs) && !empty($additional_cfs[$cf_id]) ) {
					$arr_v = $additional_cfs[$cf_id];
					array_unshift($arr_v, $v);
					$v = &$mFieldTypeObject->parseValue($arr_v);
					$active_cfs[$cf_id] = $v;
				} else {
					$v = &$mFieldTypeObject->parseValue($v);
				}
				
				if(in_array($cf_id,$file_cfs)) {
					$file_values[$cf_id] = $v;
				}

				if( (!empty($v) || $v == '0') && !in_array($cf_id,$file_cfs)) {
					# -- Now add the row
					$sql = 'INSERT INTO #__mt_cfvalues (`cf_id`, `link_id`, `value`)'
						. ' VALUES (' . $database->quote($cf_id) . ', ' . $database->quote($row->link_id) . ', ' . $database->quote((is_array($v)) ? implode("|",$v) : $v) . ')';
					$database->setQuery($sql);
					if (!$database->query()) {
						echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
						exit();
					}
				}
				unset($mFieldTypeObject);
			} // End of foreach
		}
		
		# If this link is pending approval for modification, copy over hidden values
		if ( !$isNew && $mtconf->get('needapproval_modifylisting') && !empty($hidden_cfs) ) {
			$sql = 'INSERT INTO #__mt_cfvalues (`cf_id`, `link_id`, `value`)'
				. ' SELECT `cf_id`, \'' . $row->link_id . '\', `value` FROM #__mt_cfvalues WHERE link_id = ' . $original_link_id . ' AND cf_id IN (' . implode(',',$hidden_cfs) . ')';
			$database->setQuery($sql);
			$database->query();
		}
		
		# Remove all attachment except those that are kept
		$removed_attachments = array();
		
		if(isset($keep_att_ids) && !empty($keep_att_ids) ) {
			$database->setQuery( 'SELECT cf_id, raw_filename FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) . ' AND cf_id NOT IN (\'' . implode('\',\'',$keep_att_ids) . '\')');
			$tmp_raw_filenames = $database->loadObjectList();
			
			$i=0;
			foreach($tmp_raw_filenames AS $tmp_raw_filename)
			{
				$removed_attachments[$tmp_raw_filename->cf_id] = $tmp_raw_filename->raw_filename;
				$raw_filenames[$i] = JPATH_SITE.$mtconf->get('relative_path_to_attachments') . $tmp_raw_filename->raw_filename;
				$i++;
			}
			
			$database->setQuery('DELETE FROM #__mt_cfvalues_att WHERE link_id = \'' . $row->link_id . '\' AND cf_id NOT IN (\'' . implode('\',\'',$keep_att_ids) . '\')' );
			$database->query();
			$database->setQuery('DELETE FROM #__mt_cfvalues WHERE link_id = \'' . $row->link_id . '\' AND cf_id NOT IN (\'' . implode('\',\'',$keep_att_ids) . '\') AND attachment > 0' );
			$database->query();
		} else {
			$database->setQuery( 'SELECT cf_id, raw_filename FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) );
			$tmp_raw_filenames = $database->loadObjectList();
			
			$i=0;
			foreach($tmp_raw_filenames AS $tmp_raw_filename)
			{
				$removed_attachments[$tmp_raw_filename->cf_id] = $tmp_raw_filename->raw_filename;
				$raw_filenames[$i] = JPATH_SITE.$mtconf->get('relative_path_to_attachments') . $tmp_raw_filename->raw_filename;
				$i++;
			}
			
			$database->setQuery('DELETE FROM #__mt_cfvalues_att WHERE link_id = \'' . $row->link_id . '\'' );
			$database->query();
			$database->setQuery('DELETE FROM #__mt_cfvalues WHERE link_id = \'' . $row->link_id . '\' AND attachment > 0' );
			$database->query();
		}

		if(!$isNew && isset($keep_att_ids) && !empty($keep_att_ids) && $mtconf->get('needapproval_modifylisting') && $row->link_published == 1) {

			$database->setQuery( "SELECT * FROM #__mt_cfvalues_att WHERE link_id = '" . $original_link_id . "' AND cf_id IN ('" . implode("','",$keep_att_ids) . "')" );
			$listing_atts = $database->loadObjectList();

			foreach($listing_atts AS $listing_att) {
				$file_extension = pathinfo($listing_att->raw_filename);
				$file_extension = strtolower($file_extension['extension']);

				$database->setQuery( 
					'INSERT INTO #__mt_cfvalues_att (`link_id`,`cf_id`,`raw_filename`,`filename`,`filesize`,`extension`) '
					. 'VALUES (' . $row->link_id . ', ' . $database->Quote($listing_att->cf_id). ', ' . $database->Quote($listing_att->raw_filename). ', ' . $database->Quote($listing_att->filename). ', ' . $database->Quote($listing_att->filesize). ', ' . $database->Quote($listing_att->extension). ')' );
				$database->query();
				$att_id = $database->insertid();
				
				$database->setQuery( 'UPDATE #__mt_cfvalues_att SET raw_filename = ' . $database->Quote($att_id . '.' . $file_extension) . ' WHERE att_id = ' . $database->Quote($att_id) . ' LIMIT 1' );
				$database->query();
				
				copy( 
					$mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_attachments') . $listing_att->raw_filename,
					$mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_attachments') . $att_id . "." . $file_extension 
				);
			}
			
			$database->setQuery(
				'INSERT INTO #__mt_cfvalues (cf_id,link_id,value,attachment) '
				. "\nSELECT cf_id,'" . $row->link_id . "',value,attachment "
				. "FROM #__mt_cfvalues "
				. "WHERE link_id = '" . $original_link_id . "' AND cf_id IN ('" . implode("','",$keep_att_ids) . "')");
			$database->query();
			
		}

		jimport('joomla.filesystem.file');

		foreach($files AS $k => $v) {
			if ( substr($k,0,2) == "cf" && is_numeric(substr($k,2)) && $v['error'] == 0 ) {
				$cf_id = intval(substr($k,2));

				$file_extension = pathinfo($file_values[$cf_id]['name']);
				$file_extension = strtolower($file_extension['extension']);

				// Prevents certain file types from being uploaded. Defaults to prevent PHP file (php)
				if( in_array($file_extension,explode(',',$mtconf->get('banned_attachment_filetypes'))) ) {
					continue;
				}

				if(array_key_exists($cf_id,$file_values)) {
					$file = $file_values[$cf_id];
					if(!empty($file['data'])) {
						$data = $file['data'];
					} else {
						$fp = fopen($v['tmp_name'], "r");
						$data = fread($fp, $v['size']);
						fclose($fp);
					}
				} else {
					$file = $v;
					$fp = fopen($v['tmp_name'], "r");
					$data = fread($fp, $v['size']);
					fclose($fp);
				}

				$database->setQuery( 'SELECT CONCAT(' . $database->quote(JPATH_SITE.$mtconf->get('relative_path_to_attachments')) . ',raw_filename) FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) . ' AND cf_id = ' . $database->quote($cf_id));
				$raw_filenames = array_merge($raw_filenames,$database->loadResultArray());

				$database->setQuery('DELETE FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) . ' AND cf_id = ' . $database->quote($cf_id));
				$database->query();

				$database->setQuery('DELETE FROM #__mt_cfvalues WHERE cf_id = ' . $database->quote($cf_id) . ' AND link_id = ' . $database->quote($row->link_id) . ' AND attachment > 0' );
				$database->query();

				$database->setQuery( 'INSERT INTO #__mt_cfvalues_att (link_id, cf_id, raw_filename, filename, filesize, extension) '
					. ' VALUES('
					. $database->quote($row->link_id) . ', '
					. $database->quote($cf_id) . ', '
					. $database->quote($file['name']) . ', '
					. $database->quote($file['name']) . ', '
					. $database->quote($file['size']) . ', '
					. $database->quote($file['type']) . ')'
					);

				if($database->query() !== false) {
					$att_id = $database->insertid();

					$file_extension = strrchr($file['name'],'.');
					if( $file_extension === false ) {
						$file_extension = '';
					}

					if(JFile::write( JPATH_SITE.$mtconf->get('relative_path_to_attachments').$att_id.$file_extension, $data ))
					{
						$database->setQuery( 'UPDATE #__mt_cfvalues_att SET raw_filename = ' . $database->quote($att_id . $file_extension) . ' WHERE att_id = ' . $database->quote($att_id) . ' LIMIT 1' );
						$database->query();

						$sql = 'INSERT INTO #__mt_cfvalues (`cf_id`, `link_id`, `value`, `attachment`) '
							. 'VALUES (' . $database->quote($cf_id) . ', ' . $database->quote($row->link_id) . ', ' . $database->quote($file['name']) . ',1)';
						$database->setQuery($sql);
						$database->query();
					} else {
						// Move failed, remove record from previously INSERTed row in #__mt_cfvalues_att
						$database->setQuery('DELETE FROM #__mt_cfvalues_att WHERE att_id = ' . $database->quote($att_id) . ' LIMIT 1');
						$database->query();
					}
				}
			}
		}
		
		if( !empty($raw_filenames) )
		{
			JFile::delete($raw_filenames);
		}

		if(
			$mtconf->get('allow_imgupload')
			||
			(!$mtconf->get('allow_imgupload') && $mtconf->get('needapproval_modifylisting'))
		) {
			
			if($mtconf->get('allow_imgupload')) {
				$keep_img_ids = JRequest::getVar( 'keep_img', null, 'post');
				JArrayHelper::toInteger($keep_img_ids, array());

			// If image upload is disabled, it will get the image IDs from database and make sure 
			// the images are not lost after approval
			} else {
				$database->setQuery('SELECT img_id FROM #__mt_images WHERE link_id = ' . $database->quote($original_link_id) );
				$keep_img_ids = $database->loadResultArray();
			}
			
			$redirectMsg = '';
			if(is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_small_image')) && is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_medium_image')) && is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_original_image'))) {
		
				// Duplicate listing images for approval
				// if(!$isNew && !empty($keep_img_ids) && is_array($keep_img_ids) && $mtconf->get('needapproval_modifylisting')) {
				if(!$isNew && $row->link_approved && !empty($keep_img_ids) && is_array($keep_img_ids) && $mtconf->get('needapproval_modifylisting')) {
					foreach($keep_img_ids AS $keep_img_id) {
						$database->setQuery('SELECT * FROM #__mt_images WHERE link_id = ' . $database->quote($original_link_id) . ' AND img_id = ' . $database->quote($keep_img_id) . ' LIMIT 1');
						$original_image = $database->loadObject();
						$file_extension = pathinfo($original_image->filename);
						$file_extension = strtolower($file_extension['extension']);
					
						$database->setQuery('INSERT INTO #__mt_images (link_id,filename,ordering) '
							.	"\n VALUES ('" . $row->link_id . "', '" . $original_image->filename . '_' . $row->link_id . "', '" . $original_image->ordering . "')");
						$database->query();

						$new_img_ids[$keep_img_id] = $database->insertid();
						$database->setQuery("UPDATE #__mt_images SET filename = '" . $new_img_ids[$keep_img_id] .  '_' . $row->link_id . '.' . $file_extension . "' WHERE img_id = '" . $new_img_ids[$keep_img_id] . "' LIMIT 1");
						$database->query();
						copy( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $original_image->filename, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $new_img_ids[$keep_img_id] .  '_' . $row->link_id . '.' . $file_extension );
						copy( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $original_image->filename, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $new_img_ids[$keep_img_id] .  '_' . $row->link_id . '.' . $file_extension );
						copy( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $original_image->filename, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $new_img_ids[$keep_img_id] .  '_' . $row->link_id . '.' . $file_extension );
					}
				}
		
				# Remove all images except those that are kept when modification does not require approval
				$image_filenames = array();
				if(
					!$mtconf->get('needapproval_modifylisting')
					||
					// Expression below allow modification to take effect immediately without going through approval when:
					//
					// 1) needapproval_modifylisting is set to Yes
					// 2) needapproval_addlisting is set to Yes
					// 3) Link is still awaiting approval
					//
					// A listing satisfying the above requirements is already in the approval queue for the initial 
					// submission. We want to allow subsequent modification to apply immediately because it would
					// be confusing to have 2 items in the awaiting approval queue for the same listing (one for 
					// the initial submission and another for the modification on a listing awaiting approval)
					(
						$mtconf->get('needapproval_modifylisting') && $mtconf->get('needapproval_addlisting') && !$row->link_approved
					)
				) {
					if(isset($keep_img_ids) && !empty($keep_img_ids)) {
						$database->setQuery('SELECT filename FROM #__mt_images WHERE link_id = \'' . $row->link_id . '\' AND img_id NOT IN (\'' . implode('\',\'',$keep_img_ids) . '\')' );
						$image_filenames = $database->loadResultArray();
						$database->setQuery('DELETE FROM #__mt_images WHERE link_id = \'' . $row->link_id . '\' AND img_id NOT IN (\'' . implode('\',\'',$keep_img_ids) . '\')' );
						$database->query();
					} else {
						$database->setQuery('SELECT filename FROM #__mt_images WHERE link_id = \'' . $row->link_id . '\'' );
						$image_filenames = $database->loadResultArray();
						$database->setQuery('DELETE FROM #__mt_images WHERE link_id = \'' . $row->link_id . '\'' );
						$database->query();
					}
				}
				if(!empty($image_filenames)) {
					foreach($image_filenames AS $image_filename) {
						unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $image_filename);
						unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $image_filename);
						unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $image_filename);
					}
				}
				
				$files_exceed_limit = false;
				
				if( isset($files['image']) ) {
					for($i=0;$i<count($files['image']['name']) && ($i<($mtconf->get('images_per_listing') - count($keep_img_ids)) || $mtconf->get('images_per_listing') == '0');$i++) {
						if( $mtconf->get('image_maxsize') > 0 && $files['image']['size'][$i] > $mtconf->get('image_maxsize') ) {
							// Uploaded file exceed file limit
							$files_exceed_limit = true;
						} elseif ( !empty($files['image']['name'][$i]) && $files['image']['error'][$i] == 0 &&  $files['image']['size'][$i] > 0 ) {
							$file_extension = pathinfo($files['image']['name'][$i]);
							$file_extension = strtolower($file_extension['extension']);
							if( !in_array($file_extension,array('png','gif','jpg','jpeg')) ) {
								continue;
							}
							$mtImage = new mtImage();
							$mtImage->setMethod( $mtconf->get('resize_method') );
							$mtImage->setQuality( $mtconf->get('resize_quality') );
							$mtImage->setSize( $mtconf->get('resize_listing_size') );
							$mtImage->setTmpFile( $files['image']['tmp_name'][$i] );
							$mtImage->setType( $files['image']['type'][$i] );
							$mtImage->setName( $files['image']['name'][$i] );
							$mtImage->setSquare( $mtconf->get('squared_thumbnail') );
							$mtImage->resize();
							$mtImage->setDirectory( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') );
							$mtImage->saveToDirectory();

							$mtImage->setSize( $mtconf->get('resize_medium_listing_size') );
							$mtImage->setSquare(false);
							$mtImage->resize();
							$mtImage->setDirectory( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') );
							$mtImage->saveToDirectory();
							move_uploaded_file($files['image']['tmp_name'][$i],$mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $files['image']['name'][$i]);

							$database->setQuery( 'INSERT INTO #__mt_images (link_id, filename, ordering) '
								. ' VALUES(' . $database->quote($row->link_id) . ', ' . $database->quote($files['image']['name'][$i]) . ', \'9999\')');
							$database->query();
							$img_id = $database->insertid();
							rename($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $files['image']['name'][$i], $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $img_id . '.' . $file_extension);
							rename($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $files['image']['name'][$i], $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $img_id . '.' . $file_extension);
							rename($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $files['image']['name'][$i], $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $img_id . '.' . $file_extension);
							$database->setQuery('UPDATE #__mt_images SET filename = ' . $database->quote($img_id . '.' . $file_extension) . ' WHERE img_id = ' . $database->quote($img_id));
							$database->query();
						}
					}
				}
		
				if( $files_exceed_limit ) {
					if( $mtconf->get('image_maxsize') > 1048576 ) {
						$image_upload_limit = round($mtconf->get('image_maxsize')/1048576) . 'MB';
					} else {
						$image_upload_limit = round($mtconf->get('image_maxsize')/1024) . 'KB';
					}
					$redirectMsg .= sprintf( JText::_( 'Image is not saved because it exceeded file size limit' ), $image_upload_limit );
				}
		
				$img_sort_hash = JRequest::getVar( 'img_sort_hash', null, 'post');
				
				if(!empty($img_sort_hash)) {
					$arr_img_sort_hashes = split("[&]*img\[\]=\d*", $img_sort_hash);
					$i=1;
					foreach($arr_img_sort_hashes AS $arr_img_sort_hash) {
						if(!empty($arr_img_sort_hash) && $arr_img_sort_hash > 0) {
							$sql = 'UPDATE #__mt_images SET ordering = ' . $database->quote($i) . ' WHERE img_id = ';
							if(isset($new_img_ids) && !empty($new_img_ids)) {
								$sql .= $database->quote(intval($new_img_ids[$arr_img_sort_hash]));
							} else {
								$sql .= $database->quote(intval($arr_img_sort_hash));
							}
							$sql .= ' LIMIT 1';
							$database->setQuery( $sql );
							$database->query();
							$i++;
						}
					}
				}
				$images = new mtImages( $database );
				$images->reorder('link_id='.$row->link_id);
			} else {
				if( isset($files['image']) ) {
					$redirectMsg .= JText::_( 'Image directories not writable' );
				}
			}

		}
		
		# Send e-mail notification to user/admin upon adding a new listing
		// Get owner's email
		if( $my->id > 0 ) {
			$database->setQuery( "SELECT email, name, username FROM #__users WHERE id = '".$my->id."' LIMIT 1" );
			$author = $database->loadObject();
		} else {
			if( !empty($row->email) ) {
				$author->email = $row->email;
			} else {
				$author->email = JText::_( 'Not specified' );
			}
			$author->username = JText::_( 'None' );
			$author->name = JText::_( 'Non registered user' );
		}

		$uri =& JURI::getInstance();

		if ( $isNew ) {

			# To User
			if ( $mtconf->get('notifyuser_newlisting') == 1 && ( $my->id > 0 || 
					( !empty($author->email) && (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $author->email )==true))
	
			) ) {
				
				if ( $row->link_approved == 0 ) {
					$subject = sprintf(JText::_( 'New listing email subject waiting approval' ), $row->link_name);
					$msg = "<body marginheight='0' background='http://extensions.joomla.org/images/mail/images/bg.png' topmargin='0' marginwidth='0' style='font-size: 12px; margin: 0; padding: 0; font-family: Arial, sans-serif; line-height: 20px; color: #666666; width: 100%;' bgcolor='#E3E3E3' offset='0' leftmargin='0'><table class='wrapper' background='images/bg.png' cellspacing='0' style='font-size: 12px; font-family: Arial, sans-serif; line-height: 20px; color: #666666; table-layout: fixed;' bgcolor='#E3E3E3' width='100%' cellpadding='0'><tr><td style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;'><br><table rules='none' cellspacing='0' border='0' align='center' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='600' cellpadding='0'><tr><td style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;'><table cellspacing='0' align='center' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='560' cellpadding='0'><tr><td class='view-mobile-browser' valign='top' style='font-size: 11px; font-family: Arial, sans-serif; line-height: 14px; color: #666666;' width='352'>Message not displaying? <a href=http://extensions.joomla.org/index.php?option=com_content&id=57>View it in your browser</a>.</td><td class='ourwebsite' align='right' valign='middle' style='font-size: 11px; font-family: Arial, sans-serif; line-height: 14px; color: #666666;' width='206'>Search. Download. Review.<br><a href='http://extensions.joomla.org' style='font-weight: bold; color: #2A5DB0;'>extensions.joomla.org</a></td></tr></table><br><br><table cellspacing='0' align='center' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='560' cellpadding='0'><tr><td class='logo' valign='top' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='160'><a href='http://extensions.joomla.org/' style='color: #2A5DB0;'><img src='http://extensions.joomla.org/images/mail/jed-logo.png' width='182' height='71' border='0' alt=''></a></td><td class='menu-or-slogan' align='right' valign='middle' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='400'><a href='http://extensions.joomla.org/extensions/my-page' style='color: #3a3a3a; text-decoration: underline;'>My Page</a> | <a href='http://extensions.joomla.org/component/maqmahelpdesk/' style='color: #3a3a3a; text-decoration: underline;'>Support</a> | <a href='http://forum.joomla.org/viewforum.php?f=262' style='color: #3a3a3a; text-decoration: underline;'>JED Forum</a> | <a href='http://people.joomla.org/groups/viewgroup/20-Joomla+Extensions+Directory+%28JED%29.html' style='color: #3a3a3a; text-decoration: underline;'>JED J!People</a> | <a href='http://community.joomla.org/blogs/community.html' style='color: #3a3a3a; text-decoration: underline;'>JED Blog</a></td></tr></table></td></tr></table><br><table class='main-content-wrap' rules='none' cellspacing='0' border='1' bordercolor='#d6d6d6' frame='border' align='center' style='font-size: 12px; border-color: #d6d6d6 #d6d6d6 #d6d6d6 #d6d6d6; border-collapse: collapse; background-color: #ffffff; font-family: Arial, sans-serif; line-height: 20px; color: #666666; border-spacing: 0px; border-width: 1px 1px 1px 1px; border-style: solid solid solid solid;' bgcolor='#ffffff' width='600' cellpadding='0'><tr><td style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;'><img class='block' src='http://extensions.joomla.org/images/mail/images/module-divider-3.gif' height='30' alt='' style='display: block;' width='600' /><table cellspacing='0' align='center' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='540' cellpadding='0'><tr><td valign='top' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;'><img src='http://extensions.joomla.org/images/mail/listing_pending_header.gif' width='540' height='250' alt=''></td></tr></table><p><img class='block' src='http://extensions.joomla.org/images/mail/images/module-divider.gif' height='61' alt='' style='display: block;' width='600' /></p><table cellspacing='0' align='center' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='540' cellpadding='0'><tr><td valign='top' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='166'><img src='http://extensions.joomla.org/images/mail/error_codes.jpg' width='237' height='292' alt=''></td><td style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='20'>&nbsp;</td><td valign='top' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='352'><h3 style='font-size: 16px; font-family: Arial, sans-serif; color: #000000;'><span style='color: #000000;'>Check your pending listing for Error Codes regularly.</span></h3><p style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666; text-align:justify;'>In December 2010, the JED Team announced an <a href='http://community.joomla.org/blogs/community/1339-jed-enhanced-approval-process.html'>Enhanced Approval Process</a>. This process provides error codes when a listing cannot be immediately approved. If you have a pending submission, you should check your error codes on a regular basis. If you receive an error code on the edit page of the listing, do the following:</p><ol><li>Solve the Errors or request help if needed</li><li>Check the box marked, &quot;Errors Corrected&quot;</li><li>Click Save (Click cancel if just viewing)</li></ol><p style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;'>Your listing will be reviewed again. Make sure to continue to check for more possible errors.</p></td></tr></table><img class='block' src='http://extensions.joomla.org/images/mail/images/module-divider.gif' height='61' alt='' style='display: block;' width='600' /><table cellspacing='0' align='center' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='540' cellpadding='0'><tr><td valign='top' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;'><h3 style='font-size: 16px; font-family: Arial, sans-serif; color: #000000;'><span style='color: #000000;'>If you have questions about errors, submit a support ticket.</span></h3><p style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666; text-align:justify;'> <img src='http://extensions.joomla.org/images/mail/add_ticket.jpg' width='379' height='99' hspace='5' align='left'>The JED Team announced the <a href='http://community.joomla.org/blogs/leadership/1459-jed-announces-support-system.html'>introduction to it's Support System</a>. If you recieve error codes on your pending submissions, after reviewing the<a href='http://extensions.joomla.org/index.php?option=com_content&id=50'> error code details</a>, if you still don't understand what they mean or how to solve them, submit a support ticket. Please note, the <em>average approval time</em> is up to 21 days. <strong>Please do not submit a support ticket if you have not received error codes on your submission.</strong></p></td></tr></table><img class='block' src='http://extensions.joomla.org/images/mail/images/module-divider.gif' height='61' alt='' style='display: block;' width='600' /><table cellspacing='0' align='center' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='540' cellpadding='0'><tr><td valign='top' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='166'><img src='http://extensions.joomla.org/images/mail/awaiting_approval.jpg' width='214' height='103' alt=''></td><td style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='20'>&nbsp;</td><td valign='top' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='352'><h3 style='font-size: 16px; font-family: Arial, sans-serif; color: #000000;'><span style='color: #000000;'>Ok, that sounds great, but how do I view my pending submissions?</span></h3><p style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666; text-align:justify;'>To view your pending and published listings, go to the <a href='http://extensions.joomla.org/extensions/my-page'>My Page</a> link when logged into the JED. From there, you can see pending listings and click on the &quot;Pending approval&quot; link.</p></td></tr></table><img class='block' src='http://extensions.joomla.org/images/mail/images/module-divider.gif' height='61' alt='' style='display: block;' width='600' /><table cellspacing='0' align='center' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='530' cellpadding='0'><tr><td valign='top' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='532'><h3 style='font-size: 16px; font-family: Arial, sans-serif; color: #000000;'><span style='color: #000000;'>What are some common errors that many developers miss and publishing is prevented?</span></h3><p style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666; text-align:justify'>The most common errors are:</p><ul><li>Download Link Does Not Point to Download/Product Page</li><li>Domain or images use the Joomla Trademark and is not registered/approved</li><li>Extension Is commercial but has not included a link to the Terms or Conditions</li><li>Developer attempts to restrict the usage of the extension in some way</li><li>Security standards are not followed (index.html in all folders, usage of JEXEC commands)</li><li>GPL Notices are missing in PHP/XML</li></ul><p>Make sure to read the <a href='http://docs.joomla.org/Joomla!_Extension_Directory_FAQs'>JED FAQs</a>, Understand and follow the<a href='http://extensions.joomla.org/tos'> JED Terms of Service</a> and stay up-to-date on <a href='http://extensions.joomla.org/component/maqmahelpdesk/announce_list?id_workgroup=1'>policy changes</a> that may affect your listing.</p><p>We hope that this email helps explain the approval process at the JED. If you have other questions, please feel free to post in the <a href='http://forum.joomla.org/viewforum.php?f=262'>JED Forum</a>, on the <a href='http://people.joomla.org/groups/viewgroup/20-Joomla+Extensions+Directory+(JED).html'>JED J!People Group</a> or enter a <a href='http://extensions.joomla.org/component/maqmahelpdesk/'>support ticket</a>.</p><p>If you have recommendations for improving the JED, please post them in our <a href='http://people.joomla.org/groups/viewgroup/20-Joomla+Extensions+Directory+(JED).html'>J!People Group</a>.</p><p>If you are interested in volunteering in the JED, please <a href='http://extensions.joomla.org/component/content/article/30'>submit your application</a>!</p><p><strong>Regards,</strong></p><p><strong>The Joomla! Extensions Directory Team</strong></p></td></tr></table><img class='block' src='http://extensions.joomla.org/images/mail/images/module-divider-3.gif' height='40' alt='' style='display: block;' width='600' /><table class='social' cellspacing='0' style='font-size: 12px; border-bottom: 1px solid #D0D0D0; font-family: Arial, sans-serif; line-height: 20px; color: #666666; border-top: 1px solid #D0D0D0;' bgcolor='#efefef' cellpadding='0'><tr><td height='45' valign='middle' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='30'>&nbsp;</td><td height='45' valign='middle' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='120'><b style='color: #333333;'>Connect with us:</b></td><td height='45' valign='middle' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='25'><a href='www.facebook.com/joomla' title='' style='color: #3a3a3a; text-decoration: none;'><img src='http://extensions.joomla.org/images/mail/images/social/youtube.png' width='24' height='24' border='0' alt='' ></a></td><td height='45' valign='middle' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='88'>&nbsp;&nbsp;<a href='www.youtube.com/joomla' title='' style='color: #3a3a3a; text-decoration: none;'>Youtube</a></td><td height='45' valign='middle' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='25'><a href='www.facebook.com/joomla' title='' style='color: #3a3a3a; text-decoration: none;'><img src='http://extensions.joomla.org/images/mail/images/social/facebook.png' width='24' height='24' border='0' alt='' ></a></td><td height='45' valign='middle' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='88'>&nbsp;&nbsp;<a href='www.facebook.com/joomla' title='' style='color: #3a3a3a; text-decoration: none;'>Facebook</a></td><td height='45' valign='middle' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='25'><a href='http://t.co/E9vqQk0' title='' style='color: #3a3a3a; text-decoration: none;'><img src='http://extensions.joomla.org/images/mail/images/social/linkedin.png' width='24' height='24' border='0' alt=''></a></td><td height='45' valign='middle' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='88'>&nbsp;&nbsp;<a href='http://t.co/E9vqQk0' title='' style='color: #3a3a3a; text-decoration: none;'>LinkedIn</a></td><td height='45' valign='middle' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='25'><a href='http://www.twitter.com/joomla' title='' style='color: #3a3a3a; text-decoration: none;'><img src='http://extensions.joomla.org/images/mail/images/social/twitter.png' width='24' height='24' border='0' alt=''></a></td><td height='45' valign='middle' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='88'>&nbsp;&nbsp;<a href='http://www.twitter.com/joomla' title='' style='color: #3a3a3a; text-decoration: none;'>Twitter</a></td></tr></table><img class='block' src='http://extensions.joomla.org/images/mail/images/module-divider-3.gif' height='25' alt='' style='display: block;' width='600' /><table cellspacing='0' align='center' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='540' cellpadding='0'><tr><td class='companyinfo' valign='top' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='265'><p style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;'><b style='color: #333333;'>Joomla! Extensions Directory<br></b><a href='http://extensions.joomla.org/component/content/article/30'>Learn more about the team!</a></p></td><td style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='20'>&nbsp;</td><td class='subscription' align='left' valign='top' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='255'><p style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;'>This email was sent to you because you submitted a listing to the Joomla! Extensions Directory. It is *not* marketing material or an advertisement.<a href='http://www.site.com/' style='color: #2A5DB0;'></a></p></td></tr></table><img class='block' src='http://extensions.joomla.org/images/mail/images/module-divider-3.gif' height='30' alt='' style='display: block;' width='600' /></td></tr></table><br><table cellspacing='0' align='center' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;' width='560' cellpadding='0'><tr><td class='copyright' align='center' valign='top' style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;'><p style='font-size: 12px; line-height: 20px; font-family: Arial, sans-serif; color: #666666;'>&copy; 2011 Joomla! Extensions Directory/OpenSourceMatters, Inc.</p></td></tr></table><br><br></td></tr></table></body>";
				} else {
					$subject = sprintf(JText::_( 'New listing email subject approved' ), $row->link_name);
					$msg = sprintf(JText::_( 'New listing email msg approved' ), $row->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$row->link_id&Itemid=$Itemid"),$mtconf->getjconf('fromname'));
				}

				JUTility::sendMail( $mtconf->getjconf('mailfrom'), $mtconf->getjconf('fromname'), $author->email, $subject, wordwrap($msg), $mode=true );
			}

			# To Admin
			if ( $mtconf->get('notifyadmin_newlisting') == 1 ) {
				
				if ( $row->link_approved == 0 ) {
					$subject = sprintf(JText::_( 'New listing email subject waiting approval' ), $row->link_name);
					$msg = sprintf(JText::_( 'Admin new listing msg waiting approval' ), $row->link_name, $row->link_name, $row->link_id, $author->name, $author->username, $author->email);
				} else {
					$subject = sprintf(JText::_( 'New listing email subject approved' ), $row->link_name);
					$msg = sprintf(JText::_( 'Admin new listing msg approved' ), $row->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$row->link_id&Itemid=$Itemid"), $row->link_name, $row->link_id, $author->name, $author->username, $author->email);
				}

				mosMailToAdmin( $subject, $msg );

			}

		}

		# Send e-mail notification to user/admin upon modifying an existing listing
		# E-mail is sent for modifying published extension. Unpublished extension means that they are pending approval
		# and we don't want to know about the changes during this time.
		elseif( $row->link_published == 1 ) {

			# To User
			if ( $mtconf->get('notifyuser_modifylisting') == 1 && $my->id > 0 ) {
				
				if ( $row->link_approved < 0 ) {
					$subject = sprintf(JText::_( 'Modify listing email subject waiting approval' ), $row->link_name);
					$msg = sprintf(JText::_( 'Modify listing email msg waiting approval' ), $row->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$old->link_id&Itemid=$Itemid") );
				} else {
					$subject = sprintf(JText::_( 'Modify listing email subject approved' ), $row->link_name);
					$msg = sprintf(JText::_( 'Modify listing email msg approved' ), $row->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$old->link_id&Itemid=$Itemid"),$mtconf->getjconf('fromname'));
				}

				JUTility::sendMail( $mtconf->getjconf('mailfrom'), $mtconf->getjconf('fromname'), $author->email, $subject, wordwrap($msg) );
			}

			# To Admin
			if ( $mtconf->get('notifyadmin_modifylisting') == 1 ) {

				$diff_desc = diff_main( $original->link_desc, $row->link_desc, true );
				diff_cleanup_semantic($diff_desc);
				$diff_desc = diff_prettyhtml( $diff_desc );

				$msg = "<style type=\"text/css\">\n";
				$msg .= "ins{text-decoration:underline}\n";
				$msg .= "del{text-decoration:line-through}\n";
				$msg .= "</style>";

				if ( $row->link_approved < 0 ) {
					
					$subject = sprintf(JText::_( 'Modify listing email subject waiting approval' ), $row->link_name);
					$msg .= nl2br(sprintf(JText::_( 'Admin modify listing msg waiting approval' ), $row->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$old->link_id&Itemid=$Itemid"), $row->link_name, $row->link_id, $author->name, $author->username, $author->email, $diff_desc));

				} else {

					$subject = sprintf(JText::_( 'Modify listing email subject approved' ), $row->link_name);
					$msg .= nl2br(sprintf(JText::_( 'Admin modify listing msg approved' ), $row->link_name, $uri->toString(array( 'scheme', 'host', 'port' )) . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=$old->link_id&Itemid=$Itemid"), $row->link_name, $row->link_id, $author->name, $author->username, $author->email, $diff_desc));

				}

				mosMailToAdmin( $subject, $msg, 1 );
			}

		}
		
		// Fire mosetstree onAfterModifyListing plugin
		$dispatcher 	= & JDispatcher::getInstance();
		if( isset($original) )
		{
			JPluginHelper::importPlugin('mosetstree');
			$dispatcher->trigger('onAfterModifyListing', array((array)$original,$original_cfs,(array)$row,$active_cfs, $removed_attachments, $old->link_id, $cat_id) );
		}
		
		// Fire finder plugin
		if( 
			( $isNew && $row->link_approved && $row->link_published )
			||
			( !$isNew && !$mtconf->get('needapproval_modifylisting') )
			)
		{
			JPluginHelper::importPlugin('finder');
			$dispatcher->trigger('onSaveMTreeListing', array($row->link_id));
		}
		
		if( isset($original) && $original->link_published && $original->link_approved )
		{
			if( ($isNew && $mtconf->get('needapproval_addlisting')) ) {
				$redirect_url = "index.php?option=$option&task=listcats&cat_id=$cat_id&Itemid=$Itemid";
			} elseif (!$isNew && $mtconf->get('needapproval_modifylisting')) {
				$redirect_url = "index.php?option=$option&task=viewlink&link_id=$old->link_id&Itemid=$Itemid";
			} else {
				$redirect_url = "index.php?option=$option&task=viewlink&link_id=$row->link_id&Itemid=$Itemid";
			} 
		} else {
			if( $my->id > 0 ) {
				$redirect_url = "index.php?option=$option&task=mypage&Itemid=$Itemid";
			} else {
				$redirect_url = "index.php?option=$option&task=listcats&cat_id=$cat_id&Itemid=$Itemid";
			}
		}

		$mainframe->redirect( 
			JRoute::_($redirect_url), 
			(
				($isNew) ? ( 
					($mtconf->get('needapproval_addlisting')) 
					? 
					JText::_( 'Listing will be reviewed' ) 
					: 
					JText::_( 'Listing have been added' )
				) 
				: 
				( 
					($mtconf->get('needapproval_modifylisting')) 
					? 
					JText::_( 'Listing modification will be reviewed' ) 
					: 
					JText::_( 'Listing have been updated' ) 
				) 
			)
			.
			(!empty($redirectMsg)?'<br /> '.$redirectMsg:'') 
		);
	}
}

/***
* Add Category
*/
function addcategory( $option ) {
	global $savantConf, $Itemid, $mtconf;
	
	$database	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$document	=& JFactory::getDocument();
	
	# Get cat_id / link_id
	$cat_id	= JRequest::getInt('cat_id', 0);
	$link_id	= JRequest::getInt('link_id', 0);

	if ( $cat_id == 0 && $link_id > 0 ) {
		$database->setQuery( "SELECT cl.cat_id FROM (#__mt_links AS l, #__mt_cl AS cl) WHERE l.link_id = cl.link_id AND cl.main = '1' AND link_id ='".$link_id."'" );
		$cat_parent = $database->loadResult();
	} else {
		$cat_parent = $cat_id;
	}

	$database->setQuery( "SELECT cat_name FROM #__mt_cats WHERE cat_id = '".$cat_parent."' LIMIT 1" );
	$cat_name = $database->loadResult();

	$document->setTitle(sprintf(JText::_( 'Add cat2' ), $cat_name));

	# Pathway
	$pathWay = new mtPathWay( $cat_parent );

	# Savant Template
	$savant = new Savant2($savantConf);
	assignCommonVar($savant);
	$savant->assign('pathway', $pathWay);
	$savant->assign('cat_parent', $cat_parent);

	if ( $mtconf->get('user_addcategory') == '1' && $my->id < 1 ) {
		# Error. Please login before you can add category
		$savant->assign('error_msg', JText::_( 'Please login before addcategory' ));
		$savant->display( 'page_error.tpl.php' );
	} elseif( $mtconf->get('user_addcategory') == '-1' ) {
		# Add category is disabled
		JError::raiseError(404, JText::_('Resource Not Found'));
	} else {
		# OK. User is allowed to add category
		$savant->display( 'page_addCategory.tpl.php' );
	}

}

function addcategory2( $option ) {
	global $Itemid, $mtconf, $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$database	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();

	# Get cat_parent
	$cat_parent	= JRequest::getInt('cat_parent', 0);

	# Check if any malicious user is trying to submit link
	if ( $mtconf->get('user_addcategory') == 1 && $my->id <= 0 ) {
		echo JText::_( 'NOT_EXIST' );

	} elseif( $mtconf->get('user_addcategory') == '-1' ) {
		# Add category is disabled
		JError::raiseError(404, JText::_('Resource Not Found'));

	} else {
	# Allowed

		$post = JRequest::get( 'post' );
		$row = new mtCats( $database );
		if (!$row->bind( $post )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$isNew = $row->cat_id < 1;

		# Assignment for new record
		if ($isNew) {
			$jdate		= JFactory::getDate();
			$row->cat_created = $now;

			// Required approval
			if ( $mtconf->get('needapproval_addcategory') ) {
				$row->cat_approved = '0';
			} else {
				$row->cat_approved = 1;
				$row->cat_published = 1;
				$cache = &JFactory::getCache('com_mtree');
				$cache->clean();
			}

		} else {
		# Assignment for exsiting record
			$row->cat_modified = $now;
		}

		# OK. Store new category into database
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if ( $isNew && !$mtconf->get('needapproval_addcategory')) {
			$row->updateLftRgt();
			$row->updateCatCount( 1 );
		}

		$mainframe->redirect( JRoute::_("index.php?option=$option&task=listcats&cat_id=$cat_parent&Itemid=$Itemid"), ( ($mtconf->get('needapproval_addcategory')) ?  JText::_( 'Category will be reviewed' ) : JText::_( 'Category have been added' )) );

	}
}

function att_download( $field_type, $ordering, $filename, $link_id, $cf_id, $img_id, $size ) {
	global $mainframe, $Itemid, $mtconf;
	
	$database	=& JFactory::getDBO();
	$my			=& JFactory::getUser();

	# Fieldtype's attachment
	if ( !empty($field_type) ) {
		if($ordering > 0) {
			$database->setQuery('SELECT fta.* FROM #__mt_fieldtypes_att AS fta '
				. ' LEFT JOIN #__mt_fieldtypes AS ft ON ft.ft_id=fta.ft_id '
				. ' WHERE ft.field_type = ' . $database->quote($field_type) . ' AND ordering = ' . $database->quote($ordering) . ' LIMIT 1'
				);
		} elseif( !empty($filename) ) {
			$database->setQuery('SELECT fta.* FROM #__mt_fieldtypes_att AS fta '
				. ' LEFT JOIN #__mt_fieldtypes AS ft ON ft.ft_id=fta.ft_id '
				. ' WHERE ft.field_type = ' . $database->quote($field_type) . ' AND fta.filename = ' . $database->quote($filename) . ' LIMIT 1'
				);
		}
		$attachment = $database->loadObject();

	# Custom field's attachment
	} elseif( $link_id > 0 && $cf_id > 0) {
		// Retrieve attachment's record in database
		$database->setQuery('SELECT cfva.*, cf.*, l.* FROM #__mt_cfvalues_att AS cfva, #__mt_customfields AS cf, #__mt_links AS l '
			. ' WHERE'
			. ' cfva.cf_id = cf.cf_id'
			. ' AND cfva.link_id = l.link_id'
			. ' AND cfva.link_id = ' . $database->quote($link_id) 
			. ' AND cfva.cf_id = ' . $database->quote($cf_id) 
			. ' LIMIT 1'
			);
		$attachment = $database->loadObject();
		$attachment->filedata = null;
		
		// Checks permission. We want to make sure that no attachments can be downloaded when:
		// (1) listing is not approved or published
		// (2) custom field is set up NOT to show in details and summary view
		// In both cases above, only link owner and administrator will have access to the attachment.
		// This prevents unauthorized users from downloading the attachments by guessing the URL
		if( $my->id != $attachment->user_id && strpos(strtolower($my->usertype),'administrator') === false )
		{
			if( 
				($attachment->link_published <= 0 || $attachment->link_approved <= 0)
				||
				($attachment->details_view == 0 && $attachment->summary_view == 0) 
			) {
				// Access denied.
				$mainframe->redirect( JRoute::_('index.php?option=com_mtree&Itemid='.$Itemid), JText::_( 'You are not authorized to access this attachment' ) );
			}
		}
		
		if( !is_null($attachment) ) {
			$filepath = JPATH_SITE.$mtconf->get('relative_path_to_attachments').$attachment->raw_filename;
			$handle = fopen($filepath, 'rb');
			
			$attachment->filedata = fread( $handle, $attachment->filesize );
			fclose( $handle );
		} else {
			// No such attachment exists. User redirected with error.
			$mainframe->redirect( JRoute::_('index.php?option=com_mtree&Itemid='.$Itemid), JText::_( 'You are not authorized to access this attachment' ) );
		}

	} else {
		// Insufficient argument passed. User redirected with error.
		$mainframe->redirect( JRoute::_('index.php?option=com_mtree&Itemid='.$Itemid), JText::_( 'You are not authorized to access this attachment' ) );
	}


	if (!empty($attachment) && !empty($attachment->filedata)) {
		
		// Increase the counter
		$database->setQuery( 'UPDATE #__mt_cfvalues SET counter = counter + 1 WHERE link_id = ' . $database->quote($link_id) . ' && cf_id = ' . $database->quote($cf_id) . ' LIMIT 1' );
		$database->query();
		
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header("Content-type: ".$attachment->extension);
		if($attachment->filesize>0) {
			header("Content-length: ".$attachment->filesize);
		}
		header('Content-Disposition: inline; filename="'.$attachment->filename.'";');
		header('Content-transfer-encoding: binary');
		header("Connection: close");

		echo $attachment->filedata;
		
		die();
	} else {
		$mainframe->redirect(JRoute::_('index.php?option=com_mtree&Itemid='.$Itemid), JText::_( 'You are not authorized to access this attachment' ));
	}
}

function mosMailToAdmin( $subject, $body, $mode=0) {
	global $mtconf;

	if ( strpos($mtconf->get('admin_email'),',') === false ) {
		$recipient_emails = array($mtconf->get('admin_email'));
	} else {
		$recipient_emails = explode(',', $mtconf->get('admin_email'));
	}
	for($i=0;$i<count($recipient_emails);$i++) {
		$recipient_emails[$i] = trim($recipient_emails[$i]);
	}
	
	// Input validation
	if  (!validateInputs( $recipient_emails, $subject, $body ) ) {
		$document =& JFactory::getDocument();
		JError::raiseWarning( 0, $document->getError() );
		return false;
	}
	
	JUTility::sendMail( $mtconf->getjconf('mailfrom'), $mtconf->getjconf('fromname'), $recipient_emails, $subject, wordwrap($body), $mode );
	return true;
}

/**
 * Validates e-mail input. Method is modified based on com_contact's _validateInputs.
 *
 * @param String|Array	$email		Email address
 * @param String		$subject	Email subject
 * @param String		$body		Email body
 * @return Boolean
 * @access public
 * @since 2.1
 */
function validateInputs( $email, $subject, $body ) {
	global $mtconf;

	$document =& JFactory::getDocument();

	// Prevent form submission if one of the banned text is discovered in the email field
	if(false === checkText($email, $mtconf->get('banned_email') )) {
		$document->setError( JText::sprintf( 'Mesghasbannedtext', 'Email') );
		return false;
	}

	// Prevent form submission if one of the banned text is discovered in the subject field
	if(false === checkText($subject, $mtconf->get('banned_subject'))) {
		$document->setError( JText::sprintf( 'Mesghasbannedtext', 'Subject') );
		return false;
	}

	// Prevent form submission if one of the banned text is discovered in the text field
	if(false === checkText( $body, $mtconf->get('banned_text') )) {
		$document->setError( JText::sprintf( 'Mesghasbannedtext', 'Message') );
		return false;
	}

	// test to ensure that only one email address is entered
	if( is_string($email) )
	{
		$check = explode( '@', $email );
		if ( strpos( $email, ';' ) || strpos( $email, ',' ) || strpos( $email, ' ' ) || count( $check ) > 2 ) {
			$document->setError( JText::_( 'You cannot enter more than one email address', true ) );
			return false;
		}
	}

	return true;
}

function checkText($text, $list) {
	if(empty($list) || empty($text)) return true;
	$array = explode(';', $list);
	foreach ($array as $value) {
		$value = trim($value);
		if(empty($value)) continue;
		if ( JString::stristr($text, $value) !== false ) {
			return false;
		}
	}
	return true;
}

?>