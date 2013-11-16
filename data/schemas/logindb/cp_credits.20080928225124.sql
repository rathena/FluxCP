CREATE TABLE `cp_credits` (
  `account_id` int(11) unsigned NOT NULL,
  `balance` int(11) unsigned NOT NULL default '0',
  `last_donation_date` datetime default NULL,
  `last_donation_amount` float unsigned default NULL,
  PRIMARY KEY  (`account_id`)
) ENGINE=MyISAM COMMENT='Donation credits balance for a given account.';