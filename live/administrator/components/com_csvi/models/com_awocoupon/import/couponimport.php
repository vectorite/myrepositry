<?php
/**
 * AwoCoupon coupon import
 *
 * @package 	CSVI
 * @subpackage 	Import
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: couponimport.php 2048 2012-07-28 16:27:43Z RolandD $
 */

defined( '_JEXEC' ) or die;

/**
 * Main processor for importing waitinglists
 *
 * @package CSVI
 */
class CsviModelCouponimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the vm_coupons table */
	private $_coupons = null;
	private $_vm_category = null;
	private $_vm_product = null;
	private $_vm_manufacturer = null;
	private $_vm_user = null;
	private $_vm_usergroup = null;
	
	// Private variables
	private $_category_cache = array();
	private $_catsep = '/';
	
	// Public variables
	/** @var integer contains the coupon ID */
	public $id = null;
	public $category_path = null;

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
								$value = '-1';
								break;
							default:
								$value = 1;
								break;
						}
						$this->published = $value;
						break;
					case 'coupon_value':
					case 'min_value':
						$this->$name = $this->cleanPrice($value);
						break;
					case 'startdate':
					case 'expiration':
						$this->$name = $this->convertDate($value);
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
		$this->_coupons->bind($this);

		// Check the data
		$this->_coupons->check();

		// Store the data
		if ($this->_coupons->store()) {
			if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_COUPON'));
			else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_COUPON'));
			
			// Add assets
			switch ($this->_coupons->function_type2) {
				case 'category':
					// Categories
					if (isset($this->category_path)) {
						// Clean out any existing links
						$this->_vm_category->clean($this->_coupons->id);
						// Explode the categories
						$categories = explode('|', $this->category_path);
						foreach ($categories as $category) {
							// Find the cat id
							$catid = $this->_findCategoryId($category);
								
							if ($catid) {
								// Store the category in the database
								$this->_vm_category->coupon_id = $this->_coupons->id;
								$this->_vm_category->category_id = $catid;
								$this->_vm_category->store();
								$this->_vm_category->reset();
							}
						}
					}
					break;
				case 'product':
					// Products
					if (isset($this->product_sku)) {
						// Clean out any existing links
						$this->_vm_product->clean($this->_coupons->id);
						// Explode the SKUs
						$skus = explode('|', $this->product_sku);
						foreach ($skus as $sku) {
							// Find the product ID
							$product_id = $this->_findProductId($sku);
								
							if ($product_id) {
								// Store the category in the database
								$this->_vm_product->coupon_id = $this->_coupons->id;
								$this->_vm_product->product_id = $product_id;
								$this->_vm_product->store();
								$this->_vm_product->reset();
							}
						}
					}
					break;
				case 'manufacturer':
					// Manufacturers
					if (isset($this->manufacturer_name)) {
						// Clean out any existing links
						$this->_vm_manufacturer->clean($this->_coupons->id);
						// Explode the SKUs
						$manufacturers = explode('|', $this->manufacturer_name);
						foreach ($manufacturers as $manufacturer) {
							// Find the product ID
							$mf_id = $this->_findManufacturerId($manufacturer);
					
							if ($mf_id) {
								// Store the category in the database
								$this->_vm_manufacturer->coupon_id = $this->_coupons->id;
								$this->_vm_manufacturer->manufacturer_id = $mf_id;
								$this->_vm_manufacturer->store();
								$this->_vm_manufacturer->reset();
							}
						}
					}
					break;
			}
			
			// Add user types
			switch ($this->_coupons->user_type) {
				case 'user':
					// Users
					if (isset($this->username)) {
						// Clean out any existing links
						$this->_vm_user->clean($this->_coupons->id);
						// Explode the SKUs
						$names = explode('|', $this->username);
						foreach ($names as $name) {
							// Find the product ID
							$user_id = $this->_findUserId($name);
					
							if ($user_id) {
								// Store the category in the database
								$this->_vm_user->coupon_id = $this->_coupons->id;
								$this->_vm_user->user_id = $user_id;
								$this->_vm_user->store();
								$this->_vm_user->reset();
							}
						}
					}
					break;
				case 'usergroup':
					// Users
					if (isset($this->shoppergroup)) {
						// Clean out any existing links
						$this->_vm_usergroup->clean($this->_coupons->id);
						// Explode the SKUs
						$names = explode('|', $this->shoppergroup);
						foreach ($names as $name) {
							// Find the product ID
							$usergroup_id = $this->_findUsergroupId($name);
								
							if ($usergroup_id) {
								// Store the category in the database
								$this->_vm_usergroup->coupon_id = $this->_coupons->id;
								$this->_vm_usergroup->shopper_group_id = $usergroup_id;
								$this->_vm_usergroup->store();
								$this->_vm_usergroup->reset();
							}
						}
					}
					break;
			}
		}
		else $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_COUPON_NOT_ADDED', $this->_coupons->getError()));

		// Store the debug message
		$csvilog->addDebug(JText::_('COM_CSVI_COUPON_QUERY'), true);

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
		$this->_coupons = $this->getTable('coupons');
		$this->_vm_category = $this->getTable('vm_category');
		$this->_vm_product = $this->getTable('vm_product');
		$this->_vm_manufacturer = $this->getTable('vm_manufacturer');
		$this->_vm_user = $this->getTable('vm_user');
		$this->_vm_usergroup = $this->getTable('vm_usergroup');
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
		$this->_coupons->reset();
		$this->_vm_category->reset();
		$this->_vm_product->reset();
		$this->_vm_manufacturer->reset();
		$this->_vm_user->reset();
		$this->_vm_usergroup->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}

	/**
	 * Get the category ID based on path
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$category_path	the category path
	 * @return 		int	the category ID
	 * @since 		4.2
	 */
	private function _findCategoryId($category_path) {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);

		// Load the category separator
		if (is_null($this->_catsep)) {
			$this->_catsep = $template->get('category_separator', 'general', '/');
		}

		$csvilog->addDebug('Checking category path: '.$category_path);

		// Explode slash delimited category tree into array
		$category_list = explode($this->_catsep, $category_path);
		$category_count = count($category_list);

		$category_parent_id = '0';

		// For each category in array
		for($i = 0; $i < $category_count; $i++) {
			// Check the cache first
			if (array_key_exists($category_parent_id.'.'.$category_list[$i], $this->_category_cache)) {
				$category_id = $this->_category_cache[$category_parent_id.'.'.$category_list[$i]];
			}
			else {
				// See if this category exists with it's parent in xref
				$lang = $template->get('language', 'general');
				$query = $db->getQuery(true);
				$query->select('c.virtuemart_category_id');
				$query->from('#__virtuemart_categories c');
				$query->leftJoin('#__virtuemart_category_categories x ON c.virtuemart_category_id = x.category_child_id');
				$query->leftJoin('#__virtuemart_categories_'.$lang.' l ON l.virtuemart_category_id = c.virtuemart_category_id');
				$query->where('l.category_name = '.$db->Quote($category_list[$i]));
				$query->where('x.category_child_id = c.virtuemart_category_id');
				$query->where('x.category_parent_id = '.$category_parent_id);
				$db->setQuery($query);
				$category_id = $db->loadResult();
				$csvilog->addDebug(JText::_('COM_CSVI_CHECK_CATEGORY_EXISTS'), true);

				// Add result to cache
				$this->_category_cache[$category_parent_id.'.'.$category_list[$i]] = $category_id;
			}

			// Category does not exist - create it
			if (is_null($category_id)) {
				return false;
			}
			// Set this category as parent of next in line
			$category_parent_id = $category_id;
		}
		return $category_id;
	}
	
	/**
	 * Get the VirtueMart product ID 
	 * 
	 * @copyright 
	 * @author		RolandD 
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 
	 * @since 		4.2
	 */
	private function _findProductId($sku) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('virtuemart_product_id');
		$query->from('#__virtuemart_products');
		$query->where('product_sku = '.$db->quote($sku));
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Get the VirtueMart manufacturer ID
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param		string	$name	the name of the manufacturer
	 * @return
	 * @since 		4.2
	 */
	private function _findManufacturerId($name) {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('m.virtuemart_manufacturer_id');
		$query->from('#__virtuemart_manufacturers m');
		$query->leftJoin('#__virtuemart_manufacturers_'.$template->get('language', 'general').' ml ON ml.virtuemart_manufacturer_id = m.virtuemart_manufacturer_id');
		$query->where('ml.mf_name = '.$db->quote($name));
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Get the Joomla user ID
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param		string	$name	The username to find the ID for
	 * @return		int	the user ID
	 * @since 		4.2
	 */
	private function _findUserId($name) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__users');
		$query->where('username = '.$db->quote($name));
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Get the VirtueMart shoppergroup ID
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param		string	$name	The username to find the ID for
	 * @return		int	the user ID
	 * @since 		4.2
	 */
	private function _findUsergroupId($name) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('virtuemart_shoppergroup_id');
		$query->from('#__virtuemart_shoppergroups');
		$query->where('shopper_group_name = '.$db->quote($name));
		$db->setQuery($query);
		return $db->loadResult();
	}
}
?>