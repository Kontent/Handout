<?php
/*
$cust_1 = $this->fields->getFieldByCaption('Custom Text'); // getFieldByCaption() allow you to get the field by the Caption. This is not the best way to get a field since changing the caption in the back-end will break the reference.
echo '<br />Field ID: ' . $cust_1->getId();

$cust_2 = $this->fields->getFieldById(29);  // getFieldById() is the ideal way of getting a field. The ID can be found at 'Custom Fields' section in Mosets Tree's back-end.
echo '<br />Name: ' . $cust_2->getName();
echo '<br />Has Caption? ' . (($cust_2->hasCaption()) ? 'Yes' : 'No');
echo '<br />Caption: ' . $cust_1->getCaption();
echo '<br />Value: ' . $cust_2->getValue();
echo '<br />Output: ' . $cust_2->getOutput(1);
echo '<hr />';
$this->fields->resetPointer();
while( $this->fields->hasNext() ) {
	$field = $this->fields->getField();
	echo '<br /><strong>' . $field->getCaption() . '</strong>';
	echo ': ';
	echo $field->getOutput(1); // getOutput() returns the formatted value of the field. ie: For a youtube video, the youtube player will be loaded
	// echo $field->getValue(); // getValue() returns the raw value without additional formatting. ie: When getting value from a Online Video field type, it will return the URL.
	$this->fields->next();
}
*/
	
?> 

<div id="listing">

<h2><?php 
$link_name = $this->fields->getFieldById(1);
$this->plugin( 'ahreflisting', $this->link, $link_name->getOutput(1), '', array("delete"=>true) ) 
?></h2>

<?php
if ( !empty($this->mambotAfterDisplayTitle) ) { 
	echo trim( implode( "\n", $this->mambotAfterDisplayTitle ) );
}

// Address
$address = '';
if( $this->config->getTemParam('displayAddressInOneRow','1') ) {
	$this->fields->resetPointer();
	$address_parts = array();
	$address_displayed = false;
	while( $this->fields->hasNext() ) {
		$field = $this->fields->getField();
		$output = $field->getOutput(1);
		if(in_array($field->getId(),array(4,5,6,7,8)) && !empty($output)) {
			$address_parts[] = $output;
		}
		$this->fields->next();
	}
	if( count($address_parts) > 0 ) { $address = implode(', ',$address_parts); }
}

// Other custom fields
echo '<div class="fields">';
$this->fields->resetPointer();
while( $this->fields->hasNext() ) {
	$field = $this->fields->getField();
	$value = $field->getValue();
	if( 
		( 
			(!$field->hasInputField() && !$field->isCore() && empty($value)) || (!empty($value) || $value == '0') ) 
			&& 
			// This condition ensure that fields listed in array() are skipped
			!in_array($field->getName(),array('link_name','link_desc')
		) 
		||
		// Fields in array() are always displayed regardless of its value.
		in_array($field->getName(),array('link_featured'))
	) {
		echo '<div class="row">';
		if($this->config->getTemParam('displayAddressInOneRow','1') && in_array($field->getId(),array(4,5,6,7,8)) && $address_field = $this->fields->getFieldById(4)) {
			if( $address_displayed == false ) {
				echo '<div class="caption">';
				if($address_field->hasCaption()) {
					echo $address_field->getCaption();
				}
				echo '</div>';
				echo '<div class="data">';
				echo $address_field->getDisplayPrefixText(); 
				echo $address;
				echo $address_field->getDisplaySuffixText(); 
				echo '</div>';
				$address_displayed = true;
			}
		} else {
			echo '<div class="caption">';
			if($field->hasCaption()) {
				//echo $field->getCaption();
			}
			echo '</div>';
			echo '<div class="data">';
			echo $field->getDisplayPrefixText(); 
			//echo $field->getOutput(1);
			echo $field->getDisplaySuffixText(); 
			echo '</div>';
		}
		echo '</div>';
	}
	$this->fields->next();
}

?>
<!-- Handout Plugin Start -->
<?php
$dispatcher	=& JDispatcher::getInstance();
	JPluginHelper::importPlugin('handout','handout');
		$args=array();
		$args[]=$this->link_id;
		
			 $results = $dispatcher->trigger('onDisplayMtreeListing',$args);
			 echo $results[0];


