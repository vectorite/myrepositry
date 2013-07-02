<?php
/**
 * Virtuemart Product Type table
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: vm_product_type_x.php 1892 2012-02-11 11:01:09Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

class TableVm_product_type_x extends JTable {
	
	/**
	* @param database A database connector object
	 */
	function __construct($db) {
		parent::__construct('#__vm_product_type', 'product_id', $db );
	}
	
	/**
	 * Reset the keys including primary key
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
	public function reset() {
		// Get the default values for the class from the table.
		foreach ($this->getFields() as $k => $v) {
			// If the property is not private, reset it.
			if (strpos($k, '_') !== 0) {
				$this->$k = NULL;
			}
		}
	}
	
	/**
	 * Store the product type names 
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
	public function store() {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		
		// Check if the product type ID already exists
		$query = $db->getQuery(true);
		$query->select('COUNT(product_id) AS products')->from($this->_tbl)->where('product_id = '.$this->product_id);
		$db->setQuery($query);
		$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_CHECK_PRODUCT_TYPE_ID_EXISTS'), true);
		$product_type_exists = $db->loadResult();
		
		// Variable used for reporting query type
		if ($product_type_exists > 0) {
			$q = "UPDATE ".$this->_tbl." ";
			$q .= "SET ";
			
			foreach ($this->details as $colname => $value) {
				$colname = strtolower($colname);
				$q .= $db->nameQuote($colname)." = ".$db->quote($value).",";
			}
			$q = substr($q, 0, -1)." ";
			$q .= "WHERE product_id = ".$this->product_id;
			$action = 'updated';
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_UPDATING_NEW_PRODUCT_TYPE_DETAILS'));
		}	
		else {
			$q = "INSERT INTO ".$this->_tbl." ";
			$q .= "(";
			$qfields = $db->nameQuote('product_id').',';
			$qvalues = $db->quote($this->product_id).',';
			
			foreach ($this->details as $colname => $value) {
				$colname = strtolower($colname);
				$qfields .= $db->nameQuote($colname).',';
				$qvalues .= $db->quote($value).',';
				
			}
			$q .= substr($qfields, 0, -1);
			$q .= ") VALUES (";
			$q .= substr($qvalues, 0, -1);
			$q .= ")";
			$action = 'added';
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_ADDING_NEW_PRODUCT_TYPE_DETAILS'));
		}
		$db->setQuery($q);
		if ($db->query()) {
			$csvilog->addDebug(JText::sprintf('COM_CSVI_UPDATE_PRODUCT_TYPE_X', $this->product_type_id), true);
			$csvilog->AddStats($action, JText::sprintf('COM_CSVI_PRODUCT_TYPE_DETAIL_SKU_ID', $this->identify));
		}
		else {
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_PRODUCT_TYPE_NAMES_STORE_FAILED'), true);
			$csvilog->AddStats('incorrect', JText::_('COM_CSVI_PRODUCT_TYPE_NAMES_NOT_STORED'));
		}
	}
}