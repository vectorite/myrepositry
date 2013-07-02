<?php
/**
 * Product type parameters import
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: producttypeparametersimport.php 1892 2012-02-11 11:01:09Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for product type parameters
 */
class CsviModelProducttypeparametersimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the vm_product_files table */
	private $_vm_product_type_parameter = null;

	// Public variables
	/** @var integer contains the ID for the product type */
	public $product_type_id = null;
	/** @var string contains the parameter multi-select setting */
	public $parameter_multiselect = 'N';
	/** @var string contains the parameter multi-select values */
	public $parameter_values = null;
	/** @var string contains the parameter multi-select values */
	public $product_type_parameter_delete = 'N';

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
	 * @todo		Multi-select option check
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

		// Process data
		foreach ($this->csvi_data as $name => $fields) {
			foreach ($fields as $filefieldname => $details) {
				$value = $details['value'];
				// Check if the field needs extra treatment
				switch ($name) {
					case 'parameter_name':
						$this->parameter_name = str_replace(" ", "_", $value);
						break;
					case 'parameter_description':
						$find = array("\r\n", "\n");
						$replace = array("", "");
						$this->parameter_description = str_replace($find, $replace, $value);
						break;
					case 'parameter_values':
						// Strip a trailing ;
						if (';' == substr($value, -1)) $value= substr($value, 0, -1);
						$this->parameter_values = $value;
						break;
					case 'parameter_type':
					case 'parameter_multiselect':
					case 'parameter_delete':
						$this->$name = strtoupper($value);
						break;
					default:
						$this->$name = $value;
						break;
				}
			}
		}
		// Check if we have a parameter name
		if (isset($this->parameter_name)) {
			// Get the product type ID
			if (is_null($this->product_type_id)) $this->product_type_id = $this->helper->getProductTypeId($this->product_type_name);
			if ($this->product_type_id) {
				if( $this->parameter_multiselect == "Y" && (!isset($this->parameter_values) || $this->parameter_values == "" )) {
					$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_PARAMETER_VALUES'));
					return false;
				}
				// Check the list order
				$this->_productTypeParameterListOrder();
			}
			else {
				$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_PRODUCT_ID'));
				return false;
			}
		}
		else {
			$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_PARAMETER_NAME'));
			return false;
		}

		// Check for a breakline
		if (empty($this->parameter_label)) {
			if (isset($this->parameter_type) && $this->parameter_type == "B")
				$this->parameter_label = $this->parameter_name;
		}

		// Check the multi-select option
		if ($this->parameter_multiselect == "Y" && is_null($this->parameter_values)) {
			// $csvilog->addDebug("ERROR:  If You checked Multiple select you must enter a Possible Values.");
			return false;
		}
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
		$this->_vm_product_type_parameter->bind($this);

		// Check if we need to store or delete the entry
		if ($this->product_type_parameter_delete == 'Y') {
			// Delete the parameter
			$this->_vm_product_type_parameter->delete();
		}
		else {
			// Store the data
			if ($this->_vm_product_type_parameter->store()) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_PRODUCTTYPEPARAMETER'));
				else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_PRODUCTTYPEPARAMETER'));
			}
			else $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_PRODUCTTYPEPARAMETER_NOT_ADDED', $this->_vm_product_type_parameter->getError()));

			// Store the debug message
			$csvilog->addDebug(JText::_('COM_CSVI_PRODUCTTYPEPARAMETER_QUERY'), true);
		}

		// Clean the tables
		$this->cleanTables();
	}

	/**
	 * Load the product type parameters related tables
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
		$this->_vm_product_type_parameter = $this->getTable('vm_product_type_parameter');
	}

	/**
	 * Cleaning the product type parameters related tables
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
		$this->_vm_product_type_parameter->reset();

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
	private function _productTypeParameterListOrder() {
		$db = JFactory::getDbo();

		if (!isset($this->parameter_list_order) || $this->parameter_list_order == 0) {
			$query = $db->getQuery(true);
			$query->select('MAX(parameter_list_order) AS list_order')
				->from('#__vm_product_type_parameter')
				->where('product_type_id = '.$this->product_type_id);
			$db->setQuery($query);
			$db->query();
			$this->parameter_list_order = $db->loadResult()+1;
		}
	}
}
?>
