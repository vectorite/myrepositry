<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.modeladmin');

class WorkforceModelEmployee extends JModelAdmin
{
    protected $text_prefix = 'COM_WORKFORCE';

	public function getTable($type = 'Employee', $prefix = 'WorkforceTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_workforce.employee', 'employee', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_workforce.edit.employee.data', array());

		if (empty($data)) {
			$data = $this->getItem();            

			// Prime some default values.
			if ($this->getState('employee.id') == 0) {
				$app        = JFactory::getApplication();
                $settings   = &JComponentHelper::getParams( 'com_workforce' );

                //Set defaults according to WF config
                $data->department   = $settings->get('default_department');
                $data->street       = $settings->get('default_street');
                $data->city         = $settings->get('default_city');
                $data->locstate     = $settings->get('default_locstate');
                $data->province     = $settings->get('default_province');
                $data->country      = $settings->get('default_country');
                $data->postcode     = $settings->get('default_postcode');
                $data->website      = $settings->get('default_website');
                $data->availability = $settings->get('default_availability');
			}
		}

		return $data;
	}

	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'state >= 0';
		return $condition;
	}
}


?>