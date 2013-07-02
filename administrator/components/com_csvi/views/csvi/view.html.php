<?php
/**
 * Control panel
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
 * Default View
 *
* @package CSVI
 */
class CsviViewCsvi extends JView {
	/**
	 * CSVI VirtueMart view display method
	 *
	 * @return void
	 */
	function display($tpl = null) {
		// Show the toolbar
		JToolBarHelper::title(JText::_('COM_CSVI_CONTROL_PANEL'), 'csvi_logo_48.png' );
		// Options button.
		if (JFactory::getUser()->authorise('core.admin', 'com_csvi')) {
			JToolBarHelper::preferences('com_csvi');
		}
		//JToolBarHelper::help('control_panel.html', true);

		// Assign data for display
		$helper = new CsviHelper();
	    $this->cpanel_images = $helper->getButtons();

		// Display the page
		parent::display($tpl);
	}
}
?>
