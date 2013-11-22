CREATE TABLE IF NOT EXISTS `cp_servicedesk` (
  `ticket_id` int(6) NOT NULL AUTO_INCREMENT,
  `account_id` int(7) NOT NULL,
  `category` int(6) NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'Pending',
  `char_id` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sslink` text NOT NULL,
  `chatlink` text NOT NULL,
  `videolink` text NOT NULL,
  `subject` varchar(64) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `ip` varchar(15) NOT NULL DEFAULT '0',
  `team` int(1) NOT NULL DEFAULT '1',
  `curemail` text NOT NULL,
  `lastreply` varchar(24) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ticket_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1000 ;
