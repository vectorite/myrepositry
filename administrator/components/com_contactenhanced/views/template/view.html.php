<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     	Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Contact component
 *
 * @since		1.5
 */
class ContactenhancedViewTemplate extends JView
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
		$this->form		= $this->get('form');
		$this->item		= $this->get('item');
		$this->state	= $this->get('state');
		$params			= JComponentHelper::getParams('com_contactenhanced');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		$this->assignRef('params',	$params);
		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		JRequest::setVar('hidemainmenu', 1);

		JToolBarHelper::title(JText::_('CE_TPL_MANAGER'), 'contact.png');
		JToolBarHelper::apply('template.apply','JTOOLBAR_APPLY');
		JToolBarHelper::save('template.save','JTOOLBAR_SAVE');
		JToolBarHelper::addNew('template.save2new', 'JTOOLBAR_SAVE_AND_NEW');
				// If an existing item, can save to a copy.
		if (!$isNew) {
			JToolBarHelper::custom('template.save2copy','save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY',false );
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('template.cancel','JTOOLBAR_CANCEL');
		} else {
			JToolBarHelper::cancel('template.cancel', 'JTOOLBAR_CLOSE');
		}
		//JToolBarHelper::divider();
		//JToolBarHelper::help('JHELP_COMPONENTS_CONTACTS_CONTACTS_EDIT');
	}
}
