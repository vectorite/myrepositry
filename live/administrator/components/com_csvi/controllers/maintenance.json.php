<?php
/**
 * Maintenance controller
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: maintenance.json.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controllerform');

/**
 * Maintenance Controller
 *
 * @package    CSVIVirtueMart
 */
class CsviControllerMaintenance extends JControllerForm {

	/**
	 * Update available fields in steps
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.3
	 */
	public function updateAvailableFieldsSingle() {
		// Create the view object
		$view = $this->getView('maintenance', 'json');

		// View
		$view->setLayout('availablefields');

		// Load the model
		$view->setModel($this->getModel('maintenance', 'CsviModel'), true);
		$view->setModel($this->getModel( 'availablefields', 'CsviModel' ));

		// Now display the view
		$view->display();
	}
}
?>
