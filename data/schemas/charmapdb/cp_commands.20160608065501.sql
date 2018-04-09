CREATE TABLE IF NOT EXISTS `cp_commands` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `command` varchar(128) NOT NULL DEFAULT '0',
  `issuer` varchar(32) NOT NULL DEFAULT '0',
  `account_id` int(12) NOT NULL DEFAULT '0',
  `done` int(1) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
