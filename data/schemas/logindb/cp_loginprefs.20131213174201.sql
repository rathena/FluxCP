CREATE TABLE IF NOT EXISTS `cp_loginprefs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL,
  `name` varchar(80) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Account preferences' AUTO_INCREMENT=1 ;
