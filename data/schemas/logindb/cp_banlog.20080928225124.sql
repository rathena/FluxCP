CREATE TABLE `cp_banlog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `account_id` int(11) unsigned NOT NULL,
  `banned_by` int(11) unsigned default NULL,
  `ban_type` tinyint(1) NOT NULL,
  `ban_until` datetime NOT NULL,
  `ban_date` datetime NOT NULL,
  `ban_reason` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;