CREATE TABLE IF NOT EXISTS `cp_cmspages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `body` text NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM 
INSERT INTO `cp_cmspages` (`path` ,`title` ,`body` ,`modified`) VALUES ('rules', 'Rules', 'This is a rules page.', '2013-11-20 00:00:00');