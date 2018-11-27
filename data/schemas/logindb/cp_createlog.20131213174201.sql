CREATE TABLE IF NOT EXISTS `cp_createlog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL,
  `userid` varchar(23) NOT NULL,
  `user_pass` varchar(32) NOT NULL,
  `sex` enum('M','F','S') NOT NULL DEFAULT 'M',
  `email` varchar(39) NOT NULL,
  `reg_date` datetime NOT NULL,
  `reg_ip` varchar(100) NOT NULL,
  `delete_date` datetime DEFAULT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '1',
  `confirm_code` varchar(32) DEFAULT NULL,
  `confirm_expire` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`userid`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
