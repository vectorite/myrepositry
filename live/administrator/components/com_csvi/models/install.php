<?php
/**
 * Install model
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: install.php 2062 2012-08-08 11:11:15Z RolandD $
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Install Model
 *
 * @package CSVI
 */
class CsviModelInstall extends JModel {

	private $_templates = array();
	private $_tag = '';
	private $_results = array();
	private $_tables = array();

	/**
	 * Find the version installed
	 *
	 * Version 4 is the first version
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo 		Check version from database
	 * @todo		Convert settings from INI format to JSON format
	 * @see
	 * @access 		private
	 * @param
	 * @return 		string	the version determined by the database
	 * @since 		3.0
	 */
	public function getVersion() {
		// Determine the tables in the database
		$version = $this->_getVersion();
		if (empty($version)) $version = 'current';
		return $version;
	}

	/**
	 * Start performing the upgrade
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		string	the result of the upgrade
	 * @since 		3.0
	 */
	public function getUpgrade() {
		// Get the currently installed version
		$version = $this->_translateVersion();

		// Rename the existing tables
		if ($this->_renameTables($version)) {
			// Create the new tables
			if ($this->_createTables()) {
				// Migrate the data in the tables
				if ($this->_migrateTables($version)) $this->_results['messages'][] = JText::_('COM_CSVI_UPGRADE_OK');

				// Update the version number in the database
				$this->_setVersion();

				// Load the components
				$this->_loadComponents();
			}
			else $this->_results['error'][] = '<span class="error">'.JText::_('COM_CSVI_INSTALL_NOK').'</span>';
		}
		else {
			$this->_results['error'][] = '<span class="error">'.JText::_('COM_CSVI_INSTALL_NOK').'</span>';
			$jinput->set('cancelinstall', true);
		}

		// Send the results back
		return $this->_results;
	}

	/**
	 * Rename the existing tables
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		string	$version	the currently installed version
	 * @return 		bool	true if tables are renamed | false if tables are not renamed
	 * @since 		3.0
	 */
	private function _renameTables($version) {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$this->_tag = str_ireplace('.', '_', $version);
		$ok = true;
		$removeold = $jinput->get('removeoldtables', false, 'bool');
		$random = time();

		// Load the tables to rename
		$tables = $this->_getTables($version);

		// Start renaming the tables
		foreach ($tables as $table) {
			if ($this->_tableExists($table)) {
				if ($this->_tableExists($table.'_'.$this->_tag)) {
					if ($removeold) {
						$db->setQuery("DROP TABLE ".$db->quoteName($table.'_'.$this->_tag));
						if (!$db->query()) {
							$this->_results['messages'][] = $db->getErrorMsg();
							$ok = false;
						}
					}
					else {
						$db->setQuery("ALTER TABLE ".$db->quoteName($table.'_'.$this->_tag)." RENAME TO ".$db->quoteName($table.'_'.$random));
						if (!$db->query()) {
							$this->_results['messages'][] = $db->getErrorMsg();
							$ok = false;
						}
					}
				}
				$db->setQuery("ALTER TABLE ".$db->quoteName($table)." RENAME TO ".$db->quoteName($table.'_'.$this->_tag));
				if (!$db->query()) {
					$this->_results['messages'][] = $db->getErrorMsg();
					$ok = false;
				}
			}
			$this->_results['messages'][] = $table;
		}
		return $ok;
	}

	/**
	 * Check if a table exists
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		$table	string	the name of the table to check
	 * @return 		bool	true if table exists | false if table does not exist
	 * @since 		3.0
	 */
	private function _tableExists($table) {
		$db = JFactory::getDbo();
		if (empty($this->_tables)) {
			$this->_tables = $db->getTableList();
		}
		$table = str_ireplace('#__', $db->getPrefix(), $table);
		if (in_array($table, $this->_tables)) return true;
		else return false;
	}

	/**
	 * Create the tables for this version
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo 		Remove return statement
	 * @see
	 * @access 		private
	 * @param
	 * @return 		bool	true on creating all tables | false if table is not created
	 * @since 		3.0
	 */
	private function _createTables() {
		$this->_createTemplateSettings();
		$this->_createTemplateTypes();
		$this->_createTemplateTables();
		$this->_createLogs();
		$this->_createLogDetails();
		$this->_createAvailableFields();
		$this->_createCurrency();
		$this->_createSettings();
		$this->_createIcecatIndex();
		$this->_createIcecatSuppliers();
		$this->_createRelatedProducts();
		$this->_createReplacements();
		$this->_createTemplateFields();
		$this->_createTemplateFieldsReplacement();
		$this->_createTemplateFieldsCombine();

		return true;
	}

