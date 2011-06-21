<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
?>

<?php
    $html = '<div class="' . $moduleclass_sfx . '">';

	// If we have a textual prefix, show it now
	if ( strlen($text_pfx) > 0 ) {
		$html .= "<p class='hmodule-prefix'>" . $text_pfx . "</p>";
	}

	$mtree_class = $is_mtree_listing ? ' mtree_listing' : '';
	$html .= '<ul class="hdoclist hmodule'.$mtree_class.'">';
	
	// For each row of our result..
	foreach ($rows as $row) {
	   
		$html .= "<li>";
	
		// Create a new document
		$doc = new HANDOUT_Document($row->id);
		
		$catid = $doc->getData('catid');                        
		
		$url = JRoute::_('index.php?option=com_handout&task=doc_details&gid=' . $row->id . '&Itemid=' . $menuid);    
		$caturl = JRoute::_('index.php?option=com_handout&task=cat_view&Itemid=' . $menuid . '&gid=' . $row->catid);
				
		// show icon
		if ($show_icon) {
			$html .= '<a href="' . $url . '"><img class="hasTip" title="'. JText::_('MOD_HANDOUT_DOCUMENT_INFO') . ': ::'. JText::_('MOD_HANDOUT_CATEGORY') . ': '. $row->cat_title . ' &lt;br /&gt;'. JText::_('MOD_HANDOUT_FILE_SIZE') . ': 0kb &lt;br /&gt;'. JText::_('MOD_HANDOUT_FILE_TYPE') . ': '. $doc->getData('filetype') .'&lt;br /&gt;'. JText::_('MOD_HANDOUT_DOCS_DOWNLOADS') . ': '. $doc->getData('doccounter') .'" border="0" src="'.$doc->getPath('icon', 1, '32').'" alt="'. $doc->getData('docname') . '"/></a>';
		}
	
		// Output the document name
		$html .= '<p><a href="' . $url . '">' . $doc->getData('docname') . '</a></p>';	
		
		if ($show_category) {
			$html .= '<span><a href="' . $caturl . '">' . $row->cat_title . '</a></span>';
		}
		
		if ($show_counter) {
			if ($doc->getData('doccounter') > 1) {
				$html .= '<span>'. $doc->getData('doccounter') .' ' . JText::_('MOD_HANDOUT_DOCS_DOWNLOADS') . '</span>' ;
			} else {
				$html .= '<span>'. $doc->getData('doccounter') .' ' . JText::_('MOD_HANDOUT_DOCS_DOWNLOAD') . '</span>' ;
			}
		}
			
		$html .= "</li>";
	}
	
	// If we had 0 results
	if ( count($rows) == 0 ) {
		$html .= "<li><p class=\"hempty\">";
		$html .= JText::_('MOD_HANDOUT_DOCS_NO_DOCUMENTS') ;
		$html .= "</p></li>";
	}

	$html .= "</ul>" ;
	
	// If we have a textual suffix, show it now
	if ( strlen($text_sfx) > 0 ) {
		$html .= "<p class='hmodule-suffix'>" . $text_sfx . "</p>";
	}
	
	$html .= "</div>" ;

	echo $html;
?>