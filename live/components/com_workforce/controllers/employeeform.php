<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

class WorkforceControllerEmployeeForm extends JControllerForm
{
	protected $view_item = 'employeeform';
    protected $view_list = 'employee';

	public function add()
	{
		if (!parent::add()) {
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}
	}
    
    protected function allowAdd($data = array())
	{
        $allow  = parent::allowAdd($data);
        $user = JFactory::getUser();
        
        // Check if the user should be in this editing area        
        $allow  = $user->authorise('core.admin', 'com_workforce');
        
        return $allow;
	}
    
    protected function allowEdit($data = array(), $key = 'id')
	{
        $user = JFactory::getUser();
        
        // Check if the user should be in this editing area
        $recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
        
        if($user->authorise('core.admin', 'com_workforce')){
            return true;
        }else{ 
            $employee = WorkforceHelperQuery::buildEmployee($recordId);
            
            if($employee->user_id == $user->get('id')){
                return true;
            }else{
                return false;
            }
        }
	}
    
	public function cancel($key = 'id')
	{
		parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}

	public function edit($key = null, $urlVar = 'id')
	{
        $result = parent::edit($key, $urlVar);

		return $result;
	}

	public function &getModel($name = 'employeeform', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		// Need to override the parent method completely.
		$tmpl		= JRequest::getCmd('tmpl');
		$layout		= JRequest::getCmd('layout', 'edit');
		$append		= '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		$append .= '&layout=edit';

		if ($recordId) {
			$append .= '&'.$urlVar.'='.$recordId;
		}

		$itemId	= JRequest::getInt('Itemid');
		$return	= $this->getReturnPage();

		if ($itemId) {
			$append .= '&Itemid='.$itemId;
		}

		if ($return) {
			$append .= '&return='.base64_encode($return);
		}

		return $append;
	}

	protected function getReturnPage()
	{
		$return = JRequest::getVar('return', null, 'default', 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return))) {
			return JURI::base();
		}
		else {
			return base64_decode($return);
		}
	}
    
    protected function postSaveHook(JModel &$model, $validData = array())
	{
        // do any after save functions as needed
	}

	public function save($key = null, $urlVar = 'id')
	{
		$task   = $this->getTask();
        $result = parent::save($key, $urlVar);
        
        // If ok, redirect to the return page.
		if ($result && $task != 'apply') {
			$this->setRedirect($this->getReturnPage());
		}

		return $result;
	}
}
