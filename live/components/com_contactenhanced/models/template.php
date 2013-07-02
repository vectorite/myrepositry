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
class ContactenhancedModelTemplate extends JModelItem
{

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_contactenhanced.template';

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
	public function &getItem($pk)
	{

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {
			try
			{
				$user	= JFactory::getUser();
				//$groups	= implode(',', $user->getAuthorisedViewLevels());
		
				// Create a new query object.
				$db		= JFactory::getDbo();
				$query	= $db->getQuery(true);
		
				// Select required fields from the categories.
				$query->select('*');
				$query->from('`#__ce_template` AS tpl');
				$query->where('id = '.$db->Quote($pk));
				$db->setQuery($query);
				$data = $db->loadObject();

				if ($error = $db->getErrorMsg()) {
					throw new JException($error);
				}

				if (empty($data)) {
					throw new JException(JText::sprintf('CE_ERROR_TEMPLATE_NOT_FOUND',$pk), 500);
				}
 
				$this->_item[$pk] = $data;
			}
			catch (JException $e)
			{
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}
  		return $this->_item[$pk];
	}
}


