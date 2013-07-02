<?php
/*
 * ARI Extensions Joomla! plugin
 *
 * @package		ARI Extensions Joomla! plugin
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2010 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die('Restricted access');

class plgSystemariextensionsInstallerScript
{
	private $db;

	function __construct()
	{
		$this->db =& JFactory::getDBO();
	}

	function install($parent) 
	{
		$this->extendModulesTable();
		$this->extendExtensionsTable();
	}

	function uninstall($parent) 
	{
		$this->restoreModulesTable();
		$this->restoreExtensionsTable();
	}
	
	function extendModulesTable()
	{
		if ($this->isDbFieldExists('#__modules', 'extra_params'))
			return ;
			
		$this->db->setQuery('ALTER TABLE #__modules ADD COLUMN `extra_params` varchar(5120) NOT NULL DEFAULT ""');
		$this->db->query();
	}

	function extendExtensionsTable()
	{
		if ($this->isDbFieldExists('#__extensions', 'extra_params'))
			return ;
			
		$this->db->setQuery('ALTER TABLE #__extensions ADD COLUMN `extra_params` varchar(5120) NOT NULL DEFAULT ""');
		$this->db->query();
	}
	
	function restoreModulesTable()
	{
		if (!$this->isDbFieldExists('#__modules', 'extra_params'))
			return ;
			
		$this->db->setQuery('ALTER TABLE #__modules DROP COLUMN `extra_params`');
		$this->db->query();
	}
	
	function restoreExtensionsTable()
	{
		if (!$this->isDbFieldExists('#__extensions', 'extra_params'))
			return ;
			
		$this->db->setQuery('ALTER TABLE #__extensions DROP COLUMN `extra_params`');
		$this->db->query();
	}
	
	function isDbFieldExists($table, $field)
	{
		$this->db->setQuery(
			sprintf('SHOW COLUMNS FROM %s LIKE "%s"',
				$table,
				$field
			)
		);
		$fields = $this->db->loadObjectList();
		
		return (is_array($fields) && count($fields) > 0);
	}
}
?>