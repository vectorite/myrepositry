<?php
/**
 * EZ Realty categories table
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
class TableCategories extends JTable {

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
		parent::__construct('#__ezrealty_catg', 'id', $db );
	}
	
	/**
	 * Check if a category exists 
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
	public function check() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		// Check if the category exists
		$query->select('id');
		$query->from('#__ezrealty_catg');
		$query->where($db->nameQuote('name').' = '.$db->quote($this->name));
		$db->setQuery($query);
		$catid = $db->loadResult();
		if ($catid > 0) {
			$this->id = $catid;
		}
		else {
			// Check the alias
			if (empty($this->alias)) {
				$this->alias = JApplication::stringURLSafe($this->name);
				if (trim(str_replace('-','',$this->alias)) == '') {
					$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
				}
			}
			
			// Check the access
			if (empty($this->access)) $this->access = 1;
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
