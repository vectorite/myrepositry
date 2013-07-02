CREATE TABLE IF NOT EXISTS `#__sh404sef_urls` (
        `id` int(11) NOT NULL auto_increment,
        `cpt` int(11) NOT NULL default '0',
        `rank` int(11) NOT NULL default '0',
        `oldurl` varchar(255) NOT NULL default '',
        `newurl` varchar(255) NOT NULL default '',
        `dateadd` date NOT NULL default '0000-00-00',
        PRIMARY KEY (`id`),
        KEY `newurl` (`newurl`),
        KEY `rank` (`rank`),
        KEY `oldurl` (`oldurl`)
        ) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__sh404sef_metas` (
        `id` int(11) NOT NULL auto_increment,
        `newurl` varchar(255) NOT NULL default '',
        `metadesc` varchar(255) default '',
        `metakey` varchar(255) default  '',
        `metatitle` varchar(255) default '',
        `metalang` varchar(30) default '',
        `metarobots` varchar(30) default '',
        `canonical` varchar(255) default '',
        `og_enable` tinyint(3) NOT NULL DEFAULT '2',
        `og_type` varchar(30) default '',
        `og_image` varchar(255) default '',
        `og_enable_description` tinyint(3) NOT NULL DEFAULT '2',
        `og_enable_site_name` tinyint(3) NOT NULL DEFAULT '2',
        `og_site_name` varchar(255) default '',
        `fb_admin_ids` varchar(255) default '',
        `og_enable_location` tinyint(3) NOT NULL DEFAULT '2',
        `og_latitude` varchar(30) default '',
        `og_longitude` varchar(30) default '',
        `og_street_address` varchar(255) default '',
        `og_locality` varchar(255) default '',
        `og_postal_code` varchar(30) default '',
        `og_region` varchar(255) default '',
        `og_country_name` varchar(255) default '',
        `og_enable_contact` tinyint(3) NOT NULL DEFAULT '2',
        `og_email` varchar(255) default '',
        `og_phone_number` varchar(255) default '',
        `og_fax_number` varchar(255) default '',
        `og_enable_fb_admin_ids` tinyint(3) NOT NULL DEFAULT '2',
        PRIMARY KEY (`id`),
        KEY `newurl` (`newurl`)
        ) DEFAULT CHARSET=utf8;
        
CREATE TABLE IF NOT EXISTS `#__sh404sef_aliases` (
        `id` int(11) NOT NULL auto_increment,
        `newurl` varchar(255) NOT NULL default '',
        `alias` varchar(255) NOT NULL default '',
        `type` tinyint(3) NOT NULL DEFAULT '0',
        `hits` int(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        KEY `newurl` (`newurl`),
        KEY `alias` (`alias`),
        KEY `type` (`type`)
        ) DEFAULT CHARSET=utf8;
        
CREATE TABLE IF NOT EXISTS `#__sh404sef_pageids` (
        `id` int(11) NOT NULL auto_increment,
        `newurl` varchar(255) NOT NULL default '',
        `pageid` varchar(255) NOT NULL default '',
        `type` tinyint(3) NOT NULL DEFAULT '0',
        `hits` int(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        KEY `newurl` (`newurl`),
        KEY `alias` (`pageid`),
        KEY `type` (`type`)
        ) DEFAULT CHARSET=utf8;
        
