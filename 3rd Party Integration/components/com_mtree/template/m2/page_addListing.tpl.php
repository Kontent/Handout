<script language="javascript" type="text/javascript" src="<?php echo $this->jconf['live_site'] . $this->mtconf['relative_path_to_js_library']; ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->jconf['live_site']; ?>/components/com_mtree/js/category.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->jconf['live_site']; ?>/components/com_mtree/js/addlisting.js"></script>
<?php if( $this->mtconf['allow_imgupload'] && $this->mtconf['images_per_listing'] > 0 ) 
{
?><script language="javascript" type="text/javascript" src="<?php echo $this->jconf['live_site']; ?>/components/com_mtree/js/jquery-ui-personalized-1.5.3.min.js"></script>	<?php
} 
if( $this->mtconf['use_map'] == 1 ) 
{ 
?><script language="javascript" type="text/javascript" src="<?php echo $this->jconf['live_site']; ?>/components/com_mtree/js/map.js"></script><?php
}
?>

<script language="javascript" type="text/javascript">
	jQuery.noConflict();
	var mosConfig_live_site=document.location.protocol+'//' + location.hostname + '<?php echo ($_SERVER["SERVER_PORT"] == 80) ? "":":".$_SERVER["SERVER_PORT"] ?><?php echo substr($_SERVER["PHP_SELF"],0,strrpos($_SERVER["PHP_SELF"],"/")); ?>';
	var active_cat=<?php echo $this->cat_id; ?>;
	var attCount=0;
	var attNextId=1;
	var maxAtt=<?php echo $this->mtconf['images_per_listing']; ?>;
	var msgAddAnImage = '<?php echo addslashes(JText::_( 'Add an image' )) ?>';
	var txtRemove = '<?php echo addslashes(JText::_( 'Remove' )) ?>';
	
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel'){submitform( 'viewlink' );return;}
		if (form.link_name.value == ""){
			alert( "<?php echo addslashes(JText::_( 'Please fill in link name' )) ?>" );
			<?php
			$editor = &JFactory::getEditor();
			$requiredFields = array();
			$this->fields->resetPointer();
			while( $this->fields->hasNext() ) {
				$field = $this->fields->getField();
				if(!in_array($field->name,array('link_hits','link_votes','link_rating','link__featured'))) {
					if( ($field->isRequired() && !in_array($field->name,array('link_name','link_desc'))) || ($field->isRequired() && $this->mtconf['use_wysiwyg_editor'] == 0 && $field->name == 'link_desc') ) {
						if( $field->isFile() )
						{
							echo "\n";
							echo '} else if (';
							echo 'isEmpty(\'' . $field->getName() . '\')';
							echo ' && ';
							echo '(';
							echo '(typeof form.'.$field->getKeepFileName().' == \'undefined\')';
							echo '||';
							echo '(typeof form.'.$field->getKeepFileName().' == \'object\' && form.'.$field->getKeepFileName().'.checked == false)';
							echo ')';
							echo ') {'; 
							echo "\n";
							echo 'alert("' . addslashes(JText::_( 'Please complete this field' ) . $field->caption) . '");';
						}
						else
						{
							echo '} else if (isEmpty(\'' . $field->getName() . '\')) {'; 
							echo 'alert("' . addslashes(JText::_( 'Please complete this field' ) . $field->caption) . '");';
						}
					}
					if($field->hasJSValidation()) {
						echo "\n";
						echo $field->getJSValidation();
					}
				}
				$this->fields->next();
			}
			?>
		} else {
			<?php
			if($this->mtconf['use_wysiwyg_editor'] == 1 && !is_null($this->fields->getFieldById(2))) {
				echo $editor->save( 'link_desc' );
			}
			if( $this->mtconf['allow_imgupload'] && $this->mtconf['images_per_listing'] > 0 ) {
			?>
			var hash = jQuery("#sortableimages").sortable('serialize');
			if(hash != ''){document.adminForm.img_sort_hash.value=hash;}
			<?php } ?>
			form.task.value=pressbutton;
			if(attCount>0 && checkImgExt(attCount,jQuery("input[@type=file][@name='image[]']"))==false) {
				alert('<?php echo addslashes(JText::_( 'Please select a jpg png or gif file for the images' )) ?>');
				return;
			} else {
				form.submit();
			}
		}
	}
</script>

 
<h2 class="contentheading"><?php echo ($this->link->link_id) ? JText::_( 'Edit listing' ) : 	JText::_( 'Add listing' ); ?></h2>

