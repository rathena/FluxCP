CREATE TABLE IF NOT EXISTS `cp_cmssettings` (
  `name` varchar(128) NOT NULL,
  `value` varchar(128) NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
