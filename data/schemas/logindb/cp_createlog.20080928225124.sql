CREATE TABLE `cp_createlog` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `account_id` int(11) unsigned NOT NULL,
  `userid` varchar(23) NOT NULL,
  `user_pass` varchar(32) NOT NULL,
  `sex` enum('M','F','S') NOT NULL default 'M',
  `email` varchar(39) NOT NULL,
  `reg_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `reg_ip` varchar(100) NOT NULL,
  `delete_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`userid`)
) ENGINE=MyISAM ;