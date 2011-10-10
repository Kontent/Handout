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

	<?php
	



		if($this->category->data->title != '') :
			?><h3 class="title"><?php echo $this->category->data->title;?></h3><?php
		endif;
		if($this->category->data->description != '') :
			?>
			<?php echo str_replace('<p>','<p class="title">',$this->category->data->description);?>
			<?php
		endif;
	?>
	
<?php endif; ?>