	/**
	 * Migrate the tables
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version being migrated from
	 * @return 		bool	true if migration is OK | false if errors occured during migration
	 * @since 		3.0
	 */
	private function _migrateTables($version) {
		switch ($version) {
			case '4.0':
				$this->_convertReplacements($version);
				$this->_convertTemplateSettings($version);
				$this->_convertSettings($version);
				$this->_convertIcecatSuppliers($version);
				$this->_convertIcecatIndex($version);
				$this->_convertCurrency($version);
				$this->_convertAvailableFields($version);
				$this->_convertTemplateTables($version);
				$this->_convertLogs($version);
				$this->_convertLogDetails($version);
				$this->_convertTemplates($version);
				break;
			case '4.3':
				$this->_convertReplacements($version);
				$this->_convertTemplateSettings($version);
				$this->_convertSettings($version);
				$this->_convertIcecatSuppliers($version);
				$this->_convertIcecatIndex($version);
				$this->_convertCurrency($version);
				$this->_convertAvailableFields($version);
				$this->_convertTemplateTables($version);
				$this->_convertLogs($version);
				$this->_convertLogDetails($version);
				$this->_convertTemplates($version);
				$this->_convertTemplateFields($version);
				break;
			case 'current':
				$this->_convertReplacements($version);
				$this->_convertTemplateSettings($version);
				$this->_convertSettings($version);
				$this->_convertIcecatSuppliers($version);
				$this->_convertIcecatIndex($version);
				$this->_convertCurrency($version);
				$this->_convertAvailableFields($version);
				$this->_convertTemplateTables($version);
				$this->_convertLogs($version);
				$this->_convertLogDetails($version);
				$this->_convertTemplateFields($version);
				$this->_convertTemplateFieldsReplacement($version);
				//$this->_convertTemplateFieldsCombine($version);
			default:
				break;
		}
	}

	/**
	 * Get the tables per version
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the current installed version
	 * @return 		array	list of tables
	 * @since 		3.0
	 */
	private function _getTables($version) {
		$tables = array();
		switch ($version) {
			case '4.0':
				$tables[] = '#__csvi_available_fields';
				$tables[] = '#__csvi_currency';
				$tables[] = '#__csvi_icecat_index';
				$tables[] = '#__csvi_icecat_suppliers';
				$tables[] = '#__csvi_logs';
				$tables[] = '#__csvi_log_details';
				$tables[] = '#__csvi_replacements';
				$tables[] = '#__csvi_settings';
				$tables[] = '#__csvi_template_settings';
				$tables[] = '#__csvi_template_types';
				$tables[] = '#__csvi_template_tables';
				$tables[] = '#__csvi_related_products';
				break;
			case '4.3':
			case 'current':
				$tables[] = '#__csvi_available_fields';
				$tables[] = '#__csvi_currency';
				$tables[] = '#__csvi_icecat_index';
				$tables[] = '#__csvi_icecat_suppliers';
				$tables[] = '#__csvi_logs';
				$tables[] = '#__csvi_log_details';
				$tables[] = '#__csvi_replacements';
				$tables[] = '#__csvi_settings';
				$tables[] = '#__csvi_template_fields';
				$tables[] = '#__csvi_template_fields_replacement';
				$tables[] = '#__csvi_template_fields_combine';
				$tables[] = '#__csvi_template_settings';
				$tables[] = '#__csvi_template_types';
				$tables[] = '#__csvi_template_tables';
				$tables[] = '#__csvi_related_products';
				break;
		}
		return $tables;
	}

