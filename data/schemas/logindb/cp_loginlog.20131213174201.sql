CREATE TABLE IF NOT EXISTS `cp_loginlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) DEFAULT NULL,
  `username` varchar(23) NOT NULL,
  `password` varchar(32) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `login_date` datetime NOT NULL,
  `error_code` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
