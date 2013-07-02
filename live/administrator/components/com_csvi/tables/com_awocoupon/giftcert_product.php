<?php
/**
 * AwoCoupon Gift certificate table
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
class TableGiftcert_product extends JTable {

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
		parent::__construct('#__awocoupon_vm_giftcert_product', 'id', $db );
	}

	/**
	 * Check if a coupon already exists
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
		if (isset($this->id)) return true;
		else {
			$jinput = JFactory::getApplication()->input;
			$csvilog = $jinput->get('csvilog', null, null);
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($this->_tbl_key);
			$query->from($this->_tbl);
			$query->where('product_id ='.$db->Quote($this->product_id));
			$query->where('coupon_template_id ='.$db->Quote($this->coupon_template_id));
			$query->where('profile_id ='.$db->Quote($this->profile_id));
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_CHECK_GIFTCERTIFICATE_EXISTS'), true);
			$db->query();
			if ($db->getAffectedRows() > 0) {
				$this->id = $db->loadResult();
			}
		}
	}

	/**
	 * Reset the table fields, need to do it ourselves as the fields default is not NULL
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
