<?php
/**
 * List the VirtueMart manufacturers
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvivirtuemartmanufacturer.php 2039 2012-07-14 18:16:58Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with manufacturers
 *
 * @package CSVI
 */
class JFormFieldCsviVirtuemartManufacturer extends JFormFieldCsviForm {

	protected $type = 'CsviVirtuemartManufacturer';

	/**
	 * Specify the options to load based on default site language
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		array	an array of options
	 * @since 		4.0
	 */
	protected function getOptions() {
		$conf = JFactory::getConfig();
		$lang = strtolower(str_replace('-', '_', $conf->get('language')));
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('virtuemart_manufacturer_id').' AS value,'.$db->quoteName('mf_name').' AS text');
		$query->from($db->quoteName('#__virtuemart_manufacturers_'.$lang));
		$db->setQuery($query);
		$options = $db->loadObjectList();
		if (empty($options)) $options = array();
		return array_merge(parent::getOptions(), $options);
	}
}
?>
