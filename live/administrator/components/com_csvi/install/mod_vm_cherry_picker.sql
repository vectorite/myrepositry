DELETE FROM `#__csvi_template_tables` WHERE `component` = 'mod_vm_cherry_picker';
INSERT IGNORE INTO `#__csvi_template_tables` (`template_type_name`, `template_table`, `component`) VALUES
('producttypeimport', 'producttypeimport', 'mod_vm_cherry_picker'),
('producttypeimport', 'vm_product_type', 'mod_vm_cherry_picker'),
('producttypeparametersimport', 'producttypeparametersimport', 'mod_vm_cherry_picker'),
('producttypeparametersimport', 'vm_product_type_parameter', 'mod_vm_cherry_picker'),
('producttypenamesimport', 'producttypenamesimport', 'mod_vm_cherry_picker'),
('producttypenamesimport', 'vm_product_type_x', 'mod_vm_cherry_picker'),
('producttypeexport', 'producttypeexport', 'mod_vm_cherry_picker'),
('producttypeexport', 'vm_product_type', 'mod_vm_cherry_picker'),
('producttypeparametersexport', 'producttypeparametersexport', 'mod_vm_cherry_picker'),
('producttypeparametersexport', 'vm_product_type_parameter', 'mod_vm_cherry_picker'),
('producttypenamesexport', 'producttypenamesexport', 'mod_vm_cherry_picker'),
('producttypenamesexport', 'vm_product_type_x', 'mod_vm_cherry_picker');

DELETE FROM `#__csvi_template_types` WHERE `component` = 'mod_vm_cherry_picker';
INSERT IGNORE INTO `#__csvi_template_types` (`template_type_name`, `template_type`, `component`, `url`, `options`) VALUES
('producttypeexport', 'export', 'mod_vm_cherry_picker', '', 'file,fields,layout,email,limit'),
('producttypeimport', 'import', 'mod_vm_cherry_picker', '', 'file,fields,limit'),
('producttypenamesexport', 'export', 'mod_vm_cherry_picker', '', 'file,fields,layout,email,limit'),
('producttypenamesimport', 'import', 'mod_vm_cherry_picker', '', 'file,fields,limit'),
('producttypeparametersexport', 'export', 'mod_vm_cherry_picker', '', 'file,fields,layout,email,limit'),
('producttypeparametersimport', 'import', 'mod_vm_cherry_picker', '', 'file,fields,limit');