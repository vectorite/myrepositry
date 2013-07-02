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

class SmartIconsViewIcons extends JView {
	function display($tpl = null) {
		$this->icons = $this->get('Items');
		$this->pagination = $this->get('pagination');
		$this->state = $this->get('State');

		// Set the toolbar
		$this->addToolBar();
		
		//Set language file from mod_quickicons
		JFactory::getLanguage()->load('mod_quickicon');

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}
	protected function addToolBar() {
		
		$canDo = SmartIconsHelper::getActions();
		JToolBarHelper::title(JText::_('COM_SMARTICONS_MANAGER_ICONS'), 'smarticons');
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('icon.add', 'JTOOLBAR_NEW');
		}
		
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('icon.edit', 'JTOOLBAR_EDIT');
			JToolBarHelper::divider();
		}
		
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::custom('icons.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('icons.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::custom('icons.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			JToolBarHelper::divider();
		}
		
		if ($canDo->get('core.create')) {
			$bar = JToolBar::getInstance('toolbar');
			$bar->appendButton('Popup', 'upload', 'COM_SMARTICONS_ICONS_TOOLBAR_IMPORT', 'index.php?option=com_smarticons&view=import&tmpl=component', 500, 180);
			$bar->appendButton('Link', 'download', 'COM_SMARTICONS_ICONS_TOOLBAR_EXPORT', 'index.php?option=com_smarticons&task=icons.export&format=raw');

// 			JToolBarHelper::custom('icons.export', 'download.png', 'download_f2.png', 'COM_SMARTICONS_ICONS_TOOLBAR_EXPORT', true);
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'icons.delete', 'JTOOLBAR_DELETE');
			JToolBarHelper::divider();
		}
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_smarticons');
		}
	}
	protected function setDocument() {
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_SMARTICONS_ADMINISTRATION'));
	}
}