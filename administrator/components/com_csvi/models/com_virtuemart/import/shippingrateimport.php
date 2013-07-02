<?php
/**
 * Shipping rates import
 *
 * @package 	CSVI
 * @subpackage 	Import
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: couponimport.php 1959 2012-04-04 13:24:22Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for coupons
 *
 * Main processor for importing coupons
 *
* @package CSVI
 * @todo 	Check vendor ID
 */
class CsviModelShippingrateimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the vm_coupons table */
	private $_shipmentmethods = null;
	private $_shipmentmethods_lang = null;
	private $_shipmentmethod_shoppergroups = null;

	// Public variables
	/** @var integer contains the coupon ID */
	public $virtuemart_shipmentmethod_id = null;
	public $shippingrate_delete = 'N'; 
	public $shipment_params = null;
	public $shopper_group_name = null;
	public $shipment_jplugin_id = null;
	
	// Private variables
	private $_tablesexist = true;

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
		// Set some initial values
		$this->date = JFactory::getDate();
		$this->user = JFactory::getUser();
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
		
		// Only continue if all tables exist
		if ($this->_tablesexist) {
			// Load the data
			$this->loadData();
			
			// Load the helper
			$this->helper = new Com_VirtueMart();
			
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
							$this->published = $value;
							break;
						default:
							$this->$name = $value;
							$csvilog->addDebug($name.':: '.json_encode($value));
							break;
					}
				}
			}
			
			// All good
			return true;
		}
		else {
			$template = $jinput->get('template', null, null);
			$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_LANG_TABLE_NOT_EXIST', $template->get('language', 'general')));
			return false;
		}
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
		$template = $jinput->get('template', null, null);

		// Check for the shipmentmethod ID
		if (!isset($this->virtuemart_shipmentmethod_id)) $this->_getShipmentmethodId();
		
		// Check if we need to delete the manufacturer
		if ($this->shippingrate_delete == 'Y') {
			$this->_deleteShipmentmethod();
		}
		else {
			// Combine all the values if needed
			$params = array();
			$params[] = 'shipment_logos';
			$params[] = 'countries';
			$params[] = 'zip_start';
			$params[] = 'zip_stop';
			$params[] = 'weight_start';
			$params[] = 'weight_stop';
			$params[] = 'weight_unit';
			$params[] = 'nbproducts_start';
			$params[] = 'nbproducts_stop';
			$params[] = 'orderamount_start';
			$params[] = 'orderamount_stop';
			$params[] = 'cost';
			$params[] = 'package_fee';
			$params[] = 'tax_id';
			$params[] = 'tax';
			$params[] = 'free_shipment';
			if (is_null($this->shipment_params)) {
				$this->shipment_params = '';
				foreach ($params as $param) {
					switch ($param) {
						case 'shipment_logos':
							$this->shipment_params .= $param.'='.json_encode(explode(',', $this->$param)).'|';
							break;
						case 'countries':
							// Retrieve the country ID
							$countries = explode(',', $this->$param);
							$country_ids = array();
							foreach ($countries as $country) {
								$result = $this->helper->getCountryId($country);
								if (!empty($result)) $country_ids[] = $result;
							}
							if (empty($country_ids)) $country_ids = '';
							$this->shipment_params .= $param.'='.json_encode($country_ids).'|';
							break;
						case 'tax_id':
							if (isset($this->$param)) $this->shipment_params .= $param.'="'.$this->$param.'"|';
							break;
						case 'tax':
							if (isset($this->$param)) {
								// Retrieve the calc ID
								switch ($this->$param) {
									case 'norule':
										$result = -1;
										break;
									case 'default':
										$result = 0;
										break;
									default:
										$db = JFactory::getDbo();
										$query = $db->getQuery(true);
										$query->select('virtuemart_calc_id');
										$query->from('#__virtuemart_calcs');
										$query->where('calc_name = '.$db->quote($this->$param));
										$db->setQuery($query);
										$result = $db->loadResult();
										break;
								}
								$this->shipment_params .= 'tax_id="'.$result.'"|';
							}
							break;
						default:
							if (isset($this->$param)) $this->shipment_params .= $param.'="'.$this->$param.'"|';
							break;
					}
				}
				
			}
			$csvilog->addDebug('Params: '.$this->shipment_params);
			
			// Check for the plugin ID
			if (is_null($this->shipment_jplugin_id) && $template->get('vmshipment', 'shippingrate')) {
				$this->shipment_jplugin_id = $template->get('vmshipment', 'shippingrate');
			}
			
			// Bind the data
			$this->_shipmentmethods->bind($this);
	
			// Set the modified date as we are modifying the product
			if (!isset($this->modified_on)) {
				$this->_shipmentmethods->modified_on = $this->date->toMySQL();
				$this->_shipmentmethods->modified_by = $this->user->id;
			}
	
			// Add a creating date if there is no product_id
			if (empty($this->virtuemart_shipmentmethod_id)) {
				$this->_shipmentmethods->created_on = $this->date->toMySQL();
				$this->_shipmentmethods->created_by = $this->user->id;
			}
	
			
			// Store the data
			if ($this->_shipmentmethods->store()) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_SHIPMENTMETHOD'));
				else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_SHIPMENTMETHOD'));

				$this->virtuemart_shipmentmethod_id = $this->_shipmentmethods->get('virtuemart_shipmentmethod_id');
			}
			else $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_SHIPMENTMETHOD_NOT_ADDED', $this->_shipmentmethods->getError()));

			// Store the debug message
			$csvilog->addDebug(JText::_('COM_CSVI_SHIPMENTMETHOD_QUERY'), true);

			// Store the language fields
			$this->_shipmentmethods_lang->bind($this);
			$this->_shipmentmethods_lang->virtuemart_shipmentmethod_id = $this->virtuemart_shipmentmethod_id;

			if ($this->_shipmentmethods_lang->check()) {
				if ($this->_shipmentmethods_lang->store()) {
					if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_SHIPMENTMETHOD_LANG'));
					else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_SHIPMENTMETHOD_LANG'));
				}
				else {
					$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_SHIPMENTMETHOD_LANG_NOT_ADDED', $this->_shipmentmethods_lang->getError()));
					return false;
				}
			}
			else {
				$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_SHIPMENTMETHOD_LANG_NOT_ADDED', $this->_shipmentmethods_lang->getError()));
				return false;
			}

			// Store the debug message
			$csvilog->addDebug(JText::_('COM_CSVI_SHIPMENTMETHOD_LANG_QUERY'), true);
			
			// Process any shopper groups
			if (!empty($this->shopper_group_name)) {
				// Delete all existing groups
				$this->_shipmentmethod_shoppergroups->deleteOldGroups($this->virtuemart_shipmentmethod_id);
				
				// Add new groups
				$this->_shipmentmethod_shoppergroups->virtuemart_shipmentmethod_id = $this->virtuemart_shipmentmethod_id;
				$shoppergroups = explode('|', $this->shopper_group_name);
				foreach ($shoppergroups as $group) {
					$this->_shipmentmethod_shoppergroups->virtuemart_shoppergroup_id = $this->helper->getShopperGroupId($group);
					$this->_shipmentmethod_shoppergroups->store();
					$this->_shipmentmethod_shoppergroups->id = null;
				}
			}
		}

		// Clean the tables
		$this->cleanTables();
	}

	/**
	 * Load the coupon related tables
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
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		
		$this->_shipmentmethods = $this->getTable('shipmentmethods');
		
		// Check if the language tables exist
		$db = JFactory::getDbo();
		$tables = $db->getTableList();
		if (!in_array($db->getPrefix().'virtuemart_shipmentmethods_'.$template->get('language', 'general'), $tables)) {
			$this->_tablesexist = false;
		}
		else {
			$this->_shipmentmethods_lang = $this->getTable('shipmentmethods_lang');
		}
		$this->_shipmentmethod_shoppergroups = $this->getTable('shipmentmethod_shoppergroups');
	}

	/**
	 * Cleaning the coupon related tables
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
		$this->_shipmentmethods->reset();
		$this->_shipmentmethods_lang->reset();
		$this->_shipmentmethod_shoppergroups->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}
	
	/**
	 * Delete a manufacturer and its references
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.0
	 */
	private function _deleteShipmentmethod() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		if (!empty($this->virtuemart_shipmentmethod_id)) {
			$db = JFactory::getDbo();
	
			// Delete translations
			jimport('joomla.language.helper');
			$languages = array_keys(JLanguageHelper::getLanguages('lang_code'));
			foreach ($languages as $language){
				$query = $db->getQuery(true);
				$query->delete('#__virtuemart_shipmentmethods_'.strtolower(str_replace('-', '_', $language)));
				$query->where('virtuemart_shipmentmethod_id = '.$this->virtuemart_shipmentmethod_id);
				$db->setQuery($query);
				$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_SHIPMENTMETHOD_LANG_XREF'), true);
				$db->query();
			}
	
			// Delete shipmentmethod
			if ($this->_shipmentmethods->delete($this->virtuemart_shipmentmethod_id)) {
				$csvilog->AddStats('deleted', JText::_('COM_CSVI_DELETE_SHIPMENTMETHOD'));
			}
			else {
				$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_SHIPMENTMETHOD_NOT_DELETED', $this->_shipmentmethods->getError()));
			}
		}
		else {
			$csvilog->AddStats('incorrect', JText::_('COM_CSVI_SHIPMENTMETHOD_NOT_DELETED_NO_ID'));
		}
	}
	
	/**
	 * Get the manufacturer ID
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return 		mixed	integer when category ID found | false when not found
	 * @since 		3.0
	 */
	private function _getShipmentMethodId() {
		$this->_shipmentmethods_lang->set('shipment_name', $this->shipment_name);
		if ($this->_shipmentmethods_lang->check(false)) {
			$this->virtuemart_shipmentmethod_id = $this->_shipmentmethods_lang->virtuemart_shipmentmethod_id;
			return true;
		}
		else return false;
	}
}
?>