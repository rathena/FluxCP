CREATE TABLE `cp_trusted` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `account_id` int(11) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `create_date` datetime NOT NULL,
  `delete_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;