CREATE TABLE IF NOT EXISTS `cp_pwchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL,
  `old_password` varchar(32) NOT NULL,
  `new_password` varchar(32) DEFAULT NULL,
  `change_date` datetime NOT NULL,
  `change_ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
