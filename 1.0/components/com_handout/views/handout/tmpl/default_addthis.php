<?php
 /**
 * Handout - The Joomla Download Manager
 * @version 	$Id: default_addthis.php
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 **/

defined('_JEXEC') or die;

/* Display the AddThis social bookmarking applet
*
* This template is called when user views a category or document
*
*/

?>
	<?php
	if ($this->conf->show_share):
    ?>
     <div class="hdoc-share">
		<div class="addthis_toolbox addthis_default_style ">
        	<?php if ($this->conf->show_share_facebook): ?>
				<a class="addthis_button_facebook" rel="nofollow"></a>
            <?php endif; ?>
        	<?php if ($this->conf->show_share_twitter): ?>
				<a class="addthis_button_twitter" rel="nofollow"></a>
            <?php endif; ?>
            <?php if ($this->conf->show_share_googleplusone): ?>
				<a class="addthis_button_google_plusone" g:plusone:size="small" g:plusone:count="false" rel="nofollow" ></a>
            <?php endif; ?>
        	<?php if ($this->conf->show_share_email): ?>
				<a class="addthis_button_email" rel="nofollow"></a>
            <?php endif; ?>
        	<?php if ($this->conf->show_share_compact): ?>
				<a class="addthis_button_compact" rel="nofollow"></a>
            <?php endif; ?>
		</div>
		<?php 
        if ($this->conf->ga_code):
        ?>
        <script type="text/javascript">
		 var addthis_config = {
			data_ga_property: 'UA-<?php echo $this->conf->ga_code; ?>',
			data_track_clickback: true
		 };
		</script>
        <?php endif; ?>
		<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4ded34232959f910"></script>
    </div>
    <?php endif; ?>