?>
<!-- Handout Plugin End -->
<?php 
echo '</div>';

if( $this->show_actions_rating_fav ) {
	?>
	<div class="actions-rating-fav">
	<?php if( $this->show_actions ) { ?>
	<div class="actions" style="text-align:center<?php echo ($this->show_actions_rating_fav==1)?';border:0':''; ?>"><?php 
		$this->plugin( 'ahrefreview', $this->link, array("rel"=>"nofollow") ); 
		$this->plugin( 'ahrefrecommend', $this->link, array("rel"=>"nofollow") );	
		$this->plugin( 'ahrefprint', $this->link );
		$this->plugin( 'ahrefcontact', $this->link, array("rel"=>"nofollow") );
		$this->plugin( 'ahrefvisit', $this->link );
		$this->plugin( 'ahrefreport', $this->link, array("rel"=>"nofollow") );
		$this->plugin( 'ahrefclaim', $this->link, array("rel"=>"nofollow") );
		$this->plugin( 'ahrefownerlisting', $this->link );
		$this->plugin( 'ahrefmap', $this->link );
	?></div><?php
	}
	if($this->config->get('show_rating')) {
		echo '<div class="rating">';
		$this->plugin( 'ratableRating', $this->link, $this->link->link_rating, $this->link->link_votes); 
		echo '<div id="total-votes">';
		if( $this->link->link_votes <= 1 ) {
			echo $this->link->link_votes . " " . strtolower(JText::_( 'Vote' ));
		} elseif ($this->link->link_votes > 1 ) {
			echo $this->link->link_votes . " " . strtolower(JText::_( 'Votes' ));
		}
		echo '</div>';
		echo '</div>';
	}
	if($this->config->get('show_favourite')) {
	?>
	<div class="favourite">
	<span class="fav-caption"><?php echo JText::_( 'Favoured' ) ?>:</span>
	<div id="fav-count"><?php echo number_format($this->total_favourites,0,'.',',') ?></div><?php 
		if($this->my->id > 0){ 
			if($this->is_user_favourite) {
				?><div id="fav-msg"><a href="javascript:fav(<?php echo $this->link->link_id ?>,-1);"><?php echo JText::_( 'Remove favourite' ) ?></a></div><?php 
			} else {
				?><div id="fav-msg"><a href="javascript:fav(<?php echo $this->link->link_id ?>,1);"><?php echo JText::_( 'Add as favourite' ) ?></a></div><?php 
				}
		} ?>
	</div><?php
	}
	?></div><?php 
}

if ( !empty($this->mambotBeforeDisplayContent) && $this->mambotBeforeDisplayContent[0] <> '' ) { 
	echo trim( implode( "\n", $this->mambotBeforeDisplayContent ) ); 
}

echo '<div class="listing-desc">';

$images = $this->images;
if ($this->config->getTemParam('skipFirstImage','0') == 1) {
	array_shift($images);
}

if (!empty($images) && $this->config->getTemParam('listingDetailsImagesOutputMode','1') == 3) { 
	echo '<div class="thumbnail' . (($this->config->getTemParam('imageDirectionListingSummary','right')=='right') ? '':'-left') . '">';
	echo '<a href="' . JRoute::_('index.php?option=com_mtree&task=viewimage&img_id=' . $this->link->img_id . '&Itemid=' . $this->Itemid) . '">';
	$this->plugin( 'mt_image', $images[0]->filename, '3', $this->link->link_name );
	echo '</a>';
	if( $this->total_images > 1 ) {
		echo '<div style="width:' . ($this->config->get('resize_listing_size') + 10) . 'px"><a href="' . JRoute::_('index.php?option=com_mtree&task=viewgallery&link_id=' . $this->link->link_id . '&Itemid=' . $this->Itemid) . '">' . JText::_( 'View gallery' ) . '</a></div>';
	}
	echo '</div>';
}
if(!is_null($this->fields->getFieldById(2))) { 
	$link_desc = $this->fields->getFieldById(2);
	echo $link_desc->getOutput(1);
}
echo '</div>';
if ( !empty($this->mambotAfterDisplayContent) ) { echo trim( implode( "\n", $this->mambotAfterDisplayContent ) ); }
?>
</div>