<?php
/**
 * CSVI Template helper
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: template.php 2048 2012-07-28 16:27:43Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Helper class for templates
 * @package CSVI
 */
class CsviTemplate {

	/** @var object contains the form data */
	private $_settings = array();
	private $_name = null;
	private $_id = null;
	private $_combine_ids = array();

	/**
	 * Construct the template helper
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access		public
	 * @param
	 * @return
	 * @since		4.0
	 */
	public function __construct($data=null) {
		if (!is_null($data)) {
			$this->_settings = $data;
			
			// Load the fields
			if (isset($data['id'])) $this->_loadFields($data['id']);
		}
	}

	/**
	 * Find a value in the template
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo		JRequest::_cleanVar
	 * @see 		JFilterInput
	 * @access 		public
	 * @param 		string	$name		the name of the parameter to find
	 * @param 		string	$group		the group in which to find the parameter
	 * @param		string	$default	the default value to use when not found
	 * @param		string	$filter		the filter to apply
	 * @param 		int 	$mask		Filter bit mask. 1=no trim: If this flag is cleared and the
	 * 									input is a string, the string will have leading and trailing whitespace
	 * 									trimmed. 2=allow_raw: If set, no more filtering is performed, higher bits
	 * 									are ignored. 4=allow_html: HTML is allowed, but passed through a safe
	 * 									HTML filter first. If set, no more filtering is performed. If no bits
	 * 									other than the 1 bit is set, a strict filter is applied.
	 * @param		bool	$special	if the field should require special processing
	 * @return 		mixed	the value found
	 * @since 		3.0
	 */
	public function get($name, $group='', $default = '', $filter=null, $mask=0, $special=true) {
		// Set the initial value
		$value = '';

		// Find the value
		if (empty($group)) {
			if (array_key_exists($name, $this->_settings)) $value = $this->_settings[$name];
		}
		else {
			if (array_key_exists($group, $this->_settings)) {

				if (array_key_exists($name, $this->_settings[$group])) {
					$value = $this->_settings[$group][$name];
				}
			}
		}

		// Return the found value
		if (is_array($value) && empty($value)) $value = $default;
		else if ('' === $value) $value = $default;

		// Special processing
		if ($special) {
			switch ($name) {
				case 'language':
				case 'target_language':
					$value = strtolower(str_replace('-', '_', $value));
					break;
				case 'field_delimiter':
					if (strtolower($value) == 't') $value = "\t";
					break;
			}
		}
		 
		// Clean up and return
		if (is_null($filter) && $mask == 0) return $value;
		else return JRequest::_cleanVar($value, $mask, $filter);
	}

	/**
	 * Set a value in the template
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		string	$name		the name of the parameter to find
	 * @param 		string	$group		the group in which to find the parameter
	 * @param		string	$value		the value to set
	 * @return 		void
	 * @since 		3.0
	 */
	public function set($name, $group='', $value = '') {
		// Set the value
		if (empty($group)) {
			$this->_settings[$name] = $value;
		}
		else {
			$this->_settings[$group][$name] = $value;
		}
	}

	/**
	 * Load a template
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		int	$id	the ID of the template to load
	 * @return
	 * @since 		4.0
	 */
	public function load($id) {
		if ($id > 0) {
			// Set the ID
			$this->_id = $id;
			
			// Load the data
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('settings').', '.$db->quoteName('name'));
			$query->from($db->quoteName('#__csvi_template_settings'));
			$query->where($db->quoteName('id').' = '.$db->quote($id));
			$db->setQuery($query);
			$data = $db->loadObject();

			// Fill the settings
			if (is_object($data)) {
				$settings = json_decode($data->settings, true);
				if (!is_array($settings)) $settings = array();
			}
			else $settings = array();
			$this->_settings = $settings;
	   
			// Get the new fields
			$this->_loadFields($id);
			
			// Set the name
			if (!empty($data)) $this->setName($data->name);
		}
	}

	/**
	 * Set the template name
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		string	$name	the name of the template
	 * @return
	 * @since 		4.0
	 */
	public function setName($name) {
		// Set the template name
		$this->_name = $name;
	}

	/**
	 * Get the name of the template
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
	public function getName() {
		return $this->_name;
	}
	
	/**
	 * Get the ID of the template
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
	public function getId() {
		return $this->_id;
	}

	/**
	 * Return all settings
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
	public function getSettings() {
		return $this->_settings;
	}
	
	/**
	 * Load the column headers 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 		bool 	true
	 * @since 		5.0
	 */
	public function loadColumnHeaders() {
		$jinput = JFactory::getApplication()->input;
		$columnheaders = array();
		foreach ($this->_settings['fields'] as $field) {
			$columnheaders[] = ($field->field_name == 'combine') ? $fieldname = $field->field_name.'_'.$field->field_id : $field->field_name;
		}
		$jinput->set('columnheaders', $columnheaders);
		return true;
	}
	
	/**
	 * Check if a field is used for combining 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 		int		$id	The id of the field to check
	 * @return 		bool	true if used | false if not used
	 * @since 		5.0
	 */
	public function isCombine($id) {
		return in_array($id, $this->_combine_ids);
	}
	
	/**
	 * Load the fields for the template 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 		int	$id	the template ID
	 * @return 
	 * @since 		4.3
	 */
	private function _loadFields($id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('f.id').' AS field_id, '.$db->quoteName('f.file_field_name').', '.$db->quoteName('f.template_field_name').', '.$db->quoteName('f.ordering').', '.$db->quoteName('f.field_name').', '.$db->quoteName('f.column_header').', '.$db->quoteName('f.default_value').', '.$db->quoteName('f.process').', '.$db->quoteName('f.combine_char').', '.$db->quoteName('f.sort').', '.$db->quoteName('f.cdata'));
		$query->from('#__csvi_template_fields f');
		$query->where($db->quoteName('f.template_id').' = '.$db->quote($id));
		$query->order('f.ordering');
		$db->setQuery($query);
		$this->_settings['fields'] = $db->loadObjectList('field_id');

		// Get the combine values for each field
		if (isset($this->_settings['fields'])) {
			foreach ($this->_settings['fields'] as $key => $value) {
				$query = $db->getQuery(true);
				$query->select('combine_id');
				$query->from('#__csvi_template_fields_combine');
				$query->where('field_id = '.$value->field_id);
				$db->setQuery($query);
				$this->_settings['fields'][$key]->combine = $db->loadResultArray();
				// Add the IDs to the combine array
				$this->_combine_ids = array_merge($this->_combine_ids, $this->_settings['fields'][$key]->combine);
			}
		}
		// Get the replacement values for each field
		if (isset($this->_settings['fields'])) {
			foreach ($this->_settings['fields'] as $key => $value) {
				$query = $db->getQuery(true);
				$query->select('replace_id');
				$query->from('#__csvi_template_fields_replacement');
				$query->where('field_id = '.$value->field_id);
				$db->setQuery($query);
				$this->_settings['fields'][$key]->replace = $db->loadResultArray();
			}
		}
	}
}
?>
