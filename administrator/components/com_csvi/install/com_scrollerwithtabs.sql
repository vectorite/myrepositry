DELETE FROM `#__csvi_template_tables` WHERE `component` = 'com_scrollerwithtabs';
INSERT IGNORE INTO `#__csvi_template_tables` (`template_type_name`, `template_table`, `component`) VALUES
('contentexport', 'contentexport', 'com_scrollerwithtabs'),
('contentexport', 'scrollerwithtabs_content', 'com_scrollerwithtabs'),
('contentimport', 'contentimport', 'com_scrollerwithtabs'),
('contentimport', 'scrollerwithtabs_content', 'com_scrollerwithtabs');

DELETE FROM `#__csvi_template_types` WHERE `component` = 'com_scrollerwithtabs';
INSERT IGNORE INTO `#__csvi_template_types` (`template_type_name`, `template_type`, `component`, `url`, `options`) VALUES
('contentexport', 'export', 'com_scrollerwithtabs', 'index.php?option=com_scrollerwithtabs', 'file,fields,layout,email,limit'),
('contentimport', 'import', 'com_scrollerwithtabs', 'index.php?option=com_scrollerwithtabs', 'file,fields,limit');