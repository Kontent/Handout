
 <div data-role="page" id="index">
		<div data-role="header" data-theme="b">
			<h1>Handout Documents</h1>
		</div><!-- /header -->
		<div data-role="content">	
			<a class="likelogo"  rel="external" href="#"><img src="<?php echo COM_HANDOUT_IMAGESPATH?>load.gif" alt="pic" /></a>
		<form action="<?php echo $this->action;?>" method="POST" enctype="multipart/form-data" rel="external">
				<ul class="loadform">
					<li>
						<label for="loadcode">Enter your download code:</label>
						<div class="relax">&nbsp;</div>
						<input data-theme="b" type="text" name="code" id="loadcode" value=""/>
					</li>
					<?php if ($this->usertype==2): //email required ?>
						<li>
						<label for="email">Enter a valid email address:</label>
						<div class="relax">&nbsp;</div>
						<input data-theme="b" type="email" name="email" id="email" value=""/>
					</li>
						<?php endif; ?>
					<li>
						<input type="submit" data-inline="true" class="loadbtn" value="Download file">
					</li>
				</ul>
			</form>
			<ul class="bottom">
				<li><a href="#" rel="external">Handout for Joomla</a></li>
				<li>|</li>
				<li><a href="#" rel="external">Switch to Desctop</a></li>
			</ul>
		</div><!-- /content -->
	</div>
	
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/mobile/1.0b1/jquery.mobile-1.0b1.min.js"></script>
	