<?php
/**
 * Virtuemart Product Type Parameter table
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: vm_product_type_parameter.php 1892 2012-02-11 11:01:09Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

class TableVm_product_type_parameter extends JTable {
	
	// Private variable declaration
	private $db_change = null;
	
	/**
	 * @param database A database connector object
	 */
	function __construct($db) {
		parent::__construct('#__vm_product_type_parameter', 'product_type_id', $db );
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
	* Stores a product type parameter
	*
	* In addition to storing the product type parameter the concerned table
	* is also updated. This table is #__vm_product_type_<id>. Further an index
	* is created for the new parameter type.
	*
	* @todo Add more logging
	* @todo Error checking
	 */
	public function store() {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$dbfields = CsviModelAvailablefields::DbFields('vm_product_type_parameter');
		
		// Check if it will be an update or an add
		$k = $this->check();
		
		// Execute the update
		if($k) {
			$q = "UPDATE ".$this->_tbl." SET ";
			foreach ($dbfields as $name => $details) {
				if (!empty($this->$name)
					&& $name != 'product_type_id'
					&& $name != 'paramater_name') {
					$q .= $db->nameQuote($name)." = ".$db->quote($this->$name).', ';
				}
			}
			$q = substr($q, 0, -2);
			$q .= " WHERE product_type_id = ".$this->product_type_id."
				AND parameter_name = ".$db->quote($this->parameter_name);
			$db->setQuery($q);
			$ret = $db->query();
			$csvilog->addDebug(JText::_('COM_CSVI_UPDATE_PRODUCT_TYPE_PARAMETER'), true);
			if ($this->parameter_type != "B") {
				$this->ColumnUpdate();
				$this->CreateIndex();
			}
		}
		// Execute the add
		else {
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
			$csvilog->addDebug('COM_CSVI_INSERT_PRODUCT_TYPE_PARAMETER', true);
			if ($this->parameter_type != "B") {
				$this->ColumnAdd();
				$this->CreateIndex();
			}
		}
		
		// Check the result
		if(!$ret) {
			$csvilog->AddStats('incorrect', JText::_('COM_CSVI_PRODUCT_TYPE_PARAMETER_STORE_FAILED'));
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		else {
			$csvilog->AddStats($this->db_change, JText::_('COM_CSVI_PRODUCT_TYPE_PARAMETER_STORE_SUCCESS'));
			return true;
		}
	}
	
	/**
	* Check if a relation already exists
	 */
	public function check() {
		$db = JFactory::getDbo();
		$q = "SELECT COUNT(".$this->_tbl_key.") AS total
			FROM ".$this->_tbl."
			WHERE product_type_id = ".$this->product_type_id."
			AND parameter_name = ".$db->quote($this->parameter_name);
		$db->setQuery($q);
		$result = $db->loadResult();
		
		if ($result > 0) {
			
			$this->db_change = 'updated';
			return true;
		}
		else {
			$this->db_change = 'added';
			return false;
		}
	}
	
	/**
	* Function to drop an index
	 */
	private function DropIndex() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$db = JFactory::getDbo();
		$q  = "ALTER TABLE `#__vm_product_type_";
		$q .= $this->product_type_id."` DROP INDEX `idx_product_type_".$this->product_type_id."_";
		$q .= $this->parameter_name."`;";
		$db->setQuery($q);
		$csvilog->addDebug('COM_CSVI_DROP_PRODUCT_TYPE_PARAMETER_INDEX', true);
		$db->query();
	}
	
	/**
	* Update column settings for table product_type_<id>
	 */
	private function ColumnUpdate() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$db = JFactory::getDbo();
		$q  = "ALTER TABLE `#__vm_product_type_";
		$q .= $this->product_type_id . "` MODIFY COLUMN `";
		$q .= $this->parameter_name."` ";
		$q .= $this->DbFieldType();
		if ($this->parameter_default != "" && $this->parameter_type != "T") {
			$q .= "DEFAULT '".$this->parameter_default."' NOT NULL;";
		}
		$db->setQuery($q);
		$csvilog->addDebug('COM_CSVI_MODIFY_PRODUCT_TYPE_PARAMETER_COLUMN', true);
		$db->query();
	}
	
	/**
	* Add a column for table product_type_<id>
	 */
	private function ColumnAdd() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$db = JFactory::getDbo();
		$q = "ALTER TABLE `#__vm_product_type_";
		$q .= $this->product_type_id . "` ADD `";
		$q .= $this->parameter_name."` ";
		$q .= $this->DbFieldType();
		if ($this->parameter_default != "" && $this->parameter_type != "T") {
			$q .= "DEFAULT '".$this->parameter_default."' NOT NULL;";
		}
		$db->setQuery($q);
		$csvilog->addDebug('COM_CSVI_ADD_PRODUCT_TYPE_PARAMETER_COLUMN', true);
		$db->query();
	}
	
	/**
	* Get paramter type for field type
	 */
	private function DbFieldType() {
		switch( $this->parameter_type ) {
			// Integer
			case "I": return "int(11) "; break;
			// Text
			case "T": return "text "; break;
			// Float
			case "F": return "float "; break;
			// Char
			case "C": return "char(1) "; break;
			// Date time
			case "D": return "datetime "; break;
			// Date
			case "A": return "date "; break;
			// Time
			case "M": return "time "; break;
			// Short Text
			case "S":
			// Multiple Values
			case "V":
			default: 
				return "varchar(255) ";
				break;
		}
	}
	
	/**
	* Create an index for the field
	 */
	private function CreateIndex() {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		
		// Load current indexes
		$q = "SHOW INDEX FROM #__vm_product_type_".$this->product_type_id;
		$db->setQuery($q);
		$indexes = $db->loadObjectList('Key_name');
		
		if (!isset($indexes['idx_product_type_'.$this->product_type_id.'_'.$this->parameter_name])) {
			if ($this->parameter_type == "T") {
				$q  = "ALTER TABLE `#__vm_product_type_";
				$q .= $this->product_type_id."` ADD FULLTEXT `idx_product_type_".$this->product_type_id."_";
				$q .= $this->parameter_name."` (`".$this->parameter_name."`);";
				$db->setQuery($q);
				$db->query();
			}
			else {
				$q  = "ALTER TABLE `#__vm_product_type_";
				$q .= $this->product_type_id."` ADD KEY `idx_product_type_".$this->product_type_id."_";
				$q .= $this->parameter_name."` (`".$this->parameter_name."`);";
				$db->setQuery($q);
				$db->query();
			}
			$csvilog->addDebug(JText::_('COM_CSVI_CREATE_PRODUCT_TYPE_PARAMETER_INDEX'), true);
		}
	}
	
	/**
	* Deletes a product type parameter and its associated data
	 */
	public function delete() {
		// Delete the entry from the #__vm_product_type_parameter
		if ($this->DeleteParam()) {
			// Delete the column form the #__vm_product_type_<id>
			if ($this->DeleteProductTypeParam()) return true;
			else return false;
		}
		else return false;
	}
	
	/**
	* Delete a product type parameter from the #__vm_product_type_parameter table
	 */
	private function DeleteParam() {
		$db = JFactory::getDbo();
		$k = $this->_tbl_key;
		$q = 'DELETE FROM '.$db->nameQuote( $this->_tbl ).
			' WHERE '.$this->_tbl_key.' = '. $db->quote($this->$k).'
			AND parameter_name = '.$db->quote($this->parameter_name);
		$db->setQuery($q);
		return $db->query();
	}
	
	/**
	* Delete a product type parameter from the #__vm_product_type_<id> table
	 */
	private function DeleteProductTypeParam() {
		$db = JFactory::getDbo();
		$q = 'ALTER TABLE '.$db->nameQuote( '#__vm_product_type_'.$this->product_type_id ).
			' DROP COLUMN '.$db->nameQuote($this->parameter_name);
		$db->setQuery($q);
		return $db->query();
	}
}