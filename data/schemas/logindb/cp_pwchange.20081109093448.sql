CREATE TABLE `cp_pwchange` (
  `id` int(11) NOT NULL auto_increment,
  `account_id` int(10) NOT NULL,
  `old_password` varchar(32) NOT NULL,
  `new_password` varchar(32) default NULL,
  `change_date` datetime NOT NULL,
  `change_ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;