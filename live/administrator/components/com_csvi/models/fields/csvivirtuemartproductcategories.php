<?php
/**
 * List the product categories
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvivirtuemartproductcategories.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with product categories
 *
 * @package CSVI
 */
class JFormFieldCsviVirtuemartProductCategories extends JFormFieldCsviForm {

	protected $type = 'CsviVirtuemartProductCategories';

	/**
	 * Specify the options to load
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo		Set to use chosen language
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		array	an array of options
	 * @since 		4.0
	 */
	protected function getOptions() {
		$this->options = array();
		if (class_exists('com_virtuemart')) {
			$conf = JFactory::getConfig();
			$lang = strtolower(str_replace('-', '_', $conf->get('language')));
			$helper = new Com_VirtueMart();
			$this->options = $helper->getCategoryTree($lang);
		}
		return $this->options;
	}
}
?>
