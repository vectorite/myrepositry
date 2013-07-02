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
 * Joomla! Modules exclusion filter
 */
class AEFilterModules extends AEAbstractFilter
{
	public function __construct()
	{
		$this->object	= 'modules';
		$this->subtype	= 'all';
		$this->method	= 'direct';
		
		if(AEFactory::getKettenrad()->getTag() == 'restorepoint') $this->enabled = false;

		if(empty($this->filter_name)) $this->filter_name = strtolower(basename(__FILE__,'.php'));

		parent::__construct();
	}

	public function &getExtraSQL($root)
	{
		$empty = '';
		if($root != '[SITEDB]') return $empty;

		$sql = '';
		$db = AEFactory::getDatabase();
		$this->getFilters(null); // Forcibly reload the filter data
		// Loop all components and add SQL statements
		if(!empty($this->filter_data))
		{
			foreach($this->filter_data as $type => $items)
			{
				if(!empty($items))
				{
					// Make sure that DB only backups get the correct prefix
					$configuration = AEFactory::getConfiguration();
					$abstract = AEUtilScripting::getScriptingParameter('db.abstractnames', 1);
					if($abstract) {
						$prefix = '#__';
					} else {
						$prefix = $db->getPrefix();
					}

					foreach($items as $item)
					{
						$client = ($type == 'frontend') ? 0 : 1;
						$sql .= 'DELETE FROM '.$db->quoteName($prefix.'modules_menu').' WHERE '.
							$db->quoteName('moduleid').' IN ('.
								'SELECT '.$db->quoteName('id').' FROM '.
								$db->quoteName($prefix.'modules').' WHERE '.
								'('.$db->quoteName('module').' = '.$db->Quote($item).')'.
								' AND ('.$db->quoteName('client_id').' = '.$db->Quote($client).')'.
							");\n";
						$sql .= 'DELETE FROM '.$db->quoteName($prefix.'modules').' WHERE '.
							'('.$db->quoteName('module').' = '.$db->Quote($item).')'.
							' AND ('.$db->quoteName('client_id').' = '.$db->Quote($client).')'.
							";\n";
						$sql .= 'DELETE FROM '.$db->quoteName($prefix.'extensions').
							' WHERE '.$db->quoteName('element').' = '.
							$db->Quote($item)." AND ".$db->quoteName('type').' = '.
							$db->Quote('module').";\n";
					}
				}
			}
		}

		return $sql;
	}
}