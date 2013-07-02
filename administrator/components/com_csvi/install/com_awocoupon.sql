DELETE FROM `#__csvi_template_tables` WHERE `component` = 'com_awocoupon';
INSERT IGNORE INTO `#__csvi_template_tables` (`template_type_name`, `template_table`, `component`) VALUES
('couponexport', 'couponexport', 'com_awocoupon'),
('couponexport', 'awocoupon_vm', 'com_awocoupon'),
('couponimport', 'couponimport', 'com_awocoupon'),
('couponimport', 'awocoupon_vm', 'com_awocoupon'),
('giftcertificateexport', 'giftcertificateexport', 'com_awocoupon'),
('giftcertificateexport', 'awocoupon_vm_giftcert_product', 'com_awocoupon'),
('giftcertificateimport', 'giftcertificateimport', 'com_awocoupon'),
('giftcertificateimport', 'awocoupon_vm_giftcert_product', 'com_awocoupon'),
('giftcertificatecodeimport', 'giftcertificatecodeimport', 'com_awocoupon'),
('giftcertificatecodeimport', 'awocoupon_vm_giftcert_code', 'com_awocoupon');

DELETE FROM `#__csvi_template_types` WHERE `component` = 'com_awocoupon';
INSERT IGNORE INTO `#__csvi_template_types` (`template_type_name`, `template_type`, `component`, `url`, `options`) VALUES
('couponexport', 'export', 'com_awocoupon', 'index.php?option=com_awocoupon&view=coupons', 'file,fields,coupon,layout,email,limit'),
('couponimport', 'import', 'com_awocoupon', 'index.php?option=com_awocoupon&view=coupons', 'file,fields,limit'),
('giftcertificateexport', 'export', 'com_awocoupon', 'index.php?option=com_awocoupon&view=giftcertproducts', 'file,fields,giftcertificate,layout,email,limit'),
('giftcertificateimport', 'import', 'com_awocoupon', 'index.php?option=com_awocoupon&view=giftcertproducts', 'file,fields,limit'),
('giftcertificatecodeimport', 'import', 'com_awocoupon', 'index.php?option=com_awocoupon&view=giftcertcodes', 'file,fields,limit');