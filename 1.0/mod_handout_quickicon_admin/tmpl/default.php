<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
<style type="text/css">
	#hadminpanel div.icon {
		float: left;
		margin-bottom: 5px;
		margin-right: 5px;
		text-align: center;
	}
	#hadminpanel div.icon a {
		border: 1px solid #F0F0F0;
		color: #666666;
		display: block;
		float: left;
		height: 97px;
		text-decoration: none;
		vertical-align: middle;
		width: 108px;
	}
	#hadminpanel img {margin: 0 auto;padding: 10px 0;}
	#hadminpanel span {display: block;text-align: center;}
	#hadminpanel div.icon a { color: #666666; text-decoration: none;}
	#hadminpanel div.icon a:hover {
		background: none repeat scroll 0 0 #F9F9F9;
		border-color: #EEEEEE #CCCCCC #CCCCCC #EEEEEE;
		border-left: 1px solid #EEEEEE;
		border-style: solid;
		border-width: 1px;
		color: #0B55C4;
	}
</style>
<div id="hadminpanel">
	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon">
			<a href="index.php?option=com_handout">
				<img src="components/com_handout/images/icon-48-home.png" />
				<span>Handout</span>
			</a>
		</div>
	</div>
</div>