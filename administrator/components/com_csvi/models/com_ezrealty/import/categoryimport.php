<?php
/**
 * Category import
 *
 * @package 	CSVI
 * @subpackage 	Import
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: couponimport.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for coupons
 *
 * Main processor for importing coupons
 *
 * @package CSVI
 * 
 */
class CsviModelCategoryimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the properties table */
	private $_categories = null;

	// Public variables
	/** @var integer contains the property ID */
	public $id = null;

	/**
	 * Constructor
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.4
	 */
	public function __construct() {
		parent::__construct();
		// Load the tables that will contain the data
		$this->_loadTables();
		$this->loadSettings();
    }

	/**
	 * Here starts the processing
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
	public function getStart() {
		// Load the data
		$this->loadData();

		// Get the logger
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);

		// Process data
		foreach ($this->csvi_data as $name => $fields) {
			foreach ($fields as $filefieldname => $details) {
				$value = $details['value'];
				// Check if the field needs extra treatment
				switch ($name) {
					case 'published':
						switch ($value) {
							case 'n':
							case 'N':
							case '0':
								$value = 0;
								break;
							default:
								$value = 1;
								break;
						}
						$this->$name = $value;
						break;
					case 'name':
						// Procected value
						$this->category_name = $value;
						break;
					default:
						$this->$name = $value;
						break;
				}
			}
		}

		// All good
		return true;
	}

	/**
	 * Process each record and store it in the database
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
	public function getProcessRecord() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		
		// Bind the data
		$this->_categories->bind($this);
		
		// Manually bind the category name
		$this->_categories->name = $this->category_name;
		
		// Check the data
		$this->_categories->check();
		
		// Store the data
		if ($this->_categories->store()) {
			if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_CATEGORY_DETAILS'));
			else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_CATEGORY_DETAILS'));
		}
		else  $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_CATEGORY_DETAILS_NOT_ADDED', $this->_categories->getError()));

		// Store the debug message
		$csvilog->addDebug('COM_CSVI_CATEGORY_DETAILS_QUERY', true);
			
		// Clean the tables
		$this->cleanTables();
	}

	/**
	 * Load the related tables
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.0
	 */
	private function _loadTables() {
		$this->_categories = $this->getTable('categories');
	}

	/**
	 * Cleaning the related tables
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return
	 * @since 		3.0
	 */
	protected function cleanTables() {
		$this->_categories->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}
}
?>