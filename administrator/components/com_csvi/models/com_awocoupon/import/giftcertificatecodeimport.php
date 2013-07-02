<?php
/**
 * AwoCoupon gift certificate code import
 *
 * @package 	CSVI
 * @subpackage 	Import
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: couponimport.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die;

/**
 * Main processor for importing waitinglists
 *
 * @package CSVI
 */
class CsviModelGiftcertificatecodeimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the vm_coupons table */
	private $_giftcert_code = null;
	
	// Public variables
	/** @var integer contains the gift certifcate ID */
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
	 * @since 		4.3
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

		// Check the product SKU field
		if (isset($this->product_sku)) $this->product_id = $this->_findProductId();
		
		if (!empty($this->product_id) || !empty($this->id)) {
			// Bind the data
			$this->_giftcert_code->bind($this);
	
			// Check the data
			$this->_giftcert_code->check();
	
			// Store the data
			if ($this->_giftcert_code->store()) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_GIFTCERTIFICATE_CODE'));
				else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_GIFTCERTIFICATE_CODE'));
			}
			else $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_GIFTCERTIFICATE_CODE_NOT_ADDED', $this->_giftcert_code->getError()));
		}
		else $csvilog->AddStats('incorrect', 'COM_CSVI_GIFTCERTIFICATE_MISSING_FIELDS');

		// Store the debug message
		$csvilog->addDebug(JText::_('COM_CSVI_GIFTCERTIFICATE_CODE_QUERY'), true);

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
		$this->_giftcert_code = $this->getTable('giftcert_code');
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
		$this->_giftcert_code->reset();
	
		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
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
	 * @since 		4.3
	 */
	private function _findProductId() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('virtuemart_product_id');
		$query->from('#__virtuemart_products');
		$query->where('product_sku = '.$db->quote($this->product_sku));
		$db->setQuery($query);
		return $db->loadResult();
	}
}
?>