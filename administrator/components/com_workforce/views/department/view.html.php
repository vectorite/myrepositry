<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

class WorkforceViewDepartment extends JView
{
    protected $form;
	protected $item;
	protected $state;

	function display($tpl = null)
	{
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

    protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);

		JToolBarHelper::title($isNew ? '<span class="wf_adminHeader">'.JText::_('COM_WORKFORCE_ADD_DEPARTMENT').'</span>' : '<span class="wf_adminHeader">'.JText::_('COM_WORKFORCE_EDIT_DEPARTMENT').'</span> <span class="wf_adminSubheader">['.$this->item->name.']</span>', 'workforce');

		// If not checked out, can save the item.
        JToolBarHelper::apply('department.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('department.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('department.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);

		// If an existing item, can save to a copy.
		if (!$isNew) {
			JToolBarHelper::custom('department.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('department.cancel','JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('department.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
?>