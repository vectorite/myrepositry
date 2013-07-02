<?php
/**
 * EZ Realty config class
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: com_virtuemart_config.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * The EZ Realty Config Class
 *
* @package CSVI
 */
class CsviCom_Ezrealty_Config {

	private $_ezrealtycfg = array();

	public function __construct() {
		$this->_parse();
	}

	/**
	 * Finds a given EZ Realty setting
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access
	 * @param 		string $setting The config value to find
	 * @return 		mixed	value if found | false if not found
	 * @since		4.0
	 */
	public function get($setting) {
		return $this->_ezrealtycfg->get($setting, false);
	}

	/**
	 * Parse the EZ Realty configuration
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.0
	 */
	private function _parse() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from('#__extensions');
		$query->where($db->nameQuote('element').' = '.$db->quote('com_ezrealty'));
		$query->where($db->nameQuote('type').' = '.$db->quote('component'));
		$db->setQuery($query);
		$params = $db->loadResult();
		
		$this->_ezrealtycfg = new JRegistry();
		 $this->_ezrealtycfg->loadString($params);
	}
}
?>