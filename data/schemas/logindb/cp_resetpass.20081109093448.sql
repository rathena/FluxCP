CREATE TABLE `cp_resetpass` (
  `id` int(11) NOT NULL auto_increment,
  `code` varchar(32) NOT NULL,
  `account_id` int(10) NOT NULL,
  `old_password` varchar(32) NOT NULL,
  `new_password` varchar(32) default NULL,
  `request_date` datetime NOT NULL,
  `request_ip` varchar(15) NOT NULL,
  `reset_date` datetime default NULL,
  `reset_ip` varchar(15) default NULL,
  `reset_done` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;