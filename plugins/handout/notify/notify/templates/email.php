<?php
/**
 * @package 	Handout Notify
 * @copyright 	(C) 2011 Kontent Design. All rights reserved.
 * @copyright 	(C) 2003-2008 The DOCman Development Team
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link 		http://www.sharehandouts.com
 */
defined('_JEXEC') or die('Restricted access');

/*
These are the variables you can use in the body

Action				$this->action (upload, edit, or download)

Info
	Date			$this->info->date
	Time			$this->info->time
	OS				$this->info->os
	Browser 		$this->info->browser

User
	User ID			$this->user->id
	Name			$this->user->name
	Username		$this->user->username
	E-mail			$this->user->email
	Usertype		$this->user->usertype
	Group ID		$this->user->gid
	Registered		$this->user->registerDate
	Last Visit		$this->user->lastvisitDate
	IP				$this->user->ip

Site
	Site Name		$this->site->name
	Site Url		$this->site->url

File
	Filename		$this->file->name
	Mimetype		$this->file->mime
	Extension		$this->file->ext
	Filesize		$this->file->size
	File date		$this->file->date

Document
	Title			$this->doc->docname
	ID				$this->doc->id
	Category ID		$this->doc->catid
	Category name	$this->doc->category
	Description		$this->doc->docdescription
	Date			$this->doc->docdate_published
	Owner			$this->doc->docowner
	Filename		$this->doc->docfilename
	Url				$this->doc->docurl
	Hits			$this->doc->doccounter
	Last Updated	$this->doc->doclastupdateon
	License			$this->doc->doclicense_display
	License ID		$this->doc->doclicense_id
	Access			$this->doc->access
	Published		$this->doc->published
	Checked out		$this->doc->checked_out
	Time			$this->doc->checked_out_time
	Last Updater	$this->doc->doclastupdateby
	Submitted by	$this->doc->docsubmittedby
	Maintainer		$this->doc->docmaintainedby

 */
?>

<p>Hello,</p>
<p>The user <?php echo $this->actionlang?> at <a href="<?php echo $this->site->url?>"><?php echo $this->site->name?></a> on <?php echo $this->info->date?> at <?php echo $this->info->time?>.</p>

<br />

<h2>User Information</h2>
<table cellpadding="5" style="border:1px silver dotted">
<tr>
	<th style="text-align:left;vertical-align:top;">Name</th>
	<td><?php echo $this->user->name?></td>
</tr>
<tr>
	<th style="text-align:left;vertical-align:top;">Username</th>
	<td><?php echo $this->user->username?></td>
</tr>
<tr>
	<th style="text-align:left;vertical-align:top;">E-mail</th>
	<td><?php echo $this->user->email?></td>
</tr>
<tr>
	<th style="text-align:left;vertical-align:top;">User Group</th>
	<td><?php echo $this->user->usertype?></td>
</tr>
<tr>
	<th style="text-align:left;vertical-align:top;">IP</th>
	<td><?php echo $this->user->ip?></td>
</tr>
<tr>
	<th style="text-align:left;vertical-align:top;">Browser &amp; OS</th>
	<td><?php echo $this->info->browser?> on <?php echo $this->info->os?></td>
</tr>
</table>
<br />

<?php if( $this->action !='upload') {?>
<h2>Document Information</h2>
	<table cellpadding="5" style="border:1px silver dotted">
		<tr>
			<th style="text-align:left;vertical-align:top;">Title</th>
			<td>
				<a href="<?php echo $this->doc->link?>">
					<?php echo $this->doc->docname?>
				</a>
			</td>
		</tr>
		<tr>
			<th style="text-align:left;vertical-align:top;">Description</th>
			<td><?php echo $this->doc->docdescription?></td>
		</tr>
		<tr>
			<th style="text-align:left;vertical-align:top;">Owner</th>
			<td><?php echo $this->doc->docowner?></td>
		</tr>
		<tr>
			<th style="text-align:left;vertical-align:top;">Hits</th>
			<td><?php echo $this->doc->doccounter?></td>
		</tr>
		<tr>
			<th style="text-align:left;vertical-align:top;">Published</th>
			<td><?php echo $this->doc->published?></td>
		</tr>
	</table>
<?php } ?>
<br />

<h2>File Information</h2>
<table cellpadding="5" style="border:1px silver dotted">
	<tr>
		<th style="text-align:left;vertical-align:top;">Name</th>
		<td><?php echo $this->file->name?></td>
	</tr>
	<tr>
		<th style="text-align:left;vertical-align:top;">MIME Type</th>
		<td><?php echo $this->file->mime?></td>
	</tr>
	<tr>
		<th style="text-align:left;vertical-align:top;">Extension</th>
		<td><?php echo $this->file->ext?></td>
	</tr>
	<tr>
		<th style="text-align:left;vertical-align:top;">File Size</th>
		<td><?php echo $this->file->size?></td>
	</tr>
</table>
<br />
