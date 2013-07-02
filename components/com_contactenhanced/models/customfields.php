<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * @package		com_contactenhanced
 * @since 1.5
 */
class ContactenhancedModelCustomfields extends JModelItem
{

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_contactenhanced.customfields';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
			// Initialise variables.
		$app	= JFactory::getApplication();
		$this->setState('filter.language',$app->getLanguageFilter());


	}

	/**
	 * Gets a list of contacts
	 * @param array
	 * @return mixed Object or null
	 */
	public function &getItems($catid = null)
	{
		// Initialise variables.
		$catid = (!empty($catid)) ? $catid : 0;

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$catid])) {
			try
			{
				$user	= JFactory::getUser();
				$groups	= implode(',', $user->getAuthorisedViewLevels());
		
				// Create a new query object.
				$db		= JFactory::getDbo();
				$query	= $db->getQuery(true);
		
				// Select required fields from the categories.
				$query->select('cf.*');
				$query->from('`#__ce_cf` AS cf');
				$query->where('cf.access IN ('.$groups.')');
				$query->where('(cf.catid = '.$db->Quote($catid).' OR cf.catid = 0)');
				$query->where('(cf.published > 0 OR (cf.published = 0 AND cf.iscore = 1) )');
		 
				// Filter by language
			//	if ($this->getState('filter.language')) {
					$query->where('cf.language IN (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
			//	}
				// Add the list ordering clause.
				$query->order('cf.catid, cf.ordering ASC');
				
				$db->setQuery($query);
				//echo nl2br(str_replace('#__','jos_',$query)); exit;
 
				$data = $db->loadObjectList();

				if ($error = $db->getErrorMsg()) {
					throw new JException($error);
				}

				if (empty($data)) {
					throw new JException(JText::_('CE_CF_ERROR_CUSTOM_FIELDS_NOT_FOUND'), 500);
				}
 
				$this->_item[$catid] = $data;
			}
			catch (JException $e)
			{
				$this->setError($e);
				$this->_item[$catid] = false;
			}

		}
		
  		return $this->_item[$catid];

	}
}

