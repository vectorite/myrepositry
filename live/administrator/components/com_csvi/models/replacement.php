<?php
/**
 *
 * Model class for replacement editing
 *
 * @package 	Csvi
 * @author 		RolandD
 * @link		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: replacement.php 1955 2012-03-30 21:57:05Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

// Load the model framework
jimport('joomla.application.component.modeladmin');

/**
 * Sku editing
 *
 * @package 	Csvi
 * @author 		RolandD
  * @since 		1.0
 */
class CsviModelReplacement extends JModelAdmin {

	/**
	 * @var string Model context string
	 */
	private $context = 'com_csvi.replacement';

	/**
	 * Method to get the record form located in models/forms
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		array $data Data for the form.
	 * @param 		boolean $loadData True if the form is to load its own data (default case), false if not.
	 * @return 		mixed
	 * @since 		1.0
	 */
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm($this->context, 'replacement', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) return false;

		return $form;
	}

	/**
	 * Get the form data 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		protected
	 * @param 
	 * @return 
	 * @since 		4.1
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_csvi.edit.replacement.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
}
?>
