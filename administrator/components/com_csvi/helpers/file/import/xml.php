<?php
/**
 * XML file processor class
 *
 * @package		CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: xml.php 2061 2012-08-07 09:20:26Z RolandD $
 */

defined('_JEXEC') or die;

class XmlFile extends CsviFile {

	/** @var bool  Indicates whether a record from an XML file is ready for extraction */
	private $_xml_cache = false;

	/** @var array  Contains the data found in the latest XML record read */
	private $_xml_data = array();

	/** @var array  Contains the list of fields found in the latest XML record read */
	private $_xml_fields = array();

	/** @var array  Contains details extracted from the XML file map */
	private $_xml_schema = array();

	/** @var array  Contains the list of valid record types (node name) in the input XML file */
	private $_xml_records = array();
	
	private $_record_name = '';

	/** @var integer Internal line pointer */
	public $linepointer = 0;

	/**
	 * Construct the class and its settings
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Load the column headers from a file
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		bool	true if we loaded the column header | false if the column headers are not loaded
	 * @since 		3.0
	 */
	public function loadColumnHeaders() {
		$jinput = JFactory::getApplication()->input;
		$columnheaders = array();
		$continue = true;
		$line = 0;
		// Start reading the XML file
		while ($this->data->read()) {
			// Only read the chosen records
			if ($this->data->nodeType == XMLREADER::ELEMENT && $this->data->name == $this->_record_name && $continue) {
				// Start reading the record
				while ($this->data->read() && $continue) {
					switch ($this->data->nodeType) {
						case (XMLREADER::ELEMENT):
							// Check if it has attributes
							if ($this->data->hasAttributes) {
								$parent[] = $this->data->name;
								// Get the attributes
								while ($this->data->moveToNextAttribute()) {
									// The attribute name
									if (empty($parent)) $field_name = $this->data->name;
									else $field_name = implode('/', $parent).'/'.$this->data->name;
									
									$columnheaders[] = $field_name;
								}
							}
							else if (!$this->data->isEmptyElement) $parent[] = $this->data->name;
							break;
						case (XMLREADER::END_ELEMENT) :
							$line++;
							array_pop($parent);
							if ($this->data->name == $this->_record_name) {
								$continue = false;
							}
							break;
						case XMLReader::TEXT:
						case XMLReader::CDATA:
							// The field name
							if (empty($parent)) $field_name = $this->data->name;
							else $field_name = implode('/', $parent);
								
							$columnheaders[] = $field_name;
							break;
					}
				}
			}
			else if (!$continue) {
				break;
			}
		}
		
		// Set the column headers
		$jinput->set('columnheaders', $columnheaders);
		
		// Reset the internal pointer
		$this->rewind();
		
		return true;
	}

	/**
	 * Get the file position
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		int	current position in the file
	 * @since 		3.0
	 */
	public function getFilePos() {
		return $this->linepointer;
	}

	/**
	 * Set the file position
	 *
	 * To be able to set the file position correctly, the XML reader needs to be at the start of the file
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		int	$pos	the position to move to
	 * @return 		int	current position in the file
	 * @since 		3.0
	 */
	public function setFilePos($pos) {
		// Close the XML reader
		$this->closeFile(false);
		// Open a new XML reader
		$this->processFile();
		// Move the pointer to the specified position
		return $this->_skipXmlRecords($pos);
	}

	/**
	 * Close the file
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
	public function closeFile($removefolder=true) {
		$this->data->close();
		$this->_xml_cache = false;
		$this->_closed = true;
		parent::closeFile($removefolder);
	}

	/**
	 * Read the next line in the file
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		array	with the line of data read | false if data cannot be read
	 * @since 		3.0
	 */
	public function readNextLine() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$csvifields = $jinput->get('csvifields', null, null);
		$parent = array();
		
		// Start reading the XML file
		while ($this->data->read()) {
			// Only read the chosen records
			if ($this->data->nodeType == XMLREADER::ELEMENT && $this->data->name == $this->_record_name) {
				// Start reading the record
				while ($this->data->read()) {
					switch ($this->data->nodeType) {
						case (XMLREADER::ELEMENT):
							// Check if it has attributes
							if ($this->data->hasAttributes) {
								$parent[] = $this->data->name;
								// Get the attributes
								while ($this->data->moveToNextAttribute()) {
									// The attribute name
									if (empty($parent)) $field_name = $this->data->name;
									else $field_name = implode('/', $parent).'/'.$this->data->name;
									
									// The attribute value
									$field_value = $this->data->value;
									
									// Add the field to the list of data
									if ($csvifields->valid(strtolower($field_name))) {
										$csvifields->set($field_name, $field_value);
									}
								}
							}
							else if (!$this->data->isEmptyElement) $parent[] = $this->data->name;
							break;
						case (XMLREADER::END_ELEMENT) :
							array_pop($parent);
							if ($this->data->name == $this->_record_name) {
								$this->linepointer++;
								return true;
							}
							break;
						case XMLReader::TEXT:
						case XMLReader::CDATA:
							// The field name
							if (empty($parent)) $field_name = $this->data->name;
							else $field_name = implode('/', $parent);
							
							// The field value
							$field_value =  $this->data->value;
							
							// Add the field to the list of data
							if ($csvifields->valid($field_name)) {
								$csvifields->set($field_name, $field_value);
							}
							break;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Process the file to import
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function processFile() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		
		// Use a streaming approach to support large files
		$this->data = new XMLReader();
		$this->fp = $this->data->open($this->filename);
		if ($this->fp == false) {
			$csvilog->AddStats('incorrect', JText::_('COM_CSVI_ERROR_XML_READING_FILE'));
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_CSVI_ERROR_XML_READING_FILE'), 'error');
			return false;
		}
		
		// Set the record name
		$this->_record_name = $template->get('xml_record_name', 'general');
		
		return true;
	}
	
	
	/**
	 * Sets the file pointer back to beginning
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
	public function rewind() {
		$this->linepointer = 0;
		$this->data->close();
		$this->processFile();
	}
	
	/**
	 * Advances the file pointer 1 forward
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		bool	$preview	True if called from the preview
	 * @return
	 * @since		3.0
	 */
	public function next($preview=false) {
		if (!$preview) $discard = $this->readNextLine();
	}
	
	/**
	 * Skips through the XML file until the the required number 'record' nodes has been read
	 * Assume the file pointer is at the start of file
	 *
	 * @copyright
	 * @author		doorknob, RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return 		boolean	true if records are skipped | false if records are not skipped
	 * @since 		3.0
	 */
	private function _skipXmlRecords($pos) {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$csvilog->addDebug('Forwarding to position: '.$pos);
		// Check whether the pointer needs to be moved
		if ($pos <= 0) return true;
	
		$count = 0;
		
		while ($this->data->read()) {
			// Searching for a valid record - must be the start of a node and in the list of valid record types
			if ($this->data->nodeType == XMLREADER::ELEMENT && $this->data->name == $this->_record_name) {
				// Found a valid record
				while ($this->data->nodeType == XMLREADER::ELEMENT && $this->data->name == $this->_record_name) {
					// Node is a valid record type - skip to the end of the record
					$this->data->next();
					$count++;
					if( $count == $pos) {
						return true;
					}
				}
			}
			else {
				// Not found - try again
				continue;
			}
		}
		// Hit EOF before skipping the required number of records
		return false;
	}
}
?>
