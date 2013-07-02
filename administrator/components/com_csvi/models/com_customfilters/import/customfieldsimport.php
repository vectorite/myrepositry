<?php
/**
 * Custom fields import
 *
 * @package 	CSVI
 * @subpackage 	Import
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customfieldsimport.php 2048 2012-07-28 16:27:43Z RolandD $
 */

defined( '_JEXEC' ) or die;

/**
 * Main processor for importing waitinglists
 *
 * @package CSVI
 */
class CsviModelCustomfieldsimport extends CsviModelImportfile {

	// Private tables
	private $_customfields = null;

	// Public variables
	public $id = null;
	public $vm_custom_id = null;
	public $type_id = null;

	/**
	 * Constructor
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.4
	 */
	public function __construct() {
		parent::__construct();
		// Load the tables that will contain the data
		$this->_loadTables();
		$this->loadSettings();
    }

	/**
	 * Here starts the processing
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
	public function getStart() {
		$jinput = JFactory::getApplication()->input;

		// Load the data
		$this->loadData();

		// Get the logger
		$csvilog = $jinput->get('csvilog', null, null);

		// Process data
		foreach ($this->csvi_data as $name => $fields) {
			foreach ($fields as $filefieldname => $details) {
				$value = $details['value'];
				// Check if the field needs extra treatment
				switch ($name) {
					case 'published':
						switch ($value) {
							case 'n':
							case 'N':
							case '0':
								$value = 0;
								break;
							default:
								$value = 1;
							break;
						}
						$this->published = $value;
						break;
					default:
						$this->$name = $value;
						break;
				}
			}
		}

		// All is good
		return true;
	}

	/**
	 * Process each record and store it in the database
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
	public function getProcessRecord() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);

		// Get the custom field ID
		if (empty($this->vm_custom_id) && !empty($this->custom_title)) {
			$this->vm_custom_id = $this->_getCustomId();
			if (empty($this->vm_custom_id)) {
				$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_NO_VM_CUSTOMID_FOUND', $this->custom_title));
				return false;
			}
		}
		
		// Get the display type
		if (empty($this->type_id) && !empty($this->display_type)) {
			$this->type_id = $this->_getDisplayType();
			if (empty($this->type_id)) {
				$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_NO_DISPLAY_TYPE_FOUND', $this->display_type));
			}
		}
		
		// Bind the data
		$this->_customfields->bind($this);

		// Check the data
		$this->_customfields->check();
		
		// Check the alias
		if (empty($this->_customfields->id) && empty($this->alias)) {
			if (!empty($this->custom_title))
				$this->_customfields->alias = JFilterOutput::stringURLUnicodeSlug($this->custom_title);
		}

		// Store the data
		if ($this->_customfields->store()) {
			if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_CUSTOMFIELD'));
			else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_CUSTOMFIELD'));
		}
		else $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_CUSTOMFIELD_NOT_ADDED', $this->_customfields->getError()));

		// Store the debug message
		$csvilog->addDebug(JText::_('COM_CSVI_CUSTOMFIELD_QUERY'), true);
		
		// Clean the tables
		$this->cleanTables();
	}

	/**
	 * Load the custom fields related tables
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.01
	 */
	private function _loadTables() {
		$this->_customfields = $this->getTable('customfields');
	}

	/**
	 * Cleaning the waiting list related tables
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return
	 * @since 		3.1
	 */
	protected function cleanTables() {
		$this->_customfields->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}
	
	/**
	 * Get the custom field ID 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		private
	 * @param 
	 * @return 		int	the custom field ID
	 * @since 		4.2
	 */
	private function _getCustomId() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('virtuemart_custom_id'));
		$query->from($db->quoteName('#__virtuemart_customs'));
		$query->where($db->quoteName('custom_title').' = '.$db->quote($this->custom_title));
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	* Get the type ID
	*
	* @copyright
	* @author 		RolandD
	* @todo
	* @see
	* @access 		private
	* @param
	* @return 		int	the type ID
	* @since 		4.2
	*/
	private function _getDisplayType() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__cf_filtertypes'));
		$query->where($db->quoteName('type').' = '.$db->quote($this->display_type));
		$db->setQuery($query);
		return $db->loadResult();
	}
}
?>