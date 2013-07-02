<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 *
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Joomla! Extensions exclusion filter (directories part)
 */
class AEFilterExtensiondirs extends AEAbstractFilter
{
	public function __construct()
	{
		$this->object	= 'dir';
		$this->subtype	= 'all';
		$this->method	= 'direct';
		
		if(AEFactory::getKettenrad()->getTag() == 'restorepoint') $this->enabled = false;

		if(empty($this->filter_name)) $this->filter_name = strtolower(basename(__FILE__,'.php'));
		parent::__construct();
	}

	public function hasFilters()
	{
		// Reset the filters
		$this->filter_data = array();

		// Add filters for components, languages, modules and templates
		$admin = substr(JPATH_ADMINISTRATOR, strlen(JPATH_SITE)+1 );
		$filterObjects = array(
			array('components', 'default', 'components'),
			array('components', 'default', $admin.'/components'),
			array('languages', 'frontend', 'language'),
			array('languages', 'backend', $admin.'/language'),
			array('modules', 'frontend', 'modules'),
			array('modules', 'backend', $admin.'/modules'),
			array('templates', 'frontend', 'templates'),
			array('templates', 'backend', $admin.'/templates')
		);
		foreach($filterObjects as $filterObject)
		{
			$this->createFilterEntry($filterObject[0], $filterObject[1], $filterObject[2]);
		}

		// Plugins are a special case. They're stored in the front end, but
		// there are subdirectories based on type. Extensions can define custom types,
		// therefore there is no predetermined set of directories. We have to scan for it.
		$this->createPluginFilters();

		return parent::hasFilters();
	}

	private function createFilterEntry($filter_class, $root, $base_path)
	{
		// Get the items of the specified filter class
		$filter = AEFactory::getFilterObject($filter_class);
		$items = $filter->getFilters($root);
		if(!empty($items))
		{
			// Add a directory exclusion for each item
			foreach($items as $item)
			{
				$this->set('[SITEROOT]', $base_path.'/'.$item);
			}
		}
	}

	private function createPluginFilters()
	{
		// Base plugins path
		$plugins_path = 'plugins';

		// Get all plug-in filters
		$filter = AEFactory::getFilterObject('plugins');
		$types = $filter->getFilters(null);
		if(!empty($types))
		{
			// Loop all plug-in types
			foreach($types as $type => $items)
			{
				if(!empty($items))
				{
					// Base path for this plugin type
					$base_path = $plugins_path.'/'.$type;
					// Loop all plugins of this type and add a directory exclusion
					foreach($items as $item)
					{
						$this->set('[SITEROOT]', $base_path.'/'.$item);
					}
				}
			}
		}
	}
}