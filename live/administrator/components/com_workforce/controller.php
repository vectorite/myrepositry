<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class WorkforceController extends JController
{
	public function display($cachable = false, $urlparams = false)
	{
        $document	= & JFactory::getDocument();
        $document->addStyleSheet('components/com_workforce/assets/css/workforce_backend.css');
        
        if(!JRequest::getCmd('view')){
            JRequest::setVar('view', JRequest::getCmd('view', 'departments'));
        }

		// Load the submenu.
		WorkforceHelper::addSubmenu(JRequest::getCmd('view', 'departments'));

		$view	= JRequest::getCmd('view', 'departments');
		$layout = JRequest::getCmd('layout', 'default');
		$id		= JRequest::getInt('id');        

		// Check for edit form.
		if ($view == 'department' && $layout == 'edit' && !$this->checkEditId('com_workforce.edit.department', $id)) {

			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_workforce&view=departments', false));

			return false;
		}
		else if ($view == 'employee' && $layout == 'edit' && !$this->checkEditId('com_workforce.edit.employee', $id)) {

			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_workforce&view=employees', false));

			return false;
		}

		parent::display($cachable);

		//return $this;
	}
}

