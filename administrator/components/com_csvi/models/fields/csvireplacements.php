<?php
/**
 * List the available replacement rules
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvitemplates.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list with replacements
 *
 * @package CSVI
 */
class JFormFieldCsviReplacements extends JFormFieldCsviForm {

	protected $type = 'CsviAvailableFields';

	/**
	 * Get the available fields
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		array	an array of options
	 * @since 		4.3
	 */
	protected function getOptions() {
		// Load the available replacements
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id AS value, name AS text');
		$query->from('#__csvi_replacements');
		$query->order('name');
		$db->setQuery($query);
		$replacements = $db->loadObjectList();
		
		if (!is_array($replacements)) $replacements = array();
		return array_merge(parent::getOptions(), $replacements);
	}
}
?>
