<?php
/**
 * Product types import
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: producttypeimport.php 1892 2012-02-11 11:01:09Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for product types
 */
class CsviModelProducttypeimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the vm_product_files table */
	private $_vm_product_type = null;

	// Public variables
	/** @var mixed contains the ID for the product type */
	public $product_type_id = null;

	// Private variables
	/** @var mixed contains the data of the current field being processed */
	private $_datafield = null;
	/** @var object contains general import functions */
	private $_importmodel = null;
	/** @var boolean contains the setting if a new product type table needs to be created */
	private $create_table = false;

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
		// Get the logger
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		
		// Load the data
		$this->loadData();
		
		// Load the helper
		$this->helper = new Com_VirtueMart();
	
		// Get the file_product_id
		$this->file_product_id = $this->helper->getProductId();

		// Process data
		foreach ($this->csvi_data as $name => $fields) {
			foreach ($fields as $filefieldname => $details) {
				$value = $details['value'];
				// Check if the field needs extra treatment
				switch ($name) {
					default:
						$this->$name = $value;
						break;
				}
			}
		}

		// Check if the list order is empty
		if (!isset($this->product_type_list_order)) $this->_productTypeListOrder();
		
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
		$this->_vm_product_type->bind($this);

		// Check if we already have a product type ID
		if (is_null($this->product_type_id)) {
			$this->_vm_product_type->check();
			if (is_null($this->_vm_product_type->get('product_type_id'))) {
				$this->create_table = true;
			}
		}

		// Store the data
		if ($this->_vm_product_type->store()) {
			if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_PRODUCTTYPE'));
			else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_PRODUCTTYPE'));
		}
		else $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_PRODUCTTYPE_NOT_ADDED', $this->_vm_product_type->getError()));

		// Store the debug message
		$csvilog->addDebug(JText::_('COM_CSVI_PRODUCTTYPE_QUERY'), true);

		if ($this->create_table) {
			$this->product_type_id = $this->_vm_product_type->get('product_type_id');
			// We have a new product type, need to create the table
			if ($this->_createProductTypeTable()) {
				$csvilog->AddStats('added', JText::_('COM_CSVI_CREATED_PRODUCT_TYPE_TABLE'));
			}
			else {
				$csvilog->AddStats('error', JText::_('COM_CSVI_ERROR_CREATED_PRODUCT_TYPE_TABLE'));
			}
		}

		// Clean the tables
		$this->cleanTables();
	}

	/**
	 * Load the product type related tables
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
		$this->_vm_product_type = $this->getTable('vm_product_type');
	}

	/**
	 * Cleaning the product type related tables
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
		$this->_vm_product_type->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}

	/**
	 * Get the highest list order and add 1 for the new list order
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
	private function _productTypeListOrder() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('MAX(product_type_list_order) AS list_order')->from('#__vm_product_type');
		$db->setQuery($query);
		$db->query();
		$this->product_type_list_order = $db->loadResult()+1;
	}

	/**
	 * New product types require new tables
	 *
	 * Tables are created with the name product_type_<id>
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return 		bool	true if table is created | false if table cannot be created
	 * @since 		3.0
	 */
	private function _createProductTypeTable() {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);

		$q = "CREATE TABLE IF NOT EXISTS `#__vm_product_type_";
		$q .= $this->product_type_id . "` (";
		$q .= $db->nameQuote('product_id')." int(11) NOT NULL,";
		$q .= "PRIMARY KEY (".$db->nameQuote('product_id').")";
		$q .= ")ENGINE=MyISAM";
		$db->setQuery($q);
		$csvilog->addDebug('COM_CSVI_CREATE_PRODUCT_TYPE_TABLE', true);
		if ($db->query()) return true;
		else return false;
	}
}