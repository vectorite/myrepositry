<?php
/**
 * EZ Realty helper file
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: com_virtuemart.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * The EZ Realty Config Class
 */
class Com_EzRealty {

	private $_csvidata = null;

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
	 * @since 		4.0
	 */
	public function __construct() {
		$jinput = JFactory::getApplication()->input;
		$this->_csvidata = $jinput->get('csvifields', null, null);
	}

	/**
	 * Get the property id, this is necessary for updating existing properties
	 *
	 * @copyright
	 * @author		RolandD
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		integer	id is returned
	 * @since 		5.1
	 */
	public function getPropertyId() {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$update_based_on = $template->get('update_based_on', 'property', 'id');
		if ($update_based_on == 'id') {
			return $this->_csvidata->get('id');
		}
		else {
			$property_key = $this->_csvidata->get($update_based_on);
			if ($property_key) {
				$query = $db->getQuery(true);
				$query->select('id')->from('#__ezrealty')->where($db->quoteName($update_based_on)." = ".$db->quote($property_key));
				$db->setQuery($query);
				$csvilog->addDebug('COM_CSVI_FIND_PROPERTY_ID', true);
				return $db->loadResult();
			}
			else return false;
		}
	}
}