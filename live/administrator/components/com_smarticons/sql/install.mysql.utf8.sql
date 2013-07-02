DROP TABLE IF EXISTS `#__com_smarticons`;
CREATE TABLE IF NOT EXISTS `#__com_smarticons` (
  `idIcon` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'FK to the #__assets table.',
  `catid` int(11) NOT NULL,
  `Name` varchar(45) NOT NULL,
  `Title` varchar(45) NOT NULL,
  `Text` varchar(45) NOT NULL,
  `Target` varchar(255) NOT NULL,
  `Icon` varchar(255) NOT NULL,
  `Display` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(5) NOT NULL,
  `params` text NOT NULL,
  `checked_out` tinyint(1) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`idIcon`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;