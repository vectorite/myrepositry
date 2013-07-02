<?php
/**
 * Scroller with tabs config class
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
 * The Scroller with tabs Config Class
 *
* @package CSVI
 */
class CsviCom_Scrollerwithtabs_Config {

	private $_scrollerwithtabscfg = array();

	public function __construct() {
		$this->_parse();
	}

	/**
	 * Finds a given Scroller with tabs setting
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
		return $this->_scrollerwithtabscfg->get($setting, false);
	}

	/**
	 * Parse the Scroller with tabs configuration
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
		$query->select('*');
		$query->from('#__scrollerwithtabs_info');
		$query->where($db->nameQuote('id').' = '.$db->quote('1'));
		$db->setQuery($query);
		$data = $db->loadObject();
		$this->_scrollerwithtabscfg = new JObject();
		$this->_scrollerwithtabscfg->setProperties($data);
	}
}
?>