CREATE TABLE IF NOT EXISTS `cp_servicedeskcat` (
  `cat_id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `display` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

INSERT INTO `cp_servicedeskcat` (`cat_id`, `name`, `display`) VALUES
(1, 'Technical Support', 1),
(2, 'General Support', 1),
(3, 'Report an Abuse', 1);
