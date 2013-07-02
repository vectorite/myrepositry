CREATE TABLE IF NOT EXISTS `#__workforce_departments` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `desc` text NOT NULL,
  `ordering` tinyint(11) NOT NULL default '0',
  `state` tinyint(3) NOT NULL default '1',
  `icon` varchar(255) NOT NULL default '',
  PRIMARY KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__workforce_employees` (
  `id` int(11) NOT NULL auto_increment,
  `fname` varchar(200) NOT NULL default '',
  `lname` varchar(200) NOT NULL default '',
  `position` varchar(255) NOT NULL default '',
  `department` int(11) NOT NULL default '0',
  `email` varchar(200) NOT NULL default '',
  `phone1` varchar(25) NOT NULL default '',
  `ext1` varchar(5) NOT NULL default '',
  `phone2` varchar(25) NOT NULL default '',
  `ext2` varchar(5) NOT NULL default '',
  `fax` varchar(25) NOT NULL default '',
  `street` varchar(255) NOT NULL default '',
  `street2` varchar(255) NOT NULL,
  `city` varchar(200) NOT NULL default '',
  `locstate` int(11) NOT NULL,
  `province` varchar(200) NOT NULL,
  `postcode` varchar(15) NOT NULL default '',
  `featured` int(11) NOT NULL,
  `icon` varchar(200) NOT NULL default '',
  `bio` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `state` tinyint(3) NOT NULL,
  `website` varchar(200) NOT NULL default '',
  `user_id` int(11) NOT NULL,
  `availability` text NOT NULL,
  PRIMARY KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__workforce_states` (
  `id` int(5) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `mc_name` char(2) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Default list of states' AUTO_INCREMENT=52 ;