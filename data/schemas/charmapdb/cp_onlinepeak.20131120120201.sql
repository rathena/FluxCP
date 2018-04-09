CREATE TABLE IF NOT EXISTS `cp_onlinepeak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users` int(10) unsigned NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = MYISAM;
