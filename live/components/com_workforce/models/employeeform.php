<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_workforce/models/employee.php';
jimport('joomla.event.dispatcher');

class WorkforceModelEmployeeForm extends WorkforceModelEmployee
{
	protected function populateState()
	{
		$app    = JFactory::getApplication();

		// Load state from the request.
		$pk     = JRequest::getInt('id');
		$this->setState('employee.id', $pk);

		$return = JRequest::getVar('return', null, 'default', 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', JRequest::getCmd('layout'));
	}

	public function getItem($itemId = null)
	{
		// Initialise variables.
		$itemId     = (int) (!empty($itemId)) ? $itemId : $this->getState('employee.id');

		// Get a row instance.
		$table      = $this->getTable();

		// Attempt to load the row.
		$return     = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$item      = JArrayHelper::toObject($properties, 'JObject');
        
        if (property_exists($item, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}
        
        //$value->params = new JRegistry;
        if ($itemId) {
            $item->name = $item->fname.' '.$item->lname;
        }

		return $item;
	}

	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
}