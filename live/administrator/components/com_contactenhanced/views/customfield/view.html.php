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
class ContactenhancedViewCustomfield extends JView
{
	protected $form;
	protected $item;
	protected $state;
	protected $code_editor = null;

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
		
		if($this->item->type == 'css' OR $this->item->type == 'js' OR $this->item->type == 'php'){
			$db	= JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__extensions WHERE name =".$db->Quote('plg_editors_codemirror'));
			if($db->loadResult()){
				$this->code_editor	= 'codemirror';
				JFactory::getApplication()->setUserState('editor.source.syntax',$this->item->type);
			}
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
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		JRequest::setVar('hidemainmenu', 1);

		JToolBarHelper::title(JText::_('CE_CF_MANAGER'), 'contact.png');
		JToolBarHelper::apply('customfield.apply','JTOOLBAR_APPLY');
		JToolBarHelper::save('customfield.save','JTOOLBAR_SAVE');
		JToolBarHelper::addNew('customfield.save2new', 'JTOOLBAR_SAVE_AND_NEW');
				// If an existing item, can save to a copy.
		if (!$isNew) {
			JToolBarHelper::custom('customfield.save2copy','save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY',false );
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('customfield.cancel','JTOOLBAR_CANCEL');
		} else {
			JToolBarHelper::cancel('customfield.cancel', 'JTOOLBAR_CLOSE');
		}
		//JToolBarHelper::divider();
		//JToolBarHelper::help('JHELP_COMPONENTS_CONTACTS_CONTACTS_EDIT');
	}
}
