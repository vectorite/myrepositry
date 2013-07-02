DELETE FROM `#__csvi_template_tables` WHERE `component` = 'com_ezrealty';
INSERT IGNORE INTO `#__csvi_template_tables` (`template_type_name`, `template_table`, `component`) VALUES
('propertyexport', 'propertyexport', 'com_ezrealty'),
('propertyexport', 'ezrealty', 'com_ezrealty'),
('propertyimport', 'propertyimport', 'com_ezrealty'),
('propertyimport', 'ezrealty', 'com_ezrealty'),
('categoryexport', 'categoryexport', 'com_ezrealty'),
('categoryexport', 'ezrealty_catg', 'com_ezrealty'),
('categoryimport', 'categoryimport', 'com_ezrealty'),
('categoryimport', 'ezrealty_catg', 'com_ezrealty');

DELETE FROM `#__csvi_template_types` WHERE `component` = 'com_ezrealty';
INSERT IGNORE INTO `#__csvi_template_types` (`template_type_name`, `template_type`, `component`, `url`, `options`) VALUES
('propertyexport', 'export', 'com_ezrealty', 'index.php?option=com_ezrealty&controller=properties', 'file,fields,property,layout,email,limit'),
('propertyimport', 'import', 'com_ezrealty', 'index.php?option=com_ezrealty&controller=properties', 'file,fields,property_image,property_path,limit'),
('categoryexport', 'export', 'com_ezrealty', 'index.php?option=com_ezrealty&controller=categories', 'file,fields,property,layout,email,limit'),
('categoryimport', 'import', 'com_ezrealty', 'index.php?option=com_ezrealty&controller=categories', 'file,fields,limit');