ALTER TABLE  `cp_createlog` ADD  `confirmed` TINYINT( 1 ) NOT NULL ,
ADD `confirm_code` VARCHAR( 32 ) NULL DEFAULT NULL ,
ADD `confirm_expire` DATETIME NULL DEFAULT NULL