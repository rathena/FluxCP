CREATE TABLE `cp_loginlog` (
  `id` int(11) NOT NULL auto_increment,
  `account_id` int(10) default NULL,
  `username` varchar(23) NOT NULL,
  `password` varchar(32) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `login_date` datetime NOT NULL,
  `error_code` tinyint(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;