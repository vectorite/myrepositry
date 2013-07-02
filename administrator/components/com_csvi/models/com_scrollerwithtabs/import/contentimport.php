<?php
/**
 * Scroller with Tabs content import
 *
 * @package 	CSVI
 * @subpackage 	Import
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: couponimport.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Main processor for importing content for scroller with tabs
 *
 * @package CSVI
 */
class CsviModelContentimport extends CsviModelImportfile {

	// Private tables
	private $_content = null;
	
	// Public variables
	public $id = null;

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
		// Load the data
		$this->loadData();
		
		// Load the helper
		$this->config = new CsviCom_Scrollerwithtabs_Config();

		// Get the logger
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);

		// Process data
		foreach ($this->csvi_data as $name => $fields) {
			foreach ($fields as $filefieldname => $details) {
				$value = $details['value'];
				// Check if the field needs extra treatment
				switch ($name) {
					default:
						$this->$name = $value;
						break;
				}
			}
		}

		// All good
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
		
		// Check if all fields have enough width
		$cols = $this->config->get('col_num') - 1;
		if (isset($this->title)) {
			$count = substr_count($this->title, ';');
			$diff = $cols - $count;
			if ($diff > 0) $this->title .= str_repeat(';', $diff);
		}
		
		if (isset($this->content)) {
			$count = substr_count($this->content, ';');
			$diff = $cols - $count;
			if ($diff > 0) $this->content .= str_repeat(';', $diff);
		}
		
		if (isset($this->fontsize)) {
			$count = substr_count($this->fontsize, ';');
			$diff = $cols - $count;
			if ($diff > 0) $this->fontsize .= str_repeat(';', $diff);
		}
		
		if (isset($this->color)) {
			$count = substr_count($this->color, ';');
			$diff = $cols - $count;
			if ($diff > 0) $this->color .= str_repeat(';', $diff);
		}
		
		if (isset($this->comment)) {
			$count = substr_count($this->comment, ';');
			$diff = $cols - $count;
			if ($diff > 0) $this->comment .= str_repeat(';', $diff);
		}
		
		// Bind the data
		$this->_content->bind($this);

		// Check the data
		$this->_content->check();

		// Store the data
		if ($this->_content->store()) {
			if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_CONTENT'));
			else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_CONTENT'));
		}
		else $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_CONTENT_NOT_ADDED', $this->_content->getError()));

		// Store the debug message
		$csvilog->addDebug(JText::_('COM_CSVI_CONTENT_QUERY'), true);

		// Clean the tables
		$this->cleanTables();
	}

	/**
	 * Load the coupon related tables
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.0
	 */
	private function _loadTables() {
		$this->_content = $this->getTable('content');
	}

	/**
	 * Cleaning the coupon related tables
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
	protected function cleanTables() {
		$this->_content->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}
}
?>