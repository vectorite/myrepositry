<?php
/**
 * Virtuemart Product Type table
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: vm_product_type.php 1892 2012-02-11 11:01:09Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

class TableVm_product_type extends JTable {
	
	/**
	* @param database A database connector object
	 */
	function __construct($db) {
		parent::__construct('#__vm_product_type', 'product_type_id', $db );
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
	* Check if a product type already exists
	*
	* Criteria for an existing product type are:
	* - product id
	* - shopper group id
	* If both exists, price will be updated
	 */
	public function check() {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$q = "SELECT ".$this->_tbl_key."
			FROM ".$this->_tbl."
			WHERE product_type_name = ".$db->quote($this->product_type_name);
		$db->setQuery($q);
		$csvilog->addDebug(JText::_('COM_CSVI_CHECK_PRODUCT_TYPE_NAME_EXISTS'), true);
		$db->query();
		if ($db->getAffectedRows() > 0) {
			$this->product_type_id = $db->loadResult();
		}
	}
}
?>
