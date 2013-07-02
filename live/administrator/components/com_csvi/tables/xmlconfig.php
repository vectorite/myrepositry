<?php
/**
 * XML Config table
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvi.php 1961 2012-04-06 09:23:02Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.database.tablenested');

/**
 * XML config table
 *
 * @package		CSVI
 */
class TableXmlconfig extends JTableNested {
	
	/**
	 * @param database A database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__csvi_xmlconfigs', 'id', $db);
	}

	/**
	 * Override check function
	 *
	 * @return	boolean
	 * @see		JTable::check
	 */
	public function check()	{
		// Check for a title.
		if (trim($this->title) == '') {
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_CATEGORY'));
			return false;
		}

		return true;
	}
	
	
	/**
	 * Overriden JTable::store to set created/modified and user id.
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success.
	 */
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();

		if ($this->id) {
			// Existing category
			$this->modified_time	= $date->toMySQL();
			$this->modified_user_id	= $user->get('id');
		} else {
			// New category
			$this->created_time		= $date->toMySQL();
			$this->created_user_id	= $user->get('id');
		}
		
		return parent::store($updateNulls);
	}
	
	
	/**
	 * Delete category record
	 *
	 * @author RickG
	 * @return boolean True on success
	 * @todo Add check for store and products assinged to category before allowing delete
	 */
	public function delete($pk = null, $children = true)
	{
		if (parent::delete($pk, $children)) {
			return true;
		}	
		else {
			return false;
		}
	}
}
