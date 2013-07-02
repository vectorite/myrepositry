ALTER TABLE  `#__com_smarticons` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__com_smarticons` ADD  `asset_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'FK to the #__assets table.' AFTER  `idIcon`;
ALTER TABLE  `#__com_smarticons` DROP  `NewWindow`;
ALTER TABLE  `#__com_smarticons` DROP  `Component`;
