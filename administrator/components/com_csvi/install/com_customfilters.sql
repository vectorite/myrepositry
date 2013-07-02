DELETE FROM `#__csvi_template_tables` WHERE `component` = 'com_customfilters';
INSERT IGNORE INTO `#__csvi_template_tables` (`template_type_name`, `template_table`, `component`) VALUES
('customfieldsexport', 'customfieldsexport', 'com_customfilters'),
('customfieldsexport', 'cf_customfields', 'com_customfilters'),
('customfieldsimport', 'customfieldsimport', 'com_customfilters'),
('customfieldsimport', 'cf_customfields', 'com_customfilters');

DELETE FROM `#__csvi_template_types` WHERE `component` = 'com_customfilters';
INSERT IGNORE INTO `#__csvi_template_types` (`template_type_name`, `template_type`, `component`, `url`, `options`) VALUES
('customfieldsexport', 'export', 'com_customfilters', 'index.php?option=com_customfilters&view=customfilters', 'file,fields,layout,email,limit'),
('customfieldsimport', 'import', 'com_customfilters', 'index.php?option=com_customfilters&view=customfilters', 'file,fields,limit');