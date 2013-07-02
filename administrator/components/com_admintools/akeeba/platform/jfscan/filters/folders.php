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
 * Folder exclusion filter. Excludes certain hosting directories.
 */
class AEFilterPlatformFolders extends AEAbstractFilter
{
	public function __construct()
	{
		$this->object	= 'dir';
		$this->subtype	= 'all';
		$this->method	= 'direct';
		$this->filter_name = 'PlatformFolders';

		// Get the site's root
		$configuration = AEFactory::getConfiguration();
		
		// We take advantage of the filter class magic to inject our custom filters
		$allFolders = explode('|', AEFactory::getConfiguration()->get('akeeba.basic.exclude_folders'));
		$this->filter_data['[SITEROOT]'] = array_unique($allFolders);

		parent::__construct();
	}

}