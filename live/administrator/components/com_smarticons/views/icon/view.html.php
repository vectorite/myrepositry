<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id: view.html.php 9 2012-03-28 20:07:32Z Bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class SmartIconsViewIcon extends JView
{
	function display($tpl = null) {
		// get the Data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->canDo = SmartIconsHelper::getActions($this->item->idIcon);
		// Set the toolbar
		$this->addToolBar();
		
		//Set language file from mod_quickicons
		JFactory::getLanguage()->load('mod_quickicon');
			
		parent::display($tpl);
		
		// Set the document
		$this->setDocument();
	}
	function addToolbar() {
		JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->idIcon == 0;
		$canDo = SmartIconsHelper::getActions($this->item->idIcon);
		JToolBarHelper::title($isNew ? JText::_('COM_SMARTICONS_MANAGER_ICON_NEW') : JText::_('COM_SMARTICONS_MANAGER_ICON_EDIT'), 'smarticons');
		// Built the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::apply('icon.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('icon.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('icon.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('icon.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('icon.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('icon.save', 'JTOOLBAR_SAVE');

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create'))
				{
					JToolBarHelper::custom('icon.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::custom('icon.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('icon.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	protected function setDocument()
	{
		$isNew = $this->item->idIcon == 0;
		$application = JFactory::getApplication();
		$template = $application->getTemplate();
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_SMARTICONS_ADMINISTRATION_ICON_NEW') : JText::_('COM_SMARTICONS_ADMINISTRATION_ICON_EDIT'));
		$document->addScript("../media/com_smarticons/js/edit.js");
		$document->addStyleSheet(JPATH_ADMINISTRATOR.DS.'templates'.DS.$template.DS.'css'.DS.'template.css');
		JText::script('COM_SMARTICONS_ERROR_UNACCEPTABLE');
	}
}