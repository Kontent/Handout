<?php
/**
 * Handout - The Joomla Download Manager
 * @package 	Handout
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @copyright 	(C) 2009 Artio s.r.o.
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */

defined('_JEXEC') or die;

/* Display a category item (called by default.php)
*
* This template is called when user browses Handout.
*
* General variables  :
*	$this->category->data (object) : configuration values
*	$this->buttons (object) : permission values
*	$this->paths (object) : configuration values
*	$this->links (object) : path to links
*/

?>
<?php
	if($this->category->data->title != '') :
	?>
	<div class="hcat-head">
	<?php
		if($this->category->data->image):
			?>
			<div class="hcat-icon h<?php echo $this->category->data->image_position;?>" ><img src="<?php echo $this->category->paths->thumb; ?>" alt="<?php echo $this->category->data->title;?>" /></div><?php
		endif;

		 echo $this->loadTemplate('addthis');

		if($this->category->data->title != '') :
			?><h2 class="hcat-name"><?php echo $this->category->data->title;?></h2><?php
		endif;
		if($this->category->data->description != '') :
			?><div class="hcat-description"><?php echo $this->category->data->description;?></div><?php
		endif;
	?>
		<div class="clr"></div>
	</div>
	<div class="clr"></div>
<?php endif; ?>