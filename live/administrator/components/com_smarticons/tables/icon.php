<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id: icon.php 9 2012-03-28 20:07:32Z Bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/


// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

/**
 * Hello Table class
 */
class SmartIconsTableIcon extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__com_smarticons', 'idIcon', $db);
	}
	/**
	 * Overloaded bind function
	 *
	 * @param       array           named array
	 * @return      null|string     null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = '') {
		if (isset($array['params']) && is_array($array['params']))
		{
			// Convert the params field to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}

		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules'])) {
			$rules = new JRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded load function
	 *
	 * @param       int $pk primary key
	 * @param       boolean $reset reset data
	 * @return      boolean
	 * @see JTable:load
	 */
	public function load($pk = null, $reset = true)	{
		if (parent::load($pk, $reset))
		{
			// Convert the params field to a registry.
			$params = new JRegistry;
			$params->loadString($this->params);
			$this->params = $params;
			return true;
		}
		else
		{
			return false;
		}
	}
	public function check() {
		if (trim($this->Name) == '') {
			$this->setError(JText::_('COM_SMARTICONS_ERROR_PROVIDE_VALID_NAME'));
			return false;
		}

		return true;
	}
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return      string
	 * @since       1.6
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_smarticons.icon.'.(int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return      string
	 * @since       1.6
	 */
	protected function _getAssetTitle()	{
		return $this->Name;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @return      int
	 * @since       1.6
	 */
	protected function _getAssetParentId($table = null, $id = null)	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_smarticons');
		return $asset->id;
	}
}