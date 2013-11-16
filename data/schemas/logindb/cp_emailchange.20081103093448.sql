CREATE TABLE IF NOT EXISTS `cp_emailchange` (
  `id` int(11) NOT NULL auto_increment,
  `code` varchar(32) NOT NULL,
  `account_id` int(10) NOT NULL,
  `old_email` varchar(39) NOT NULL,
  `new_email` varchar(39) NOT NULL,
  `request_date` datetime NOT NULL,
  `request_ip` varchar(15) NOT NULL,
  `change_date` datetime default NULL,
  `change_ip` varchar(15) default NULL,
  `change_done` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;