<?php
/**
 * Virtuemart medias table
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: medias.php 2061 2012-08-07 09:20:26Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

/**
* @package CSVI
 */
class TableMedias extends JTable {

	/**
	 * Table constructor
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
	public function __construct($db) {
		parent::__construct('#__virtuemart_medias', 'virtuemart_media_id', $db);
	}

	/**
	 * Check if a media file already exists
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		true if media exists, false if media does not exist
	 * @since 		4.0
	 */
	public function check() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($this->_tbl_key);
		$query->from($this->_tbl);
		$query->where('file_url = '.$db->quote($this->file_url));
		$db->setQuery($query);
		$id = $db->loadResult();
		if ($id > 0) {
			$this->virtuemart_media_id = $id;
			return true;
		}
		else return false;
	}

	/**
	 * Reset the keys including primary key
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
	public function reset() {
		// Get the default values for the class from the table.
		foreach ($this->getFields() as $k => $v) {
			// If the property is not private, reset it.
			if (strpos($k, '_') !== 0) {
				$this->$k = NULL;
			}
		}
	}
}
?>