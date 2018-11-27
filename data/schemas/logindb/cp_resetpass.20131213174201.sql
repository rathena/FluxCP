CREATE TABLE IF NOT EXISTS `cp_resetpass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL,
  `account_id` int(10) NOT NULL,
  `old_password` varchar(32) NOT NULL,
  `new_password` varchar(32) DEFAULT NULL,
  `request_date` datetime NOT NULL,
  `request_ip` varchar(15) NOT NULL,
  `reset_date` datetime DEFAULT NULL,
  `reset_ip` varchar(15) DEFAULT NULL,
  `reset_done` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
