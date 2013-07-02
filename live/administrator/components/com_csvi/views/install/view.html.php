<?php
/**
 * Install view
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php 1976 2012-04-24 06:43:42Z RolandD $
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );

/**
 * Install View
 *
 * @package CSVI
 */
class CsviViewInstall extends JView {

	/**
	 * Display the installation screen
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function display($tpl = null) {
		// Empty the message queue
		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('COM_CSVI_COMPLETE_CSVI_INSTALL'), 'warning');
		
		// Load the stylesheet
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'administrator/components/com_csvi/assets/css/install.css');

		// Load the installed version
		$this->selectversion = $this->get('Version');
		$this->newversion = JText::_('COM_CSVI_CSVI_VERSION');

		// Options of extra tasks to do during installation
		$this->installoptions = array();
		$this->installoptions[] = JHtml::_('select.option', 'availablefields', JText::_('COM_CSVI_UPDATEAVAILABLEFIELDS_LABEL'));
		$this->installoptions[] = JHtml::_('select.option', 'sampletemplates', JText::_('COM_CSVI_INSTALLDEFAULTTEMPLATES_LABEL'));

		// Show the toolbar
		JToolBarHelper::title(JText::_('COM_CSVI_INSTALL'), 'csvi_install_48');
		//JToolBarHelper::help('install.html', true);

		// Display it all
		parent::display($tpl);
	}
}
?>