	/**
	 * Create the template settings table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return 		bool	true if query is succesful | false if query fails
	 * @since 		3.0
	 */
	private function _createTemplateSettings() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__csvi_template_settings` (
				`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the saved setting',
				`name` VARCHAR(255) NOT NULL COMMENT 'Name for the saved setting',
				`settings` TEXT NOT NULL COMMENT 'The actual settings',
				`process` ENUM('import','export') NOT NULL DEFAULT 'import' COMMENT 'The type of template',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM CHARSET=utf8 COMMENT='Stores the template settings for CSVI';");
		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}

	/**
	 * Create the template type table
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access		private
	 * @param
	 * @return
	 * @since		4.0
	 */
	private function _createTemplateTypes() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS  `#__csvi_template_types` (
			  		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
				  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `template_type_name` varchar(55) NOT NULL,
				  `template_type` varchar(55) NOT NULL,
				  `component` varchar(55) NOT NULL COMMENT 'Name of the component',
				  `url` varchar(100) DEFAULT NULL COMMENT 'The URL of the page the import is for',
				  `options` varchar(255) NOT NULL DEFAULT 'fields' COMMENT 'The template pages to show for the template type',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `type_name` (`template_type_name`,`template_type`,`component`)
			) ENGINE=MyISAM CHARSET=utf8 COMMENT='Template types for CSVI';");
		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}

	/**
	 * Create the template tables table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.0
	 */
	private function _createTemplateTables() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS  `#__csvi_template_tables` (
			  	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
				  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `template_type_name` varchar(55) NOT NULL,
				  `template_table` varchar(55) NOT NULL,
				  `component` varchar(55) NOT NULL,
				  `indexed` int(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `type_name` (`template_type_name`,`template_table`,`component`)
				) ENGINE=MyISAM CHARSET=utf8 COMMENT='Template tables used per template type for CSVI'");
		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}

	/**
	 * Create the log table
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access		private
	 * @param
	 * @return
	 * @since		4.0
	 */
	private function _createLogs() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__csvi_logs` (
			  `id` int(11) NOT NULL auto_increment,
			  `userid` int(11) NOT NULL,
			  `logstamp` datetime NOT NULL,
			  `action` varchar(255) NOT NULL,
			  `action_type` varchar(255) NOT NULL default '',
			  `template_name` varchar(255) NULL default NULL,
			  `records` int(11) NOT NULL,
			  `run_id` INT(11) NULL DEFAULT NULL,
			  `file_name` VARCHAR(255) NULL DEFAULT NULL,
			  `run_cancelled` TINYINT(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM CHARSET=utf8 COMMENT='Log results for CSVI';");

		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}

	/**
	 * Create the log details table
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access		private
	 * @param
	 * @return
	 * @since		4.0
	 */
	private function _createLogDetails() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__csvi_log_details` (
			  `id` int(11) NOT NULL auto_increment,
			  `log_id` int(11) NOT NULL,
			  `line` int(11) NOT NULL,
			  `description` text NOT NULL,
			  `result` varchar(45) NOT NULL,
			  `status` varchar(45) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM CHARSET=utf8 COMMENT='Log details for CSVI';");

		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}

	/**
	 * Create the available fields table
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access		private
	 * @param
	 * @return
	 * @since		4.0
	 */
	private function _createAvailableFields() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__csvi_available_fields` (
			  `id` int(11) NOT NULL auto_increment,
			  `csvi_name` varchar(255) NOT NULL,
			  `component_name` varchar(55) NOT NULL,
			  `component_table` varchar(55) NOT NULL,
			  `component` varchar(55) NOT NULL,
			  `isprimary` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `component_name_table` (`component_name`,`component_table`,`component`)
			) ENGINE=MyISAM CHARSET=utf8 COMMENT='Available fields for CSVI';");

		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}

	/**
	 * Create the currency table
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access		private
	 * @param
	 * @return
	 * @since		4.0
	 */
	private function _createCurrency() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS  `#__csvi_currency` (
			  `currency_id` tinyint(4) NOT NULL auto_increment,
			  `currency_code` varchar(3) NULL DEFAULT NULL,
			  `currency_rate` varchar(55) NULL DEFAULT NULL,
			  PRIMARY KEY  (`currency_id`),
			  UNIQUE INDEX `currency_code` (`currency_code`)
			) ENGINE=MyISAM CHARSET=utf8 COMMENT='Curriencies and exchange rates for CSVI';");

		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}

	/**
	 * Create the settings table
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access		private
	 * @param
	 * @return
	 * @since		4.0
	 */
	private function _createSettings() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__csvi_settings` (
				`id` INT(11) NOT NULL auto_increment,
				`params` TEXT NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM CHARSET=utf8 COMMENT='Configuration values for CSVI';");
		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
		else {
			$db->setQuery("INSERT IGNORE INTO `#__csvi_settings` (`id`, `params`) VALUES (1, '');");
			if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
		}

	}

	/**
	 * create ICEcat index table
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access		private
	 * @param
	 * @return
	 * @since		4.0
	 */
	private function _createIcecatIndex() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__csvi_icecat_index` (
			  `path` varchar(100) DEFAULT NULL,
			  `product_id` int(2) DEFAULT NULL,
			  `updated` int(14) DEFAULT NULL,
			  `quality` varchar(6) DEFAULT NULL,
			  `supplier_id` int(1) DEFAULT NULL,
			  `prod_id` varchar(16) DEFAULT NULL,
			  `catid` int(3) DEFAULT NULL,
			  `m_prod_id` varchar(10) DEFAULT NULL,
			  `ean_upc` varchar(10) DEFAULT NULL,
			  `on_market` int(1) DEFAULT NULL,
			  `country_market` varchar(10) DEFAULT NULL,
			  `model_name` varchar(26) DEFAULT NULL,
			  `product_view` int(5) DEFAULT NULL,
			  `high_pic` varchar(51) DEFAULT NULL,
			  `high_pic_size` int(5) DEFAULT NULL,
			  `high_pic_width` int(3) DEFAULT NULL,
			  `high_pic_height` int(3) DEFAULT NULL,
			  `m_supplier_id` int(3) DEFAULT NULL,
			  `m_supplier_name` varchar(51) DEFAULT NULL,
			  INDEX `product_mpn` (`prod_id`),
			  INDEX `manufacturer_name` (`supplier_id`)
			) ENGINE=MyISAM CHARSET=utf8 COMMENT='ICEcat index data for CSVI';");

		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}

	/**
	 * Create the ICEcat suppliers table
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access		private
	 * @param
	 * @return
	 * @since		4.0
	 */
	private function _createIcecatSuppliers() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__csvi_icecat_suppliers` (
				`supplier_id` INT(11) UNSIGNED NOT NULL,
				`supplier_name` VARCHAR(255) NOT NULL,
				UNIQUE INDEX `Unique supplier` (`supplier_id`, `supplier_name`),
				INDEX `Supplier name` (`supplier_name`)
			) ENGINE=MyISAM CHARSET=utf8 COMMENT='ICEcat supplier data for CSVI';");

		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}

	/**
	 * Create the related products table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.3.1
	 */
	private function _createRelatedProducts() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__csvi_related_products` (
				`product_sku` VARCHAR(64) NOT NULL,
				`related_sku` TEXT NOT NULL
			) ENGINE=MyISAM CHARSET=utf8 COMMENT='Related products import for CSVI';");

		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}

	/**
	 * Create the replacements table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.0
	 */
	private function _createReplacements() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__csvi_replacements` (
			`id` INT(10) NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(100) NOT NULL,
			`findtext` TEXT NOT NULL,
			`replacetext` TEXT NOT NULL,
			`multivalue` ENUM('0','1') NOT NULL,
			`method` ENUM('text','regex') NOT NULL DEFAULT 'text',
			`checked_out` INT(11) UNSIGNED NULL DEFAULT '0',
			`checked_out_time` DATETIME NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM CHARSET=utf8 COMMENT='Replacement rules for CSVI';");
		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}
	
	/**
	 * Create the template fields table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.0
	 */
	private function _createTemplateFields() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__csvi_template_fields` (
					`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the template field',
					`template_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The template ID',
					`ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The order of the field',
					`field_name` VARCHAR(255) NOT NULL COMMENT 'Name for the field',
					`file_field_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name for the field from the file',
					`template_field_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name for the field from the template',
					`column_header` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Header for the column',
					`default_value` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Default value for the field',
					`process` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT 'Process the field',
					`combine_char` VARCHAR(5) NOT NULL DEFAULT '' COMMENT 'The character(s) to combine the fields',
					`sort` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT 'Sort the field',
					`cdata` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT 'Use the CDATA tag',
					PRIMARY KEY (`id`)
				)
				COMMENT='Holds the fields for a CSVI template';");
		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}
	
	/**
	 * Create the template fields <--> replacement rule cross reference table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.0
	 */
	private function _createTemplateFieldsReplacement() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__csvi_template_fields_replacement` (
				`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the cross reference',
				`field_id` VARCHAR(255) NOT NULL COMMENT 'ID of the field',
				`replace_id` VARCHAR(255) NOT NULL COMMENT 'ID of the replacement rule',
				PRIMARY KEY (`id`)
			) COMMENT='Holds the replacement cross reference for a CSVI template field';");
		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}
	
	/**
	 * Create the template fields <--> combine rule cross reference table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		5.0
	 */
	private function _createTemplateFieldsCombine() {
		$db = JFactory::getDbo();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__csvi_template_fields_combine` (
				`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the cross reference',
				`field_id` VARCHAR(255) NOT NULL COMMENT 'ID of the field',
				`combine_id` VARCHAR(255) NOT NULL COMMENT 'ID of the combine rule',
				PRIMARY KEY (`id`)
			) COMMENT='Holds the combine cross reference for a CSVI template field';");
		if (!$db->query()) $this->_results['messages'][] = $db->getErrorMsg();
	}
	
	/**
	 * Convert the replacements table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertReplacements($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.0':
			case '4.3':
				$db->setQuery('REPLACE INTO #__csvi_replacements (SELECT `id`,`name`,`findtext`, `replacetext`, 0, `method`, `checked_out`, `checked_out_time` FROM #__csvi_replacements'.'_'.$this->_tag.')');
				if ($db->query()) {
					$this->_results['messages'][] = JText::_('COM_CSVI_REPLACEMENTS_CONVERTED');
					return true;
				}
				else $this->_results['messages'][] = $db->getErrorMsg();
				break;
		}
	}

	/**
	 * Convert the template settings table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertTemplateSettings($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.0':
				$db->setQuery('REPLACE INTO #__csvi_template_settings ('.$db->nameQuote('id').', '.$db->nameQuote('name').', '.$db->nameQuote('settings').') (SELECT '.$db->nameQuote('id').', '.$db->nameQuote('name').', '.$db->nameQuote('settings').' FROM #__csvi_template_settings'.'_'.$this->_tag.')');
				if ($db->query()) {
					$this->_results['messages'][] = JText::_('COM_CSVI_TEMPLATE_SETTINGS_CONVERTED');
					return true;
				}
				else $this->_results['messages'][] = $db->getErrorMsg();
				break;
			case '4.3':
				// See if there is the free or pro version
				$fields = $db->getTableColumns('#__csvi_template_settings'.'_'.$this->_tag);
				if (array_key_exists('process', $fields)) {
					// Pro version
					$db->setQuery('REPLACE INTO #__csvi_template_settings (SELECT * FROM #__csvi_template_settings'.'_'.$this->_tag.')');
					if ($db->query()) {
						$this->_results['messages'][] = JText::_('COM_CSVI_TEMPLATE_SETTINGS_CONVERTED');
						return true;
					}
					else $this->_results['messages'][] = $db->getErrorMsg();
				}
				else {
					// Free version
					$db->setQuery('SELECT * FROM #__csvi_template_settings'.'_'.$this->_tag);
					$templates = $db->loadObjectList();
					foreach ($templates as $template) {
						$settings = json_decode($template->settings, true);
						$process = $settings['options']['action'];
						$db->setQuery('INSERT IGNORE INTO #__csvi_template_settings VALUES ('.$db->quote($template->id).', '.$db->quote($template->name).', '.$db->quote($template->settings).', '.$db->quote($process).')');
						if ($db->query()) {
							$this->_results['messages'][] = JText::_('COM_CSVI_TEMPLATE_SETTINGS_CONVERTED');
							$this->_results['messages'][] = $template->name;
						}
						else {
							$this->_results['messages'][] = $db->getErrorMsg();
						}

					}
				}
				break;
		}
	}

	/**
	 * Convert the template settings table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertSettings($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.0':
			case '4.3':
				$db->setQuery('REPLACE INTO #__csvi_settings (SELECT * FROM #__csvi_settings'.'_'.$this->_tag.')');
				if ($db->query()) {
					$this->_results['messages'][] = JText::_('COM_CSVI_SETTINGS_CONVERTED');
					return true;
				}
				else $this->_results['messages'][] = $db->getErrorMsg();
				break;
		}
	}

	/**
	 * Convert the ICEcat suppliers table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertIcecatSuppliers($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.0':
			case '4.3':
				$db->setQuery('REPLACE INTO #__csvi_icecat_suppliers (SELECT * FROM #__csvi_icecat_suppliers'.'_'.$this->_tag.')');
				if ($db->query()) {
					$this->_results['messages'][] = JText::_('COM_CSVI_ICECAT_SUPPLIERS_CONVERTED');
					return true;
				}
				else $this->_results['messages'][] = $db->getErrorMsg();
				break;
		}
	}

	/**
	 * Convert the ICEcat index table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertIcecatIndex($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.0':
			case '4.3':
				$db->setQuery('REPLACE INTO #__csvi_icecat_index (SELECT * FROM #__csvi_icecat_index'.'_'.$this->_tag.')');
				if ($db->query()) {
					$this->_results['messages'][] = JText::_('COM_CSVI_ICECAT_INDEX_CONVERTED');
					return true;
				}
				else $this->_results['messages'][] = $db->getErrorMsg();
				break;
		}
	}

	/**
	 * Convert the ICEcat index table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertCurrency($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.0':
			case '4.3':
				$db->setQuery('REPLACE INTO #__csvi_currency (SELECT * FROM #__csvi_currency'.'_'.$this->_tag.')');
				if ($db->query()) {
					$this->_results['messages'][] = JText::_('COM_CSVI_CURRENCY_CONVERTED');
					return true;
				}
				else $this->_results['messages'][] = $db->getErrorMsg();
				break;
		}
	}

	/**
	 * Convert the ICEcat index table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertAvailableFields($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.0':
			case '4.3':
				$db->setQuery('REPLACE INTO #__csvi_available_fields (SELECT * FROM #__csvi_available_fields'.'_'.$this->_tag.')');
				if ($db->query()) {
					$this->_results['messages'][] = JText::_('COM_CSVI_AVAILABLE_FIELDS_CONVERTED');
					return true;
				}
				else $this->_results['messages'][] = $db->getErrorMsg();
				break;
		}
	}

	/**
	 * Convert the template tables table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertTemplateTables($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.0':
			case '4.3':
				$db->setQuery('REPLACE INTO #__csvi_template_tables (SELECT * FROM #__csvi_template_tables'.'_'.$this->_tag.')');
				if ($db->query()) {
					$this->_results['messages'][] = JText::_('COM_CSVI_TEMPLATE_TABLES_CONVERTED');
					return true;
				}
				else $this->_results['messages'][] = $db->getErrorMsg();
				break;
		}
	}
	
	/**
	 * Convert the template fields tables table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertTemplateFields($version) {
		$db = JFactory::getDbo();
	
		switch ($version) {
			case '4.0':
				$db->setQuery('REPLACE INTO #__csvi_template_fields (SELECT * FROM #__csvi_template_fields'.'_'.$this->_tag.')');
				if ($db->query()) {
					$this->_results['messages'][] = JText::_('COM_CSVI_TEMPLATE_FIELDS_CONVERTED');
					return true;
				}
				else $this->_results['messages'][] = $db->getErrorMsg();
				break;
			case '4.3':
				// See if there is the free or pro version
				$fields = $db->getTableColumns('#__csvi_template_settings'.'_'.$this->_tag);
				if (array_key_exists('process', $fields)) {
					// Pro version
					$db->setQuery('REPLACE INTO #__csvi_template_fields ('.$db->quoteName('id').','.$db->quoteName('template_id').','.$db->quoteName('ordering').','.$db->quoteName('field_name').','.$db->quoteName('column_header').','.$db->quoteName('default_value').','.$db->quoteName('process').') 
					(SELECT '.$db->quoteName('id').','.$db->quoteName('template_id').','.$db->quoteName('ordering').','.$db->quoteName('field_name').','.$db->quoteName('column_header').','.$db->quoteName('default_value').','.$db->quoteName('process').' FROM #__csvi_template_fields'.'_'.$this->_tag.')');
					if ($db->query()) {
						$this->_results['messages'][] = JText::_('COM_CSVI_TEMPLATE_FIELDS_CONVERTED');
						return true;
					}
					else $this->_results['messages'][] = $db->getErrorMsg();
				}
				else {
					// Free version
					$db->setQuery('SELECT * FROM #__csvi_template_settings'.'_'.$this->_tag);
					$templates = $db->loadObjectList();
					foreach ($templates as $template) {
						$settings = json_decode($template->settings, true);
						$process = $settings['options']['action'];
						$fields = $settings[$process.'_fields'];
						$previous_id = 0;
						foreach ($fields['_selected_name'] as $key => $field) {
							$column_header = (isset($fields['_column_header'])) ? $fields['_column_header'][$key] : null;
							$sort = (isset($fields['_sort_field'])) ? $fields['_sort_field'][$key] : null;
							$db->setQuery('INSERT IGNORE INTO #__csvi_template_fields (template_id, ordering, field_name, column_header, default_value, process, sort) VALUES ('.$db->quote($template->id).', '.($key+1).', '.$db->quote($field).', '.$db->quote($column_header).', '.$db->quote($fields['_default_value'][$key]).', '.$db->quote($fields['_process_field'][$key]).', '.$db->quote($sort).')');
							if ($db->query()) {
								$id = $db->insertid();
								if ($previous_id) {
									$this->_results['messages'][] = $template->name;
									// Add the combine if needed
									if ($fields['_combine_field'][$key] > 0) {
										$db->setQuery('INSERT IGNORE INTO #__csvi_template_fields_combine (field_id, combine_id) VALUES ('.$previous_id.', '.$id.')');
										$db->query();
									}
									// Add the replacement if needed
									if ($fields['_replace_field'][$key] > 0) {
										$db->setQuery('INSERT IGNORE INTO #__csvi_template_fields_replacement (field_id, replace_id) VALUES ('.$previous_id.', '.$id.')');
										$db->query();
									}
								}
							}
							else {
								$this->_results['messages'][] = $db->getErrorMsg();
							}
							$previous_id = $id;
						}
					}
				}
				break;
		}
	}
	
	/**
	 * Convert the template tables table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertTemplateFieldsReplacement($version) {
		$db = JFactory::getDbo();
	
		switch ($version) {
			case '4.3':
				$db->setQuery('REPLACE INTO #__csvi_template_fields_replacement (SELECT * FROM #__csvi_template_fields_replacement'.'_'.$this->_tag.')');
				if ($db->query()) {
					$this->_results['messages'][] = JText::_('COM_CSVI_TEMPLATE_FIELDS_REPLACEMENT_CONVERTED');
					return true;
				}
				else $this->_results['messages'][] = $db->getErrorMsg();
				break;
		}
	}
	
	/**
	 * Convert the templates
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertTemplates($version) {
		$db = JFactory::getDbo();
		
		switch ($version) {
			case '4.0':
				// Read the old templates
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__csvi_template_settings');
				$db->setQuery($query);
				$templates = $db->loadObjectList();
				foreach ($templates as $template) {
					$settings = json_decode($template->settings);
					if (is_object($settings)) {
						// Update the process field
						$query = $db->getQuery(true);
						$query->update('#__csvi_template_settings');
						$query->set($db->nameQuote('process').' = '.$db->quote($settings->options->action));
						$query->where($db->nameQuote('id').' = '.$template->id);
						$db->setQuery($query);
						$db->query();
						
						// Check if there is a fields section
						if (isset($settings->import_fields->_selected_name) || isset($settings->export_fields->_selected_name)) {
							// Delete any existing fields for this template ID
							$query = $db->getQuery(true);
							$query->delete('#__csvi_template_fields');
							$query->where($db->nameQuote('template_id').' = '.$template->id);
							$db->setQuery($query);
							$db->query();
							
							// Get the fields in a single object
							if (isset($settings->import_fields->_selected_name)) $fields = $settings->import_fields;
							else if (isset($settings->export_fields->_selected_name)) $fields = $settings->export_fields;
							
							// Process all the fields
							foreach ($fields->_selected_name as $key => $fieldname) {
								$table = $this->getTable('templatefield');
								$data['template_id'] = $template->id;
								$data['field_name'] = $fieldname;
								if ($settings->options->action == 'import') $data['column_header'] = '';
								else if ($settings->options->action == 'export') $data['column_header'] = $fields->_column_header[$key];
								$data['default_value'] = $fields->_default_value[$key];
								$data['process'] = $fields->_process_field[$key];
								$data['combine'] = $fields->_combine_field[$key];
								if ($settings->options->action == 'import') $data['sort'] = '0';
								else if ($settings->options->action == 'export') $data['sort'] = $data['sort'] = $fields->_sort_field[$key];
								$table->bind($data);
								if (!$table->store()) {
									return $table->getErrorMsg();
								}
								else {
									$fieldid = $table->id;
									$db = JFactory::getDbo();
									// Store the replacement rules
									if (isset($fields->_replace_field)) {
										if (is_array($fields->_replace_field)) $rules = $fields->_replace_field[$key];
										else if (is_object($fields->_replace_field)) $rules = $fields->_replace_field->$key;
										if (!empty($rules)) {
											foreach ($rules as $rule) {
												if (!empty($rule)) {
													$query = $db->getQuery(true);
													$query->insert('#__csvi_template_fields_replacement');
													$query->values(array('null, '.$fieldid.', '.$rule));
													$db->setQuery($query);
													$db->query();
												}
											}
										}
									}
								}
							}
						}
						
						// Delete any old replacement references
						$query = $db->getQuery(true);
						$query->select('r.id');
						$query->from('#__csvi_template_fields_replacement r');
						$query->innerJoin('#__csvi_template_fields f ON r.field_id = f.id');
						$db->setQuery($query);
						$rids = $db->loadResultArray();
						
						if (!empty($rids)) {
							$query = $db->getQuery(true);
							$query->delete('#__csvi_template_fields_replacement');
							$query->where('id NOT IN ('.implode(',', $rids).')');
							$db->setQuery($query);
							$db->query();
						}
					}
				}
				break;
		}
		return true;
	}

	/**
	 * Convert the logs table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertLogs($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.0':
			case '4.3':
				$db->setQuery('REPLACE INTO #__csvi_logs (SELECT * FROM #__csvi_logs'.'_'.$this->_tag.')');
				if ($db->query()) {
					$this->_results['messages'][] = JText::_('COM_CSVI_LOGS_CONVERTED');
					return true;
				}
				else $this->_results['messages'][] = $db->getErrorMsg();
				break;
		}
	}

	/**
	 * Convert the log details table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertLogDetails($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.0':
			case '4.3':
				$db->setQuery('REPLACE INTO #__csvi_log_details (SELECT * FROM #__csvi_log_details'.'_'.$this->_tag.')');
				if ($db->query()) {
					$this->_results['messages'][] = JText::_('COM_CSVI_LOG_DETAILS_CONVERTED');
					return true;
				}
				else $this->_results['messages'][] = $db->getErrorMsg();
				break;
		}
	}

	/**
	 * Proxy function for calling the update the available fields
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function getAvailableFields() {
		// Get the logger class
		$jinput = JFactory::getApplication()->input;
		$csvilog = new CsviLog();
		$jinput->set('csvilog', $csvilog);
		$model = $this->getModel('Availablefields');
		// Prepare to load the available fields
		$model->prepareAvailableFields();

		// Update the available fields
		$model->getFillAvailableFields();
	}

	/**
	 * Proxy function for installing sample templates
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function getSampleTemplates() {
		$db = JFactory::getDbo();
		// Read the example template file
		$fp = fopen(JPATH_COMPONENT_ADMINISTRATOR.'/install/example_templates.csv', "r");
		if ($fp) {
			while (($data = fgetcsv($fp, 0, ",")) !== FALSE) {
				$db->setQuery("INSERT IGNORE INTO #__csvi_template_settings (".$db->quoteName('name').", ".$db->quoteName('settings').", ".$db->quoteName('process').")
						VALUES (".$db->Quote($data[0]).", ".$db->Quote($data[1]).", ".$db->Quote($data[2]).")");
				if ($db->query()) {
					$this->_results['messages'][] = JText::sprintf('COM_CSVI_RESTORE_TEMPLATE', $data[0]);
					$template_id = $db->insertid();
						
					// Template is stored, add the fields
					$fields = json_decode($data[3]);
					$replacements = json_decode($data[4]);
					$combines = json_decode($data[5]);
					foreach ($fields as $field) {
						$db->setQuery("INSERT IGNORE INTO #__csvi_template_fields (".$db->quoteName('template_id').", ".$db->quoteName('ordering').", ".$db->quoteName('field_name').", ".$db->quoteName('column_header').", ".$db->quoteName('default_value').", ".$db->quoteName('process').", ".$db->quoteName('combine_char').", ".$db->quoteName('sort').", ".$db->quoteName('cdata').")
								VALUES (".$db->Quote($template_id).", ".$db->Quote($field->ordering).", ".$db->Quote($field->field_name).", ".$db->Quote($field->column_header).", ".$db->Quote($field->default_value).", ".$db->Quote($field->process).", ".$db->Quote($field->combine_char).", ".$db->Quote($field->sort).", ".$db->Quote($field->cdata).")");
						if ($db->query()) {
							$field_id = $db->insertid();
							// Field is stored add the replacement link
							foreach ($replacements as $replacement) {
								if ($replacement->field_id == $field->id) {
									$db->setQuery("INSERT IGNORE INTO #__csvi_template_fields_replacement (".$db->quoteName('field_id').", ".$db->quoteName('replace_id').")
											VALUES (".$db->Quote($field_id).", ".$db->Quote($replacement->replace_id).")");
									$db->query();
								}
							}
							// Field is stored add the combine link
							foreach ($combines as $combine) {
								if ($combine->field_id == $field->id) {
									$db->setQuery("INSERT IGNORE INTO #__csvi_template_fields_combine (".$db->quoteName('field_id').", ".$db->quoteName('combine_id').")
											VALUES (".$db->Quote($field_id).", ".$db->Quote($combine->combine_id).")");
									$db->query();
								}
							}
						}
					}
				}
				else {
					$this->_results['messages'][] = $db->getErrorMsg();
					$this->_results['messages'][] = JText::sprintf('COM_CSVI_COMPONENT_HAS_NOT_BEEN_ADDED', $file);
				}
			}
			fclose($fp);
		}
	}

	/**
	 * Create a proxy for including other models
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return
	 * @since 		3.0
	 */
	protected function getModel($model) {
		return $this->getInstance($model, 'CsviModel');
	}

	/**
	 * Set the current version in the database
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.1
	 */
	private function _setVersion() {
		$db = JFactory::getDbo();
		$q = "INSERT IGNORE INTO #__csvi_settings (id, params) VALUES (2, '".JText::_('COM_CSVI_CSVI_VERSION')."')
			ON DUPLICATE KEY UPDATE params = '".JText::_('COM_CSVI_CSVI_VERSION')."'";
		$db->setQuery($q);
		$db->query();
	}

	/**
	 * Get the current version in the database
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.2
	 */
	private function _getVersion() {
		$db = JFactory::getDbo();
		
		// Check if the table exists
		$tables = $db->getTableList();
		
		// Load the settings
		if (in_array($db->getPrefix().'csvi_settings', $tables)) {
			$q = "SELECT params
				FROM #__csvi_settings
				WHERE id = 2";
			$db->setQuery($q);
			return $db->loadResult();
		}
		else return '';
	}

	/**
	 * Translate version
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return 		string with the working version
	 * @since 		3.5
	 */
	private function _translateVersion() {
		$jinput = JFactory::getApplication()->input;
		$version = $jinput->get('version', 'current', 'string');
		switch ($version) {
			case '4.0.1':
			case '4.1':
			case '4.2':
			case '4.2.1':
				return '4.0';
				break;
			case '4.3':
			case '4.3.1':
			case '4.3.2':
			case '4.3.3':
			case '4.4.':
			case '4.5':
			case '4.5.1':
			case '4.5.2':
				return '4.3';
				break;
			default:
				return $version;
				break;
		}
	}

	/**
	 * Load supported components
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.0
	 */
	private function _loadComponents() {
		$db = JFactory::getDbo();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$files = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.'/install', '.sql', false, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'availablefields_extra.sql'));
		if (!empty($files)) {
			foreach ($files as $file) {
				$error = false;
				if (JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.'/install/'.$file)) {
					$q = JFile::read(JPATH_COMPONENT_ADMINISTRATOR.'/install/'.$file);
					$queries = $db->splitSql(JFile::read(JPATH_COMPONENT_ADMINISTRATOR.'/install/'.$file));
					foreach ($queries as $query) {
						$query = trim($query);
						if (!empty($query)) {
							$db->setQuery($query);
							if (!$db->query()) {
								$this->_results['messages'][] = $db->getErrorMsg();
								$error = true;
							}
						}
					}
					if ($error) $this->_results['messages'][] = JText::sprintf('COM_CSVI_COMPONENT_HAS_NOT_BEEN_ADDED', $file);
					else $this->_results['messages'][] = JText::sprintf('COM_CSVI_COMPONENT_HAS_BEEN_ADDED', $file);
				}
				else $this->_results['messages'][] = JText::sprintf('COM_CSVI_COMPONENT_NOT_FOUND', $file);
			}
		}
	}
}
?>