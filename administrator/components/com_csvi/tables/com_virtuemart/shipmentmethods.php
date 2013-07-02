<?php
/**
 * Virtuemart Shipment methods table
 *
 * @package		CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: coupons.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

/**
 * @package CSVI
 */
class TableShipmentmethods extends JTable {

	/**
	 * Table constructor
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
	public function __construct($db) {
		parent::__construct('#__virtuemart_shipmentmethods', 'virtuemart_shipmentmethod_id', $db );
	}

	/**
	 * Check if a shipment method already exists
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
	public function check() {
		if (isset($this->virtuemart_shipmentmethod_id)) return true;
		else {
			$jinput = JFactory::getApplication()->input;
			$template = $jinput->get('template', null, null);
			$csvilog = $jinput->get('csvilog', null, null);
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($this->_tbl_key);
			$query->from('#__virtuemart_shipmentmethods_'.$template->get('language', 'general'));
			$query->where('shipment_name ='.$db->Quote($this->shipment_name));
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_CHECK_SHIPMENTMETHOD_EXISTS'), true);
			$db->query();
			if ($db->getAffectedRows() > 0) {
				$this->virtuemart_shipmentmethod_id = $db->loadResult();
			}
		}
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
}
?>
