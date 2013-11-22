CREATE TABLE IF NOT EXISTS `cp_servicedeska` (
  `action_id` int(6) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(6) NOT NULL,
  `author` varchar(32) NOT NULL,
  `text` text NOT NULL,
  `action` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip` varchar(15) NOT NULL DEFAULT '0',
  `isstaff` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
