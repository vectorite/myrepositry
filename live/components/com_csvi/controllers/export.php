<?php
/**
 * Export view
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: export.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

/**
 * Api Controller
 *
 * @package CSVI
 */
class CsvivirtuemartControllerExport extends JController {
	
	/**
	 * Method to display the view 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 
	 * @since 		3.0
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Export for front-end 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 
	 * @since 		3.0
	 */
	public function Export() {
		// Create the view
		$view = $this->getView('export', 'raw');
		
		// Add the export model
		$view->setModel( $this->getModel( 'export', 'CsvivirtuemartModel' ), true );
		
		// Add the export model path
		$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.'/models');
		$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.'/models/export');
		
		// General export functions
		$view->setModel( $this->getModel( 'exportfile', 'CsvivirtuemartModel' ));
		// Log functions
		$view->setModel( $this->getModel( 'log', 'CsvivirtuemartModel' ));
		// Settings functions
		$view->setModel( $this->getModel( 'settings', 'CsvivirtuemartModel' ));
		// General category functions
		$view->setModel( $this->getModel( 'category', 'CsvivirtuemartModel' ));
		// Available fields
		$view->setModel( $this->getModel( 'availablefields', 'CsvivirtuemartModel' ));
		
		// Load the model
		$model = $this->getModel('export');
		
		// Add extra helper paths
		$view->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers');
		$view->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/xml');
		$view->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');
		
		// Load the helper classes
		$view->loadHelper('csvidb');
		$view->loadHelper('vm_config');
		$view->loadHelper('template');
		
		// Prepare for export
		if ($model->getPrepareExport()) {		
			// Set the layout
			$view->setLayout('export');
			
			// Display it all
			$view->display();
		}
	}
}
?>