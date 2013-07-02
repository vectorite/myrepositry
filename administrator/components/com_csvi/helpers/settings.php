<?php
/**
 * CSVI settings class
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: settings.php 1975 2012-04-24 06:27:02Z RolandD $
 */

defined( '_JEXEC' ) or die;

/**
 * The CSVI Config Class
 *
 * @package CSVI
 */
class CsviSettings {

	/** @var array The parameter object */
	private $_params = false;

	public function __construct() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from('#__csvi_settings');
		$query->where('id = 1');
		$db->setQuery($query);
		$settings = $db->loadResult();
		$registry = new JRegistry();
		$registry->loadString($settings);
		$this->_params = $registry;
	}

	/**
	 * Get a requested value
	 *
	 * @param string $setting the setting to get the value for
	 * @param mixed $default the default value if no $setting is found
	 */
	public function get($setting, $default=false) {
		return $this->_params->get($setting, $default);
	}
}
?>