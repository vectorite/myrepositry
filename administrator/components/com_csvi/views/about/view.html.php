<?php
/**
 * About view
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
 * About View
 *
* @package CSVI
 */
class CsviViewAbout extends JView {

	/**
	* About view display method
	* @return void
	* */
	function display($tpl = null) {

		// Assign the values
		$this->folders = $this->get('FolderCheck');

		// Get the panel
		$this->loadHelper('panel');

		// Show the toolbar
		JToolBarHelper::title(JText::_('COM_CSVI_ABOUT'), 'csvi_about_48');
		//JToolBarHelper::help('about.html', true);

		// Display it all
		parent::display($tpl);
	}
}
?>