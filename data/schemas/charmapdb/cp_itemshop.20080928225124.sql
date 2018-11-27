CREATE TABLE IF NOT EXISTS `cp_itemshop` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `nameid` int(11) unsigned NOT NULL default '0',
  `quantity` int(11) unsigned NOT NULL default '0',
  `cost` int(11) unsigned NOT NULL,
  `info` text,
  `create_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Item shop';
