<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: default_document.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined('_JEXEC') or die;

/*
* Display a document item (called by documents_list.php)
*
* This template is called when a single document summary is to be output
*
* General variables  :
* 	$this->conf (object) : template configuration parameters

* Template variables :
*   $this->doc->data  (object) : holds the document data
*   $this->doc->links (object) : holds the document operations
*   $this->doc->paths (object) : holds the document paths
*   $this->doc->buttons (object) : holds the document buttons
*/
echo '<li class="hdoc-item">';
	if(!$this->doc->data->published)  
		$iconClass="hunpublished";
	elseif($this->doc->data->checked_out) 
		$iconClass="hcheckedout";
	else 
		$iconClass='';
	echo '<div class="hdoc-icon '.$iconClass.'">';
	
	//output document image
	switch($this->conf->doc_image) :
		case 0 :  //none
			//do nothing
		break;
	
		case 1 :   //icon
			$icon_ext = strrchr($this->doc->paths->icon, "/");
			$icon_ext = strrchr($icon_ext, "-");
			
			 $href = isset($this->doc->buttons['download']) ? 'href="'.$this->doc->buttons['download']->link.'"' : "";
			?>
			<a <?php echo $href;?> class="hasTip" title="<?php echo $this->doc->data->docname ?>::<?php echo JText::_('COM_HANDOUT_CLICK_TO_DOWNLOAD'); ?>">
				<img src="<?php echo COM_HANDOUT_IMAGESPATH . 'icons/icon-'.$this->conf->doc_icon_size.$icon_ext ?>" alt="<?php echo $this->doc->data->docname ?>" />
			</a>
			<?php
		break;
	
		case 2  :  //thumb
			if($this->doc->data->docthumbnail) {
				$href = isset($this->doc->buttons['download']) ? 'href="'.$this->doc->buttons['download']->link.'"' : "";
				?>
				<a class="hdoc-thumb" <?php echo $href;?>>
					<img src="<?php echo $this->doc->paths->thumb; ?>" alt="<?php echo $this->doc->data->docname ?>" />
				</a>		
				<?php
			}
		break;
	endswitch;
	?></div>
	<?php 
		//output document link
		if(isset($this->doc->buttons['download']) && $this->conf->item_title_link) :
		?>
			<h4 class="hasTip" title="<?php echo $this->doc->data->docname ?>::<?php echo JText::_('COM_HANDOUT_CLICK_TO_DOWNLOAD'); ?>"><a href="<?php echo $this->doc->buttons['download']->link;?>"><?php
		else :
		?>
			<h4 class="hasTip" title="<?php echo $this->doc->data->docname ?>::<?php echo JText::_('COM_HANDOUT_CLICK_TO_SEE_DETAILS'); ?>"><a><?php
		endif;
		echo $this->doc->data->docname."</a>";
		
		if($this->doc->data->new) :
			?><span class="hdoc-new"><?php echo $this->doc->data->new ?></span><?php
		endif;
		if($this->doc->data->hot) :
			?><span class="hdoc-hot"><?php echo $this->doc->data->hot ?></span><?php
		endif;
		echo "</h4>";
		
		echo "<div class='hdoc-details'>";
	
		if($this->conf->item_tooltip) :
			$tooltip = '';
			if($this->conf->item_filename)
			{	
				$tooltip .= JTEXT::_('COM_HANDOUT_FNAME').": ";
				$tooltip .= $this->doc->data->filename;
				$tooltip .= "&lt;br /&gt;";
			}
			
			if($this->conf->item_filesize)
			{
				$tooltip .= JTEXT::_('COM_HANDOUT_FSIZE').": ";
				$tooltip .= ' '.$this->doc->data->filesize.'';
				$tooltip .= "&lt;br /&gt;";
			}
			
			if($this->conf->item_filetype)
			{
				$tooltip .= JTEXT::_('COM_HANDOUT_FTYPE').": ";
				$tooltip .= $this->doc->data->mime;
				$tooltip .= "&lt;br /&gt;";
			}
	
			// Strip javascript
			$tooltip = preg_replace( '@<script[^>]*?>.*?</script>@si', '',  $tooltip );
	
			// Strip all whitespace around <TAGS>.
			// $tooltip = preg_replace("/(\s+)?(\<.+\>)(\s+)?/", "$2",  $tooltip);
	
			// remove any \r's from windows
			$tooltip = str_replace ("\r", "", $tooltip);
	
			// replace remaining \n's with <br />
			$tooltip = str_replace ("\n", "<br /> ", $tooltip);
	
			$icon = JURI::root(true).'/media/com_handout/images/icon-16-tooltip.png'; 		
			$text   = '<img src="'. $icon .'" border="0" alt="'. JText::_( 'COM_HANDOUT_MORE_INFO' ) .'"/>';
			// $style = 'style="text-decoration: none; color: #333;"';
			echo '<span class="editlinktip hasTip" title="' . JText::_('COM_HANDOUT_DOCUMENT_INFO') . ': ::' . $tooltip.'">'. $text .'</span>';
				
		endif;
		
		$item_output_array = array();
		
		if($this->conf->item_filetype) 
		{
			$item_output_array[] = '<span class="hdoc-type">' . JText::_('COM_HANDOUT_FILETYPE') .': <span>'.$this->doc->data->filetype . '</span></span>';
		}
		if($this->conf->item_filesize) 
		{
			$item_output_array[] =  '<span class="hdoc-size">' . JText::_('COM_HANDOUT_SIZE') . ': <span>' . round($this->doc->data->filesize) . JText::_('COM_HANDOUT_KB') . '</span></span>';	
		}

		//output document date
		if ( $this->conf->item_date ) :
			$item_output_array[] =  '<span class="hdoc-date">' . JText::_('COM_HANDOUT_UPLOADED') .': <span>'. strftime( JText::_('COM_HANDOUT_DATEFORMAT_SHORT'), strtotime($this->doc->data->docdate_published)).'</span></span>';
		endif;
		
		//output document counter
		if ( $this->conf->item_hits  ) :
			$item_output_array[] =  '<span class="hdoc-counter">' . JText::_('COM_HANDOUT_DOWNLOADS') .': <span>'. $this->doc->data->doccounter.'</span></span>';
		endif;
	
		//output document url
		if ( $this->conf->item_homepage && $this->doc->data->docurl != '') :
				$item_output_array[] =  '<span class="hdoc-homepage">' . JText::_('COM_HANDOUT_INFOURL') .': <span><a href="'.$this->doc->data->docurl.'">'.$this->doc->data->docurl.'</span></span>';
		endif;

		//output number of comments
		if ( true || $this->conf->item_comments_count  ) :				
			$item_output_array[] =  '<span class="hdoc-comments">' . JText::_('COM_HANDOUT_COMMENTS') .': '.$this->doc->data->kunena_discuss_count.'</span>';
		endif;
		
		echo implode (' | ',$item_output_array);

	//output document description
		if ( $this->conf->item_description && $this->doc->data->docdescription ) :
			?>
			<div class="hdoc-description">
				<?php echo $this->doc->data->docdescription;?>
			</div>
			<?php
		endif;
		?>
	
	</div>
	
	<div class="hdoc-taskbar">
		<ul>
			<?php 
			foreach($this->doc->buttons as $button) {
				$popup = ($button->params->get('popup', false)) ? 'type="popup"' : '';
				$attr = '';
				if($class = $button->params->get('class', '')) {
					$attr = 'class="' . $class . '"';
				}
				?><li <?php echo $attr?>>
					<a href="<?php echo $button->link?>" <?php echo $popup?>>
						<span><span><?php echo $button->text ?></span></span>
					</a>
				</li><?php
			}    	    	
			?>
		</ul>
	</div>
	<div class="clr"></div>
</li>
