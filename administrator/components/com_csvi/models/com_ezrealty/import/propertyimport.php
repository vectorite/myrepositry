<?php
/**
 * Property import
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
 * Processor for properties
 *
 * Main processor for importing properties
 *
 * @package CSVI
 */
class CsviModelPropertyimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the properties table */
	private $_properties = null;
	private $_categories = null;
	private $_countries = null;
	private $_states = null;
	private $_cities = null;
	private $_images = null;

	// Public variables
	/** @var integer contains the property ID */
	public $id = null;
	public $cnid = null;
	public $stid = null;
	public $image1 = null;
	public $image2 = null;
	public $image3 = null;
	public $image4 = null;
	public $image5 = null;
	public $image6 = null;
	public $image7 = null;
	public $image8 = null;
	public $image9 = null;
	public $image10 = null;
	public $image11 = null;
	public $image12 = null;
	public $image13 = null;
	public $image14 = null;
	public $image15 = null;
	public $image16 = null;
	public $image17 = null;
	public $image18 = null;
	public $image19 = null;
	public $image20 = null;
	public $image21 = null;
	public $image22 = null;
	public $image23 = null;
	public $image24 = null;
	public $fname = null;
	public $file_title = null;
	public $file_description = null;
	public $file_ordering = null;

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

		// Get the logger
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);

		// Process data
		foreach ($this->csvi_data as $name => $fields) {
			foreach ($fields as $filefieldname => $details) {
				$value = $details['value'];
				// Check if the field needs extra treatment
				switch ($name) {
					case 'published':
					case 'featured':
					case 'pool':
					case 'fplace':
					case 'bbq':
					case 'gazebo':
					case 'lug':
					case 'bir':
					case 'heating':
					case 'airco':
					case 'shops':
					case 'schools':
					case 'elevator':
					case 'pets':
					case 'extra1':
					case 'extra2':
					case 'extra3':
					case 'extra4':
					case 'extra5':
					case 'extra6':
					case 'extra7':
					case 'extra8':
					case 'showprice':
					case 'ensuite':
					case 'openhouse':
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
						$this->$name = $value;
						break;
					case 'bond':
					case 'closeprice':
					case 'offpeak':
					case 'price':
						$this->$name = $this->cleanPrice($value);
						break;
					case 'aucdate':
					case 'availdate':
					case 'checked_out_time':
					case 'ohdate':
					case 'ohdate2':
						$this->$name = $this->convertDate($value);
						break;
					case 'listdate':
						$this->$name = $this->convertDate($value, 'date');
						break;
					case 'transaction_type':
						switch(strtolower($value)) {
							case 'for sale':
								$this->type = 1;
								break;
							case 'for rent':
								$this->type = 2;
								break;
							case 'for lease':
								$this->type = 3;
								break;
							case 'for auction':
								$this->type = 4;
								break;
							case 'property exchange':
								$this->type = 5;
								break;
							case 'sale by tender':
								$this->type = 6;
								break;
							default:
								$this->type = 1;
								break;
						}
						break;
					case 'market_status':
						switch(strtolower($value)) {
							case 'available':
								$this->sold = 1;
								break;
							case 'under offer':
								$this->sold = 2;
								break;
							case 'subject to contract':
								$this->sold = 3;
								break;
							case 'under contract':
								$this->sold = 4;
								break;
							case 'sold':
								$this->sold = 5;
								break;
							case 'leased':
								$this->sold = 6;
								break;
							case 'unavailable':
								$this->sold = 7;
								break;
							case 'closed':
								$this->sold = 8;
								break;
							case 'withdrawn':
								$this->sold = 9;
								break;
							case 'off market':
								$this->sold = 10;
								break;
							default:
								$this->sold = 1;
								break;
						}
						break;
					case 'furnished':
						switch(strtolower($value)) {
							case 'not applicable':
								$this->furnished = 1;
								break;
							case 'furnished':
								$this->furnished = 2;
								break;
							case 'partly furnished':
								$this->furnished = 3;
								break;
							case 'unfurnished':
								$this->furnished = 4;
								break;
							default:
								$this->furnished = 1;
								break;
						}
						break;
					case 'bedrooms':
						switch(strtolower($value)) {
							case 'property has no bedrooms':
								$this->bedrooms = 0;
								break;
							case 'studio':
								$this->bedrooms = -1;
								break;
							case '1':
								$this->bedrooms = 1;
								break;
							case '2':
								$this->bedrooms = 2;
								break;
							case '3':
								$this->bedrooms = 3;
								break;
							case '4':
								$this->bedrooms = 4;
								break;
							case '5':
								$this->bedrooms = 5;
								break;
							case '6':
								$this->bedrooms = 6;
								break;
							case '7':
								$this->bedrooms = 7;
								break;
							case '8':
								$this->bedrooms = 8;
								break;
							case '9':
								$this->bedrooms = 9;
								break;
							case '10':
								$this->bedrooms = 10;
								break;
							default:
								$this->bedrooms = 0;
								break;
						}
						break;
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
		
		// Check the category
		if (isset($this->category)) $this->_processCategory();
		
		// Check the country
		if (isset($this->country)) $this->_processCountry();
		
		// Check the state
		if (isset($this->state) && !empty($this->cnid)) $this->_processState();
		
		// Check the city
		if (isset($this->city) && !empty($this->stid)) $this->_processCity();
		
		// Check the agent
		if (isset($this->agent)) $this->_processAgent();
		
		// Handle the images
		if (is_null($this->_images)) {
			if (!is_null($this->image1)) $this->_processMedia('image1');
			if (!is_null($this->image2)) $this->_processMedia('image2');
			if (!is_null($this->image3)) $this->_processMedia('image3');
			if (!is_null($this->image4)) $this->_processMedia('image4');
			if (!is_null($this->image5)) $this->_processMedia('image5');
			if (!is_null($this->image6)) $this->_processMedia('image6');
			if (!is_null($this->image7)) $this->_processMedia('image7');
			if (!is_null($this->image8)) $this->_processMedia('image8');
			if (!is_null($this->image9)) $this->_processMedia('image9');
			if (!is_null($this->image10)) $this->_processMedia('image10');
			if (!is_null($this->image11)) $this->_processMedia('image11');
			if (!is_null($this->image12)) $this->_processMedia('image12');
			if (!is_null($this->image13)) $this->_processMedia('image13');
			if (!is_null($this->image14)) $this->_processMedia('image14');
			if (!is_null($this->image15)) $this->_processMedia('image15');
			if (!is_null($this->image16)) $this->_processMedia('image16');
			if (!is_null($this->image17)) $this->_processMedia('image17');
			if (!is_null($this->image18)) $this->_processMedia('image18');
			if (!is_null($this->image19)) $this->_processMedia('image19');
			if (!is_null($this->image20)) $this->_processMedia('image20');
			if (!is_null($this->image21)) $this->_processMedia('image21');
			if (!is_null($this->image22)) $this->_processMedia('image22');
			if (!is_null($this->image23)) $this->_processMedia('image23');
			if (!is_null($this->image24)) $this->_processMedia('image24');
		}
		else {
			// We can use the new images tables
			$this->_processMedia('fname');
		}
		
		// Bind the data
		$this->_properties->bind($this);

		// Store the data
		if ($this->_properties->store()) {
			if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_PROPERTY'));
			else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_PROPERTY'));
		}
		else $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_PROPERTY_NOT_ADDED', $this->_properties->getError()));

		// Store the debug message
		$csvilog->addDebug(JText::_('COM_CSVI_PROPERTY_QUERY'), true);

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
		$this->_properties = $this->getTable('properties');
		$this->_categories = $this->getTable('categories');
		$this->_countries = $this->getTable('countries');
		$this->_states = $this->getTable('states');
		$this->_cities = $this->getTable('cities');
		
		// See if this is a newer EZ Realty version
		$db = JFactory::getDbo();
		$tables = $db->getTableList();
		if (!in_array($db->getPrefix().'ezrealty_images', $tables)) {
			$this->_tablesexist = false;
		}
		else {
			$this->_tablesexist = true;
			// Load the language tables
			$this->_images = $this->getTable('images');
		}
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
		$this->_properties->reset();
		$this->_categories->reset();
		$this->_countries->reset();
		$this->_states->reset();
		$this->_cities->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}
	
	/**
	 * Process property images
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.3
	 */
	private function _processMedia($image) {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$csvilog = $jinput->get('csvilog', null, null);
		// Check if any image handling needs to be done
		if ($template->get('process_image', 'image', false)) {
			if (!is_null($this->$image)) {
				
				// Create an array of images to process
				$images = explode('|', $this->$image);
				$titles = explode('|', $this->file_title);
				$descriptions = explode('|', $this->file_description);
				$order = explode('|', $this->file_ordering);
				$ordering = 1;
				$max_width = $template->get('resize_max_width', 'image', 1024);
				$max_height = $template->get('resize_max_height', 'image', 768);
				
				// Image handling
				$imagehelper = new ImageHelper;
	
				foreach ($images as $key => $image) {
					$image = trim($image);
					// Create image name if needed
					if (count($images) == 1) $img_counter = 0;
					else $img_counter = $key + 1;
					
					if (!empty($image)) {
						// Verify the original image
						if ($imagehelper->isRemote($image)) {
							$original = $image;
							$remote = true;
							$full_path =  $template->get('file_location_property_images', 'path');
						}
						else {
							$original = $template->get('file_location_property_images', 'path').$image;
							$remote = false;
								
							// Get subfolders
							$path_parts = pathinfo($original);
							$full_path = $path_parts['dirname'].'/';
						}
			
						// Generate image names
						$file_details = $imagehelper->ProcessImage($original, $full_path);
						// Process the file details
						if ($file_details['exists'] && $file_details['isimage']) {
							// Check if the image is an external image
							$title = (isset($titles[$key])) ? $titles[$key] : $file_details['output_name'];
							$description = (isset($descriptions[$key])) ? $descriptions[$key] : '';
							
							$data = array();
							$data['propid'] = $this->id;
							$data['fname'] = $file_details['output_name'];
							$data['title'] = $title;
							$data['description'] = $description;
							$data['ordering'] = (isset($order[$key]) && !empty($order[$key])) ? $order[$key] : $ordering;
							
							if (substr($file_details['name'], 0, 4) == 'http') {
								if (is_null($this->_images)) $csvilog->AddStats('incorrect', 'COM_CSVI_EZREALTY_NOSUPPORT_URL');
								else {
									// External images are supported now but needs to be stored with separate data
									if (substr($file_details['output_path'], -1) == '/') $data['path'] = substr($file_details['output_path'], 0, -1);   
									else $data['path'] = $file_details['output_path'];
								}
							}
							else {
								$this->$image = $file_details['output_name'];
			
								// Create the thumbnail
								if ($template->get('thumb_create', 'image')) {
									$imagehelper->createThumbnail($file_details['output_path'].$this->$image, $template->get('file_location_property_images', 'path').'th/', $this->$image);
								}
			
							}
							// Store the property image relation
							$this->_images->bind($data);
							if (!$this->_images->check()) {
								if ($this->_images->store()) {
									$csvilog->addDebug('COM_CSVI_STORE_PRODUCT_IMAGE_RELATION', true);
									$ordering++;
								}
							}
							else {
								$csvilog->addDebug('Property image relation already exists');
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * Manage the category 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		private
	 * @param 
	 * @return 
	 * @since 		4.3
	 */
	private function _processCategory() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		
		// Check if the category exists
		$query->select('id');
		$query->from('#__ezrealty_catg');
		$query->where($db->nameQuote('name').' = '.$db->quote($this->category));
		$db->setQuery($query);
		$catid = $db->loadResult();
		if ($catid > 0) {
			$this->cid = $catid;
		}
		else {
			// Category doesn't exist, let's create it
			$this->_categories->name = $this->category;
			$this->_categories->alias = JApplication::stringURLSafe($this->category);
			if (trim(str_replace('-','',$this->_categories->alias)) == '') {
				$this->_categories->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
			}
			$this->_categories->published = 1;
			$this->_categories->access = 1;
			if ($this->_categories->store()) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_CATEGORY_DETAILS'));
				else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_CATEGORY_DETAILS'));
				$this->cid = $this->_categories->id;
			}
			else  $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_CATEGORY_DETAILS_NOT_ADDED', $this->_categories->getError()));

			// Store the debug message
			$csvilog->addDebug('COM_CSVI_CATEGORY_DETAILS_QUERY', true);
		}
	}
	
	/**
	 * Manage the country
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.3
	 */
	private function _processCountry() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
	
		$this->_countries->name = $this->country;
		
		// Check if the category exists
		if (!$this->_countries->check()) {
			// Country doesn't exist, let's create it
			$this->_countries->published = 1;
			if ($this->_countries->store()) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_COUNTRY_DETAILS'));
				else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_COUNTRY_DETAILS'));
				$this->cnid = $this->_countries->id;
			}
			else  $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_COUNTRY_DETAILS_NOT_ADDED', $this->_countries->getError()));
	
			// Store the debug message
			$csvilog->addDebug('COM_CSVI_COUNTRY_DETAILS_QUERY', true);
		}
		else $this->cnid = $this->_countries->id;
	}
	
	/**
	 * Manage the state
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.3
	 */
	private function _processState() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		
		// We need the name and country
		$this->_states->name = $this->state;
		$this->_states->countid = $this->cnid;
		
		// Check if the category exists
		if (!$this->_states->check()) {
			// State doesn't exist, let's create it
			$this->_states->published = 1;
			if ($this->_states->store()) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_STATE_DETAILS'));
				else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_STATE_DETAILS'));
				$this->stid = $this->_states->id;
			}
			else  $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_STATE_DETAILS_NOT_ADDED', $this->_states->getError()));
	
			// Store the debug message
			$csvilog->addDebug('COM_CSVI_STATE_DETAILS_QUERY', true);
		}
		else $this->stid = $this->_states->id;
	}
	
	/**
	 * Manage the city
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.3
	 */
	private function _processCity() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
	
		// We need the city and state
		$this->_cities->ezcity = $this->city;
		$this->_cities->stateid = $this->stid;
		
		// Check if the category exists
		if (!$this->_cities->check()) {
			// City doesn't exist, let's create it
			$this->_cities->published = 1;
			if ($this->_cities->store()) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_CITY_DETAILS'));
				else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_CITY_DETAILS'));
				$this->locid = $this->_cities->id;
			}
			else  $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_CITY_DETAILS_NOT_ADDED', $this->_cities->getError()));
	
			// Store the debug message
			$csvilog->addDebug('COM_CSVI_CITY_DETAILS_QUERY', true);
		}
		else $this->locid = $this->_cities->id;
	}
	
	/**
	 * Process agent 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		private
	 * @param 
	 * @return 
	 * @since 		4.3
	 */
	private function _processAgent() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		
		// Check if the agent exists
		$query->select('mid');
		$query->from('#__ezrealty_profile');
		$query->where($db->nameQuote('dealer_name').' = '.$db->quote($this->agent));
		$db->setQuery($query);
		$mid = $db->loadResult();
		if ($mid > 0) {
			$this->owner = $mid;
		}
		else {
			// Store the debug message
			$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_EZREALTY_OWNER_NOT_FOUND', $this->agent));
		}
	}
}
?>