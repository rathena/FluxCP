CREATE TABLE IF NOT EXISTS `cp_xferlog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `from_account_id` int(10) unsigned NOT NULL,
  `target_account_id` int(10) unsigned NOT NULL,
  `target_char_id` int(11) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  `for_free` tinyint(1) unsigned NOT NULL default '0',
  `transfer_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Credit transfer log.';
