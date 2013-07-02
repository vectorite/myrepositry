<?php
/**
 * Settings view
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.application.component.view' );

/**
 * Settings View
 *
* @package CSVI
 */
class CsviViewSettings extends JView {

	/**
	* About view display method
	* @return void
	* */
	function display($tpl = null) {
		// Load the form
		$this->form = $this->get('Form');

		// Load a list of tables
		$this->tablelist = $this->get('TableList');

		// Get the panel
		$this->loadHelper('panel');

		// Show the toolbar
		JToolBarHelper::title(JText::_('COM_CSVI_SETTINGS_TITLE'), 'csvi_settings_48');
		JToolBarHelper::custom('settings.reset', 'csvi_reset_32', 'csvi_reset_32', JText::_('COM_CSVI_RESET_SETTINGS'), false);
		JToolBarHelper::custom('settings.save', 'csvi_save_32', 'csvi_save_32', JText::_('COM_CSVI_SAVE'), false);
		//JToolBarHelper::help('settings.html', true);

		// Display it all
		parent::display($tpl);
	}
}
?>