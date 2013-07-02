<?php
/**
 * Module VM Cherry Picker helper file
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: com_akeebasubs.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die;

/**
 * Module VM Cherry Picker helper file
 *
 * @package CSVI
 */
class Mod_Vm_Cherry_Picker {

	private $_csvidata = null;

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
	 * @since 		4.0
	 */
	public function __construct() {
		$jinput = JFactory::getApplication()->input;
		$this->_csvidata = $jinput->get('csvifields', null, null);
	}

	/**
	 * Get the product type ID, cannot do without it
	 * 
	 * The product_type_id is not auto incremental, therefore it needs to be
	 * set manually
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 
	 * @since 		5.1
	 */
	public function getProductTypeId($product_type_name) {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$db = JFactory::getDbo();
	
		$product_type_id = $this->_csvidata->get('product_type_id');
		
		if (!$product_type_id) {
			$query = $db->getQuery(true);
			$query->select('product_type_id')->from($db->nameQuote('#__vm_product_type'))->where('product_type_name = '.$db->quote($product_type_name));
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_FIND_PRODUCT_TYPE_ID', true);
			$db->query();
			return $db->loadResult();
		}
		else return false;
	}
	
}