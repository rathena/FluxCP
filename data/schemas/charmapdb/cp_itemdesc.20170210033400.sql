CREATE TABLE IF NOT EXISTS `cp_itemdesc` (
  `itemid` int(10) unsigned NOT NULL auto_increment,
  `itemdesc` text NOT NULL,
  PRIMARY KEY  (`itemid`)
) ENGINE=MyISAM COMMENT='Stored item descriptions from parsed itemInfo.';
