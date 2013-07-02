-- Extension specific tables
DROP TABLE IF EXISTS `#__foxcontact_sessions`;
DROP TABLE IF EXISTS `#__foxcontact_settings`;

-- Assets
DELETE FROM `#__assets` WHERE `name` = 'com_foxcontact';

-- Installed extension
DELETE FROM `#__extensions` WHERE `element` = 'com_foxcontact';

-- Installed modules
DELETE FROM `#__extensions` WHERE `element` = 'mod_foxcontact';

-- Administrator menu item and Site menu items
DELETE FROM `#__menu` WHERE `link` LIKE '%com_foxcontact%';

-- Site modules
DELETE FROM `#__modules` WHERE `module` = 'mod_foxcontact';

-- Joomla auto updater
DELETE FROM `#__update_sites` WHERE `name` LIKE '%foxcontact%';

