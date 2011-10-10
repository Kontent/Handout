
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
?>

<div data-role="page" id="popup">
		<div data-role="header" data-theme="d">
			<h1>Agreement terms</h1>
		</div><!-- /header -->
		<div data-role="content">	
		
			<p> 
			<?php echo $this->license; ?> 
			
			
			</p>
			
			<a class="loadbtn" rel="external" href="index.php?option=com_handout&task=license_result&agree=1&inline=1&gid=<?php echo $this->data->id ?>" data-role="button" data-theme="b"  >Agree &amp; Srart Download</a>
			
			
			<a href="javascript:history.go(-1);" data-role="button"  data-theme="c">Cancel</a>
		</div><!-- /content -->
	</div><!-- /page -->
	
	
<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/mobile/1.0b1/jquery.mobile-1.0b1.min.js"></script>
	
