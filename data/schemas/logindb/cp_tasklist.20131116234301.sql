CREATE TABLE IF NOT EXISTS `cp_tasklist` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `author` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `priority` int(1) NOT NULL DEFAULT '0',
  `assigned` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
