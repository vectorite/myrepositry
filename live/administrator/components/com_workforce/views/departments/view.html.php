<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.view');

class WorkforceViewDepartments extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		// Initialise variables.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

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
        $canDo	= WorkforceHelper::getActions();

		JToolBarHelper::title('<span class="wf_adminHeader">'.JText::_('COM_WORKFORCE').'</span> <span class="wf_adminSubheader">['.JText::_('COM_WORKFORCE_DEPARTMENTS').']</span>', 'workforce');
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('department.add','JTOOLBAR_NEW');
		}

		if (($canDo->get('core.edit'))) {
			JToolBarHelper::editList('department.edit','JTOOLBAR_EDIT');            
		}

		if ($canDo->get('core.edit.state')) {
			if ($this->state->get('filter.state') != 2){
				JToolBarHelper::divider();
				JToolBarHelper::custom('departments.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('departments.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::divider();
            JToolBarHelper::deleteList('', 'departments.delete','JTOOLBAR_EMPTY_TRASH');			
		}
		else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
            JToolBarHelper::trash('departments.trash','JTOOLBAR_TRASH');			
		}

		if ($canDo->get('core.admin')) {
            JToolBarHelper::divider();
            JToolBarHelper::custom('editcss.edit', 'css.png', 'css_f2.png','JTOOLBAR_EDIT_CSS', false);
			JToolBarHelper::divider();
            JToolBarHelper::preferences('com_workforce');
		}
	}
}
?>