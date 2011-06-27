<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: default.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined('_JEXEC') or die;

/* Display the document details page(required)
*
* This template is called when u user preform a details operation on a document.
*
* General variables  :
*	$this->data (object) : configuration values
*	$this->buttons (object) : permission values
*	$this->paths (object) : configuration values
*	$this->links (object) : path to links
*	$this->perms (object) : permission values
*	$this->conf (object) : configuration values
*/

JHTML::stylesheet('handout.css', COM_HANDOUT_CSSPATH);

$mainframe = &JFactory::getApplication();
$pathway = & $mainframe->getPathWay();
$pathway->addItem($this->data->docname);
$mainframe->setPageTitle( JText::_('COM_HANDOUT_TITLE_DETAILS') . ' | ' . $this->data->docname );

//append meta keywords and description to page head
$document =& JFactory::getDocument();
if ($this->data->doc_meta_description) {
	$document->setDescription($document->getDescription. "," . $this->data->doc_meta_description);
}
if ($this->data->doc_meta_keywords) {
	$document->setMetaData('keywords', $document->getMetaData('keywords'). "," . $this->data->doc_meta_keywords);
}

$document->setMetaData('language', $this->data->doclanguage);

?>
<div id="handout" class="hdetails">
	<?php $this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'handout' . DS . 'tmpl' );?>
	<?php echo $this->loadTemplate('menu'); ?>    

    <div id="hdoc-details">
        <?php
    if ($this->conf->details_image && $this->data->docthumbnail) :
        ?><div class="hdoc-thumb"><img src="<?php echo $this->paths->thumb ?>" alt="<?php echo $this->data->docname;?>" /></div><?php
    endif;
	 echo $this->loadTemplate('addthis');
    ?>
    <h2><?php echo JText::_('COM_HANDOUT_DETAILSFOR') ?><em>&nbsp;<?php echo $this->data->docname ?></em></h2>
    
    <dl>
        <dt class="hdoc-property"><?php echo JText::_('COM_HANDOUT_PROPERTY')?></dt>
        <dd class="hdoc-value"><?php echo JText::_('COM_HANDOUT_VALUE')?></dd>
    
    <?php
	if($this->conf->details_name) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_NAME') ?>:</dt>
        <dd><?php echo $this->data->docname ?></dd>
        <?php
    endif;
    if($this->conf->details_description) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_DESC') ?>:</dt>
        <dd><p><?php echo $this->data->docdescription ?></p></dd>
        <?php
    endif;
    if($this->conf->details_filename) :
         ?>
        <dt><?php echo JText::_('COM_HANDOUT_FNAME') ?>:</dt>
        <dd><?php echo $this->data->filename ?></dd>
        <?php
    endif;
    if($this->conf->details_filesize) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_FSIZE') ?>:</dt>
        <dd><?php echo $this->data->filesize ?></dd>
        <?php
    endif;
    if($this->conf->details_filetype) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_FTYPE') ?>:</dt>
        <dd><?php echo $this->data->filetype ?>&nbsp;(<?php echo JText::_('COM_HANDOUT_MIME').":&nbsp;".$this->data->mime ?>)</dd>
        <?php
    endif;
    if($this->conf->details_fileversion) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_FVERSION') ?>:</dt>
        <dd><?php echo $this->data->docversion ?></dd>
        <?php
    endif;
        if($this->conf->details_filetype) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_FLANGUAGE') ?>:</dt>
        <dd><?php echo $this->data->doclanguage ?></dd>
        <?php
    endif;
    if($this->conf->details_submitter) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_SUBMITTED_BY') ?>:</dt>
        <dd><?php echo $this->data->submitted_by ?></dd>
        <?php
    endif;
    if($this->conf->details_created) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_SUBMITTED_DATE') ?>:</dt>
        <dd><?php  echo strftime( JText::_('COM_HANDOUT_DATEFORMAT_LONG'), strtotime($this->data->docdate_published)); ?></dd>
        <?php
    endif;
    if($this->conf->details_readers) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_OWNER') ?>:</dt>
        <dd><?php echo $this->data->owner ?></dd>
        <?php
    endif;
    if($this->conf->details_maintainers) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_MAINTAINERS') ?>:</dt>
        <dd><?php echo $this->data->maintainedby ?></dd>
        <?php
    endif;
    if($this->conf->details_downloads) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_DOWNLOADS') ?>:</dt>
        <dd><?php echo $this->data->doccounter ?></dd>
        <?php
    endif;
    if($this->conf->details_updated) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_LAST_UPDATED') ?>:</dt>
        <dd><?php  if (!strstr($this->data->doclastupdateon, '0000-00-00')) 
					echo strftime( JText::_('COM_HANDOUT_DATEFORMAT_LONG'), strtotime($this->data->doclastupdateon)); ?>
        </dd>
        <?php
    endif;
    if($this->conf->details_homepage) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_INFOURL') ?>:</dt>
        <dd><a href="<?php echo $this->data->docurl;?>"><?php echo $this->data->docurl;?></a></dd>
        <?php
    endif;
    if($this->conf->details_crc_checksum) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_CRC_CHECKSUM') ?>:</dt>
        <dd><?php echo $this->data->params->get('crc_checksum'); ?></dd>
        <?php
    endif;
    if($this->conf->details_md5_checksum) :
        ?>
        <dt><?php echo JText::_('COM_HANDOUT_MD5_CHECKSUM') ?>:</dt>
        <dd><?php echo $this->data->params->get('md5_checksum'); ?></dd>
        <?php
    endif;
    ?>
    </dl>
    <div class="clr"></div>
    </div>
    
    <div class="hdoc-taskbar">
        <ul>
        	 <li><a href="javascript: history.go(-1);"><span><span><?php echo  JText::_('COM_HANDOUT_BACK') ?></span></span></a></li>
            <?php
				// don't show details button on this page
    	        unset($this->buttons['details']); 
    	        //show remaining buttons
                foreach($this->buttons as $button) {
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
    
    <?php 
	if ($this->data->kunena_discuss_contents)
	{
		echo $this->data->kunena_discuss_contents;
	}
	?>
    
    <div class="clr"></div>
    <?php include_once(JPATH_COMPONENT . DS . 'footer.php'); ?>
</div>
