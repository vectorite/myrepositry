ALTER TABLE `#__ce_cf` CHANGE `name` `name` VARCHAR( 255 );
ALTER TABLE `#__ce_messages` CHANGE `category_id` `catid` INT(11) NOT NULL;
ALTER TABLE `#__ce_messages` 
	  ADD `access` INT( 11 ) UNSIGNED NOT NULL
	, ADD `language` CHAR( 7 ) NOT NULL;
