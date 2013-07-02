<?php
/**
 * Virtuemart Product Type Cross reference table
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: vm_product_product_type_xref.php 1892 2012-02-11 11:01:09Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

class TableVm_product_product_type_xref extends JTable {
	
	/**
	* @param database A database connector object
	 */
	function __construct($db) {
		parent::__construct('#__vm_product_product_type_xref', 'product_id', $db );
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
	* Store a value
	 */
	public function store() {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		if (!$this->check()) {
			$q = "INSERT INTO ".$db->nameQuote( $this->_tbl )."
				VALUES (".$db->Quote($this->product_id).", ".$db->quote($this->product_type_id).")";
			$db->setQuery($q);
			return $db->query();
		}
		else {
			$csvilog->addDebug(JText::_('COM_CSVI_CROSS_REFERENCE_EXISTS'));
		}
	}
	
	/**
	* Function to check if cross reference already exists
	 */
	public function check() {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$q = "SELECT COUNT(product_id) AS total
			FROM ".$db->nameQuote( $this->_tbl )."
			WHERE product_id = ".$db->quote($this->product_id)."
			AND product_type_id = ".$db->quote($this->product_type_id);
		$db->setQuery($q);
		$csvilog->addDebug(JText::_('COM_CSVI_PRODUCT_TYPE_XREF_CHECK'), true);
		if ($db->loadResult() > 0) return true;
		else return false;
	}
}