<center>
<form action="<?php echo JRoute::_("index.php") ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm">
<table width="100%" cellpadding="0" cellspacing="4" border="0" align="center" id="mtreetbl">
	
	<?php echo ( (isset($this->warn_duplicate) && $this->warn_duplicate == 1) ? '<tr><td colspan="2">' . JText::_( 'There is already a pending approval for modification' ) . '</td></tr>' : '' )?>
	
	<tr><td colspan="2" align="left">
		<input type="button" value="<?php echo JText::_( 'Submit listing' ) ?>" onclick="javascript:submitbutton('savelisting')" class="button" /> <input type="button" value="<?php echo JText::_( 'Cancel' ) ?>" onclick="history.back();" class="button" />
	</td></tr>
	<tr valign="bottom">
		<td width="20%" align="left" valign="top"><?php echo JText::_( 'Category' ) ?>:</td>
		<td width="80%" align="left" colspan="2">
			<?php if($this->mtconf['allow_changing_cats_in_addlisting']) { ?>
			<ul class="linkcats" id="linkcats">
			<li id="lc<?php echo $this->cat_id; ?>"><?php echo $this->pathWay->printPathWayFromCat_withCurrentCat( $this->cat_id, '' ); ?></li>
			<?php
			if ( !empty($this->other_cats) ) {
				foreach( $this->other_cats AS $other_cat ) {
					if ( is_numeric( $other_cat ) ) {
						echo '<li id="lc' . $other_cat . '">';
						if($this->mtconf['allow_user_assign_more_than_one_category']) {
							echo '<a href="javascript:remSecCat('.$other_cat.')">'.JText::_( 'Remove' ).'</a>';
						}
						echo $this->pathWay->printPathWayFromCat_withCurrentCat( $other_cat, '' ) . '</li>';
					}
				}
			}
			?>
			</ul>
			<a href="#" onclick="javascript:togglemc();return false;" id="lcmanage"><?php echo JText::_( 'Manage' ); ?></a>
			<div id="mc_con">
			<div id="mc_selectcat">
				<span id="mc_active_pathway"><?php echo $this->pathWay->printPathWayFromCat_withCurrentCat( $this->cat_id, '' ); ?></span>
				<?php echo $this->catlist; ?>
			</div>
			<input type="button" class="button" value="<?php echo JText::_( 'Update category' ) ?>" id="mcbut1" onclick="updateMainCat()" />
			<?php if($this->mtconf['allow_user_assign_more_than_one_category']) { ?>
			<input type="button" class="button" value="<?php echo JText::_( 'Also appear in this categories' ) ?>" id="mcbut2" onclick="addSecCat()" />
			<?php } ?>
			</div>
			<?php } else {
			
				echo $this->pathWay->printPathWayFromCat_withCurrentCat( $this->cat_id, '' );
				
			} ?>
		</td>
	</tr>
	<?php
	$this->fields->resetPointer();
	while( $this->fields->hasNext() ) {
		$field = $this->fields->getField();
		if($field->hasInputField()) {
			echo '<tr><td valign="top" align="left">';
			if($field->getCaption() != false) {
				if($field->isRequired()) {
					echo '<strong>' . $field->getCaption() . '</strong>:';
				} else {
					echo $field->getCaption() . ':';
				}
			}
			echo '</td><td align="left">';
			echo $field->getModPrefixText();
			echo $field->getInputHTML();
			
			echo $field->getModSuffixText();
			echo '</td></tr>';
		}
	
		$this->fields->next();
	} ?>
	
	<?php /* Handout Plugin output */?>
	
	<?php 
	$dispatcher	=& JDispatcher::getInstance();
	JPluginHelper::importPlugin('handout','handout');
	$args[]='';
	$args[]='';
	$args[]=true;
	$args[]=$this->link->link_id;
	
			 $results = $dispatcher->trigger('getUploadForm',$args);
	          echo $results[0];
			 //	echo var_dump($results);
	?>

	
	<!-- <tr><td>
		<?php //echo '<label>'.JText::_('Handout File:').'</label></td><td><input type="file" name="handout_file" />'; ?>
		</td>
		</tr>-->
	<?php //end Handout Plugin?>
</table>
	
<script language="javascript">




 function addRow(count)
 { count ++;
   var tbl = document.getElementById('mtreetbl');
   var lastRow = tbl.rows.length-1;
   
   var iteration = lastRow;
   var row = tbl.insertRow(lastRow);
 
   var cellLeft = row.insertCell(0);
   var textNode = document.createTextNode('');
   cellLeft.appendChild(textNode);
   
   
   var cellRight = row.insertCell(1);
   var el = document.createElement('input');
   el.type = 'file';
   el.name = 'handout_file_' + count;
   cellRight.appendChild(el);
   return count;
 }


</script>

