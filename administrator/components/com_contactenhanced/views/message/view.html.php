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
class ContactenhancedViewMessage extends JView
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
		$lang =& JFactory::getLanguage();
		$lang->load('com_contactenhanced',JPATH_ROOT);

		$this->item		= $this->get('item');
		$this->state	= $this->get('state');
		$params			= JComponentHelper::getParams('com_contactenhanced');
		
		$customfields	=& $this->get('RecordedFields');
		$attachments	=& $this->get('Attachments');
		$replies		=& $this->get('Replies');
		
		$this->addToolbar();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		

		$this->assignRef('customfields',	$customfields);
		$this->assignRef('attachments',		$attachments);
		$this->assignRef('replies',			$replies);
		$this->assignRef('params',			$params);
		
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

		JToolBarHelper::title(JText::_('CE_TITLE_MESSAGE'), 'contact.png');
		JToolBarHelper::back();
		JToolBarHelper::cancel('message.cancel','JTOOLBAR_CANCEL');
	}
}
