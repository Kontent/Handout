<?php




defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');



if(!class_exists('plgCommunityHandout'))
{
	class plgCommunityHandout extends CApplications
	{
		var $name		= 'Handout';
		var $_name		= 'handout';
		
	    function plgCommunityHandout(& $subject, $config)
	    {
			parent::__construct($subject, $config);
	    }


	    function onGroupDisplay()
	    {

	     $groupid=JRequest::getVar('groupid');
	     $db=& JFactory::getDBO();
         $query="select * from #__handout where js_group_id=1";//.$groupid;
         $db->setQuery($query);
         $docs=$db->loadObjectList();
        // echo var_dump($docs);
		$linkhtml="<table>";
		if($docs)
		{
			foreach ($docs as $doc)
			{
				$filename=$doc->docname;
				$linkhtml=$linkhtml.'<tr><td><a href="index.php?option=com_handout&task=doc_details&gid='.$doc->id.'&groupid='.$groupid.'">'.$filename.'</a></td><td></td><td><a href="index.php?option=com_handout&task=doc_download&gid='.$doc->id.'">Download</a></td></tr>';
			}
			
		}
		$linkhtml.="</table>";
		$linkhtml.='<table><tr><td><a href="index.php?option=com_handout&task=upload&groupid='.$groupid.'">Submit Document</td></tr></table>';
		$html = $linkhtml;
         return $html;   
	    
	    
	    }
	
		
		
		
	}	
}


