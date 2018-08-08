CREATE TABLE IF NOT EXISTS `cp_cmspages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `body` text NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `cp_cmspages` (`id`, `path`, `title`, `body`, `modified`) VALUES
(1, 'rules', 'Rules', 'This is a rules page.', '2013-11-20 00:00:00'),
(2, 'downloads', 'Downloads', 'This is a download page.', '2017-07-20 00:00:00');
