
CREATE TABLE IF NOT EXISTS `cp_taskliststaff` (
  `account_id` int(7) NOT NULL,
  `account_name` varchar(32) NOT NULL,
  `preferred_name` varchar(32) NOT NULL,
  `emailalerts` int(1) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
