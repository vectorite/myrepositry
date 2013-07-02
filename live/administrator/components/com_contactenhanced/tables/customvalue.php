<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;


/**
 * @package		com_contactenhanced
*/
class ContactenhancedTableCustomvalue extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__ce_cv', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @param	array		Named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @since	1.6
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}
		$jform	= JRequest::getVar('jform'	, '', 'POST', 'none', JREQUEST_ALLOWRAW);
		if (isset($jform['description'])) {
			$array['description']	= $jform['description'];
		}

	
		return parent::bind($array, $ignore);
	}

	/**
	 * Stores a custom field
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success, false on failure.
	 * @since	1.6
	 */
	public function store($updateNulls = false)
	{
		
		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	/**
	 * Overloaded check function
	 *
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{
		/** check for valid value */
		if (trim($this->name) == '') {
			$this->setError(JText::_('CE_CV_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}
		/** check for valid text */
		if (trim($this->value) == '') {
			$this->setError(JText::_('CE_CV_WARNING_PROVIDE_VALID_TEXT'));
			return false;
		}

		return true;
	}
	
}
