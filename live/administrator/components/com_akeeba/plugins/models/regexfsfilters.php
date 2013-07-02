<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/**
 * Regular expression based filesystem filters management Model
 *
 */
class AkeebaModelRegexfsfilters extends FOFModel
{

	/**
	 * Returns an array containing a list of regex filters and their respective type for a given root
	 * @return array Array of definitions
	 */
	public function get_regex_filters($root)
	{
		// These are the regex filters I know of
		$known_filters = array(
			'regexfiles',
			'regexdirectories',
			'regexskipdirs',
			'regexskipfiles'
		);

		// Filters already set
		$set_filters = array();

		// Loop all filter types
		foreach($known_filters as $filter_name)
		{
			// Get this filter type's set filters
			$filter = AEFactory::getFilterObject($filter_name);
			$temp_filters = $filter->getFilters($root);

			// Merge this filter type's regular expressions to the list
			if( count($temp_filters) )
			{
				foreach($temp_filters as $new_regex)
				{
					$set_filters[] = array(
						'type'	=> $filter_name,
						'item'	=> $new_regex
					);
				}
			}

		}

		return $set_filters;
	}

	/**
	 * Delete a regex filter
	 * @param string $type Filter type
	 * @param string $root The filter's root
	 * @param string $string The filter string to remove
	 * @return bool True on success
	 */
	public function remove( $type, $root, $string )
	{
		$filter = AEFactory::getFilterObject($type);
		$success = $filter->remove($root, $string);
		if($success)
		{
			$filters = AEFactory::getFilters();
			$filters->save();
		}

		return $success;
	}

	/**
	 * Creates a new regec filter
	 * @param string $type Filter type
	 * @param string $root The filter's root
	 * @param string $string The filter string to remove
	 * @return bool True on success
	 */
	public function setFilter( $type, $root, $string )
	{
		$filter = AEFactory::getFilterObject($type);
		$success = $filter->set($root, $string);
		if($success)
		{
			$filters = AEFactory::getFilters();
			$filters->save();
		}

		return $success;
	}

	public function doAjax()
	{
		$action = $this->getState('action');
		$verb = array_key_exists('verb', $action) ? $action['verb'] : null;

		$ret_array = array();

		switch($verb)
		{
			// Produce a list of regex filters
			case 'list':
				$ret_array = $this->get_regex_filters($action['root']);
				break;

			// Set a filter (used by the editor)
			case 'set':
				$ret_array = array('success' => $this->setFilter($action['type'], $action['root'], $action['node']) );
				break;

			// Remove a filter (used by the editor)
			case 'remove':
				$ret_array = array('success' => $this->remove($action['type'], $action['root'], $action['node']) );
				break;
		}
		
		return $ret_array;
	}
}