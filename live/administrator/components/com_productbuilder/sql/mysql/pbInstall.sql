CREATE TABLE IF NOT EXISTS `#__pb_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `sku` varchar(64) NOT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '1',
   `image_path` varchar(255) NOT NULL,
  `alias` varchar(64) NOT NULL,
  `compatibility` smallint(6) NOT NULL,
  `ordering` int(11) NOT NULL,
  `description` text,
  `metaKeywords` text,
  `metaDecr` text,
  `language` char(7) NOT NULL COMMENT 'The language code for the pbproduct',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__pb_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `product_id` int(11) NOT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL,
  `connectWith` int(11) NOT NULL DEFAULT '0',
  `type` int(11) DEFAULT NULL,
  `editable` int(11) NOT NULL DEFAULT '1',
  `defOption` int(11) NOT NULL DEFAULT '0',
  `defaultProd` int(11) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL COMMENT 'The language code for the pbproduct',
  `note` text,
  `products_ordering` varchar(64) NOT NULL,
  `products_ordering_dir` varchar(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__pb_group_vm_cat_xref` (
   `group_id` int(11) NOT NULL,
  `vm_cat_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`vm_cat_id`)	
)DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__pb_group_vm_prod_xref` (
  `group_id` int(11) NOT NULL,
  `vm_product_id` int(11) NOT NULL,
PRIMARY KEY (`group_id`,`vm_product_id`)
)DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__pb_quant_group` (
  `group_id` int(11) NOT NULL,
  `displ_qbox` int(11) NOT NULL,
  `q_box_type` int(11) NOT NULL DEFAULT '0',
  `def_quantity` int(11) NOT NULL DEFAULT '1',
  `start` int(11) NOT NULL DEFAULT '1',
  `end` int(11) NOT NULL DEFAULT '1',
  `pace` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`group_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__pb_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `published` smallint(6) NOT NULL,
  `color` varchar(6) NOT NULL,
  `note` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__pb_tag_xref_vmprod` (
  `tag_id` int(11) NOT NULL,
  `vm_prod_id` int(11) NOT NULL,
  KEY `tag_id` (`tag_id`),
  KEY `vm_prod_id` (`vm_prod_id`)
) DEFAULT CHARSET=utf8;

ALTER TABLE `#__virtuemart_order_items` ADD `productbuilder_product_id` VARCHAR( 36 ) NOT NULL COMMENT 'the id of the productbuilder product'