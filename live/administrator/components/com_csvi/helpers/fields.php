<?php
/**
 * Process fields class
 *
 * @package		CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: file.php 1994 2012-05-22 06:18:05Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * CsviFields class
 *
 * The CsviFields class handles all import/export field operations
 *
 * @package CSVI
 */
class CsviFields {
	
	private $_fields = array();
	private $_skip_default_value = false;
	/** @var object contains the ICEcat helper */
	private $_icecat = null;
	/** @var array contains the ICEcat data */
	private $_icecat_data = null;
	
	/**
	 * Class constructor 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 
	 * @since 		4.6
	 */
	public function __construct() {
		$jinput = JFactory::getApplication()->input;
		// Load the settings
		$template = $jinput->get('template', null, null);
		$this->_skip_default_value = $template->get('skip_default_value', 'general');
	}

	/**
	 * Add field
	 * 
	 * Adds a field array to the class
	 * 
	 * @copyright 
	 * @author		RolandD 
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 		$data	array	The field data to add
	 * @return 		bool	true if added | false if not added
	 * @since 		4.6
	 */	
	public function add($data) {
		if (is_array($data)) {
			// Get the name
			$fieldname = $data['name'];
			
			// Check if the name exists
			if (!isset($this->_fields[$fieldname])) {
				$_fields[$fieldname] = array(); 
			}
			
			// Add a used field to track usage
			$data['used'] = false;
			
			// Add the data
			$this->_fields[$fieldname][$data['file_field_name']] = $data;
			
			return true;
		}
		else return false;
	}
	