<table width="100%" cellpadding="0" cellspacing="0">
<?php if( $this->mtconf['use_map'] == 1 ) { ?>
<tr><td>
<fieldset>
	<legend><?php echo JText::_( 'Map' ) ?></legend>
	<?php
	$width = '100%';
	$height = '200px';
	?>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $this->mtconf['gmaps_api_key']; ?>" type="text/javascript"></script>
	<script type="text/javascript">
		var map = null;
	    var geocoder = null;
		var marker = null;
		var txtEnterAddress = '<?php echo JText::_( 'Enter an address and press Locate in map or move the red marker to the location in the map below.', true ); ?>';
		var txtLocateInMap = '<?php echo  JText::_( 'Locate in map', true ); ?>';
		var txtLocating = '<?php echo JText::_( 'Locating...', true ); ?>';
		var txtNotFound = '<?php echo JText::_( 'Not found:', true ); ?>';
		var defaultCountry = '<?php echo addslashes($this->mtconf['map_default_country']); ?>';
		var defaultState = '<?php echo addslashes($this->mtconf['map_default_state']); ?>';
		var defaultCity = '<?php echo addslashes($this->mtconf['map_default_city']); ?>';
		var defaultLat = '<?php echo addslashes($this->mtconf['map_default_lat']); ?>';
		var defaultLng = '<?php echo addslashes($this->mtconf['map_default_lng']); ?>';
		var defaultZoom = <?php echo addslashes($this->mtconf['map_default_zoom']); ?>;
		var linkValLat = '<?php echo $this->link->lat; ?>';
		var linkValLng = '<?php echo $this->link->lng; ?>';
		var linkValZoom = '<?php echo $this->link->zoom; ?>';
		var mapControl = [new <?php echo implode("(), new ",explode(',',$this->mtconf['map_control'])); ?>()];
	</script>
	<div style="padding:4px 0; width:95%"><input type="button" onclick="locateInMap()" value="<?php echo JText::_( 'Locate in map' ); ?>" name="locateButton" id="locateButton" /><span style="padding:0px; margin:3px" id="map-msg"></span></div>
	<div id="map" style="width:<?php echo $width; ?>;height:<?php echo $height; ?>"></div>
	<input type="hidden" name="lat" id="lat" value="<?php echo $this->link->lat; ?>" />
	<input type="hidden" name="lng" id="lng" value="<?php echo $this->link->lng; ?>" />
	<input type="hidden" name="zoom" id="zoom" value="<?php echo $this->link->zoom; ?>" />
	
</fieldset>
</td></tr>
<?php 
}

if( $this->mtconf['allow_imgupload'] && $this->mtconf['images_per_listing'] > 0 ) { ?>
<tr><td>
<fieldset class="images">
	<legend><?php echo JText::_( 'Images' ) ?></legend>
	<span><small><?php echo JText::_( 'Drag to sort images, deselect checkbox to remove.' ); ?></small></span>
	<ol id="sortableimages"><?php
	foreach( $this->images AS $image ) {
		echo '<li id="img_' . $image->img_id . '">';
		echo '<input type="checkbox" name="keep_img[]" value="' . $image->img_id . '" checked />';
		echo '<a href="' . $this->jconf['live_site'] . $this->mtconf['relative_path_to_listing_medium_image'] . $image->filename . '" target="_blank">';
		echo '<img border="0" style="position:relative;border:1px solid black;" align="middle" src="' . $this->jconf['live_site'] . $this->mtconf['relative_path_to_listing_small_image'] . $image->filename . '" alt="' . $image->filename . '" />';
		echo '</a>';
		echo '</li>';
	}
	?>
	</ol>
	<ol id="uploadimages">
	</ol>
	<div class="actionimages">
		<a href="javascript:addAtt();" id="add_att"><?php if(count($this->images) < $this->mtconf['images_per_listing']) { ?><?php echo JText::_( 'Add an image' ) ?><?php } ?></a>
		<?php if( $this->image_size_limit > 0 ) { ?>
		<br /><small><?php echo sprintf( JText::_( 'Limit of x per image' ), $this->image_size_limit )?></small>
		<?php } ?>
	</div>
</fieldset>
<input type="hidden" name="img_sort_hash" value="" />
</td></tr>
<?php } ?>
<tr><td align="left">
	<br />
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="savelisting" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />			
	<?php if ( $this->link->link_id == 0 ) { ?>
	<input type="hidden" name="cat_id" value="<?php echo $this->cat_id ?>" />
	<?php } else { ?>
	<input type="hidden" name="link_id" value="<?php echo $this->link->link_id ?>" />
	<input type="hidden" name="cat_id" value="<?php echo $this->cat_id ?>" />
	<?php } ?>
	<input type="hidden" name="other_cats" id="other_cats" value="<?php echo ( ( !empty($this->other_cats) ) ? implode(', ', $this->other_cats) : '' ) ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="button" value="<?php echo JText::_( 'Submit listing' ) ?>" onclick="javascript:submitbutton('savelisting')" class="button" /> <input type="button" value="<?php echo JText::_( 'Cancel' ) ?>" onclick="history.back();" class="button" />
</td></tr>
</table>
</form>

</center>