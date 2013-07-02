<?php
/**
 * EZ Realty properties table
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
class TableProperties extends JTable {

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
		parent::__construct('#__ezrealty', 'id', $db );
	}

	/**
	 * Check if a property already exists
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
		if (empty($this->id)) return false;
		else {
			$jinput = JFactory::getApplication()->input;
			$csvilog = $jinput->get('csvilog', null, null);
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from($this->_tbl);
			$query->where('id ='.$this->id);
			$db->setQuery($query);
			$record = $db->loadResult();
			$csvilog->addDebug('COM_CSVI_CHECK_PROPERTY_EXISTS', true);
			if ($record > 0) return true;
			else return false;
		}
	}
	
	/**
	 * Store a property
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
	public function store($updateNulls = false) {
		// Initialise variables.
		$k = $this->_tbl_key;
		
		// If a primary key exists update the object, otherwise insert it.
		if ($this->check()) {
			$stored = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
		}
		else {
			$stored = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
		}
		
		// If the store failed return false.
		if (!$stored) {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}
		
		return true;
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
