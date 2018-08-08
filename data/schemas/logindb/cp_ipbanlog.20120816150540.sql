CREATE TABLE IF NOT EXISTS `cp_ipbanlog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip_address` varchar(15) NOT NULL,
  `banned_by` int(11) unsigned default NULL,
  `ban_type` tinyint(1) NOT NULL,
  `ban_until` datetime NOT NULL,
  `ban_date` datetime NOT NULL,
  `ban_reason` text NOT NULL,
  PRIMARY KEY  (`id`),
  INDEX (`ip_address`),
  INDEX (`banned_by`)
) ENGINE=MyISAM ;
