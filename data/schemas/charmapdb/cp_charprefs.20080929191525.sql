CREATE TABLE IF NOT EXISTS `cp_charprefs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `account_id` int(11) unsigned NOT NULL,
  `char_id` int(11) unsigned NOT NULL,
  `name` varchar(80) NOT NULL,
  `value` varchar(255) default NULL,
  `create_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Character preferences.';
