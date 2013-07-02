<?php
/**
 * Product type names import
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: producttypenamesimport.php 1892 2012-02-11 11:01:09Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for product type names
 */
class CsviModelProducttypenamesimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the vm_product table */
	private $_vm_product_product_type_xref = null;

	// Public variables
	/** @var int contains the ID for the product type */
	public $product_type_id = null;
	/** @var int contains the ID for the product */
	public $product_id = null;

	// Private variables
	/** @var mixed contains the data of the current field being processed */
	private $_datafield = null;
	/** @var object contains general import functions */
	private $_importmodel = null;

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
		$this->helper = new Mod_Vm_Cherry_Picker();
		$this->vmhelper = new Com_VirtueMart();
		
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
		return true;
	}

	/**
	 * Process each record and store it in the database
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo 		Merge with vm_product_type_x table class
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function getProcessRecord() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$db = JFactory::getDbo();
		$action = '';

		// Get the product type */
		if (empty($this->product_type_id) && isset($this->product_type_name)) {
			$this->product_type_id = $this->helper->getProductTypeId($this->product_type_name);
		}

		// Get the product ID
		if (empty($this->product_id)) $this->product_id = $this->vmhelper->getProductId();

		if ($this->product_type_id && $this->product_id) {
			// Get the fields for the product type #
			$q = "SHOW COLUMNS FROM #__vm_product_type_".$this->product_type_id;
			$db->setQuery($q);
			$columns = $db->loadResultArray();

			if (is_array($columns)) {
				// Check if the product type ID already exists
				$q = "SELECT COUNT(product_id) AS products
					FROM #__vm_product_type_".$this->product_type_id."
					WHERE product_id = '".$this->product_id."'";
				$db->setQuery($q);
				$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_CHECK_PRODUCT_TYPE_ID_EXISTS'), true);
				$product_type_exists = $db->loadResult();

				// Variable used for reporting query type
				if ($product_type_exists > 0) {
					$q = "UPDATE #__vm_product_type_".$this->product_type_id." ";
					$q .= "SET ";

					foreach ($columns as $key => $colname) {
						$colname = strtolower($colname);
						if (isset($this->$colname)) {
							$q .= $db->nameQuote($colname)." = ".$db->quote($this->$colname).",";
						}
					}
					$q = substr($q, 0, -1)." ";
					$q .= "WHERE product_id = ".$this->product_id;

					$action = 'updated';
					$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_UPDATING_NEW_PRODUCT_TYPE_DETAILS'), true);
				}
				else {
					$q = "INSERT INTO #__vm_product_type_".$this->product_type_id." ";
					$q .= "(";
					$qfields = '';
					$qvalues = '';

					foreach ($columns as $key => $colname) {
						$colname = strtolower($colname);
						if (isset($this->$colname)) {
							$qfields .= $db->nameQuote($colname).',';
							$qvalues .= $db->quote($this->$colname).',';
						}

					}
					$q .= substr($qfields, 0, -1);
					$q .= ") VALUES (";
					$q .= substr($qvalues, 0, -1);
					$q .= ")";
					$action = 'added';
					$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_ADDING_NEW_PRODUCT_TYPE_DETAILS'), true);
				}
				$db->setQuery($q);
				if ($db->query()) {
					$csvilog->addDebug(JText::sprintf('COM_CSVI_UPDATE_PRODUCT_TYPE_X', $this->product_type_id), true);
					$identify = (isset($this->product_sku)) ? $this->product_sku : $this->product_id;
					$csvilog->AddStats($action, JText::sprintf('COM_CSVI_PRODUCT_TYPE_DETAIL_SKU_ID', $identify));
				}
				else {
					$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_PRODUCT_TYPE_NAMES_STORE_FAILED'), true);
					$csvilog->AddStats('incorrect', JText::_('COM_CSVI_PRODUCT_TYPE_NAMES_NOT_STORED'));
				}

				// Bind the data for cross reference
				$this->_vm_product_product_type_xref->bind($this);
				// Add the cross reference
				$this->_vm_product_product_type_xref->store();
			}
			else {
				$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_PRODUCT_TYPE_ID_NOT_FOUND'), true);
				$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_PRODUCT_TYPE_ID_FOUND'));
			}
		}
		else {
			if (!$this->product_type_id) {
				$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_PRODUCT_TYPE_ID_NOT_FOUND'));
				$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_PRODUCT_TYPE_ID_FOUND'));
			}
			else {
				$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_PRODUCT_ID_NOT_FOUND'));
				$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_PRODUCT_ID_FOUND'));
			}

		}

		// Clean the tables
		$this->cleanTables();
	}

	/**
	 * Load the product type names related tables
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
		$this->_vm_product_product_type_xref = $this->getTable('vm_product_product_type_xref');
	}

	/**
	 * Cleaning the product type names related tables
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
		$this->_vm_product_product_type_xref->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}
}