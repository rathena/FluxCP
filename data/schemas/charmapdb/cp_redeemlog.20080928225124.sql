CREATE TABLE IF NOT EXISTS `cp_redeemlog` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `nameid` int(11) unsigned NOT NULL default '0',
  `quantity` int(11) unsigned NOT NULL default '0',
  `cost` int(11) unsigned NOT NULL,
  `account_id` int(11) unsigned NOT NULL,
  `char_id` int(11) unsigned default NULL,
  `redeemed` tinyint(1) unsigned NOT NULL,
  `redemption_date` datetime default NULL,
  `purchase_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Log of redeemed donation items.';
