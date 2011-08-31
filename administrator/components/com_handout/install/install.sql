
CREATE TABLE IF NOT EXISTS `jos_handout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL DEFAULT '1',
  `docname` text NOT NULL,
  `docdescription` longtext,
  `docdate_published` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `docowner` int(4) NOT NULL DEFAULT '-1',
  `docfilename` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `docurl` text,
  `doccounter` int(11) DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `docthumbnail` text,
  `doclastupdateon` datetime DEFAULT '0000-00-00 00:00:00',
  `doclastupdateby` int(5) NOT NULL DEFAULT '-1',
  `docsubmittedby` int(5) NOT NULL DEFAULT '-1',
  `docmaintainedby` int(5) DEFAULT '0',
  `doclicense_id` int(5) DEFAULT '0',
  `doclicense_display` tinyint(1) NOT NULL DEFAULT '0',
  `docversion` varchar(20) DEFAULT '',
  `doclanguage` varchar(10) DEFAULT 'en-GB',
  `doc_meta_keywords` text NOT NULL,
  `doc_meta_description` text NOT NULL,
  `kunena_discuss_id` int(11) unsigned NOT NULL DEFAULT '0',
  `mtree_id` int(11) unsigned NOT NULL DEFAULT '0',
  `access` int(11) unsigned NOT NULL DEFAULT '0',
  `attribs` text NOT NULL,
  `download_limit` int(11) NOT NULL,
  `allow_single_download` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pub_own_cat_name` (`published`,`docowner`,`catid`,`docname`(64)),
  KEY `pub_own_cat_date` (`published`,`docowner`,`catid`,`docdate_published`),
  KEY `own_pub_cat_count` (`docowner`,`published`,`catid`,`doccounter`),
  KEY `own_pub_cat_id` (`docowner`,`published`,`catid`,`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__handout_groups` (
  `groups_id` int(11) NOT NULL auto_increment,
  `groups_name` text NOT NULL,
  `groups_description` longtext,
  `groups_access` tinyint(4) NOT NULL default '1',
  `groups_members` text,
  PRIMARY KEY  (`groups_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__handout_history` (
  `id` int(11) NOT NULL auto_increment,
  `doc_id` int(11) NOT NULL,
  `revision` int(5) NOT NULL default '1',
  `his_date` datetime NOT NULL,
  `his_who` int(11) NOT NULL,
  `his_obs` longtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__handout_licenses` (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `license` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__handout_log` (
  `id` int(11) NOT NULL auto_increment,
  `log_docid` int(11) NOT NULL,
  `log_code` varchar(70) NOT NULL,
  `log_ip` text NOT NULL,
  `log_datetime` datetime NOT NULL,
  `log_user` int(11) NOT NULL default '0',
  `log_browser` text,
  `log_os` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__handout_codes` (
`id` int(11) NOT NULL auto_increment ,
`name` varchar(70) NOT NULL , 
`docid` int(11) NOT NULL  ,
`usage` tinyint( 1 ) NOT NULL ,
`published` tinyint( 1 ) NOT NULL ,
  PRIMARY KEY  (`id`) 
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;