	/**
	 * Load a field value
	 * 
	 * Load the details of a field
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 		string	$name	The name of the field to retrieve
	 * @return 		string	if field is found | null if field is not found
	 * @since 		5.0
	 */
	public function get($name) {
		// Check if the field exists
		foreach ($this->_fields as $fieldname => $fields) {
			foreach ($fields as $key => $field) {
				// See if the name matches the field
				// Combine: $name == $fieldname
				// Add the same field into multiple destination fields: $name == $field['template_field_name']
				if ($name == $fieldname || $name == $field['template_field_name']) {
					return $this->_validateInput($field);
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Reset the used fields 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 
	 * @since 		4.6
	 */
	public function reset() {
		foreach ($this->_fields as $name => $fields) {
			foreach ($fields as $key => $field) {
				$this->_fields[$name][$key]['used'] = false;
				$this->_fields[$name][$key]['value'] = null;
			}
		}
	}
	
	/**
	 * Check if the fieldname exists 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 		string	$name	The name of the field to check
	 * @return 		bool	true if the fieldname is found | false if the fieldname is not found
	 * @since 		4.6
	 */
	public function valid($name) {
		foreach ($this->_fields as $fields) {
			if (array_key_exists($name, $fields)) {
				return true;
			}
		}
		
		// Nothing found
		return false;
	}
	
	/**
	 * Validate based on fieldname
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		string	$name	The name of the field to check
	 * @return 		string	the value found for the field
	 * @since 		4.6
	 */
	public function validateField($name) {
		foreach ($this->_fields as $fields) {
			if (array_key_exists($name, $fields)) {
				$value = $this->_validateInput($fields[$name]);
			}
		}
	
		// Nothing found
		return false;
	}
	
	/**
	 * Set a value on a field 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 		$key	string	The name of the field to add the data to
	 * @param		$value	string	The data to add to the field
	 * @return 
	 * @since 		4.6
	 */
	public function set($key, $value) {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		
		foreach ($this->_fields as $name => $fields) {
			foreach ($fields as $field => $details) {
				if ($details['file_field_name'] == $key && empty($details['value'])) {
					// Set the value
					if (empty($value)) $value = $this->_fields[$name][$field]['default_value'];
					$this->_fields[$name][$field]['value'] = $value;
					
					// Return as we are done
					return true;
				}
			}
		}
	}
	
	/**
	 * Create import data 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 
	 * @since 		4.6
	 */
	public function prepareData() {
		// Validate the fields
		foreach ($this->_fields as $name => $fields) {
			foreach ($fields as $field => $details) {
				if ($name !== 'skip' && $details['published']) {
					$datafield = $this->_validateInput($details);
					if ($datafield !== false) {
						// Set the new value
						$this->_fields[$name][$field]['value'] = $datafield;
					}
				}
			}
		}
		
		// Combine the fields
		foreach ($this->_fields as $name => $fields) {
			foreach ($fields as $field => $details) {
				if ($name !== 'skip' && $details['published']) {
					if (is_array($details['combine']) && !empty($details['combine'])) {
						foreach ($details['combine'] as $field_id) {
							if ($field_id > 0) $this->_fields[$name][$field]['value'] .= $details['combine_char'].$this->get('combine_'.$field_id);
						}
					}
				}
			}
		}
	}
	
	/**
	 * Get a list of fieldnames being processed 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 		array	List of processed fieldnames
	 * @since 		5.0
	 */
	public function getFieldnames() {
		return array_keys($this->_fields);
	}
	
	/**
	 * Validate input data
	 *
	 * Checks if the field has a value, if not check if the user wants us to
	 * use the default value
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param 		array 	$details 	the list of field details
	 * @return		true returns validated value | return false if the column count does not match
	 * @since
	 */
	private function _validateInput($details) {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$newvalue = '';
		$fieldname = $details['file_field_name'];
		
		// Check if the user wants ICEcat data
		if ($template->get('use_icecat', 'product', false, 'bool') && !empty($this->_icecat_data) && (array_key_exists($fieldname, $this->_icecat_data))) {
			$csvilog->addDebug(JText::sprintf('COM_CSVI_USE_ICECAT_FIELD', $fieldname));
			$newvalue = $this->_icecat_data[$fieldname];
		}
		else if (!empty($details)) {
			// Check if the field has a value
			if (isset($details['value']) && strlen($details['value']) > 0) {
				$csvilog->addDebug(JText::_('COM_CSVI_USE_FIELD_VALUE'));
				$newvalue = trim($details['value']);
			}
			// Field has no value, check if we need to take it from another field
			else if (!empty($details['template_field_name'])) {
				$newvalue = $this->_fields[$details['template_field_name']][$details['file_field_name']]['value'];
			}
			// Field has no value, check if we can use default value
			else if (!$this->_skip_default_value) {
				$csvilog->addDebug(JText::_('COM_CSVI_USE_DEFAULT_VALUE'));
				$newvalue = $details['default_value'];
			}
			else {
				$csvilog->addDebug(JText::_('COM_CSVI_USE_NO_VALUE'));
				return '';
			}
		}
		else return false;
	
		// Replace the value and return
		if (!empty($newvalue) && !empty($details['replace'])) return CsviHelper::replaceValue($details['replace'], $newvalue);
		else return $newvalue;
	}
	
	/**
	 * Get the data to process by the model 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 		array	array of fields and data
	 * @since 		4.6
	 */
	public function getData() {
		// Remove the skip and combine fields
		$data = array();
		foreach ($this->_fields as $name => $field) {
			if ($name !== 'skip' && strpos($name, 'combine') === false) {
				$data[$name] = $field;
			}
		}
		return $data;
	}
	
	/**
	 * Load the ICEcat data for a product
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
	public function getIcecat() {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		if ($template->get('use_icecat', 'product')) {
			$csvilog = $jinput->get('csvilog', null, null);
	
			// Load the ICEcat helper
			if (is_null($this->_icecat)) $this->_icecat = new IcecatHelper();
	
			// Clean the data holder
			$this->icecat_data = null;
	
			// Check conditions
			// 1. Do we have an MPN
			$mpn = $this->get($template->get('mpn_column_name', 'product', 'product_sku'));
			if ($mpn) {
				$csvilog->addDebug(JText::sprintf('COM_CSVI_ICECAT_FOUND_REFERENCE', $mpn));
				// 2. Do we have a manufacturer name
				$mf_name = $this->get('manufacturer_name');
				$csvilog->addDebug(JText::sprintf('COM_CSVI_ICECAT_FOUND_MF_NAME', $mf_name));
				if ($mf_name) {
					// Load the ICEcat data
					$this->_icecat_data = $this->_icecat->getData($mpn, $mf_name);
				}
				else {
					$csvilog->addDebug(JText::_('COM_CSVI_ICECAT_NO_MANUFACTURER'));
					return false;
				}
			}
			else {
				$csvilog->addDebug(JText::_('COM_CSVI_ICECAT_NO_REFERENCE'));
				return false;
			}
		}
		return false;
	
	}
}
?>